@extends('layouts.app')

@section('title', 'Keuangan Pertanian')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-3.5 sm:space-y-6">

    {{-- ── SweetAlert Flash for Server Validation Errors ── --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errMsg = @json($errors->first());
                if (window.AgriSwal && typeof window.AgriSwal.toastError === 'function') {
                    window.AgriSwal.toastError(errMsg);
                } else if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        iconColor: '#E4574C',
                        title: errMsg,
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            });
        </script>
    @endif


    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4 pb-3 sm:pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center justify-between gap-2">
                <h1 class="text-lg sm:text-2xl font-extrabold text-gray-900 tracking-tight">Keuangan Pertanian</h1>

                {{-- Mobile Action Buttons (Right-aligned next to title on mobile) --}}
                <div class="flex items-center gap-1.5 sm:hidden">
                    <button type="button"
                            onclick="openManageCategoriesModal()"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold border border-gray-200 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Kategori</span>
                    </button>
                    <button type="button"
                            onclick="openCreateTransactionModal()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Catat</span>
                    </button>
                </div>
            </div>
            <p class="hidden sm:block text-xs sm:text-sm text-gray-500 mt-1">
                Pencatatan dan pembukuan arus kas masuk & keluar operasional tani dengan kategori & sub-kategori fleksibel.
            </p>
        </div>

        {{-- Desktop Action Buttons & Filter Periode --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2.5 sm:justify-end">
            <div class="hidden sm:flex items-center gap-2">
                {{-- Tombol Kelola Kategori & Sub-Kategori --}}
                <button type="button"
                        onclick="openManageCategoriesModal()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold border border-gray-200 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Kategori</span>
                </button>

                {{-- Tombol Tambah Transaksi --}}
                <button type="button"
                        onclick="openCreateTransactionModal()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-xs hover:shadow transition-all active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Catat Transaksi</span>
                </button>
            </div>

            {{-- Filter Periode (Segmented control) --}}
            <div class="grid grid-cols-3 sm:inline-flex rounded-xl bg-gray-100 p-1 border border-gray-200 w-full sm:w-auto">
                <button type="button"
                        onclick="setPeriodeFilter('semua')"
                        id="filter-periode-semua"
                        class="py-1 sm:py-1.5 px-2 sm:px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center {{ $periode === 'semua' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Semua
                </button>
                <button type="button"
                        onclick="setPeriodeFilter('bulan_ini')"
                        id="filter-periode-bulan_ini"
                        class="py-1 sm:py-1.5 px-2 sm:px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center {{ $periode === 'bulan_ini' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Bulan Ini
                </button>
                <button type="button"
                        onclick="setPeriodeFilter('tahun_ini')"
                        id="filter-periode-tahun_ini"
                        class="py-1 sm:py-1.5 px-2 sm:px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center {{ $periode === 'tahun_ini' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Tahun Ini
                </button>
            </div>
        </div>
    </div>

    {{-- ── Metric Overview Cards (Ringkas & 1 Baris di Mobile) ── --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-4">

        {{-- 1. Total Pemasukan --}}
        <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-2.5 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500 truncate">
                    <span class="sm:hidden">Pemasukan</span>
                    <span class="hidden sm:inline">Total Pemasukan</span>
                </span>
                <span class="w-5 h-5 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </span>
            </div>
            <div class="mt-1 sm:mt-3">
                <p id="metric-total-pemasukan" class="text-xs sm:text-xl font-black text-emerald-600 truncate tracking-tight">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
                <p id="metric-desc-pemasukan" class="text-[11px] text-gray-500 mt-0.5 hidden sm:block">
                    Dari {{ $jumlahPemasukan }} transaksi pemasukan
                </p>
            </div>
        </div>

        {{-- 2. Total Pengeluaran --}}
        <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-2.5 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500 truncate">
                    <span class="sm:hidden">Pengeluaran</span>
                    <span class="hidden sm:inline">Total Pengeluaran</span>
                </span>
                <span class="w-5 h-5 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </span>
            </div>
            <div class="mt-1 sm:mt-3">
                <p id="metric-total-pengeluaran" class="text-xs sm:text-xl font-black text-rose-600 truncate tracking-tight">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
                <p id="metric-desc-pengeluaran" class="text-[11px] text-gray-500 mt-0.5 hidden sm:block">
                    Dari {{ $jumlahPengeluaran }} transaksi pengeluaran
                </p>
            </div>
        </div>

        {{-- 3. Aktivitas Transaksi --}}
        <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-2.5 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500 truncate">
                    <span class="sm:hidden">Transaksi</span>
                    <span class="hidden sm:inline">Aktivitas Transaksi</span>
                </span>
                <span class="w-5 h-5 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="mt-1 sm:mt-3">
                <p id="metric-total-transaksi" class="text-xs sm:text-xl font-black text-gray-900 truncate tracking-tight">
                    {{ $totalTransaksi }} <span class="text-[10px] sm:text-xs font-semibold text-gray-500">Catatan</span>
                </p>
                <p id="metric-desc-transaksi" class="text-[11px] text-gray-500 mt-0.5 hidden sm:block">
                    {{ $jumlahPemasukan }} pemasukan • {{ $jumlahPengeluaran }} pengeluaran
                </p>
            </div>
        </div>

    </div>

    {{-- ── Transaction History Table & Cards ── --}}
    <div class="bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        {{-- Table Header / Filter Tabs --}}
        <div class="p-3.5 sm:p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-gray-900">Riwayat Catatan Transaksi</h2>
                <p class="hidden sm:block text-xs text-gray-500 mt-0.5">Daftar seluruh arus kas masuk dan pengeluaran yang telah dicatat.</p>
            </div>

            {{-- Quick Filter Buttons --}}
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                <button type="button" onclick="filterTransactions('all')" id="btn-tab-all"
                        class="tab-btn px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 transition-all cursor-pointer whitespace-nowrap">
                    Semua ({{ $totalTransaksi }})
                </button>
                <button type="button" onclick="filterTransactions('pemasukan')" id="btn-tab-pemasukan"
                        class="tab-btn px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all cursor-pointer whitespace-nowrap">
                    Pemasukan ({{ $jumlahPemasukan }})
                </button>
                <button type="button" onclick="filterTransactions('pengeluaran')" id="btn-tab-pengeluaran"
                        class="tab-btn px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all cursor-pointer whitespace-nowrap">
                    Pengeluaran ({{ $jumlahPengeluaran }})
                </button>
            </div>
        </div>

        {{-- Content List Container --}}
        <div id="transaksi-empty-state" class="p-12 text-center {{ $transaksiList->isEmpty() ? '' : 'hidden' }}">
            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-800">Belum Ada Catatan Transaksi</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                Mulai kelola keuangan usaha tani Anda dengan mencatat pemasukan atau pengeluaran pertama.
            </p>
            <div class="mt-4 flex items-center justify-center gap-2">
                <button type="button" onclick="openCreateTransactionModal()" class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold transition-all shadow-xs cursor-pointer">
                    + Catat Transaksi Baru
                </button>
                <button type="button" onclick="openManageCategoriesModal()" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-semibold transition-all cursor-pointer">
                    Kelola Kategori
                </button>
            </div>
        </div>

        <div id="transaksi-content-wrapper" class="{{ $transaksiList->isEmpty() ? 'hidden' : '' }}">
            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Tipe</th>
                            <th class="px-5 py-3">Transaksi</th>
                            <th class="px-5 py-3">Sub-Kategori</th>
                            <th class="px-5 py-3">Keterangan / Terkait</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transaksi-table-body" class="divide-y divide-gray-100">
                        @php
                            $catOrderMap = [];
                            $subOrderMap = [];
                            if (isset($categories)) {
                                foreach ($categories as $cIdx => $catModel) {
                                    $catOrderMap[$catModel->nama] = $catModel->urutan ?? ($cIdx + 1);
                                    $subOrderMap[$catModel->nama] = [];
                                    if ($catModel->subcategories) {
                                        foreach ($catModel->subcategories as $sIdx => $subModel) {
                                            $subOrderMap[$catModel->nama][strtolower(trim($subModel->nama))] = $subModel->urutan ?? ($sIdx + 1);
                                        }
                                    }
                                }
                            }

                            $groupedTransaksi = $transaksiList->groupBy(function($item) {
                                return $item['kategori'] ?: 'Lainnya';
                            })->sortBy(function($items, $catName) use ($catOrderMap) {
                                return $catOrderMap[$catName] ?? 9999;
                            });
                        @endphp
                        @foreach($groupedTransaksi as $catName => $items)
                            @php
                                $catPengeluaran = $items->where('tipe', 'pengeluaran')->sum('nominal');
                                $catPemasukan = $items->where('tipe', 'pemasukan')->sum('nominal');

                                $subGroups = $items->groupBy(function($item) {
                                    $raw = trim($item['sub_kategori'] ?? '');
                                    if (!$raw) return 'Umum';
                                    $p = explode(' › ', $raw);
                                    return trim($p[0]) ?: 'Umum';
                                })->sortBy(function($subItems, $subName) use ($subOrderMap, $catName) {
                                    // Finishing selalu diletakkan paling bawah di sub-kategori
                                    if (stripos($subName, 'finishing') !== false) {
                                        return 999999;
                                    }
                                    $subKey = strtolower(trim($subName));
                                    return $subOrderMap[$catName][$subKey] ?? 1000;
                                });
                            @endphp
                            {{-- Header Kategori Utama (Kontras Tinggi & Pemisah Jelas) --}}
                            <tr class="category-header-row bg-emerald-900 text-white border-t-2 border-emerald-950" data-category="{{ $catName }}">
                                <td colspan="7" class="px-5 py-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-300/40"></span>
                                            <span class="font-extrabold text-white text-xs sm:text-sm uppercase tracking-wider">{{ $catName }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-emerald-800 text-emerald-200">({{ $items->count() }} transaksi)</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs font-semibold">
                                            @if($catPengeluaran > 0)
                                                <span class="text-rose-300">Pengeluaran: -Rp {{ number_format($catPengeluaran, 0, ',', '.') }}</span>
                                            @endif
                                            @if($catPengeluaran > 0 && $catPemasukan > 0)
                                                <span class="text-emerald-600">|</span>
                                            @endif
                                            @if($catPemasukan > 0)
                                                <span class="text-emerald-300">Pemasukan: +Rp {{ number_format($catPemasukan, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{-- Header Sub-Kategori & Baris Transaksi --}}
                            @foreach($subGroups as $subName => $subItems)
                                @php
                                    $subPengeluaran = $subItems->where('tipe', 'pengeluaran')->sum('nominal');
                                    $subPemasukan = $subItems->where('tipe', 'pemasukan')->sum('nominal');
                                @endphp
                                <tr class="subcategory-header-row bg-slate-100/90 border-y border-slate-200/90" data-category="{{ $catName }}" data-subcategory="{{ $subName }}">
                                    <td colspan="7" class="pl-8 pr-5 py-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-400 font-bold text-xs select-none">↳</span>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-sky-100/90 text-sky-900 border border-sky-200">
                                                    <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                    {{ $subName }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 font-medium">({{ $subItems->count() }} transaksi)</span>
                                            </div>
                                            <div class="flex items-center gap-2.5 text-xs font-semibold">
                                                @if($subPengeluaran > 0)
                                                    <span class="text-rose-600">Subtotal: -Rp {{ number_format($subPengeluaran, 0, ',', '.') }}</span>
                                                @endif
                                                @if($subPengeluaran > 0 && $subPemasukan > 0)
                                                    <span class="text-slate-300">|</span>
                                                @endif
                                                @if($subPemasukan > 0)
                                                    <span class="text-emerald-700">Subtotal: +Rp {{ number_format($subPemasukan, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($subItems as $t)
                                <tr class="hover:bg-gray-50 transition-colors transaction-row" data-type="{{ $t['tipe'] }}" data-category="{{ $catName }}" data-subcategory="{{ $subName }}">
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
                                                <button type="button"
                                                        onclick="previewNota('{{ $t['nota_url'] }}', '{{ addslashes($t['judul']) }}')"
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    Nota
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $subParts = !empty($t['sub_kategori']) ? explode(' › ', $t['sub_kategori']) : [];
                                            $leafSubName = count($subParts) > 1 ? end($subParts) : (!empty($subParts) ? $subParts[0] : null);
                                        @endphp
                                        @if($leafSubName)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/90 shadow-2xs" title="{{ $t['sub_kategori'] }}">
                                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                {{ $leafSubName }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 max-w-xs break-words">
                                        {{ $t['deskripsi'] ?: '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-extrabold whitespace-nowrap {{ $t['tipe'] === 'pemasukan' ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $t['tipe'] === 'pemasukan' ? '+' : '-' }}{{ $t['formatted_nominal'] }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    onclick='openEditTransactionModal(@json($t))'
                                                    class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                                    title="Edit Transaksi">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="deleteTransaction({{ $t['raw_id'] }}, '{{ addslashes($t['judul']) }}')"
                                                    class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Transaksi">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div id="transaksi-mobile-body" class="sm:hidden divide-y divide-gray-100">
                @foreach($groupedTransaksi as $catName => $items)
                    @php
                        $catPengeluaran = $items->where('tipe', 'pengeluaran')->sum('nominal');
                        $catPemasukan = $items->where('tipe', 'pemasukan')->sum('nominal');

                        $subGroups = $items->groupBy(function($item) {
                            $raw = trim($item['sub_kategori'] ?? '');
                            if (!$raw) return 'Umum';
                            $p = explode(' › ', $raw);
                            return trim($p[0]) ?: 'Umum';
                        })->sortBy(function($subItems, $subName) use ($subOrderMap, $catName) {
                            // Finishing selalu diletakkan paling bawah di sub-kategori
                            if (stripos($subName, 'finishing') !== false) {
                                return 999999;
                            }
                            $subKey = strtolower(trim($subName));
                            return $subOrderMap[$catName][$subKey] ?? 1000;
                        });
                    @endphp
                    <div class="category-mobile-group border-b-2 border-emerald-950/20 last:border-b-0" data-category="{{ $catName }}">
                        <div class="px-3.5 py-2.5 bg-emerald-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-300/40"></span>
                                <span class="font-extrabold text-xs uppercase tracking-wide text-white">{{ $catName }}</span>
                                <span class="text-[10px] text-emerald-200 bg-emerald-800 px-1.5 py-0.2 rounded font-semibold">({{ $items->count() }})</span>
                            </div>
                            <div class="text-[11px] font-bold text-right">
                                @if($catPengeluaran > 0)
                                    <span class="text-rose-300 block">-Rp {{ number_format($catPengeluaran, 0, ',', '.') }}</span>
                                @endif
                                @if($catPemasukan > 0)
                                    <span class="text-emerald-300 block">+Rp {{ number_format($catPemasukan, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                        @foreach($subGroups as $subName => $subItems)
                            @php
                                $subPengeluaran = $subItems->where('tipe', 'pengeluaran')->sum('nominal');
                                $subPemasukan = $subItems->where('tipe', 'pemasukan')->sum('nominal');
                            @endphp
                            <div class="subcategory-mobile-group border-b border-gray-200/80 last:border-b-0" data-category="{{ $catName }}" data-subcategory="{{ $subName }}">
                                <div class="px-3 py-1.5 bg-slate-100 border-b border-slate-200/80 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400 text-xs">↳</span>
                                        <span class="font-bold text-sky-900 bg-sky-100 px-2 py-0.5 rounded text-[11px] border border-sky-200 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                            {{ $subName }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 font-medium">({{ $subItems->count() }})</span>
                                    </div>
                                    <div class="text-[10.5px] font-semibold text-right">
                                        @if($subPengeluaran > 0)
                                            <span class="text-rose-600">-Rp {{ number_format($subPengeluaran, 0, ',', '.') }}</span>
                                        @endif
                                        @if($subPemasukan > 0)
                                            <span class="text-emerald-700">+Rp {{ number_format($subPemasukan, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach($subItems as $t)
                                        @php
                                            $subParts = !empty($t['sub_kategori']) ? explode(' › ', $t['sub_kategori']) : [];
                                            $leafSubName = count($subParts) > 1 ? end($subParts) : (!empty($subParts) ? $subParts[0] : null);
                                        @endphp
                                        <div class="p-3 sm:p-4 space-y-1.5 sm:space-y-2 transaction-row hover:bg-gray-50 transition-colors" data-type="{{ $t['tipe'] }}" data-category="{{ $catName }}" data-subcategory="{{ $subName }}">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <span class="text-[11px] font-semibold text-gray-500">
                                                        {{ $t['tanggal'] ? $t['tanggal']->format('d M Y') : '—' }}
                                                    </span>
                                                    <h3 class="text-sm font-bold text-gray-900 mt-0.5">{{ $t['judul'] }}</h3>
                                                </div>
                                                <span class="text-sm font-extrabold shrink-0 {{ $t['tipe'] === 'pemasukan' ? 'text-emerald-700' : 'text-rose-600' }}">
                                                    {{ $t['tipe'] === 'pemasukan' ? '+' : '-' }}{{ $t['formatted_nominal'] }}
                                                </span>
                                            </div>
                                            <div class="space-y-1 pt-0.5">
                                                <div class="flex items-center gap-1.5 flex-wrap text-xs">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t['tipe'] === 'pemasukan' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                        {{ $t['tipe'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                                                    </span>
                                                    @if($leafSubName)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                                            <svg class="w-2.5 h-2.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                            {{ $leafSubName }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($t['deskripsi'] && $t['deskripsi'] !== '—')
                                                    <p class="text-xs text-gray-500">{{ $t['deskripsi'] }}</p>
                                                @endif
                                            </div>

                                            {{-- Mobile Action Row --}}
                                            <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100">
                                                @if($t['nota_url'])
                                                    <button type="button"
                                                            onclick="previewNota('{{ $t['nota_url'] }}', '{{ addslashes($t['judul']) }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                        Nota
                                                    </button>
                                                @endif

                                                <button type="button"
                                                        onclick='openEditTransactionModal(@json($t))'
                                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                        onclick="deleteTransaction({{ $t['raw_id'] }}, '{{ addslashes($t['judul']) }}')"
                                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1: CATAT TRANSAKSI BARU (CREATE)                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-create-transaksi" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto hidden">
    <div class="bg-surface w-full max-w-lg rounded-2xl shadow-xl border border-gray-200 overflow-hidden my-auto flex flex-col animate-scale-up"
         style="max-height: calc(100dvh - 32px); max-height: calc(100vh - 32px); height: auto; display: flex; flex-direction: column;">
        {{-- Modal Header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/70 shrink-0" style="flex-shrink: 0;">
            <div>
                <h3 class="text-base font-bold text-gray-900">Catat Transaksi Keuangan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Input transaksi dengan kategori & sub-kategori operasional tani.</p>
            </div>
            <button type="button" onclick="closeModal('modal-create-transaksi')" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="form-create-transaksi" method="POST" action="/keuangan" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden" style="flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
            @csrf

            {{-- Scrollable Form Body --}}
            <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0" style="flex: 1 1 auto; min-height: 0; overflow-y: auto;">

            {{-- 1. Pilihan Tipe: Pemasukan / Pengeluaran --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tipe Transaksi *</label>
                <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl border border-gray-200">
                    <button type="button"
                            id="create-type-pengeluaran"
                            onclick="setCreateType('pengeluaran')"
                            class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-rose-700 shadow-xs border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Pengeluaran
                    </button>
                    <button type="button"
                            id="create-type-pemasukan"
                            onclick="setCreateType('pemasukan')"
                            class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Pemasukan
                    </button>
                </div>
                <input type="hidden" name="tipe" id="create-input-tipe" value="pengeluaran">
            </div>

            {{-- 2. Kategori & Sub-Kategori --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-200">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="create-select-kategori" class="block text-xs font-bold text-gray-700">
                            Kategori Utama *
                        </label>
                        <button type="button" onclick="openManageCategoriesModal()" class="text-[11px] font-bold text-emerald-700 hover:underline">
                            + Kelola Kategori
                        </button>
                    </div>
                    <select id="create-select-kategori"
                            name="kategori"
                            required
                            onchange="handleCategorySelection('create')"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="">— Pilih Kategori —</option>
                    </select>
                </div>

                {{-- Sub-Kategori Dropdown (Dinamis) --}}
                <div id="create-sub-kategori-container" class="hidden">
                    <label for="create-select-sub-kategori" class="block text-xs font-semibold text-gray-700 mb-1">
                        Sub-Kategori / Sub-Sub <span class="text-gray-400 font-normal">(Pilih atau isi sesuai kegiatan)</span>
                    </label>
                    <div class="space-y-2">
                        <select id="create-select-sub-kategori"
                                onchange="handleSubCategorySelection('create')"
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">— Tanpa Sub-Kategori —</option>
                        </select>
                        <input type="text"
                               id="create-input-sub-kategori"
                               name="sub_kategori"
                               placeholder="Atau ketik sub-kategori khusus..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                </div>
            </div>

            {{-- 3. Judul Transaksi --}}
            <div>
                <label for="create-judul" class="block text-xs font-semibold text-gray-700 mb-1">
                    Judul Transaksi *
                </label>
                <input type="text"
                       id="create-judul"
                       name="judul"
                       required
                       placeholder="Contoh: Pupuk Organik Kandang 50 Karung / HOK Cangkul 3 Orang"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- 4. Nominal & Tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create-nominal" class="block text-xs font-semibold text-gray-700 mb-1">
                        Nominal (Rp) *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-sm font-bold text-gray-400">Rp</span>
                        <input type="number"
                               id="create-nominal"
                               name="nominal"
                               required
                               min="1"
                               step="1"
                               placeholder="50000"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label for="create-tanggal" class="block text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Transaksi *
                    </label>
                    <input type="date"
                           id="create-tanggal"
                           name="tanggal"
                           required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            {{-- 5. Tanaman Terkait (Opsional) --}}
            <div>
                <label for="create-crop-id" class="block text-xs font-semibold text-gray-700 mb-1">
                    Hubungkan ke Tanaman <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <select id="create-crop-id"
                        name="crop_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">— Umum / Operasional Kebun Keseluruhan —</option>
                    @foreach($crops as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->nama_tanaman }} {{ $c->varietas ? '('.$c->varietas.')' : '' }} [{{ $c->status }}]
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Catatan / Keterangan --}}
            <div>
                <label for="create-keterangan" class="block text-xs font-semibold text-gray-700 mb-1">
                    Catatan / Rincian Tambahan <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <textarea id="create-keterangan"
                          name="keterangan"
                          rows="2"
                          placeholder="Jumlah HOK, nama pekerja, toko, atau kebutuhan..."
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
            </div>

            {{-- 7. Foto Nota / Kuitansi (Opsional) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Foto Bukti / Nota <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <label for="create-foto-nota" class="flex items-center gap-3 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl cursor-pointer bg-gray-50/50 transition-colors">
                    <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p id="create-foto-label" class="text-xs font-semibold text-gray-700">Pilih foto nota / kuitansi...</p>
                        <p class="text-[10px] text-gray-400">JPG, PNG, atau WebP (Maks. 10MB)</p>
                    </div>
                    <input type="file"
                           id="create-foto-nota"
                           name="foto_nota"
                           accept="image/*"
                           onchange="handleFileInputChange(this, 'create-foto-label')"
                           class="hidden">
                </label>
            </div>

            </div>

            {{-- Modal Buttons (Tetap di bawah / Fixed) --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 bg-gray-50/90 shrink-0" style="flex-shrink: 0;">
                <button type="button"
                        onclick="closeModal('modal-create-transaksi')"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all cursor-pointer">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2: EDIT TRANSAKSI                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-transaksi" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto hidden">
    <div class="bg-surface w-full max-w-lg rounded-2xl shadow-xl border border-gray-200 overflow-hidden my-auto flex flex-col animate-scale-up"
         style="max-height: calc(100dvh - 32px); max-height: calc(100vh - 32px); height: auto; display: flex; flex-direction: column;">
        {{-- Modal Header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/70 shrink-0" style="flex-shrink: 0;">
            <div>
                <h3 class="text-base font-bold text-gray-900">Edit Transaksi Keuangan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui rincian transaksi, kategori, atau sub-kategori.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-transaksi')" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="form-edit-transaksi" method="POST" action="" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden" style="flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
            @csrf
            @method('PUT')

            {{-- Scrollable Form Body --}}
            <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0" style="flex: 1 1 auto; min-height: 0; overflow-y: auto;">

            {{-- 1. Pilihan Tipe --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tipe Transaksi *</label>
                <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl border border-gray-200">
                    <button type="button"
                            id="edit-type-pengeluaran"
                            onclick="setEditType('pengeluaran')"
                            class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Pengeluaran
                    </button>
                    <button type="button"
                            id="edit-type-pemasukan"
                            onclick="setEditType('pemasukan')"
                            class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Pemasukan
                    </button>
                </div>
                <input type="hidden" name="tipe" id="edit-input-tipe" value="pengeluaran">
            </div>

            {{-- 2. Kategori & Sub-Kategori --}}
            <div class="space-y-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-200">
                <div>
                    <label for="edit-select-kategori" class="block text-xs font-bold text-gray-700 mb-1">
                        Kategori Utama *
                    </label>
                    <select id="edit-select-kategori"
                            name="kategori"
                            required
                            onchange="handleCategorySelection('edit')"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="">— Pilih Kategori —</option>
                    </select>
                </div>

                {{-- Sub-Kategori Dropdown (Dinamis) --}}
                <div id="edit-sub-kategori-container" class="hidden">
                    <label for="edit-select-sub-kategori" class="block text-xs font-semibold text-gray-700 mb-1">
                        Sub-Kategori / Sub-Sub <span class="text-gray-400 font-normal">(Pilih atau isi sesuai kegiatan)</span>
                    </label>
                    <div class="space-y-2">
                        <select id="edit-select-sub-kategori"
                                onchange="handleSubCategorySelection('edit')"
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">— Tanpa Sub-Kategori —</option>
                        </select>
                        <input type="text"
                               id="edit-input-sub-kategori"
                               name="sub_kategori"
                               placeholder="Atau ketik sub-kategori khusus..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                </div>
            </div>

            {{-- 3. Judul Transaksi --}}
            <div>
                <label for="edit-judul" class="block text-xs font-semibold text-gray-700 mb-1">
                    Judul Transaksi *
                </label>
                <input type="text"
                       id="edit-judul"
                       name="judul"
                       required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- 4. Nominal & Tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit-nominal" class="block text-xs font-semibold text-gray-700 mb-1">
                        Nominal (Rp) *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-sm font-bold text-gray-400">Rp</span>
                        <input type="number"
                               id="edit-nominal"
                               name="nominal"
                               required
                               min="1"
                               step="1"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label for="edit-tanggal" class="block text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Transaksi *
                    </label>
                    <input type="date"
                           id="edit-tanggal"
                           name="tanggal"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            {{-- 5. Tanaman Terkait --}}
            <div>
                <label for="edit-crop-id" class="block text-xs font-semibold text-gray-700 mb-1">
                    Hubungkan ke Tanaman <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <select id="edit-crop-id"
                        name="crop_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">— Umum / Operasional Kebun Keseluruhan —</option>
                    @foreach($crops as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->nama_tanaman }} {{ $c->varietas ? '('.$c->varietas.')' : '' }} [{{ $c->status }}]
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Catatan / Keterangan --}}
            <div>
                <label for="edit-keterangan" class="block text-xs font-semibold text-gray-700 mb-1">
                    Catatan / Rincian Tambahan <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <textarea id="edit-keterangan"
                          name="keterangan"
                          rows="2"
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
            </div>

            {{-- 7. Ganti Foto Nota (Opsional) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Ganti Foto Nota <span class="text-gray-400 font-normal">(Biarkan kosong jika tidak diubah)</span>
                </label>
                <label for="edit-foto-nota" class="flex items-center gap-3 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl cursor-pointer bg-gray-50/50 transition-colors">
                    <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p id="edit-foto-label" class="text-xs font-semibold text-gray-700">Pilih file foto baru...</p>
                        <p class="text-[10px] text-gray-400">JPG, PNG, atau WebP (Maks. 10MB)</p>
                    </div>
                    <input type="file"
                           id="edit-foto-nota"
                           name="foto_nota"
                           accept="image/*"
                           onchange="handleFileInputChange(this, 'edit-foto-label')"
                           class="hidden">
                </label>
            </div>

            </div>

            {{-- Modal Buttons (Tetap di bawah / Fixed) --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 bg-gray-50/90 shrink-0" style="flex-shrink: 0;">
                <button type="button"
                        onclick="closeModal('modal-edit-transaksi')"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 3: KELOLA KATEGORI & SUB-KATEGORI                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-manage-kategori" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto hidden">
    <div class="bg-surface w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col my-auto animate-scale-up"
         style="max-height: calc(100dvh - 32px); max-height: calc(100vh - 32px); height: auto; display: flex; flex-direction: column;">
        {{-- Header (Tetap di atas / Fixed) --}}
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-gray-50/95 shrink-0" style="flex-shrink: 0;">
            <div>
                <h3 class="text-base font-bold text-gray-900">Kelola Kategori & Sub-Kategori</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tambah atau perbarui struktur kategori pengeluaran dan pemasukan.</p>
            </div>
            <button type="button" onclick="closeModal('modal-manage-kategori')" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body: Form Tambah Kategori Baru & List Kategori (Scrollable dengan min-h-0) --}}
        <div class="p-4 sm:p-5 overflow-y-auto flex-1 min-h-0 space-y-4 sm:space-y-5" style="flex: 1 1 auto; min-height: 0; overflow-y: auto;">

            {{-- Form Tambah Kategori / Sub-Kategori Baru --}}
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 space-y-3">
                <h4 id="form-cat-heading" class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah Kategori / Sub-Kategori Baru</span>
                </h4>

                <form id="form-manage-category" method="POST" action="/keuangan/kategori" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_method" id="cat-method-override" value="POST">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="cat-parent-id" class="block text-xs font-semibold text-gray-700 mb-1">
                                Induk Kategori (Kosongkan jika Kategori Utama)
                            </label>
                            <select id="cat-parent-id" name="parent_id" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:ring-2 focus:ring-emerald-500">
                                <option value="">— Ini Kategori Utama (Parent) —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">📁 {{ $cat->nama }} ({{ ucfirst($cat->tipe) }})</option>
                                    @foreach($cat->subcategories as $sub)
                                        <option value="{{ $sub->id }}">&nbsp;&nbsp;↳ 📂 [Sub] {{ $sub->nama }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cat-tipe" class="block text-xs font-semibold text-gray-700 mb-1">
                                Berlaku Untuk *
                            </label>
                            <select id="cat-tipe" name="tipe" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:ring-2 focus:ring-emerald-500">
                                <option value="pengeluaran">Pengeluaran (Biaya)</option>
                                <option value="pemasukan">Pemasukan (Uang Masuk)</option>
                                <option value="keduanya">Keduanya (Pengeluaran & Pemasukan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="cat-nama" class="block text-xs font-semibold text-gray-700 mb-1">
                            Nama Kategori / Sub-Kategori *
                        </label>
                        <input type="text"
                               id="cat-nama"
                               name="nama"
                               required
                               placeholder="Contoh: Panen, Olah Lahan Garapan, atau BOP Panen"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <button type="button" id="cat-btn-cancel-edit" onclick="resetCategoryForm()" class="text-xs text-gray-500 hover:text-gray-700 hidden">
                            Batal Edit
                        </button>
                        <button type="submit" id="cat-btn-submit" class="ml-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar Kategori dan Sub-Kategori --}}
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Daftar Struktur Kategori Saat Ini</h4>

                <div id="category-tree-container" class="space-y-2.5">
                    @foreach($categories as $cat)
                        <div class="border border-gray-200 rounded-xl p-3 bg-white hover:border-emerald-300 transition-colors">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-gray-900 text-sm">{{ $cat->nama }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->tipe === 'pemasukan' ? 'bg-emerald-100 text-emerald-800' : ($cat->tipe === 'pengeluaran' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($cat->tipe) }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        Kategori Utama
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            onclick="prepareAddSubCategory({{ $cat->id }}, '{{ addslashes($cat->nama) }}', '{{ $cat->tipe }}')"
                                            class="px-2 py-1 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 cursor-pointer">
                                        + Sub
                                    </button>
                                    <button type="button"
                                            onclick="prepareEditCategory({{ $cat->id }}, '{{ addslashes($cat->nama) }}', '{{ $cat->tipe }}', null)"
                                            class="p-1 rounded text-gray-500 hover:text-blue-600 hover:bg-blue-50 cursor-pointer"
                                            title="Edit Kategori">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button type="button"
                                            onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->nama) }}', false)"
                                            class="p-1 rounded text-gray-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer"
                                            title="Hapus Kategori">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Sub-Kategori List --}}
                            @if($cat->subcategories->isNotEmpty())
                                <div class="mt-3 pt-2.5 border-t border-gray-100 pl-2 sm:pl-3 space-y-2">
                                    @foreach($cat->subcategories as $sub)
                                        <div class="p-2 rounded-lg bg-sky-50/70 border border-sky-200/80 hover:bg-sky-50 transition-colors">
                                            <div class="flex items-center justify-between text-xs gap-2">
                                                <span class="text-sky-950 font-semibold flex items-center gap-1.5 flex-wrap">
                                                    <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                    </svg>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300 shrink-0">Sub</span>
                                                    <span>{{ $sub->nama }}</span>
                                                </span>
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button"
                                                            onclick="prepareAddSubCategory({{ $sub->id }}, '{{ addslashes($sub->nama) }}', '{{ $sub->tipe }}')"
                                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 cursor-pointer border border-emerald-300"
                                                            title="Tambah Sub-Sub">+ Sub-Sub</button>
                                                    <button type="button"
                                                            onclick="prepareEditCategory({{ $sub->id }}, '{{ addslashes($sub->nama) }}', '{{ $sub->tipe }}', {{ $cat->id }})"
                                                            class="p-1 rounded text-sky-700 hover:text-blue-700 hover:bg-sky-100"
                                                            title="Edit Sub-Kategori">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </button>
                                                    <button type="button"
                                                            onclick="deleteCategory({{ $sub->id }}, '{{ addslashes($sub->nama) }}', true)"
                                                            class="p-1 rounded text-sky-700 hover:text-rose-700 hover:bg-rose-50"
                                                            title="Hapus Sub-Kategori">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Sub-Sub-Kategori --}}
                                            @if($sub->subcategories->isNotEmpty())
                                                <div class="mt-2 pl-3 ml-2 border-l-2 border-amber-300 space-y-1.5">
                                                    @foreach($sub->subcategories as $subSub)
                                                        <div class="flex items-center justify-between text-[11px] py-1 px-2 rounded-md bg-amber-50/80 border border-amber-200/80 hover:bg-amber-100/60 transition-colors">
                                                            <span class="text-amber-950 font-medium flex items-center gap-1.5 flex-wrap">
                                                                <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                                </svg>
                                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300 shrink-0">Sub-Sub</span>
                                                                <span>{{ $subSub->nama }}</span>
                                                            </span>
                                                            <div class="flex items-center gap-1 shrink-0">
                                                                <button type="button"
                                                                        onclick="prepareEditCategory({{ $subSub->id }}, '{{ addslashes($subSub->nama) }}', '{{ $subSub->tipe }}', {{ $sub->id }})"
                                                                        class="p-0.5 rounded text-amber-700 hover:text-blue-700 hover:bg-amber-100"
                                                                        title="Edit Sub-Sub">
                                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                </button>
                                                                <button type="button"
                                                                        onclick="deleteCategory({{ $subSub->id }}, '{{ addslashes($subSub->nama) }}', true)"
                                                                        class="p-0.5 rounded text-amber-700 hover:text-rose-700 hover:bg-rose-50"
                                                                        title="Hapus Sub-Sub">
                                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer (Tetap di bawah / Fixed) --}}
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-end shrink-0" style="flex-shrink: 0;">
            <button type="button" onclick="closeModal('modal-manage-kategori')" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold cursor-pointer">
                Selesai
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 4: PREVIEW BUKTI / NOTA                                              --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-preview-nota" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="relative bg-surface rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 id="preview-nota-title" class="text-sm font-bold text-gray-900 truncate">Bukti Nota Transaksi</h3>
            <button type="button" onclick="closeModal('modal-preview-nota')" class="p-1 rounded-lg text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 flex items-center justify-center bg-gray-900/5 max-h-[70vh] overflow-auto">
            <img id="preview-nota-img" src="" alt="Bukti Nota" class="max-h-[65vh] max-w-full rounded-lg object-contain shadow-xs">
        </div>
        <div class="p-3 border-t border-gray-200 flex items-center justify-between">
            <a id="preview-nota-link" href="" target="_blank" class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                Buka di Tab Baru
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <button type="button" onclick="closeModal('modal-preview-nota')" class="px-3.5 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // State Aplikasi Keuangan
    let appCategories = @json($categories);
    let currentTransaksiList = @json($transaksiList);
    let currentPeriode = '{{ $periode }}';
    let currentFilterType = 'all';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Filter Transaksi List Tab (Semua / Pemasukan / Pengeluaran)
    function filterTransactions(type) {
        currentFilterType = type;
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

        // Filter individual rows
        rows.forEach(row => {
            if (type === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.dataset.type === type ? '' : 'none';
            }
        });

        // Hide/show subcategory header rows in desktop table
        const subHeaderRows = document.querySelectorAll('.subcategory-header-row');
        subHeaderRows.forEach(header => {
            const catName = header.dataset.category;
            const subName = header.dataset.subcategory;
            const matchingRows = Array.from(document.querySelectorAll(`.transaction-row[data-category="${catName}"][data-subcategory="${subName}"]`))
                .filter(r => r.style.display !== 'none');
            header.style.display = matchingRows.length > 0 ? '' : 'none';
        });

        // Hide/show category header rows in desktop table
        const catHeaderRows = document.querySelectorAll('.category-header-row');
        catHeaderRows.forEach(header => {
            const catName = header.dataset.category;
            const matchingRows = Array.from(document.querySelectorAll(`.transaction-row[data-category="${catName}"]`))
                .filter(r => r.style.display !== 'none');
            header.style.display = matchingRows.length > 0 ? '' : 'none';
        });

        // Hide/show mobile subcategory groups
        const subMobileGroups = document.querySelectorAll('.subcategory-mobile-group');
        subMobileGroups.forEach(group => {
            const matchingRows = Array.from(group.querySelectorAll('.transaction-row'))
                .filter(r => r.style.display !== 'none');
            group.style.display = matchingRows.length > 0 ? '' : 'none';
        });

        // Hide/show mobile category groups
        const catMobileGroups = document.querySelectorAll('.category-mobile-group');
        catMobileGroups.forEach(group => {
            const matchingRows = Array.from(group.querySelectorAll('.transaction-row'))
                .filter(r => r.style.display !== 'none');
            group.style.display = matchingRows.length > 0 ? '' : 'none';
        });
    }

    // Mengisi dropdown kategori berdasarkan tipe transaksi (pengeluaran / pemasukan)
    function populateCategoryDropdown(prefix, selectedType, selectedCatName = null, selectedSubCatName = null) {
        const catSelect = document.getElementById(`${prefix}-select-kategori`);
        if (!catSelect) return;

        catSelect.innerHTML = '<option value="">— Pilih Kategori —</option>';

        const filtered = appCategories.filter(c => c.tipe === selectedType || c.tipe === 'keduanya');

        filtered.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.nama;
            opt.textContent = cat.nama;
            if (selectedCatName && cat.nama === selectedCatName) {
                opt.selected = true;
            }
            catSelect.appendChild(opt);
        });

        handleCategorySelection(prefix, selectedSubCatName);
    }

    function handleCategorySelection(prefix, presetSubCatName = null) {
        const catSelect = document.getElementById(`${prefix}-select-kategori`);
        const subContainer = document.getElementById(`${prefix}-sub-kategori-container`);
        const subSelect = document.getElementById(`${prefix}-select-sub-kategori`);
        const subInput = document.getElementById(`${prefix}-input-sub-kategori`);

        if (!catSelect || !subContainer || !subSelect) return;

        const selectedCatName = catSelect.value;
        const currentCat = appCategories.find(c => c.nama === selectedCatName);

        if (currentCat && currentCat.subcategories && currentCat.subcategories.length > 0) {
            subContainer.classList.remove('hidden');
            subSelect.innerHTML = '<option value="">— Pilih Sub-Kategori —</option>';

            currentCat.subcategories.forEach(sub => {
                const subSubs = sub.subcategories || [];
                if (subSubs.length > 0) {
                    // Sub-kategori punya anak → pakai optgroup
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = `📂 Sub: ${sub.nama}`;
                    subSubs.forEach(subSub => {
                        const opt = document.createElement('option');
                        opt.value = sub.nama + ' › ' + subSub.nama;
                        opt.textContent = `↳ 🏷️ [Sub-Sub] ${subSub.nama}`;
                        if (presetSubCatName && opt.value === presetSubCatName) {
                            opt.selected = true;
                        }
                        optgroup.appendChild(opt);
                    });
                    subSelect.appendChild(optgroup);
                } else {
                    // Sub-kategori tanpa anak → option biasa
                    const opt = document.createElement('option');
                    opt.value = sub.nama;
                    opt.textContent = `📂 [Sub] ${sub.nama}`;
                    if (presetSubCatName && sub.nama === presetSubCatName) {
                        opt.selected = true;
                    }
                    subSelect.appendChild(opt);
                }
            });

            subInput.value = presetSubCatName || '';
        } else if (selectedCatName) {
            subContainer.classList.remove('hidden');
            subSelect.innerHTML = '<option value="">— Kategori ini tidak memiliki sub bawaan —</option>';
            subInput.value = presetSubCatName || '';
        } else {
            subContainer.classList.add('hidden');
            subSelect.innerHTML = '<option value="">— Tanpa Sub-Kategori —</option>';
            subInput.value = '';
        }
    }

    function handleSubCategorySelection(prefix) {
        const subSelect = document.getElementById(`${prefix}-select-sub-kategori`);
        const subInput = document.getElementById(`${prefix}-input-sub-kategori`);
        if (subSelect && subInput) {
            if (subSelect.value) {
                subInput.value = subSelect.value;
            }
        }
    }

    // Modal Create Transaksi
    function openCreateTransactionModal() {
        setCreateType('pengeluaran');
        document.getElementById('create-judul').value = '';
        document.getElementById('create-nominal').value = '';
        document.getElementById('create-crop-id').value = '';
        document.getElementById('create-keterangan').value = '';
        document.getElementById('create-foto-nota').value = '';
        document.getElementById('create-foto-label').innerText = 'Pilih foto nota / kuitansi...';
        openModal('modal-create-transaksi');
    }

    function setCreateType(type) {
        document.getElementById('create-input-tipe').value = type;
        const btnPengeluaran = document.getElementById('create-type-pengeluaran');
        const btnPemasukan = document.getElementById('create-type-pemasukan');

        if (type === 'pengeluaran') {
            btnPengeluaran.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-rose-700 shadow-xs border border-rose-200';
            btnPemasukan.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
        } else {
            btnPemasukan.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-700 shadow-xs border border-emerald-200';
            btnPengeluaran.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
        }

        populateCategoryDropdown('create', type);
    }

    // Modal Edit Transaksi
    function openEditTransactionModal(item) {
        const form = document.getElementById('form-edit-transaksi');
        form.action = '/keuangan/' + item.raw_id;

        setEditType(item.tipe, item.kategori, item.sub_kategori);
        document.getElementById('edit-judul').value = item.judul || '';
        document.getElementById('edit-nominal').value = item.nominal || '';

        let tgl = '';
        if (item.tanggal_raw) {
            tgl = item.tanggal_raw;
        } else if (typeof item.tanggal === 'string') {
            tgl = item.tanggal.substring(0, 10);
        }
        document.getElementById('edit-tanggal').value = tgl;
        document.getElementById('edit-crop-id').value = item.crop_id || '';
        document.getElementById('edit-keterangan').value = item.keterangan || '';
        document.getElementById('edit-foto-nota').value = '';
        document.getElementById('edit-foto-label').innerText = item.nota_url ? 'Ganti foto nota saat ini...' : 'Pilih file foto baru...';

        openModal('modal-edit-transaksi');
    }

    function setEditType(type, presetCat = null, presetSubCat = null) {
        document.getElementById('edit-input-tipe').value = type;
        const btnPengeluaran = document.getElementById('edit-type-pengeluaran');
        const btnPemasukan = document.getElementById('edit-type-pemasukan');

        if (type === 'pengeluaran') {
            btnPengeluaran.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-rose-700 shadow-xs border border-rose-200';
            btnPemasukan.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
        } else {
            btnPemasukan.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-700 shadow-xs border border-emerald-200';
            btnPengeluaran.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
        }

        populateCategoryDropdown('edit', type, presetCat, presetSubCat);
    }

    // Modal Kelola Kategori
    function openManageCategoriesModal() {
        resetCategoryForm();
        openModal('modal-manage-kategori');
    }

    function prepareAddSubCategory(parentId, parentName, parentType) {
        resetCategoryForm();
        document.getElementById('form-cat-heading').innerHTML = `<span>Tambah Sub-Kategori untuk: <b>${escapeHtml(parentName)}</b></span>`;
        document.getElementById('cat-parent-id').value = parentId;
        document.getElementById('cat-tipe').value = parentType;
        document.getElementById('cat-nama').focus();
    }

    function prepareEditCategory(id, name, type, parentId = null) {
        const form = document.getElementById('form-manage-category');
        form.action = '/keuangan/kategori/' + id;
        document.getElementById('cat-method-override').value = 'PUT';
        document.getElementById('form-cat-heading').innerHTML = `<span>Edit: <b>${escapeHtml(name)}</b></span>`;
        document.getElementById('cat-parent-id').value = parentId || '';
        document.getElementById('cat-tipe').value = type;
        document.getElementById('cat-nama').value = name;
        document.getElementById('cat-btn-cancel-edit').classList.remove('hidden');
        document.getElementById('cat-btn-submit').innerText = 'Perbarui Kategori';
        document.getElementById('cat-nama').focus();
    }

    function resetCategoryForm() {
        const form = document.getElementById('form-manage-category');
        form.action = '/keuangan/kategori';
        document.getElementById('cat-method-override').value = 'POST';
        document.getElementById('form-cat-heading').innerHTML = `
            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Kategori / Sub-Kategori Baru</span>
        `;
        document.getElementById('cat-parent-id').value = '';
        document.getElementById('cat-tipe').value = 'pengeluaran';
        document.getElementById('cat-nama').value = '';
        document.getElementById('cat-btn-cancel-edit').classList.add('hidden');
        document.getElementById('cat-btn-submit').innerText = 'Simpan Kategori';
    }

    function handleFileInputChange(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).innerText = input.files[0].name;
        }
    }

    function previewNota(url, title) {
        document.getElementById('preview-nota-img').src = url;
        document.getElementById('preview-nota-link').href = url;
        document.getElementById('preview-nota-title').innerText = 'Nota: ' + title;
        openModal('modal-preview-nota');
    }

    // ── Helper File to Base64 (Untuk simpan nota offline) ──
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
            reader.readAsDataURL(file);
        });
    }

    // ── AJAX: Mengambil Data Keuangan (Online & Offline Fallback) ──
    async function loadKeuanganData(periode = currentPeriode) {
        currentPeriode = periode;
        let data = null;

        // Coba ambil online jika perangkat terhubung
        if (navigator.onLine) {
            try {
                const res = await fetch(`/keuangan?periode=${encodeURIComponent(periode)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (res.ok) {
                    data = await res.json();
                    if (data.success && window.AgriOfflineStore) {
                        window.AgriOfflineStore.cacheKeuanganData(data.transaksiList, data.categories);
                    }
                }
            } catch (err) {
                console.warn('Network issue, falling back to local IndexedDB store:', err);
            }
        }

        // Jika offline atau jaringan bermasalah, baca dari local IndexedDB
        if (!data || !data.success) {
            if (window.AgriOfflineStore) {
                const offData = await window.AgriOfflineStore.getOfflineKeuanganData(periode);
                if (offData && offData.success) {
                    data = offData;
                }
            }
        }

        if (!data || !data.success) return;

        // 1. Update Metrics
        const elPemasukan = document.getElementById('metric-total-pemasukan');
        const elDescPemasukan = document.getElementById('metric-desc-pemasukan');
        const elPengeluaran = document.getElementById('metric-total-pengeluaran');
        const elDescPengeluaran = document.getElementById('metric-desc-pengeluaran');
        const elTransaksi = document.getElementById('metric-total-transaksi');
        const elDescTransaksi = document.getElementById('metric-desc-transaksi');

        if (elPemasukan) elPemasukan.innerText = data.formatted_total_pemasukan;
        if (elDescPemasukan) elDescPemasukan.innerText = `Dari ${data.jumlahPemasukan} transaksi pemasukan`;
        if (elPengeluaran) elPengeluaran.innerText = data.formatted_total_pengeluaran;
        if (elDescPengeluaran) elDescPengeluaran.innerText = `Dari ${data.jumlahPengeluaran} transaksi pengeluaran`;
        if (elTransaksi) elTransaksi.innerHTML = `${data.totalTransaksi} <span class="text-xs font-semibold text-gray-500">Catatan</span>`;
        if (elDescTransaksi) elDescTransaksi.innerText = `${data.jumlahPemasukan} pemasukan • ${data.jumlahPengeluaran} pengeluaran`;

        // 2. Update Tabs Count
        const tabAll = document.getElementById('btn-tab-all');
        const tabPemasukan = document.getElementById('btn-tab-pemasukan');
        const tabPengeluaran = document.getElementById('btn-tab-pengeluaran');
        if (tabAll) tabAll.innerText = `Semua (${data.totalTransaksi})`;
        if (tabPemasukan) tabPemasukan.innerText = `Pemasukan (${data.jumlahPemasukan})`;
        if (tabPengeluaran) tabPengeluaran.innerText = `Pengeluaran (${data.jumlahPengeluaran})`;

        // 3. Update Categories
        if (data.categories) {
            appCategories = data.categories;
            renderCategoryTree(appCategories);
        }

        // 4. Update Transactions
        if (data.transaksiList) {
            currentTransaksiList = data.transaksiList;
            renderTransactionsTable(currentTransaksiList);
        }
    }
    window.loadKeuanganData = loadKeuanganData;

    // ── Helper: Format Rupiah ──
    function formatRupiah(num) {
        return 'Rp ' + Math.round(Number(num || 0)).toLocaleString('id-ID');
    }

    // ── Helper: Kelompokkan Transaksi Berdasarkan Kategori & Sub-Kategori ──
    function groupTransactionsByCategory(list) {
        const catGroups = {};
        const catOrder = [];

        const categoriesSource = (window.appCategories && Array.isArray(window.appCategories))
            ? window.appCategories
            : (typeof appCategories !== 'undefined' && Array.isArray(appCategories) ? appCategories : []);

        categoriesSource.forEach(cat => {
            if (cat && cat.nama && !catOrder.includes(cat.nama)) {
                catOrder.push(cat.nama);
            }
        });

        list.forEach(t => {
            const cat = t.kategori || 'Lainnya';
            if (!catGroups[cat]) {
                catGroups[cat] = [];
                if (!catOrder.includes(cat)) {
                    catOrder.push(cat);
                }
            }
            catGroups[cat].push(t);
        });

        return catOrder
            .filter(cat => catGroups[cat] && catGroups[cat].length > 0)
            .map(cat => {
                const catTransactions = catGroups[cat];
                const subGroups = {};
                const subOrder = [];

                // Ambil daftar urutan sub-kategori terdaftar untuk kategori ini
                const currentCatObj = categoriesSource.find(c => c.nama === cat);
                const definedSubOrder = {};
                if (currentCatObj && Array.isArray(currentCatObj.subcategories)) {
                    currentCatObj.subcategories.forEach((s, idx) => {
                        if (s && s.nama) {
                            definedSubOrder[s.nama.toLowerCase().trim()] = s.urutan !== undefined ? s.urutan : idx;
                        }
                    });
                }

                catTransactions.forEach(t => {
                    const rawSub = (t.sub_kategori || '').trim();
                    const parts = rawSub ? rawSub.split(' › ').map(s => s.trim()).filter(Boolean) : [];
                    const subName = parts.length > 0 ? parts[0] : 'Umum';
                    const leafName = parts.length > 1 ? parts[parts.length - 1] : (parts.length === 1 ? parts[0] : null);

                    t._leafSub = leafName;
                    t._parentSub = subName;

                    if (!subGroups[subName]) {
                        subGroups[subName] = [];
                        subOrder.push(subName);
                    }
                    subGroups[subName].push(t);
                });

                // Finishing selalu diletakkan paling bawah di sub-kategori
                subOrder.sort((a, b) => {
                    const isFinishingA = a.toLowerCase().includes('finishing');
                    const isFinishingB = b.toLowerCase().includes('finishing');
                    if (isFinishingA && !isFinishingB) return 1;
                    if (!isFinishingA && isFinishingB) return -1;

                    const keyA = a.toLowerCase().trim();
                    const keyB = b.toLowerCase().trim();
                    const orderA = definedSubOrder[keyA] !== undefined ? definedSubOrder[keyA] : 1000;
                    const orderB = definedSubOrder[keyB] !== undefined ? definedSubOrder[keyB] : 1000;
                    if (orderA !== orderB) return orderA - orderB;
                    return a.localeCompare(b);
                });

                const subcategories = subOrder.map(subName => {
                    const subItems = subGroups[subName];
                    return {
                        name: subName,
                        transactions: subItems,
                        totalPengeluaran: subItems
                            .filter(t => t.tipe === 'pengeluaran')
                            .reduce((sum, t) => sum + (parseFloat(t.nominal) || 0), 0),
                        totalPemasukan: subItems
                            .filter(t => t.tipe === 'pemasukan')
                            .reduce((sum, t) => sum + (parseFloat(t.nominal) || 0), 0)
                    };
                });

                return {
                    name: cat,
                    transactions: catTransactions,
                    subcategories: subcategories,
                    totalPengeluaran: catTransactions
                        .filter(t => t.tipe === 'pengeluaran')
                        .reduce((sum, t) => sum + (parseFloat(t.nominal) || 0), 0),
                    totalPemasukan: catTransactions
                        .filter(t => t.tipe === 'pemasukan')
                        .reduce((sum, t) => sum + (parseFloat(t.nominal) || 0), 0)
                };
            });
    }

    // ── Helper: Render Smallest Sub Badge (Tampilan Paling Kecil / Daun Saja) ──
    function renderSmallestSubBadgeHtml(subKategori) {
        if (!subKategori) {
            return `<span class="text-xs text-gray-400 italic">—</span>`;
        }

        const parts = subKategori.split(' › ').map(s => s.trim()).filter(Boolean);
        if (parts.length === 0) {
            return `<span class="text-xs text-gray-400 italic">—</span>`;
        }

        const smallestSub = parts.length > 1 ? parts[parts.length - 1] : parts[0];

        return `
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/90 shadow-2xs" title="${escapeHtml(subKategori)}">
                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                ${escapeHtml(smallestSub)}
            </span>
        `;
    }

    function renderSmallestSubBadgeMobileHtml(subKategori) {
        if (!subKategori) return '';

        const parts = subKategori.split(' › ').map(s => s.trim()).filter(Boolean);
        if (parts.length === 0) return '';

        const smallestSub = parts.length > 1 ? parts[parts.length - 1] : parts[0];

        return `
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                <svg class="w-2.5 h-2.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                ${escapeHtml(smallestSub)}
            </span>
        `;
    }

    // ── Render Tabel & Kartu Transaksi Secara Dinamis (Dikelompokkan per Kategori) ──
    function renderTransactionsTable(list) {
        const emptyState = document.getElementById('transaksi-empty-state');
        const contentWrapper = document.getElementById('transaksi-content-wrapper');
        const tableBody = document.getElementById('transaksi-table-body');
        const mobileBody = document.getElementById('transaksi-mobile-body');

        if (!list || list.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (contentWrapper) contentWrapper.classList.add('hidden');
            if (tableBody) tableBody.innerHTML = '';
            if (mobileBody) mobileBody.innerHTML = '';
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (contentWrapper) contentWrapper.classList.remove('hidden');

        const grouped = groupTransactionsByCategory(list);

        // 1. Render Desktop Table Body
        if (tableBody) {
            tableBody.innerHTML = grouped.map(group => {
                const subtotalParts = [];
                if (group.totalPengeluaran > 0) {
                    subtotalParts.push(`<span class="text-rose-300">Pengeluaran: -${formatRupiah(group.totalPengeluaran)}</span>`);
                }
                if (group.totalPemasukan > 0) {
                    subtotalParts.push(`<span class="text-emerald-300">Pemasukan: +${formatRupiah(group.totalPemasukan)}</span>`);
                }
                const subtotalHtml = subtotalParts.join('<span class="text-emerald-600">|</span>');

                const catHeaderRow = `
                    <tr class="category-header-row bg-emerald-900 text-white border-t-2 border-emerald-950" data-category="${escapeHtml(group.name)}">
                        <td colspan="7" class="px-5 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-300/40"></span>
                                    <span class="font-extrabold text-white text-xs sm:text-sm uppercase tracking-wider">${escapeHtml(group.name)}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-emerald-800 text-emerald-200">(${group.transactions.length} transaksi)</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold">
                                    ${subtotalHtml}
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

                const subRows = group.subcategories.map(sub => {
                    const subtotalPartsSub = [];
                    if (sub.totalPengeluaran > 0) {
                        subtotalPartsSub.push(`<span class="text-rose-600">Subtotal: -${formatRupiah(sub.totalPengeluaran)}</span>`);
                    }
                    if (sub.totalPemasukan > 0) {
                        subtotalPartsSub.push(`<span class="text-emerald-700">Subtotal: +${formatRupiah(sub.totalPemasukan)}</span>`);
                    }
                    const subtotalSubHtml = subtotalPartsSub.join('<span class="text-slate-300">|</span>');

                    const subHeaderRow = `
                        <tr class="subcategory-header-row bg-slate-100/90 border-y border-slate-200/90" data-category="${escapeHtml(group.name)}" data-subcategory="${escapeHtml(sub.name)}">
                            <td colspan="7" class="pl-8 pr-5 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400 font-bold text-xs select-none">↳</span>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-sky-100/90 text-sky-900 border border-sky-200">
                                            <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                            ${escapeHtml(sub.name)}
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-medium">(${sub.transactions.length} transaksi)</span>
                                    </div>
                                    <div class="flex items-center gap-2.5 text-xs font-semibold">
                                        ${subtotalSubHtml}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;

                    const itemRows = sub.transactions.map(t => {
                    const isPemasukan = t.tipe === 'pemasukan';
                    const tipeBadge = isPemasukan
                        ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Pemasukan
                           </span>`
                        : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Pengeluaran
                           </span>`;

                    const nominalClass = isPemasukan ? 'text-emerald-700' : 'text-rose-600';
                    const nominalSign = isPemasukan ? '+' : '-';
                    const notaBtn = t.nota_url
                        ? `<button type="button"
                                   onclick="previewNota('${escapeHtml(t.nota_url)}', '${escapeHtml(t.judul)}')"
                                   class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer">
                               <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                               Nota
                           </button>`
                        : '';

                    const offlineBadge = (t.is_offline || t.is_synced === false)
                        ? `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300" title="Tersimpan di HP (Belum disinkronkan ke server)">
                            <span class="w-1 h-1 rounded-full bg-amber-500"></span> Offline
                           </span>`
                        : '';

                    const effectiveId = t.raw_id || t.id;

                    return `
                        <tr class="hover:bg-gray-50 transition-colors transaction-row" data-type="${t.tipe}" data-category="${escapeHtml(group.name)}" data-subcategory="${escapeHtml(sub.name)}">
                            <td class="px-5 py-3.5 whitespace-nowrap text-gray-600 font-medium">
                                ${escapeHtml(t.formatted_tanggal || '—')}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                ${tipeBadge}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-gray-900">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span>${escapeHtml(t.judul)}</span>
                                    ${offlineBadge}
                                    ${notaBtn}
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                ${renderSmallestSubBadgeHtml(t.sub_kategori)}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 max-w-xs break-words">
                                ${escapeHtml(t.deskripsi || '—')}
                            </td>
                            <td class="px-5 py-3.5 text-right font-extrabold whitespace-nowrap ${nominalClass}">
                                ${nominalSign}${escapeHtml(t.formatted_nominal)}
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            onclick='openEditTransactionModal(${JSON.stringify(t)})'
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                            title="Edit Transaksi">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                            onclick="deleteTransaction('${effectiveId}', '${escapeHtml(t.judul)}')"
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Hapus Transaksi">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                    return subHeaderRow + itemRows;
                }).join('');

                return catHeaderRow + subRows;
            }).join('');
        }

        // 2. Render Mobile View
        if (mobileBody) {
            mobileBody.innerHTML = grouped.map(group => {
                const subtotalMobile = [];
                if (group.totalPengeluaran > 0) {
                    subtotalMobile.push(`<span class="text-rose-300 block">-Rp ${Math.round(group.totalPengeluaran).toLocaleString('id-ID')}</span>`);
                }
                if (group.totalPemasukan > 0) {
                    subtotalMobile.push(`<span class="text-emerald-300 block">+Rp ${Math.round(group.totalPemasukan).toLocaleString('id-ID')}</span>`);
                }

                const subBlocks = group.subcategories.map(sub => {
                    const subSubtotal = [];
                    if (sub.totalPengeluaran > 0) {
                        subSubtotal.push(`<span class="text-rose-600 block">-Rp ${Math.round(sub.totalPengeluaran).toLocaleString('id-ID')}</span>`);
                    }
                    if (sub.totalPemasukan > 0) {
                        subSubtotal.push(`<span class="text-emerald-700 block">+Rp ${Math.round(sub.totalPemasukan).toLocaleString('id-ID')}</span>`);
                    }

                    const cards = sub.transactions.map(t => {
                        const isPemasukan = t.tipe === 'pemasukan';
                        const tipeBadge = isPemasukan
                            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Pemasukan</span>`
                            : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Pengeluaran</span>`;

                        const nominalClass = isPemasukan ? 'text-emerald-700' : 'text-rose-600';
                        const nominalSign = isPemasukan ? '+' : '-';
                        const notaBtn = t.nota_url
                            ? `<button type="button"
                                       onclick="previewNota('${escapeHtml(t.nota_url)}', '${escapeHtml(t.judul)}')"
                                       class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100">
                                   <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                   Nota
                               </button>`
                            : '';

                        const offlineBadge = (t.is_offline || t.is_synced === false)
                            ? `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <span class="w-1 h-1 rounded-full bg-amber-500"></span> Offline
                               </span>`
                            : '';

                        const descP = (t.deskripsi && t.deskripsi !== '—')
                            ? `<p class="text-xs text-gray-500">${escapeHtml(t.deskripsi)}</p>`
                            : '';

                        const effectiveId = t.raw_id || t.id;

                        return `
                            <div class="p-3 sm:p-4 space-y-1.5 sm:space-y-2 transaction-row hover:bg-gray-50 transition-colors" data-type="${t.tipe}" data-category="${escapeHtml(group.name)}" data-subcategory="${escapeHtml(sub.name)}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="text-[11px] font-semibold text-gray-500">
                                            ${escapeHtml(t.formatted_tanggal || '—')}
                                        </span>
                                        <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                            <h3 class="text-sm font-bold text-gray-900">${escapeHtml(t.judul)}</h3>
                                            ${offlineBadge}
                                        </div>
                                    </div>
                                    <span class="text-sm font-extrabold shrink-0 ${nominalClass}">
                                        ${nominalSign}${escapeHtml(t.formatted_nominal)}
                                    </span>
                                </div>
                                <div class="space-y-1 pt-0.5">
                                    <div class="flex items-center gap-1.5 flex-wrap text-xs">
                                        ${tipeBadge}
                                        ${renderSmallestSubBadgeMobileHtml(t.sub_kategori)}
                                    </div>
                                    ${descP}
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100">
                                    ${notaBtn}
                                    <button type="button"
                                            onclick='openEditTransactionModal(${JSON.stringify(t)})'
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                                        Edit
                                    </button>
                                    <button type="button"
                                            onclick="deleteTransaction('${effectiveId}', '${escapeHtml(t.judul)}')"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');

                    return `
                        <div class="subcategory-mobile-group border-b border-gray-200/80 last:border-b-0" data-category="${escapeHtml(group.name)}" data-subcategory="${escapeHtml(sub.name)}">
                            <div class="px-3 py-1.5 bg-slate-100 border-b border-slate-200/80 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400 text-xs">↳</span>
                                    <span class="font-bold text-sky-900 bg-sky-100 px-2 py-0.5 rounded text-[11px] border border-sky-200 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        ${escapeHtml(sub.name)}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-medium">(${sub.transactions.length})</span>
                                </div>
                                <div class="text-[10.5px] font-semibold text-right">
                                    ${subSubtotal.join('')}
                                </div>
                            </div>
                            <div class="divide-y divide-gray-100">
                                ${cards}
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="category-mobile-group border-b-2 border-emerald-950/20 last:border-b-0" data-category="${escapeHtml(group.name)}">
                        <div class="px-3.5 py-2.5 bg-emerald-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-300/40"></span>
                                <span class="font-extrabold text-xs uppercase tracking-wide text-white">${escapeHtml(group.name)}</span>
                                <span class="text-[10px] text-emerald-200 bg-emerald-800 px-1.5 py-0.2 rounded font-semibold">(${group.transactions.length})</span>
                            </div>
                            <div class="text-[11px] font-bold text-right">
                                ${subtotalMobile.join('')}
                            </div>
                        </div>
                        ${subBlocks}
                    </div>
                `;
            }).join('');
        }

        filterTransactions(currentFilterType);
    }

    // ── Render Tree Kategori Secara Dinamis ──
    function renderCategoryTree(categories) {
        const container = document.getElementById('category-tree-container');
        const parentSelect = document.getElementById('cat-parent-id');
        if (!container) return;

        if (!categories || categories.length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-500 py-3 text-center">Belum ada kategori terdaftar.</p>';
        } else {
            container.innerHTML = categories.map(cat => {
                const badgeClass = cat.tipe === 'pemasukan' ? 'bg-emerald-100 text-emerald-800' : (cat.tipe === 'pengeluaran' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800');
                const subcategories = cat.subcategories || [];

                return `
                    <div class="border border-gray-200 rounded-xl p-3.5 bg-white hover:border-emerald-300 transition-colors shadow-2xs">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-gray-900 text-sm">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                    ${escapeHtml(cat.nama)}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                                    ${cat.tipe.charAt(0).toUpperCase() + cat.tipe.slice(1)}
                                </span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    Kategori Utama
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button"
                                        onclick="prepareAddSubCategory(${cat.id}, '${escapeHtml(cat.nama)}', '${cat.tipe}')"
                                        class="px-2 py-1 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 cursor-pointer">
                                    + Sub
                                </button>
                                <button type="button"
                                        onclick="prepareEditCategory(${cat.id}, '${escapeHtml(cat.nama)}', '${cat.tipe}', null)"
                                        class="p-1 rounded text-gray-500 hover:text-blue-600 hover:bg-blue-50 cursor-pointer"
                                        title="Edit Kategori">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button type="button"
                                        onclick="deleteCategory(${cat.id}, '${escapeHtml(cat.nama)}', false)"
                                        class="p-1 rounded text-gray-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer"
                                        title="Hapus Kategori">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        ${subcategories.length > 0 ? `
                            <div class="mt-3 pt-2.5 border-t border-gray-100 pl-2 sm:pl-3 space-y-2">
                                ${subcategories.map(sub => {
                                    const subSubs = sub.subcategories || [];
                                    return `
                                    <div class="p-2 rounded-lg bg-sky-50/70 border border-sky-200/80 hover:bg-sky-50 transition-colors">
                                        <div class="flex items-center justify-between text-xs gap-2">
                                            <span class="text-sky-950 font-semibold flex items-center gap-1.5 flex-wrap">
                                                <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                </svg>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300 shrink-0">Sub</span>
                                                <span>${escapeHtml(sub.nama)}</span>
                                            </span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <button type="button"
                                                        onclick="prepareAddSubCategory(${sub.id}, '${escapeHtml(sub.nama)}', '${sub.tipe}')"
                                                        class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 cursor-pointer border border-emerald-300"
                                                        title="Tambah Sub-Sub">+ Sub-Sub</button>
                                                <button type="button"
                                                        onclick="prepareEditCategory(${sub.id}, '${escapeHtml(sub.nama)}', '${sub.tipe}', ${cat.id})"
                                                        class="p-1 rounded text-sky-700 hover:text-blue-700 hover:bg-sky-100"
                                                        title="Edit Sub-Kategori">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                <button type="button"
                                                        onclick="deleteCategory(${sub.id}, '${escapeHtml(sub.nama)}', true)"
                                                        class="p-1 rounded text-sky-700 hover:text-rose-700 hover:bg-rose-50"
                                                        title="Hapus Sub-Kategori">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        ${subSubs.length > 0 ? `
                                            <div class="mt-2 pl-3 ml-2 border-l-2 border-amber-300 space-y-1.5">
                                                ${subSubs.map(subSub => `
                                                    <div class="flex items-center justify-between text-[11px] py-1 px-2 rounded-md bg-amber-50/80 border border-amber-200/80 hover:bg-amber-100/60 transition-colors">
                                                        <span class="text-amber-950 font-medium flex items-center gap-1.5 flex-wrap">
                                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                            </svg>
                                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300 shrink-0">Sub-Sub</span>
                                                            <span>${escapeHtml(subSub.nama)}</span>
                                                        </span>
                                                        <div class="flex items-center gap-1 shrink-0">
                                                            <button type="button"
                                                                    onclick="prepareEditCategory(${subSub.id}, '${escapeHtml(subSub.nama)}', '${subSub.tipe}', ${sub.id})"
                                                                    class="p-0.5 rounded text-amber-700 hover:text-blue-700 hover:bg-amber-100" title="Edit Sub-Sub">
                                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            </button>
                                                            <button type="button"
                                                                    onclick="deleteCategory(${subSub.id}, '${escapeHtml(subSub.nama)}', true)"
                                                                    class="p-0.5 rounded text-amber-700 hover:text-rose-700 hover:bg-rose-50" title="Hapus Sub-Sub">
                                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                                }).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            }).join('');
        }

        if (parentSelect) {
            const currentVal = parentSelect.value;
            parentSelect.innerHTML = '<option value="">— Ini Kategori Utama (Parent) —</option>';
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = `📁 ${cat.nama} (${cat.tipe.charAt(0).toUpperCase() + cat.tipe.slice(1)})`;
                if (currentVal && String(currentVal) === String(cat.id)) {
                    opt.selected = true;
                }
                parentSelect.appendChild(opt);
                // Tambahkan sub-kategori sebagai opsi parent juga (untuk sub-sub)
                const subs = cat.subcategories || [];
                subs.forEach(sub => {
                    const subOpt = document.createElement('option');
                    subOpt.value = sub.id;
                    subOpt.innerHTML = `\u00A0\u00A0↳ 📂 [Sub] ${escapeHtml(sub.nama)}`;
                    if (currentVal && String(currentVal) === String(sub.id)) {
                        subOpt.selected = true;
                    }
                    parentSelect.appendChild(subOpt);
                });
            });
        }

        const currentCreateType = document.getElementById('create-input-tipe')?.value || 'pengeluaran';
        const currentCreateCat = document.getElementById('create-select-kategori')?.value;
        const currentCreateSub = document.getElementById('create-input-sub-kategori')?.value;
        populateCategoryDropdown('create', currentCreateType, currentCreateCat, currentCreateSub);
    }

    // ── Helper Notifikasi & Dialog SweetAlert ──
    function notifySuccess(message) {
        if (window.AgriSwal && typeof window.AgriSwal.toastSuccess === 'function') {
            window.AgriSwal.toastSuccess(message);
        } else if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                iconColor: '#2FB344',
                title: message,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        }
    }

    function notifyError(message) {
        if (window.AgriSwal && typeof window.AgriSwal.toastError === 'function') {
            window.AgriSwal.toastError(message);
        } else if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                iconColor: '#E4574C',
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
    }

    function confirmDeleteModal(title, itemName, onConfirm) {
        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(title, itemName, onConfirm);
        } else if (window.Swal) {
            Swal.fire({
                title: title || 'Hapus Data?',
                html: `Apakah Anda yakin ingin menghapus <strong>"${itemName}"</strong>?<br><span class="agri-swal-subtitle">Data yang dihapus tidak dapat dipulihkan kembali.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'agri-swal-btn-danger',
                    cancelButton: 'agri-swal-btn-cancel'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        }
    }

    // ── Hapus Transaksi (Online & Offline Support) ──
    function deleteTransaction(id, title) {
        confirmDeleteModal('Hapus Transaksi?', title, () => {
            performDeleteTransaction(id);
        });
    }

    async function performDeleteTransaction(id) {
        if (!navigator.onLine) {
            if (window.AgriOfflineStore) {
                await window.AgriOfflineStore.deleteKeuanganTransactionOffline(id);
                notifySuccess('Catatan transaksi dihapus secara offline.');
                loadKeuanganData();
                const p = await window.AgriOfflineStore.getPendingCount();
                if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
            }
            return;
        }

        try {
            const res = await fetch(`/keuangan/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                notifySuccess(data.message || 'Catatan transaksi berhasil dihapus!');
                loadKeuanganData();
            } else {
                notifyError(data.message || 'Gagal menghapus transaksi.');
            }
        } catch (err) {
            console.warn('Network error, deleting transaction offline:', err);
            if (window.AgriOfflineStore) {
                await window.AgriOfflineStore.deleteKeuanganTransactionOffline(id);
                notifySuccess('Catatan transaksi dihapus secara offline.');
                loadKeuanganData();
                const p = await window.AgriOfflineStore.getPendingCount();
                if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
            } else {
                notifyError('Terjadi kesalahan jaringan.');
            }
        }
    }

    // ── Hapus Kategori / Sub-Kategori (Online & Offline Support) ──
    function deleteCategory(id, name, isSub) {
        const label = isSub ? 'Sub-Kategori' : 'Kategori';
        confirmDeleteModal(`Hapus ${label}?`, name, () => {
            performDeleteCategory(id);
        });
    }

    async function performDeleteCategory(id) {
        if (!navigator.onLine) {
            if (window.AgriOfflineStore) {
                await window.AgriOfflineStore.deleteKeuanganCategoryOffline(id);
                notifySuccess('Kategori dihapus secara offline.');
                loadKeuanganData();
                const p = await window.AgriOfflineStore.getPendingCount();
                if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
            }
            return;
        }

        try {
            const res = await fetch(`/keuangan/kategori/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                notifySuccess(data.message || 'Kategori berhasil dihapus!');
                loadKeuanganData();
            } else {
                notifyError(data.message || 'Gagal menghapus kategori.');
            }
        } catch (err) {
            console.warn('Network error, deleting category offline:', err);
            if (window.AgriOfflineStore) {
                await window.AgriOfflineStore.deleteKeuanganCategoryOffline(id);
                notifySuccess('Kategori dihapus secara offline.');
                loadKeuanganData();
                const p = await window.AgriOfflineStore.getPendingCount();
                if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
            } else {
                notifyError('Terjadi kesalahan jaringan.');
            }
        }
    }

    // ── Switch Filter Periode (AJAX) ──
    function setPeriodeFilter(periode) {
        currentPeriode = periode;

        ['semua', 'bulan_ini', 'tahun_ini'].forEach(p => {
            const btn = document.getElementById('filter-periode-' + p);
            if (btn) {
                if (p === periode) {
                    btn.className = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer bg-white text-gray-900 shadow-xs';
                } else {
                    btn.className = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900';
                }
            }
        });

        const newUrl = '/keuangan' + (periode !== 'semua' ? '?periode=' + encodeURIComponent(periode) : '');
        window.history.pushState(null, '', newUrl);

        loadKeuanganData(periode);
    }

    // ── Form Submit Handlers (Online & Offline Support) ──
    document.addEventListener('DOMContentLoaded', function() {
        // Jika sedang offline saat halaman dibuka, render data dari IndexedDB
        if (!navigator.onLine) {
            loadKeuanganData();
        }

        // Auto-refresh when background sync completes
        window.addEventListener('agri:sync-success', () => {
            loadKeuanganData();
        });
        window.addEventListener('agri:data-changed', () => {
            loadKeuanganData();
        });

        // 1. Form Catat Transaksi Baru
        const formCreate = document.getElementById('form-create-transaksi');
        if (formCreate) {
            formCreate.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerText : 'Simpan Transaksi';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Menyimpan...';
                }

                const formData = new FormData(this);

                // Mode Offline
                if (!navigator.onLine) {
                    try {
                        const fileInput = this.querySelector('input[name="foto_nota"]');
                        let base64Photo = null;
                        if (fileInput && fileInput.files && fileInput.files[0]) {
                            base64Photo = await fileToBase64(fileInput.files[0]);
                        }

                        const offlineData = {
                            tipe: formData.get('tipe'),
                            kategori: formData.get('kategori'),
                            sub_kategori: formData.get('sub_kategori'),
                            judul: formData.get('judul'),
                            nominal: formData.get('nominal'),
                            tanggal: formData.get('tanggal'),
                            crop_id: formData.get('crop_id'),
                            keterangan: formData.get('keterangan'),
                            foto_nota_base64: base64Photo
                        };

                        if (window.AgriOfflineStore) {
                            await window.AgriOfflineStore.addKeuanganTransactionOffline(offlineData);
                            closeModal('modal-create-transaksi');
                            formCreate.reset();
                            notifySuccess('Transaksi disimpan di HP (Mode Offline). Akan otomatis disinkronkan saat online.');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        }
                    } catch (err) {
                        console.error('Offline save error:', err);
                        notifyError('Gagal menyimpan transaksi offline: ' + err.message);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        }
                    }
                    return;
                }

                // Mode Online
                try {
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        closeModal('modal-create-transaksi');
                        formCreate.reset();
                        notifySuccess(data.message || 'Transaksi keuangan berhasil dicatat!');
                        loadKeuanganData();
                    } else {
                        let errorMsg = data.message || 'Gagal mencatat transaksi.';
                        if (data.errors) {
                            const firstErr = Object.values(data.errors)[0];
                            if (Array.isArray(firstErr)) errorMsg = firstErr[0];
                        }
                        notifyError(errorMsg);
                    }
                } catch (err) {
                    console.warn('Network error saving transaction, falling back to offline:', err);
                    if (window.AgriOfflineStore) {
                        try {
                            const fileInput = this.querySelector('input[name="foto_nota"]');
                            let base64Photo = null;
                            if (fileInput && fileInput.files && fileInput.files[0]) {
                                base64Photo = await fileToBase64(fileInput.files[0]);
                            }

                            const offlineData = {
                                tipe: formData.get('tipe'),
                                kategori: formData.get('kategori'),
                                sub_kategori: formData.get('sub_kategori'),
                                judul: formData.get('judul'),
                                nominal: formData.get('nominal'),
                                tanggal: formData.get('tanggal'),
                                crop_id: formData.get('crop_id'),
                                keterangan: formData.get('keterangan'),
                                foto_nota_base64: base64Photo
                            };

                            await window.AgriOfflineStore.addKeuanganTransactionOffline(offlineData);
                            closeModal('modal-create-transaksi');
                            formCreate.reset();
                            notifySuccess('Koneksi terputus. Transaksi disimpan di HP (Mode Offline).');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        } catch (e) {
                            notifyError('Terjadi kesalahan koneksi saat menyimpan.');
                        }
                    } else {
                        notifyError('Terjadi kesalahan koneksi saat menyimpan.');
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                }
            });
        }

        // 2. Form Edit Transaksi
        const formEdit = document.getElementById('form-edit-transaksi');
        if (formEdit) {
            formEdit.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerText : 'Simpan Perubahan';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Menyimpan...';
                }

                const formData = new FormData(this);
                const rawId = currentEditTransaction ? (currentEditTransaction.raw_id || currentEditTransaction.id) : null;

                // Mode Offline
                if (!navigator.onLine) {
                    try {
                        const fileInput = this.querySelector('input[name="foto_nota"]');
                        let base64Photo = null;
                        if (fileInput && fileInput.files && fileInput.files[0]) {
                            base64Photo = await fileToBase64(fileInput.files[0]);
                        }

                        const offlineData = {
                            tipe: formData.get('tipe'),
                            kategori: formData.get('kategori'),
                            sub_kategori: formData.get('sub_kategori'),
                            judul: formData.get('judul'),
                            nominal: formData.get('nominal'),
                            tanggal: formData.get('tanggal'),
                            crop_id: formData.get('crop_id'),
                            keterangan: formData.get('keterangan'),
                            foto_nota_base64: base64Photo
                        };

                        if (window.AgriOfflineStore && rawId) {
                            await window.AgriOfflineStore.updateKeuanganTransactionOffline(rawId, offlineData);
                            closeModal('modal-edit-transaksi');
                            notifySuccess('Perubahan disimpan di HP (Mode Offline). Akan disinkronkan saat online.');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        }
                    } catch (err) {
                        console.error('Offline edit error:', err);
                        notifyError('Gagal mengubah transaksi offline: ' + err.message);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        }
                    }
                    return;
                }

                // Mode Online
                try {
                    if (!formData.has('_method')) {
                        formData.append('_method', 'PUT');
                    }

                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        closeModal('modal-edit-transaksi');
                        notifySuccess(data.message || 'Transaksi keuangan berhasil diperbarui!');
                        loadKeuanganData();
                    } else {
                        let errorMsg = data.message || 'Gagal memperbarui transaksi.';
                        if (data.errors) {
                            const firstErr = Object.values(data.errors)[0];
                            if (Array.isArray(firstErr)) errorMsg = firstErr[0];
                        }
                        notifyError(errorMsg);
                    }
                } catch (err) {
                    console.warn('Network error during online edit, falling back to offline:', err);
                    if (window.AgriOfflineStore && rawId) {
                        try {
                            const fileInput = this.querySelector('input[name="foto_nota"]');
                            let base64Photo = null;
                            if (fileInput && fileInput.files && fileInput.files[0]) {
                                base64Photo = await fileToBase64(fileInput.files[0]);
                            }

                            const offlineData = {
                                tipe: formData.get('tipe'),
                                kategori: formData.get('kategori'),
                                sub_kategori: formData.get('sub_kategori'),
                                judul: formData.get('judul'),
                                nominal: formData.get('nominal'),
                                tanggal: formData.get('tanggal'),
                                crop_id: formData.get('crop_id'),
                                keterangan: formData.get('keterangan'),
                                foto_nota_base64: base64Photo
                            };

                            await window.AgriOfflineStore.updateKeuanganTransactionOffline(rawId, offlineData);
                            closeModal('modal-edit-transaksi');
                            notifySuccess('Koneksi terputus. Perubahan disimpan di HP (Mode Offline).');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        } catch (e) {
                            notifyError('Terjadi kesalahan koneksi.');
                        }
                    } else {
                        notifyError('Terjadi kesalahan koneksi.');
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                }
            });
        }

        // 3. Form Kelola Kategori & Sub-Kategori
        const formCategory = document.getElementById('form-manage-category');
        if (formCategory) {
            formCategory.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('cat-btn-submit');
                const originalText = submitBtn ? submitBtn.innerText : 'Simpan Kategori';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Menyimpan...';
                }

                const formData = new FormData(this);

                // Mode Offline
                if (!navigator.onLine) {
                    try {
                        const offlineCatData = {
                            nama: formData.get('nama'),
                            tipe: formData.get('tipe'),
                            parent_id: formData.get('parent_id') || null
                        };

                        if (window.AgriOfflineStore) {
                            await window.AgriOfflineStore.addKeuanganCategoryOffline(offlineCatData);
                            resetCategoryForm();
                            notifySuccess('Kategori disimpan di HP (Mode Offline). Akan disinkronkan saat online.');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        }
                    } catch (err) {
                        console.error('Offline category error:', err);
                        notifyError('Gagal menyimpan kategori offline: ' + err.message);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        }
                    }
                    return;
                }

                // Mode Online
                try {
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        resetCategoryForm();
                        notifySuccess(data.message || 'Kategori berhasil disimpan!');
                        loadKeuanganData();
                    } else {
                        let errorMsg = data.message || 'Gagal menyimpan kategori.';
                        if (data.errors) {
                            const firstErr = Object.values(data.errors)[0];
                            if (Array.isArray(firstErr)) errorMsg = firstErr[0];
                        }
                        notifyError(errorMsg);
                    }
                } catch (err) {
                    console.warn('Network error saving category, falling back to offline:', err);
                    if (window.AgriOfflineStore) {
                        try {
                            const offlineCatData = {
                                nama: formData.get('nama'),
                                tipe: formData.get('tipe'),
                                parent_id: formData.get('parent_id') || null
                            };

                            await window.AgriOfflineStore.addKeuanganCategoryOffline(offlineCatData);
                            resetCategoryForm();
                            notifySuccess('Koneksi terputus. Kategori disimpan di HP (Mode Offline).');
                            loadKeuanganData();
                            const p = await window.AgriOfflineStore.getPendingCount();
                            if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
                        } catch (e) {
                            notifyError('Terjadi kesalahan koneksi.');
                        }
                    } else {
                        notifyError('Terjadi kesalahan koneksi.');
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                }
            });
        }
    });

    // Close on Escape
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modal-create-transaksi');
            closeModal('modal-edit-transaksi');
            closeModal('modal-manage-kategori');
            closeModal('modal-preview-nota');
        }
    });
</script>
@endsection
