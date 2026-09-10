<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineSyncController extends Controller
{
    /**
     * Get all medicines for offline cache initial load.
     */
    public function index(): JsonResponse
    {
        $medicines = Medicine::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $medicines,
        ]);
    }

    /**
     * Process offline mutations queue (creates, updates, deletes).
     */
    public function sync(Request $request): JsonResponse
    {
        $mutations = $request->input('mutations', []);

        if (!is_array($mutations) || empty($mutations)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada antrean sinkronisasi.',
                'medicines' => Medicine::latest()->get(),
            ]);
        }

        $processedCount = 0;
        $idMappings = [];

        DB::beginTransaction();

        try {
            foreach ($mutations as $item) {
                $action = $item['action'] ?? null;
                $data = $item['data'] ?? [];
                $localId = $item['local_id'] ?? null;

                $validTypes = ['Fungisida', 'Insektisida', 'Pupuk', 'Vitamin', 'Bibit', 'Perlengkapan', 'Peralatan'];

                if ($action === 'create') {
                    $medicine = Medicine::create([
                        'nama' => $data['nama'] ?? 'Tanpa Nama',
                        'jenis' => in_array($data['jenis'] ?? '', $validTypes) ? $data['jenis'] : 'Fungisida',
                        'cara_kerja' => $data['cara_kerja'] ?? null,
                        'sasaran_obat' => $data['sasaran_obat'] ?? null,
                        'tanaman_sasaran' => $data['tanaman_sasaran'] ?? '-',
                        'dosis_anjuran' => $data['dosis_anjuran'] ?? null,
                        'interval_aplikasi' => $data['interval_aplikasi'] ?? null,
                        'harga' => isset($data['harga']) && is_numeric($data['harga']) ? (int)$data['harga'] : null,
                        'unsur_bahan' => $data['unsur_bahan'] ?? null,
                        'fase' => $data['fase'] ?? null,
                        'catatan_keamanan' => $data['catatan_keamanan'] ?? null,
                        'keterangan' => $data['keterangan'] ?? null,
                        'tanggal_beli' => $data['tanggal_beli'] ?? null,
                        'toko_obat' => $data['toko_obat'] ?? null,
                    ]);

                    if ($localId) {
                        $idMappings[$localId] = $medicine->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $medicine = Medicine::find($id);
                        if ($medicine) {
                            $updateData = [];
                            if (isset($data['nama'])) $updateData['nama'] = $data['nama'];
                            if (isset($data['jenis']) && in_array($data['jenis'], $validTypes)) $updateData['jenis'] = $data['jenis'];
                            if (array_key_exists('cara_kerja', $data)) $updateData['cara_kerja'] = $data['cara_kerja'];
                            if (array_key_exists('sasaran_obat', $data)) $updateData['sasaran_obat'] = $data['sasaran_obat'];
                            if (isset($data['tanaman_sasaran'])) $updateData['tanaman_sasaran'] = $data['tanaman_sasaran'];
                            if (array_key_exists('dosis_anjuran', $data)) $updateData['dosis_anjuran'] = $data['dosis_anjuran'];
                            if (array_key_exists('interval_aplikasi', $data)) $updateData['interval_aplikasi'] = $data['interval_aplikasi'];
                            if (array_key_exists('harga', $data)) $updateData['harga'] = is_numeric($data['harga']) ? (int)$data['harga'] : null;
                            if (array_key_exists('unsur_bahan', $data)) $updateData['unsur_bahan'] = $data['unsur_bahan'];
                            if (array_key_exists('fase', $data)) $updateData['fase'] = $data['fase'];
                            if (array_key_exists('catatan_keamanan', $data)) $updateData['catatan_keamanan'] = $data['catatan_keamanan'];
                            if (array_key_exists('keterangan', $data)) $updateData['keterangan'] = $data['keterangan'];
                            if (array_key_exists('tanggal_beli', $data)) $updateData['tanggal_beli'] = $data['tanggal_beli'];
                            if (array_key_exists('toko_obat', $data)) $updateData['toko_obat'] = $data['toko_obat'];

                            $medicine->update($updateData);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $medicine = Medicine::find($id);
                        if ($medicine) {
                            $medicine->delete();
                            $processedCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyinkronkan {$processedCount} perubahan data.",
                'processed_count' => $processedCount,
                'id_mappings' => $idMappings,
                'medicines' => Medicine::latest()->get(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Offline sync error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses sinkronisasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
