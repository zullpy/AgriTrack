<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $categories = FinancialCategory::parents()->with('subcategories')->get();

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
        ]);
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
        if ($request->hasFile('foto_nota')) {
            $uploadResult = $cloudinaryService->upload($request->file('foto_nota'), 'pertanian/keuangan');
            $fotoPath = $uploadResult['url'] ?? null;
        }

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
        if ($request->hasFile('foto_nota')) {
            $uploadResult = $cloudinaryService->upload($request->file('foto_nota'), 'pertanian/keuangan');
            $fotoPath = $uploadResult['url'] ?? null;
        }

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

        $category->update($validated);

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
}
