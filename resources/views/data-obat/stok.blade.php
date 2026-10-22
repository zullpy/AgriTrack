@extends('layouts.app')

@section('title', 'Stok & Pembelian Obat - AgriTrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ── TAB NAVIGASI: Data Obat vs Stok Obat ── --}}
    <div class="flex items-center gap-1.5 p-1 bg-gray-100/90 rounded-2xl w-fit border border-gray-200/70">
        <a href="/data-obat"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-200/60 transition-all">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <span>Data Obat</span>
        </a>
        <a href="/stok-obat"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs bg-white text-amber-800 border border-gray-200/60 transition-all">
            <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            <span>Stok Obat</span>
            @if($stokMenipisCount > 0)
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">{{ $stokMenipisCount }}</span>
            @endif
        </a>
    </div>

    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Stok & Pembelian Obat</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    {{ $totalStokUnits }} Total Unit
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau ketersediaan stok obat, status obat menipis/habis, dan catat pembelian obat baru / restock.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    onclick="openGeneralPurchaseModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-bold active:scale-95 transition-all shadow-sm shrink-0 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Catat Pembelian Obat</span>
            </button>
        </div>
    </div>

    {{-- ── Flash message ── --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold animate-slide-up shadow-sm">
            <div class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
            <span class="flex-1 min-w-0">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 p-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- ── 4 Summary Stat Cards (Compact di Mobile) ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        {{-- Card 1: Total Obat --}}
        <div class="bg-surface rounded-xl sm:rounded-2xl border border-gray-200/90 p-3 sm:p-4 shadow-xs flex items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider block truncate">Total Jenis</span>
                <span class="text-lg sm:text-2xl font-extrabold text-gray-900 tabular-nums mt-0.5 block leading-tight">{{ $totalObat }}</span>
                <span class="hidden sm:block text-[11px] text-gray-500 font-medium mt-0.5 truncate">{{ $totalStokUnits }} Unit tersimpan</span>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </div>
        </div>

        {{-- Card 2: Stok Aman --}}
        <a href="/stok-obat?status=aman" class="bg-surface rounded-xl sm:rounded-2xl border border-emerald-200 p-3 sm:p-4 shadow-xs flex items-center justify-between gap-2 sm:gap-3 hover:bg-emerald-50/40 transition-colors">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-[11px] font-bold text-emerald-800 uppercase tracking-wider block truncate">Stok Aman (>3)</span>
                <span class="text-lg sm:text-2xl font-extrabold text-emerald-700 tabular-nums mt-0.5 block leading-tight">{{ $stokAmanCount }}</span>
                <span class="hidden sm:block text-[11px] text-emerald-600 font-medium mt-0.5 truncate">Persediaan aman</span>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </a>

        {{-- Card 3: Stok Menipis --}}
        <a href="/stok-obat?status=menipis" class="bg-surface rounded-xl sm:rounded-2xl border border-amber-200 p-3 sm:p-4 shadow-xs flex items-center justify-between gap-2 sm:gap-3 hover:bg-amber-50/40 transition-colors">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-[11px] font-bold text-amber-800 uppercase tracking-wider block truncate">Menipis (1-3)</span>
                <span class="text-lg sm:text-2xl font-extrabold text-amber-700 tabular-nums mt-0.5 block leading-tight">{{ $stokMenipisCount }}</span>
                <span class="hidden sm:block text-[11px] text-amber-600 font-medium mt-0.5 truncate">Perlu restock</span>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
        </a>

        {{-- Card 4: Stok Habis --}}
        <a href="/stok-obat?status=habis" class="bg-surface rounded-xl sm:rounded-2xl border border-rose-200 p-3 sm:p-4 shadow-xs flex items-center justify-between gap-2 sm:gap-3 hover:bg-rose-50/40 transition-colors">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-[11px] font-bold text-rose-800 uppercase tracking-wider block truncate">Stok Habis (0)</span>
                <span class="text-lg sm:text-2xl font-extrabold text-rose-700 tabular-nums mt-0.5 block leading-tight">{{ $stokHabisCount }}</span>
                <span class="hidden sm:block text-[11px] text-rose-600 font-medium mt-0.5 truncate">Tidak ada stok</span>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- ── Filter & Search Section ── --}}
    <div class="bg-surface rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
        <form method="GET" action="/stok-obat" class="flex flex-col sm:flex-row gap-2.5">
            {{-- Search input --}}
            <div class="relative flex-1 min-w-0">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama obat, kategori, tanaman, atau toko..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 bg-white">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>

            {{-- Kategori Filter --}}
            <div class="shrink-0">
                <select name="jenis"
                        onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->nama }}" {{ request('jenis') === $cat->nama ? 'selected' : '' }}>
                            {{ $cat->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs sm:text-sm font-bold hover:bg-black transition-colors shrink-0">
                Cari
            </button>

            @if(request()->hasAny(['search', 'jenis', 'status']))
                <a href="/stok-obat"
                   class="px-3.5 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center shrink-0">
                    Reset
                </a>
            @endif
        </form>

        {{-- Filter Status Pills --}}
        <div class="flex items-center gap-1.5 flex-wrap pt-2 border-t border-gray-100 text-xs">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mr-1">Filter Stok:</span>
            @php $currentStatus = request('status'); @endphp

            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
               class="px-3 py-1 rounded-lg font-bold transition-colors {{ empty($currentStatus) ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua ({{ $totalObat }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'aman']) }}"
               class="px-3 py-1 rounded-lg font-bold transition-colors flex items-center gap-1.5 {{ $currentStatus === 'aman' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 hover:bg-emerald-100' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus === 'aman' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Stok Aman ({{ $stokAmanCount }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'menipis']) }}"
               class="px-3 py-1 rounded-lg font-bold transition-colors flex items-center gap-1.5 {{ $currentStatus === 'menipis' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 border border-amber-200/80 hover:bg-amber-100' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus === 'menipis' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Stok Menipis ({{ $stokMenipisCount }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'habis']) }}"
               class="px-3 py-1 rounded-lg font-bold transition-colors flex items-center gap-1.5 {{ $currentStatus === 'habis' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-800 border border-rose-200/80 hover:bg-rose-100' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus === 'habis' ? 'bg-white' : 'bg-rose-500' }}"></span>
                Stok Habis ({{ $stokHabisCount }})
            </a>
        </div>
    </div>

    @if($medicines->isEmpty())
        <div class="p-8 sm:p-12 text-center bg-surface rounded-2xl border border-gray-200 shadow-xs space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Tidak ada data stok obat ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto">Silakan ubah filter pencarian atau catat pembelian obat baru untuk mulai mengisi data stok.</p>
            <button type="button"
                    onclick="openGeneralPurchaseModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Catat Pembelian Obat Sekarang</span>
            </button>
        </div>
    @else

        {{-- ── Mobile View: Kartu Stok ── --}}
        <div class="block lg:hidden space-y-3">
            @foreach ($medicines as $med)
                <div class="bg-surface rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            @php $medPhotos = $med->foto_urls; @endphp
                            @if(!empty($medPhotos))
                                <button type="button"
                                        onclick="openPhotoViewerModal({{ json_encode($medPhotos) }}, '{{ addslashes($med->nama) }}')"
                                        class="relative group shrink-0 cursor-pointer mt-0.5 focus:outline-none"
                                        title="Lihat Foto / Nota ({{ count($medPhotos) }})">
                                    <img src="{{ $medPhotos[0] }}" alt="{{ $med->nama }}" class="w-11 h-11 object-cover rounded-xl border border-amber-200 shadow-2xs">
                                    <span class="absolute -bottom-1 -right-1 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-amber-600 text-white text-[9px] font-bold shadow-2xs">
                                        {{ count($medPhotos) }}
                                    </span>
                                </button>
                            @else
                                <div class="w-11 h-11 rounded-xl bg-gray-100 border border-gray-200/80 flex items-center justify-center shrink-0 text-gray-400 mt-0.5">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-800">
                                        {{ $med->jenis }}
                                    </span>
                                    @if(!empty($medPhotos))
                                        <button type="button"
                                                onclick="openPhotoViewerModal({{ json_encode($medPhotos) }}, '{{ addslashes($med->nama) }}')"
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors cursor-pointer whitespace-nowrap"
                                                title="Lihat Foto / Nota ({{ count($medPhotos) }})">
                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                            </svg>
                                            <span>Nota ({{ count($medPhotos) }})</span>
                                        </button>
                                    @endif
                                </div>
                                <h2 class="text-base font-bold text-gray-900 leading-tight">{{ $med->nama }}</h2>
                                @if($med->tanaman_sasaran)
                                    <span class="text-xs text-gray-500 block truncate">Sasaran: {{ $med->tanaman_sasaran }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Badge Stok Mobile --}}
                        <div class="text-right shrink-0">
                            @if(($med->stok ?? 0) > 3)
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $med->stok_volume_label }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-medium">{{ $med->stok_label }}</span>
                                </div>
                            @elseif(($med->stok ?? 0) > 0)
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $med->stok_volume_label }}
                                    </span>
                                    <span class="text-[10px] text-amber-700 font-bold">Restock ({{ $med->stok_label }})</span>
                                </div>
                            @else
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Habis
                                    </span>
                                    <span class="text-[10px] text-rose-600 font-bold">0 {{ $med->base_unit_label }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Info Pembelian Terakhir --}}
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Harga Beli</span>
                            <span class="font-bold text-green-700 tabular-nums">{{ $med->formatted_harga ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Kemasan</span>
                            <span class="font-semibold text-gray-800 truncate block">{{ $med->kemasan_label ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Toko Obat</span>
                            <span class="font-semibold text-gray-800 truncate block">{{ $med->toko_obat ?: '—' }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                        <button type="button"
                                onclick="openQuickPurchaseModal({{ json_encode($med) }})"
                                class="flex-1 py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            <span>Beli / Restock</span>
                        </button>

                        <a href="/data-obat/{{ $med->id }}/edit"
                           class="py-2 px-3 rounded-xl border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                            Edit
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Desktop View: Tabel Solid Bersih ── --}}
        <div class="hidden lg:block bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3.5 min-w-[220px]">Nama Obat & Kategori</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Status & Sisa Stok</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Kemasan</th>
                            <th class="px-5 py-3.5 whitespace-nowrap">Harga Beli Terakhir</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Toko Terakhir</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Tanggal Beli</th>
                            <th class="px-5 py-3.5 text-right pr-6 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($medicines as $med)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    @php $medPhotos = $med->foto_urls; @endphp
                                    <div class="flex items-center gap-3">
                                        @if(!empty($medPhotos))
                                            <button type="button"
                                                    onclick="openPhotoViewerModal({{ json_encode($medPhotos) }}, '{{ addslashes($med->nama) }}')"
                                                    class="relative group shrink-0 cursor-pointer focus:outline-none"
                                                    title="Lihat foto / nota ({{ count($medPhotos) }})">
                                                <img src="{{ $medPhotos[0] }}" alt="{{ $med->nama }}" class="w-10 h-10 object-cover rounded-xl border border-amber-200 group-hover:scale-105 group-hover:border-amber-400 group-hover:shadow-xs transition-all shadow-2xs">
                                                <span class="absolute -bottom-1 -right-1 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-amber-600 text-white text-[9px] font-bold shadow-2xs">
                                                    {{ count($medPhotos) }}
                                                </span>
                                            </button>
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200/80 flex items-center justify-center shrink-0 text-gray-400">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-gray-900 leading-snug truncate" title="{{ $med->nama }}">{{ $med->nama }}</div>
                                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-700 whitespace-nowrap">
                                                    {{ $med->jenis }}
                                                </span>
                                                @if(!empty($medPhotos))
                                                    <button type="button"
                                                            onclick="openPhotoViewerModal({{ json_encode($medPhotos) }}, '{{ addslashes($med->nama) }}')"
                                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/90 hover:bg-amber-100 transition-colors whitespace-nowrap cursor-pointer">
                                                        <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                                        </svg>
                                                        <span>Nota ({{ count($medPhotos) }})</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if(($med->stok ?? 0) > 3)
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $med->stok_volume_label }}
                                            </span>
                                            <span class="text-[10px] font-semibold text-emerald-700">Stok Aman ({{ $med->stok_label }})</span>
                                        </div>
                                    @elseif(($med->stok ?? 0) > 0)
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ $med->stok_volume_label }}
                                            </span>
                                            <span class="text-[10px] font-bold text-amber-700">Perlu Restock ({{ $med->stok_label }})</span>
                                        </div>
                                    @else
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Habis (0 {{ $med->base_unit_label }})
                                            </span>
                                            <span class="text-[10px] font-bold text-rose-700">Kosong</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-700 font-medium">
                                    {{ $med->kemasan_label ?: '—' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-green-700 tabular-nums block">
                                        {{ $med->formatted_harga ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-800 font-medium">
                                    {{ $med->toko_obat ?: '—' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                                    @if($med->tanggal_beli)
                                        {{ \Carbon\Carbon::parse($med->tanggal_beli)->translatedFormat('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-4 pr-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                onclick="openQuickPurchaseModal({{ json_encode($med) }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                            <span>Beli / Restock</span>
                                        </button>

                                        <a href="/data-obat/{{ $med->id }}/edit"
                                           title="Edit Data Obat"
                                           class="p-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($medicines->hasPages())
            <div class="mt-4">
                {{ $medicines->links() }}
            </div>
        @endif
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL CATAT PEMBELIAN OBAT UMUM (#general-purchase-modal) ── --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div id="general-purchase-modal"
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs items-center justify-center p-3 sm:p-4 transition-opacity duration-200"
         onclick="if(event.target === this) closeGeneralPurchaseModal()">
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-2xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden animate-slide-up"
             onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-amber-50/70 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Catat Pembelian Obat</h3>
                        <p class="text-xs text-amber-900 font-medium">Update harga & stok jika obat sudah ada, atau simpan langsung jika obat baru pertama kali beli.</p>
                    </div>
                </div>
                <button type="button"
                        onclick="closeGeneralPurchaseModal()"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="general-purchase-form" action="/stok-obat/pembelian" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
                @csrf

                {{-- Input Ketik Nama Obat dengan Autocomplete Otomatis --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700">
                        Nama Obat <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="relative">
                            <input type="text"
                                   name="nama"
                                   id="gp-nama-input"
                                   required
                                   autocomplete="off"
                                   placeholder="Ketik nama obat (misal: Antracol, Curacron, dsb)..."
                                   class="w-full text-xs font-bold px-3.5 py-2.5 pr-9 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white shadow-2xs">
                            <input type="hidden" name="medicine_id" id="gp-medicine-id">

                            {{-- Clear button --}}
                            <button type="button"
                                    id="gp-clear-name-btn"
                                    onclick="clearMedicineLookup()"
                                    class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Floating Autocomplete Suggestions Dropdown --}}
                        <div id="gp-autocomplete-list"
                             class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl max-h-56 overflow-y-auto z-50 divide-y divide-gray-100">
                            {{-- Diisi secara dinamis oleh JavaScript --}}
                        </div>
                    </div>

                    {{-- Status Banner (Muncul saat obat terdeteksi di data obat / obat baru) --}}
                    <div id="gp-selected-status" class="hidden rounded-xl p-3 text-xs transition-all animate-fade-in">
                        {{-- Diisi JavaScript --}}
                    </div>

                    {{-- Form Tambahan untuk Obat Baru Pertama Kali Beli (Kategori & Sasaran) --}}
                    <div id="gp-new-med-fields" class="hidden space-y-2 pt-2 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-0.5">Kategori Obat</label>
                                <select name="jenis" id="gp-jenis-select" class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white font-medium">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->nama }}">{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-0.5">Tanaman Sasaran</label>
                                <input type="text" name="tanaman_sasaran" id="gp-sasaran-input" placeholder="e.g. Cabai, Padi" class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Nama Toko Pembelian <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="toko_obat"
                               id="gp-toko-input"
                               required
                               placeholder="e.g. Toko Tani Makmur"
                               class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Harga Satuan (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="harga"
                               id="gp-harga-input"
                               required
                               placeholder="Rp 0"
                               class="w-full text-xs font-bold text-green-700 px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                        <p id="gp-harga-hint" class="text-[11px] text-gray-500 mt-1">
                            Jika harga berbeda akan meng-update harga obat. Jika harga sama, hanya tanggal beli yang diperbarui.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Ukuran & Satuan Kemasan
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number"
                                   step="any"
                                   min="0"
                                   name="kemasan_jumlah"
                                   id="gp-kemasan-jumlah-input"
                                   placeholder="e.g. 500 / 1"
                                   class="w-1/2 text-xs font-semibold px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                            <select name="kemasan_satuan"
                                    id="gp-kemasan-satuan-select"
                                    class="w-1/2 text-xs font-bold text-gray-700 px-2.5 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
                                <option value="liter">liter</option>
                                <option value="ml">ml</option>
                            </select>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Ukuran isi per kemasan (misal 500 ml atau 1 kg).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Jumlah Beli (Qty) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="jumlah"
                               id="gp-jumlah-input"
                               min="1"
                               value="1"
                               required
                               class="w-full text-xs font-bold px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                        <p class="text-[10px] text-gray-400 mt-1">Banyaknya botol / kemasan yang dibeli.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Tanggal Beli
                        </label>
                        <input type="date"
                               name="tanggal_beli"
                               id="gp-tanggal-input"
                               value="{{ date('Y-m-d') }}"
                               class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <input type="text"
                           name="catatan"
                           placeholder="e.g. Pembelian restock musim tanam"
                           class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                </div>

                {{-- Foto Nota Pembelian (Kamera & Galeri) --}}
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700">
                        Foto Bukti / Nota Pembelian <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>

                    {{-- Tombol Buka Kamera & Pilih Galeri --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" 
                                onclick="document.getElementById('gp-foto-camera').click()"
                                class="py-2.5 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-900 border border-amber-200 text-xs font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                            </svg>
                            <span>Buka Kamera</span>
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('gp-foto-nota').click()"
                                class="py-2.5 px-3 rounded-xl bg-white hover:bg-gray-50 active:scale-95 text-gray-700 border border-gray-300 text-xs font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                            <span>Pilih Galeri</span>
                        </button>
                    </div>

                    {{-- Hidden Inputs --}}
                    <input type="file"
                           id="gp-foto-camera"
                           accept="image/*"
                           capture="environment"
                           onchange="handlePurchasePhotoSelected(this, 'gp')"
                           class="hidden">
                    <input type="file"
                           id="gp-foto-nota"
                           name="foto_nota"
                           accept="image/*"
                           onchange="handlePurchasePhotoSelected(this, 'gp')"
                           class="hidden">

                    {{-- Preview Card --}}
                    <div id="gp-foto-preview-card" class="hidden items-center justify-between p-2.5 bg-amber-50/70 border border-amber-200 rounded-xl">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img id="gp-foto-preview-img" src="" class="w-12 h-12 object-cover rounded-lg border border-amber-300 shrink-0">
                            <div class="min-w-0">
                                <p id="gp-foto-preview-name" class="text-xs font-semibold text-gray-800 truncate"></p>
                                <p id="gp-foto-preview-size" class="text-[10px] text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button"
                                onclick="clearPurchasePhoto('gp')"
                                class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                title="Hapus foto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button"
                            onclick="closeGeneralPurchaseModal()"
                            class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-all shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Simpan Pembelian & Update Stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL PEMBELIAN CEPAT SPESIFIK (#quick-purchase-modal) ── --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div id="quick-purchase-modal"
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs items-center justify-center p-3 sm:p-4 transition-opacity duration-200"
         onclick="if(event.target === this) closeQuickPurchaseModal()">
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-2xl max-w-lg w-full overflow-hidden animate-slide-up"
             onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-amber-50/70">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Catat Pembelian Obat</h3>
                        <p id="qp-med-name" class="text-xs font-semibold text-amber-900 line-clamp-1"></p>
                    </div>
                </div>
                <button type="button"
                        onclick="closeQuickPurchaseModal()"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="quick-purchase-form" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Stok Saat Ini</span>
                        <span id="qp-stok-current" class="text-base font-extrabold text-gray-900 tabular-nums">0</span>
                    </div>
                    <div class="text-amber-500 font-bold text-sm">+</div>
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Jumlah Beli</span>
                        <span id="qp-qty-preview" class="text-base font-extrabold text-amber-700 tabular-nums">1</span>
                    </div>
                    <div class="text-amber-500 font-bold text-sm">=</div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Stok Setelah Beli</span>
                        <span id="qp-stok-result" class="text-base font-extrabold text-emerald-700 tabular-nums">1</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Nama Toko Pembelian <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="toko_obat"
                               id="qp-toko-input"
                               required
                               placeholder="e.g. Toko Tani Makmur"
                               class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Harga Satuan Baru (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="harga"
                               id="qp-harga-input"
                               required
                               placeholder="Rp 0"
                               class="w-full text-xs font-bold text-green-700 px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Ukuran & Satuan Kemasan
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number"
                                   step="any"
                                   min="0"
                                   name="kemasan_jumlah"
                                   id="qp-kemasan-jumlah-input"
                                   placeholder="e.g. 500 / 1"
                                   class="w-1/2 text-xs font-semibold px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                            <select name="kemasan_satuan"
                                    id="qp-kemasan-satuan-select"
                                    class="w-1/2 text-xs font-bold text-gray-700 px-2.5 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
                                <option value="liter">liter</option>
                                <option value="ml">ml</option>
                            </select>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Ukuran isi per kemasan (misal 500 ml atau 1 kg).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Jumlah Beli (Qty) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="jumlah"
                               id="qp-jumlah-input"
                               min="1"
                               value="1"
                               required
                               class="w-full text-xs font-bold px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white tabular-nums">
                        <p class="text-[10px] text-gray-400 mt-1">Banyaknya botol / kemasan yang dibeli.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Tanggal Beli
                        </label>
                        <input type="date"
                               name="tanggal_beli"
                               id="qp-tanggal-input"
                               value="{{ date('Y-m-d') }}"
                               class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <input type="text"
                           name="catatan"
                           id="qp-catatan-input"
                           placeholder="e.g. Pembelian batch musim tanam baru"
                           class="w-full text-xs font-medium px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 bg-white">
                </div>

                {{-- Foto Nota Pembelian (Kamera & Galeri) --}}
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700">
                        Foto Bukti / Nota Pembelian <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>

                    {{-- Tombol Buka Kamera & Pilih Galeri --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" 
                                onclick="document.getElementById('qp-foto-camera').click()"
                                class="py-2.5 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-900 border border-amber-200 text-xs font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                            </svg>
                            <span>Buka Kamera</span>
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('qp-foto-nota').click()"
                                class="py-2.5 px-3 rounded-xl bg-white hover:bg-gray-50 active:scale-95 text-gray-700 border border-gray-300 text-xs font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                            <span>Pilih Galeri</span>
                        </button>
                    </div>

                    {{-- Hidden Inputs --}}
                    <input type="file"
                           id="qp-foto-camera"
                           accept="image/*"
                           capture="environment"
                           onchange="handlePurchasePhotoSelected(this, 'qp')"
                           class="hidden">
                    <input type="file"
                           id="qp-foto-nota"
                           name="foto_nota"
                           accept="image/*"
                           onchange="handlePurchasePhotoSelected(this, 'qp')"
                           class="hidden">

                    {{-- Preview Card --}}
                    <div id="qp-foto-preview-card" class="hidden items-center justify-between p-2.5 bg-amber-50/70 border border-amber-200 rounded-xl">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img id="qp-foto-preview-img" src="" class="w-12 h-12 object-cover rounded-lg border border-amber-300 shrink-0">
                            <div class="min-w-0">
                                <p id="qp-foto-preview-name" class="text-xs font-semibold text-gray-800 truncate"></p>
                                <p id="qp-foto-preview-size" class="text-[10px] text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button"
                                onclick="clearPurchasePhoto('qp')"
                                class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                title="Hapus foto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button"
                            onclick="closeQuickPurchaseModal()"
                            class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-all shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Simpan Pembelian & Update Stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL VIEWER FOTO / NOTA OBAT (#photo-viewer-modal) ── --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div id="photo-viewer-modal"
         class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs items-center justify-center p-3 sm:p-4 transition-opacity duration-200"
         onclick="if(event.target === this) closePhotoViewerModal()">
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-2xl max-w-xl w-full overflow-hidden animate-scale-up"
             onclick="event.stopPropagation()">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                    <h3 id="pv-title" class="text-sm font-bold text-gray-900 truncate">Foto Obat / Nota</h3>
                </div>
                <button type="button"
                        onclick="closePhotoViewerModal()"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-3">
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-950/5 relative group flex items-center justify-center min-h-[220px]">
                    <img id="pv-image" src="" alt="Foto / Nota" class="w-full max-h-[60vh] object-contain rounded-lg">
                    <div id="pv-counter" class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-lg bg-black/65 text-white text-[11px] font-semibold backdrop-blur-xs">
                        Foto 1
                    </div>
                    <a id="pv-link" href="#" target="_blank" class="absolute bottom-2.5 right-2.5 px-3 py-1.5 rounded-xl bg-black/75 hover:bg-black text-white text-xs font-semibold backdrop-blur-xs flex items-center gap-1.5 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                        </svg>
                        <span>Ukuran Penuh</span>
                    </a>
                </div>
                <div id="pv-thumbnails" class="hidden flex items-center gap-2 overflow-x-auto pb-1"></div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
let currentQuickMed = null;
let selectedMedicine = null;

const allMedicinesCatalog = @json($catalogMedicines ?? []);

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderMedicineSuggestions(query) {
    const listEl = document.getElementById('gp-autocomplete-list');
    if (!listEl) return;

    const trimmed = (query || '').trim().toLowerCase();
    let matches = [];

    if (!trimmed) {
        matches = allMedicinesCatalog.slice(0, 8);
    } else {
        matches = allMedicinesCatalog.filter(m =>
            m.nama.toLowerCase().includes(trimmed) ||
            (m.jenis && m.jenis.toLowerCase().includes(trimmed)) ||
            (m.toko_obat && m.toko_obat.toLowerCase().includes(trimmed))
        ).slice(0, 8);
    }

    if (matches.length === 0 && !trimmed) {
        listEl.innerHTML = `<div class="p-3 text-xs text-gray-400 text-center">Belum ada obat tersimpan di katalog.</div>`;
        listEl.classList.remove('hidden');
        return;
    }

    let html = '';
    matches.forEach(m => {
        html += `
        <div class="px-3.5 py-2.5 hover:bg-amber-50/80 cursor-pointer flex items-center justify-between gap-2.5 transition-colors"
             onmousedown="selectMedicine(${m.id})">
            <div class="min-w-0">
                <div class="text-xs font-bold text-gray-900 truncate">${escapeHtml(m.nama)}</div>
                <div class="text-[11px] text-gray-500 truncate flex items-center gap-1.5 mt-0.5">
                    <span class="px-1.5 py-0.2 rounded bg-gray-100 text-gray-700 font-semibold">${escapeHtml(m.jenis)}</span>
                    ${m.kemasan_label ? `<span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-800 font-semibold border border-amber-200/60">${escapeHtml(m.kemasan_label)}</span>` : ''}
                    ${m.toko_obat ? `<span class="truncate">Toko: ${escapeHtml(m.toko_obat)}</span>` : ''}
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs font-extrabold text-emerald-700">${m.formatted_harga}</div>
                <div class="text-[10px] font-bold text-gray-500">Stok: ${m.stok}</div>
            </div>
        </div>`;
    });

    if (trimmed && !allMedicinesCatalog.some(m => m.nama.toLowerCase() === trimmed)) {
        html += `
        <div class="px-3.5 py-2.5 bg-emerald-50/70 hover:bg-emerald-100/70 cursor-pointer text-xs font-bold text-emerald-900 flex items-center gap-2 border-t border-emerald-100 transition-colors"
             onmousedown="selectNewMedicine('${escapeHtml(query)}')">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="truncate">Catat sebagai obat baru pertama beli: "<strong>${escapeHtml(query)}</strong>"</span>
        </div>`;
    }

    listEl.innerHTML = html;
    listEl.classList.remove('hidden');
}

function selectMedicine(id) {
    const med = allMedicinesCatalog.find(m => m.id === id);
    if (!med) return;

    selectedMedicine = med;
    const namaInput = document.getElementById('gp-nama-input');
    const medIdInput = document.getElementById('gp-medicine-id');
    const tokoInput = document.getElementById('gp-toko-input');
    const hargaInput = document.getElementById('gp-harga-input');
    const kemasanJumlahInput = document.getElementById('gp-kemasan-jumlah-input');
    const kemasanSatuanSelect = document.getElementById('gp-kemasan-satuan-select');
    const clearBtn = document.getElementById('gp-clear-name-btn');
    const statusBox = document.getElementById('gp-selected-status');
    const newMedFields = document.getElementById('gp-new-med-fields');
    const hargaHint = document.getElementById('gp-harga-hint');

    namaInput.value = med.nama;
    medIdInput.value = med.id;
    if (clearBtn) clearBtn.classList.remove('hidden');

    if (med.toko_obat) tokoInput.value = med.toko_obat;
    if (med.harga > 0) {
        hargaInput.value = 'Rp ' + Number(med.harga).toLocaleString('id-ID');
    }

    if (kemasanJumlahInput) {
        kemasanJumlahInput.value = med.kemasan_jumlah !== null && med.kemasan_jumlah !== undefined ? med.kemasan_jumlah : '';
    }
    if (kemasanSatuanSelect) {
        kemasanSatuanSelect.value = med.kemasan_satuan || 'kg';
    }

    if (newMedFields) newMedFields.classList.add('hidden');

    // if (statusBox) {
    //     statusBox.className = 'mt-2 p-2.5 rounded-xl border border-amber-200 bg-amber-50/80 text-xs text-amber-950 flex items-start gap-2.5';
    //     statusBox.innerHTML = `
    //         <span class="w-2 h-2 rounded-full bg-amber-500 mt-1 shrink-0"></span>
    //         <div>
    //             <div class="font-bold text-gray-900">${escapeHtml(med.nama)} (${escapeHtml(med.jenis)}) — <span class="text-amber-800 font-extrabold">Stok saat ini: ${med.stok}</span></div>
    //             <div class="text-[11px] text-gray-600 mt-0.5">Obat terdaftar. Jika harga beli sama hanya tanggal beli yang diperbarui; jika berbeda, harga data obat akan di-update.</div>
    //         </div>`;
    //     statusBox.classList.remove('hidden');
    // }

    if (hargaHint) {
        const hFmt = med.harga > 0 ? 'Rp ' + Number(med.harga).toLocaleString('id-ID') : 'Rp 0';
        hargaHint.innerHTML = `<span class="text-amber-800 font-bold">Harga saat ini: ${hFmt}.</span> Ubah jika beli dengan harga berbeda, atau biarkan sama untuk hanya memperbarui tanggal pembelian.`;
    }

    hideMedicineSuggestions();
    recalcGeneralStok();
}

function selectNewMedicine(typedName) {
    selectedMedicine = null;
    const namaInput = document.getElementById('gp-nama-input');
    const medIdInput = document.getElementById('gp-medicine-id');
    const kemasanJumlahInput = document.getElementById('gp-kemasan-jumlah-input');
    const kemasanSatuanSelect = document.getElementById('gp-kemasan-satuan-select');
    const clearBtn = document.getElementById('gp-clear-name-btn');
    const statusBox = document.getElementById('gp-selected-status');
    const newMedFields = document.getElementById('gp-new-med-fields');
    const hargaHint = document.getElementById('gp-harga-hint');

    namaInput.value = typedName.trim();
    medIdInput.value = '';
    if (clearBtn) clearBtn.classList.remove('hidden');

    if (kemasanJumlahInput) kemasanJumlahInput.value = '';
    if (kemasanSatuanSelect) kemasanSatuanSelect.value = 'kg';

    if (newMedFields) newMedFields.classList.remove('hidden');

    if (hargaHint) {
        hargaHint.textContent = 'Obat baru pertama kali beli akan langsung disimpan ke Data Obat & Stok Obat.';
    }

    hideMedicineSuggestions();
    recalcGeneralStok();
}

function clearMedicineLookup() {
    selectedMedicine = null;
    const namaInput = document.getElementById('gp-nama-input');
    const medIdInput = document.getElementById('gp-medicine-id');
    const kemasanJumlahInput = document.getElementById('gp-kemasan-jumlah-input');
    const kemasanSatuanSelect = document.getElementById('gp-kemasan-satuan-select');
    const clearBtn = document.getElementById('gp-clear-name-btn');
    const statusBox = document.getElementById('gp-selected-status');
    const newMedFields = document.getElementById('gp-new-med-fields');
    const hargaHint = document.getElementById('gp-harga-hint');

    if (namaInput) {
        namaInput.value = '';
    }
    if (medIdInput) medIdInput.value = '';
    if (kemasanJumlahInput) kemasanJumlahInput.value = '';
    if (kemasanSatuanSelect) kemasanSatuanSelect.value = 'kg';
    if (clearBtn) clearBtn.classList.add('hidden');
    if (statusBox) statusBox.classList.add('hidden');
    if (newMedFields) newMedFields.classList.add('hidden');
    if (hargaHint) {
        hargaHint.textContent = 'Jika harga berbeda akan meng-update harga obat. Jika harga sama, hanya tanggal beli yang diperbarui.';
    }

    hideMedicineSuggestions();
    recalcGeneralStok();
}

function hideMedicineSuggestions() {
    const listEl = document.getElementById('gp-autocomplete-list');
    if (listEl) listEl.classList.add('hidden');
}

window.openGeneralPurchaseModal = function() {
    const modal = document.getElementById('general-purchase-modal');
    if (!modal) return;
    clearPurchasePhoto('gp');
    clearMedicineLookup();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => {
        const inp = document.getElementById('gp-nama-input');
        if (inp) {
            inp.focus();
            renderMedicineSuggestions(inp.value);
        }
    }, 100);
    recalcGeneralStok();
};

window.closeGeneralPurchaseModal = function() {
    const modal = document.getElementById('general-purchase-modal');
    if (!modal) return;
    clearPurchasePhoto('gp');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
    hideMedicineSuggestions();
};

function recalcGeneralStok() {
    const jumlahInput = document.getElementById('gp-jumlah-input');
    const curStokEl = document.getElementById('gp-stok-current');
    const qtyPreviewEl = document.getElementById('gp-qty-preview');
    const resStokEl = document.getElementById('gp-stok-result');

    let currentStok = 0;
    if (selectedMedicine) {
        currentStok = parseInt(selectedMedicine.stok) || 0;
    }

    const qty = Math.max(1, parseInt(jumlahInput?.value) || 1);
    if (curStokEl) curStokEl.textContent = currentStok;
    if (qtyPreviewEl) qtyPreviewEl.textContent = qty;
    if (resStokEl) resStokEl.textContent = currentStok + qty;
}

// ── Fungsi Kompresi Foto Otomatis (~80% kompresi) via Canvas ──
async function compressPurchasePhoto(file) {
    if (!file || !file.type.startsWith('image/')) {
        return { file, originalSize: file ? file.size : 0, compressedSize: file ? file.size : 0, dataUrl: null, savings: 0 };
    }

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                const maxWidth = 1280;
                const maxHeight = 1280;
                let width = img.width;
                let height = img.height;

                if (width > maxWidth || height > maxHeight) {
                    if (width > height) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    } else {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, width, height);
                ctx.drawImage(img, 0, 0, width, height);

                const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || 'nota_obat';

                function attemptCompression(quality) {
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: e.target.result, savings: 0 });
                            return;
                        }
                        if (blob.size >= file.size && quality > 0.25) {
                            attemptCompression(Math.max(0.2, quality - 0.15));
                            return;
                        }
                        if (blob.size < file.size) {
                            const compressedFile = new File([blob], `${baseName}.jpg`, {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            });
                            const savings = Math.round(((file.size - blob.size) / file.size) * 100);
                            resolve({
                                file: compressedFile,
                                originalSize: file.size,
                                compressedSize: blob.size,
                                dataUrl: canvas.toDataURL('image/jpeg', quality),
                                savings: savings
                            });
                        } else {
                            resolve({
                                file: file,
                                originalSize: file.size,
                                compressedSize: file.size,
                                dataUrl: e.target.result,
                                savings: 0
                            });
                        }
                    }, 'image/jpeg', quality);
                }

                attemptCompression(0.5);
            };
            img.onerror = () => resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: e.target.result, savings: 0 });
        };
        reader.onerror = () => resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: null, savings: 0 });
    });
}

// ── Photo Handler for Purchases (Kamera & Galeri) ──
window.handlePurchasePhotoSelected = async function(input, prefix) {
    if (!input.files || !input.files[0]) return;
    const rawFile = input.files[0];
    const targetInput = document.getElementById(prefix + '-foto-nota');

    const previewCard = document.getElementById(prefix + '-foto-preview-card');
    const previewImg = document.getElementById(prefix + '-foto-preview-img');
    const previewName = document.getElementById(prefix + '-foto-preview-name');
    const previewSize = document.getElementById(prefix + '-foto-preview-size');

    if (previewCard) {
        previewCard.classList.remove('hidden');
        previewCard.classList.add('flex');
    }
    if (previewName) previewName.innerText = rawFile.name || 'Foto Nota';
    if (previewSize) previewSize.innerText = 'Mengompres foto (~80%)...';

    let result = { file: rawFile, dataUrl: null, savings: 0 };
    try {
        result = await compressPurchasePhoto(rawFile);
    } catch (e) {
        console.warn('Kompresi nota error, memakai file asli:', e);
        result = { file: rawFile, dataUrl: null, savings: 0 };
    }

    const finalFile = result.file;

    // Sinkronkan file terkompresi ke input file utama form (name="foto_nota")
    if (targetInput) {
        try {
            const dt = new DataTransfer();
            dt.items.add(finalFile);
            targetInput.files = dt.files;
        } catch (e) {
            console.warn('DataTransfer sync fallback:', e);
        }
    }

    if (previewCard && previewImg && previewName && previewSize) {
        previewName.innerText = finalFile.name || 'Foto Nota';
        const finalKb = Math.round(finalFile.size / 1024);
        if (result.savings > 0) {
            previewSize.innerText = `${finalKb} KB (Hemat ${result.savings}%)`;
        } else {
            previewSize.innerText = `${finalKb} KB`;
        }

        if (result.dataUrl) {
            previewImg.src = result.dataUrl;
        } else {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(finalFile);
        }
    }
};

window.clearPurchasePhoto = function(prefix) {
    const mainInput = document.getElementById(prefix + '-foto-nota');
    const cameraInput = document.getElementById(prefix + '-foto-camera');
    if (mainInput) mainInput.value = '';
    if (cameraInput) cameraInput.value = '';

    const previewCard = document.getElementById(prefix + '-foto-preview-card');
    const previewImg = document.getElementById(prefix + '-foto-preview-img');
    if (previewCard) {
        previewCard.classList.add('hidden');
        previewCard.classList.remove('flex');
    }
    if (previewImg) previewImg.src = '';
};

// ── Photo Viewer Modal Functions ──
let currentViewerPhotos = [];
let currentViewerIndex = 0;

window.openPhotoViewerModal = function(rawPhotos, title) {
    const photos = (Array.isArray(rawPhotos) ? rawPhotos : [rawPhotos]).filter(u => typeof u === 'string' && u.trim().length > 0 && u.trim() !== '/storage' && !u.endsWith('/storage/'));
    if (!photos.length) return;

    currentViewerPhotos = photos;
    currentViewerIndex = 0;

    const modal = document.getElementById('photo-viewer-modal');
    const titleEl = document.getElementById('pv-title');
    const imgEl = document.getElementById('pv-image');
    const linkEl = document.getElementById('pv-link');
    const counterEl = document.getElementById('pv-counter');
    const thumbsContainer = document.getElementById('pv-thumbnails');

    if (titleEl) titleEl.textContent = title ? `Foto / Nota: ${title}` : 'Foto Obat / Nota';

    function setViewerPhoto(idx) {
        currentViewerIndex = idx;
        if (imgEl) imgEl.src = photos[idx];
        if (linkEl) linkEl.href = photos[idx];
        if (counterEl) {
            counterEl.textContent = photos.length > 1 ? `Foto ${idx + 1} dari ${photos.length}` : 'Foto Nota';
        }
        if (thumbsContainer) {
            thumbsContainer.querySelectorAll('.pv-thumb-btn').forEach((btn, bIdx) => {
                if (bIdx === idx) {
                    btn.className = 'pv-thumb-btn shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 border-amber-500 ring-2 ring-amber-400/30 transition-all cursor-pointer';
                } else {
                    btn.className = 'pv-thumb-btn shrink-0 w-12 h-12 rounded-lg overflow-hidden border border-gray-200 opacity-60 hover:opacity-100 transition-all cursor-pointer';
                }
            });
        }
    }

    if (thumbsContainer) {
        if (photos.length > 1) {
            thumbsContainer.innerHTML = '';
            photos.forEach((url, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = idx === 0 
                    ? 'pv-thumb-btn shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 border-amber-500 ring-2 ring-amber-400/30 transition-all cursor-pointer'
                    : 'pv-thumb-btn shrink-0 w-12 h-12 rounded-lg overflow-hidden border border-gray-200 opacity-60 hover:opacity-100 transition-all cursor-pointer';
                btn.onclick = () => setViewerPhoto(idx);
                btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
                thumbsContainer.appendChild(btn);
            });
            thumbsContainer.classList.remove('hidden');
        } else {
            thumbsContainer.innerHTML = '';
            thumbsContainer.classList.add('hidden');
        }
    }

    setViewerPhoto(0);

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }
};

window.closePhotoViewerModal = function() {
    const modal = document.getElementById('photo-viewer-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.body.classList.remove('overflow-hidden');
};

// ── Quick Purchase Modal Functions ──
window.openQuickPurchaseModal = function(medicine) {
    if (!medicine) return;
    currentQuickMed = medicine;

    clearPurchasePhoto('qp');

    const modal = document.getElementById('quick-purchase-modal');
    const form = document.getElementById('quick-purchase-form');
    const nameEl = document.getElementById('qp-med-name');
    const currentStokEl = document.getElementById('qp-stok-current');
    const qtyPreviewEl = document.getElementById('qp-qty-preview');
    const resultStokEl = document.getElementById('qp-stok-result');
    const tokoInput = document.getElementById('qp-toko-input');
    const hargaInput = document.getElementById('qp-harga-input');
    const kemasanJumlahInput = document.getElementById('qp-kemasan-jumlah-input');
    const kemasanSatuanSelect = document.getElementById('qp-kemasan-satuan-select');
    const jumlahInput = document.getElementById('qp-jumlah-input');
    const catatanInput = document.getElementById('qp-catatan-input');

    if (!modal || !form) return;

    form.action = `/data-obat/${medicine.id}/pembelian`;
    const kemasanLabel = medicine.kemasan_label ? ` • ${medicine.kemasan_label}` : '';
    nameEl.textContent = `${medicine.nama} (${medicine.jenis || 'Obat Pertanian'})${kemasanLabel}`;

    const currentStok = parseInt(medicine.stok) || 0;
    currentStokEl.textContent = currentStok;

    jumlahInput.value = 1;
    qtyPreviewEl.textContent = '1';
    resultStokEl.textContent = currentStok + 1;

    let defaultToko = medicine.toko_obat || '';
    let defaultHarga = medicine.harga ? 'Rp ' + Number(medicine.harga).toLocaleString('id-ID') : '';
    if (Array.isArray(medicine.purchases) && medicine.purchases.length > 0) {
        const latest = medicine.purchases[0];
        defaultToko = latest.toko_obat || defaultToko;
        defaultHarga = latest.harga ? 'Rp ' + Number(latest.harga).toLocaleString('id-ID') : defaultHarga;
    }

    tokoInput.value = defaultToko;
    hargaInput.value = defaultHarga;
    if (kemasanJumlahInput) {
        kemasanJumlahInput.value = medicine.kemasan_jumlah !== null && medicine.kemasan_jumlah !== undefined ? medicine.kemasan_jumlah : '';
    }
    if (kemasanSatuanSelect) {
        kemasanSatuanSelect.value = medicine.kemasan_satuan || 'kg';
    }
    catatanInput.value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    if (hargaInput) hargaInput.focus();
};

window.closeQuickPurchaseModal = function() {
    const modal = document.getElementById('quick-purchase-modal');
    if (modal) {
        clearPurchasePhoto('qp');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.body.classList.remove('overflow-hidden');
    currentQuickMed = null;
};

document.addEventListener('DOMContentLoaded', () => {
    // Autocomplete & live typing for medicine lookup
    const gpNamaInput = document.getElementById('gp-nama-input');
    if (gpNamaInput) {
        gpNamaInput.addEventListener('input', function() {
            const val = this.value;
            const clearBtn = document.getElementById('gp-clear-name-btn');
            if (clearBtn) clearBtn.classList.toggle('hidden', !val);

            const matched = allMedicinesCatalog.find(m => m.nama.trim().toLowerCase() === val.trim().toLowerCase());
            if (matched) {
                selectMedicine(matched.id);
            } else if (val.trim().length > 0) {
                selectedMedicine = null;
                document.getElementById('gp-medicine-id').value = '';
                const newMedFields = document.getElementById('gp-new-med-fields');
                if (newMedFields) newMedFields.classList.remove('hidden');

                

                const hargaHint = document.getElementById('gp-harga-hint');
                if (hargaHint) {
                    hargaHint.textContent = 'Obat baru pertama kali beli akan langsung disimpan ke Data Obat & Stok Obat.';
                }
                recalcGeneralStok();
            } else {
                selectedMedicine = null;
                document.getElementById('gp-medicine-id').value = '';
                const statusBox = document.getElementById('gp-selected-status');
                if (statusBox) statusBox.classList.add('hidden');
                const newMedFields = document.getElementById('gp-new-med-fields');
                if (newMedFields) newMedFields.classList.add('hidden');
                recalcGeneralStok();
            }

            renderMedicineSuggestions(val);
        });

        gpNamaInput.addEventListener('focus', function() {
            renderMedicineSuggestions(this.value);
        });
    }

    document.addEventListener('click', function(e) {
        const listEl = document.getElementById('gp-autocomplete-list');
        const gpNamaInput = document.getElementById('gp-nama-input');
        if (listEl && !listEl.contains(e.target) && e.target !== gpNamaInput) {
            hideMedicineSuggestions();
        }
    });

    // Live qty & rupiah for general modal
    const gpJumlah = document.getElementById('gp-jumlah-input');
    const gpHarga = document.getElementById('gp-harga-input');
    if (gpJumlah) gpJumlah.addEventListener('input', recalcGeneralStok);
    if (gpHarga) {
        gpHarga.addEventListener('input', function() {
            let val = this.value.replace(/[^0-9]/g, '');
            this.value = val ? 'Rp ' + parseInt(val, 10).toLocaleString('id-ID') : '';
        });
    }

    // Live qty & rupiah for quick modal
    const qpJumlah = document.getElementById('qp-jumlah-input');
    const qpHarga = document.getElementById('qp-harga-input');
    if (qpJumlah) {
        qpJumlah.addEventListener('input', () => {
            const currentStok = parseInt(currentQuickMed?.stok) || 0;
            const qty = Math.max(1, parseInt(qpJumlah.value) || 1);
            document.getElementById('qp-stok-current').textContent = currentStok;
            document.getElementById('qp-qty-preview').textContent = qty;
            document.getElementById('qp-stok-result').textContent = currentStok + qty;
        });
    }
    if (qpHarga) {
        qpHarga.addEventListener('input', function() {
            let val = this.value.replace(/[^0-9]/g, '');
            this.value = val ? 'Rp ' + parseInt(val, 10).toLocaleString('id-ID') : '';
        });
    }

    // Pastikan foto tersinkron saat form pembelian disubmit
    ['general-purchase-form', 'quick-purchase-form'].forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;
        const prefix = formId === 'general-purchase-form' ? 'gp' : 'qp';
        form.addEventListener('submit', function() {
            const camInput = document.getElementById(prefix + '-foto-camera');
            const targetInput = document.getElementById(prefix + '-foto-nota');
            if ((!targetInput || !targetInput.files || !targetInput.files.length) && camInput && camInput.files && camInput.files.length) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(camInput.files[0]);
                    targetInput.files = dt.files;
                } catch(e) {}
            }
        });
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeGeneralPurchaseModal();
        closeQuickPurchaseModal();
    }
});
</script>
@endpush
@endsection
