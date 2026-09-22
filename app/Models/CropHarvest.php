<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropHarvest extends Model
{
    protected $fillable = [
        'crop_id',
        'panen_ke',
        'tanggal_panen',
        'hst_saat_panen',
        'total_panen',
        'harga_panen',
        'total_harga_kotor',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_panen' => 'date',
            'panen_ke' => 'integer',
            'hst_saat_panen' => 'integer',
        ];
    }

    public function getNumericHargaKotorAttribute(): float
    {
        if (! empty($this->total_harga_kotor)) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $this->total_harga_kotor);
            if ($cleaned !== '') {
                return (float) $cleaned;
            }
        }

        // Fallback: hitung otomatis jika total_panen dan harga_panen ada nilainya
        if (! empty($this->total_panen) && ! empty($this->harga_panen)) {
            // Ambil angka dari total panen (misal: "100 kg" -> 100, "12.5 kg" -> 12.5)
            $totalStr = str_replace(',', '.', (string) $this->total_panen);
            if (preg_match('/[0-9]+(?:\.[0-9]+)?/', $totalStr, $matches)) {
                $qty = (float) $matches[0];
                $unitPrice = (float) preg_replace('/[^0-9]/', '', (string) $this->harga_panen);
                if ($qty > 0 && $unitPrice > 0) {
                    return $qty * $unitPrice;
                }
            }
        }

        return 0;
    }

    public function getFormattedHargaKotorAttribute(): string
    {
        $val = $this->numeric_harga_kotor;
        if ($val > 0) {
            return 'Rp '.number_format($val, 0, ',', '.');
        }

        if (! empty($this->total_harga_kotor)) {
            return str_starts_with($this->total_harga_kotor, 'Rp') ? $this->total_harga_kotor : 'Rp '.$this->total_harga_kotor;
        }

        return '—';
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
