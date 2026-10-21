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
    'jumlah',
    'kemasan_jumlah',
    'kemasan_satuan',
    'tanggal_beli',
    'foto_nota',
    'catatan',
])]
class MedicinePurchase extends Model
{
    protected $appends = ['formatted_harga', 'foto_url', 'kemasan_label'];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'jumlah' => 'integer',
            'kemasan_jumlah' => 'float',
            'tanggal_beli' => 'date',
        ];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getKemasanLabelAttribute(): ?string
    {
        if ($this->kemasan_jumlah === null || ! $this->kemasan_satuan) {
            return null;
        }

        $formattedJumlah = (float) $this->kemasan_jumlah == (int) $this->kemasan_jumlah
            ? (string) (int) $this->kemasan_jumlah
            : (string) (float) $this->kemasan_jumlah;

        return "{$formattedJumlah} {$this->kemasan_satuan}";
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
        if (! $this->foto_nota || ! is_string($this->foto_nota) || trim($this->foto_nota) === '') {
            return null;
        }

        $trimmed = trim($this->foto_nota);
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        return Storage::disk('public')->url($trimmed);
    }
}
