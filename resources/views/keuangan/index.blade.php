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

    {{-- ── Sub Menu Tab Navigasi: Riwayat & Statistik & Laporan ── --}}
    @php
        $activeTab = request()->query('tab', 'riwayat');
        $isStatistikActive = ($activeTab === 'statistik');
        $isLaporanActive = ($activeTab === 'laporan');
        $isRiwayatActive = (!$isStatistikActive && !$isLaporanActive);
    @endphp

    <div class="flex items-center gap-1 sm:gap-2 border-b border-gray-200 overflow-x-auto no-scrollbar">
        {{-- Tombol Sub Menu Riwayat --}}
        <button type="button"
                onclick="setKeuanganSubmenu('riwayat')"
                id="btn-subnav-riwayat"
                class="subnav-tab-btn flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2.5 text-xs sm:text-sm whitespace-nowrap transition-all cursor-pointer {{ $isRiwayatActive ? 'font-extrabold text-emerald-800 bg-emerald-50/70 border-b-2 border-emerald-600 rounded-t-xl' : 'font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent' }}">
            <svg class="w-4 h-4 {{ $isRiwayatActive ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Riwayat</span>
            <span id="subnav-badge-riwayat" class="px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold {{ $isRiwayatActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                {{ $totalTransaksi }}
            </span>
        </button>

        {{-- Tombol Sub Menu Statistik --}}
        <button type="button"
                onclick="setKeuanganSubmenu('statistik')"
                id="btn-subnav-statistik"
                class="subnav-tab-btn flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2.5 text-xs sm:text-sm whitespace-nowrap transition-all cursor-pointer {{ $isStatistikActive ? 'font-extrabold text-emerald-800 bg-emerald-50/70 border-b-2 border-emerald-600 rounded-t-xl' : 'font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent' }}">
            <svg class="w-4 h-4 {{ $isStatistikActive ? 'text-purple-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            <span>Statistik</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold {{ $isStatistikActive ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                Infografis
            </span>
        </button>

        {{-- Tombol Sub Menu Laporan Keuangan (Subtotal) --}}
        <button type="button"
                onclick="setKeuanganSubmenu('laporan')"
                id="btn-subnav-laporan"
                class="subnav-tab-btn flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2.5 text-xs sm:text-sm whitespace-nowrap transition-all cursor-pointer {{ $isLaporanActive ? 'font-extrabold text-emerald-800 bg-emerald-50/70 border-b-2 border-emerald-600 rounded-t-xl' : 'font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent' }}">
            <svg class="w-4 h-4 {{ $isLaporanActive ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Laporan Keuangan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold {{ $isLaporanActive ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                Subtotal
            </span>
        </button>
    </div>

    {{-- ── KONTEN SUB MENU 1: RIWAYAT ── --}}
    <div id="submenu-content-riwayat" class="space-y-3.5 sm:space-y-6 {{ !$isRiwayatActive ? 'hidden' : '' }}">

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
                    {{-- <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Tipe</th>
                            <th class="px-5 py-3">Transaksi</th>
                            <th class="px-5 py-3">Sub-Kategori</th>
                            <th class="px-5 py-3">Keterangan / Terkait</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead> --}}
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
                                                    onclick="openEditTransactionModal({{ $t['raw_id'] }})"
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
                                                        onclick="openEditTransactionModal({{ $t['raw_id'] }})"
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
    {{-- Akhir Konten Sub Menu 1: Riwayat --}}

    {{-- ── KONTEN SUB MENU 2: STATISTIK ── --}}
    <div id="submenu-content-statistik" class="space-y-3.5 sm:space-y-6 {{ !$isStatistikActive ? 'hidden' : '' }}">
        <style>
            .donut-slice {
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, filter 0.25s ease;
                transform-origin: 190px 190px;
            }
            .donut-slice:hover, .donut-slice.active {
                transform: scale(1.045);
                filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.16));
            }
            .donut-slice.dimmed {
                opacity: 0.3;
            }
            .category-stat-card {
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
            }
            .category-stat-card:hover, .category-stat-card.active {
                transform: translateY(-2px);
            }
        </style>

        <div id="section-category-stats" class="bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            {{-- Header Infografis --}}
            <div class="p-3.5 sm:p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50/70 via-white to-emerald-50/20">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </span>
                        <h2 class="text-sm sm:text-base font-extrabold text-gray-900 tracking-tight">
                            Statistik Distribusi Kategori
                        </h2>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5 sm:ml-9">
                        Visualisasi persentase dan proporsi anggaran tani berdasarkan kategori transaksi.
                    </p>
                </div>

                {{-- Filter Tipe Chart (Pengeluaran / Pemasukan / Semua) --}}
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-gray-200 self-start sm:self-auto">
                    <button type="button"
                            onclick="setChartTypeFilter('pengeluaran')"
                            id="chart-tab-pengeluaran"
                            class="chart-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-900 shadow-xs">
                        Pengeluaran
                    </button>
                    <button type="button"
                            onclick="setChartTypeFilter('pemasukan')"
                            id="chart-tab-pemasukan"
                            class="chart-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                        Pemasukan
                    </button>
                    <button type="button"
                            onclick="setChartTypeFilter('semua')"
                            id="chart-tab-semua"
                            class="chart-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                        Semua
                    </button>
                </div>
            </div>

            {{-- Empty State (Jika tidak ada transaksi untuk tipe/filter ini) --}}
            <div id="chart-empty-state" class="p-8 sm:p-12 text-center hidden">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-xs sm:text-sm font-bold text-gray-800" id="chart-empty-title">Belum Ada Data Transaksi</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto" id="chart-empty-desc">
                    Belum ada catatan kategori pada tipe ini untuk menampilkan infografis diagram lingkaran.
                </p>
            </div>

            {{-- Area Infografis Utama (Legend Kategori + Donut Chart SVG) --}}
            <div id="chart-content-area" class="p-4 sm:p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

                    {{-- Sisi Kiri: Daftar Kategori Bergaris Sesuai Referensi Infografis --}}
                    <div class="lg:col-span-5 space-y-3 order-2 lg:order-1">
                        <div class="border-b border-gray-100 pb-2 mb-2 flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Daftar Kategori</span>
                            <span class="text-[11px] font-bold text-gray-400">Persentase</span>
                        </div>
                        <div id="chart-category-legend-list" class="space-y-2.5">
                            {{-- Diisi secara dinamis melalui JavaScript renderCategoryStatsChart() --}}
                        </div>
                    </div>

                    {{-- Sisi Kanan: Donut Chart dengan Label Persentase di Luar Slices --}}
                    <div class="lg:col-span-7 flex flex-col items-center justify-center order-1 lg:order-2">
                        <div class="relative w-full max-w-[320px] sm:max-w-[370px] aspect-square flex items-center justify-center">
                            <svg id="donut-chart-svg" viewBox="0 0 380 380" class="w-full h-full overflow-visible select-none">
                                <defs>
                                    <filter id="donut-shadow" x="-15%" y="-15%" width="130%" height="130%">
                                        <feDropShadow dx="0" dy="5" stdDeviation="7" flood-opacity="0.12" flood-color="#0f172a" />
                                    </filter>
                                </defs>
                                {{-- Donut Slices & Callouts --}}
                                <g id="donut-chart-slices" filter="url(#donut-shadow)"></g>
                                <g id="donut-chart-labels"></g>
                                {{-- Center Info Group --}}
                                <g id="donut-center-group" class="pointer-events-none select-none">
                                    <circle cx="190" cy="190" r="64" fill="#ffffff" class="transition-all duration-300" />
                                    <text id="donut-center-title" x="190" y="174" text-anchor="middle" font-size="11" font-weight="600" fill="#64748b" class="font-sans">
                                        Total
                                    </text>
                                    <text id="donut-center-nominal" x="190" y="196" text-anchor="middle" font-size="13.5" font-weight="900" fill="#0f172a" class="font-sans tracking-tight">
                                        Rp 0
                                    </text>
                                    <text id="donut-center-sub" x="190" y="214" text-anchor="middle" font-size="10" font-weight="600" fill="#94a3b8" class="font-sans">
                                        0 Transaksi
                                    </text>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- ── Bagian Bawah: Rincian Persentase dengan Kategori nya ── --}}
                <div class="border-t border-gray-100 pt-5 mt-4">
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="text-xs sm:text-sm font-extrabold text-gray-900 tracking-tight">
                                Rincian Persentase per Kategori
                            </h3>
                        </div>
                        <span id="chart-category-count-badge" class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-gray-100 text-gray-600">
                            0 Kategori
                        </span>
                    </div>

                    {{-- Grid Kartu Persentase Kategori --}}
                    <div id="chart-percentage-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3.5">
                        {{-- Diisi secara dinamis melalui JavaScript renderCategoryStatsChart() --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Akhir Konten Sub Menu 2: Statistik --}}

    {{-- ── KONTEN SUB MENU 3: LAPORAN KEUANGAN (SUBTOTAL PER KATEGORI & SUBKATEGORI) ── --}}
    <div id="submenu-content-laporan" class="space-y-3.5 sm:space-y-6 {{ !$isLaporanActive ? 'hidden' : '' }}">

        {{-- Print Styles CSS for Official Financial Report --}}
        <style>
            @media print {
                body {
                    background-color: #ffffff !important;
                    color: #000000 !important;
                    font-size: 11pt !important;
                }
                aside, nav, header, #sidebar, #mobile-nav,
                .subnav-tab-btn, #btn-subnav-riwayat, #btn-subnav-statistik, #btn-subnav-laporan,
                .no-print, button, input, .chart-type-btn {
                    display: none !important;
                }
                #submenu-content-laporan {
                    display: block !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100% !important;
                }
                .laporan-print-header {
                    display: block !important;
                    margin-bottom: 20px !important;
                    text-align: center !important;
                    border-bottom: 2px solid #334155 !important;
                    padding-bottom: 12px !important;
                }
                .laporan-table {
                    border-collapse: collapse !important;
                    width: 100% !important;
                }
                .laporan-table th, .laporan-table td {
                    border: 1px solid #cbd5e1 !important;
                    padding: 6px 10px !important;
                }
                .laporan-card {
                    box-shadow: none !important;
                    border: 1px solid #94a3b8 !important;
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                    margin-bottom: 20px !important;
                }
            }
        </style>

        {{-- Print-only Official Header --}}
        <div class="hidden laporan-print-header text-center">
            <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase">LAPORAN KEUANGAN PERTANIAN</h1>
            <h2 class="text-sm font-semibold text-gray-700">Rekapitulasi Subtotal Per Kategori & Subkategori</h2>
            <p class="text-xs text-gray-500 mt-1">
                Periode: <span class="font-bold print-label-periode">{{ $periode === 'bulan_ini' ? 'Bulan Ini' : ($periode === 'tahun_ini' ? 'Tahun Ini' : 'Semua Periode') }}</span> • Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
            </p>
        </div>

        {{-- Header Kartu Laporan & Kontrol Filter --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-3.5 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 bg-gradient-to-r from-slate-50/70 via-white to-blue-50/20 no-print">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <h2 class="text-sm sm:text-base font-extrabold text-gray-900 tracking-tight">
                        Laporan Subtotal Keuangan
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-0.5 sm:ml-9">
                    Ringkasan pembukuan hanya berupa subtotal per kategori dan subkategori untuk periode yang aktif.
                </p>
            </div>

            {{-- Filter Tipe Laporan & Tombol Aksi --}}
            <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
                {{-- Segmented View Filter (Semua / Pengeluaran / Pemasukan) --}}
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-gray-200">
                    <button type="button"
                            onclick="setLaporanTypeFilter('semua')"
                            id="laporan-filter-semua"
                            class="laporan-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-900 shadow-xs">
                        Semua
                    </button>
                    <button type="button"
                            onclick="setLaporanTypeFilter('pengeluaran')"
                            id="laporan-filter-pengeluaran"
                            class="laporan-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                        Pengeluaran
                    </button>
                    <button type="button"
                            onclick="setLaporanTypeFilter('pemasukan')"
                            id="laporan-filter-pemasukan"
                            class="laporan-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                        Pemasukan
                    </button>
                </div>

            </div>
        </div>

        {{-- Summary Metric Cards for Report --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4">
            {{-- Total Pengeluaran --}}
            <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-3 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">
                        Total Pengeluaran
                    </span>
                    <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 sm:mt-3">
                    <p id="laporan-summary-pengeluaran" class="text-sm sm:text-xl font-black text-rose-600 truncate tracking-tight">
                        {{ $laporanSubtotal['pengeluaran']['formatted_total'] ?? 'Rp 0' }}
                    </p>
                    <p id="laporan-count-pengeluaran" class="text-[11px] text-gray-500 mt-0.5">
                        {{ count($laporanSubtotal['pengeluaran']['categories'] ?? []) }} Kategori Terdaftar
                    </p>
                </div>
            </div>

            {{-- Total Pemasukan --}}
            <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-3 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">
                        Total Pemasukan
                    </span>
                    <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 sm:mt-3">
                    <p id="laporan-summary-pemasukan" class="text-sm sm:text-xl font-black text-emerald-600 truncate tracking-tight">
                        {{ $laporanSubtotal['pemasukan']['formatted_total'] ?? 'Rp 0' }}
                    </p>
                    <p id="laporan-count-pemasukan" class="text-[11px] text-gray-500 mt-0.5">
                        {{ count($laporanSubtotal['pemasukan']['categories'] ?? []) }} Kategori Terdaftar
                    </p>
                </div>
            </div>

            {{-- Saldo Bersih --}}
            <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200 p-3 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">
                        Saldo Bersih
                    </span>
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 sm:mt-3">
                    <p id="laporan-summary-saldo" class="text-sm sm:text-xl font-black truncate tracking-tight {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                        {{ $laporanSubtotal['ringkasan']['formatted_saldo_bersih'] ?? 'Rp 0' }}
                    </p>
                    <p id="laporan-status-saldo" class="text-[11px] font-semibold mt-0.5 {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? '✓ Arus Kas Surplus / Sehat' : '⚠ Arus Kas Defisit' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Kotak Pencarian Cepat Kategori/Subkategori --}}
        <div class="flex items-center justify-between gap-3 no-print">
            <div class="relative w-full sm:w-80">
                <input type="text"
                       id="input-search-laporan"
                       oninput="filterLaporanSearch(this.value)"
                       placeholder="Cari kategori atau subkategori..."
                       class="w-full pl-9 pr-3.5 py-2 text-xs sm:text-sm rounded-xl border border-gray-200 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <span id="laporan-filter-indicator" class="text-xs text-gray-500 hidden sm:inline">
                Menampilkan seluruh subtotal kategori & subkategori
            </span>
        </div>

        {{-- Empty State (Jika tidak ada transaksi pada filter periode) --}}
        <div id="laporan-empty-state" class="p-8 sm:p-12 text-center bg-surface rounded-2xl border border-gray-200 shadow-xs {{ ($totalTransaksi > 0) ? 'hidden' : '' }}">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xs sm:text-sm font-bold text-gray-800" id="laporan-empty-title">Belum Ada Data Transaksi</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto" id="laporan-empty-desc">
                Belum ada catatan transaksi pada periode ini untuk menghitung subtotal per kategori dan subkategori.
            </p>
        </div>

        {{-- Area Konten Laporan Subtotal (Tabel Pengeluaran & Pemasukan) --}}
        <div id="laporan-tables-container" class="space-y-4 sm:space-y-6 {{ ($totalTransaksi === 0) ? 'hidden' : '' }}">

            {{-- ── TABEL 1: SUB-TOTAL PENGELUARAN ── --}}
            <div id="laporan-section-pengeluaran" class="laporan-card bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                {{-- Table Section Header --}}
                <div class="p-3.5 sm:p-4.5 border-b border-gray-200 bg-gradient-to-r from-rose-50/70 to-white flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-gray-900 tracking-tight uppercase">
                            Subtotal Pengeluaran (Beban Operasional)
                        </h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium hidden sm:inline">Total Beban:</span>
                        <span id="laporan-badge-pengeluaran-total" class="px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-200">
                            {{ $laporanSubtotal['pengeluaran']['formatted_total'] ?? 'Rp 0' }}
                        </span>
                    </div>
                </div>

                {{-- Table Pengeluaran --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm laporan-table">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider border-b border-gray-200">
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Kategori & Subkategori</th>
                                <th class="py-2.5 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Banyak Transaksi</th>
                                <th class="py-2.5 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Proporsi</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4 text-right whitespace-nowrap">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-tbody-pengeluaran" class="divide-y divide-gray-100">
                            @forelse($laporanSubtotal['pengeluaran']['categories'] ?? [] as $cIdx => $cat)
                                {{-- Baris Kategori Utama (Parent) --}}
                                <tr class="laporan-cat-row bg-slate-50/60 hover:bg-slate-100/70 transition-colors font-bold text-gray-900 border-t border-gray-200"
                                    data-cat-type="pengeluaran"
                                    data-cat-name="{{ strtolower($cat['nama']) }}">
                                    <td class="py-2.5 sm:py-3 px-3 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            @if(count($cat['subkategori']) > 0)
                                                <button type="button"
                                                        onclick="toggleLaporanCategory('pengeluaran-cat-{{ $cIdx }}')"
                                                        class="no-print p-0.5 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 transition-transform">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 rotate-90" id="icon-cat-pengeluaran-cat-{{ $cIdx }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="w-4 h-4 inline-block"></span>
                                            @endif
                                            <span class="text-rose-600 font-extrabold text-sm sm:text-base">▪</span>
                                            <span class="font-extrabold text-gray-900">{{ $cat['nama'] }}</span>
                                            <span class="no-print text-[10px] font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">
                                                {{ count($cat['subkategori']) }} sub
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center text-gray-600 font-medium">
                                        {{ $cat['jumlah_transaksi'] }}x
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                            {{ $cat['persentase'] }}%
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-3 sm:px-4 text-right font-black text-rose-600">
                                        {{ $cat['formatted_subtotal'] }}
                                    </td>
                                </tr>

                                {{-- Baris-baris Subkategori & Sub-subkategori (Children) --}}
                                @foreach($cat['subkategori'] as $sIdx => $sub)
                                    @if(!empty($sub['has_children']) && count($sub['children']) > 0)
                                        {{-- Parent Subkategori (misal: Pemupukan) --}}
                                        <tr class="laporan-sub-row laporan-sub-parent-row group bg-gray-50/60 hover:bg-gray-100/70 font-semibold text-gray-800 pengeluaran-cat-{{ $cIdx }}"
                                            data-cat-type="pengeluaran"
                                            data-sub-name="{{ strtolower($sub['nama']) }}"
                                            data-parent-name="{{ strtolower($cat['nama']) }}"
                                            data-is-sub-parent="true">
                                            <td class="py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-900" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 font-mono text-xs select-none">├─</span>
                                                    <span class="font-bold text-gray-800">{{ $sub['nama'] }}</span>
                                                    <span class="no-print text-[10px] font-semibold text-gray-500 bg-white border border-gray-200 px-1.5 py-0.5 rounded shadow-2xs">
                                                        {{ count($sub['children']) }} sub-item
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-700">
                                                {{ $sub['jumlah_transaksi'] }}x
                                            </td>
                                            <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-600">
                                                {{ $sub['persentase_kategori'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                            </td>
                                            <td class="py-2 px-3 sm:px-4 text-right font-bold text-gray-900 text-xs sm:text-sm">
                                                {{ $sub['formatted_subtotal'] }}
                                            </td>
                                        </tr>

                                        {{-- Sub-subkategori (Children, misal: Pupuk & HOK Pemupukan) --}}
                                        @foreach($sub['children'] as $child)
                                            <tr class="laporan-sub-row laporan-sub-child-row group hover:bg-rose-50/30 transition-colors pengeluaran-cat-{{ $cIdx }}"
                                                data-cat-type="pengeluaran"
                                                data-sub-name="{{ strtolower($child['nama']) }}"
                                                data-parent-sub-name="{{ strtolower($sub['nama']) }}"
                                                data-parent-name="{{ strtolower($cat['nama']) }}"
                                                data-is-child="true">
                                                <td class="py-1.5 sm:py-2 pl-16 sm:pl-24 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-600" style="padding-left: clamp(4rem, 10vw, 5.75rem);">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                                        <span class="text-gray-700 font-medium">
                                                            {{ $child['nama'] }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                                    {{ $child['jumlah_transaksi'] }}x
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                                    {{ $child['persentase_parent'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari sub</span>
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-medium text-gray-700 text-xs sm:text-sm">
                                                    {{ $child['formatted_subtotal'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- Subkategori tunggal tanpa anak --}}
                                        <tr class="laporan-sub-row group hover:bg-rose-50/20 transition-colors pengeluaran-cat-{{ $cIdx }}"
                                            data-cat-type="pengeluaran"
                                            data-sub-name="{{ strtolower($sub['nama']) }}"
                                            data-parent-name="{{ strtolower($cat['nama']) }}">
                                            <td class="py-1.5 sm:py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-700" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                                    <span class="{{ $sub['is_tanpa_subkategori'] ? 'italic text-gray-400' : 'text-gray-700 font-medium' }}">
                                                        {{ $sub['nama'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                                {{ $sub['jumlah_transaksi'] }}x
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                                {{ $sub['persentase_kategori'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-semibold text-gray-800 text-xs sm:text-sm">
                                                {{ $sub['formatted_subtotal'] }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-xs italic">
                                        Tidak ada pengeluaran pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-rose-50/80 border-t-2 border-rose-200 font-black text-rose-900 text-xs sm:text-sm">
                                <td class="py-3 px-3 sm:px-4 uppercase tracking-wider font-extrabold" colspan="3">
                                    TOTAL PENGELUARAN
                                </td>
                                <td id="laporan-tfoot-pengeluaran-total" class="py-3 px-3 sm:px-4 text-right font-black text-sm sm:text-base text-rose-700">
                                    {{ $laporanSubtotal['pengeluaran']['formatted_total'] ?? 'Rp 0' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ── TABEL 2: SUB-TOTAL PEMASUKAN ── --}}
            <div id="laporan-section-pemasukan" class="laporan-card bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                {{-- Table Section Header --}}
                <div class="p-3.5 sm:p-4.5 border-b border-gray-200 bg-gradient-to-r from-emerald-50/70 to-white flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-gray-900 tracking-tight uppercase">
                            Subtotal Pemasukan (Pendapatan Usaha Tani)
                        </h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium hidden sm:inline">Total Pendapatan:</span>
                        <span id="laporan-badge-pemasukan-total" class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ $laporanSubtotal['pemasukan']['formatted_total'] ?? 'Rp 0' }}
                        </span>
                    </div>
                </div>

                {{-- Table Pemasukan --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm laporan-table">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider border-b border-gray-200">
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Kategori & Subkategori</th>
                                <th class="py-2.5 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Banyak Transaksi</th>
                                <th class="py-2.5 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Proporsi</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4 text-right whitespace-nowrap">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-tbody-pemasukan" class="divide-y divide-gray-100">
                            @forelse($laporanSubtotal['pemasukan']['categories'] ?? [] as $cIdx => $cat)
                                {{-- Baris Kategori Utama (Parent) --}}
                                <tr class="laporan-cat-row bg-slate-50/60 hover:bg-slate-100/70 transition-colors font-bold text-gray-900 border-t border-gray-200"
                                    data-cat-type="pemasukan"
                                    data-cat-name="{{ strtolower($cat['nama']) }}">
                                    <td class="py-2.5 sm:py-3 px-3 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            @if(count($cat['subkategori']) > 0)
                                                <button type="button"
                                                        onclick="toggleLaporanCategory('pemasukan-cat-{{ $cIdx }}')"
                                                        class="no-print p-0.5 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 transition-transform">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 rotate-90" id="icon-cat-pemasukan-cat-{{ $cIdx }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="w-4 h-4 inline-block"></span>
                                            @endif
                                            <span class="text-emerald-600 font-extrabold text-sm sm:text-base">▪</span>
                                            <span class="font-extrabold text-gray-900">{{ $cat['nama'] }}</span>
                                            <span class="no-print text-[10px] font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">
                                                {{ count($cat['subkategori']) }} sub
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center text-gray-600 font-medium">
                                        {{ $cat['jumlah_transaksi'] }}x
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                            {{ $cat['persentase'] }}%
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-3 sm:px-4 text-right font-black text-emerald-600">
                                        {{ $cat['formatted_subtotal'] }}
                                    </td>
                                </tr>

                                {{-- Baris-baris Subkategori & Sub-subkategori (Children) --}}
                                @foreach($cat['subkategori'] as $sIdx => $sub)
                                    @if(!empty($sub['has_children']) && count($sub['children']) > 0)
                                        {{-- Parent Subkategori --}}
                                        <tr class="laporan-sub-row laporan-sub-parent-row group bg-gray-50/60 hover:bg-gray-100/70 font-semibold text-gray-800 pemasukan-cat-{{ $cIdx }}"
                                            data-cat-type="pemasukan"
                                            data-sub-name="{{ strtolower($sub['nama']) }}"
                                            data-parent-name="{{ strtolower($cat['nama']) }}"
                                            data-is-sub-parent="true">
                                            <td class="py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-900" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 font-mono text-xs select-none">├─</span>
                                                    <span class="font-bold text-gray-800">{{ $sub['nama'] }}</span>
                                                    <span class="no-print text-[10px] font-semibold text-gray-500 bg-white border border-gray-200 px-1.5 py-0.5 rounded shadow-2xs">
                                                        {{ count($sub['children']) }} sub-item
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-700">
                                                {{ $sub['jumlah_transaksi'] }}x
                                            </td>
                                            <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-600">
                                                {{ $sub['persentase_kategori'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                            </td>
                                            <td class="py-2 px-3 sm:px-4 text-right font-bold text-gray-900 text-xs sm:text-sm">
                                                {{ $sub['formatted_subtotal'] }}
                                            </td>
                                        </tr>

                                        {{-- Sub-subkategori (Children) --}}
                                        @foreach($sub['children'] as $child)
                                            <tr class="laporan-sub-row laporan-sub-child-row group hover:bg-emerald-50/30 transition-colors pemasukan-cat-{{ $cIdx }}"
                                                data-cat-type="pemasukan"
                                                data-sub-name="{{ strtolower($child['nama']) }}"
                                                data-parent-sub-name="{{ strtolower($sub['nama']) }}"
                                                data-parent-name="{{ strtolower($cat['nama']) }}"
                                                data-is-child="true">
                                                <td class="py-1.5 sm:py-2 pl-16 sm:pl-24 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-600" style="padding-left: clamp(4rem, 10vw, 5.75rem);">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                                        <span class="text-gray-700 font-medium">
                                                            {{ $child['nama'] }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                                    {{ $child['jumlah_transaksi'] }}x
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                                    {{ $child['persentase_parent'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari sub</span>
                                                </td>
                                                <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-medium text-gray-700 text-xs sm:text-sm">
                                                    {{ $child['formatted_subtotal'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- Subkategori tunggal tanpa anak --}}
                                        <tr class="laporan-sub-row group hover:bg-emerald-50/20 transition-colors pemasukan-cat-{{ $cIdx }}"
                                            data-cat-type="pemasukan"
                                            data-sub-name="{{ strtolower($sub['nama']) }}"
                                            data-parent-name="{{ strtolower($cat['nama']) }}">
                                            <td class="py-1.5 sm:py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-700" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                                    <span class="{{ $sub['is_tanpa_subkategori'] ? 'italic text-gray-400' : 'text-gray-700 font-medium' }}">
                                                        {{ $sub['nama'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                                {{ $sub['jumlah_transaksi'] }}x
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                                {{ $sub['persentase_kategori'] }}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                            </td>
                                            <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-semibold text-gray-800 text-xs sm:text-sm">
                                                {{ $sub['formatted_subtotal'] }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-xs italic">
                                        Tidak ada pemasukan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-emerald-50/80 border-t-2 border-emerald-200 font-black text-emerald-900 text-xs sm:text-sm">
                                <td class="py-3 px-3 sm:px-4 uppercase tracking-wider font-extrabold" colspan="3">
                                    TOTAL PEMASUKAN
                                </td>
                                <td id="laporan-tfoot-pemasukan-total" class="py-3 px-3 sm:px-4 text-right font-black text-sm sm:text-base text-emerald-700">
                                    {{ $laporanSubtotal['pemasukan']['formatted_total'] ?? 'Rp 0' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ── REKAPITULASI AKHIR (ARUS KAS BERSIH) ── --}}
            <div id="laporan-section-rekap-bersih" class="laporan-card bg-surface rounded-2xl border border-gray-200 shadow-xs p-4 sm:p-6 bg-gradient-to-br from-slate-50 to-emerald-50/30">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-200/80 pb-3 mb-3">
                    <div>
                        <h4 class="text-xs sm:text-sm font-black text-gray-900 uppercase tracking-wider">
                            Ringkasan Arus Kas Bersih
                        </h4>
                        <p class="text-xs text-gray-500">
                            Selisih total subtotal pemasukan dikurangi subtotal pengeluaran.
                        </p>
                    </div>
                    <span id="laporan-rekap-status-badge" class="px-3 py-1 rounded-full text-xs font-bold self-start sm:self-auto {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                        {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? 'SURPLUS' : 'DEFISIT' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs sm:text-sm">
                    <div class="p-3 rounded-xl bg-white border border-gray-200/70 flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Total Pemasukan:</span>
                        <span id="laporan-rekap-pemasukan" class="font-extrabold text-emerald-600">
                            {{ $laporanSubtotal['pemasukan']['formatted_total'] ?? 'Rp 0' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-gray-200/70 flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Total Pengeluaran:</span>
                        <span id="laporan-rekap-pengeluaran" class="font-extrabold text-rose-600">
                            {{ $laporanSubtotal['pengeluaran']['formatted_total'] ?? 'Rp 0' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-gray-200/70 flex items-center justify-between ring-2 ring-emerald-500/20">
                        <span class="text-gray-900 font-bold">Saldo Bersih:</span>
                        <span id="laporan-rekap-saldo" class="font-black text-sm sm:text-base {{ ($laporanSubtotal['ringkasan']['saldo_bersih'] ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $laporanSubtotal['ringkasan']['formatted_saldo_bersih'] ?? 'Rp 0' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>
    {{-- Akhir Konten Sub Menu 3: Laporan --}}

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
                    <div class="flex items-center justify-between mb-1">
                        <label for="create-select-sub-kategori" class="block text-xs font-semibold text-gray-700">
                            Sub-Kategori / Sub-Sub
                        </label>
                        <span class="text-[11px] text-gray-400">Pilih atau buat baru</span>
                    </div>
                    <div class="space-y-2">
                        <select id="create-select-sub-kategori"
                                onchange="handleSubCategorySelection('create')"
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">— Tanpa Sub-Kategori —</option>
                        </select>
                        <input type="text"
                               id="create-input-sub-kategori"
                               name="sub_kategori"
                               oninput="handleSubCategoryInput('create')"
                               placeholder="Atau ketik nama sub-kategori baru..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <p id="create-sub-kategori-helper" class="text-[11px] text-emerald-700 font-medium flex items-center gap-1.5 hidden bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Sub-kategori baru ini akan otomatis tersimpan ke master kategori saat disimpan.</span>
                        </p>
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
                        <input type="text"
                               inputmode="numeric"
                               id="create-nominal"
                               name="nominal"
                               required
                               placeholder="50.000"
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
                    <div class="flex items-center justify-between mb-1">
                        <label for="edit-select-sub-kategori" class="block text-xs font-semibold text-gray-700">
                            Sub-Kategori / Sub-Sub
                        </label>
                        <span class="text-[11px] text-gray-400">Pilih atau buat baru</span>
                    </div>
                    <div class="space-y-2">
                        <select id="edit-select-sub-kategori"
                                onchange="handleSubCategorySelection('edit')"
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">— Tanpa Sub-Kategori —</option>
                        </select>
                        <input type="text"
                               id="edit-input-sub-kategori"
                               name="sub_kategori"
                               oninput="handleSubCategoryInput('edit')"
                               placeholder="Atau ketik nama sub-kategori baru..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <p id="edit-sub-kategori-helper" class="text-[11px] text-emerald-700 font-medium flex items-center gap-1.5 hidden bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Sub-kategori baru ini akan otomatis tersimpan ke master kategori saat disimpan.</span>
                        </p>
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
                        <input type="text"
                               inputmode="numeric"
                               id="edit-nominal"
                               name="nominal"
                               required
                               placeholder="50.000"
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
    let currentEditTransaction = null;
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
        if (id === 'modal-edit-transaksi') {
            currentEditTransaction = null;
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
        const subHelper = document.getElementById(`${prefix}-sub-kategori-helper`);

        if (!catSelect || !subContainer || !subSelect) return;

        const selectedCatName = catSelect.value;
        const currentCat = appCategories.find(c => c.nama === selectedCatName);

        if (selectedCatName) {
            subContainer.classList.remove('hidden');
            subSelect.innerHTML = '';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '— Tanpa Sub-Kategori —';
            subSelect.appendChild(defaultOpt);

            let matchFound = false;

            if (currentCat && currentCat.subcategories && currentCat.subcategories.length > 0) {
                currentCat.subcategories.forEach(sub => {
                    const subSubs = sub.subcategories || [];
                    if (subSubs.length > 0) {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = `📂 Sub: ${sub.nama}`;

                        const optSelf = document.createElement('option');
                        optSelf.value = sub.nama;
                        optSelf.textContent = `📂 ${sub.nama} (Utama)`;
                        if (presetSubCatName && optSelf.value.toLowerCase() === presetSubCatName.toLowerCase()) {
                            optSelf.selected = true;
                            matchFound = true;
                        }
                        optgroup.appendChild(optSelf);

                        subSubs.forEach(subSub => {
                            const opt = document.createElement('option');
                            opt.value = sub.nama + ' › ' + subSub.nama;
                            opt.textContent = `↳ 🏷️ [Sub-Sub] ${subSub.nama}`;
                            if (presetSubCatName && opt.value.toLowerCase() === presetSubCatName.toLowerCase()) {
                                opt.selected = true;
                                matchFound = true;
                            }
                            optgroup.appendChild(opt);
                        });
                        subSelect.appendChild(optgroup);
                    } else {
                        const opt = document.createElement('option');
                        opt.value = sub.nama;
                        opt.textContent = `📂 [Sub] ${sub.nama}`;
                        if (presetSubCatName && sub.nama.toLowerCase() === presetSubCatName.toLowerCase()) {
                            opt.selected = true;
                            matchFound = true;
                        }
                        subSelect.appendChild(opt);
                    }
                });
            }

            const newOpt = document.createElement('option');
            newOpt.value = '__NEW__';
            newOpt.textContent = '➕ Tambah / Ketik Sub-Kategori Baru...';
            subSelect.appendChild(newOpt);

            if (presetSubCatName) {
                subInput.value = presetSubCatName;
                if (!matchFound) {
                    subSelect.value = '__NEW__';
                    if (subHelper) subHelper.classList.remove('hidden');
                } else if (subHelper) {
                    subHelper.classList.add('hidden');
                }
            } else {
                subSelect.value = '';
                subInput.value = '';
                if (subHelper) subHelper.classList.add('hidden');
            }
        } else {
            subContainer.classList.add('hidden');
            subSelect.innerHTML = '<option value="">— Tanpa Sub-Kategori —</option>';
            subInput.value = '';
            if (subHelper) subHelper.classList.add('hidden');
        }
    }

    function handleSubCategorySelection(prefix) {
        const subSelect = document.getElementById(`${prefix}-select-sub-kategori`);
        const subInput = document.getElementById(`${prefix}-input-sub-kategori`);
        const subHelper = document.getElementById(`${prefix}-sub-kategori-helper`);
        if (!subSelect || !subInput) return;

        if (subSelect.value === '__NEW__') {
            subInput.value = '';
            subInput.focus();
            if (subHelper) subHelper.classList.remove('hidden');
        } else if (subSelect.value) {
            subInput.value = subSelect.value;
            if (subHelper) subHelper.classList.add('hidden');
        } else {
            subInput.value = '';
            if (subHelper) subHelper.classList.add('hidden');
        }
    }

    function handleSubCategoryInput(prefix) {
        const subSelect = document.getElementById(`${prefix}-select-sub-kategori`);
        const subInput = document.getElementById(`${prefix}-input-sub-kategori`);
        const subHelper = document.getElementById(`${prefix}-sub-kategori-helper`);
        if (!subSelect || !subInput) return;

        const val = (subInput.value || '').trim();
        let matchFound = false;

        for (let i = 0; i < subSelect.options.length; i++) {
            const opt = subSelect.options[i];
            if (opt.value && opt.value !== '__NEW__' && opt.value.toLowerCase() === val.toLowerCase()) {
                subSelect.value = opt.value;
                matchFound = true;
                break;
            }
        }

        if (!matchFound) {
            if (val.length > 0) {
                const newOpt = subSelect.querySelector('option[value="__NEW__"]');
                if (newOpt) subSelect.value = '__NEW__';
                if (subHelper) subHelper.classList.remove('hidden');
            } else {
                subSelect.value = '';
                if (subHelper) subHelper.classList.add('hidden');
            }
        } else {
            if (subHelper) subHelper.classList.add('hidden');
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
    function openEditTransactionModal(itemOrId) {
        let item = itemOrId;
        if (typeof itemOrId === 'number' || typeof itemOrId === 'string') {
            item = currentTransaksiList.find(t => t.raw_id == itemOrId || t.id == itemOrId);
        }
        if (!item) return;

        currentEditTransaction = item;
        const form = document.getElementById('form-edit-transaksi');
        form.action = '/keuangan/' + (item.raw_id || item.id);

        setEditType(item.tipe, item.kategori, item.sub_kategori);
        document.getElementById('edit-judul').value = item.judul || '';
        document.getElementById('edit-nominal').value = formatRupiahNumber(item.nominal || '');

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

        const subnavBadge = document.getElementById('subnav-badge-riwayat');
        if (subnavBadge) subnavBadge.innerText = data.totalTransaksi;

        // 3. Update Categories
        if (data.categories) {
            appCategories = data.categories;
            renderCategoryTree(appCategories);
        }

        // 4. Update Transactions
        if (data.transaksiList) {
            currentTransaksiList = data.transaksiList;
            renderTransactionsTable(currentTransaksiList);
            if (typeof renderCategoryStatsChart === 'function') {
                renderCategoryStatsChart(currentTransaksiList);
            }
            if (typeof renderLaporanKeuangan === 'function') {
                renderLaporanKeuangan(currentTransaksiList);
            }
        }

        const printPeriodeLabel = document.querySelector('.print-label-periode');
        if (printPeriodeLabel) {
            printPeriodeLabel.textContent = periode === 'bulan_ini' ? 'Bulan Ini' : (periode === 'tahun_ini' ? 'Tahun Ini' : 'Semua Periode');
        }
    }
    window.loadKeuanganData = loadKeuanganData;

    // ── Helper: Format Rupiah ──
    function formatRupiah(num) {
        return 'Rp ' + Math.round(Number(num || 0)).toLocaleString('id-ID');
    }

    function formatRupiahNumber(val) {
        if (!val && val !== 0) return '';
        const digits = val.toString().replace(/[^0-9]/g, '');
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function bindRupiahInput(inputEl) {
        if (!inputEl) return;

        inputEl.addEventListener('input', () => {
            const cursorPosition = inputEl.selectionStart;
            const prevVal = inputEl.value;
            const digitsBeforeCursor = prevVal.slice(0, cursorPosition).replace(/\D/g, '').length;

            const formatted = formatRupiahNumber(prevVal);
            inputEl.value = formatted;

            let newCursorPos = 0;
            let digitCount = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) digitCount++;
                if (digitCount === digitsBeforeCursor) {
                    newCursorPos = i + 1;
                    break;
                }
            }
            if (digitCount < digitsBeforeCursor || formatted.length === 0) {
                newCursorPos = formatted.length;
            }
            inputEl.setSelectionRange(newCursorPos, newCursorPos);
        });

        inputEl.addEventListener('blur', () => {
            inputEl.value = formatRupiahNumber(inputEl.value);
        });
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

    // ── Manajemen Sub Menu Keuangan (Riwayat vs Statistik) ──
    // ── Manajemen Sub Menu Keuangan (Riwayat vs Statistik vs Laporan) ──
    let currentSubmenu = '{{ request()->query("tab", "riwayat") }}';

    function setKeuanganSubmenu(tab) {
        currentSubmenu = tab;
        const contentRiwayat = document.getElementById('submenu-content-riwayat');
        const contentStatistik = document.getElementById('submenu-content-statistik');
        const contentLaporan = document.getElementById('submenu-content-laporan');
        const btnRiwayat = document.getElementById('btn-subnav-riwayat');
        const btnStatistik = document.getElementById('btn-subnav-statistik');
        const btnLaporan = document.getElementById('btn-subnav-laporan');
        const sidebarRiwayat = document.getElementById('sidebar-link-keuangan-riwayat');
        const sidebarStatistik = document.getElementById('sidebar-link-keuangan-statistik');
        const sidebarLaporan = document.getElementById('sidebar-link-keuangan-laporan');

        // Hide all contents
        if (contentRiwayat) contentRiwayat.classList.add('hidden');
        if (contentStatistik) contentStatistik.classList.add('hidden');
        if (contentLaporan) contentLaporan.classList.add('hidden');

        // Reset all tab buttons
        const inactiveBtnClass = 'subnav-tab-btn flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2.5 text-xs sm:text-sm whitespace-nowrap font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent transition-all cursor-pointer';
        const activeBtnClass = 'subnav-tab-btn flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2.5 text-xs sm:text-sm whitespace-nowrap font-extrabold text-emerald-800 bg-emerald-50/70 border-b-2 border-emerald-600 rounded-t-xl transition-all cursor-pointer';

        if (btnRiwayat) {
            btnRiwayat.className = inactiveBtnClass;
            const icon = btnRiwayat.querySelector('svg');
            if (icon) icon.className = 'w-4 h-4 text-gray-400';
            const badge = document.getElementById('subnav-badge-riwayat');
            if (badge) badge.className = 'px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-gray-100 text-gray-600';
        }
        if (btnStatistik) {
            btnStatistik.className = inactiveBtnClass;
            const icon = btnStatistik.querySelector('svg');
            if (icon) icon.className = 'w-4 h-4 text-gray-400';
        }
        if (btnLaporan) {
            btnLaporan.className = inactiveBtnClass;
            const icon = btnLaporan.querySelector('svg');
            if (icon) icon.className = 'w-4 h-4 text-gray-400';
        }

        // Reset all sidebar links
        const resetSidebar = (el) => {
            if (!el) return;
            el.className = 'flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-text-secondary hover:text-text hover:bg-gray-50';
            const dot = el.querySelector('span');
            if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-gray-300';
        };
        const activateSidebar = (el) => {
            if (!el) return;
            el.className = 'flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-primary-dark font-semibold bg-emerald-100';
            const dot = el.querySelector('span');
            if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-primary';
        };

        resetSidebar(sidebarRiwayat);
        resetSidebar(sidebarStatistik);
        resetSidebar(sidebarLaporan);

        if (tab === 'laporan') {
            if (contentLaporan) contentLaporan.classList.remove('hidden');
            if (btnLaporan) {
                btnLaporan.className = activeBtnClass;
                const icon = btnLaporan.querySelector('svg');
                if (icon) icon.className = 'w-4 h-4 text-blue-600';
            }
            activateSidebar(sidebarLaporan);

            if (typeof renderLaporanKeuangan === 'function') {
                renderLaporanKeuangan(currentTransaksiList);
            }
        } else if (tab === 'statistik') {
            if (contentStatistik) contentStatistik.classList.remove('hidden');
            if (btnStatistik) {
                btnStatistik.className = activeBtnClass;
                const icon = btnStatistik.querySelector('svg');
                if (icon) icon.className = 'w-4 h-4 text-purple-600';
            }
            activateSidebar(sidebarStatistik);

            if (typeof renderCategoryStatsChart === 'function') {
                renderCategoryStatsChart(currentTransaksiList);
            }
        } else {
            // Default: riwayat
            if (contentRiwayat) contentRiwayat.classList.remove('hidden');
            if (btnRiwayat) {
                btnRiwayat.className = activeBtnClass;
                const icon = btnRiwayat.querySelector('svg');
                if (icon) icon.className = 'w-4 h-4 text-emerald-600';
                const badge = document.getElementById('subnav-badge-riwayat');
                if (badge) badge.className = 'px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-100 text-emerald-800';
            }
            activateSidebar(sidebarRiwayat);
        }

        try {
            const url = new URL(window.location.href);
            if (tab === 'statistik') {
                url.searchParams.set('tab', 'statistik');
            } else if (tab === 'laporan') {
                url.searchParams.set('tab', 'laporan');
            } else {
                url.searchParams.delete('tab');
            }
            window.history.replaceState(null, '', url.toString());
        } catch (e) {}
    }
    window.setKeuanganSubmenu = setKeuanganSubmenu;

    // ── Manajemen Laporan Keuangan (Rekap Subtotal Kategori & Subkategori) ──
    let currentLaporanTypeFilter = 'semua';
    let isLaporanSubCollapsed = false;

    function setLaporanTypeFilter(type) {
        currentLaporanTypeFilter = type;
        ['semua', 'pengeluaran', 'pemasukan'].forEach(t => {
            const btn = document.getElementById('laporan-filter-' + t);
            if (btn) {
                if (t === type) {
                    btn.className = 'laporan-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-900 shadow-xs';
                } else {
                    btn.className = 'laporan-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900';
                }
            }
        });

        const secPengeluaran = document.getElementById('laporan-section-pengeluaran');
        const secPemasukan = document.getElementById('laporan-section-pemasukan');
        const secRekap = document.getElementById('laporan-section-rekap-bersih');

        if (type === 'pengeluaran') {
            if (secPengeluaran) secPengeluaran.classList.remove('hidden');
            if (secPemasukan) secPemasukan.classList.add('hidden');
            if (secRekap) secRekap.classList.add('hidden');
        } else if (type === 'pemasukan') {
            if (secPengeluaran) secPengeluaran.classList.add('hidden');
            if (secPemasukan) secPemasukan.classList.remove('hidden');
            if (secRekap) secRekap.classList.add('hidden');
        } else {
            if (secPengeluaran) secPengeluaran.classList.remove('hidden');
            if (secPemasukan) secPemasukan.classList.remove('hidden');
            if (secRekap) secRekap.classList.remove('hidden');
        }

        // Re-apply search filter if any
        const searchInput = document.getElementById('input-search-laporan');
        if (searchInput && searchInput.value) {
            filterLaporanSearch(searchInput.value);
        }
    }
    window.setLaporanTypeFilter = setLaporanTypeFilter;

    function toggleAllLaporanAccordion() {
        isLaporanSubCollapsed = !isLaporanSubCollapsed;
        const subRows = document.querySelectorAll('.laporan-sub-row');
        const icon = document.getElementById('icon-toggle-all-laporan');
        const text = document.getElementById('text-toggle-all-laporan');

        subRows.forEach(row => {
            row.style.display = isLaporanSubCollapsed ? 'none' : '';
        });

        const catIcons = document.querySelectorAll('[id^="icon-cat-"]');
        catIcons.forEach(ic => {
            if (isLaporanSubCollapsed) {
                ic.classList.remove('rotate-90');
                ic.classList.add('rotate-0');
            } else {
                ic.classList.remove('rotate-0');
                ic.classList.add('rotate-90');
            }
        });

        if (text) text.textContent = isLaporanSubCollapsed ? 'Buka Sub' : 'Tutup Sub';
        if (icon) {
            if (isLaporanSubCollapsed) {
                icon.classList.remove('rotate-0');
                icon.classList.add('rotate-180');
            } else {
                icon.classList.remove('rotate-180');
                icon.classList.add('rotate-0');
            }
        }
    }
    window.toggleAllLaporanAccordion = toggleAllLaporanAccordion;

    function toggleLaporanCategory(catClass) {
        const rows = document.querySelectorAll('.' + catClass);
        const icon = document.getElementById('icon-cat-' + catClass);
        let isNowHidden = false;

        rows.forEach(r => {
            if (r.style.display === 'none') {
                r.style.display = '';
                isNowHidden = false;
            } else {
                r.style.display = 'none';
                isNowHidden = true;
            }
        });

        if (icon) {
            if (isNowHidden) {
                icon.classList.remove('rotate-90');
                icon.classList.add('rotate-0');
            } else {
                icon.classList.remove('rotate-0');
                icon.classList.add('rotate-90');
            }
        }
    }
    window.toggleLaporanCategory = toggleLaporanCategory;

    function filterLaporanSearch(query) {
        const q = (query || '').trim().toLowerCase();
        const catRows = document.querySelectorAll('.laporan-cat-row');
        const subRows = document.querySelectorAll('.laporan-sub-row');
        const indicator = document.getElementById('laporan-filter-indicator');

        if (!q) {
            catRows.forEach(cr => { cr.style.display = ''; });
            subRows.forEach(sr => { sr.style.display = isLaporanSubCollapsed ? 'none' : ''; });
            if (indicator) indicator.textContent = 'Menampilkan seluruh subtotal kategori & subkategori';
            return;
        }

        if (indicator) indicator.textContent = `Menyaring dengan kata kunci: "${query}"`;

        catRows.forEach(catRow => {
            const catName = (catRow.getAttribute('data-cat-name') || '').toLowerCase();
            const catMatches = catName.includes(q);
            const parentTable = catRow.closest('tbody');
            let hasMatchingChild = false;

            const relatedSubs = parentTable ? Array.from(parentTable.querySelectorAll(`.laporan-sub-row[data-parent-name="${catName}"]`)) : [];

            // Detect matched parent subcategories
            const parentSubMatched = {};
            relatedSubs.forEach(subRow => {
                const subName = (subRow.getAttribute('data-sub-name') || '').toLowerCase();
                const parentSubName = (subRow.getAttribute('data-parent-sub-name') || '').toLowerCase();
                const isChild = subRow.getAttribute('data-is-child') === 'true';
                const isSubParent = subRow.getAttribute('data-is-sub-parent') === 'true';

                if (isChild && subName.includes(q)) {
                    if (parentSubName) parentSubMatched[parentSubName] = true;
                }
                if (isSubParent && subName.includes(q)) {
                    parentSubMatched[subName] = true;
                }
            });

            relatedSubs.forEach(subRow => {
                const subName = (subRow.getAttribute('data-sub-name') || '').toLowerCase();
                const parentSubName = (subRow.getAttribute('data-parent-sub-name') || '').toLowerCase();
                const isChild = subRow.getAttribute('data-is-child') === 'true';
                const isSubParent = subRow.getAttribute('data-is-sub-parent') === 'true';

                let visible = false;
                if (catMatches) {
                    visible = true;
                } else if (isSubParent) {
                    visible = subName.includes(q) || !!parentSubMatched[subName];
                } else if (isChild) {
                    visible = subName.includes(q) || (parentSubName && parentSubName.includes(q));
                } else {
                    visible = subName.includes(q);
                }

                if (visible) {
                    subRow.style.display = '';
                    hasMatchingChild = true;
                } else {
                    subRow.style.display = 'none';
                }
            });

            if (catMatches || hasMatchingChild) {
                catRow.style.display = '';
            } else {
                catRow.style.display = 'none';
            }
        });
    }
    window.filterLaporanSearch = filterLaporanSearch;

    function calculateClientSubtotalReport(list) {
        const report = {
            pengeluaran: { total: 0, formatted_total: 'Rp 0', categories: [] },
            pemasukan: { total: 0, formatted_total: 'Rp 0', categories: [] },
            ringkasan: { total_pemasukan: 0, total_pengeluaran: 0, saldo_bersih: 0 }
        };

        ['pengeluaran', 'pemasukan'].forEach(tipe => {
            const group = (list || []).filter(t => t.tipe === tipe);
            let totalTipe = 0;
            const catMap = {};

            group.forEach(t => {
                const catName = (t.kategori || 'Lainnya').trim();
                const rawSub = (t.sub_kategori || '').trim();
                const nominal = parseFloat(t.nominal) || 0;
                totalTipe += nominal;

                if (!catMap[catName]) {
                    catMap[catName] = {
                        nama: catName,
                        subtotal: 0,
                        jumlah_transaksi: 0,
                        subkategoriMap: {}
                    };
                }
                catMap[catName].subtotal += nominal;
                catMap[catName].jumlah_transaksi += 1;

                if (rawSub === '') {
                    const subKey = '— (Tanpa Subkategori)';
                    if (!catMap[catName].subkategoriMap[subKey]) {
                        catMap[catName].subkategoriMap[subKey] = {
                            nama: subKey,
                            is_tanpa_subkategori: true,
                            subtotal: 0,
                            jumlah_transaksi: 0,
                            has_children: false,
                            childrenMap: {},
                            _direct_nominal: 0,
                            _direct_count: 0
                        };
                    }
                    catMap[catName].subkategoriMap[subKey].subtotal += nominal;
                    catMap[catName].subkategoriMap[subKey].jumlah_transaksi += 1;
                } else {
                    const parts = rawSub.split(/\s*[›>]\s*/);
                    const subName = parts[0].trim();
                    const childName = (parts.length > 1) ? parts.slice(1).join(' › ').trim() : null;

                    if (!catMap[catName].subkategoriMap[subName]) {
                        catMap[catName].subkategoriMap[subName] = {
                            nama: subName,
                            is_tanpa_subkategori: false,
                            subtotal: 0,
                            jumlah_transaksi: 0,
                            has_children: false,
                            childrenMap: {},
                            _direct_nominal: 0,
                            _direct_count: 0
                        };
                    }
                    catMap[catName].subkategoriMap[subName].subtotal += nominal;
                    catMap[catName].subkategoriMap[subName].jumlah_transaksi += 1;

                    if (childName) {
                        catMap[catName].subkategoriMap[subName].has_children = true;
                        if (!catMap[catName].subkategoriMap[subName].childrenMap[childName]) {
                            catMap[catName].subkategoriMap[subName].childrenMap[childName] = {
                                nama: childName,
                                subtotal: 0,
                                jumlah_transaksi: 0
                            };
                        }
                        catMap[catName].subkategoriMap[subName].childrenMap[childName].subtotal += nominal;
                        catMap[catName].subkategoriMap[subName].childrenMap[childName].jumlah_transaksi += 1;
                    } else {
                        catMap[catName].subkategoriMap[subName]._direct_nominal += nominal;
                        catMap[catName].subkategoriMap[subName]._direct_count += 1;
                    }
                }
            });

            const categories = Object.values(catMap).sort((a, b) => b.subtotal - a.subtotal);
            categories.forEach(c => {
                c.persentase = totalTipe > 0 ? Math.round((c.subtotal / totalTipe) * 1000) / 10 : 0;
                c.formatted_subtotal = formatRupiah(c.subtotal);

                const subs = Object.values(c.subkategoriMap).sort((a, b) => b.subtotal - a.subtotal);
                subs.forEach(s => {
                    s.persentase_kategori = c.subtotal > 0 ? Math.round((s.subtotal / c.subtotal) * 1000) / 10 : 0;
                    s.formatted_subtotal = formatRupiah(s.subtotal);

                    if (s.has_children && s.childrenMap) {
                        if (s._direct_nominal > 0) {
                            s.childrenMap['Lainnya / Umum'] = {
                                nama: 'Lainnya / Umum',
                                subtotal: s._direct_nominal,
                                jumlah_transaksi: s._direct_count
                            };
                        }
                        const children = Object.values(s.childrenMap).sort((a, b) => b.subtotal - a.subtotal);
                        children.forEach(ch => {
                            ch.persentase_parent = s.subtotal > 0 ? Math.round((ch.subtotal / s.subtotal) * 1000) / 10 : 0;
                            ch.formatted_subtotal = formatRupiah(ch.subtotal);
                        });
                        s.children = children;
                    } else {
                        s.children = [];
                    }
                    delete s.childrenMap;
                    delete s._direct_nominal;
                    delete s._direct_count;
                });
                c.subkategori = subs;
            });

            report[tipe].total = totalTipe;
            report[tipe].formatted_total = formatRupiah(totalTipe);
            report[tipe].categories = categories;
        });

        report.ringkasan.total_pemasukan = report.pemasukan.total;
        report.ringkasan.total_pengeluaran = report.pengeluaran.total;
        report.ringkasan.saldo_bersih = report.pemasukan.total - report.pengeluaran.total;
        report.ringkasan.formatted_total_pemasukan = formatRupiah(report.ringkasan.total_pemasukan);
        report.ringkasan.formatted_total_pengeluaran = formatRupiah(report.ringkasan.total_pengeluaran);
        report.ringkasan.formatted_saldo_bersih = formatRupiah(report.ringkasan.saldo_bersih);

        return report;
    }

    function renderLaporanKeuangan(list) {
        if (!list) list = currentTransaksiList || [];

        const emptyState = document.getElementById('laporan-empty-state');
        const tablesContainer = document.getElementById('laporan-tables-container');

        if (!list || list.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (tablesContainer) tablesContainer.classList.add('hidden');

            const sPengeluaran = document.getElementById('laporan-summary-pengeluaran');
            const cPengeluaran = document.getElementById('laporan-count-pengeluaran');
            const sPemasukan = document.getElementById('laporan-summary-pemasukan');
            const cPemasukan = document.getElementById('laporan-count-pemasukan');
            const sSaldo = document.getElementById('laporan-summary-saldo');
            const stSaldo = document.getElementById('laporan-status-saldo');

            if (sPengeluaran) sPengeluaran.textContent = 'Rp 0';
            if (cPengeluaran) cPengeluaran.textContent = '0 Kategori Terdaftar';
            if (sPemasukan) sPemasukan.textContent = 'Rp 0';
            if (cPemasukan) cPemasukan.textContent = '0 Kategori Terdaftar';
            if (sSaldo) {
                sSaldo.textContent = 'Rp 0';
                sSaldo.className = 'text-sm sm:text-xl font-black truncate tracking-tight text-emerald-700';
            }
            if (stSaldo) {
                stSaldo.textContent = '✓ Arus Kas Seimbang';
                stSaldo.className = 'text-[11px] font-semibold mt-0.5 text-emerald-600';
            }
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (tablesContainer) tablesContainer.classList.remove('hidden');

        const report = calculateClientSubtotalReport(list);

        // Update Summary Cards
        const elPengeluaran = document.getElementById('laporan-summary-pengeluaran');
        const elCountPengeluaran = document.getElementById('laporan-count-pengeluaran');
        const elPemasukan = document.getElementById('laporan-summary-pemasukan');
        const elCountPemasukan = document.getElementById('laporan-count-pemasukan');
        const elSaldo = document.getElementById('laporan-summary-saldo');
        const elStatusSaldo = document.getElementById('laporan-status-saldo');

        if (elPengeluaran) elPengeluaran.textContent = report.pengeluaran.formatted_total;
        if (elCountPengeluaran) elCountPengeluaran.textContent = `${report.pengeluaran.categories.length} Kategori Terdaftar`;
        if (elPemasukan) elPemasukan.textContent = report.pemasukan.formatted_total;
        if (elCountPemasukan) elCountPemasukan.textContent = `${report.pemasukan.categories.length} Kategori Terdaftar`;

        const isSurplus = report.ringkasan.saldo_bersih >= 0;
        if (elSaldo) {
            elSaldo.textContent = report.ringkasan.formatted_saldo_bersih;
            elSaldo.className = `text-sm sm:text-xl font-black truncate tracking-tight ${isSurplus ? 'text-emerald-700' : 'text-rose-600'}`;
        }
        if (elStatusSaldo) {
            elStatusSaldo.textContent = isSurplus ? '✓ Arus Kas Surplus / Sehat' : '⚠ Arus Kas Defisit';
            elStatusSaldo.className = `text-[11px] font-semibold mt-0.5 ${isSurplus ? 'text-emerald-600' : 'text-rose-600'}`;
        }

        // Update Section Header Badges & Table Footers
        const bPengeluaran = document.getElementById('laporan-badge-pengeluaran-total');
        const fPengeluaran = document.getElementById('laporan-tfoot-pengeluaran-total');
        const bPemasukan = document.getElementById('laporan-badge-pemasukan-total');
        const fPemasukan = document.getElementById('laporan-tfoot-pemasukan-total');

        if (bPengeluaran) bPengeluaran.textContent = report.pengeluaran.formatted_total;
        if (fPengeluaran) fPengeluaran.textContent = report.pengeluaran.formatted_total;
        if (bPemasukan) bPemasukan.textContent = report.pemasukan.formatted_total;
        if (fPemasukan) fPemasukan.textContent = report.pemasukan.formatted_total;

        // Update Bottom Net Cash Flow Card
        const rkPemasukan = document.getElementById('laporan-rekap-pemasukan');
        const rkPengeluaran = document.getElementById('laporan-rekap-pengeluaran');
        const rkSaldo = document.getElementById('laporan-rekap-saldo');
        const rkBadge = document.getElementById('laporan-rekap-status-badge');

        if (rkPemasukan) rkPemasukan.textContent = report.pemasukan.formatted_total;
        if (rkPengeluaran) rkPengeluaran.textContent = report.pengeluaran.formatted_total;
        if (rkSaldo) {
            rkSaldo.textContent = report.ringkasan.formatted_saldo_bersih;
            rkSaldo.className = `font-black text-sm sm:text-base ${isSurplus ? 'text-emerald-700' : 'text-rose-600'}`;
        }
        if (rkBadge) {
            rkBadge.textContent = isSurplus ? 'SURPLUS' : 'DEFISIT';
            rkBadge.className = `px-3 py-1 rounded-full text-xs font-bold self-start sm:self-auto ${isSurplus ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200'}`;
        }

        // Render Table Body for Pengeluaran
        const tbodyPengeluaran = document.getElementById('laporan-tbody-pengeluaran');
        if (tbodyPengeluaran) {
            tbodyPengeluaran.innerHTML = buildLaporanTbodyHtml(report.pengeluaran.categories, 'pengeluaran');
        }

        // Render Table Body for Pemasukan
        const tbodyPemasukan = document.getElementById('laporan-tbody-pemasukan');
        if (tbodyPemasukan) {
            tbodyPemasukan.innerHTML = buildLaporanTbodyHtml(report.pemasukan.categories, 'pemasukan');
        }

        // Re-apply current type filter & search
        setLaporanTypeFilter(currentLaporanTypeFilter);
    }
    window.renderLaporanKeuangan = renderLaporanKeuangan;

    function buildLaporanTbodyHtml(categories, type) {
        if (!categories || categories.length === 0) {
            const label = type === 'pengeluaran' ? 'pengeluaran' : 'pemasukan';
            return `<tr><td colspan="4" class="py-8 text-center text-gray-400 text-xs italic">Tidak ada ${label} pada periode ini.</td></tr>`;
        }

        const isExpense = (type === 'pengeluaran');
        const bulletColor = isExpense ? 'text-rose-600' : 'text-emerald-600';
        const subtotalColor = isExpense ? 'text-rose-600' : 'text-emerald-600';
        const pctBadge = isExpense ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-emerald-50 text-emerald-800 border-emerald-100';
        const childRowHover = isExpense ? 'hover:bg-rose-50/30' : 'hover:bg-emerald-50/30';
        const subRowHover = isExpense ? 'hover:bg-rose-50/20' : 'hover:bg-emerald-50/20';

        let html = '';
        categories.forEach((cat, cIdx) => {
            const catId = `${type}-cat-${cIdx}`;
            const subCount = cat.subkategori ? cat.subkategori.length : 0;
            const hasSubs = subCount > 0;
            const subDisplay = isLaporanSubCollapsed ? 'style="display: none;"' : '';
            const iconRotate = isLaporanSubCollapsed ? 'rotate-0' : 'rotate-90';

            html += `
                <tr class="laporan-cat-row bg-slate-50/60 hover:bg-slate-100/70 transition-colors font-bold text-gray-900 border-t border-gray-200"
                    data-cat-type="${type}"
                    data-cat-name="${escapeHtml(cat.nama.toLowerCase())}">
                    <td class="py-2.5 sm:py-3 px-3 sm:px-4">
                        <div class="flex items-center gap-2">
                            ${hasSubs ? `
                                <button type="button"
                                        onclick="toggleLaporanCategory('${catId}')"
                                        class="no-print p-0.5 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 transition-transform">
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 ${iconRotate}" id="icon-cat-${catId}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            ` : `<span class="w-4 h-4 inline-block"></span>`}
                            <span class="${bulletColor} font-extrabold text-sm sm:text-base">▪</span>
                            <span class="font-extrabold text-gray-900">${escapeHtml(cat.nama)}</span>
                            <span class="no-print text-[10px] font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">
                                ${subCount} sub
                            </span>
                        </div>
                    </td>
                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center text-gray-600 font-medium">
                        ${cat.jumlah_transaksi}x
                    </td>
                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold ${pctBadge} border">
                            ${cat.persentase}%
                        </span>
                    </td>
                    <td class="py-2.5 sm:py-3 px-3 sm:px-4 text-right font-black ${subtotalColor}">
                        ${cat.formatted_subtotal}
                    </td>
                </tr>
            `;

            if (cat.subkategori) {
                cat.subkategori.forEach(sub => {
                    const hasChildren = sub.has_children && sub.children && sub.children.length > 0;

                    if (hasChildren) {
                        html += `
                            <tr class="laporan-sub-row laporan-sub-parent-row group bg-gray-50/60 hover:bg-gray-100/70 font-semibold text-gray-800 ${catId}"
                                ${subDisplay}
                                data-cat-type="${type}"
                                data-sub-name="${escapeHtml(sub.nama.toLowerCase())}"
                                data-parent-name="${escapeHtml(cat.nama.toLowerCase())}"
                                data-is-sub-parent="true">
                                <td class="py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-900" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 font-mono text-xs select-none">├─</span>
                                        <span class="font-bold text-gray-800">${escapeHtml(sub.nama)}</span>
                                        <span class="no-print text-[10px] font-semibold text-gray-500 bg-white border border-gray-200 px-1.5 py-0.5 rounded shadow-2xs">
                                            ${sub.children.length} sub-item
                                        </span>
                                    </div>
                                </td>
                                <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-700">
                                    ${sub.jumlah_transaksi}x
                                </td>
                                <td class="py-2 px-2 sm:px-4 text-center text-xs font-semibold text-gray-600">
                                    ${sub.persentase_kategori}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                </td>
                                <td class="py-2 px-3 sm:px-4 text-right font-bold text-gray-900 text-xs sm:text-sm">
                                    ${sub.formatted_subtotal}
                                </td>
                            </tr>
                        `;

                        sub.children.forEach(child => {
                            html += `
                                <tr class="laporan-sub-row laporan-sub-child-row group ${childRowHover} transition-colors ${catId}"
                                    ${subDisplay}
                                    data-cat-type="${type}"
                                    data-sub-name="${escapeHtml(child.nama.toLowerCase())}"
                                    data-parent-sub-name="${escapeHtml(sub.nama.toLowerCase())}"
                                    data-parent-name="${escapeHtml(cat.nama.toLowerCase())}"
                                    data-is-child="true">
                                    <td class="py-1.5 sm:py-2 pl-16 sm:pl-24 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-600" style="padding-left: clamp(4rem, 10vw, 5.75rem);">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                            <span class="text-gray-700 font-medium">
                                                ${escapeHtml(child.nama)}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                        ${child.jumlah_transaksi}x
                                    </td>
                                    <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                        ${child.persentase_parent}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari sub</span>
                                    </td>
                                    <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-medium text-gray-700 text-xs sm:text-sm">
                                        ${child.formatted_subtotal}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        const isMuted = sub.is_tanpa_subkategori;
                        html += `
                            <tr class="laporan-sub-row group ${subRowHover} transition-colors ${catId}"
                                ${subDisplay}
                                data-cat-type="${type}"
                                data-sub-name="${escapeHtml(sub.nama.toLowerCase())}"
                                data-parent-name="${escapeHtml(cat.nama.toLowerCase())}">
                                <td class="py-1.5 sm:py-2 pl-10 sm:pl-14 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-700" style="padding-left: clamp(2.5rem, 6vw, 3.5rem);">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-gray-300 font-mono text-xs select-none">└─</span>
                                        <span class="${isMuted ? 'italic text-gray-400' : 'text-gray-700 font-medium'}">
                                            ${escapeHtml(sub.nama)}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-500">
                                    ${sub.jumlah_transaksi}x
                                </td>
                                <td class="py-1.5 sm:py-2 px-2 sm:px-4 text-center text-xs text-gray-400">
                                    ${sub.persentase_kategori}% <span class="text-[9px] text-gray-400 hidden sm:inline">dari kat.</span>
                                </td>
                                <td class="py-1.5 sm:py-2 px-3 sm:px-4 text-right font-semibold text-gray-800 text-xs sm:text-sm">
                                    ${sub.formatted_subtotal}
                                </td>
                            </tr>
                        `;
                    }
                });
            }
        });

        return html;
    }

    // ── Palet Warna Infografis Statistik Kategori (Harmonis & Vibrant Sesuai Gambar) ──
    const CHART_PALETTE = [
        { bg: '#E11D48', light: '#FFE4E6', text: '#BE123C' },  // A: Vibrant Rose / Pink (Sesuai Referensi)
        { bg: '#C026D3', light: '#FAE8FF', text: '#A21CAF' },  // B: Magenta / Fuchsia (Sesuai Referensi)
        { bg: '#6366F1', light: '#EEF2FF', text: '#4F46E5' },  // C: Indigo / Periwinkle (Sesuai Referensi)
        { bg: '#0EA5E9', light: '#E0F2FE', text: '#0284C7' },  // D: Sky Blue (Sesuai Referensi)
        { bg: '#14B8A6', light: '#CCFBF1', text: '#0F766E' },  // E: Mint / Cyan (Sesuai Referensi)
        { bg: '#F59E0B', light: '#FEF3C7', text: '#D97706' },  // F: Amber
        { bg: '#10B981', light: '#D1FAE5', text: '#047857' },  // G: Emerald
        { bg: '#8B5CF6', light: '#EDE9FE', text: '#6D28D9' },  // H: Violet
        { bg: '#EC4899', light: '#FCE7F3', text: '#DB2777' },  // I: Pink
        { bg: '#0284C7', light: '#E0F2FE', text: '#0369A1' },  // J: Ocean Blue
        { bg: '#64748B', light: '#F1F5F9', text: '#475569' }   // K: Slate
    ];

    let currentChartType = 'pengeluaran'; // 'pengeluaran' | 'pemasukan' | 'semua'
    window._currentCategoryStats = [];
    window._currentChartSummary = null;

    // ── Switch Tipe Tab Chart (Pengeluaran / Pemasukan / Semua) ──
    function setChartTypeFilter(type) {
        currentChartType = type;
        ['pengeluaran', 'pemasukan', 'semua'].forEach(t => {
            const btn = document.getElementById('chart-tab-' + t);
            if (btn) {
                if (t === type) {
                    btn.className = 'chart-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-900 shadow-xs';
                } else {
                    btn.className = 'chart-type-btn py-1 sm:py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer text-gray-600 hover:text-gray-900';
                }
            }
        });
        renderCategoryStatsChart(currentTransaksiList);
    }
    window.setChartTypeFilter = setChartTypeFilter;

    // ── Interaksi Hover & Highlight Kategori ──
    function highlightCategory(idx) {
        // Highlight slice di SVG Donut
        const slices = document.querySelectorAll('.donut-slice');
        slices.forEach(slice => {
            if (parseInt(slice.dataset.index, 10) === idx) {
                slice.classList.add('active');
                slice.classList.remove('dimmed');
            } else {
                slice.classList.add('dimmed');
                slice.classList.remove('active');
            }
        });

        // Highlight kartu di grid bawah
        const cards = document.querySelectorAll('.category-stat-card');
        cards.forEach(card => {
            if (parseInt(card.dataset.index, 10) === idx) {
                card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/40', 'scale-[1.02]');
            } else {
                card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/40', 'scale-[1.02]');
            }
        });

        // Highlight item legend daftar kategori
        const legendItems = document.querySelectorAll('.chart-legend-item');
        legendItems.forEach(item => {
            if (parseInt(item.dataset.index, 10) === idx) {
                item.classList.add('bg-slate-100/90');
            } else {
                item.classList.remove('bg-slate-100/90');
            }
        });

        // Update teks di bagian tengah Donut
        if (window._currentCategoryStats && window._currentCategoryStats[idx]) {
            const cat = window._currentCategoryStats[idx];
            const titleEl = document.getElementById('donut-center-title');
            const nominalEl = document.getElementById('donut-center-nominal');
            const subEl = document.getElementById('donut-center-sub');
            if (titleEl) {
                titleEl.textContent = cat.name.length > 16 ? cat.name.substring(0, 14) + '…' : cat.name;
                titleEl.setAttribute('fill', cat.color);
            }
            if (nominalEl) {
                nominalEl.textContent = formatRupiah(cat.total);
            }
            if (subEl) {
                subEl.textContent = `${cat.percentage}% (${cat.count} trx)`;
                subEl.setAttribute('fill', '#475569');
            }
        }
    }
    window.highlightCategory = highlightCategory;

    function unhighlightCategory() {
        const slices = document.querySelectorAll('.donut-slice');
        slices.forEach(slice => {
            slice.classList.remove('active', 'dimmed');
        });

        const cards = document.querySelectorAll('.category-stat-card');
        cards.forEach(card => {
            card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/40', 'scale-[1.02]');
        });

        const legendItems = document.querySelectorAll('.chart-legend-item');
        legendItems.forEach(item => {
            item.classList.remove('bg-slate-100/90');
        });

        resetDonutCenterSummary();
    }
    window.unhighlightCategory = unhighlightCategory;

    function resetDonutCenterSummary() {
        const titleEl = document.getElementById('donut-center-title');
        const nominalEl = document.getElementById('donut-center-nominal');
        const subEl = document.getElementById('donut-center-sub');
        if (window._currentChartSummary) {
            const s = window._currentChartSummary;
            if (titleEl) {
                titleEl.textContent = s.title;
                titleEl.setAttribute('fill', '#64748b');
            }
            if (nominalEl) {
                nominalEl.textContent = formatRupiah(s.totalAmount);
            }
            if (subEl) {
                subEl.textContent = `${s.totalCount} Transaksi`;
                subEl.setAttribute('fill', '#94a3b8');
            }
        }
    }

    // ── Render Statistik Infografis & Persentase Kategori ──
    function renderCategoryStatsChart(list) {
        if (!list) list = currentTransaksiList || [];

        // Filter berdasarkan currentChartType
        let filtered = list;
        if (currentChartType !== 'semua') {
            filtered = list.filter(t => t.tipe === currentChartType);
        }

        const emptyState = document.getElementById('chart-empty-state');
        const contentArea = document.getElementById('chart-content-area');
        const emptyTitle = document.getElementById('chart-empty-title');
        const emptyDesc = document.getElementById('chart-empty-desc');

        if (!filtered || filtered.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (contentArea) contentArea.classList.add('hidden');
            if (emptyTitle) {
                const label = currentChartType === 'pengeluaran' ? 'Pengeluaran' : (currentChartType === 'pemasukan' ? 'Pemasukan' : 'Transaksi');
                emptyTitle.textContent = `Belum Ada Data ${label}`;
            }
            if (emptyDesc) {
                emptyDesc.textContent = `Belum ada catatan kategori pada periode ini untuk menampilkan infografis diagram lingkaran.`;
            }
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (contentArea) contentArea.classList.remove('hidden');

        // Agregasi transaksi berdasarkan kategori
        const catMap = {};
        let totalAmount = 0;
        let totalCount = 0;

        filtered.forEach(t => {
            const cat = (t.kategori || 'Lainnya').trim();
            const nominal = parseFloat(t.nominal) || 0;
            if (!catMap[cat]) {
                catMap[cat] = { name: cat, total: 0, count: 0 };
            }
            catMap[cat].total += nominal;
            catMap[cat].count += 1;
            totalAmount += nominal;
            totalCount += 1;
        });

        if (totalAmount <= 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (contentArea) contentArea.classList.add('hidden');
            return;
        }

        // Urutkan kategori terbesar ke terkecil (A = kategori terbesar, B = berikutnya, dst)
        const categories = Object.values(catMap).sort((a, b) => b.total - a.total);

        // Pasang huruf A, B, C, D, E..., warna, dan persentase
        categories.forEach((cat, idx) => {
            const letter = String.fromCharCode(65 + (idx % 26));
            const palette = CHART_PALETTE[idx % CHART_PALETTE.length];
            const pct = Math.round((cat.total / totalAmount) * 1000) / 10;
            cat.letter = letter;
            cat.color = palette.bg;
            cat.lightColor = palette.light;
            cat.textColor = palette.text;
            cat.percentage = pct;
            cat.index = idx;
        });

        window._currentCategoryStats = categories;
        const typeLabel = currentChartType === 'pengeluaran' ? 'Total Pengeluaran' : (currentChartType === 'pemasukan' ? 'Total Pemasukan' : 'Total Transaksi');
        window._currentChartSummary = {
            title: typeLabel,
            totalAmount: totalAmount,
            totalCount: totalCount
        };

        // Update badge jumlah kategori
        const countBadge = document.getElementById('chart-category-count-badge');
        if (countBadge) countBadge.textContent = `${categories.length} Kategori`;

        // 1. Render Daftar Legend Kategori (Kiri) Sesuai Referensi Gambar Infografis
        const legendList = document.getElementById('chart-category-legend-list');
        if (legendList) {
            legendList.innerHTML = categories.map((cat, i) => `
                <div class="chart-legend-item flex items-center justify-between gap-3 group cursor-pointer p-1.5 -mx-1.5 rounded-xl transition-all" data-index="${i}" onmouseenter="highlightCategory(${i})" onmouseleave="unhighlightCategory()">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white shrink-0 shadow-xs transition-transform group-hover:scale-110" style="background-color: ${cat.color}">
                            ${cat.letter}
                        </span>
                        <span class="text-xs font-bold text-gray-800 truncate" title="${escapeHtml(cat.name)}">
                            ${escapeHtml(cat.name)}
                        </span>
                        <div class="flex-1 h-0.5 min-w-[20px] rounded-full transition-all group-hover:h-1 opacity-80" style="background: linear-gradient(90deg, ${cat.color} 0%, ${cat.color}33 100%)"></div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black text-gray-900">${cat.percentage}%</span>
                    </div>
                </div>
            `).join('');
        }

        // 2. Render SVG Donut Chart & Callout Labels
        const slicesGroup = document.getElementById('donut-chart-slices');
        const labelsGroup = document.getElementById('donut-chart-labels');
        resetDonutCenterSummary();

        if (slicesGroup && labelsGroup) {
            const cx = 190;
            const cy = 190;
            const rOuter = 110;
            const rInner = 68;
            const rLabel = 148;

            let slicesHtml = '';
            let labelsHtml = '';

            if (categories.length === 1) {
                const cat = categories[0];
                const p1 = `M ${cx} ${cy - rOuter} A ${rOuter} ${rOuter} 0 0 1 ${cx} ${cy + rOuter} L ${cx} ${cy + rInner} A ${rInner} ${rInner} 0 0 0 ${cx} ${cy - rInner} Z`;
                const p2 = `M ${cx} ${cy + rOuter} A ${rOuter} ${rOuter} 0 0 1 ${cx} ${cy - rOuter} L ${cx} ${cy - rInner} A ${rInner} ${rInner} 0 0 0 ${cx} ${cy + rInner} Z`;
                slicesHtml = `
                    <path d="${p1}" fill="${cat.color}" stroke="#ffffff" stroke-width="2.5" class="donut-slice cursor-pointer" data-index="0" onmouseenter="highlightCategory(0)" onmouseleave="unhighlightCategory()"></path>
                    <path d="${p2}" fill="${cat.color}" stroke="#ffffff" stroke-width="2.5" class="donut-slice cursor-pointer" data-index="0" onmouseenter="highlightCategory(0)" onmouseleave="unhighlightCategory()"></path>
                `;
                labelsHtml = `
                    <text x="${cx}" y="${cy - rLabel}" text-anchor="middle" dominant-baseline="central" class="font-sans select-none pointer-events-none">
                        <tspan x="${cx}" dy="-6" font-size="12" font-weight="900" fill="${cat.color}">${cat.letter}</tspan>
                        <tspan x="${cx}" dy="14" font-size="10.5" font-weight="700" fill="#334155">${cat.percentage}%</tspan>
                    </text>
                `;
            } else {
                let currentAngle = -Math.PI / 2;

                categories.forEach((cat, i) => {
                    const angleSpan = (cat.total / totalAmount) * 2 * Math.PI;
                    const startAngle = currentAngle;
                    const endAngle = currentAngle + angleSpan;
                    const midAngle = startAngle + angleSpan / 2;
                    currentAngle = endAngle;

                    const x1 = cx + rOuter * Math.cos(startAngle);
                    const y1 = cy + rOuter * Math.sin(startAngle);
                    const x2 = cx + rOuter * Math.cos(endAngle);
                    const y2 = cy + rOuter * Math.sin(endAngle);

                    const x3 = cx + rInner * Math.cos(endAngle);
                    const y3 = cy + rInner * Math.sin(endAngle);
                    const x4 = cx + rInner * Math.cos(startAngle);
                    const y4 = cy + rInner * Math.sin(startAngle);

                    const largeArc = angleSpan > Math.PI ? 1 : 0;
                    const d = `M ${x1} ${y1} A ${rOuter} ${rOuter} 0 ${largeArc} 1 ${x2} ${y2} L ${x3} ${y3} A ${rInner} ${rInner} 0 ${largeArc} 0 ${x4} ${y4} Z`;

                    slicesHtml += `
                        <path d="${d}" fill="${cat.color}" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round" class="donut-slice cursor-pointer" data-index="${i}" onmouseenter="highlightCategory(${i})" onmouseleave="unhighlightCategory()"></path>
                    `;

                    // Callout label outside slice (Huruf di atas, persentase di bawah - persis seperti contoh gambar)
                    const lx = cx + rLabel * Math.cos(midAngle);
                    const ly = cy + rLabel * Math.sin(midAngle);

                    labelsHtml += `
                        <text x="${lx}" y="${ly}" text-anchor="middle" dominant-baseline="central" class="font-sans select-none cursor-pointer" onmouseenter="highlightCategory(${i})" onmouseleave="unhighlightCategory()">
                            <tspan x="${lx}" dy="-5" font-size="11.5" font-weight="900" fill="${cat.color}">${cat.letter}</tspan>
                            <tspan x="${lx}" dy="13" font-size="10" font-weight="700" fill="#334155">${cat.percentage}%</tspan>
                        </text>
                    `;
                });
            }

            slicesGroup.innerHTML = slicesHtml;
            labelsGroup.innerHTML = labelsHtml;
        }

        // 3. Render Kartu Rincian Persentase Kategori di Bagian Bawah
        const cardsGrid = document.getElementById('chart-percentage-cards-grid');
        if (cardsGrid) {
            cardsGrid.innerHTML = categories.map((cat, i) => `
                <div class="category-stat-card group relative bg-white hover:bg-slate-50/90 rounded-2xl p-3.5 border border-gray-200/90 shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden" data-index="${i}" onmouseenter="highlightCategory(${i})" onmouseleave="unhighlightCategory()">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white shrink-0 shadow-xs transition-transform group-hover:scale-110" style="background-color: ${cat.color}">
                                ${cat.letter}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 truncate" title="${escapeHtml(cat.name)}">
                                    ${escapeHtml(cat.name)}
                                </h4>
                                <p class="text-[10.5px] text-gray-500 font-medium">
                                    ${cat.count} transaksi
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black shrink-0" style="background-color: ${cat.lightColor}; color: ${cat.color}">
                            ${cat.percentage}%
                        </span>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-gray-100">
                        <div class="flex items-baseline justify-between text-xs mb-1.5">
                            <span class="text-gray-400 text-[10.5px] font-medium">Nominal</span>
                            <span class="font-extrabold text-gray-900 text-xs sm:text-[13px]">${formatRupiah(cat.total)}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500" style="width: ${cat.percentage}%; background-color: ${cat.color}"></div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }
    window.renderCategoryStatsChart = renderCategoryStatsChart;

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
                                            onclick="openEditTransactionModal('${effectiveId}')"
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
                                            onclick="openEditTransactionModal('${effectiveId}')"
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

        try {
            const url = new URL(window.location.href);
            if (periode !== 'semua') {
                url.searchParams.set('periode', periode);
            } else {
                url.searchParams.delete('periode');
            }
            if (currentSubmenu === 'statistik') {
                url.searchParams.set('tab', 'statistik');
            } else if (currentSubmenu === 'laporan') {
                url.searchParams.set('tab', 'laporan');
            } else {
                url.searchParams.delete('tab');
            }
            window.history.pushState(null, '', url.toString());
        } catch (e) {
            const tabParam = (currentSubmenu === 'statistik' || currentSubmenu === 'laporan') ? '&tab=' + currentSubmenu : '';
            const newUrl = '/keuangan' + (periode !== 'semua' ? '?periode=' + encodeURIComponent(periode) : '') + tabParam;
            window.history.pushState(null, '', newUrl);
        }

        loadKeuanganData(periode);
    }

    // ── Form Submit Handlers (Online & Offline Support) ──
    document.addEventListener('DOMContentLoaded', function() {
        bindRupiahInput(document.getElementById('create-nominal'));
        bindRupiahInput(document.getElementById('edit-nominal'));

        // Inisialisasi Sub Menu aktif berdasarkan URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab') || '{{ request()->query("tab", "riwayat") }}';
        if (initialTab === 'statistik') {
            setKeuanganSubmenu('statistik');
        } else if (initialTab === 'laporan') {
            setKeuanganSubmenu('laporan');
        } else {
            setKeuanganSubmenu('riwayat');
        }

        // Inisialisasi Infografis Statistik Kategori
        if (typeof renderCategoryStatsChart === 'function') {
            const hasPengeluaran = (currentTransaksiList || []).some(t => t.tipe === 'pengeluaran');
            const hasPemasukan = (currentTransaksiList || []).some(t => t.tipe === 'pemasukan');
            if (!hasPengeluaran && hasPemasukan) {
                setChartTypeFilter('pemasukan');
            } else {
                setChartTypeFilter('pengeluaran');
            }
        }

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

                const nominalVal = parseInt((formData.get('nominal') || '').toString().replace(/[^0-9]/g, '') || '0', 10);
                if (nominalVal <= 0) {
                    notifyError('Nominal harus lebih dari 0');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                    return;
                }

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
                        if (data.categories) {
                            appCategories = data.categories;
                            renderCategoryTree(appCategories);
                        }
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

                try {
                    const formData = new FormData(this);
                    const rawId = currentEditTransaction ? (currentEditTransaction.raw_id || currentEditTransaction.id) : null;

                    const nominalVal = parseInt((formData.get('nominal') || '').toString().replace(/[^0-9]/g, '') || '0', 10);
                    if (nominalVal <= 0) {
                        notifyError('Nominal harus lebih dari 0');
                        return;
                    }

                    // Hapus input file jika tidak ada file yang dipilih agar tidak mengirim payload multipart yang tidak perlu
                    const fileInput = this.querySelector('input[name="foto_nota"]');
                    if (fileInput && (!fileInput.files || fileInput.files.length === 0)) {
                        formData.delete('foto_nota');
                    }

                    // Mode Offline
                    if (!navigator.onLine) {
                        try {
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
                            } else {
                                notifyError('Gagal menyimpan transaksi offline: ID transaksi tidak ditemukan.');
                            }
                        } catch (err) {
                            console.error('Offline edit error:', err);
                            notifyError('Gagal mengubah transaksi offline: ' + err.message);
                        }
                        return;
                    }

                    // Mode Online
                    if (!formData.has('_method')) {
                        formData.append('_method', 'PUT');
                    }

                    let actionUrl = this.action;
                    if (!actionUrl || actionUrl.endsWith('/keuangan') || actionUrl.endsWith('/keuangan/')) {
                        if (rawId) {
                            actionUrl = '/keuangan/' + rawId;
                        }
                    }

                    const res = await fetch(actionUrl, {
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
                        if (data.categories) {
                            appCategories = data.categories;
                            renderCategoryTree(appCategories);
                        }
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
                    const rawId = currentEditTransaction ? (currentEditTransaction.raw_id || currentEditTransaction.id) : null;
                    if (window.AgriOfflineStore && rawId) {
                        try {
                            const fileInput = this.querySelector('input[name="foto_nota"]');
                            let base64Photo = null;
                            if (fileInput && fileInput.files && fileInput.files[0]) {
                                base64Photo = await fileToBase64(fileInput.files[0]);
                            }

                            const formData = new FormData(this);
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
                        notifyError('Terjadi kesalahan saat menyimpan perubahan.');
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
