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
        'catatan',
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

    /**
     * Hitung HST berjalan hari ini (atau HST terkunci jika sudah dipanen).
     */
    public function getCurrentHstAttribute(): int
    {
        if ($this->status === 'Sudah Dipanen') {
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

        // Jika sudah dipanen dan tanggal cek melewati tanggal panen
        if ($this->status === 'Sudah Dipanen' && $this->tanggal_panen) {
            $harvestDate = Carbon::parse($this->tanggal_panen)->startOfDay();
            if ($targetDate->gt($harvestDate)) {
                return null;
            }
        }

        return (int) $plantDate->diffInDays($targetDate);
    }

    /**
     * Dapatkan emoji tanaman sesuai katalog atau nama komoditas.
     */
    public function getEmojiAttribute(): string
    {
        $name = strtolower($this->nama_tanaman ?? '');
        $varietas = strtolower($this->varietas ?? '');

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
            if (str_contains($name, $keyword) || str_contains($varietas, $keyword)) {
                return $emoji;
            }
        }

        return '🌱';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Sedang Ditanam');
    }

    public function scopeHarvested(Builder $query): Builder
    {
        return $query->where('status', 'Sudah Dipanen');
    }
}
