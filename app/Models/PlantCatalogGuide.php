<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantCatalogGuide extends Model
{
    protected $fillable = [
        'plant_catalog_id',
        'phase',
        'hst',
        'focus',
        'nutrients',
        'dosis',
        'metode',
        'tips',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'nutrients' => 'array',
            'urutan' => 'integer',
        ];
    }

    public function plantCatalog(): BelongsTo
    {
        return $this->belongsTo(PlantCatalog::class);
    }
}
