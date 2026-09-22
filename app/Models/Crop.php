<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $fillable = [
        'nama_tanaman',
        'varietas',
        'populasi',
        'tanggal_tanam',
        'status',
        'tanggal_panen',
        'total_hst_panen',
        'total_panen',
        'harga_panen',
        'total_harga_kotor',
        'catatan',
    ];

    protected $appends = [
        'current_hst',
        'emoji',
        'next_harvest_number',
        'harvest_count',
        'total_pendapatan_kotor',
        'formatted_total_pendapatan_kotor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_tanam' => 'date',
            'tanggal_panen' => 'date',
            'total_hst_panen' => 'integer',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CropActivity::class)->orderBy('target_hst');
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(CropHarvest::class)->orderBy('panen_ke');
    }

    public function getNextHarvestNumberAttribute(): int
    {
        return ($this->relationLoaded('harvests') ? $this->harvests->count() : $this->harvests()->count()) + 1;
    }

    public function getHarvestCountAttribute(): int
    {
        return $this->relationLoaded('harvests') ? $this->harvests->count() : $this->harvests()->count();
    }

    public function getTotalPendapatanKotorAttribute(): float
    {
        return (float) ($this->relationLoaded('harvests')
            ? $this->harvests->sum(fn ($h) => $h->numeric_harga_kotor)
            : $this->harvests()->get()->sum(fn ($h) => $h->numeric_harga_kotor));
    }

    public function getFormattedTotalPendapatanKotorAttribute(): string
    {
        $val = $this->total_pendapatan_kotor;

        return $val > 0 ? 'Rp '.number_format($val, 0, ',', '.') : '—';
    }

    /**
     * Hitung HST berjalan hari ini (atau HST terkunci jika sudah dipanen / diakhiri).
     */
    public function getCurrentHstAttribute(): int
    {
        if ($this->status !== 'Sedang Ditanam') {
            return (int) ($this->total_hst_panen ?? 0);
        }

        $plantDate = Carbon::parse($this->tanggal_tanam)->startOfDay();
        $today = now()->startOfDay();

        if ($today->lt($plantDate)) {
            return 0;
        }

        return (int) $plantDate->diffInDays($today);
    }

    /**
     * Hitung HST pada tanggal tertentu untuk tampilan kalender.
     */
    public function getHstOnDate(Carbon $date): ?int
    {
        $plantDate = Carbon::parse($this->tanggal_tanam)->startOfDay();
        $targetDate = $date->copy()->startOfDay();

        // Sebelum tanggal tanam
        if ($targetDate->lt($plantDate)) {
            return null;
        }

        // Jika sudah dipanen / diakhiri dan tanggal cek melewati tanggal panen / akhir
        if ($this->status !== 'Sedang Ditanam' && $this->tanggal_panen) {
            $harvestDate = Carbon::parse($this->tanggal_panen)->startOfDay();
            if ($targetDate->gt($harvestDate)) {
                return null;
            }
        }

        return (int) $plantDate->diffInDays($targetDate);
    }

    /**
     * Dapatkan emoji tanaman sesuai katalog atau kamus komoditas umum.
     */
    public static function getEmojiForName(string $name, ?string $varietas = null): string
    {
        $name = strtolower(trim($name));
        $varietas = $varietas ? strtolower(trim($varietas)) : '';

        // 1. Cek dari PlantCatalog aktif (pencocokan nama & keywords persis seperti di Dashboard)
        try {
            $catalogs = PlantCatalog::aktif()->get();
            $matched = $catalogs->first(function (PlantCatalog $cat) use ($name, $varietas) {
                $catName = strtolower($cat->name);
                if ($catName === $name || str_contains($name, $catName) || str_contains($catName, $name)) {
                    return true;
                }
                if ($varietas && (str_contains($varietas, $catName) || str_contains($catName, $varietas))) {
                    return true;
                }
                if ($cat->keywords) {
                    $kws = array_map('trim', explode(',', strtolower($cat->keywords)));
                    foreach ($kws as $kw) {
                        if ($kw && (str_contains($name, $kw) || ($varietas && str_contains($varietas, $kw)))) {
                            return true;
                        }
                    }
                }

                return false;
            });

            if ($matched && ! empty($matched->emoji)) {
                return $matched->emoji;
            }
        } catch (\Throwable) {
            // fallback below
        }

        // 2. Fallback kamus emoji komoditas umum
        $map = [
            'jagung' => '🌽',
            'padi' => '🌾',
            'beras' => '🌾',
            'cabai' => '🌶️',
            'cabe' => '🌶️',
            'tomat' => '🍅',
            'terong' => '🍆',
            'mentimun' => '🥒',
            'timun' => '🥒',
            'semangka' => '🍉',
            'melon' => '🍈',
            'bawang' => '🧅',
            'kentang' => '🥔',
            'wortel' => '🥕',
            'singkong' => '🌿',
            'kedelai' => '🫘',
            'kacang' => '🥜',
            'bayam' => '🥬',
            'sawi' => '🥬',
            'kangkung' => '🥬',
            'kopi' => '☕',
            'kakao' => '🍫',
            'sawit' => '🌴',
            'tebu' => '🎋',
            'tembakau' => '🍂',
        ];

        foreach ($map as $keyword => $emoji) {
            if (str_contains($name, $keyword) || ($varietas && str_contains($varietas, $keyword))) {
                return $emoji;
            }
        }

        return '🌱';
    }

    /**
     * Dapatkan emoji tanaman sesuai katalog atau nama komoditas.
     */
    public function getEmojiAttribute(): string
    {
        return self::getEmojiForName($this->nama_tanaman ?? '', $this->varietas ?? null);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Sedang Ditanam');
    }

    public function scopeHarvested(Builder $query): Builder
    {
        return $query->whereIn('status', ['Sudah Dipanen', 'Diakhiri']);
    }
}
