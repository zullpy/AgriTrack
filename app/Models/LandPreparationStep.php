<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Database\Factories\LandPreparationStepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandPreparationStep extends Model
{
    /** @use HasFactory<LandPreparationStepFactory> */
    use HasFactory;

    protected $fillable = [
        'nomor',
        'urutan',
        'judul',
        'waktu',
        'deskripsi',
        'tips',
        'spesifikasi',
        'foto',
    ];

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var list<string>
     */
    protected $appends = [
        'foto_urls',
        'foto_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'spesifikasi' => 'array',
            'foto' => 'array',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (LandPreparationStep $step) {
            $cloudinary = app(CloudinaryService::class);
            foreach ($step->foto_urls as $url) {
                $cloudinary->deleteImage($url);
            }
        });
    }

    /**
     * Ambil daftar URL foto yang siap ditampilkan di view.
     *
     * @return array<int, string>
     */
    public function getFotoUrlsAttribute(): array
    {
        $raw = $this->foto;
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
                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://') && ! str_starts_with($url, '/')) {
                    $url = '/storage/'.ltrim($url, '/');
                }
                $urls[] = $url;
            }
        }

        return array_values($urls);
    }

    /**
     * Ambil jumlah foto saat ini.
     */
    public function getFotoCountAttribute(): int
    {
        return count($this->foto_urls);
    }
}
