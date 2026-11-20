@extends('layouts.app')

@section('title', 'Katalog Tanaman')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6 pb-12">

    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Katalog Tanaman</h1>
                <span id="catalog-count-badge"
                    class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $catalogs->count() }} Tanaman
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Panduan komprehensif per tanaman: Tahap Semai, Penanaman, Pemeliharaan, dan Tips Penting khusus komoditas terpilih.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('tanaman-katalog.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Tanaman</span>
            </a>
        </div>
    </div>

    {{-- ── Search & Filter Bar ── --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-gray-200/90 shadow-xs">
        <form method="GET" action="{{ route('tanaman-katalog.index') }}" class="relative flex-1" id="catalog-search-form" onsubmit="return true;">
            <label for="catalog-search-input" class="sr-only">Cari Tanaman</label>
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </span>
            <input type="text"
                   id="catalog-search-input"
                   name="q"
                   value="{{ request('q') }}"
                   autocomplete="off"
                   oninput="handleCatalogSearch(this.value)"
                   placeholder="Cari tanaman (e.g. Timun, Cabe, Tomat, Jagung)..."
                   class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            <button type="button"
                id="btn-clear-catalog-search"
                onclick="clearCatalogSearch()"
                class="{{ request('q') ? '' : 'hidden' }} absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                title="Hapus pencarian"
                aria-label="Hapus pencarian">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </form>

        @if(request('q'))
            <a href="{{ route('tanaman-katalog.index') }}"
               id="btn-reset-server-search"
               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors shrink-0">
                <span>Reset Pencarian</span>
            </a>
        @endif
    </div>

    {{-- ── Grid Katalog Tanaman ── --}}
    @if($catalogs->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center max-w-md mx-auto space-y-4 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-inner border border-emerald-100">
                🌱
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Belum Ada Tanaman di Katalog</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    @if(request('q'))
                        Tidak ada tanaman yang cocok dengan pencarian "<strong>{{ request('q') }}</strong>".
                    @else
                        Mulai bangun panduan budidaya dengan menambahkan tanaman pertama Anda.
                    @endif
                </p>
            </div>
            <a href="{{ route('tanaman-katalog.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Tanaman Baru</span>
            </a>
        </div>
    @else
        @php
            $themeMap = [
                'emerald' => ['hover_bg' => 'hover:bg-emerald-50/20', 'border' => 'border-emerald-100/90', 'hover_border' => 'hover:border-emerald-500', 'glow' => 'bg-emerald-200/40', 'btn' => 'bg-emerald-600 group-hover:bg-emerald-700', 'footer_border' => 'border-emerald-100/80', 'heading_hover' => 'group-hover:text-emerald-700', 'shadow_color' => 'rgba(22,163,74,0.25)', 'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
                'rose'    => ['hover_bg' => 'hover:bg-rose-50/20',    'border' => 'border-rose-100/90',    'hover_border' => 'hover:border-rose-500',    'glow' => 'bg-rose-200/40',    'btn' => 'bg-rose-600 group-hover:bg-rose-700',    'footer_border' => 'border-rose-100/80',    'heading_hover' => 'group-hover:text-rose-700',    'shadow_color' => 'rgba(239,68,68,0.25)',    'badge' => 'bg-rose-50 text-rose-800 border-rose-200'],
                'amber'   => ['hover_bg' => 'hover:bg-amber-50/20',   'border' => 'border-amber-100/90',   'hover_border' => 'hover:border-amber-500',   'glow' => 'bg-amber-200/40',   'btn' => 'bg-amber-600 group-hover:bg-amber-700',   'footer_border' => 'border-amber-100/80',   'heading_hover' => 'group-hover:text-amber-800',   'shadow_color' => 'rgba(234,179,8,0.30)',   'badge' => 'bg-amber-50 text-amber-800 border-amber-200'],
                'yellow'  => ['hover_bg' => 'hover:bg-yellow-50/20',  'border' => 'border-yellow-100/90',  'hover_border' => 'hover:border-yellow-500',  'glow' => 'bg-yellow-200/40',  'btn' => 'bg-yellow-600 group-hover:bg-yellow-700',  'footer_border' => 'border-yellow-100/80',  'heading_hover' => 'group-hover:text-yellow-800',  'shadow_color' => 'rgba(234,179,8,0.25)',  'badge' => 'bg-yellow-50 text-yellow-800 border-yellow-200'],
                'sky'     => ['hover_bg' => 'hover:bg-sky-50/20',     'border' => 'border-sky-100/90',     'hover_border' => 'hover:border-sky-500',     'glow' => 'bg-sky-200/40',     'btn' => 'bg-sky-600 group-hover:bg-sky-700',     'footer_border' => 'border-sky-100/80',     'heading_hover' => 'group-hover:text-sky-700',     'shadow_color' => 'rgba(14,165,233,0.25)',     'badge' => 'bg-sky-50 text-sky-800 border-sky-200'],
                'lime'    => ['hover_bg' => 'hover:bg-lime-50/20',    'border' => 'border-lime-100/90',    'hover_border' => 'hover:border-lime-500',    'glow' => 'bg-lime-200/40',    'btn' => 'bg-lime-600 group-hover:bg-lime-700',    'footer_border' => 'border-lime-100/80',    'heading_hover' => 'group-hover:text-lime-700',    'shadow_color' => 'rgba(101,163,13,0.25)',    'badge' => 'bg-lime-50 text-lime-800 border-lime-200'],
                'teal'    => ['hover_bg' => 'hover:bg-teal-50/20',    'border' => 'border-teal-100/90',    'hover_border' => 'hover:border-teal-500',    'glow' => 'bg-teal-200/40',    'btn' => 'bg-teal-600 group-hover:bg-teal-700',    'footer_border' => 'border-teal-100/80',    'heading_hover' => 'group-hover:text-teal-700',    'shadow_color' => 'rgba(13,148,136,0.25)',    'badge' => 'bg-teal-50 text-teal-800 border-teal-200'],
            ];
        @endphp

        <div id="catalog-grid" class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-6">
            @foreach($catalogs as $catalog)
                @php
                    $t = $themeMap[$catalog->theme] ?? $themeMap['emerald'];
                @endphp

                <div class="plant-card group relative bg-surface {{ $t['hover_bg'] }} border-2 {{ $t['border'] }} {{ $t['hover_border'] }} rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden cursor-pointer select-none"
                     data-name="{{ strtolower($catalog->name) }}"
                     data-keywords="{{ strtolower(($catalog->name ?? '') . ' ' . ($catalog->varietas ?? '') . ' ' . ($catalog->keywords ?? '') . ' ' . ($catalog->deskripsi ?? '')) }}"
                     onclick="openCatalogPlantMenuModal({{ $catalog->id }})">

                    <div class="relative z-10">
                        <div class="flex items-center justify-between gap-1">
                            <h3 class="text-sm sm:text-xl font-bold text-gray-900 leading-tight {{ $t['heading_hover'] }} transition-colors">
                                {{ $catalog->name }}
                            </h3>
                            <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-medium bg-gray-100 text-gray-500 flex-shrink-0 whitespace-nowrap">
                                Katalog
                            </span>
                        </div>
                    </div>

                    <div class="my-3 sm:my-6 flex items-center justify-center relative py-2 sm:py-4">
                        <div class="absolute w-24 h-24 sm:w-40 sm:h-40 rounded-full {{ $t['glow'] }} blur-xl sm:blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <span class="relative text-[3.8rem] sm:text-[7rem] leading-none select-none drop-shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:-rotate-3 plant-sway"
                              style="filter: drop-shadow(0 8px 24px {{ $t['shadow_color'] }});">{{ $catalog->emoji }}</span>
                    </div>

                    <div class="pt-3 sm:pt-4 border-t {{ $t['footer_border'] }} relative z-10">
                        <div class="w-full py-2 sm:py-2.5 px-3 sm:px-4 rounded-xl {{ $t['btn'] }} text-white text-[11px] sm:text-xs font-bold flex items-center justify-between transition-colors shadow-sm">
                            <span class="truncate">Menu & Panduan</span>
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty search state for client-side instant search --}}
        <div id="catalog-search-empty"
            class="hidden bg-white rounded-2xl border border-gray-200 p-10 text-center max-w-md mx-auto space-y-4 shadow-xs">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl border border-amber-100">
                🔍
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Tanaman Tidak Ditemukan</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Tidak ada tanaman yang cocok dengan pencarian "<strong id="search-empty-query"></strong>".
                </p>
            </div>
            <button type="button"
                onclick="clearCatalogSearch()"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-all cursor-pointer">
                <span>Hapus Pencarian</span>
            </button>
        </div>
    @endif

</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL 1: Dialog Pilihan Menu saat Kartu Tanaman Diklik (Sesuai Screenshot)
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="catalogPlantModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modalPlantTitle">

    <div id="catalogPlantCard"
         class="bg-white w-full max-w-lg max-h-[92vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-6 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 relative overflow-hidden">

        {{-- Mobile drag pill indicator --}}
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden flex-shrink-0"></div>

        {{-- Header --}}
        <div class="flex items-start justify-between pb-3 sm:pb-4 border-b border-gray-100 relative z-10 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="modalPlantIcon" class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-2xl shadow-inner flex-shrink-0">
                    🌱
                </div>
                <div>
                    <span id="modalPlantBadge" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 mb-0.5">
                        Katalog Tanaman
                    </span>
                    <h3 id="modalPlantTitle" class="text-base sm:text-xl font-bold text-gray-900 leading-tight">Nama Tanaman</h3>
                    <p id="modalPlantSubtitle" class="text-xs text-gray-500">Pilih menu tahapan budidaya atau pemeliharaan</p>
                </div>
            </div>
            <button type="button"
                    onclick="closeCatalogPlantModal()"
                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors flex-shrink-0"
                    aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- 4 Menu Options --}}
        <div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 overflow-y-auto flex-1 pr-1 relative z-10">

            {{-- 1. Tahap Semai Bibit --}}
            <div onclick="openStageViewer('semai')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openStageViewer('semai')"
                 class="w-full text-left group flex items-start gap-3 sm:gap-4 p-3 sm:p-3.5 rounded-2xl border-2 border-emerald-100 hover:border-emerald-500 bg-emerald-50/40 hover:bg-emerald-50/90 transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-emerald-800 transition-colors">
                            Tahap Semai Bibit
                        </h4>
                        <span class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            SOP Semai
                        </span>
                    </div>
                    <p id="modalSemaiDesc" class="text-xs text-gray-600 mt-1 leading-relaxed">
                        Panduan persemaian bibit, perlakuan benih, dan perawatan sebelum pindah tanam.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-emerald-700">
                        <span class="flex items-center">
                            <span>Buka Panduan Semai</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openStageViewer('semai', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-emerald-800 bg-emerald-100/90 hover:bg-emerald-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi panduan semai tanaman ini">
                                <svg class="w-3 h-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Tahap Penanaman --}}
            <div onclick="openStageViewer('penanaman')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openStageViewer('penanaman')"
                 class="w-full text-left group flex items-start gap-3 sm:gap-4 p-3 sm:p-3.5 rounded-2xl border-2 border-amber-100 hover:border-amber-400 bg-amber-50/40 hover:bg-amber-50/90 transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-amber-800 transition-colors">
                            Tahap Penanaman
                        </h4>
                        <span class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Olah Tanah &amp; Tanam
                        </span>
                    </div>
                    <p id="modalPenanamanDesc" class="text-xs text-gray-600 mt-1 leading-relaxed">
                        SOP persiapan lahan tanah gembur, pemupukan dasar organik, mulsa, dan penanaman.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-amber-800">
                        <span class="flex items-center">
                            <span>Buka Panduan Penanaman</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openStageViewer('penanaman', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-amber-800 bg-amber-100/90 hover:bg-amber-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi panduan penanaman tanaman ini">
                                <svg class="w-3 h-3 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Tahap Pemeliharaan --}}
            <div onclick="openStageViewer('pemeliharaan')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openStageViewer('pemeliharaan')"
                 class="w-full text-left group flex items-start gap-3 sm:gap-4 p-3 sm:p-3.5 rounded-2xl border-2 border-teal-100 hover:border-teal-500 bg-teal-50/40 hover:bg-teal-50/90 transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-teal-600/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-teal-800 transition-colors">
                            Tahap Pemeliharaan
                        </h4>
                        <span class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-teal-800 bg-teal-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Perawatan &amp; Nutrisi
                        </span>
                    </div>
                    <p id="modalPemeliharaanDesc" class="text-xs text-gray-600 mt-1 leading-relaxed">
                        Panduan perawatan harian, pemasangan ajir, pemangkasan tunas air, penyiraman, dan pemupukan.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-teal-700">
                        <span class="flex items-center">
                            <span>Buka Panduan Pemeliharaan</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openStageViewer('pemeliharaan', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-teal-800 bg-teal-100/90 hover:bg-teal-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi panduan pemeliharaan tanaman ini">
                                <svg class="w-3 h-3 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Tips dan Catatan Penting --}}
            <div onclick="openStageViewer('tips')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openStageViewer('tips')"
                 class="w-full text-left group flex items-start gap-3 sm:gap-4 p-3 sm:p-3.5 rounded-2xl border-2 border-indigo-100 hover:border-indigo-400 bg-indigo-50/40 hover:bg-indigo-50/90 transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-indigo-800 transition-colors">
                            Tips dan Catatan Penting
                        </h4>
                        <span class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-indigo-800 bg-indigo-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Aturan Lapangan
                        </span>
                    </div>
                    <p id="modalTipsDesc" class="text-xs text-gray-600 mt-1 leading-relaxed">
                        Instruksi pencegahan penyakit, kehati-hatian, pantangan pemupukan, dan tips sukses panen.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-indigo-700">
                        <span class="flex items-center">
                            <span>Buka Tips Penting</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openStageViewer('tips', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-indigo-800 bg-indigo-100/90 hover:bg-indigo-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi tips penting tanaman ini">
                                <svg class="w-3 h-3 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Problem & Solusi Hama --}}
            <div onclick="openStageViewer('hama')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openStageViewer('hama')"
                 class="w-full text-left group flex items-start gap-3 sm:gap-4 p-3 sm:p-3.5 rounded-2xl border-2 border-rose-100 hover:border-rose-400 bg-rose-50/40 hover:bg-rose-50/90 transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.002A11.959 11.959 0 0112 2.714zm0 13.036h.008v.008H12v-.008z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-rose-800 transition-colors">
                            Problem &amp; Solusi Hama
                        </h4>
                        <span class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-rose-800 bg-rose-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Hama &amp; Penyakit
                        </span>
                    </div>
                    <p id="modalHamaDesc" class="text-xs text-gray-600 mt-1 leading-relaxed">
                        Identifikasi gejala hama, jamur/penyakit, serta solusi pengendalian dan bahan aktif obat.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-rose-700">
                        <span class="flex items-center">
                            <span>Buka Solusi Hama</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openStageViewer('hama', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-rose-800 bg-rose-100/90 hover:bg-rose-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi problem & solusi hama tanaman ini">
                                <svg class="w-3 h-3 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Direct Catalog Link --}}
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
            <a id="modalLinkFullGuide"
               href="#"
               class="text-xs font-bold text-emerald-700 hover:underline inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                <span>Buka Semua Panduan di Halaman Penuh</span>
                <span class="ml-0.5">→</span>
            </a>
            <a id="modalLinkEdit"
               href="#"
               class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-800">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
                <span>Edit Tanaman</span>
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL 2: Tampilan Pembaca Konten Tahapan Khusus Tanaman Terpilih
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="stageContentModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true">

    <div id="stageContentCard"
         class="bg-white w-full max-w-2xl max-h-[92vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-6 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 relative overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 flex-shrink-0">
            <button type="button"
                    onclick="backToCatalogMenuModal()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Kembali ke Pilihan Menu</span>
            </button>
            <div class="flex items-center gap-2">
                <span id="stageViewerPlantIcon" class="text-xl">🌱</span>
                <span id="stageViewerPlantTitle" class="font-bold text-sm sm:text-base text-gray-900">Nama Tanaman</span>
            </div>
            <button type="button"
                    onclick="closeStageContentModal()"
                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- 4 Quick Switch Tabs --}}
        <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl my-3 overflow-x-auto no-scrollbar flex-shrink-0">
            <button type="button"
                    onclick="switchStageViewer('semai')"
                    id="tabViewerSemai"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                <span>Tahap Semai</span>
            </button>
            <button type="button"
                    onclick="switchStageViewer('penanaman')"
                    id="tabViewerPenanaman"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                </svg>
                <span>Penanaman</span>
            </button>
            <button type="button"
                    onclick="switchStageViewer('pemeliharaan')"
                    id="tabViewerPemeliharaan"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <span>Pemeliharaan</span>
            </button>
            <button type="button"
                    onclick="switchStageViewer('tips')"
                    id="tabViewerTips"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                </svg>
                <span>Tips Penting</span>
            </button>
            <button type="button"
                    onclick="switchStageViewer('hama')"
                    id="tabViewerHama"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.002A11.959 11.959 0 0112 2.714zm0 13.036h.008v.008H12v-.008z"/>
                </svg>
                <span>Problem &amp; Solusi Hama</span>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-y-auto pr-1 py-1 flex flex-col">
            {{-- VIEW MODE --}}
            <div id="stageViewMode" class="flex-1 flex flex-col">
                <div class="mb-3 flex items-center justify-between gap-2 flex-wrap">
                    <span id="stageViewerPillBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Tahap Semai &amp; Pembibitan
                    </span>
                    <div class="flex items-center gap-2">
                        <span id="stageViewerPlantSpecificTag" class="text-xs font-bold text-gray-400">
                            Khusus Tanaman Ini
                        </span>
                        <button type="button"
                                id="btnStageEditToggle"
                                onclick="toggleStageViewerEdit(true)"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            <span>Edit Isi</span>
                        </button>
                    </div>
                </div>

                <div id="stageViewerTextContent" class="text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line font-sans selection:bg-emerald-100 p-4 rounded-2xl bg-gray-50/70 border border-gray-100 flex-1">
                    <!-- Konten dinamis khusus tanaman tersebut -->
                </div>

                {{-- Khusus Tips & Hama: Tampilan Kartu mirip Menu Informasi Penting --}}
                <div id="stageViewerCardsContainer" class="hidden flex-1 flex flex-col space-y-3 min-h-0">
                    <div class="flex items-center justify-between gap-3 shrink-0">
                        <div class="relative flex-1">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="text"
                                   id="stageViewerSearchInput"
                                   oninput="filterStageViewerCards(this.value)"
                                   placeholder="Cari topik / gejala / hama / solusi..."
                                   class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-gray-200 text-xs text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
                        </div>
                        <span id="stageViewerCardCount" class="text-xs font-bold text-gray-500 whitespace-nowrap bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200">
                            0 Catatan
                        </span>
                    </div>
                    <div id="stageViewerCardsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 overflow-y-auto pr-1">
                        <!-- Kartu hasil parsing -->
                    </div>
                </div>

                <div id="stageViewerEmptyState" class="hidden text-center py-8 text-gray-400 space-y-3 bg-gray-50/60 rounded-2xl border border-dashed border-gray-200 p-6">
                    <p class="text-xs sm:text-sm">Panduan untuk tahapan ini belum diisi.</p>
                    <button type="button"
                            onclick="toggleStageViewerEdit(true)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.688-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Tulis Panduan Tahap Ini Sekarang</span>
                    </button>
                </div>
            </div>

            {{-- EDIT MODE --}}
            <div id="stageEditMode" class="hidden flex-1 flex flex-col space-y-3">
                <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-gray-700">Edit Panduan:</span>
                        <span id="stageEditLabel" class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">Tahap Semai</span>
                    </div>
                    <span class="text-[11px] text-gray-400">Tersimpan ke database katalog</span>
                </div>

                <div class="flex-1 flex flex-col">
                    <textarea id="stageViewerTextarea"
                              rows="9"
                              class="w-full flex-1 text-xs sm:text-sm text-gray-800 p-3.5 rounded-2xl bg-white border-2 border-emerald-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-200 outline-none font-sans leading-relaxed resize-y shadow-inner"
                              placeholder="Tuliskan panduan langkah demi langkah khusus tahapan tanaman ini..."></textarea>
                </div>

                <div id="stageEditAlert" class="hidden p-2.5 rounded-xl text-xs font-semibold"></div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button"
                            onclick="toggleStageViewerEdit(false)"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            id="btnSaveStageContent"
                            onclick="saveCatalogStageContent()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div id="stageViewerFooter" class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <button type="button"
                    onclick="backToCatalogMenuModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                ← Pilihan Tahapan
            </button>
            <div class="flex items-center gap-2">
                <a id="linkEditFromStageViewer"
                   href="#"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:text-emerald-700 bg-gray-50 hover:bg-emerald-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                    <span>Form Lengkap</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Database Katalog Tanaman untuk JavaScript
    const CATALOG_MAP = @json($catalogs->keyBy('id'));
    let currentCatalogId = null;
    let currentStageType = 'semai';

    // ── Real-time Search & Filter Katalog Tanaman ──
    function handleCatalogSearch(query) {
        const q = (query || '').trim().toLowerCase();
        const clearBtn = document.getElementById('btn-clear-catalog-search');
        if (clearBtn) {
            if (q.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        const grid = document.getElementById('catalog-grid');
        if (!grid) return;

        const cards = grid.querySelectorAll('.plant-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();
            const matches = !q || name.includes(q) || keywords.includes(q);
            if (matches) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const emptySearchState = document.getElementById('catalog-search-empty');
        if (emptySearchState) {
            if (visibleCount === 0 && cards.length > 0) {
                emptySearchState.classList.remove('hidden');
                const querySpan = document.getElementById('search-empty-query');
                if (querySpan) querySpan.textContent = query;
            } else {
                emptySearchState.classList.add('hidden');
            }
        }

        const countBadge = document.getElementById('catalog-count-badge');
        if (countBadge) {
            if (q) {
                countBadge.textContent = `${visibleCount} Tanaman Ditemukan`;
            } else {
                countBadge.textContent = `${cards.length} Tanaman`;
            }
        }
    }

    function clearCatalogSearch() {
        const input = document.getElementById('catalog-search-input');
        if (input) {
            input.value = '';
            input.focus();
            handleCatalogSearch('');
        }
        const serverResetBtn = document.getElementById('btn-reset-server-search');
        if (serverResetBtn) {
            serverResetBtn.classList.add('hidden');
        }
        if (window.location.search.includes('q=')) {
            const url = new URL(window.location.href);
            url.searchParams.delete('q');
            window.history.replaceState({}, '', url.pathname);
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const input = document.getElementById('catalog-search-input');
            if (input && document.activeElement === input && input.value) {
                clearCatalogSearch();
            }
        }
    });

    // Inisialisasi status search pada saat load jika sudah ada query
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('catalog-search-input');
        if (searchInput && searchInput.value) {
            handleCatalogSearch(searchInput.value);
        }
    });

    function openCatalogPlantMenuModal(id) {
        const plant = CATALOG_MAP[id];
        if (!plant) return;

        currentCatalogId = id;

        // Set info header
        document.getElementById('modalPlantIcon').innerText = plant.emoji || '🌱';
        document.getElementById('modalPlantTitle').innerText = plant.name;
        const cycleText = (plant.cycle && plant.cycle !== '—') ? plant.cycle : '';
        const badgeText = plant.varietas
            ? (cycleText ? plant.varietas + ' • ' + cycleText : plant.varietas)
            : (cycleText || 'Panduan Budidaya');
        document.getElementById('modalPlantBadge').innerText = badgeText;

        // Deskripsi sub-menu
        document.getElementById('modalSemaiDesc').innerText = 'Panduan persemaian bibit khusus ' + plant.name + ', perlakuan benih, media tray, dan waktu semai.';
        document.getElementById('modalPenanamanDesc').innerText = 'SOP penanaman bibit ' + plant.name + ', persiapan lahan tanah gembur, jarak tanam, dan waktu pindah tanam.';
        document.getElementById('modalPemeliharaanDesc').innerText = 'Panduan pemeliharaan rutin ' + plant.name + ', pemangkasan tunas air, ajir, penyiraman, dan pemupukan.';
        document.getElementById('modalTipsDesc').innerText = 'Tips krusial, rahasia hasil maksimal, dan pantangan penting khusus ' + plant.name + '.';
        document.getElementById('modalHamaDesc').innerText = 'Identifikasi problem hama & penyakit tanaman ' + plant.name + ' beserta solusinya.';

        // Link Halaman Lengkap & Edit
        document.getElementById('modalLinkFullGuide').href = '/tanaman-katalog/' + plant.id;
        document.getElementById('modalLinkEdit').href = '/tanaman-katalog/' + plant.id + '/edit';

        // Tampilkan Modal Pilihan
        const modal = document.getElementById('catalogPlantModal');
        const card = document.getElementById('catalogPlantCard');
        if (modal && card) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            card.classList.remove('translate-y-8', 'sm:scale-95');
            card.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function closeCatalogPlantModal() {
        const modal = document.getElementById('catalogPlantModal');
        const card = document.getElementById('catalogPlantCard');
        if (modal && card) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
            card.classList.remove('translate-y-0', 'sm:scale-100');
            card.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    let isCatalogStageEditing = false;

    function openStageViewer(stageType, autoEdit = false) {
        closeCatalogPlantModal();
        currentStageType = stageType;

        const plant = CATALOG_MAP[currentCatalogId];
        if (!plant) return;

        // Header Viewer
        document.getElementById('stageViewerPlantIcon').innerText = plant.emoji || '🌱';
        document.getElementById('stageViewerPlantTitle').innerText = plant.name;
        document.getElementById('stageViewerPlantSpecificTag').innerText = 'Khusus Tanaman ' + plant.name;

        const editUrl = '/tanaman-katalog/' + plant.id + '/edit';
        const linkEdit = document.getElementById('linkEditFromStageViewer');
        if (linkEdit) linkEdit.href = editUrl;

        switchStageViewer(stageType);

        if (autoEdit) {
            toggleStageViewerEdit(true);
        } else {
            toggleStageViewerEdit(false);
        }

        // Tampilkan Modal Viewer
        const stageModal = document.getElementById('stageContentModal');
        const stageCard = document.getElementById('stageContentCard');
        if (stageModal && stageCard) {
            stageModal.classList.remove('opacity-0', 'pointer-events-none');
            stageModal.classList.add('opacity-100');
            stageCard.classList.remove('translate-y-8', 'sm:scale-95');
            stageCard.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function toggleStageViewerEdit(isEditing) {
        isCatalogStageEditing = Boolean(isEditing);
        const viewMode = document.getElementById('stageViewMode');
        const editMode = document.getElementById('stageEditMode');
        const alertEl = document.getElementById('stageEditAlert');
        const textarea = document.getElementById('stageViewerTextarea');
        const labelEl = document.getElementById('stageEditLabel');

        if (alertEl) {
            alertEl.classList.add('hidden');
            alertEl.innerText = '';
        }

        const plant = CATALOG_MAP[currentCatalogId];
        if (!plant) return;

        const stageLabels = {
            'semai': 'Tahap Semai & Pembibitan',
            'penanaman': 'Tahap Penanaman & Jarak Tanam',
            'pemeliharaan': 'Tahap Pemeliharaan & Nutrisi',
            'tips': 'Tips dan Catatan Penting',
            'hama': 'Problem & Solusi Hama / Penyakit'
        };

        const stagePropMap = {
            'semai': 'tahap_semai',
            'penanaman': 'tahap_penanaman',
            'pemeliharaan': 'tahap_pemeliharaan',
            'tips': 'tips_penting',
            'hama': 'hama_penyakit'
        };

        if (labelEl) {
            labelEl.innerText = (stageLabels[currentStageType] || currentStageType) + ' (' + plant.name + ')';
        }

        if (isCatalogStageEditing) {
            if (viewMode) viewMode.classList.add('hidden');
            if (editMode) editMode.classList.remove('hidden');
            if (textarea) {
                const currentVal = plant[stagePropMap[currentStageType]] || '';
                textarea.value = currentVal;
                setTimeout(() => textarea.focus(), 80);
            }
        } else {
            if (editMode) editMode.classList.add('hidden');
            if (viewMode) viewMode.classList.remove('hidden');
        }
    }

    async function saveCatalogStageContent() {
        const plant = CATALOG_MAP[currentCatalogId];
        if (!plant) return;

        const textarea = document.getElementById('stageViewerTextarea');
        const btnSave = document.getElementById('btnSaveStageContent');
        const alertEl = document.getElementById('stageEditAlert');

        const newContent = textarea ? textarea.value.trim() : '';

        // UI Loading State
        if (btnSave) {
            btnSave.disabled = true;
            btnSave.innerHTML = `<svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyimpan...</span>`;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const response = await fetch('{{ route("tanaman-katalog.update-stage") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    catalog_id: plant.id,
                    plant_name: plant.name,
                    stage_type: currentStageType,
                    content: newContent,
                    emoji: plant.emoji || '🌱'
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Update local catalog object
                const stagePropMap = {
                    'semai': 'tahap_semai',
                    'penanaman': 'tahap_penanaman',
                    'pemeliharaan': 'tahap_pemeliharaan',
                    'tips': 'tips_penting',
                    'hama': 'hama_penyakit'
                };
                plant[stagePropMap[currentStageType]] = newContent;

                // Update text in view mode
                switchStageViewer(currentStageType);
                toggleStageViewerEdit(false);

                // Show toast notification
                if (window.AgriSwal && typeof window.AgriSwal.toastSuccess === 'function') {
                    window.AgriSwal.toastSuccess(data.message || 'Panduan berhasil disimpan!');
                }
            } else {
                throw new Error(data.message || 'Gagal menyimpan perubahan.');
            }
        } catch (err) {
            if (alertEl) {
                alertEl.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200';
                alertEl.innerText = err.message || 'Terjadi kesalahan saat menyimpan panduan.';
                alertEl.classList.remove('hidden');
            }
        } finally {
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> <span>Simpan Perubahan</span>`;
            }
        }
    }

    function switchStageViewer(type) {
        currentStageType = type;
        const plant = CATALOG_MAP[currentCatalogId];
        if (!plant) return;

        if (isCatalogStageEditing) {
            toggleStageViewerEdit(true);
        }

        // Tab styling reset
        const tabs = ['semai', 'penanaman', 'pemeliharaan', 'tips', 'hama'];
        tabs.forEach(t => {
            const btn = document.getElementById('tabViewer' + t.charAt(0).toUpperCase() + t.slice(1));
            if (btn) {
                if (t === type) {
                    btn.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap bg-emerald-600 text-white shadow-xs';
                } else {
                    btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap text-gray-600 hover:text-gray-900 hover:bg-gray-200/80 transition-all';
                }
            }
        });

        const badgeEl = document.getElementById('stageViewerPillBadge');
        const textEl = document.getElementById('stageViewerTextContent');
        const emptyEl = document.getElementById('stageViewerEmptyState');

        let content = '';
        let badgeLabel = '';
        let badgeClass = '';

        if (type === 'semai') {
            content = plant.tahap_semai || '';
            badgeLabel = 'Tahap Semai & Pembibitan ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else if (type === 'penanaman') {
            content = plant.tahap_penanaman || '';
            badgeLabel = 'Tahap Penanaman & Jarak Tanam ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200';
        } else if (type === 'pemeliharaan') {
            content = plant.tahap_pemeliharaan || '';
            badgeLabel = 'Tahap Pemeliharaan & Nutrisi ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200';
        } else if (type === 'tips') {
            content = plant.tips_penting || '';
            badgeLabel = 'Tips Penting & Pantangan ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200';
        } else if (type === 'hama') {
            content = plant.hama_penyakit || '';
            badgeLabel = 'Problem & Solusi Hama ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200';
        }

        badgeEl.innerText = badgeLabel;
        badgeEl.className = badgeClass;

        const cardsContainer = document.getElementById('stageViewerCardsContainer');
        const cardsGrid = document.getElementById('stageViewerCardsGrid');
        const searchInput = document.getElementById('stageViewerSearchInput');
        const countEl = document.getElementById('stageViewerCardCount');

        if (content && content.trim() !== '') {
            emptyEl.classList.add('hidden');
            textEl.classList.add('hidden');
            if (cardsContainer) cardsContainer.classList.remove('hidden');
            if (searchInput) searchInput.value = '';

            if (type === 'tips' || type === 'hama') {
                if (cardsGrid) cardsGrid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3.5 overflow-y-auto pr-1';
                if (searchInput) searchInput.placeholder = 'Cari topik / gejala / hama / solusi...';

                const cardList = parseNotesToCards(content, type, plant.name);
                if (countEl) countEl.innerText = cardList.length + ' Catatan';

                let html = '';
                cardList.forEach(card => {
                    const isHama = (type === 'hama');
                    const hoverBorder = isHama ? 'hover:border-rose-400' : 'hover:border-amber-400';
                    const iconBg = isHama ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                    const hoverTitle = isHama ? 'group-hover:text-rose-900' : 'group-hover:text-amber-900';
                    const badgeText = isHama ? 'Solusi Terverifikasi' : 'SOP Terverifikasi';
                    const badgeColor = isHama ? 'text-rose-700' : 'text-emerald-700';

                    const iconSvg = isHama
                        ? `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.002A11.959 11.959 0 0112 2.714zm0 13.036h.008v.008H12v-.008z"/></svg>`
                        : `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/></svg>`;

                    html += `
                        <div class="note-card bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs ${hoverBorder} hover:shadow-md transition-all flex flex-col justify-between group"
                             data-title="${escapeHtml(card.title)}"
                             data-content="${escapeHtml(card.content)}">
                            <div>
                                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <span class="w-9 h-9 rounded-xl ${iconBg} flex items-center justify-center shrink-0 border mt-0.5">
                                            ${iconSvg}
                                        </span>
                                        <div class="min-w-0">
                                            <h3 class="text-sm sm:text-base font-bold text-gray-900 ${hoverTitle} transition-colors leading-snug">
                                                ${escapeHtml(card.title)}
                                            </h3>
                                            <span class="text-[10px] text-gray-400 mt-0.5 block">
                                                ${escapeHtml(card.subtitle)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                ${(() => {
                                    if (isHama) {
                                        let gejala = '';
                                        let solusi = '';
                                        const mGejala = (card.content || '').match(/Gejala\s*:\s*([^]+?)(?=(?:Solusi\s*:|$))/i);
                                        const mSolusi = (card.content || '').match(/Solusi\s*:\s*([^]+)$/i);
                                        if (mGejala) gejala = mGejala[1].trim();
                                        if (mSolusi) solusi = mSolusi[1].trim();

                                        if (gejala || solusi) {
                                            return `
                                                ${gejala ? `
                                                    <div class="mt-3 rounded-xl p-3 bg-amber-50/70 border border-amber-200/80 text-amber-950">
                                                        <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 mb-1">
                                                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                                            </svg>
                                                            <span>Gejala:</span>
                                                        </div>
                                                        <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-line">${escapeHtml(gejala)}</p>
                                                    </div>
                                                ` : ''}
                                                ${solusi ? `
                                                    <div class="mt-2 rounded-xl p-3 bg-emerald-50/70 border border-emerald-200/80 text-emerald-950">
                                                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-900 mb-1">
                                                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span>Solusi &amp; Pengendalian:</span>
                                                        </div>
                                                        <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-line">${escapeHtml(solusi)}</p>
                                                    </div>
                                                ` : ''}
                                            `;
                                        }
                                    }
                                    return `<div class="text-xs sm:text-sm text-gray-700 mt-3 leading-relaxed whitespace-pre-line">${escapeHtml(card.content)}</div>`;
                                })()}
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                <span class="flex items-center gap-1 ${badgeColor} font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>${badgeText}</span>
                                </span>
                                <span>Tersimpan di Sistem</span>
                            </div>
                        </div>
                    `;
                });
                if (cardsGrid) cardsGrid.innerHTML = html;
            } else {
                // Tampilan mirip Menu Tahapan (semai, penanaman, pemeliharaan)
                if (cardsGrid) cardsGrid.className = 'flex flex-col space-y-3.5 overflow-y-auto pr-1';
                if (searchInput) searchInput.placeholder = 'Cari langkah / instruksi tahapan...';

                const stepList = parseRawTextToSteps(content, type, plant.name);
                if (countEl) countEl.innerText = stepList.length + ' Langkah';

                const themeMap = {
                    'semai': {
                        numBg: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        hoverBorder: 'hover:border-emerald-300',
                        badgeBg: 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
                        hoverTitle: 'group-hover:text-emerald-900',
                        sopBadge: 'SOP Semai Terverifikasi'
                    },
                    'penanaman': {
                        numBg: 'bg-amber-100 text-amber-800 border-amber-200',
                        hoverBorder: 'hover:border-amber-300',
                        badgeBg: 'bg-amber-50 text-amber-800 border-amber-200/60',
                        hoverTitle: 'group-hover:text-amber-900',
                        sopBadge: 'SOP Penanaman Terverifikasi'
                    },
                    'pemeliharaan': {
                        numBg: 'bg-sky-100 text-sky-800 border-sky-200',
                        hoverBorder: 'hover:border-sky-300',
                        badgeBg: 'bg-sky-50 text-sky-800 border-sky-200/60',
                        hoverTitle: 'group-hover:text-sky-900',
                        sopBadge: 'SOP Perawatan Terverifikasi'
                    }
                };
                const t = themeMap[type] || themeMap['semai'];

                let html = '';
                stepList.forEach(step => {
                    html += `
                        <div class="note-card bg-surface rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs ${t.hoverBorder} hover:shadow-sm transition-all group"
                             data-title="${escapeHtml(step.judul)}"
                             data-content="${escapeHtml(step.deskripsi)}">
                            <div class="flex items-start gap-3.5 sm:gap-4">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl ${t.numBg} flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border shadow-2xs">
                                    ${escapeHtml(step.nomor)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-2">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 ${t.hoverTitle} transition-colors leading-snug">
                                            ${escapeHtml(step.judul)}
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold ${t.badgeBg} w-fit shrink-0">
                                            ${escapeHtml(step.badge)}
                                        </span>
                                    </div>
                                    ${step.waktu ? `
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                Waktu: ${escapeHtml(step.waktu)}
                                            </span>
                                        </div>
                                    ` : ''}
                                    <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                                        ${escapeHtml(step.deskripsi)}
                                    </div>
                                    <div class="mt-3.5 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                        <span class="flex items-center gap-1 text-emerald-700 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>${t.sopBadge}</span>
                                        </span>
                                        <span>Standar Operasional Lapangan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                if (cardsGrid) cardsGrid.innerHTML = html;
            }
        } else {
            textEl.classList.add('hidden');
            if (cardsContainer) cardsContainer.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function parseNotesToCards(rawText, type, plantName) {
        if (!rawText || !rawText.trim()) return [];

        const lines = rawText.replace(/\r\n/g, '\n').split('\n');
        const cards = [];
        const defaultCategory = (type === 'hama') ? 'Hama & Penyakit' : 'Aturan Lapangan';
        let currentCategory = defaultCategory;
        let currentCard = null;

        function finalize(c) {
            let clean = c.contentLines.join('\n').trim().replace(/\n{3,}/g, '\n\n');
            let sub = c.category || defaultCategory;
            if (plantName) sub += ' • ' + plantName;
            return {
                title: c.title || (type === 'hama' ? 'Pengendalian Hama' : 'Tips Lapangan'),
                subtitle: sub,
                category: c.category || '',
                content: clean || c.title
            };
        }

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const trimmed = line.trim();
            if (!trimmed) {
                if (currentCard && currentCard.contentLines.length > 0) {
                    currentCard.contentLines.push('');
                }
                continue;
            }

            const secMatch = trimmed.match(/^(?:[0-9]+\.\s*)?(Hama Utama.*?|Penyakit Utama.*?|Aturan Lapangan.*?|Panduan.*?):?$/i);
            if (secMatch) {
                currentCategory = secMatch[1].replace(/:$/, '').trim();
                continue;
            }

            const bulletWithColon = trimmed.match(/^[•\-*]\s+([^:\n]{3,70}):(.*)$/);
            const numWithColon = trimmed.match(/^[0-9]+\.\s+([^:\n]{3,70}):(.*)$/);
            const bulletNoColon = trimmed.match(/^[•\-*]\s+(.+)$/);
            const numNoColon = trimmed.match(/^[0-9]+\.\s+(.+)$/);

            if (bulletWithColon || numWithColon) {
                if (currentCard) cards.push(finalize(currentCard));
                const m = bulletWithColon || numWithColon;
                const title = m[1].trim();
                const remainder = m[2].trim();
                currentCard = {
                    title: title,
                    category: currentCategory,
                    contentLines: remainder ? [remainder] : []
                };
            } else if (bulletNoColon || numNoColon) {
                const isIndented = line.match(/^\s{2,}[•\-*]/);
                if (isIndented && currentCard) {
                    currentCard.contentLines.push(trimmed);
                } else {
                    if (currentCard) cards.push(finalize(currentCard));
                    const text = (bulletNoColon || numNoColon)[1].trim();
                    const colonIdx = text.indexOf(':');

                    if (colonIdx > 2 && colonIdx < 55) {
                        currentCard = {
                            title: text.substring(0, colonIdx).trim(),
                            category: currentCategory,
                            contentLines: [text.substring(colonIdx + 1).trim()].filter(Boolean)
                        };
                    } else {
                        const commaMatch = text.match(/^([^.,;]{10,55})[.,;](.+)$/);
                        if (commaMatch) {
                            currentCard = {
                                title: commaMatch[1].trim(),
                                category: currentCategory,
                                contentLines: [text]
                            };
                        } else {
                            const prefix = (type === 'hama') ? 'Problem Hama #' : 'Tips Budidaya #';
                            currentCard = {
                                title: prefix + (cards.length + 1),
                                category: currentCategory,
                                contentLines: [text]
                            };
                        }
                    }
                }
            } else {
                if (currentCard) {
                    currentCard.contentLines.push(trimmed);
                } else {
                    const defTitle = (type === 'hama')
                        ? 'Problem & Solusi Hama' + (plantName ? ' ' + plantName : '')
                        : 'Tips Penting' + (plantName ? ' ' + plantName : '');
                    currentCard = {
                        title: defTitle,
                        category: currentCategory,
                        contentLines: [trimmed]
                    };
                }
            }
        }

        if (currentCard) cards.push(finalize(currentCard));
        return cards;
    }

    function parseRawTextToSteps(rawText, stageType, plantName) {
        if (!rawText || !rawText.trim()) return [];

        const lines = rawText.replace(/\r\n/g, '\n').split('\n');
        const steps = [];
        const stageLabels = {
            'semai': 'Tahap Semai',
            'penanaman': 'Tahap Penanaman',
            'pemeliharaan': 'Tahap Perawatan'
        };
        const stageBadge = stageLabels[stageType] || 'Tahapan SOP';
        let currentStep = null;

        function finalize(st, fallbackIdx) {
            let content = (st.lines || []).join('\n').trim().replace(/\n{3,}/g, '\n\n');
            let waktu = null;
            const timeMatch = content.match(/(?:waktu|durasi|umur|interval|fase)\s*:\s*([^,\n\(\)]+)/i);
            if (timeMatch) waktu = timeMatch[1].trim();

            return {
                nomor: st.nomor || String(fallbackIdx),
                judul: st.judul || ('Langkah ' + (st.nomor || fallbackIdx)),
                deskripsi: content,
                waktu: waktu,
                badge: stageBadge,
                plantName: plantName || ''
            };
        }

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const trimmed = line.trim();
            if (trimmed === '') {
                if (currentStep && currentStep.lines.length > 0) {
                    currentStep.lines.push('');
                }
                continue;
            }

            const numMatch = trimmed.match(/^(?:Langkah|Tahap)?\s*([0-9]+)[\.\:\)]\s*(.+)$/i);
            if (numMatch) {
                if (currentStep) {
                    steps.push(finalize(currentStep, steps.length + 1));
                }
                const stepNum = numMatch[1];
                const titleAndRest = numMatch[2].trim();
                const colonPos = titleAndRest.indexOf(':');
                if (colonPos !== -1 && colonPos < 65) {
                    const j = titleAndRest.slice(0, colonPos).trim();
                    const r = titleAndRest.slice(colonPos + 1).trim();
                    currentStep = {
                        nomor: stepNum,
                        judul: j || titleAndRest,
                        lines: r ? [r] : []
                    };
                } else {
                    currentStep = {
                        nomor: stepNum,
                        judul: titleAndRest.replace(/:$/, ''),
                        lines: []
                    };
                }
            } else {
                if (currentStep) {
                    currentStep.lines.push(trimmed);
                } else {
                    currentStep = {
                        nomor: '1',
                        judul: (stageLabels[stageType] || 'Tahapan') + (plantName ? ' ' + plantName : ''),
                        lines: [trimmed]
                    };
                }
            }
        }

        if (currentStep) {
            steps.push(finalize(currentStep, steps.length + 1));
        }

        return steps;
    }

    function filterStageViewerCards(query) {
        const q = (query || '').toLowerCase().trim();
        const grid = document.getElementById('stageViewerCardsGrid');
        if (!grid) return;
        const cards = grid.querySelectorAll('.note-card');
        let count = 0;
        cards.forEach(card => {
            const t = (card.getAttribute('data-title') || '').toLowerCase();
            const c = (card.getAttribute('data-content') || '').toLowerCase();
            if (!q || t.includes(q) || c.includes(q)) {
                card.classList.remove('hidden');
                count++;
            } else {
                card.classList.add('hidden');
            }
        });
        const countEl = document.getElementById('stageViewerCardCount');
        if (countEl) {
            const unit = (currentStageType === 'tips' || currentStageType === 'hama') ? ' Catatan' : ' Langkah';
            countEl.innerText = count + unit;
        }
    }

    function closeStageContentModal() {
        const stageModal = document.getElementById('stageContentModal');
        const stageCard = document.getElementById('stageContentCard');
        if (stageModal && stageCard) {
            stageModal.classList.remove('opacity-100');
            stageModal.classList.add('opacity-0', 'pointer-events-none');
            stageCard.classList.remove('translate-y-0', 'sm:scale-100');
            stageCard.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    function backToCatalogMenuModal() {
        closeStageContentModal();
        setTimeout(function() {
            if (currentCatalogId) {
                openCatalogPlantMenuModal(currentCatalogId);
            }
        }, 150);
    }
</script>
<style>
    @keyframes plantSway {
        0%, 100% { transform: rotate(-2deg) scale(1); }
        50%       { transform: rotate(2deg)  scale(1.03); }
    }
    .plant-sway {
        animation: plantSway 4s ease-in-out infinite;
        transform-origin: bottom center;
        display: inline-block;
    }
</style>
@endsection
