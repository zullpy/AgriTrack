<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicinePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::with('purchases');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
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

        if ($fase = $request->input('fase')) {
            $query->where('fase', $fase);
        }

        $medicines = $query->latest()->paginate(10)->withQueryString();

        return view('data-obat.index', compact('medicines'));
    }

    public function create()
    {
        $existingMedicines = Medicine::with('purchases')->get();

        return view('data-obat.form', compact('existingMedicines'));
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

        // Bersihkan harga pada setiap array purchases jika ada
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
            }
            $request->merge(['purchases' => $purchasesInput]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|in:Fungisida,Insektisida,Pupuk,Vitamin,Bibit,Perlengkapan,Peralatan',
            'cara_kerja' => 'nullable|string|in:Sistemik,Kontak,Sistemik + Kontak',
            'sasaran_obat' => 'nullable|string|max:255',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'harga' => 'nullable|integer|min:0',
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
            'purchases.*.tanggal_beli' => 'nullable|date',
            'purchases.*.catatan' => 'nullable|string|max:1000',
            'purchases.*.foto_nota' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotoPaths = [];
        if ($request->hasFile('foto_nota')) {
            $files = is_array($request->file('foto_nota')) ? $request->file('foto_nota') : [$request->file('foto_nota')];
            foreach ($files as $f) {
                if ($f && $f->isValid()) {
                    $uploadedPhotoPaths[] = $f->store('medicines', 'public');
                }
            }
        }
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
                        $pFoto = $request->file("purchases.{$idx}.foto_nota")->store('medicines', 'public');
                    }
                    if (! empty($pData['toko_obat']) || ! empty($pData['harga']) || ! empty($pData['tanggal_beli'])) {
                        $purchasesInput[] = [
                            'toko_obat' => $pData['toko_obat'] ?? null,
                            'harga' => $pData['harga'] ?? null,
                            'tanggal_beli' => $pData['tanggal_beli'] ?? null,
                            'catatan' => $pData['catatan'] ?? null,
                            'foto_nota' => $pFoto ?? $primaryFotoPath,
                        ];
                    }
                }
            }

            // Jika tidak menggunakan array purchases, gunakan input tunggal
            if (empty($purchasesInput) && (! empty($validated['toko_obat']) || ! empty($validated['harga']) || ! empty($validated['tanggal_beli']))) {
                $purchasesInput[] = [
                    'toko_obat' => $validated['toko_obat'] ?? null,
                    'harga' => $validated['harga'] ?? null,
                    'tanggal_beli' => $validated['tanggal_beli'] ?? null,
                    'catatan' => null,
                    'foto_nota' => $primaryFotoPath,
                ];
            }

            $updatedStores = [];
            $addedStores = [];

            foreach ($purchasesInput as $p) {
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
                            Storage::disk('public')->delete($existingPurchase->foto_nota);
                        }
                        $updatePurchaseData['foto_nota'] = $p['foto_nota'];
                    }

                    if (! empty($updatePurchaseData)) {
                        $existingPurchase->update($updatePurchaseData);
                    }
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
            foreach (['jenis', 'cara_kerja', 'sasaran_obat', 'tanaman_sasaran', 'dosis_anjuran', 'unsur_bahan', 'fase', 'keterangan'] as $field) {
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

            if (! empty($masterUpdates)) {
                $existing->update($masterUpdates);
            }

            $msgParts = [];
            if (! empty($updatedStores)) {
                $msgParts[] = "harga & tanggal toko '".implode(', ', $updatedStores)."' berhasil diperbarui";
            }
            if (! empty($addedStores)) {
                $msgParts[] = "pembelian dari toko baru '".implode(', ', $addedStores)."' berhasil ditambahkan";
            }
            $msgDetail = ! empty($msgParts) ? ' ('.implode(', ', $msgParts).')' : '';

            return redirect('/data-obat')->with('success', "Obat '{$existing->nama}' sudah terdaftar{$msgDetail}.");
        }

        // Jika obat belum ada, buat baru
        $medicine = Medicine::create($validated);

        // Simpan pembelian
        $purchasesToAdd = [];
        if ($request->has('purchases') && is_array($request->input('purchases'))) {
            foreach ($request->input('purchases') as $idx => $pData) {
                $pFoto = null;
                if ($request->hasFile("purchases.{$idx}.foto_nota")) {
                    $pFoto = $request->file("purchases.{$idx}.foto_nota")->store('medicines', 'public');
                }
                if (! empty($pData['toko_obat']) || ! empty($pData['harga']) || ! empty($pData['tanggal_beli'])) {
                    $purchasesToAdd[] = [
                        'toko_obat' => $pData['toko_obat'] ?? null,
                        'harga' => $pData['harga'] ?? null,
                        'tanggal_beli' => $pData['tanggal_beli'] ?? null,
                        'catatan' => $pData['catatan'] ?? null,
                        'foto_nota' => $pFoto ?? $primaryFotoPath,
                    ];
                }
            }
        }

        if (empty($purchasesToAdd) && (! empty($validated['toko_obat']) || ! empty($validated['harga']) || ! empty($validated['tanggal_beli']))) {
            $purchasesToAdd[] = [
                'toko_obat' => $validated['toko_obat'] ?? null,
                'harga' => $validated['harga'] ?? null,
                'tanggal_beli' => $validated['tanggal_beli'] ?? null,
                'catatan' => null,
                'foto_nota' => $primaryFotoPath,
            ];
        }

        foreach ($purchasesToAdd as $p) {
            $medicine->purchases()->create($p);
        }

        return redirect('/data-obat')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Medicine $medicine)
    {
        $medicine->load('purchases');
        $existingMedicines = Medicine::with('purchases')->where('id', '!=', $medicine->id)->get();

        return view('data-obat.form', compact('medicine', 'existingMedicines'));
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
            }
            $request->merge(['purchases' => $purchasesInput]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|in:Fungisida,Insektisida,Pupuk,Vitamin,Bibit,Perlengkapan,Peralatan',
            'cara_kerja' => 'nullable|string|in:Sistemik,Kontak,Sistemik + Kontak',
            'sasaran_obat' => 'nullable|string|max:255',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'harga' => 'nullable|integer|min:0',
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
            'purchases.*.tanggal_beli' => 'nullable|date',
            'purchases.*.catatan' => 'nullable|string|max:1000',
            'purchases.*.foto_nota' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Tangani foto yang dihapus oleh user pada form edit
        $currentPaths = $medicine->foto_paths;
        if ($request->has('deleted_foto_paths') && is_array($request->input('deleted_foto_paths'))) {
            $deletedPaths = array_map(fn ($p) => str_replace('\\', '/', trim((string) $p)), $request->input('deleted_foto_paths'));
            foreach ($deletedPaths as $delPath) {
                if (Storage::disk('public')->exists($delPath)) {
                    Storage::disk('public')->delete($delPath);
                }
                $currentPaths = array_values(array_filter($currentPaths, fn ($p) => str_replace('\\', '/', trim((string) $p)) !== $delPath));
            }
        }

        // Tangani foto-foto baru yang diupload
        $newPhotoPaths = [];
        if ($request->hasFile('foto_nota')) {
            $files = is_array($request->file('foto_nota')) ? $request->file('foto_nota') : [$request->file('foto_nota')];
            foreach ($files as $f) {
                if ($f && $f->isValid()) {
                    $newPhotoPaths[] = $f->store('medicines', 'public');
                }
            }
        }

        $allPhotoPaths = array_values(array_unique(array_merge($currentPaths, $newPhotoPaths)));
        $validated['foto_nota'] = ! empty($allPhotoPaths) ? json_encode($allPhotoPaths) : null;
        unset($validated['deleted_foto_paths']);

        $medicine->update($validated);

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
                    $pFoto = $request->file("purchases.{$idx}.foto_nota")->store('medicines', 'public');
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
                        'tanggal_beli' => ! empty($pData['tanggal_beli']) ? $pData['tanggal_beli'] : null,
                        'catatan' => $pData['catatan'] ?? $purchase->catatan,
                    ];
                    if ($pFoto) {
                        if ($purchase->foto_nota) {
                            Storage::disk('public')->delete($purchase->foto_nota);
                        }
                        $updateData['foto_nota'] = $pFoto;
                    }
                    $purchase->update($updateData);
                    $existingIds[] = $purchase->id;
                } else {
                    $newPurchase = $medicine->purchases()->create([
                        'toko_obat' => $pData['toko_obat'] ?? null,
                        'harga' => $cleanPrice,
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

    public function destroy(Medicine $medicine)
    {
        foreach ($medicine->foto_paths as $p) {
            if ($p) {
                Storage::disk('public')->delete($p);
            }
        }

        foreach ($medicine->purchases as $p) {
            if ($p->foto_nota) {
                Storage::disk('public')->delete($p->foto_nota);
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

        // Hapus file fisik dari storage disk public jika ada
        if (Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }

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
}
