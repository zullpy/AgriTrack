<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $fillable = [
        'nama_tanaman',
        'varietas',
        'populasi',
        'tanggal_tanam',
        'status',
        'tanggal_panen',
        'total_hst_panen',
        'total_panen',
        'harga_panen',
        'total_harga_kotor',
        'catatan',
    ];

    protected $appends = [
        'current_hst',
        'emoji',
        'next_harvest_number',
        'harvest_count',
        'total_pendapatan_kotor',
        'formatted_total_pendapatan_kotor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_tanam' => 'date',
            'tanggal_panen' => 'date',
            'total_hst_panen' => 'integer',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CropActivity::class)->orderBy('target_hst');
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(CropHarvest::class)->orderBy('panen_ke');
    }

    public function getNextHarvestNumberAttribute(): int
    {
        return ($this->relationLoaded('harvests') ? $this->harvests->count() : $this->harvests()->count()) + 1;
    }

    public function getHarvestCountAttribute(): int
    {
        return $this->relationLoaded('harvests') ? $this->harvests->count() : $this->harvests()->count();
    }

    public function getTotalPendapatanKotorAttribute(): float
    {
        return (float) ($this->relationLoaded('harvests')
            ? $this->harvests->sum(fn ($h) => $h->numeric_harga_kotor)
            : $this->harvests()->get()->sum(fn ($h) => $h->numeric_harga_kotor));
    }

    public function getFormattedTotalPendapatanKotorAttribute(): string
    {
        $val = $this->total_pendapatan_kotor;

        return $val > 0 ? 'Rp '.number_format($val, 0, ',', '.') : '—';
    }

    /**
     * Hitung HST berjalan hari ini (atau HST terkunci jika sudah dipanen / diakhiri).
     */
    public function getCurrentHstAttribute(): int
    {
        if ($this->status !== 'Sedang Ditanam') {
            return (int) ($this->total_hst_panen ?? 0);
        }

        $plantDate = Carbon::parse($this->tanggal_tanam)->startOfDay();
        $today = now()->startOfDay();

        if ($today->lt($plantDate)) {
            return 0;
        }

        return (int) $plantDate->diffInDays($today);
    }

    /**
     * Hitung HST pada tanggal tertentu untuk tampilan kalender.
     */
    public function getHstOnDate(Carbon $date): ?int
    {
        $plantDate = Carbon::parse($this->tanggal_tanam)->startOfDay();
        $targetDate = $date->copy()->startOfDay();

        // Sebelum tanggal tanam
        if ($targetDate->lt($plantDate)) {
            return null;
        }

        // Jika sudah dipanen / diakhiri dan tanggal cek melewati tanggal panen / akhir
        if ($this->status !== 'Sedang Ditanam' && $this->tanggal_panen) {
            $harvestDate = Carbon::parse($this->tanggal_panen)->startOfDay();
            if ($targetDate->gt($harvestDate)) {
                return null;
            }
        }

        return (int) $plantDate->diffInDays($targetDate);
    }

    /**
     * Dapatkan emoji tanaman sesuai katalog atau kamus komoditas umum.
     */
    public static function getEmojiForName(string $name, ?string $varietas = null): string
    {
        $name = strtolower(trim($name));
        $varietas = $varietas ? strtolower(trim($varietas)) : '';

        // 1. Cek dari PlantCatalog aktif (pencocokan nama & keywords persis seperti di Dashboard)
        try {
            $catalogs = PlantCatalog::aktif()->get();
            $matched = $catalogs->first(function (PlantCatalog $cat) use ($name, $varietas) {
                $catName = strtolower($cat->name);
                if ($catName === $name || str_contains($name, $catName) || str_contains($catName, $name)) {
                    return true;
                }
                if ($varietas && (str_contains($varietas, $catName) || str_contains($catName, $varietas))) {
                    return true;
                }
                if ($cat->keywords) {
                    $kws = array_map('trim', explode(',', strtolower($cat->keywords)));
                    foreach ($kws as $kw) {
                        if ($kw && (str_contains($name, $kw) || ($varietas && str_contains($varietas, $kw)))) {
                            return true;
                        }
                    }
                }

                return false;
            });

            if ($matched && ! empty($matched->emoji)) {
                return $matched->emoji;
            }
        } catch (\Throwable) {
            // fallback below
        }

        // 2. Fallback kamus emoji komoditas umum
        $map = [
            'jagung' => '🌽',
            'padi' => '🌾',
            'beras' => '🌾',
            'cabai' => '🌶️',
            'cabe' => '🌶️',
            'tomat' => '🍅',
            'terong' => '🍆',
            'mentimun' => '🥒',
            'timun' => '🥒',
            'semangka' => '🍉',
            'melon' => '🍈',
            'bawang' => '🧅',
            'kentang' => '🥔',
            'wortel' => '🥕',
            'singkong' => '🌿',
            'kedelai' => '🫘',
            'kacang' => '🥜',
            'bayam' => '🥬',
            'sawi' => '🥬',
            'kangkung' => '🥬',
            'kopi' => '☕',
            'kakao' => '🍫',
            'sawit' => '🌴',
            'tebu' => '🎋',
            'tembakau' => '🍂',
        ];

        foreach ($map as $keyword => $emoji) {
            if (str_contains($name, $keyword) || ($varietas && str_contains($varietas, $keyword))) {
                return $emoji;
            }
        }

        return '🌱';
    }

    /**
     * Dapatkan emoji tanaman sesuai katalog atau nama komoditas.
     */
    public function getEmojiAttribute(): string
    {
        return self::getEmojiForName($this->nama_tanaman ?? '', $this->varietas ?? null);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Sedang Ditanam');
    }

    public function scopeHarvested(Builder $query): Builder
    {
        return $query->whereIn('status', ['Sudah Dipanen', 'Diakhiri']);
    }

    /**
     * Rangkuman akumulasi pemakaian obat untuk tanaman ini.
     *
     * @return array{
     *     total_biaya: float,
     *     formatted_total_biaya: string,
     *     total_aplikasi_count: int,
     *     total_tank_count: float,
     *     formatted_total_tank: string,
     *     total_jenis_obat: int,
     *     items: array<int, array<string, mixed>>
     * }
     */
    public function getMedicineSummaryAttribute(): array
    {
        $activities = $this->relationLoaded('activities')
            ? $this->activities
            : $this->activities()->orderBy('target_hst')->get();

        static $allMedicines = null;
        if ($allMedicines === null) {
            $allMedicines = Medicine::all(['id', 'nama', 'jenis', 'harga', 'kemasan_jumlah', 'kemasan_satuan']);
        }

        $aggregated = [];
        $totalAplikasiCount = 0;
        $totalTankCount = 0.0;
        $totalBiayaAkumulasi = 0.0;

        foreach ($activities as $act) {
            if (empty($act->aplikasi_obat)) {
                continue;
            }

            $totalAplikasiCount++;
            $tanks = (float) ($act->jumlah_tank ?? 1.0);
            if ($tanks <= 0) {
                $tanks = 1.0;
            }
            $totalTankCount += $tanks;

            $rowExplicitBiaya = (float) ($act->total_biaya_obat ?? 0);

            // Parse obat dari string aplikasi_obat
            $rawItems = array_filter(array_map('trim', explode(',', $act->aplikasi_obat)));
            $rowParsedBiayaTotal = 0.0;

            foreach ($rawItems as $rawItem) {
                $itemBiaya = 0.0;
                if (preg_match('/\(Rp\s*([\d.,]+)\)/i', $rawItem, $mPrice)) {
                    $itemBiaya = (float) preg_replace('/[^\d]/', '', $mPrice[1]);
                    $rawItem = trim(preg_replace('/\(Rp\s*[\d.,]+\)/i', '', $rawItem));
                }

                $medName = '';
                $doseText = '';
                $doseQty = 0.0;
                $doseUnit = '';

                if (str_contains($rawItem, ':')) {
                    [$namePart, $dPart] = explode(':', $rawItem, 2);
                    $medName = trim($namePart);
                    $doseText = trim($dPart);
                } elseif (preg_match('/^(.*?)\s*(\d+(?:[.,]\d+)?\s*(?:ml|liter|ltr|lt|l|gram|gr|g|kg|sachet|bungkus|sendok|sdm|sdt)\b.*)$/i', $rawItem, $mDose)) {
                    $medName = trim($mDose[1]);
                    $doseText = trim($mDose[2]);
                } else {
                    $medName = $rawItem;
                }

                if (! empty($doseText) && preg_match('/^(\d+(?:[.,]\d+)?)\s*(.*)$/i', $doseText, $mUnit)) {
                    $doseQty = (float) str_replace(',', '.', $mUnit[1]);
                    $doseUnit = strtolower(trim($mUnit[2]));
                }

                $matched = $allMedicines->first(function ($m) use ($medName) {
                    return strcasecmp($m->nama, $medName) === 0 || str_contains(strtolower($medName), strtolower($m->nama));
                });

                $canonicalName = $matched ? $matched->nama : $medName;
                $jenis = $matched ? $matched->jenis : null;
                $baseUnit = $doseUnit ?: ($matched ? $matched->base_unit_label : 'satuan');

                if ($itemBiaya <= 0 && $doseQty > 0 && $matched && $matched->harga_per_base_unit > 0) {
                    $multiplier = 1.0;
                    if (in_array($doseUnit, ['kg', 'kilogram', 'liter', 'ltr', 'lt', 'l'])) {
                        $multiplier = 1000.0;
                    }
                    $itemBiaya = round($doseQty * $multiplier * $matched->harga_per_base_unit * $tanks);
                }

                $rowParsedBiayaTotal += $itemBiaya;
                $totalDoseInField = $doseQty > 0 ? ($doseQty * $tanks) : 0.0;

                $normKey = strtolower($canonicalName);
                if (! isset($aggregated[$normKey])) {
                    $aggregated[$normKey] = [
                        'nama' => $canonicalName,
                        'jenis' => $jenis,
                        'frekuensi' => 0,
                        'total_tank' => 0.0,
                        'total_dosis_qty' => [],
                        'total_biaya' => 0.0,
                        'riwayat' => [],
                    ];
                }

                $aggregated[$normKey]['frekuensi']++;
                $aggregated[$normKey]['total_tank'] += $tanks;
                $aggregated[$normKey]['total_biaya'] += $itemBiaya;

                if ($totalDoseInField > 0) {
                    $aggregated[$normKey]['total_dosis_qty'][$baseUnit] = ($aggregated[$normKey]['total_dosis_qty'][$baseUnit] ?? 0.0) + $totalDoseInField;
                }

                $fmtTotalDose = $totalDoseInField > 0
                    ? (((float) $totalDoseInField == (int) $totalDoseInField ? (string) (int) $totalDoseInField : (string) round($totalDoseInField, 1)).' '.$baseUnit)
                    : '—';

                $aggregated[$normKey]['riwayat'][] = [
                    'hst' => $act->target_hst,
                    'tanggal' => $act->target_date ? $act->target_date->translatedFormat('d M Y') : null,
                    'nama_kegiatan' => $act->nama_kegiatan,
                    'dosis_per_tank_text' => $doseQty > 0 ? (((float) $doseQty == (int) $doseQty ? (string) (int) $doseQty : (string) $doseQty).' '.$baseUnit.'/tank') : '—',
                    'jumlah_tank' => $tanks,
                    'total_dosis_text' => $fmtTotalDose,
                    'biaya' => $itemBiaya,
                    'formatted_biaya' => $itemBiaya > 0 ? 'Rp '.number_format($itemBiaya, 0, ',', '.') : '—',
                ];
            }

            $totalBiayaAkumulasi += ($rowExplicitBiaya > 0 ? $rowExplicitBiaya : $rowParsedBiayaTotal);
        }

        foreach ($aggregated as &$item) {
            $doseParts = [];
            foreach ($item['total_dosis_qty'] as $u => $qty) {
                $fmtQty = (float) $qty == (int) $qty ? (string) (int) $qty : (string) round($qty, 1);
                $doseParts[] = "{$fmtQty} {$u}";
            }
            $item['total_dosis_text'] = ! empty($doseParts) ? implode(', ', $doseParts) : '—';
            $item['formatted_total_biaya'] = $item['total_biaya'] > 0 ? 'Rp '.number_format($item['total_biaya'], 0, ',', '.') : '—';
            $fmtTank = (float) $item['total_tank'] == (int) $item['total_tank'] ? (string) (int) $item['total_tank'] : (string) $item['total_tank'];
            $item['formatted_total_tank'] = $fmtTank.' Tank';
        }
        unset($item);

        uasort($aggregated, fn ($a, $b) => $b['total_biaya'] <=> $a['total_biaya'] ?: $b['frekuensi'] <=> $a['frekuensi']);

        $fmtOverallTank = (float) $totalTankCount == (int) $totalTankCount ? (string) (int) $totalTankCount : (string) round($totalTankCount, 1);

        return [
            'total_biaya' => $totalBiayaAkumulasi,
            'formatted_total_biaya' => $totalBiayaAkumulasi > 0 ? 'Rp '.number_format($totalBiayaAkumulasi, 0, ',', '.') : 'Rp 0',
            'total_aplikasi_count' => $totalAplikasiCount,
            'total_tank_count' => $totalTankCount,
            'formatted_total_tank' => $fmtOverallTank.' Tank',
            'total_jenis_obat' => count($aggregated),
            'items' => array_values($aggregated),
        ];
    }
}
