<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Database\Factories\PlantCatalogHamaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class PlantCatalogHama extends Model
{
    /** @use HasFactory<PlantCatalogHamaFactory> */
    use HasFactory;

    protected $fillable = [
        'plant_catalog_id',
        'judul',
        'kategori',
        'gejala',
        'solusi',
        'foto',
        'urutan',
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
            'foto' => 'array',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (PlantCatalogHama $hama) {
            $cloudinary = app(CloudinaryService::class);
            foreach ($hama->foto_urls as $url) {
                try {
                    $cloudinary->deleteImage($url);
                } catch (Throwable) {
                    // Safe cleanup on delete
                }
            }
        });
    }

    public function plantCatalog(): BelongsTo
    {
        return $this->belongsTo(PlantCatalog::class);
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

        return $urls;
    }

    public function getFotoCountAttribute(): int
    {
        return count($this->foto_urls);
    }
}
