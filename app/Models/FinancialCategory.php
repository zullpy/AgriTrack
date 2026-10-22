<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'tipe',
        'nama',
        'keterangan',
        'urutan',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('subcategories')->orderBy('urutan')->orderBy('nama');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id')->orderBy('urutan')->orderBy('nama');
    }

    /**
     * Pastikan kategori utama dan hierarki sub-kategori tersimpan di financial_categories.
     */
    public static function ensureCategoryHierarchy(string $kategori, ?string $subKategori = null, string $tipe = 'pengeluaran'): self
    {
        $kategori = trim($kategori);
        if ($kategori === '') {
            $kategori = 'Lainnya';
        }

        $validTipe = in_array($tipe, ['pemasukan', 'pengeluaran', 'keduanya'], true) ? $tipe : 'pengeluaran';

        // 1. Cari atau buat Kategori Utama (parent_id = null)
        $parent = self::whereNull('parent_id')
            ->where(function ($q) use ($kategori) {
                $q->where('nama', $kategori)
                    ->orWhereRaw('LOWER(TRIM(nama)) = ?', [strtolower($kategori)]);
            })->first();

        if (! $parent) {
            $maxUrutan = (int) self::whereNull('parent_id')->max('urutan');
            $parent = self::create([
                'parent_id' => null,
                'nama' => $kategori,
                'tipe' => $validTipe,
                'urutan' => $maxUrutan + 1,
            ]);
        } elseif ($parent->tipe !== 'keduanya' && $parent->tipe !== $validTipe) {
            $parent->update(['tipe' => 'keduanya']);
        }

        // 2. Jika tidak ada sub_kategori, return parent
        $subKategori = $subKategori !== null ? trim($subKategori) : '';
        if ($subKategori === '') {
            return $parent;
        }

        // 3. Hierarki sub-kategori (misal "BOP Panen › Mamin" atau "Agenda ke Cikalong")
        $parts = array_map('trim', explode('›', $subKategori));
        $currentParentId = $parent->id;

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $existing = self::where('parent_id', $currentParentId)
                ->where(function ($q) use ($part) {
                    $q->where('nama', $part)
                        ->orWhereRaw('LOWER(TRIM(nama)) = ?', [strtolower($part)]);
                })->first();

            if (! $existing) {
                $nextUrutan = ((int) self::where('parent_id', $currentParentId)->max('urutan')) + 1;
                $existing = self::create([
                    'parent_id' => $currentParentId,
                    'nama' => $part,
                    'tipe' => $parent->tipe ?? $validTipe,
                    'urutan' => $nextUrutan,
                ]);
            }

            $currentParentId = $existing->id;
        }

        return $parent;
    }

    /**
     * Sinkronkan semua kategori dan sub-kategori dari transaksi yang ada ke tabel financial_categories.
     */
    public static function syncFromTransactions(): void
    {
        $transactions = FinancialTransaction::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->select('kategori', 'sub_kategori', 'tipe')
            ->distinct()
            ->get();

        foreach ($transactions as $trx) {
            self::ensureCategoryHierarchy(
                (string) $trx->kategori,
                $trx->sub_kategori ? (string) $trx->sub_kategori : null,
                (string) ($trx->tipe ?? 'pengeluaran')
            );
        }
    }

    /**
     * Dapatkan path hierarki kategori dari root hingga ke kategori ini.
     *
     * @return array{root: ?self, path: array<string>, pathString: string}
     */
    public static function getCategoryHierarchyPath(int|self $category): array
    {
        $current = ($category instanceof self) ? $category : self::find($category);
        if (! $current) {
            return ['root' => null, 'path' => [], 'pathString' => ''];
        }

        if ($current->parent_id === null) {
            return [
                'root' => $current,
                'path' => [],
                'pathString' => '',
            ];
        }

        $segments = [$current->nama];
        $currParentId = $current->parent_id;
        $root = null;

        while ($currParentId !== null) {
            $parent = self::find($currParentId);
            if (! $parent) {
                break;
            }

            if ($parent->parent_id === null) {
                $root = $parent;
                break;
            }

            $segments[] = $parent->nama;
            $currParentId = $parent->parent_id;
        }

        $path = array_values(array_reverse($segments));

        return [
            'root' => $root,
            'path' => $path,
            'pathString' => implode(' › ', $path),
        ];
    }

    /**
     * Perbarui riwayat transaksi keuangan (FinancialTransaction) saat kategori atau sub-kategori diedit.
     *
     * @param  array{root: ?self, path: array<string>, pathString: string}  $oldHierarchy
     */
    public static function cascadeCategoryUpdate(self $category, string $oldNama, ?int $oldParentId, array $oldHierarchy): void
    {
        $newNama = trim($category->nama);
        $newHierarchy = self::getCategoryHierarchyPath($category);

        $oldRoot = $oldHierarchy['root'];
        $oldRootName = $oldRoot?->nama ?? $oldNama;
        $oldSubPath = $oldHierarchy['pathString'];

        $newRoot = $newHierarchy['root'];
        $newRootName = $newRoot?->nama ?? $newNama;
        $newSubPath = $newHierarchy['pathString'];

        // Kasus 1: Kategori Utama tetap Kategori Utama (parent_id === null)
        if ($oldParentId === null && $category->parent_id === null) {
            if ($oldNama !== $newNama) {
                FinancialTransaction::where(function ($q) use ($oldNama) {
                    $q->where('kategori', $oldNama)
                        ->orWhereRaw('LOWER(TRIM(kategori)) = ?', [strtolower(trim($oldNama))]);
                })->update(['kategori' => $newNama]);
            }

            return;
        }

        // Kasus 2: Kategori Utama diubah menjadi Sub-Kategori dari kategori lain
        if ($oldParentId === null && $category->parent_id !== null) {
            $transactions = FinancialTransaction::where(function ($q) use ($oldNama) {
                $q->where('kategori', $oldNama)
                    ->orWhereRaw('LOWER(TRIM(kategori)) = ?', [strtolower(trim($oldNama))]);
            })->get();

            foreach ($transactions as $trx) {
                $curSub = trim((string) $trx->sub_kategori);
                $updatedSub = $curSub !== '' ? ($newSubPath.' › '.$curSub) : $newSubPath;
                $trx->update([
                    'kategori' => $newRootName,
                    'sub_kategori' => $updatedSub,
                ]);
            }

            return;
        }

        // Kasus 3: Sub-kategori atau Sub-sub-kategori (oldParentId !== null)
        if ($oldSubPath !== $newSubPath || $oldNama !== $newNama || $oldRootName !== $newRootName) {
            $query = FinancialTransaction::query();

            if (! empty($oldRootName)) {
                $query->where(function ($q) use ($oldRootName) {
                    $q->where('kategori', $oldRootName)
                        ->orWhereRaw('LOWER(TRIM(kategori)) = ?', [strtolower(trim($oldRootName))]);
                });
            }

            $query->where(function ($q) use ($oldSubPath, $oldNama) {
                $q->where('sub_kategori', $oldSubPath)
                    ->orWhere('sub_kategori', 'like', $oldSubPath.' › %')
                    ->orWhere('sub_kategori', 'like', $oldSubPath.' > %')
                    ->orWhere('sub_kategori', $oldNama);
            });

            $transactions = $query->get();

            foreach ($transactions as $trx) {
                $curSub = (string) $trx->sub_kategori;
                $updatedSub = $curSub;

                if ($curSub === $oldSubPath) {
                    $updatedSub = $newSubPath;
                } elseif (str_starts_with($curSub, $oldSubPath.' › ')) {
                    $rest = substr($curSub, strlen($oldSubPath.' › '));
                    $updatedSub = $newSubPath !== '' ? ($newSubPath.' › '.$rest) : $rest;
                } elseif (str_starts_with($curSub, $oldSubPath.' > ')) {
                    $rest = substr($curSub, strlen($oldSubPath.' > '));
                    $updatedSub = $newSubPath !== '' ? ($newSubPath.' › '.$rest) : $rest;
                } elseif ($curSub === $oldNama) {
                    $updatedSub = $newNama;
                }

                $updates = [];
                if ($updatedSub !== $curSub) {
                    $updates['sub_kategori'] = $updatedSub !== '' ? $updatedSub : null;
                }
                if (! empty($newRootName) && $trx->kategori !== $newRootName) {
                    $updates['kategori'] = $newRootName;
                }

                if (! empty($updates)) {
                    $trx->update($updates);
                }
            }
        }
    }
}
