<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropActivity;
use App\Services\CloudinaryService;
use App\Services\ImageCompressionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KalenderHstSyncController extends Controller
{
    public function __construct(
        protected ImageCompressionService $compressionService,
        protected CloudinaryService $cloudinaryService
    ) {}

    /**
     * Process offline mutations queue for Kalender HST activities and harvests.
     */
    public function sync(Request $request): JsonResponse
    {
        $mutations = $request->input('mutations', []);

        if (! is_array($mutations) || empty($mutations)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada antrean sinkronisasi kegiatan.',
                'processed_count' => 0,
            ]);
        }

        $processedCount = 0;
        $idMappings = [];

        DB::beginTransaction();

        try {
            foreach ($mutations as $item) {
                $action = $item['action'] ?? null;
                $cropId = $item['crop_id'] ?? null;
                $data = $item['data'] ?? [];
                $localId = $item['local_id'] ?? null;

                if ($action === 'create' || $action === 'create_activity') {
                    $crop = Crop::find($cropId);
                    if (! $crop) {
                        continue;
                    }

                    $uploadedPhotos = [];
                    if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                        $uploadedPhotos = $this->saveBase64Photos(array_slice($data['photos_base64'], 0, 5));
                    }

                    $keterangan = $data['keterangan'] ?? $data['catatan'] ?? null;

                    $targetHst = isset($data['target_hst']) ? (int) $data['target_hst'] : $crop->current_hst;
                    if (isset($data['tanggal_kegiatan']) && ! isset($data['target_hst'])) {
                        $plantDate = Carbon::parse($crop->tanggal_tanam)->startOfDay();
                        $actDate = Carbon::parse($data['tanggal_kegiatan'])->startOfDay();
                        $targetHst = max(0, (int) $plantDate->diffInDays($actDate, false));
                    }

                    $activity = $crop->activities()->create([
                        'nama_kegiatan' => $data['nama_kegiatan'] ?? 'Kegiatan Offline',
                        'aplikasi_obat' => $data['aplikasi_obat'] ?? null,
                        'sasaran' => $data['sasaran'] ?? null,
                        'target_hst' => $targetHst,
                        'status' => 'Belum',
                        'keterangan' => $keterangan,
                        'catatan' => $keterangan,
                        'foto_kegiatan' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
                    ]);

                    if ($localId) {
                        $idMappings[$localId] = $activity->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update' || $action === 'update_activity') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $activity = CropActivity::find($id);
                        if ($activity) {
                            $updateData = [];
                            if (isset($data['nama_kegiatan'])) {
                                $updateData['nama_kegiatan'] = $data['nama_kegiatan'];
                            }
                            if (isset($data['target_hst'])) {
                                $updateData['target_hst'] = (int) $data['target_hst'];
                            }
                            if (isset($data['aplikasi_obat'])) {
                                $updateData['aplikasi_obat'] = $data['aplikasi_obat'];
                            }
                            if (isset($data['sasaran'])) {
                                $updateData['sasaran'] = $data['sasaran'];
                            }
                            if (isset($data['keterangan']) || isset($data['catatan'])) {
                                $val = $data['keterangan'] ?? $data['catatan'];
                                $updateData['keterangan'] = $val;
                                $updateData['catatan'] = $val;
                            }

                            // If new photos were attached while offline
                            if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                                $newUploaded = $this->saveBase64Photos($data['photos_base64']);
                                $existing = $activity->foto_kegiatan ?? [];
                                if (is_string($existing)) {
                                    $existing = json_decode($existing, true) ?: [];
                                }
                                $updateData['foto_kegiatan'] = array_merge($existing, $newUploaded);
                            }

                            $activity->update($updateData);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete' || $action === 'delete_activity') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $activity = CropActivity::find($id);
                        if ($activity) {
                            // Destroy physical photos
                            $photos = $activity->foto_kegiatan ?? [];
                            if (is_string($photos)) {
                                $photos = json_decode($photos, true) ?: [];
                            }
                            if (is_array($photos)) {
                                foreach ($photos as $p) {
                                    $publicId = is_array($p) ? ($p['public_id'] ?? null) : null;
                                    $url = is_array($p) ? ($p['url'] ?? null) : $p;
                                    $this->cloudinaryService->destroy($publicId, $url);
                                }
                            }
                            $activity->delete();
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_photo') {
                    $activityId = $data['activity_id'] ?? null;
                    $targetUrl = $data['target'] ?? null;
                    if ($activityId && $targetUrl) {
                        $activity = CropActivity::find($activityId);
                        if ($activity) {
                            $photos = $activity->foto_kegiatan ?? [];
                            if (is_string($photos)) {
                                $photos = json_decode($photos, true) ?: [];
                            }
                            $updated = [];
                            foreach ($photos as $p) {
                                $url = is_array($p) ? ($p['url'] ?? $p['secure_url'] ?? null) : $p;
                                if ($url === $targetUrl) {
                                    $publicId = is_array($p) ? ($p['public_id'] ?? null) : null;
                                    $this->cloudinaryService->destroy($publicId, $url);
                                } else {
                                    $updated[] = $p;
                                }
                            }
                            $activity->foto_kegiatan = ! empty($updated) ? $updated : null;
                            $activity->save();
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'record_harvest') {
                    $crop = Crop::find($cropId);
                    if ($crop && ! empty($data['tanggal_panen'])) {
                        $crop->harvests()->create([
                            'panen_ke' => $crop->next_harvest_number,
                            'tanggal_panen' => $data['tanggal_panen'],
                            'total_panen' => $data['total_panen'] ?? null,
                            'harga_panen' => $data['harga_panen'] ?? null,
                            'total_harga' => $data['total_harga'] ?? null,
                            'catatan' => $data['catatan'] ?? null,
                        ]);
                        $processedCount++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sinkronisasi berhasil diproses.',
                'processed_count' => $processedCount,
                'id_mappings' => $idMappings,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Kalender HST offline sync error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses sinkronisasi kegiatan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Decode base64 photos, compress them, and upload to Cloudinary/local storage.
     *
     * @param  array<int, string>  $base64List
     * @return array<int, array{url: string, public_id: string|null, storage_type: string}>
     */
    protected function saveBase64Photos(array $base64List): array
    {
        $uploaded = [];

        foreach ($base64List as $base64) {
            if (! is_string($base64) || empty($base64)) {
                continue;
            }

            $extension = 'jpg';
            $data = $base64;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $ext = strtolower($matches[1]);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $extension = $ext === 'jpeg' ? 'jpg' : $ext;
                }
                $data = substr($base64, strpos($base64, ',') + 1);
            }

            $binary = base64_decode($data);
            if ($binary === false || strlen($binary) === 0) {
                continue;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'agri_hst_').'.'.$extension;
            file_put_contents($tempFile, $binary);

            try {
                $compressed = $this->compressionService->compress($tempFile);
                $res = $this->cloudinaryService->upload($compressed['path']);

                $uploaded[] = [
                    'url' => $res['url'],
                    'public_id' => $res['public_id'],
                    'storage_type' => $res['storage_type'],
                ];

                if (file_exists($compressed['path'])) {
                    @unlink($compressed['path']);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kompres/upload foto base64 offline: '.$e->getMessage());
            } finally {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }

        return $uploaded;
    }
}
