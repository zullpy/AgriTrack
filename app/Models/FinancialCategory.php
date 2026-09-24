<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'tipe',
        'nama',
        'keterangan',
        'urutan',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('subcategories')->orderBy('urutan')->orderBy('nama');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id')->orderBy('urutan')->orderBy('nama');
    }
}
