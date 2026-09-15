<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantCatalog extends Model
{
    protected $fillable = [
        'key',
        'name',
        'emoji',
        'cycle',
        'theme',
        'keywords',
        'urutan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function guides(): HasMany
    {
        return $this->hasMany(PlantCatalogGuide::class)->orderBy('urutan');
    }

    /**
     * Pecah keywords menjadi array untuk pencocokan crop aktif.
     *
     * @return string[]
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [strtolower($this->name)];
        }

        return array_map('trim', explode(',', strtolower($this->keywords)));
    }

    /**
     * Tailwind badge color classes berdasarkan tema.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->theme) {
            'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'sky' => 'bg-sky-100 text-sky-800 border-sky-200',
            'lime' => 'bg-lime-100 text-lime-800 border-lime-200',
            'teal' => 'bg-teal-100 text-teal-800 border-teal-200',
            default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }
}
