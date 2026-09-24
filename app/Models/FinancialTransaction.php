<?php

namespace App\Models;

use Database\Factories\FinancialTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FinancialTransaction extends Model
{
    /** @use HasFactory<FinancialTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'tipe',
        'kategori',
        'sub_kategori',
        'judul',
        'nominal',
        'tanggal',
        'keterangan',
        'foto_nota',
    ];

    protected $appends = ['formatted_nominal', 'foto_url'];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto_nota) {
            return null;
        }

        if (str_starts_with($this->foto_nota, 'http://') || str_starts_with($this->foto_nota, 'https://')) {
            return $this->foto_nota;
        }

        return Storage::disk('public')->url($this->foto_nota);
    }
}
