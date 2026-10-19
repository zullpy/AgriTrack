<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicinePurchase;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    public function index(Request $request)
    {
        $query = Medicine::with('purchases');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('formulasi', 'like', "%{$search}%")
                    ->orWhere('sasaran_obat', 'like', "%{$search}%")
                    ->orWhere('tanaman_sasaran', 'like', "%{$search}%")
                    ->orWhere('unsur_bahan', 'like', "%{$search}%")
                    ->orWhere('toko_obat', 'like', "%{$search}%")
                    ->orWhereHas('purchases', function ($pq) use ($search) {
                        $pq->where('toko_obat', 'like', "%{$search}%");
                    });
            });
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis', $jenis);
        }

        if ($caraKerja = $request->input('cara_kerja')) {
            $query->where('cara_kerja', $caraKerja);
        }

        if ($formulasi = $request->input('formulasi')) {
            $query->where('formulasi', $formulasi);
        }

        if ($fase = $request->input('fase')) {
            $query->where('fase', $fase);
        }

        $medicines = $query->latest()->paginate(10)->withQueryString();
        MedicineCategory::ensureTableExists();
        $categories = MedicineCategory::orderBy('urutan')->orderBy('nama')->get();
        $stokMenipisCount = Medicine::where('stok', '>', 0)->where('stok', '<=', 3)->count();

        return view('data-obat.index', compact('medicines', 'categories', 'stokMenipisCount'));
    }

    public function create()
    {
        MedicineCategory::ensureTableExists();
        $existingMedicines = Medicine::with('purchases')->get();
        $categories = MedicineCategory::orderBy('urutan')->orderBy('nama')->get();

        return view('data-obat.form', compact('existingMedicines', 'categories'));
    }

    public function store(Request $request)
    {
        // Bersihkan harga utama jika ada
        if ($request->has('harga')) {
            $raw = $request->input('harga');
            if ($raw === null || $raw === '') {
                $request->merge(['harga' => null]);
            } else {
                $cleaned = preg_replace('/\D/', '', (string) $raw);
                $request->merge(['harga' => $cleaned !== '' ? (int) $cleaned : null]);
            }
        }

        // Bersihkan stok jika ada
        if ($request->has('stok')) {
            $rawStok = $request->input('stok');
            if ($rawStok === null || $rawStok === '') {
                $request->merge(['stok' => null]);
            } else {
                $cleanedStok = preg_replace('/\D/', '', (string) $rawStok);
                $request->merge(['stok' => $cleanedStok !== '' ? (int) $cleanedStok : null]);
            }
        }

        // Bersihkan harga & jumlah pada setiap array purchases jika ada
        if ($request->has('purchases') && is_array($request->input('purchases'))) {
            $purchasesInput = $request->input('purchases');
            foreach ($purchasesInput as $idx => $p) {
                if (array_key_exists('harga', $p)) {
                    $rawP = $p['harga'];
                    if ($rawP === null || trim((string) $rawP) === '') {
                        $purchasesInput[$idx]['harga'] = null;
                    } else {
                        $cleaned = preg_replace('/\D/', '', (string) $rawP);
                        $purchasesInput[$idx]['harga'] = $cleaned !== '' ? (int) $cleaned : null;
                    }
                }
                if (array_key_exists('jumlah', $p)) {
                    $rawJ = $p['jumlah'];
                    if ($rawJ === null || trim((string) $rawJ) === '') {
                        $purchasesInput[$idx]['jumlah'] = null;
                    } else {
                        $cleanedJ = preg_replace('/\D/', '', (string) $rawJ);
                        $purchasesInput[$idx]['jumlah'] = $cleanedJ !== '' ? (int) $cleanedJ : null;
                    }
                }
            }
            $request->merge(['purchases' => $purchasesInput]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'cara_kerja' => 'nullable|string|in:Sistemik,Kontak,Sistemik + Kontak',
            'formulasi' => 'nullable|string|max:50',
            'sasaran_obat' => 'nullable|string|max:255',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'harga' => 'nullable|integer|min:0',
            'stok' => 'nullable|integer|min:0',
            'kemasan_jumlah' => 'nullable|numeric|min:0',
            'kemasan_satuan' => 'nullable|string|max:50',
            'unsur_bahan' => 'nullable|string|max:255',
            'fase' => 'nullable|string|in:Vegetatif,Generatif,Semua Fase',
            'foto_nota' => 'nullable',
            'foto_nota.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'catatan_keamanan' => 'nullable|string|max:1000',
            'keterangan' => 'nullable|string|max:2000',
            'tanggal_beli' => 'nullable|date',
            'toko_obat' => 'nullable|string|max:255',
            'purchases' => 'nullable|array',
            'purchases.*.toko_obat' => 'nullable|string|max:255',
            'purchases.*.harga' => 'nullable|integer|min:0',
            'purchases.*.jumlah' => 'nullable|integer|min:0',
            'purchases.*.tanggal_beli' => 'nullable|date',
            'purchases.*.catatan' => 'nullable|string|max:1000',
            'purchases.*.foto_nota' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotoPaths = $this->uploadPhotos($request->file('foto_nota'));
        $primaryFotoPath = $uploadedPhotoPaths[0] ?? null;
        $validated['foto_nota'] = ! empty($uploadedPhotoPaths) ? json_encode($uploadedPhotoPaths) : null;

        // Cek apakah obat dengan nama yang sama sudah ada (case-insensitive & trim)
        $existing = Medicine::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($validated['nama']))])->first();

        if ($existing) {
            // Obat sudah ada: Kumpulkan pembelian baru/pembaruan toko
            $purchasesInput = [];

            if ($request->has('purchases') && is_array($request->input('purchases'))) {
                foreach ($request->input('purchases') as $idx => $pData) {
                    $pFoto = null;
                    if ($request->hasFile("purchases.{$idx}.foto_nota")) {
                        $pFoto = $this->uploadSinglePhoto($request->file("purchases.{$idx}.foto_nota"));
                    }
                    if (! empty($pData['toko_obat']) || ! empty($pData['harga']) || ! empty($pData['tanggal_beli']) || ! empty($pData['jumlah'])) {
                        $purchasesInput[] = [
                            'toko_obat' => $pData['toko_obat'] ?? null,
                            'harga' => $pData['harga'] ?? null,
                            'jumlah' => ! empty($pData['jumlah']) ? (int) $pData['jumlah'] : 1,
                            'tanggal_beli' => $pData['tanggal_beli'] ?? null,
                            'catatan' => $pData['catatan'] ?? null,
                            'foto_nota' => $pFoto ?? $primaryFotoPath,
                        ];
                    }
                }
            }

            // Jika tidak menggunakan array purchases, gunakan input tunggal
            if (empty($purchasesInput) && (! empty($validated['toko_obat']) || ! empty($validated['harga']) || ! empty($validated['tanggal_beli']) || ! empty($request->input('jumlah')))) {
                $purchasesInput[] = [
                    'toko_obat' => $validated['toko_obat'] ?? null,
                    'harga' => $validated['harga'] ?? null,
                    'jumlah' => ! empty($request->input('jumlah')) ? (int) $request->input('jumlah') : 1,
                    'tanggal_beli' => $validated['tanggal_beli'] ?? null,
                    'catatan' => null,
                    'foto_nota' => $primaryFotoPath,
                ];
            }

            $updatedStores = [];
            $addedStores = [];
            $totalAddedQty = 0;

            foreach ($purchasesInput as $p) {
                $qty = (int) ($p['jumlah'] ?? 1);
                $totalAddedQty += max(1, $qty);
                $storeName = trim($p['toko_obat'] ?? '');
                $existingPurchase = null;

                if (! empty($storeName)) {
                    $existingPurchase = $existing->purchases()
                        ->whereRaw('LOWER(TRIM(toko_obat)) = ?', [strtolower($storeName)])
                        ->first();
                }

                if ($existingPurchase) {
                    // Nama toko sama: perbarui harga jika berbeda/diisi dan perbarui tanggal beli
                    $updatePurchaseData = [];
                    if (array_key_exists('harga', $p)) {
                        $updatePurchaseData['harga'] = $p['harga'];
                    }
                    if (! empty($p['tanggal_beli'])) {
                        $updatePurchaseData['tanggal_beli'] = $p['tanggal_beli'];
                    }
                    if (! empty($p['catatan'])) {
                        $updatePurchaseData['catatan'] = $p['catatan'];
                    }
                    if (! empty($p['foto_nota'])) {
                        if ($existingPurchase->foto_nota && $existingPurchase->foto_nota !== $p['foto_nota']) {
                            $this->cloudinaryService->deleteImage($existingPurchase->foto_nota);
                        }
                        $updatePurchaseData['foto_nota'] = $p['foto_nota'];
                    }
                    $updatePurchaseData['jumlah'] = ($existingPurchase->jumlah ?? 0) + $qty;

                    $existingPurchase->update($updatePurchaseData);
                    $updatedStores[] = $existingPurchase->toko_obat;
                } else {
                    // Nama toko baru: buat baris pembelian baru
                    $newP = $existing->purchases()->create($p);
                    if (! empty($newP->toko_obat)) {
                        $addedStores[] = $newP->toko_obat;
                    }
                }
            }

            // Perbarui data utama obat dengan data terbaru jika ada
            $masterUpdates = [];
            foreach (['jenis', 'cara_kerja', 'formulasi', 'sasaran_obat', 'tanaman_sasaran', 'dosis_anjuran', 'kemasan_jumlah', 'kemasan_satuan', 'unsur_bahan', 'fase', 'keterangan'] as $field) {
                if (! empty($validated[$field])) {
                    $masterUpdates[$field] = $validated[$field];
                }
            }
            if (! empty($uploadedPhotoPaths)) {
                $combined = array_values(array_unique(array_merge($existing->foto_paths, $uploadedPhotoPaths)));
                $masterUpdates['foto_nota'] = json_encode($combined);
            }

            // Update harga dan toko terakhir pada master record
            if (array_key_exists('harga', $validated) && $validated['harga'] === null) {
                $masterUpdates['harga'] = null;
            } elseif (! empty($purchasesInput)) {
                $lastP = end($purchasesInput);
                if (array_key_exists('harga', $lastP)) {
                    $masterUpdates['harga'] = $lastP['harga'];
                }
                if (! empty($lastP['toko_obat'])) {
                    $masterUpdates['toko_obat'] = $lastP['toko_obat'];
                }
                if (! empty($lastP['tanggal_beli'])) {
                    $masterUpdates['tanggal_beli'] = $lastP['tanggal_beli'];
                }
            }

            // Update stok obat: tambah stok dari pembelian baru jika ada, atau update nilai manual
            $baseTotal = (float) ($existing->base_total_unit ?: 1.0);
            if ($totalAddedQty > 0) {
                $currentVolume = ((float) ($existing->stok_volume ?? 0) > 0)
                    ? (float) $existing->stok_volume
                    : ((float) ($existing->stok ?? 0) * $baseTotal);
                $newVolume = $currentVolume + ((float) $totalAddedQty * $baseTotal);
                $masterUpdates['stok_volume'] = $newVolume;
                $masterUpdates['stok'] = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;
            } elseif (array_key_exists('stok', $validated) && $validated['stok'] !== null) {
                $masterUpdates['stok'] = (int) $validated['stok'];
                $masterUpdates['stok_volume'] = (float) $validated['stok'] * $baseTotal;
            }

            if (! empty($masterUpdates)) {
                $existing->update($masterUpdates);
            }

            $msgParts = [];
            if ($totalAddedQty > 0) {
                $msgParts[] = "stok bertambah +{$totalAddedQty} (total stok: {$existing->stok})";
            }
            if (isset($masterUpdates['harga']) && $masterUpdates['harga'] !== null) {
                $msgParts[] = 'harga terbaru Rp '.number_format($masterUpdates['harga'], 0, ',', '.');
            }
            if (! empty($updatedStores)) {
                $msgParts[] = "toko '".implode(', ', $updatedStores)."' diperbarui";
            }
            if (! empty($addedStores)) {
                $msgParts[] = "toko baru '".implode(', ', $addedStores)."' ditambahkan";
            }
            $msgDetail = ! empty($msgParts) ? ' ('.implode(', ', $msgParts).')' : '';

            return redirect('/data-obat')->with('success', "Obat '{$existing->nama}' berhasil diperbarui{$msgDetail}.");
        }

        // Pastikan kategori obat tersimpan di database dengan warna otomatis
        MedicineCategory::firstOrCreate(
            ['nama' => $validated['jenis']],
            [
                'warna' => MedicineCategory::getNextAvailableColor(),
                'urutan' => (MedicineCategory::max('urutan') ?? 0) + 1,
            ]
        );

        // Kumpulkan pembelian yang akan ditambahkan untuk obat baru
        $purchasesToAdd = [];
        if ($request->has('purchases') && is_array($request->input('purchases'))) {
            foreach ($request->input('purchases') as $idx => $pData) {
                $pFoto = null;
                if ($request->hasFile("purchases.{$idx}.foto_nota")) {
                    $pFoto = $this->uploadSinglePhoto($request->file("purchases.{$idx}.foto_nota"));
                }
                if (! empty($pData['toko_obat']) || ! empty($pData['harga']) || ! empty($pData['tanggal_beli']) || ! empty($pData['jumlah'])) {
                    $purchasesToAdd[] = [
                        'toko_obat' => $pData['toko_obat'] ?? null,
                        'harga' => $pData['harga'] ?? null,
                        'jumlah' => ! empty($pData['jumlah']) ? (int) $pData['jumlah'] : 1,
                        'tanggal_beli' => $pData['tanggal_beli'] ?? null,
                        'catatan' => $pData['catatan'] ?? null,
                        'foto_nota' => $pFoto ?? $primaryFotoPath,
                    ];
                }
            }
        }

        if (empty($purchasesToAdd) && (! empty($validated['toko_obat']) || ! empty($validated['harga']) || ! empty($validated['tanggal_beli']) || ! empty($request->input('jumlah')))) {
            $purchasesToAdd[] = [
                'toko_obat' => $validated['toko_obat'] ?? null,
                'harga' => $validated['harga'] ?? null,
                'jumlah' => ! empty($request->input('jumlah')) ? (int) $request->input('jumlah') : 1,
                'tanggal_beli' => $validated['tanggal_beli'] ?? null,
                'catatan' => null,
                'foto_nota' => $primaryFotoPath,
            ];
        }

        // Tentukan stok awal jika tidak diisi manual
        if (! isset($validated['stok']) || $validated['stok'] === null) {
            $initStok = 0;
            foreach ($purchasesToAdd as $p) {
                $initStok += (int) ($p['jumlah'] ?? 1);
            }
            $validated['stok'] = max(0, $initStok);
        }

        // Jika obat belum ada, buat baru
        $medicine = Medicine::create($validated);
        $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
        $medicine->updateQuietly([
            'stok_volume' => (float) ($medicine->stok ?? 0) * $baseTotal,
        ]);

        // Simpan riwayat pembelian obat
        foreach ($purchasesToAdd as $p) {
            $medicine->purchases()->create($p);
        }

        return redirect('/data-obat')->with('success', "Data obat '{$medicine->nama}' berhasil ditambahkan.");
    }

    public function edit(Medicine $medicine)
    {
        MedicineCategory::ensureTableExists();
        $medicine->load('purchases');
        $existingMedicines = Medicine::with('purchases')->where('id', '!=', $medicine->id)->get();
        $categories = MedicineCategory::orderBy('urutan')->orderBy('nama')->get();

        return view('data-obat.form', compact('medicine', 'existingMedicines', 'categories'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        if ($request->has('harga')) {
            $raw = $request->input('harga');
            if ($raw === null || $raw === '') {
                $request->merge(['harga' => null]);
            } else {
                $cleaned = preg_replace('/\D/', '', (string) $raw);
                $request->merge(['harga' => $cleaned !== '' ? (int) $cleaned : null]);
            }
        }

        if ($request->has('stok')) {
            $rawStok = $request->input('stok');
            if ($rawStok === null || $rawStok === '') {
                $request->merge(['stok' => null]);
            } else {
                $cleanedStok = preg_replace('/\D/', '', (string) $rawStok);
                $request->merge(['stok' => $cleanedStok !== '' ? (int) $cleanedStok : null]);
            }
        }

        if ($request->has('purchases') && is_array($request->input('purchases'))) {
            $purchasesInput = $request->input('purchases');
            foreach ($purchasesInput as $idx => $p) {
                if (array_key_exists('harga', $p)) {
                    $rawP = $p['harga'];
                    if ($rawP === null || trim((string) $rawP) === '') {
                        $purchasesInput[$idx]['harga'] = null;
                    } else {
                        $cleaned = preg_replace('/\D/', '', (string) $rawP);
                        $purchasesInput[$idx]['harga'] = $cleaned !== '' ? (int) $cleaned : null;
                    }
                }
                if (array_key_exists('jumlah', $p)) {
                    $rawJ = $p['jumlah'];
                    if ($rawJ === null || trim((string) $rawJ) === '') {
                        $purchasesInput[$idx]['jumlah'] = null;
                    } else {
                        $cleanedJ = preg_replace('/\D/', '', (string) $rawJ);
                        $purchasesInput[$idx]['jumlah'] = $cleanedJ !== '' ? (int) $cleanedJ : null;
                    }
                }
            }
            $request->merge(['purchases' => $purchasesInput]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'cara_kerja' => 'nullable|string|in:Sistemik,Kontak,Sistemik + Kontak',
            'formulasi' => 'nullable|string|max:50',
            'sasaran_obat' => 'nullable|string|max:255',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'harga' => 'nullable|integer|min:0',
            'stok' => 'nullable|integer|min:0',
            'kemasan_jumlah' => 'nullable|numeric|min:0',
            'kemasan_satuan' => 'nullable|string|max:50',
            'unsur_bahan' => 'nullable|string|max:255',
            'fase' => 'nullable|string|in:Vegetatif,Generatif,Semua Fase',
            'foto_nota' => 'nullable',
            'foto_nota.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'deleted_foto_paths' => 'nullable|array',
            'deleted_foto_paths.*' => 'string',
            'catatan_keamanan' => 'nullable|string|max:1000',
            'keterangan' => 'nullable|string|max:2000',
            'tanggal_beli' => 'nullable|date',
            'toko_obat' => 'nullable|string|max:255',
            'purchases' => 'nullable|array',
            'purchases.*.id' => 'nullable|integer',
            'purchases.*.toko_obat' => 'nullable|string|max:255',
            'purchases.*.harga' => 'nullable|integer|min:0',
            'purchases.*.jumlah' => 'nullable|integer|min:0',
            'purchases.*.tanggal_beli' => 'nullable|date',
            'purchases.*.catatan' => 'nullable|string|max:1000',
            'purchases.*.foto_nota' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Tangani foto yang dihapus oleh user pada form edit
        $currentPaths = $medicine->foto_paths;
        if ($request->has('deleted_foto_paths') && is_array($request->input('deleted_foto_paths'))) {
            $deletedPaths = array_map(fn ($p) => str_replace('\\', '/', trim((string) $p)), $request->input('deleted_foto_paths'));
            foreach ($deletedPaths as $delPath) {
                $this->cloudinaryService->deleteImage($delPath);
                $currentPaths = array_values(array_filter($currentPaths, fn ($p) => str_replace('\\', '/', trim((string) $p)) !== $delPath));
            }
        }

        // Tangani foto-foto baru yang diupload
        $newPhotoPaths = $this->uploadPhotos($request->file('foto_nota'));

        $allPhotoPaths = array_values(array_unique(array_merge($currentPaths, $newPhotoPaths)));
        $validated['foto_nota'] = ! empty($allPhotoPaths) ? json_encode($allPhotoPaths) : null;
        unset($validated['deleted_foto_paths']);

        // Pastikan kategori obat tersimpan di database dengan warna otomatis
        MedicineCategory::firstOrCreate(
            ['nama' => $validated['jenis']],
            [
                'warna' => MedicineCategory::getNextAvailableColor(),
                'urutan' => (MedicineCategory::max('urutan') ?? 0) + 1,
            ]
        );

        $medicine->update($validated);

        if (array_key_exists('stok', $validated) && $validated['stok'] !== null) {
            $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
            $medicine->updateQuietly([
                'stok_volume' => (float) $validated['stok'] * $baseTotal,
            ]);
        }

        // Kelola data riwayat pembelian jika dikirim melalui form edit
        if ($request->has('purchases') && is_array($request->input('purchases'))) {
            $existingIds = [];
            foreach ($request->input('purchases') as $idx => $pData) {
                $hasStore = ! empty(trim($pData['toko_obat'] ?? ''));
                $hasDate = ! empty($pData['tanggal_beli']);
                $hasPrice = isset($pData['harga']) && $pData['harga'] !== null && $pData['harga'] !== '';
                $hasId = ! empty($pData['id']);

                if (! $hasStore && ! $hasDate && ! $hasPrice && ! $hasId) {
                    continue;
                }

                $pFoto = null;
                if ($request->hasFile("purchases.{$idx}.foto_nota")) {
                    $pFoto = $this->uploadSinglePhoto($request->file("purchases.{$idx}.foto_nota"));
                }

                $purchaseId = $pData['id'] ?? null;
                $purchase = null;
                if ($purchaseId) {
                    $purchase = MedicinePurchase::where('medicine_id', $medicine->id)->find($purchaseId);
                }

                // Jika ID tidak ada, cek apakah toko dengan nama yang sama sudah ada di obat ini
                if (! $purchase && ! empty($pData['toko_obat'])) {
                    $purchase = $medicine->purchases()
                        ->whereRaw('LOWER(TRIM(toko_obat)) = ?', [strtolower(trim($pData['toko_obat']))])
                        ->first();
                }

                $cleanPrice = array_key_exists('harga', $pData) && $pData['harga'] !== '' && $pData['harga'] !== null
                    ? (int) $pData['harga']
                    : null;

                if ($purchase) {
                    $updateData = [
                        'toko_obat' => $pData['toko_obat'] ?? $purchase->toko_obat,
                        'harga' => $cleanPrice,
                        'jumlah' => ! empty($pData['jumlah']) ? (int) $pData['jumlah'] : ($purchase->jumlah ?? 1),
                        'tanggal_beli' => ! empty($pData['tanggal_beli']) ? $pData['tanggal_beli'] : null,
                        'catatan' => $pData['catatan'] ?? $purchase->catatan,
                    ];
                    if ($pFoto) {
                        if ($purchase->foto_nota) {
                            $this->cloudinaryService->deleteImage($purchase->foto_nota);
                        }
                        $updateData['foto_nota'] = $pFoto;
                    }
                    $purchase->update($updateData);
                    $existingIds[] = $purchase->id;
                } else {
                    $newPurchase = $medicine->purchases()->create([
                        'toko_obat' => $pData['toko_obat'] ?? null,
                        'harga' => $cleanPrice,
                        'jumlah' => ! empty($pData['jumlah']) ? (int) $pData['jumlah'] : 1,
                        'tanggal_beli' => ! empty($pData['tanggal_beli']) ? $pData['tanggal_beli'] : null,
                        'catatan' => $pData['catatan'] ?? null,
                        'foto_nota' => $pFoto ?? ($allPhotoPaths[0] ?? null),
                    ]);
                    $existingIds[] = $newPurchase->id;
                }
            }

            // Hapus pembelian yang sudah di-remove dari form oleh user
            if (! empty($existingIds)) {
                $medicine->purchases()->whereNotIn('id', $existingIds)->delete();
            } else {
                $medicine->purchases()->delete();
            }

            // Update master harga dan toko:
            // JIKA harga pada input utama dikosongkan (null/empty), maka harga obat HARUS NULL (terhapus)!
            if (array_key_exists('harga', $validated) && $validated['harga'] === null) {
                $medicine->update(['harga' => null]);
            } else {
                // Cari purchase terakhir yang memiliki harga
                $latestWithPrice = $medicine->purchases()->whereNotNull('harga')->latest('tanggal_beli')->latest('id')->first();
                if ($latestWithPrice) {
                    $medicine->update([
                        'harga' => $latestWithPrice->harga,
                        'toko_obat' => $latestWithPrice->toko_obat ?? $medicine->toko_obat,
                        'tanggal_beli' => $latestWithPrice->tanggal_beli ?? $medicine->tanggal_beli,
                    ]);
                } else {
                    $medicine->update([
                        'harga' => $validated['harga'] ?? null,
                    ]);
                }
            }
        } else {
            if (array_key_exists('harga', $validated)) {
                $medicine->update(['harga' => $validated['harga']]);
            }
        }

        return redirect('/data-obat')->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Catat pembelian baru untuk obat yang sudah ada (tambah stok & update harga).
     */
    public function storePurchase(Request $request, Medicine $medicine): RedirectResponse
    {
        if ($request->has('harga')) {
            $raw = $request->input('harga');
            if ($raw === null || $raw === '') {
                $request->merge(['harga' => null]);
            } else {
                $cleaned = preg_replace('/\D/', '', (string) $raw);
                $request->merge(['harga' => $cleaned !== '' ? (int) $cleaned : null]);
            }
        }

        if ($request->has('jumlah')) {
            $rawJ = $request->input('jumlah');
            if ($rawJ === null || $rawJ === '') {
                $request->merge(['jumlah' => 1]);
            } else {
                $cleanedJ = preg_replace('/\D/', '', (string) $rawJ);
                $request->merge(['jumlah' => $cleanedJ !== '' ? max(1, (int) $cleanedJ) : 1]);
            }
        }

        $validated = $request->validate([
            'toko_obat' => 'nullable|string|max:255',
            'harga' => 'nullable|integer|min:0',
            'jumlah' => 'nullable|integer|min:1',
            'tanggal_beli' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
            'foto_nota' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $fotoNotaPath = null;
        if ($request->hasFile('foto_nota')) {
            $fotoNotaPath = $this->uploadSinglePhoto($request->file('foto_nota'));
        }

        $jumlahBeli = $validated['jumlah'] ?? 1;

        // Cek jika toko yang sama sudah tercatat sebelumnya di riwayat
        $storeName = trim($validated['toko_obat'] ?? '');
        $existingPurchase = null;
        if (! empty($storeName)) {
            $existingPurchase = $medicine->purchases()
                ->whereRaw('LOWER(TRIM(toko_obat)) = ?', [strtolower($storeName)])
                ->first();
        }

        $oldHarga = $medicine->harga;
        $cleanHarga = $validated['harga'] ?? null;
        $isPriceDifferent = ($cleanHarga !== null && ($oldHarga === null || (int) $cleanHarga !== (int) $oldHarga));
        $tanggalBeli = $validated['tanggal_beli'] ?? now()->toDateString();

        if ($existingPurchase) {
            $purchaseUpdate = [
                'jumlah' => ($existingPurchase->jumlah ?? 0) + $jumlahBeli,
                'tanggal_beli' => $tanggalBeli,
            ];
            if ($isPriceDifferent) {
                $purchaseUpdate['harga'] = $cleanHarga;
            }
            if (! empty($validated['catatan'])) {
                $purchaseUpdate['catatan'] = $validated['catatan'];
            }
            if ($fotoNotaPath) {
                if ($existingPurchase->foto_nota && $existingPurchase->foto_nota !== $fotoNotaPath) {
                    $this->cloudinaryService->deleteImage($existingPurchase->foto_nota);
                }
                $purchaseUpdate['foto_nota'] = $fotoNotaPath;
            }
            $existingPurchase->update($purchaseUpdate);
        } else {
            $medicine->purchases()->create([
                'toko_obat' => $validated['toko_obat'] ?? null,
                'harga' => $cleanHarga,
                'jumlah' => $jumlahBeli,
                'tanggal_beli' => $tanggalBeli,
                'catatan' => $validated['catatan'] ?? null,
                'foto_nota' => $fotoNotaPath,
            ]);
        }

        // Perbarui data master obat: stok bertambah & tanggal diperbarui
        $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
        $currentVolume = ((float) ($medicine->stok_volume ?? 0) > 0)
            ? (float) $medicine->stok_volume
            : ((float) ($medicine->stok ?? 0) * $baseTotal);
        $newVolume = $currentVolume + ((float) $jumlahBeli * $baseTotal);
        $newStok = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;

        $masterUpdates = [
            'stok' => $newStok,
            'stok_volume' => $newVolume,
            'tanggal_beli' => $tanggalBeli,
        ];

        // Jika harga beda: update harga. Jika sama: cuma update tanggal
        if ($isPriceDifferent) {
            $masterUpdates['harga'] = $cleanHarga;
        }

        if (! empty($validated['toko_obat'])) {
            $masterUpdates['toko_obat'] = $validated['toko_obat'];
        }

        if ($fotoNotaPath) {
            $combined = array_values(array_unique(array_merge($medicine->foto_paths, [$fotoNotaPath])));
            $masterUpdates['foto_nota'] = json_encode($combined);
        }

        $medicine->update($masterUpdates);

        $tglFormatted = Carbon::parse($tanggalBeli)->translatedFormat('d M Y');
        $msg = $isPriceDifferent
            ? "Pembelian obat '{$medicine->nama}' berhasil dicatat! Harga diperbarui menjadi Rp ".number_format($cleanHarga, 0, ',', '.')." (tanggal beli: {$tglFormatted}, stok +{$jumlahBeli}, total: {$medicine->stok})."
            : "Pembelian obat '{$medicine->nama}' berhasil dicatat! Harga tetap Rp ".number_format((int) $oldHarga, 0, ',', '.').", tanggal beli diperbarui ke {$tglFormatted} (stok +{$jumlahBeli}, total: {$medicine->stok}).";

        return redirect()->back()->with('success', $msg);
    }

    public function destroy(Medicine $medicine)
    {
        foreach ($medicine->foto_paths as $p) {
            if ($p) {
                $this->cloudinaryService->deleteImage($p);
            }
        }

        foreach ($medicine->purchases as $p) {
            if ($p->foto_nota) {
                $this->cloudinaryService->deleteImage($p->foto_nota);
            }
        }

        $medicine->delete();

        return redirect('/data-obat')->with('success', 'Data obat berhasil dihapus.');
    }

    /**
     * Hapus satu file foto spesifik dari storage dan database.
     */
    public function destroyPhoto(Request $request, Medicine $medicine)
    {
        $rawPath = $request->input('path');
        if (! $rawPath) {
            return response()->json(['success' => false, 'message' => 'Path foto tidak valid.'], 400);
        }

        $normalizedPath = str_replace('\\', '/', trim((string) $rawPath));

        $this->cloudinaryService->deleteImage($normalizedPath);

        // Hapus dari foto_paths obat
        $currentPaths = $medicine->foto_paths;
        $remaining = array_values(array_filter($currentPaths, fn ($p) => str_replace('\\', '/', trim((string) $p)) !== $normalizedPath));

        $medicine->update([
            'foto_nota' => ! empty($remaining) ? json_encode($remaining) : null,
        ]);

        // Hapus juga dari purchases jika merujuk ke path yang sama
        foreach ($medicine->purchases as $p) {
            if ($p->foto_nota && str_replace('\\', '/', trim((string) $p->foto_nota)) === $normalizedPath) {
                $p->update(['foto_nota' => null]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File foto berhasil dihapus.',
            'remaining_count' => count($remaining),
            'remaining_paths' => $remaining,
        ]);
    }

    /**
     * Upload an array of files using CloudinaryService.
     *
     * @param  array<UploadedFile>|UploadedFile|null  $files
     * @return array<string>
     */
    private function uploadPhotos(mixed $files): array
    {
        if (empty($files)) {
            return [];
        }

        $fileList = is_array($files) ? $files : [$files];
        $urls = [];

        foreach ($fileList as $f) {
            if ($f && $f->isValid()) {
                $res = $this->cloudinaryService->upload($f, 'pertanian/medicines');
                if (! empty($res['url'])) {
                    $urls[] = ($res['storage_type'] ?? '') === 'cloudinary' ? $res['url'] : ($res['public_id'] ?? $res['url']);
                }
            }
        }

        return $urls;
    }

    /**
     * Upload a single file using CloudinaryService.
     */
    private function uploadSinglePhoto(mixed $file): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        $res = $this->cloudinaryService->upload($file, 'pertanian/medicines');

        if (empty($res)) {
            return null;
        }

        return ($res['storage_type'] ?? '') === 'cloudinary' ? ($res['url'] ?? null) : ($res['public_id'] ?? $res['url'] ?? null);
    }

    /**
     * Dapatkan daftar kategori obat (untuk JSON endpoint / sync).
     */
    public function getCategories(Request $request): JsonResponse
    {
        MedicineCategory::ensureTableExists();

        $categories = MedicineCategory::withCount('medicines')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'color_presets' => MedicineCategory::colorPresets(),
        ]);
    }

    /**
     * Simpan kategori obat baru.
     */
    public function storeCategory(Request $request): RedirectResponse|JsonResponse
    {
        MedicineCategory::ensureTableExists();

        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:medicine_categories,nama',
            'warna' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ], [
            'nama.unique' => 'Kategori obat dengan nama tersebut sudah ada.',
            'nama.required' => 'Nama kategori obat wajib diisi.',
        ]);

        try {
            if (empty($validated['warna'])) {
                $validated['warna'] = MedicineCategory::getNextAvailableColor();
            }

            if (! isset($validated['urutan'])) {
                $validated['urutan'] = (MedicineCategory::max('urutan') ?? 0) + 1;
            }

            $validated['deskripsi'] = ! empty($validated['deskripsi']) ? trim((string) $validated['deskripsi']) : null;

            if (empty($validated['id'])) {
                $validated['id'] = ((int) DB::table('medicine_categories')->max('id')) + 1;
            }

            $category = MedicineCategory::create($validated);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Kategori '{$category->nama}' berhasil ditambahkan!",
                    'data' => $category,
                    'theme_config' => $category->theme_config,
                ]);
            }

            return redirect()->back()->with('success', "Kategori '{$category->nama}' berhasil ditambahkan!");
        } catch (\Throwable $e) {
            Log::error('Error storeCategory: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan kategori: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan kategori: '.$e->getMessage());
        }
    }

    /**
     * Perbarui kategori obat.
     */
    public function updateCategory(Request $request, $category): RedirectResponse|JsonResponse
    {
        MedicineCategory::ensureTableExists();

        if (! ($category instanceof MedicineCategory)) {
            $category = MedicineCategory::findOrFail($category);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('medicine_categories', 'nama')->ignore($category->id)],
            'warna' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ], [
            'nama.unique' => 'Kategori obat dengan nama tersebut sudah ada.',
            'nama.required' => 'Nama kategori obat wajib diisi.',
        ]);

        try {
            if (empty($validated['warna'])) {
                $validated['warna'] = $category->warna ?: MedicineCategory::getNextAvailableColor($category->id);
            }

            $validated['deskripsi'] = ! empty($validated['deskripsi']) ? trim((string) $validated['deskripsi']) : null;

            $oldName = $category->nama;
            $category->update($validated);

            if ($oldName !== $category->nama) {
                Medicine::where('jenis', $oldName)->update(['jenis' => $category->nama]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Kategori '{$category->nama}' berhasil diperbarui!",
                    'data' => $category,
                    'theme_config' => $category->theme_config,
                ]);
            }

            return redirect()->back()->with('success', "Kategori '{$category->nama}' berhasil diperbarui!");
        } catch (\Throwable $e) {
            Log::error('Error updateCategory: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kategori: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui kategori: '.$e->getMessage());
        }
    }

    /**
     * Hapus kategori obat.
     */
    public function destroyCategory(Request $request, $category): RedirectResponse|JsonResponse
    {
        MedicineCategory::ensureTableExists();

        if (! ($category instanceof MedicineCategory)) {
            $category = MedicineCategory::findOrFail($category);
        }

        try {
            $count = Medicine::where('jenis', $category->nama)->count();

            if ($count > 0) {
                $msg = "Kategori '{$category->nama}' tidak dapat dihapus karena masih digunakan oleh {$count} data obat.";

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                    ], 422);
                }

                return redirect()->back()->with('error', $msg);
            }

            $name = $category->nama;
            $category->delete();

            $msg = "Kategori '{$name}' berhasil dihapus.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Error destroyCategory: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kategori: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus kategori: '.$e->getMessage());
        }
    }

    /**
     * Tampilan Khusus Menu Stok Obat & Pembelian
     */
    public function stok(Request $request)
    {
        $query = Medicine::with('purchases');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('jenis', 'like', "%{$search}%")
                    ->orWhere('tanaman_sasaran', 'like', "%{$search}%")
                    ->orWhere('toko_obat', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'aman') {
                $query->where('stok', '>', 3);
            } elseif ($status === 'menipis') {
                $query->where('stok', '>', 0)->where('stok', '<=', 3);
            } elseif ($status === 'habis') {
                $query->where(function ($q) {
                    $q->whereNull('stok')->orWhere('stok', '<=', 0);
                });
            }
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis', $jenis);
        }

        // Summary statistics
        $allMedicines = Medicine::with('purchases')->get();
        $totalObat = $allMedicines->count();
        $stokAmanCount = $allMedicines->where('stok', '>', 3)->count();
        $stokMenipisCount = $allMedicines->where('stok', '>', 0)->where('stok', '<=', 3)->count();
        $stokHabisCount = $allMedicines->filter(fn ($m) => ($m->stok ?? 0) <= 0)->count();
        $totalStokUnits = $allMedicines->sum('stok');

        $medicines = $query->orderBy('stok', 'asc')->paginate(12)->withQueryString();
        MedicineCategory::ensureTableExists();
        $categories = MedicineCategory::orderBy('urutan')->orderBy('nama')->get();
        $catalogMedicines = $allMedicines->map(fn ($m) => [
            'id' => $m->id,
            'nama' => $m->nama,
            'jenis' => $m->jenis ?? 'Obat Pertanian',
            'tanaman_sasaran' => $m->tanaman_sasaran ?? '',
            'kemasan_jumlah' => $m->kemasan_jumlah,
            'kemasan_satuan' => $m->kemasan_satuan,
            'kemasan_label' => $m->kemasan_label,
            'stok_label' => $m->stok_label,
            'stok_volume' => $m->stok_volume,
            'stok_volume_label' => $m->stok_volume_label,
            'harga' => $m->harga ? (int) $m->harga : 0,
            'formatted_harga' => $m->harga ? 'Rp '.number_format($m->harga, 0, ',', '.') : 'Rp 0',
            'stok' => $m->stok ? (int) $m->stok : 0,
            'toko_obat' => $m->toko_obat ?? '',
        ])->values()->all();

        return view('data-obat.stok', compact(
            'medicines',
            'categories',
            'allMedicines',
            'catalogMedicines',
            'totalObat',
            'stokAmanCount',
            'stokMenipisCount',
            'stokHabisCount',
            'totalStokUnits'
        ));
    }

    /**
     * Catat Pembelian Obat Umum (Update stok jika ada, buat baru jika belum)
     */
    public function storeGeneralPurchase(Request $request): RedirectResponse
    {
        if ($request->has('harga')) {
            $raw = $request->input('harga');
            $cleaned = preg_replace('/\D/', '', (string) $raw);
            $request->merge(['harga' => $cleaned !== '' ? (int) $cleaned : null]);
        }

        if ($request->has('jumlah')) {
            $rawJ = $request->input('jumlah');
            $cleanedJ = preg_replace('/\D/', '', (string) $rawJ);
            $request->merge(['jumlah' => $cleanedJ !== '' ? max(1, (int) $cleanedJ) : 1]);
        }

        $validated = $request->validate([
            'medicine_id' => 'nullable|exists:medicines,id',
            'nama' => 'required_without:medicine_id|nullable|string|max:255',
            'jenis' => 'nullable|string|max:100',
            'kemasan_jumlah' => 'nullable|numeric|min:0',
            'kemasan_satuan' => 'nullable|string|max:50',
            'toko_obat' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'jumlah' => 'required|integer|min:1',
            'tanggal_beli' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
            'tanaman_sasaran' => 'nullable|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
        ]);

        $medicine = null;
        if (! empty($validated['medicine_id'])) {
            $medicine = Medicine::find($validated['medicine_id']);
        }

        if (! $medicine && ! empty($validated['nama'])) {
            $trimmedName = trim($validated['nama']);
            $medicine = Medicine::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($trimmedName)])->first();
        }

        $cleanHarga = $validated['harga'];
        $jumlahBeli = $validated['jumlah'];
        $tanggalBeli = $validated['tanggal_beli'] ?? now()->toDateString();
        $toko = trim($validated['toko_obat']);
        $catatan = $validated['catatan'] ?? null;

        if ($medicine) {
            $oldHarga = $medicine->harga;
            $isPriceDifferent = ($oldHarga === null || (int) $cleanHarga !== (int) $oldHarga);

            // Update kemasan & satuan jika diisi
            if (! empty($validated['kemasan_satuan'])) {
                $medicine->kemasan_satuan = $validated['kemasan_satuan'];
            }
            if (array_key_exists('kemasan_jumlah', $validated) && $validated['kemasan_jumlah'] !== null) {
                $medicine->kemasan_jumlah = $validated['kemasan_jumlah'];
            }

            // Selalu tambah stok & update tanggal
            $baseTotal = (float) ($medicine->base_total_unit ?: 1.0);
            $currentVolume = ((float) ($medicine->stok_volume ?? 0) > 0)
                ? (float) $medicine->stok_volume
                : ((float) ($medicine->stok ?? 0) * $baseTotal);
            $newVolume = $currentVolume + ((float) $jumlahBeli * $baseTotal);
            $medicine->stok = $baseTotal > 0 ? (int) ceil($newVolume / $baseTotal) : (int) $newVolume;
            $medicine->stok_volume = $newVolume;
            $medicine->tanggal_beli = $tanggalBeli;
            if (! empty($toko)) {
                $medicine->toko_obat = $toko;
            }

            // Update harga hanya jika berbeda
            if ($isPriceDifferent) {
                $medicine->harga = $cleanHarga;
            }

            $medicine->save();

            // Record purchase di riwayat pembelian per toko
            $existingPurchase = $medicine->purchases()
                ->whereRaw('LOWER(TRIM(toko_obat)) = ?', [strtolower($toko)])
                ->first();

            if ($existingPurchase) {
                $purchaseUpdate = [
                    'jumlah' => ($existingPurchase->jumlah ?? 0) + $jumlahBeli,
                    'tanggal_beli' => $tanggalBeli,
                    'catatan' => $catatan ?? $existingPurchase->catatan,
                ];
                if ($isPriceDifferent) {
                    $purchaseUpdate['harga'] = $cleanHarga;
                }
                $existingPurchase->update($purchaseUpdate);
            } else {
                $medicine->purchases()->create([
                    'toko_obat' => $toko,
                    'harga' => $cleanHarga,
                    'jumlah' => $jumlahBeli,
                    'tanggal_beli' => $tanggalBeli,
                    'catatan' => $catatan,
                ]);
            }

            $tglFormatted = Carbon::parse($tanggalBeli)->translatedFormat('d M Y');
            $successMsg = $isPriceDifferent
                ? "Pembelian '{$medicine->nama}' berhasil dicatat! Harga diperbarui menjadi Rp ".number_format($cleanHarga, 0, ',', '.')." (tanggal beli: {$tglFormatted}, stok +{$jumlahBeli}, total: {$medicine->stok_label})."
                : "Pembelian '{$medicine->nama}' berhasil dicatat! Harga tetap Rp ".number_format((int) $oldHarga, 0, ',', '.').", tanggal beli diperbarui ke {$tglFormatted} (stok +{$jumlahBeli}, total: {$medicine->stok_label}).";

            return redirect()->back()->with('success', $successMsg);
        }

        // Obat belum ada (pertama kali beli): langsung simpan ke stok dan data obat!
        if (! empty($validated['jenis'])) {
            MedicineCategory::firstOrCreate(
                ['nama' => $validated['jenis']],
                [
                    'warna' => MedicineCategory::getNextAvailableColor(),
                    'urutan' => (MedicineCategory::max('urutan') ?? 0) + 1,
                ]
            );
        }

        $newMedicine = Medicine::create([
            'nama' => trim($validated['nama']),
            'jenis' => ! empty($validated['jenis']) ? $validated['jenis'] : 'Fungisida',
            'tanaman_sasaran' => ! empty($validated['tanaman_sasaran']) ? $validated['tanaman_sasaran'] : 'Umum / Semua Tanaman',
            'dosis_anjuran' => $validated['dosis_anjuran'] ?? null,
            'kemasan_jumlah' => $validated['kemasan_jumlah'] ?? null,
            'kemasan_satuan' => ! empty($validated['kemasan_satuan']) ? $validated['kemasan_satuan'] : null,
            'harga' => $cleanHarga,
            'stok' => $jumlahBeli,
            'toko_obat' => $toko,
            'tanggal_beli' => $tanggalBeli,
        ]);

        $baseTotal = (float) ($newMedicine->base_total_unit ?: 1.0);
        $newMedicine->updateQuietly([
            'stok_volume' => (float) $jumlahBeli * $baseTotal,
        ]);

        $newMedicine->purchases()->create([
            'toko_obat' => $toko,
            'harga' => $cleanHarga,
            'jumlah' => $jumlahBeli,
            'tanggal_beli' => $tanggalBeli,
            'catatan' => $catatan,
        ]);

        return redirect()->back()->with('success', "Obat baru '{$newMedicine->nama}' berhasil disimpan ke Data Obat & stok awal tersimpan ({$newMedicine->stok_label}, Rp ".number_format($cleanHarga, 0, ',', '.').').');
    }
}
