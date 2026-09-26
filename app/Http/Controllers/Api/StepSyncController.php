<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandPreparationStep;
use App\Models\PlantingSeed;
use App\Models\PlantingStep;
use App\Services\CloudinaryService;
use App\Services\ImageCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StepSyncController extends Controller
{
    public function __construct(
        protected ImageCompressionService $compressionService,
        protected CloudinaryService $cloudinaryService
    ) {}

    /**
     * Get all Land Preparation Steps for offline sync initial load.
     */
    public function getPengolahanTanah(): JsonResponse
    {
        $steps = LandPreparationStep::orderBy('urutan')
            ->orderBy('nomor')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'steps' => $steps,
        ]);
    }

    /**
     * Synchronize offline mutations for Land Preparation Steps.
     */
    public function syncPengolahanTanah(Request $request): JsonResponse
    {
        $mutations = $request->input('mutations', []);

        if (! is_array($mutations) || empty($mutations)) {
            $steps = LandPreparationStep::orderBy('urutan')->orderBy('nomor')->orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Tidak ada antrean sinkronisasi pengolahan tanah.',
                'processed_count' => 0,
                'steps' => $steps,
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

                if ($action === 'create' || $action === 'create_step') {
                    $rawNomor = trim((string) ($data['nomor'] ?? '1'));
                    $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
                    $urutan = $numericPart > 0 ? $numericPart : ((int) (LandPreparationStep::max('urutan') ?? 0) + 1);

                    $uploadedPhotos = [];
                    if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                        $uploadedPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/pengolahan_tanah');
                    }

                    $step = LandPreparationStep::create([
                        'nomor' => $rawNomor,
                        'urutan' => $urutan,
                        'judul' => $data['judul'] ?? 'Tahapan Baru',
                        'waktu' => $data['waktu'] ?? null,
                        'deskripsi' => $data['deskripsi'] ?? '-',
                        'tips' => $data['tips'] ?? null,
                        'spesifikasi' => null,
                        'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
                    ]);

                    if ($localId) {
                        $idMappings[$localId] = $step->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update' || $action === 'update_step') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $step = LandPreparationStep::find($id);
                        if ($step) {
                            $rawNomor = isset($data['nomor']) ? trim((string) $data['nomor']) : $step->nomor;
                            $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
                            $urutan = $numericPart > 0 ? $numericPart : $step->urutan;

                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $deletedTargets = $data['deleted_photos'] ?? [];
                            if (! is_array($deletedTargets)) {
                                $deletedTargets = [];
                            }

                            $remainingPhotos = [];
                            foreach ($existingPhotos as $photoItem) {
                                $photoUrl = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['public_id'] ?? '') : (string) $photoItem;
                                $photoId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                                $isDeleted = false;
                                foreach ($deletedTargets as $target) {
                                    if ($target && ($target === $photoUrl || $target === $photoId || str_contains($photoUrl, $target) || ($photoId && str_contains($target, $photoId)))) {
                                        $isDeleted = true;
                                        break;
                                    }
                                }

                                if ($isDeleted) {
                                    if ($photoUrl) {
                                        $this->cloudinaryService->deleteImage($photoUrl);
                                    }
                                    if ($photoId) {
                                        $this->cloudinaryService->deleteImage($photoId);
                                    }
                                } else {
                                    $remainingPhotos[] = $photoItem;
                                }
                            }

                            $newUploadedPhotos = [];
                            if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                                $newUploadedPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/pengolahan_tanah');
                            }

                            $allPhotos = array_merge($remainingPhotos, $newUploadedPhotos);

                            $updatePayload = [
                                'nomor' => $rawNomor,
                                'urutan' => $urutan,
                                'judul' => $data['judul'] ?? $step->judul,
                                'waktu' => array_key_exists('waktu', $data) ? $data['waktu'] : $step->waktu,
                                'deskripsi' => $data['deskripsi'] ?? $step->deskripsi,
                                'tips' => array_key_exists('tips', $data) ? $data['tips'] : $step->tips,
                                'foto' => ! empty($allPhotos) ? $allPhotos : null,
                            ];

                            $step->update($updatePayload);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete' || $action === 'delete_step') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $step = LandPreparationStep::find($id);
                        if ($step) {
                            if (is_array($step->foto)) {
                                foreach ($step->foto as $photoItem) {
                                    $url = is_array($photoItem) ? ($photoItem['url'] ?? null) : (string) $photoItem;
                                    $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? null) : null;
                                    if ($url) {
                                        $this->cloudinaryService->deleteImage($url);
                                    }
                                    if ($publicId) {
                                        $this->cloudinaryService->deleteImage($publicId);
                                    }
                                }
                            }
                            $step->delete();
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'upload_photos') {
                    $id = $data['step_id'] ?? $data['id'] ?? null;
                    if ($id) {
                        $step = LandPreparationStep::find($id);
                        if ($step && ! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                            $newPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/pengolahan_tanah');
                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $step->update([
                                'foto' => array_merge($existingPhotos, $newPhotos),
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_photo') {
                    $id = $data['step_id'] ?? $data['id'] ?? null;
                    $targetUrl = $data['photo_url'] ?? null;
                    if ($id && $targetUrl) {
                        $step = LandPreparationStep::find($id);
                        if ($step) {
                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $remainingPhotos = [];
                            foreach ($existingPhotos as $photoItem) {
                                $url = is_array($photoItem) ? ($photoItem['url'] ?? '') : (string) $photoItem;
                                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                                if ($url === $targetUrl || str_contains($url, $targetUrl) || ($publicId && $publicId === $targetUrl)) {
                                    if ($url) {
                                        $this->cloudinaryService->deleteImage($url);
                                    }
                                    if ($publicId) {
                                        $this->cloudinaryService->deleteImage($publicId);
                                    }
                                } else {
                                    $remainingPhotos[] = $photoItem;
                                }
                            }
                            $step->update([
                                'foto' => ! empty($remainingPhotos) ? $remainingPhotos : null,
                            ]);
                            $processedCount++;
                        }
                    }
                }
            }

            DB::commit();

            $freshSteps = LandPreparationStep::orderBy('urutan')
                ->orderBy('nomor')
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyinkronkan {$processedCount} perubahan langkah pengolahan tanah.",
                'processed_count' => $processedCount,
                'id_mappings' => $idMappings,
                'steps' => $freshSteps,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Pengolahan tanah sync error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyinkronkan pengolahan tanah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all Planting Seeds with their Steps for offline sync.
     */
    public function getPenanamanBibit(): JsonResponse
    {
        $seeds = PlantingSeed::with(['steps' => function ($q) {
            $q->orderBy('urutan')->orderBy('nomor')->orderBy('id');
        }])->orderBy('urutan')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'seeds' => $seeds,
        ]);
    }

    /**
     * Synchronize offline mutations for Planting Seeds and Steps.
     */
    public function syncPenanamanBibit(Request $request): JsonResponse
    {
        $mutations = $request->input('mutations', []);

        if (! is_array($mutations) || empty($mutations)) {
            $seeds = PlantingSeed::with(['steps' => function ($q) {
                $q->orderBy('urutan')->orderBy('nomor')->orderBy('id');
            }])->orderBy('urutan')->orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Tidak ada antrean sinkronisasi penanaman bibit.',
                'processed_count' => 0,
                'seeds' => $seeds,
            ]);
        }

        $processedCount = 0;
        $seedIdMappings = [];
        $stepIdMappings = [];

        DB::beginTransaction();

        try {
            foreach ($mutations as $item) {
                $action = $item['action'] ?? null;
                $data = $item['data'] ?? [];
                $localId = $item['local_id'] ?? null;

                // ── Operasi Bibit (Seed) ──
                if ($action === 'create_seed') {
                    $maxUrutan = (int) (PlantingSeed::max('urutan') ?? 0);
                    $seed = PlantingSeed::create([
                        'nama_bibit' => trim($data['nama_bibit'] ?? 'Bibit Tanpa Nama'),
                        'varietas' => ! empty($data['varietas']) ? trim($data['varietas']) : null,
                        'deskripsi' => ! empty($data['deskripsi']) ? trim($data['deskripsi']) : null,
                        'urutan' => $maxUrutan + 1,
                    ]);

                    if ($localId) {
                        $seedIdMappings[$localId] = $seed->id;
                    }
                    $processedCount++;
                } elseif ($action === 'update_seed') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $actualId = $seedIdMappings[$id] ?? $id;
                        $seed = PlantingSeed::find($actualId);
                        if ($seed) {
                            $seed->update([
                                'nama_bibit' => trim($data['nama_bibit'] ?? $seed->nama_bibit),
                                'varietas' => array_key_exists('varietas', $data) ? $data['varietas'] : $seed->varietas,
                                'deskripsi' => array_key_exists('deskripsi', $data) ? $data['deskripsi'] : $seed->deskripsi,
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_seed') {
                    $id = $data['id'] ?? null;
                    if ($id) {
                        $actualId = $seedIdMappings[$id] ?? $id;
                        $seed = PlantingSeed::find($actualId);
                        if ($seed) {
                            $seed->delete();
                            $processedCount++;
                        }
                    }
                }

                // ── Operasi Langkah (Step) ──
                elseif ($action === 'create_step') {
                    $rawSeedId = $data['planting_seed_id'] ?? $data['seed_id'] ?? null;
                    $actualSeedId = $seedIdMappings[$rawSeedId] ?? $rawSeedId;

                    $seed = PlantingSeed::find($actualSeedId);
                    if ($seed) {
                        $rawNomor = trim((string) ($data['nomor'] ?? '1'));
                        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
                        $maxUrutan = (int) ($seed->steps()->max('urutan') ?? 0);
                        $urutan = $numericPart > 0 ? $numericPart : ($maxUrutan + 1);

                        $uploadedPhotos = [];
                        if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                            $uploadedPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/penanaman_bibit');
                        }

                        $step = $seed->steps()->create([
                            'nomor' => $rawNomor,
                            'urutan' => $urutan,
                            'judul' => $data['judul'] ?? 'Langkah Baru',
                            'waktu' => $data['waktu'] ?? null,
                            'deskripsi' => $data['deskripsi'] ?? '-',
                            'tips' => $data['tips'] ?? null,
                            'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
                        ]);

                        if ($localId) {
                            $stepIdMappings[$localId] = $step->id;
                        }
                        $processedCount++;
                    }
                } elseif ($action === 'update_step') {
                    $id = $data['id'] ?? null;
                    $actualStepId = $stepIdMappings[$id] ?? $id;
                    if ($actualStepId) {
                        $step = PlantingStep::find($actualStepId);
                        if ($step) {
                            $rawNomor = isset($data['nomor']) ? trim((string) $data['nomor']) : $step->nomor;
                            $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
                            $urutan = $numericPart > 0 ? $numericPart : $step->urutan;

                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $deletedTargets = $data['deleted_photos'] ?? [];
                            if (! is_array($deletedTargets)) {
                                $deletedTargets = [];
                            }

                            $remainingPhotos = [];
                            foreach ($existingPhotos as $photoItem) {
                                $photoUrl = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['public_id'] ?? '') : (string) $photoItem;
                                $photoId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                                $isDeleted = false;
                                foreach ($deletedTargets as $target) {
                                    if ($target && ($target === $photoUrl || $target === $photoId || str_contains($photoUrl, $target) || ($photoId && str_contains($target, $photoId)))) {
                                        $isDeleted = true;
                                        break;
                                    }
                                }

                                if ($isDeleted) {
                                    if ($photoUrl) {
                                        $this->cloudinaryService->deleteImage($photoUrl);
                                    }
                                    if ($photoId) {
                                        $this->cloudinaryService->deleteImage($photoId);
                                    }
                                } else {
                                    $remainingPhotos[] = $photoItem;
                                }
                            }

                            $newUploadedPhotos = [];
                            if (! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                                $newUploadedPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/penanaman_bibit');
                            }

                            $allPhotos = array_merge($remainingPhotos, $newUploadedPhotos);

                            $step->update([
                                'nomor' => $rawNomor,
                                'urutan' => $urutan,
                                'judul' => $data['judul'] ?? $step->judul,
                                'waktu' => array_key_exists('waktu', $data) ? $data['waktu'] : $step->waktu,
                                'deskripsi' => $data['deskripsi'] ?? $step->deskripsi,
                                'tips' => array_key_exists('tips', $data) ? $data['tips'] : $step->tips,
                                'foto' => ! empty($allPhotos) ? $allPhotos : null,
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_step') {
                    $id = $data['id'] ?? null;
                    $actualStepId = $stepIdMappings[$id] ?? $id;
                    if ($actualStepId) {
                        $step = PlantingStep::find($actualStepId);
                        if ($step) {
                            $step->delete();
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'upload_step_photos') {
                    $id = $data['step_id'] ?? $data['id'] ?? null;
                    $actualStepId = $stepIdMappings[$id] ?? $id;
                    if ($actualStepId) {
                        $step = PlantingStep::find($actualStepId);
                        if ($step && ! empty($data['photos_base64']) && is_array($data['photos_base64'])) {
                            $newPhotos = $this->saveBase64Photos($data['photos_base64'], 'pertanian/penanaman_bibit');
                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $step->update([
                                'foto' => array_merge($existingPhotos, $newPhotos),
                            ]);
                            $processedCount++;
                        }
                    }
                } elseif ($action === 'delete_step_photo') {
                    $id = $data['step_id'] ?? $data['id'] ?? null;
                    $actualStepId = $stepIdMappings[$id] ?? $id;
                    $targetUrl = $data['photo_url'] ?? null;
                    if ($actualStepId && $targetUrl) {
                        $step = PlantingStep::find($actualStepId);
                        if ($step) {
                            $existingPhotos = is_array($step->foto) ? $step->foto : [];
                            $remainingPhotos = [];
                            foreach ($existingPhotos as $photoItem) {
                                $url = is_array($photoItem) ? ($photoItem['url'] ?? '') : (string) $photoItem;
                                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                                if ($url === $targetUrl || str_contains($url, $targetUrl) || ($publicId && $publicId === $targetUrl)) {
                                    if ($url) {
                                        $this->cloudinaryService->deleteImage($url);
                                    }
                                    if ($publicId) {
                                        $this->cloudinaryService->deleteImage($publicId);
                                    }
                                } else {
                                    $remainingPhotos[] = $photoItem;
                                }
                            }
                            $step->update([
                                'foto' => ! empty($remainingPhotos) ? $remainingPhotos : null,
                            ]);
                            $processedCount++;
                        }
                    }
                }
            }

            DB::commit();

            $freshSeeds = PlantingSeed::with(['steps' => function ($q) {
                $q->orderBy('urutan')->orderBy('nomor')->orderBy('id');
            }])->orderBy('urutan')->orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyinkronkan {$processedCount} perubahan tahapan penanaman bibit.",
                'processed_count' => $processedCount,
                'seed_id_mappings' => $seedIdMappings,
                'step_id_mappings' => $stepIdMappings,
                'seeds' => $freshSeeds,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Penanaman bibit sync error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyinkronkan penanaman bibit: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Decode base64 photos, compress them, and upload to Cloudinary/local storage.
     *
     * @param  array<int, string>  $base64List
     * @return array<int, array{url: string, public_id: string|null, storage_type: string}>
     */
    protected function saveBase64Photos(array $base64List, string $folder = 'pertanian/pengolahan_tanah'): array
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

            $tempFile = tempnam(sys_get_temp_dir(), 'agri_step_').'.'.$extension;
            file_put_contents($tempFile, $binary);

            try {
                $compressed = $this->compressionService->compress($tempFile);
                $res = $this->cloudinaryService->upload($compressed['path'], $folder);

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
