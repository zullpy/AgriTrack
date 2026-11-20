<?php

namespace App\Http\Controllers;

use App\Models\FertilizerFermentationStep;
use App\Models\LandPreparationStep;
use App\Models\PlantingSeed;
use App\Models\PlantingStep;
use App\Models\StepNote;
use App\Services\CloudinaryService;
use App\Services\ImageCompressionService;
use Database\Seeders\FertilizerFermentationStepSeeder;
use Database\Seeders\LandPreparationStepSeeder;
use Database\Seeders\PlantingSeedSeeder;
use Database\Seeders\StepNoteSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class StepController extends Controller
{
    public function __construct(
        protected ImageCompressionService $compressionService,
        protected CloudinaryService $cloudinaryService
    ) {}

    public function index(): View
    {
        return view('steps.index');
    }

    public function fermentasiPupuk(): View
    {
        if (FertilizerFermentationStep::count() === 0) {
            (new FertilizerFermentationStepSeeder)->run();
        }

        $steps = FertilizerFermentationStep::orderBy('urutan')
            ->orderBy('nomor')
            ->orderBy('id')
            ->get();

        return view('steps.fermentasi-pupuk', compact('steps'));
    }

    public function storeFermentasiPupuk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $urutan = $numericPart > 0 ? $numericPart : ((int) (FertilizerFermentationStep::max('urutan') ?? 0) + 1);

        $uploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/fermentasi_pupuk');

        $createdStep = FertilizerFermentationStep::create([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'spesifikasi' => null,
            'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan fermentasi pupuk nomor {$rawNomor} ('{$validated['judul']}') berhasil ditambahkan.",
                'step' => $createdStep,
            ], 201);
        }

        return redirect()->route('steps.fermentasi-pupuk')
            ->with('success', "Tahapan fermentasi pupuk nomor {$rawNomor} ('{$validated['judul']}') berhasil ditambahkan.");
    }

    public function updateFermentasiPupuk(Request $request, FertilizerFermentationStep $step): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'deleted_photos' => 'nullable|array',
            'deleted_photos.*' => 'string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $urutan = $numericPart > 0 ? $numericPart : $step->urutan;

        // Proses foto yang dihapus
        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $deletedTargets = $request->input('deleted_photos', []);
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
                try {
                    if ($photoUrl) {
                        $this->cloudinaryService->deleteImage($photoUrl);
                    }
                    if ($photoId) {
                        $this->cloudinaryService->deleteImage($photoId);
                    }
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus foto lama fermentasi pupuk: '.$e->getMessage());
                }
            } else {
                $remainingPhotos[] = $photoItem;
            }
        }

        foreach ($deletedTargets as $target) {
            if ($target) {
                try {
                    $this->cloudinaryService->deleteImage((string) $target);
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus file target fermentasi pupuk: '.$e->getMessage());
                }
            }
        }

        $newUploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/fermentasi_pupuk');
        $allPhotos = array_merge($remainingPhotos, $newUploadedPhotos);

        $step->update([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'foto' => ! empty($allPhotos) ? array_values($allPhotos) : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan fermentasi pupuk nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.",
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.fermentasi-pupuk')
            ->with('success', "Tahapan fermentasi pupuk nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.");
    }

    public function destroyFermentasiPupuk(Request $request, FertilizerFermentationStep $step): RedirectResponse|JsonResponse
    {
        $id = $step->id;
        $nomor = $step->nomor;
        $judul = $step->judul;

        if (is_array($step->foto)) {
            foreach ($step->foto as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? null) : (string) $photoItem;
                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? null) : null;
                try {
                    if ($url) {
                        $this->cloudinaryService->deleteImage($url);
                    }
                    if ($publicId) {
                        $this->cloudinaryService->deleteImage($publicId);
                    }
                } catch (Throwable $e) {
                    Log::warning('Gagal hapus foto saat delete step fermentasi pupuk: '.$e->getMessage());
                }
            }
        }

        $step->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan fermentasi pupuk nomor {$nomor} ('{$judul}') berhasil dihapus.",
                'id' => $id,
            ]);
        }

        return redirect()->route('steps.fermentasi-pupuk')
            ->with('success', "Tahapan fermentasi pupuk nomor {$nomor} ('{$judul}') berhasil dihapus.");
    }

    public function uploadPhotoFermentasiPupuk(Request $request, FertilizerFermentationStep $step): RedirectResponse|JsonResponse
    {
        $request->validate([
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/fermentasi_pupuk');

        if (empty($uploadedPhotos)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada foto yang dipilih atau dijepret.',
                ], 422);
            }

            return redirect()->route('steps.fermentasi-pupuk')
                ->with('error', 'Tidak ada foto yang dipilih atau dijepret.');
        }

        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $step->update([
            'foto' => array_values(array_merge($existingPhotos, $uploadedPhotos)),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.',
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.fermentasi-pupuk')
            ->with('success', count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.');
    }

    public function destroyPhotoFermentasiPupuk(Request $request, FertilizerFermentationStep $step): RedirectResponse|JsonResponse
    {
        try {
            $targetUrl = trim((string) $request->input('photo_url'));
            if ($targetUrl === '') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Target foto tidak valid.'], 400);
                }

                return redirect()->route('steps.fermentasi-pupuk');
            }

            $existingPhotos = is_array($step->foto) ? $step->foto : [];
            $remainingPhotos = [];
            $photoDeleted = false;

            foreach ($existingPhotos as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['secure_url'] ?? '') : (string) $photoItem;
                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                $isMatch = ($url === $targetUrl
                    || ($url !== '' && str_contains($url, $targetUrl))
                    || ($targetUrl !== '' && str_contains($targetUrl, $url))
                    || ($publicId !== '' && ($publicId === $targetUrl || str_contains($targetUrl, $publicId))));

                if ($isMatch && ! $photoDeleted) {
                    $photoDeleted = true;
                    try {
                        if ($publicId) {
                            $this->cloudinaryService->deleteImage($publicId);
                        } elseif ($url) {
                            $this->cloudinaryService->deleteImage($url);
                        } else {
                            $this->cloudinaryService->deleteImage($targetUrl);
                        }
                    } catch (Throwable $e) {
                        Log::warning('Gagal menghapus file fisik foto fermentasi pupuk: '.$e->getMessage());
                    }
                } else {
                    $remainingPhotos[] = $photoItem;
                }
            }

            if (! $photoDeleted) {
                try {
                    $this->cloudinaryService->deleteImage($targetUrl);
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus file targetUrl fermentasi pupuk: '.$e->getMessage());
                }
            }

            $step->update([
                'foto' => ! empty($remainingPhotos) ? array_values($remainingPhotos) : null,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                $freshStep = $step->fresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil dihapus dari sistem.',
                    'remaining_count' => count($remainingPhotos),
                    'foto_urls' => $freshStep->foto_urls,
                    'step' => $freshStep,
                ]);
            }

            return redirect()->route('steps.fermentasi-pupuk')
                ->with('success', 'Foto berhasil dihapus dari sistem dan penyimpanan.');
        } catch (Throwable $e) {
            Log::error('Exception saat menghapus foto fermentasi pupuk: '.$e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus foto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->route('steps.fermentasi-pupuk')
                ->with('error', 'Gagal menghapus foto: '.$e->getMessage());
        }
    }

    public function pengolahanTanah(): View
    {
        if (LandPreparationStep::count() === 0) {
            (new LandPreparationStepSeeder)->run();
        }

        $steps = LandPreparationStep::orderBy('urutan')
            ->orderBy('nomor')
            ->orderBy('id')
            ->get();

        return view('steps.pengolahan-tanah', compact('steps'));
    }

    public function storePengolahanTanah(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $urutan = $numericPart > 0 ? $numericPart : ((int) (LandPreparationStep::max('urutan') ?? 0) + 1);

        $uploadedPhotos = $this->handleUploadedFiles($request);

        $createdStep = LandPreparationStep::create([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'spesifikasi' => null,
            'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan nomor {$rawNomor} ('{$validated['judul']}') berhasil ditambahkan.",
                'step' => $createdStep,
            ], 201);
        }

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$rawNomor} ('{$validated['judul']}') berhasil ditambahkan.");
    }

    public function updatePengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'deleted_photos' => 'nullable|array',
            'deleted_photos.*' => 'string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $urutan = $numericPart > 0 ? $numericPart : $step->urutan;

        // Proses foto yang dihapus
        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $deletedTargets = $request->input('deleted_photos', []);
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

        // Hapus juga secara langsung setiap target yang dikirim dari form
        foreach ($deletedTargets as $target) {
            if ($target) {
                $this->cloudinaryService->deleteImage((string) $target);
            }
        }

        // Upload foto baru
        $newUploadedPhotos = $this->handleUploadedFiles($request);
        $allPhotos = array_merge($remainingPhotos, $newUploadedPhotos);

        $step->update([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'foto' => ! empty($allPhotos) ? $allPhotos : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.",
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.");
    }

    public function destroyPengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse|JsonResponse
    {
        $id = $step->id;
        $nomor = $step->nomor;
        $judul = $step->judul;

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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan nomor {$nomor} ('{$judul}') berhasil dihapus.",
                'id' => $id,
            ]);
        }

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$nomor} ('{$judul}') berhasil dihapus.");
    }

    public function uploadPhotoPengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse|JsonResponse
    {
        $request->validate([
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request);

        if (empty($uploadedPhotos)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada foto yang dipilih atau dijepret.',
                ], 422);
            }

            return redirect()->route('steps.pengolahan-tanah')
                ->with('error', 'Tidak ada foto yang dipilih atau dijepret.');
        }

        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $step->update([
            'foto' => array_merge($existingPhotos, $uploadedPhotos),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.',
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.');
    }

    public function destroyPhotoPengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse|JsonResponse
    {
        try {
            $targetUrl = trim((string) $request->input('photo_url'));
            if ($targetUrl === '') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Target foto tidak valid.'], 400);
                }

                return redirect()->route('steps.pengolahan-tanah');
            }

            $existingPhotos = is_array($step->foto) ? $step->foto : [];
            $remainingPhotos = [];
            $photoDeleted = false;

            foreach ($existingPhotos as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['secure_url'] ?? '') : (string) $photoItem;
                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                $isMatch = ($url === $targetUrl
                    || ($url !== '' && str_contains($url, $targetUrl))
                    || ($targetUrl !== '' && str_contains($targetUrl, $url))
                    || ($publicId !== '' && ($publicId === $targetUrl || str_contains($targetUrl, $publicId))));

                if ($isMatch && ! $photoDeleted) {
                    $photoDeleted = true;
                    try {
                        if ($publicId) {
                            $this->cloudinaryService->deleteImage($publicId);
                        } elseif ($url) {
                            $this->cloudinaryService->deleteImage($url);
                        } else {
                            $this->cloudinaryService->deleteImage($targetUrl);
                        }
                    } catch (Throwable $e) {
                        Log::warning('Gagal menghapus file fisik foto pengolahan tanah: '.$e->getMessage());
                    }
                } else {
                    $remainingPhotos[] = $photoItem;
                }
            }

            if (! $photoDeleted) {
                try {
                    $this->cloudinaryService->deleteImage($targetUrl);
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus file targetUrl pengolahan tanah: '.$e->getMessage());
                }
            }

            $step->update([
                'foto' => ! empty($remainingPhotos) ? array_values($remainingPhotos) : null,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                $freshStep = $step->fresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil dihapus dari sistem.',
                    'remaining_count' => count($remainingPhotos),
                    'foto_urls' => $freshStep->foto_urls,
                    'step' => $freshStep,
                ]);
            }

            return redirect()->route('steps.pengolahan-tanah')
                ->with('success', 'Foto berhasil dihapus dari sistem dan penyimpanan.');
        } catch (Throwable $e) {
            Log::error('Exception saat menghapus foto pengolahan tanah: '.$e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus foto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->route('steps.pengolahan-tanah')
                ->with('error', 'Gagal menghapus foto: '.$e->getMessage());
        }
    }

    /**
     * Helper untuk memproses file upload baik dari galeri maupun jepretan kamera langsung.
     *
     * @return array<int, array{url: string, public_id: string, storage_type: string}>
     */
    protected function handleUploadedFiles(Request $request, string $folder = 'pertanian/pengolahan_tanah'): array
    {
        $files = [];

        if ($request->hasFile('foto')) {
            $galleryFiles = $request->file('foto');
            $files = array_merge($files, is_array($galleryFiles) ? $galleryFiles : [$galleryFiles]);
        }

        if ($request->hasFile('foto_kamera')) {
            $cameraFiles = $request->file('foto_kamera');
            $files = array_merge($files, is_array($cameraFiles) ? $cameraFiles : [$cameraFiles]);
        }

        $uploaded = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $compressed = $this->compressionService->compress($file);
                $res = $this->cloudinaryService->upload($compressed['path'], $folder);

                if (file_exists($compressed['path'])) {
                    @unlink($compressed['path']);
                }

                $uploaded[] = [
                    'url' => $res['url'],
                    'public_id' => $res['public_id'],
                    'storage_type' => $res['storage_type'],
                ];
            }
        }

        return $uploaded;
    }

    public function penanamanBibit(): View
    {
        if (PlantingSeed::count() === 0) {
            (new PlantingSeedSeeder)->run();
        }

        $seeds = PlantingSeed::with('steps')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('steps.penanaman-bibit', compact('seeds'));
    }

    public function storePlantingSeed(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_bibit' => 'required|string|max:255',
            'varietas' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $maxUrutan = (int) (PlantingSeed::max('urutan') ?? 0);

        $seed = PlantingSeed::create([
            'nama_bibit' => trim($validated['nama_bibit']),
            'varietas' => ! empty($validated['varietas']) ? trim($validated['varietas']) : null,
            'deskripsi' => ! empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
            'urutan' => $maxUrutan + 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Jenis bibit '{$seed->nama_bibit}' berhasil ditambahkan.",
                'seed' => $seed,
            ], 201);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('success', "Jenis bibit '{$seed->nama_bibit}' berhasil ditambahkan.");
    }

    public function updatePlantingSeed(Request $request, PlantingSeed $seed): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_bibit' => 'required|string|max:255',
            'varietas' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $seed->update([
            'nama_bibit' => trim($validated['nama_bibit']),
            'varietas' => ! empty($validated['varietas']) ? trim($validated['varietas']) : null,
            'deskripsi' => ! empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Informasi bibit '{$seed->nama_bibit}' berhasil diperbarui.",
                'seed' => $seed->fresh(),
            ]);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('success', "Informasi bibit '{$seed->nama_bibit}' berhasil diperbarui.");
    }

    public function destroyPlantingSeed(Request $request, PlantingSeed $seed): RedirectResponse|JsonResponse
    {
        $id = $seed->id;
        $nama = $seed->nama_bibit;
        $seed->delete(); // Otomatis menghapus steps & foto fisik via model deleting event

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Bibit '{$nama}' beserta seluruh tahapan dan foto dokumentasinya berhasil dihapus.",
                'id' => $id,
            ]);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('success', "Bibit '{$nama}' beserta seluruh tahapan dan foto dokumentasinya berhasil dihapus.");
    }

    public function storePlantingStep(Request $request, PlantingSeed $seed): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $maxUrutan = (int) ($seed->steps()->max('urutan') ?? 0);
        $urutan = $numericPart > 0 ? $numericPart : ($maxUrutan + 1);

        $uploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/penanaman_bibit');

        $createdStep = $seed->steps()->create([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Langkah nomor {$rawNomor} ('{$validated['judul']}') untuk {$seed->nama_bibit} berhasil ditambahkan.",
                'step' => $createdStep,
            ], 201);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('active_seed_id', $seed->id)
            ->with('success', "Langkah nomor {$rawNomor} ('{$validated['judul']}') untuk {$seed->nama_bibit} berhasil ditambahkan.");
    }

    public function updatePlantingStep(Request $request, PlantingStep $step): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'tips' => 'nullable|string',
            'deleted_photos' => 'nullable|array',
            'deleted_photos.*' => 'string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $rawNomor = trim($validated['nomor']);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $rawNomor);
        $urutan = $numericPart > 0 ? $numericPart : $step->urutan;

        // Proses foto yang dihapus
        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $deletedTargets = $request->input('deleted_photos', []);
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

        foreach ($deletedTargets as $target) {
            if ($target) {
                $this->cloudinaryService->deleteImage((string) $target);
            }
        }

        // Upload foto baru
        $newUploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/penanaman_bibit');
        $allPhotos = array_merge($remainingPhotos, $newUploadedPhotos);

        $step->update([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'foto' => ! empty($allPhotos) ? $allPhotos : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.",
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('active_seed_id', $step->planting_seed_id)
            ->with('success', "Tahapan nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.");
    }

    public function destroyPlantingStep(Request $request, PlantingStep $step): RedirectResponse|JsonResponse
    {
        $id = $step->id;
        $nomor = $step->nomor;
        $judul = $step->judul;
        $seedId = $step->planting_seed_id;
        $step->delete(); // Otomatis menghapus file foto fisik melalui model deleting event

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan nomor {$nomor} ('{$judul}') berhasil dihapus.",
                'id' => $id,
                'seed_id' => $seedId,
            ]);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('active_seed_id', $seedId)
            ->with('success', "Tahapan nomor {$nomor} ('{$judul}') berhasil dihapus.");
    }

    public function uploadPhotoPlantingStep(Request $request, PlantingStep $step): RedirectResponse|JsonResponse
    {
        $request->validate([
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request, 'pertanian/penanaman_bibit');

        if (empty($uploadedPhotos)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada foto yang dipilih atau dijepret.',
                ], 422);
            }

            return redirect()->route('steps.penanaman-bibit')
                ->with('error', 'Tidak ada foto yang dipilih atau dijepret.');
        }

        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $step->update([
            'foto' => array_merge($existingPhotos, $uploadedPhotos),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.',
                'step' => $step->fresh(),
            ]);
        }

        return redirect()->route('steps.penanaman-bibit')
            ->with('active_seed_id', $step->planting_seed_id)
            ->with('success', count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.');
    }

    public function destroyPhotoPlantingStep(Request $request, PlantingStep $step): RedirectResponse|JsonResponse
    {
        try {
            $targetUrl = trim((string) $request->input('photo_url'));
            if ($targetUrl === '') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Target foto tidak valid.'], 400);
                }

                return redirect()->route('steps.penanaman-bibit')
                    ->with('active_seed_id', $step->planting_seed_id);
            }

            $existingPhotos = is_array($step->foto) ? $step->foto : [];
            $remainingPhotos = [];
            $photoDeleted = false;

            foreach ($existingPhotos as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['secure_url'] ?? '') : (string) $photoItem;
                $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

                $isMatch = ($url === $targetUrl
                    || ($url !== '' && str_contains($url, $targetUrl))
                    || ($targetUrl !== '' && str_contains($targetUrl, $url))
                    || ($publicId !== '' && ($publicId === $targetUrl || str_contains($targetUrl, $publicId))));

                if ($isMatch && ! $photoDeleted) {
                    $photoDeleted = true;
                    try {
                        if ($publicId) {
                            $this->cloudinaryService->deleteImage($publicId);
                        } elseif ($url) {
                            $this->cloudinaryService->deleteImage($url);
                        } else {
                            $this->cloudinaryService->deleteImage($targetUrl);
                        }
                    } catch (Throwable $e) {
                        Log::warning('Gagal menghapus file fisik foto penanaman bibit: '.$e->getMessage());
                    }
                } else {
                    $remainingPhotos[] = $photoItem;
                }
            }

            if (! $photoDeleted) {
                try {
                    $this->cloudinaryService->deleteImage($targetUrl);
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus file targetUrl bibit: '.$e->getMessage());
                }
            }

            $step->update([
                'foto' => ! empty($remainingPhotos) ? array_values($remainingPhotos) : null,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                $freshStep = $step->fresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil dihapus dari sistem.',
                    'remaining_count' => count($remainingPhotos),
                    'foto_urls' => $freshStep->foto_urls,
                    'step' => $freshStep,
                ]);
            }

            return redirect()->route('steps.penanaman-bibit')
                ->with('active_seed_id', $step->planting_seed_id)
                ->with('success', 'Foto berhasil dihapus dari sistem dan penyimpanan.');
        } catch (Throwable $e) {
            Log::error('Exception saat menghapus foto penanaman bibit: '.$e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus foto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->route('steps.penanaman-bibit')
                ->with('active_seed_id', $step->planting_seed_id)
                ->with('error', 'Gagal menghapus foto: '.$e->getMessage());
        }
    }

    public function catatan(): View
    {
        if (StepNote::count() === 0) {
            (new StepNoteSeeder)->run();
        }

        $notes = StepNote::latest()->get();

        return view('steps.catatan', compact('notes'));
    }

    public function storeCatatan(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'catatan' => 'required|string',
        ]);

        $note = StepNote::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Informasi penting '{$note->judul}' berhasil ditambahkan.",
                'note' => $note,
            ], 201);
        }

        return redirect()->route('steps.catatan')
            ->with('success', "Informasi penting '{$note->judul}' berhasil ditambahkan.");
    }

    public function updateCatatan(Request $request, StepNote $note): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'catatan' => 'required|string',
        ]);

        $note->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Informasi penting '{$note->judul}' berhasil diperbarui.",
                'note' => $note->fresh(),
            ]);
        }

        return redirect()->route('steps.catatan')
            ->with('success', "Informasi penting '{$note->judul}' berhasil diperbarui.");
    }

    public function destroyCatatan(Request $request, StepNote $note): RedirectResponse|JsonResponse
    {
        $id = $note->id;
        $judul = $note->judul;
        $note->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Informasi penting '{$judul}' berhasil dihapus.",
                'id' => $id,
            ]);
        }

        return redirect()->route('steps.catatan')
            ->with('success', "Informasi penting '{$judul}' berhasil dihapus.");
    }
}
