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
}
