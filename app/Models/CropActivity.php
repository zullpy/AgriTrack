<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropActivity extends Model
{
    protected $fillable = [
        'crop_id',
        'nama_kegiatan',
        'aplikasi_obat',
        'sasaran',
        'target_hst',
        'status',
        'tanggal_selesai',
        'catatan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'target_hst' => 'integer',
            'tanggal_selesai' => 'date',
        ];
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Hitung tanggal kalender saat HST target tercapai.
     */
    public function getTargetDateAttribute(): ?Carbon
    {
        if (!$this->crop || !$this->crop->tanggal_tanam) {
            return null;
        }

        return Carbon::parse($this->crop->tanggal_tanam)->startOfDay()->addDays($this->target_hst);
    }

    /**
     * Cek apakah kegiatan jatuh tempo hari ini atau terlewati.
     */
    public function getIsDueOrPassedAttribute(): bool
    {
        if (!$this->crop) return false;
        return $this->crop->current_hst >= $this->target_hst;
    }
}
