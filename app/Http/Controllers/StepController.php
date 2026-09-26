<?php

namespace App\Http\Controllers;

use App\Models\LandPreparationStep;
use App\Services\CloudinaryService;
use App\Services\ImageCompressionService;
use Database\Seeders\LandPreparationStepSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    public function storePengolahanTanah(Request $request): RedirectResponse
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

        LandPreparationStep::create([
            'nomor' => $rawNomor,
            'urutan' => $urutan,
            'judul' => $validated['judul'],
            'waktu' => $validated['waktu'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'tips' => $validated['tips'] ?? null,
            'spesifikasi' => null,
            'foto' => ! empty($uploadedPhotos) ? $uploadedPhotos : null,
        ]);

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$rawNomor} ('{$validated['judul']}') berhasil ditambahkan.");
    }

    public function updatePengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse
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

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$rawNomor} ('{$step->judul}') berhasil diperbarui.");
    }

    public function destroyPengolahanTanah(LandPreparationStep $step): RedirectResponse
    {
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

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', "Tahapan nomor {$nomor} ('{$judul}') berhasil dihapus.");
    }

    public function uploadPhotoPengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse
    {
        $request->validate([
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request);

        if (empty($uploadedPhotos)) {
            return redirect()->route('steps.pengolahan-tanah')
                ->with('error', 'Tidak ada foto yang dipilih atau dijepret.');
        }

        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $step->update([
            'foto' => array_merge($existingPhotos, $uploadedPhotos),
        ]);

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', count($uploadedPhotos).' foto berhasil ditambahkan ke langkah nomor '.$step->nomor.'.');
    }

    public function destroyPhotoPengolahanTanah(Request $request, LandPreparationStep $step): RedirectResponse
    {
        $targetUrl = $request->input('photo_url');
        if (! $targetUrl) {
            return redirect()->route('steps.pengolahan-tanah');
        }

        $existingPhotos = is_array($step->foto) ? $step->foto : [];
        $remainingPhotos = [];

        foreach ($existingPhotos as $photoItem) {
            $url = is_array($photoItem) ? ($photoItem['url'] ?? '') : (string) $photoItem;
            $publicId = is_array($photoItem) ? ($photoItem['public_id'] ?? '') : '';

            $isMatch = ($url === $targetUrl || str_contains($url, $targetUrl) || ($publicId && $publicId === $targetUrl) || ($publicId && str_contains($targetUrl, $publicId)));

            if ($isMatch) {
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

        $this->cloudinaryService->deleteImage($targetUrl);

        $step->update([
            'foto' => ! empty($remainingPhotos) ? $remainingPhotos : null,
        ]);

        return redirect()->route('steps.pengolahan-tanah')
            ->with('success', 'Foto berhasil dihapus dari sistem dan penyimpanan.');
    }

    /**
     * Helper untuk memproses file upload baik dari galeri maupun jepretan kamera langsung.
     *
     * @return array<int, array{url: string, public_id: string, storage_type: string}>
     */
    protected function handleUploadedFiles(Request $request): array
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
                $res = $this->cloudinaryService->upload($compressed['path'], 'pertanian/pengolahan_tanah');

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
        return view('steps.penanaman-bibit');
    }
}
