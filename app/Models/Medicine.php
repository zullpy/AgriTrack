<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'nama',
    'jenis',
    'cara_kerja',
    'sasaran_obat',
    'tanaman_sasaran',
    'dosis_anjuran',
    'interval_aplikasi',
    'harga',
    'unsur_bahan',
    'fase',
    'foto_nota',
    'catatan_keamanan',
    'keterangan',
    'tanggal_beli',
    'toko_obat',
])]
class Medicine extends Model
{
    protected $appends = ['formatted_harga', 'foto_url', 'foto_urls'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'tanggal_beli' => 'date',
        ];
    }

    /**
     * Relasi ke riwayat pembelian / toko obat.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(MedicinePurchase::class)->orderBy('tanggal_beli', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Formatted rupiah price string.
     */
    public function getFormattedHargaAttribute(): ?string
    {
        if ($this->harga === null) {
            return null;
        }

        return 'Rp '.number_format($this->harga, 0, ',', '.');
    }

    /**
     * Array path foto nota tersimpan.
     *
     * @return array<string>
     */
    public function getFotoPathsAttribute(): array
    {
        if (! $this->foto_nota) {
            return [];
        }

        if (is_array($this->foto_nota)) {
            return $this->foto_nota;
        }

        $decoded = json_decode($this->foto_nota, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [$this->foto_nota];
    }

    /**
     * Array URL publik dari semua foto nota obat.
     *
     * @return array<string>
     */
    public function getFotoUrlsAttribute(): array
    {
        $paths = $this->foto_paths;
        $urls = [];
        foreach ($paths as $path) {
            if ($path) {
                $urls[] = Storage::disk('public')->url($path);
            }
        }

        // Cek jika ada foto pada salah satu pembelian jika foto utama kosong
        if (empty($urls)) {
            $purchases = $this->relationLoaded('purchases') ? $this->purchases : $this->purchases()->get();
            foreach ($purchases as $p) {
                if ($p->foto_url && ! in_array($p->foto_url, $urls, true)) {
                    $urls[] = $p->foto_url;
                }
            }
        }

        return $urls;
    }

    /**
     * URL Foto / Nota obat publik pertama (untuk backward compatibility).
     */
    public function getFotoUrlAttribute(): ?string
    {
        $urls = $this->foto_urls;

        return $urls[0] ?? null;
    }
}
