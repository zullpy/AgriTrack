<?php

namespace App\Http\Controllers;

use App\Models\CropHarvest;
use App\Models\MedicinePurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    public function index(Request $request): View
    {
        $periode = (string) $request->query('periode', 'semua');

        $harvestQuery = CropHarvest::with('crop')->latest('tanggal_panen');
        $purchaseQuery = MedicinePurchase::with('medicine')->latest('tanggal_beli');

        if ($periode === 'bulan_ini') {
            $now = Carbon::now();
            $harvestQuery->whereMonth('tanggal_panen', $now->month)
                ->whereYear('tanggal_panen', $now->year);
            $purchaseQuery->whereMonth('tanggal_beli', $now->month)
                ->whereYear('tanggal_beli', $now->year);
        } elseif ($periode === 'tahun_ini') {
            $now = Carbon::now();
            $harvestQuery->whereYear('tanggal_panen', $now->year);
            $purchaseQuery->whereYear('tanggal_beli', $now->year);
        }

        $harvests = $harvestQuery->get();
        $purchases = $purchaseQuery->get();

        $totalPemasukan = (float) $harvests->sum(function (CropHarvest $harvest): float {
            return $harvest->numeric_harga_kotor;
        });

        $totalPengeluaran = (float) $purchases->sum('harga');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        $transaksiList = collect();

        foreach ($harvests as $h) {
            $namaTanaman = $h->crop ? $h->crop->nama_tanaman : 'Tanaman';
            $transaksiList->push([
                'id' => 'panen-'.$h->id,
                'tipe' => 'pemasukan',
                'kategori' => 'Hasil Panen',
                'judul' => 'Panen '.$namaTanaman.' (Ke-'.$h->panen_ke.')',
                'deskripsi' => ($h->total_panen ?: '-').($h->harga_panen ? ' @ '.$h->harga_panen : '').($h->catatan ? ' • '.$h->catatan : ''),
                'tanggal' => $h->tanggal_panen,
                'nominal' => $h->numeric_harga_kotor,
                'formatted_nominal' => $h->formatted_harga_kotor,
                'nota_url' => null,
            ]);
        }

        foreach ($purchases as $p) {
            $namaObat = $p->medicine ? $p->medicine->nama : 'Pembelian Obat';
            $transaksiList->push([
                'id' => 'obat-'.$p->id,
                'tipe' => 'pengeluaran',
                'kategori' => 'Pembelian Obat/Pupuk',
                'judul' => $namaObat,
                'deskripsi' => ($p->toko_obat ?: 'Toko Obat').($p->catatan ? ' • '.$p->catatan : ''),
                'tanggal' => $p->tanggal_beli,
                'nominal' => (float) $p->harga,
                'formatted_nominal' => $p->formatted_harga ?? ('Rp '.number_format((float) $p->harga, 0, ',', '.')),
                'nota_url' => $p->foto_url,
            ]);
        }

        $transaksiList = $transaksiList->sortByDesc(function (array $item): int {
            return $item['tanggal'] ? $item['tanggal']->timestamp : 0;
        })->values();

        return view('keuangan.index', [
            'periode' => $periode,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoBersih' => $saldoBersih,
            'transaksiList' => $transaksiList,
            'totalTransaksi' => $transaksiList->count(),
            'jumlahPanen' => $harvests->count(),
            'jumlahPembelian' => $purchases->count(),
        ]);
    }
}
