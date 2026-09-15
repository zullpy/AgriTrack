<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'medicine_id',
    'toko_obat',
    'harga',
    'tanggal_beli',
    'foto_nota',
    'catatan',
])]
class MedicinePurchase extends Model
{
    protected $appends = ['formatted_harga', 'foto_url'];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'tanggal_beli' => 'date',
        ];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getFormattedHargaAttribute(): ?string
    {
        if ($this->harga === null) {
            return null;
        }

        return 'Rp '.number_format($this->harga, 0, ',', '.');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto_nota) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_nota);
    }
}
