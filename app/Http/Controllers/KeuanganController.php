<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Database\Seeders\FinancialCategorySeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $periode = (string) $request->query('periode', 'semua');

        $query = FinancialTransaction::with('crop')->latest('tanggal');

        if ($periode === 'bulan_ini') {
            $now = Carbon::now();
            $query->whereMonth('tanggal', $now->month)
                ->whereYear('tanggal', $now->year);
        } elseif ($periode === 'tahun_ini') {
            $now = Carbon::now();
            $query->whereYear('tanggal', $now->year);
        }

        $transactions = $query->get();

        $pemasukanList = $transactions->where('tipe', 'pemasukan');
        $pengeluaranList = $transactions->where('tipe', 'pengeluaran');

        $totalPemasukan = (float) $pemasukanList->sum('nominal');
        $totalPengeluaran = (float) $pengeluaranList->sum('nominal');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

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
                'tanggal' => $m->tanggal,
                'tanggal_raw' => $m->tanggal ? $m->tanggal->format('Y-m-d') : null,
                'formatted_tanggal' => $m->tanggal ? $m->tanggal->format('d M Y') : '—',
                'nominal' => (float) $m->nominal,
                'formatted_nominal' => $m->formatted_nominal,
                'nota_url' => $m->foto_url,
                'crop_id' => $m->crop_id,
                'keterangan' => $m->keterangan,
            ];
        })->values();

        $crops = Crop::orderBy('nama_tanaman')->get();
        if (FinancialCategory::count() === 0) {
            (new FinancialCategorySeeder)->run();
        }
        FinancialCategory::syncFromTransactions();
        $categories = FinancialCategory::parents()->with('subcategories')->get();

        $laporanSubtotal = $this->calculateSubtotalReport($transactions);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'periode' => $periode,
                'totalPemasukan' => $totalPemasukan,
                'formatted_total_pemasukan' => 'Rp '.number_format($totalPemasukan, 0, ',', '.'),
                'totalPengeluaran' => $totalPengeluaran,
                'formatted_total_pengeluaran' => 'Rp '.number_format($totalPengeluaran, 0, ',', '.'),
                'saldoBersih' => $saldoBersih,
                'formatted_saldo_bersih' => 'Rp '.number_format($saldoBersih, 0, ',', '.'),
                'transaksiList' => $transaksiList,
                'totalTransaksi' => $transaksiList->count(),
                'jumlahPemasukan' => $pemasukanList->count(),
                'jumlahPengeluaran' => $pengeluaranList->count(),
                'categories' => $categories,
                'laporanSubtotal' => $laporanSubtotal,
            ]);
        }

        return view('keuangan.index', [
            'periode' => $periode,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoBersih' => $saldoBersih,
            'transaksiList' => $transaksiList,
            'totalTransaksi' => $transaksiList->count(),
            'jumlahPemasukan' => $pemasukanList->count(),
            'jumlahPengeluaran' => $pengeluaranList->count(),
            'crops' => $crops,
            'categories' => $categories,
            'laporanSubtotal' => $laporanSubtotal,
        ]);
    }

    /**
     * Menghitung rekapitulasi subtotal per kategori dan subkategori untuk laporan keuangan.
     *
     * @param  Collection<int, FinancialTransaction>  $transactions
     * @return array<string, mixed>
     */
    protected function calculateSubtotalReport($transactions): array
    {
        $report = [
            'pengeluaran' => [
                'total' => 0.0,
                'formatted_total' => 'Rp 0',
                'categories' => [],
            ],
            'pemasukan' => [
                'total' => 0.0,
                'formatted_total' => 'Rp 0',
                'categories' => [],
            ],
            'ringkasan' => [
                'total_pemasukan' => 0.0,
                'total_pengeluaran' => 0.0,
                'saldo_bersih' => 0.0,
                'formatted_total_pemasukan' => 'Rp 0',
                'formatted_total_pengeluaran' => 'Rp 0',
                'formatted_saldo_bersih' => 'Rp 0',
            ],
        ];

        foreach (['pengeluaran', 'pemasukan'] as $tipe) {
            $group = $transactions->where('tipe', $tipe);
            $totalTipe = (float) $group->sum('nominal');
            $categoriesMap = [];

            foreach ($group as $trx) {
                $catName = trim((string) ($trx->kategori ?: 'Lainnya'));
                $rawSub = trim((string) ($trx->sub_kategori ?: ''));
                $nominal = (float) $trx->nominal;

                if (! isset($categoriesMap[$catName])) {
                    $categoriesMap[$catName] = [
                        'nama' => $catName,
                        'subtotal' => 0.0,
                        'jumlah_transaksi' => 0,
                        'subkategori' => [],
                    ];
                }

                $categoriesMap[$catName]['subtotal'] += $nominal;
                $categoriesMap[$catName]['jumlah_transaksi'] += 1;

                if ($rawSub === '') {
                    $subKey = '— (Tanpa Subkategori)';
                    if (! isset($categoriesMap[$catName]['subkategori'][$subKey])) {
                        $categoriesMap[$catName]['subkategori'][$subKey] = [
                            'nama' => $subKey,
                            'is_tanpa_subkategori' => true,
                            'subtotal' => 0.0,
                            'jumlah_transaksi' => 0,
                            'has_children' => false,
                            'children' => [],
                        ];
                    }
                    $categoriesMap[$catName]['subkategori'][$subKey]['subtotal'] += $nominal;
                    $categoriesMap[$catName]['subkategori'][$subKey]['jumlah_transaksi'] += 1;
                } else {
                    $parts = preg_split('/\s*[›>]\s*/u', $rawSub);
                    $subName = trim($parts[0]);
                    $childName = (count($parts) > 1) ? trim(implode(' › ', array_slice($parts, 1))) : null;

                    if (! isset($categoriesMap[$catName]['subkategori'][$subName])) {
                        $categoriesMap[$catName]['subkategori'][$subName] = [
                            'nama' => $subName,
                            'is_tanpa_subkategori' => false,
                            'subtotal' => 0.0,
                            'jumlah_transaksi' => 0,
                            'has_children' => false,
                            'children' => [],
                            '_direct_nominal' => 0.0,
                            '_direct_count' => 0,
                        ];
                    }

                    $categoriesMap[$catName]['subkategori'][$subName]['subtotal'] += $nominal;
                    $categoriesMap[$catName]['subkategori'][$subName]['jumlah_transaksi'] += 1;

                    if ($childName !== null && $childName !== '') {
                        $categoriesMap[$catName]['subkategori'][$subName]['has_children'] = true;
                        if (! isset($categoriesMap[$catName]['subkategori'][$subName]['children'][$childName])) {
                            $categoriesMap[$catName]['subkategori'][$subName]['children'][$childName] = [
                                'nama' => $childName,
                                'subtotal' => 0.0,
                                'jumlah_transaksi' => 0,
                            ];
                        }
                        $categoriesMap[$catName]['subkategori'][$subName]['children'][$childName]['subtotal'] += $nominal;
                        $categoriesMap[$catName]['subkategori'][$subName]['children'][$childName]['jumlah_transaksi'] += 1;
                    } else {
                        $categoriesMap[$catName]['subkategori'][$subName]['_direct_nominal'] += $nominal;
                        $categoriesMap[$catName]['subkategori'][$subName]['_direct_count'] += 1;
                    }
                }
            }

            uasort($categoriesMap, fn ($a, $b) => $b['subtotal'] <=> $a['subtotal']);

            foreach ($categoriesMap as &$catItem) {
                uasort($catItem['subkategori'], fn ($a, $b) => $b['subtotal'] <=> $a['subtotal']);
                $catItem['persentase'] = $totalTipe > 0 ? round(($catItem['subtotal'] / $totalTipe) * 100, 1) : 0;
                $catItem['formatted_subtotal'] = 'Rp '.number_format($catItem['subtotal'], 0, ',', '.');

                foreach ($catItem['subkategori'] as &$subItem) {
                    $subItem['persentase_kategori'] = $catItem['subtotal'] > 0 ? round(($subItem['subtotal'] / $catItem['subtotal']) * 100, 1) : 0;
                    $subItem['formatted_subtotal'] = 'Rp '.number_format($subItem['subtotal'], 0, ',', '.');

                    if (! empty($subItem['has_children']) && ! empty($subItem['children'])) {
                        if (! empty($subItem['_direct_nominal'])) {
                            $subItem['children']['Lainnya / Umum'] = [
                                'nama' => 'Lainnya / Umum',
                                'subtotal' => $subItem['_direct_nominal'],
                                'jumlah_transaksi' => $subItem['_direct_count'],
                            ];
                        }
                        uasort($subItem['children'], fn ($a, $b) => $b['subtotal'] <=> $a['subtotal']);
                        foreach ($subItem['children'] as &$childItem) {
                            $childItem['persentase_parent'] = $subItem['subtotal'] > 0 ? round(($childItem['subtotal'] / $subItem['subtotal']) * 100, 1) : 0;
                            $childItem['formatted_subtotal'] = 'Rp '.number_format($childItem['subtotal'], 0, ',', '.');
                        }
                        unset($childItem);
                        $subItem['children'] = array_values($subItem['children']);
                    }
                    unset($subItem['_direct_nominal'], $subItem['_direct_count']);
                }
                unset($subItem);
                $catItem['subkategori'] = array_values($catItem['subkategori']);
            }
            unset($catItem);

            $report[$tipe]['total'] = $totalTipe;
            $report[$tipe]['formatted_total'] = 'Rp '.number_format($totalTipe, 0, ',', '.');
            $report[$tipe]['categories'] = array_values($categoriesMap);
        }

        $report['ringkasan']['total_pemasukan'] = $report['pemasukan']['total'];
        $report['ringkasan']['total_pengeluaran'] = $report['pengeluaran']['total'];
        $report['ringkasan']['saldo_bersih'] = $report['pemasukan']['total'] - $report['pengeluaran']['total'];
        $report['ringkasan']['formatted_total_pemasukan'] = 'Rp '.number_format($report['ringkasan']['total_pemasukan'], 0, ',', '.');
        $report['ringkasan']['formatted_total_pengeluaran'] = 'Rp '.number_format($report['ringkasan']['total_pengeluaran'], 0, ',', '.');
        $report['ringkasan']['formatted_saldo_bersih'] = 'Rp '.number_format($report['ringkasan']['saldo_bersih'], 0, ',', '.');

        return $report;
    }

    public function store(Request $request, CloudinaryService $cloudinaryService): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:100',
            'sub_kategori' => 'nullable|string|max:100',
            'judul' => 'required|string|max:255',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'crop_id' => 'nullable|exists:crops,id',
            'keterangan' => 'nullable|string|max:1000',
            'foto_nota' => 'nullable|image|max:10240',
            'foto_nota_base64' => 'nullable|string',
        ]);

        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $validated['nominal']);
        if ($nominal <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Nominal harus lebih dari 0',
                    'errors' => ['nominal' => ['Nominal harus lebih dari 0']],
                ], 422);
            }

            return back()->withErrors(['nominal' => 'Nominal harus lebih dari 0'])->withInput();
        }

        $fotoPath = null;
        if ($request->filled('foto_nota_base64')) {
            $fotoPath = $this->uploadSingleBase64Photo($request->input('foto_nota_base64'), $cloudinaryService);
        } elseif ($request->hasFile('foto_nota')) {
            $uploadResult = $cloudinaryService->upload($request->file('foto_nota'), 'pertanian/keuangan');
            $fotoPath = $uploadResult['url'] ?? null;
        }

        // Pastikan hierarki kategori dan sub-kategori tersimpan di master kategori
        FinancialCategory::ensureCategoryHierarchy(
            $validated['kategori'],
            $validated['sub_kategori'] ?? null,
            $validated['tipe']
        );

        $transaction = FinancialTransaction::create([
            'crop_id' => $validated['crop_id'] ?? null,
            'tipe' => $validated['tipe'],
            'kategori' => $validated['kategori'],
            'sub_kategori' => $validated['sub_kategori'] ?? null,
            'judul' => $validated['judul'],
            'nominal' => $nominal,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_nota' => $fotoPath,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi keuangan berhasil dicatat!',
                'data' => $transaction,
                'categories' => FinancialCategory::parents()->with('subcategories')->get(),
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', 'Transaksi keuangan berhasil dicatat!');
    }

    public function update(Request $request, FinancialTransaction $transaction, CloudinaryService $cloudinaryService): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:100',
            'sub_kategori' => 'nullable|string|max:100',
            'judul' => 'required|string|max:255',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'crop_id' => 'nullable|exists:crops,id',
            'keterangan' => 'nullable|string|max:1000',
            'foto_nota' => 'nullable|image|max:10240',
            'foto_nota_base64' => 'nullable|string',
        ]);

        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $validated['nominal']);
        if ($nominal <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Nominal harus lebih dari 0',
                    'errors' => ['nominal' => ['Nominal harus lebih dari 0']],
                ], 422);
            }

            return back()->withErrors(['nominal' => 'Nominal harus lebih dari 0'])->withInput();
        }

        $fotoPath = $transaction->foto_nota;
        if ($request->filled('foto_nota_base64')) {
            $newPhoto = $this->uploadSingleBase64Photo($request->input('foto_nota_base64'), $cloudinaryService);
            if ($newPhoto) {
                $fotoPath = $newPhoto;
            }
        } elseif ($request->hasFile('foto_nota')) {
            $uploadResult = $cloudinaryService->upload($request->file('foto_nota'), 'pertanian/keuangan');
            $fotoPath = $uploadResult['url'] ?? null;
        }

        // Pastikan hierarki kategori dan sub-kategori tersimpan di master kategori
        FinancialCategory::ensureCategoryHierarchy(
            $validated['kategori'],
            $validated['sub_kategori'] ?? null,
            $validated['tipe']
        );

        $transaction->update([
            'crop_id' => $validated['crop_id'] ?? null,
            'tipe' => $validated['tipe'],
            'kategori' => $validated['kategori'],
            'sub_kategori' => $validated['sub_kategori'] ?? null,
            'judul' => $validated['judul'],
            'nominal' => $nominal,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_nota' => $fotoPath,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi keuangan berhasil diperbarui!',
                'data' => $transaction,
                'categories' => FinancialCategory::parents()->with('subcategories')->get(),
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', 'Transaksi keuangan berhasil diperbarui!');
    }

    public function destroy(Request $request, FinancialTransaction $transaction): RedirectResponse|JsonResponse
    {
        $transaction->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi keuangan berhasil dihapus!',
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', 'Transaksi keuangan berhasil dihapus!');
    }

    public function storeCategory(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:financial_categories,id',
            'tipe' => 'required|in:pemasukan,pengeluaran,keduanya',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $cat = FinancialCategory::create($validated);

        $label = $request->parent_id ? 'Sub-kategori' : 'Kategori';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$label} berhasil ditambahkan!",
                'data' => $cat,
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', "{$label} berhasil ditambahkan!");
    }

    public function updateCategory(Request $request, FinancialCategory $category): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:financial_categories,id',
            'tipe' => 'required|in:pemasukan,pengeluaran,keduanya',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $oldNama = $category->nama;
        $oldParentId = $category->parent_id;
        $oldHierarchy = FinancialCategory::getCategoryHierarchyPath($category);

        $category->update($validated);

        FinancialCategory::cascadeCategoryUpdate($category, $oldNama, $oldParentId, $oldHierarchy);

        $label = $category->parent_id ? 'Sub-kategori' : 'Kategori';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$label} berhasil diperbarui!",
                'data' => $category,
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', "{$label} berhasil diperbarui!");
    }

    public function destroyCategory(Request $request, FinancialCategory $category): RedirectResponse|JsonResponse
    {
        $label = $category->parent_id ? 'Sub-kategori' : 'Kategori';
        $category->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$label} berhasil dihapus!",
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', "{$label} berhasil dihapus!");
    }

    /**
     * Decode base64 image data and upload it via CloudinaryService or store locally.
     */
    private function uploadSingleBase64Photo(string $base64, CloudinaryService $cloudinaryService): ?string
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
            $uploadResult = $cloudinaryService->upload($tempPath, 'pertanian/keuangan');
            @unlink($tempPath);

            return $uploadResult['url'] ?? null;
        } catch (\Throwable $e) {
            @unlink($tempPath);
            Log::warning('Cloudinary upload base64 failed, saving locally: '.$e->getMessage());

            $filename = 'keuangan/nota_'.Str::random(40).'.'.$extension;
            Storage::disk('public')->put($filename, $binary);

            return Storage::disk('public')->url($filename);
        }
    }
}
