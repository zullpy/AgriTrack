@extends('layouts.app')

@section('title', 'Keuangan Pertanian')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Keuangan Pertanian</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Laporan Otomatis
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Rekapitulasi pendapatan hasil panen dan pengeluaran obat serta sarana produksi tani.</p>
        </div>

        {{-- Filter Periode --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500 shrink-0">Periode:</span>
            <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-gray-200">
                <a href="/keuangan?periode=semua"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $periode === 'semua' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Semua
                </a>
                <a href="/keuangan?periode=bulan_ini"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $periode === 'bulan_ini' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Bulan Ini
                </a>
                <a href="/keuangan?periode=tahun_ini"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $periode === 'tahun_ini' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Tahun Ini
                </a>
            </div>
        </div>
    </div>

    {{-- ── Metric Overview Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- 1. Total Pemasukan --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pemasukan</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-lg sm:text-xl font-extrabold text-emerald-600">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    Dari {{ $jumlahPanen }} kali pencatatan panen
                </p>
            </div>
        </div>

        {{-- 2. Total Pengeluaran --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pengeluaran</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-lg sm:text-xl font-extrabold text-rose-600">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    Dari {{ $jumlahPembelian }} pembelian obat/pupuk
                </p>
            </div>
        </div>

        {{-- 3. Saldo / Laba Bersih --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Laba / Saldo Bersih</span>
                <span class="w-8 h-8 rounded-xl {{ $saldoBersih >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }} flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-lg sm:text-xl font-extrabold {{ $saldoBersih >= 0 ? 'text-gray-900' : 'text-rose-600' }}">
                    {{ $saldoBersih >= 0 ? 'Rp ' . number_format($saldoBersih, 0, ',', '.') : '-Rp ' . number_format(abs($saldoBersih), 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    @if($saldoBersih >= 0)
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                            Surplus / Untung
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded-md">
                            Defisit Operasional
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. Total Transaksi --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Aktivitas Transaksi</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-lg sm:text-xl font-extrabold text-gray-900">
                    {{ $totalTransaksi }} <span class="text-xs font-semibold text-gray-500">Catatan</span>
                </p>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    Terhubung otomatis dari modul obat & panen
                </p>
            </div>
        </div>

    </div>

    {{-- ── Transaction History Table & Cards ── --}}
    <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Table Header / Filter Tabs --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Riwayat Transaksi Keuangan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar arus kas masuk dari hasil panen dan arus kas keluar dari pembelian obat.</p>
            </div>

            {{-- Quick Filter Buttons --}}
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                <button type="button" onclick="filterTransactions('all')" id="btn-tab-all"
                        class="tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 transition-all">
                    Semua ({{ $totalTransaksi }})
                </button>
                <button type="button" onclick="filterTransactions('pemasukan')" id="btn-tab-pemasukan"
                        class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all">
                    Pemasukan ({{ $jumlahPanen }})
                </button>
                <button type="button" onclick="filterTransactions('pengeluaran')" id="btn-tab-pengeluaran"
                        class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all">
                    Pengeluaran ({{ $jumlahPembelian }})
                </button>
            </div>
        </div>

        {{-- Content List --}}
        @if($transaksiList->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Belum Ada Catatan Transaksi</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                    Catatan pemasukan akan otomatis muncul saat Anda mencatat panen di Kalender HST, dan pengeluaran muncul saat mencatat pembelian obat di Data Obat.
                </p>
                <div class="mt-4 flex items-center justify-center gap-3">
                    <a href="/data-obat" class="px-3.5 py-1.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold transition-all">
                        Data Obat
                    </a>
                    <a href="/kalender-hst" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold transition-all">
                        Kalender HST
                    </a>
                </div>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Tipe</th>
                            <th class="px-5 py-3">Transaksi</th>
                            <th class="px-5 py-3">Keterangan / Sumber</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksiList as $t)
                            <tr class="hover:bg-gray-50 transition-colors transaction-row" data-type="{{ $t['tipe'] }}">
                                <td class="px-5 py-3.5 whitespace-nowrap text-gray-600 font-medium">
                                    {{ $t['tanggal'] ? $t['tanggal']->format('d M Y') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($t['tipe'] === 'pemasukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-bold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $t['judul'] }}</span>
                                        @if($t['nota_url'])
                                            <a href="{{ $t['nota_url'] }}" target="_blank" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                Nota
                                            </a>
                                        @endif
                                    </div>
                                    <span class="text-[11px] font-normal text-gray-400 block">{{ $t['kategori'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $t['deskripsi'] ?: '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-extrabold whitespace-nowrap {{ $t['tipe'] === 'pemasukan' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $t['tipe'] === 'pemasukan' ? '+' : '-' }}{{ $t['formatted_nominal'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="sm:hidden divide-y divide-gray-100">
                @foreach($transaksiList as $t)
                    <div class="p-4 space-y-2 transaction-row hover:bg-gray-50 transition-colors" data-type="{{ $t['tipe'] }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[11px] font-semibold text-gray-500 block">
                                    {{ $t['tanggal'] ? $t['tanggal']->format('d M Y') : '—' }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 mt-0.5">{{ $t['judul'] }}</h3>
                            </div>
                            <span class="text-sm font-extrabold shrink-0 {{ $t['tipe'] === 'pemasukan' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $t['tipe'] === 'pemasukan' ? '+' : '-' }}{{ $t['formatted_nominal'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t['tipe'] === 'pemasukan' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $t['tipe'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                            <span class="text-[11px] text-gray-600 truncate max-w-[180px]">
                                {{ $t['deskripsi'] ?: $t['kategori'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
    function filterTransactions(type) {
        const rows = document.querySelectorAll('.transaction-row');
        const buttons = document.querySelectorAll('.tab-btn');

        buttons.forEach(btn => {
            btn.classList.remove('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
            btn.classList.add('text-gray-600');
        });

        const activeBtn = document.getElementById('btn-tab-' + type);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600');
            activeBtn.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
        }

        rows.forEach(row => {
            if (type === 'all' || row.dataset.type === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
