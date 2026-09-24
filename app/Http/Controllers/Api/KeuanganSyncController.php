<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KeuanganSyncController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    /**
     * Snapshot data keuangan untuk offline store initial sync.
     */
    public function index(): JsonResponse
    {
        $transactions = FinancialTransaction::with('crop')->latest('tanggal')->get();
        $categories = FinancialCategory::parents()->with('subcategories')->get();

        $transaksiList = $transactions->map(function (FinancialTransaction $m): array {
            $cropInfo = $m->crop ? $m->crop->nama_tanaman : null;
            $deskripsi = $cropInfo ? ($cropInfo.($m->keterangan ? ' • '.$m->keterangan : '')) : ($m->keterangan ?: '—');

            return [
                'id' => 'transaksi-'.$m->id,
                'raw_id' => $m->id,
                'source' => 'manual',
                'tipe' => $m->tipe,
                'kategori' => $m->kategori,
                'sub_kategori' => $m->sub_kategori,
                'kategori_label' => $m->sub_kategori ? ($m->kategori.' › '.$m->sub_kategori) : $m->kategori,
                'judul' => $m->judul,
                'deskripsi' => $deskripsi,
                'tanggal' => $m->tanggal ? $m->tanggal->format('Y-m-d') : null,
                'tanggal_raw' => $m->tanggal ? $m->tanggal->format('Y-m-d') : null,
                'formatted_tanggal' => $m->tanggal ? $m->tanggal->format('d M Y') : '—',
                'nominal' => (float) $m->nominal,
                'formatted_nominal' => $m->formatted_nominal,
                'nota_url' => $m->foto_url,
                'crop_id' => $m->crop_id,
                'keterangan' => $m->keterangan,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'transactions' => $transaksiList,
            'categories' => $categories,
        ]);
    }

    /**
     * Memproses antrean mutasi offline keuangan (transaksi & kategori).
     */
    public function sync(Request $request): JsonResponse
    {
        $mutations = $request->input('mutations', []);

        if (! is_array($mutations) || empty($mutations)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada antrean sinkronisasi keuangan.',
                'processed_count' => 0,
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

                // 1. Mutasi Transaksi
                if ($action === 'create_transaction' || $action === 'create') {
                    $fotoUrl = null;
                    $base64 = $data['foto_base64'] ?? $data['foto_nota_base64'] ?? null;
                    if ($base64 && is_string($base64)) {
                        $fotoUrl = $this->saveBase64Photo($base64);
                    }

                    $nominal = isset($data['nominal']) ? (int) preg_replace('/[^0-9]/', '', (string) $data['nominal']) : 0;
                    if ($nominal <= 0) {
                        $nominal = 1;
                    }

                    $trx = FinancialTransaction::create([
                        'crop_id' => ! empty($data['crop_id']) ? (int) $data['crop_id'] : null,
                        'tipe' => in_array($data['tipe'] ?? '', ['pemasukan', 'pengeluaran']) ? $data['tipe'] : 'pengeluaran',
                        'kategori' => $data['kategori'] ?? 'Lainnya',
                        'sub_kategori' => $data['sub_kategori'] ?? null,
                        'judul' => $data['judul'] ?? 'Catatan Keuangan Offline',
                        'nominal' => $nominal,
                        'tanggal' => $data['tanggal'] ?? now()->format('Y-m-d'),
                        'keterangan' => $data['keterangan'] ?? null,
                        'foto_nota' => $fotoUrl,
                    ]);

                    if ($localId) {
                        $idMappings[$localId] = $trx->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update_transaction' || $action === 'update') {
                    $rawId = $data['raw_id'] ?? $data['id'] ?? null;
                    if ($rawId && isset($idMappings[$rawId])) {
                        $rawId = $idMappings[$rawId];
                    }

                    if ($rawId) {
                        $trx = FinancialTransaction::find($rawId);
                        if ($trx) {
                            $updateData = [];
                            if (isset($data['tipe'])) {
                                $updateData['tipe'] = $data['tipe'];
                            }
                            if (isset($data['kategori'])) {
                                $updateData['kategori'] = $data['kategori'];
                            }
                            if (array_key_exists('sub_kategori', $data)) {
                                $updateData['sub_kategori'] = $data['sub_kategori'];
                            }
                            if (isset($data['judul'])) {
                                $updateData['judul'] = $data['judul'];
                            }
                            if (isset($data['nominal'])) {
                                $updateData['nominal'] = (int) preg_replace('/[^0-9]/', '', (string) $data['nominal']);
                            }
                            if (isset($data['tanggal'])) {
                                $updateData['tanggal'] = $data['tanggal'];
                            }
                            if (array_key_exists('crop_id', $data)) {
                                $updateData['crop_id'] = ! empty($data['crop_id']) ? (int) $data['crop_id'] : null;
                            }
                            if (array_key_exists('keterangan', $data)) {
                                $updateData['keterangan'] = $data['keterangan'];
                            }

                            $base64 = $data['foto_base64'] ?? $data['foto_nota_base64'] ?? null;
                            if ($base64 && is_string($base64)) {
                                $newPhoto = $this->saveBase64Photo($base64);
                                if ($newPhoto) {
                                    $updateData['foto_nota'] = $newPhoto;
                                }
                            }

                            $trx->update($updateData);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_transaction' || $action === 'delete') {
                    $rawId = $data['raw_id'] ?? $data['id'] ?? null;
                    if ($rawId && isset($idMappings[$rawId])) {
                        $rawId = $idMappings[$rawId];
                    }

                    if ($rawId) {
                        $trx = FinancialTransaction::find($rawId);
                        if ($trx) {
                            $trx->delete();
                            $processedCount++;
                        }
                    }
                }

                // 2. Mutasi Kategori
                elseif ($action === 'create_category') {
                    $parentId = ! empty($data['parent_id']) ? (int) $data['parent_id'] : null;
                    if ($parentId && isset($idMappings[$parentId])) {
                        $parentId = $idMappings[$parentId];
                    }

                    $cat = FinancialCategory::create([
                        'parent_id' => $parentId,
                        'tipe' => in_array($data['tipe'] ?? '', ['pemasukan', 'pengeluaran', 'keduanya']) ? $data['tipe'] : 'pengeluaran',
                        'nama' => $data['nama'] ?? 'Kategori Baru',
                        'keterangan' => $data['keterangan'] ?? null,
                        'urutan' => isset($data['urutan']) ? (int) $data['urutan'] : 0,
                    ]);

                    if ($localId) {
                        $idMappings[$localId] = $cat->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update_category') {
                    $catId = $data['id'] ?? null;
                    if ($catId && isset($idMappings[$catId])) {
                        $catId = $idMappings[$catId];
                    }

                    if ($catId) {
                        $cat = FinancialCategory::find($catId);
                        if ($cat) {
                            $parentId = array_key_exists('parent_id', $data) ? (! empty($data['parent_id']) ? (int) $data['parent_id'] : null) : $cat->parent_id;
                            if ($parentId && isset($idMappings[$parentId])) {
                                $parentId = $idMappings[$parentId];
                            }

                            $cat->update([
                                'parent_id' => $parentId,
                                'tipe' => $data['tipe'] ?? $cat->tipe,
                                'nama' => $data['nama'] ?? $cat->nama,
                                'keterangan' => array_key_exists('keterangan', $data) ? $data['keterangan'] : $cat->keterangan,
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_category') {
                    $catId = $data['id'] ?? null;
                    if ($catId && isset($idMappings[$catId])) {
                        $catId = $idMappings[$catId];
                    }

                    if ($catId) {
                        $cat = FinancialCategory::find($catId);
                        if ($cat) {
                            $cat->delete();
                            $processedCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyinkronkan {$processedCount} data keuangan.",
                'processed_count' => $processedCount,
                'id_mappings' => $idMappings,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Keuangan sync error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses sinkronisasi keuangan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Decode base64 dan upload foto nota.
     */
    private function saveBase64Photo(string $base64): ?string
    {
        $extension = 'jpg';
        $data = $base64;

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            $data = substr($base64, strpos($base64, ',') + 1);
            $ext = strtolower($type[1]);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $extension = $ext === 'jpeg' ? 'jpg' : $ext;
            }
        }

        $binary = base64_decode($data);
        if ($binary === false || strlen($binary) === 0) {
            return null;
        }

        $tempPath = sys_get_temp_dir().'/nota_'.Str::random(32).'.'.$extension;
        file_put_contents($tempPath, $binary);

        try {
            $uploadResult = $this->cloudinaryService->upload($tempPath, 'pertanian/keuangan');
            @unlink($tempPath);

            return $uploadResult['url'] ?? null;
        } catch (\Throwable $e) {
            @unlink($tempPath);
            // Fallback simpan ke storage public lokal
            $filename = 'keuangan/nota_'.Str::random(40).'.'.$extension;
            Storage::disk('public')->put($filename, $binary);

            return $filename;
        }
    }
}
