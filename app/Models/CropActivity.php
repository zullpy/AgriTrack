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
        'foto_kegiatan',
    ];

    protected function casts(): array
    {
        return [
            'target_hst' => 'integer',
            'tanggal_selesai' => 'date',
            'foto_kegiatan' => 'array',
        ];
    }

    /**
     * Ambil daftar URL foto yang siap ditampilkan di view.
     *
     * @return array<int, string>
     */
    public function getFotoUrlsAttribute(): array
    {
        $raw = $this->foto_kegiatan;
        if (empty($raw)) {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [$raw];
        }

        if (! is_array($raw)) {
            return [];
        }

        $urls = [];
        foreach ($raw as $item) {
            $url = null;
            if (is_array($item)) {
                $url = $item['url'] ?? $item['secure_url'] ?? $item['path'] ?? null;
            } elseif (is_string($item)) {
                $url = trim($item);
            }

            if ($url) {
                // Jika merupakan path lokal tanpa http
                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://') && ! str_starts_with($url, '/')) {
                    $url = '/storage/'.ltrim($url, '/');
                }
                $urls[] = $url;
            }
        }

        return array_values($urls);
    }

    /**
     * Ambil jumlah foto kegiatan saat ini.
     */
    public function getFotoCountAttribute(): int
    {
        return count($this->foto_urls);
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
        if (! $this->crop || ! $this->crop->tanggal_tanam) {
            return null;
        }

        return Carbon::parse($this->crop->tanggal_tanam)->startOfDay()->addDays($this->target_hst);
    }

    /**
     * Cek apakah kegiatan jatuh tempo hari ini atau terlewati.
     */
    public function getIsDueOrPassedAttribute(): bool
    {
        if (! $this->crop) {
            return false;
        }

        return $this->crop->current_hst >= $this->target_hst;
    }
}
