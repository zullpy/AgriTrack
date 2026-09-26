<?php

namespace App\Models;

use Database\Factories\PlantingSeedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantingSeed extends Model
{
    /** @use HasFactory<PlantingSeedFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_bibit',
        'varietas',
        'deskripsi',
        'urutan',
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
        ];
    }

    /**
     * Relasi ke tahapan penanaman bibit untuk komoditas ini.
     *
     * @return HasMany<PlantingStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(PlantingStep::class)->orderBy('urutan')->orderBy('nomor')->orderBy('id');
    }

    /**
     * Alias relasi untuk kemudahan akses.
     *
     * @return HasMany<PlantingStep, $this>
     */
    public function plantingSteps(): HasMany
    {
        return $this->steps();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (PlantingSeed $seed) {
            // Hapus setiap langkah agar event deleting masing-masing langkah terpanggil (menghapus file foto fisik)
            foreach ($seed->steps as $step) {
                $step->delete();
            }
        });
    }
}
