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
    'formulasi',
    'sasaran_obat',
    'tanaman_sasaran',
    'dosis_anjuran',
    'interval_aplikasi',
    'harga',
    'kemasan_jumlah',
    'kemasan_satuan',
    'unsur_bahan',
    'fase',
    'foto_nota',
    'catatan_keamanan',
    'keterangan',
    'tanggal_beli',
    'toko_obat',
    'stok',
    'stok_volume',
])]
class Medicine extends Model
{
    /**
     * Daftar formulasi pestisida (Kode => Ciri Fisik).
     */
    public const FORMULASI_OPTIONS = [
        'EC' => 'cairan pekat seperti minyak',
        'SC' => 'suspensi pekat',
        'WP' => 'serbuk kering',
        'SL' => 'cairan larut sempurna',
        'WG' => 'butiran granul',
        'OD' => 'dispersi minyak',
        'SP' => 'serbuk larut air',
    ];

    protected static function booted(): void
    {
        static::saving(function (Medicine $medicine) {
            $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
            if (($medicine->stok_volume === null || (float) $medicine->stok_volume == 0.0) && (int) ($medicine->stok ?? 0) > 0) {
                $medicine->stok_volume = (float) $medicine->stok * $baseTotal;
            }
        });
    }

    protected $appends = [
        'formatted_harga',
        'foto_url',
        'foto_urls',
        'formulasi_ciri_fisik',
        'formulasi_detail',
        'kemasan_label',
        'base_unit_label',
        'base_total_unit',
        'harga_per_base_unit',
        'formatted_harga_per_base_unit',
        'stok_label',
        'stok_volume_label',
    ];

    /**
     * Label kemasan (e.g. "1 kg", "500 gram", "100 ml").
     */
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

    /**
     * Satuan dasar untuk dosis (e.g. "gram" untuk kg/gram, "ml" untuk liter/ml).
     */
    public function getBaseUnitLabelAttribute(): string
    {
        $satuan = strtolower(trim((string) $this->kemasan_satuan));
        if (in_array($satuan, ['kg', 'kilogram', 'gram', 'gr', 'g'], true)) {
            return 'gram';
        }
        if (in_array($satuan, ['liter', 'ltr', 'lt', 'l', 'ml'], true)) {
            return 'ml';
        }
        if (in_array($satuan, ['sachet', 'bungkus'], true)) {
            return 'sachet';
        }

        if ($satuan !== '') {
            return $satuan;
        }

        $jenis = strtolower(trim((string) $this->jenis));
        if (in_array($jenis, ['perlengkapan', 'peralatan', 'alat'], true)) {
            return 'pcs';
        }

        $nama = strtolower(trim((string) $this->nama));
        if (preg_match('/(karung|kresek|tali|rapia|mulsa|ajir|bambu|polybag|polibag|tray|selang|terpal|plastik)/i', $nama)) {
            return 'pcs';
        }

        return 'gram';
    }

    /**
     * Total kapasitas kemasan dalam satuan dasar dosis (e.g. 1 kg -> 1000 gram).
     */
    public function getBaseTotalUnitAttribute(): float
    {
        $jumlah = (float) ($this->kemasan_jumlah ?? 0);
        $satuan = strtolower(trim((string) $this->kemasan_satuan));

        // Jika data kemasan belum tersimpan di kolom, coba deteksi dari nama obat
        if ($jumlah <= 0) {
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*(kg|kilogram|gram|gr|g|liter|ltr|lt|l|ml|sachet|pcs|buah|lembar|rol|roll|karung)\b/i', $this->nama, $m)) {
                $jumlah = (float) str_replace(',', '.', $m[1]);
                $satuan = strtolower($m[2]);
            } else {
                $jumlah = 1.0;
                $satuan = $this->base_unit_label;
            }
        }

        if (in_array($satuan, ['kg', 'kilogram', 'liter', 'ltr', 'lt', 'l'], true)) {
            return $jumlah * 1000.0;
        }

        return max(1.0, $jumlah);
    }

    /**
     * Estimasi harga per satuan dosis dasar (e.g. Rp / gram atau Rp / ml).
     */
    public function getHargaPerBaseUnitAttribute(): float
    {
        $totalBase = $this->base_total_unit;
        if ($totalBase <= 0 || ! $this->harga) {
            return 0.0;
        }

        return round((float) $this->harga / $totalBase, 2);
    }

    /**
     * Format Rupiah harga per satuan dosis dasar (e.g. "Rp 25 / gram").
     */
    public function getFormattedHargaPerBaseUnitAttribute(): ?string
    {
        $rate = $this->harga_per_base_unit;
        if ($rate <= 0) {
            return null;
        }

        $formattedRate = $rate < 10 && (fmod($rate, 1.0) !== 0.0)
            ? number_format($rate, 2, ',', '.')
            : number_format(round($rate), 0, ',', '.');

        return "Rp {$formattedRate} / {$this->base_unit_label}";
    }

    /**
     * Get ciri fisik dari kode formulasi obat.
     */
    public function getFormulasiCiriFisikAttribute(): ?string
    {
        if (! $this->formulasi) {
            return null;
        }

        return self::FORMULASI_OPTIONS[$this->formulasi] ?? null;
    }

    /**
     * Get formulasi lengkap (Kode & Ciri Fisik).
     */
    public function getFormulasiDetailAttribute(): ?string
    {
        if (! $this->formulasi) {
            return null;
        }

        $ciri = $this->formulasi_ciri_fisik;

        return $ciri ? "{$this->formulasi} • {$ciri}" : $this->formulasi;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'stok_volume' => 'float',
            'kemasan_jumlah' => 'float',
            'tanggal_beli' => 'date',
        ];
    }

    /**
     * Label stok (e.g. "5 botol", "10 (100 ml)", atau "5 unit").
     */
    public function getStokLabelAttribute(): string
    {
        $stok = (int) ($this->stok ?? 0);
        if ($this->kemasan_label) {
            $ks = strtolower(trim((string) $this->kemasan_satuan));
            if (in_array($ks, ['ml', 'l', 'liter', 'gram', 'g', 'kg'])) {
                return "{$stok} ({$this->kemasan_label})";
            }

            return "{$stok} {$this->kemasan_satuan}";
        }

        $satuan = $this->kemasan_satuan ? " {$this->kemasan_satuan}" : ' unit';

        return "{$stok}{$satuan}";
    }

    /**
     * Label volume stok presisi per satuan dasar (e.g. "170 ml", "950 gram", "8 pcs").
     */
    public function getStokVolumeLabelAttribute(): string
    {
        $vol = (float) ($this->stok_volume ?? ((float) ($this->stok ?? 0) * $this->base_total_unit));
        $formatted = (float) $vol == (int) $vol
            ? number_format($vol, 0, ',', '.')
            : number_format($vol, 1, ',', '.');

        return "{$formatted} {$this->base_unit_label}";
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
            if ($path && is_string($path) && trim($path) !== '') {
                $trimmed = trim($path);
                if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
                    $urls[] = $trimmed;
                } else {
                    $urls[] = Storage::disk('public')->url($trimmed);
                }
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

        return array_values(array_filter($urls));
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
