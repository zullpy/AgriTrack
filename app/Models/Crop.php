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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Sedang Ditanam');
    }

    public function scopeHarvested(Builder $query): Builder
    {
        return $query->where('status', 'Sudah Dipanen');
    }
}
