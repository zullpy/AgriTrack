<?php

namespace App\Services;

use App\Models\CropActivity;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;

class MedicineStockService
{
    /**
     * Satuan-satuan yang dikategorikan sebagai perlengkapan/peralatan (tidak dikali jumlah tank).
     */
    public const EQUIPMENT_UNITS = [
        'pcs', 'buah', 'lembar', 'rol', 'roll', 'gulung', 'ikat', 'batang', 'set', 'karung', 'unit', 'biji', 'pack', 'pak', 'meter', 'm', 'tali', 'kresek', 'sak', 'kantong', 'pohon',
    ];

    /**
     * Parse daftar pemakaian obat & perlengkapan dari string aplikasi_obat suatu kegiatan.
     *
     * @return array<int, array{medicine_id: int, medicine_name: string, dose_per_tank: float, amount: float, unit: string, is_equipment: bool}>
     */
    public function parseActivityMedicines(CropActivity $activity): array
    {
        $rawObat = $activity->aplikasi_obat;
        if (! $rawObat || trim($rawObat) === '') {
            return [];
        }

        $allMedicines = Medicine::all();
        if ($allMedicines->isEmpty()) {
            return [];
        }

        $tanks = (float) ($activity->jumlah_tank ?? 1.0);
        if ($tanks <= 0) {
            $tanks = 1.0;
        }

        $rawItems = array_filter(array_map('trim', explode(',', $rawObat)));
        $parsedResults = [];

        foreach ($rawItems as $rawItem) {
            // Hilangkan format harga jika ada, misal "(Rp 15.000)"
            if (preg_match('/\(Rp\s*[\d.,]+\)/i', $rawItem)) {
                $rawItem = trim(preg_replace('/\(Rp\s*[\d.,]+\)/i', '', $rawItem));
            }

            $medName = '';
            $doseText = '';
            $doseQty = 0.0;
            $doseUnit = '';

            if (str_contains($rawItem, ':')) {
                // Format: "Nama Obat: 15 ml" atau "Kategori: Nama Obat 15 ml"
                $parts = explode(':', $rawItem);
                if (count($parts) === 2) {
                    $medName = trim($parts[0]);
                    $doseText = trim($parts[1]);
                } else {
                    // Jika ada lebih dari satu titik dua, e.g. "Insektisida: Curacron: 15 ml"
                    $medName = trim($parts[1]);
                    $doseText = trim(end($parts));
                }
            } elseif (preg_match('/^(.*?)\s*(\d+(?:[.,]\d+)?\s*(?:ml|liter|ltr|lt|l|gram|gr|g|kg|sachet|bungkus|sendok|sdm|sdt|pcs|buah|lembar|rol|roll|gulung|ikat|batang|set|karung|unit|biji|pack|pak|meter|m|sak|kantong|tali|kresek|pohon)\b.*)$/i', $rawItem, $mDose)) {
                $medName = trim($mDose[1]);
                $doseText = trim($mDose[2]);
            } else {
                $medName = $rawItem;
            }

            // Bersihkan nama obat dari prefix kategori umum jika tersisa
            $medName = preg_replace('/^(insektisida|fungisida|herbisida|pupuk|vitamin|perekat|bakterisida|nematisida|perlengkapan|peralatan)\s*[:\-]\s*/i', '', $medName);

            // Ekstrak angka dosis dan satuan dari $doseText
            if (! empty($doseText) && preg_match('/^(\d+(?:[.,]\d+)?)\s*([a-zA-Z]+)?/i', $doseText, $mUnit)) {
                $doseQty = (float) str_replace(',', '.', $mUnit[1]);
                $doseUnit = strtolower(trim($mUnit[2] ?? ''));
            }

            // Cocokkan ke database Medicine
            $matched = $allMedicines->first(function ($m) use ($medName) {
                return strcasecmp($m->nama, $medName) === 0;
            });

            if (! $matched) {
                $matched = $allMedicines->first(function ($m) use ($medName) {
                    return str_contains(strtolower($medName), strtolower($m->nama));
                });
            }

            if (! $matched) {
                $matched = $allMedicines->first(function ($m) use ($medName) {
                    return str_contains(strtolower($m->nama), strtolower($medName));
                });
            }

            if (! $matched) {
                continue;
            }

            // Tentukan apakah item ini adalah perlengkapan / peralatan
            $isEquipment = $this->isEquipmentItem($matched, $doseUnit);

            $finalUnit = $doseUnit ?: $matched->base_unit_label;
            $unitLower = strtolower($finalUnit);

            if ($isEquipment) {
                // Perlengkapan: dipakai per pcs / unit langsung, TIDAK dikalikan tank!
                $totalUsed = max(1.0, $doseQty > 0 ? $doseQty : 1.0);
            } else {
                // Obat/Pupuk: dosis per tangki semprot, dikalikan jumlah tank
                $baseQty = $doseQty > 0 ? $doseQty : 1.0;
                $multiplier = 1.0;

                if (in_array($unitLower, ['kg', 'kilogram', 'liter', 'ltr', 'lt', 'l'], true)) {
                    $multiplier = 1000.0;
                } elseif (in_array($unitLower, ['sendok', 'sdm'], true)) {
                    $multiplier = $matched->base_unit_label === 'ml' ? 15.0 : 10.0;
                }

                $totalUsed = ($baseQty * $multiplier) * $tanks;
                $finalUnit = $matched->base_unit_label;
            }

            $parsedResults[] = [
                'medicine_id' => $matched->id,
                'medicine_name' => $matched->nama,
                'dose_per_tank' => $doseQty,
                'amount' => round($totalUsed, 2),
                'unit' => $finalUnit,
                'is_equipment' => $isEquipment,
            ];
        }

        return $parsedResults;
    }

    /**
     * Periksa apakah suatu obat / barang tergolong perlengkapan / alat.
     */
    public function isEquipmentItem(Medicine $medicine, ?string $doseUnit = null): bool
    {
        $unit = strtolower(trim((string) ($doseUnit ?: $medicine->base_unit_label)));
        if (in_array($unit, self::EQUIPMENT_UNITS, true)) {
            return true;
        }

        $jenis = strtolower(trim((string) $medicine->jenis));
        if (str_contains($jenis, 'perlengkapan') || str_contains($jenis, 'peralatan') || str_contains($jenis, 'alat') || str_contains($jenis, 'bibit') || str_contains($jenis, 'benih')) {
            return true;
        }

        $nama = strtolower(trim((string) $medicine->nama));
        if (preg_match('/\b(karung|kresek|tali|rapia|mulsa|ajir|bambu|polybag|polibag|tray|selang|terpal|plastik|paranet|gembor|sprayer|kardus|kantong|bibit|benih)\b/i', $nama)) {
            return true;
        }

        return false;
    }

    /**
     * Potong stok obat & perlengkapan saat kegiatan ditandai Selesai.
     *
     * @return array<int, array{medicine_id: int, medicine_name: string, dose_per_tank: float, amount: float, unit: string, is_equipment: bool}>
     */
    public function deductForActivity(CropActivity $activity): array
    {
        if ($activity->stok_dipotong) {
            return (array) ($activity->stok_deductions ?? []);
        }

        $items = $this->parseActivityMedicines($activity);
        if (empty($items)) {
            return [];
        }

        DB::transaction(function () use ($activity, $items) {
            foreach ($items as $item) {
                /** @var Medicine|null $medicine */
                $medicine = Medicine::where('id', $item['medicine_id'])->lockForUpdate()->first();
                if (! $medicine) {
                    continue;
                }

                $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
                $currentVolume = ((float) ($medicine->stok_volume ?? 0) > 0)
                    ? (float) $medicine->stok_volume
                    : ((float) ($medicine->stok ?? 0) * $baseTotal);

                $deductAmount = (float) $item['amount'];
                $newVolume = max(0.0, $currentVolume - $deductAmount);
                $newStok = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;

                $medicine->updateQuietly([
                    'stok_volume' => $newVolume,
                    'stok' => $newStok,
                ]);
            }

            $activity->updateQuietly([
                'stok_dipotong' => true,
                'stok_deductions' => $items,
            ]);
        });

        return $items;
    }

    /**
     * Kembalikan stok obat & perlengkapan saat status kegiatan dibatalkan dari Selesai menjadi Belum,
     * atau saat kegiatan dihapus.
     */
    public function restoreForActivity(CropActivity $activity): void
    {
        if (! $activity->stok_dipotong || empty($activity->stok_deductions)) {
            return;
        }

        $deductions = is_array($activity->stok_deductions)
            ? $activity->stok_deductions
            : json_decode($activity->stok_deductions, true);

        if (! is_array($deductions) || empty($deductions)) {
            $activity->updateQuietly([
                'stok_dipotong' => false,
                'stok_deductions' => null,
            ]);

            return;
        }

        DB::transaction(function () use ($activity, $deductions) {
            foreach ($deductions as $item) {
                if (empty($item['medicine_id']) || empty($item['amount'])) {
                    continue;
                }

                /** @var Medicine|null $medicine */
                $medicine = Medicine::where('id', $item['medicine_id'])->lockForUpdate()->first();
                if (! $medicine) {
                    continue;
                }

                $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
                $currentVolume = ((float) ($medicine->stok_volume ?? 0) > 0)
                    ? (float) $medicine->stok_volume
                    : ((float) ($medicine->stok ?? 0) * $baseTotal);

                $restoreAmount = (float) $item['amount'];
                $newVolume = $currentVolume + $restoreAmount;
                $newStok = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;

                $medicine->updateQuietly([
                    'stok_volume' => $newVolume,
                    'stok' => $newStok,
                ]);
            }

            $activity->updateQuietly([
                'stok_dipotong' => false,
                'stok_deductions' => null,
            ]);
        });
    }

    /**
     * Sinkronisasi penambahan stok obat dari pembelian baru.
     */
    public function addPurchasedStock(Medicine $medicine, float $purchasedQuantity): void
    {
        $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
        $currentVolume = $medicine->stok_volume !== null
            ? (float) $medicine->stok_volume
            : ((float) ($medicine->stok ?? 0) * $baseTotal);

        $addedVolume = $purchasedQuantity * $baseTotal;
        $newVolume = $currentVolume + $addedVolume;
        $newStok = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;

        $medicine->updateQuietly([
            'stok_volume' => $newVolume,
            'stok' => $newStok,
        ]);
    }
}
