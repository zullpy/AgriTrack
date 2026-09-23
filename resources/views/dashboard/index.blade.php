@extends('layouts.app')

@section('title', 'Dashboard Tanaman')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Bar / Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-gray-100">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Katalog Tanaman & Panduan</h1>
            <p class="text-sm text-text-muted mt-0.5">Pilih salah satu tanaman di bawah untuk mengakses <strong>Kalender HST</strong> dan <strong>Panduan Pemupukan</strong>.</p>
        </div>
    </div>


    {{-- Plant Cards Grid (Dynamic) --}}
    @php
        $themeMap = [
            'emerald' => ['hover_bg' => 'hover:bg-emerald-50/20', 'border' => 'border-emerald-100/90', 'hover_border' => 'hover:border-emerald-500', 'glow' => 'bg-emerald-200/40', 'btn' => 'bg-emerald-600 group-hover:bg-emerald-700', 'footer_border' => 'border-emerald-100/80', 'heading_hover' => 'group-hover:text-emerald-700', 'active_bg' => 'bg-emerald-500', 'shadow_color' => 'rgba(22,163,74,0.25)'],
            'rose'    => ['hover_bg' => 'hover:bg-rose-50/20',    'border' => 'border-rose-100/90',    'hover_border' => 'hover:border-rose-500',    'glow' => 'bg-rose-200/40',    'btn' => 'bg-rose-600 group-hover:bg-rose-700',    'footer_border' => 'border-rose-100/80',    'heading_hover' => 'group-hover:text-rose-700',    'active_bg' => 'bg-rose-500',    'shadow_color' => 'rgba(239,68,68,0.25)'],
            'amber'   => ['hover_bg' => 'hover:bg-amber-50/20',   'border' => 'border-amber-100/90',   'hover_border' => 'hover:border-amber-500',   'glow' => 'bg-amber-200/40',   'btn' => 'bg-amber-600 group-hover:bg-amber-700',   'footer_border' => 'border-amber-100/80',   'heading_hover' => 'group-hover:text-amber-800',   'active_bg' => 'bg-amber-500',   'shadow_color' => 'rgba(234,179,8,0.30)'],
            'yellow'  => ['hover_bg' => 'hover:bg-yellow-50/20',  'border' => 'border-yellow-100/90',  'hover_border' => 'hover:border-yellow-500',  'glow' => 'bg-yellow-200/40',  'btn' => 'bg-yellow-600 group-hover:bg-yellow-700',  'footer_border' => 'border-yellow-100/80',  'heading_hover' => 'group-hover:text-yellow-800',  'active_bg' => 'bg-yellow-500',  'shadow_color' => 'rgba(234,179,8,0.25)'],
            'sky'     => ['hover_bg' => 'hover:bg-sky-50/20',     'border' => 'border-sky-100/90',     'hover_border' => 'hover:border-sky-500',     'glow' => 'bg-sky-200/40',     'btn' => 'bg-sky-600 group-hover:bg-sky-700',     'footer_border' => 'border-sky-100/80',     'heading_hover' => 'group-hover:text-sky-700',     'active_bg' => 'bg-sky-500',     'shadow_color' => 'rgba(14,165,233,0.25)'],
            'lime'    => ['hover_bg' => 'hover:bg-lime-50/20',    'border' => 'border-lime-100/90',    'hover_border' => 'hover:border-lime-500',    'glow' => 'bg-lime-200/40',    'btn' => 'bg-lime-600 group-hover:bg-lime-700',    'footer_border' => 'border-lime-100/80',    'heading_hover' => 'group-hover:text-lime-700',    'active_bg' => 'bg-lime-500',    'shadow_color' => 'rgba(101,163,13,0.25)'],
            'teal'    => ['hover_bg' => 'hover:bg-teal-50/20',    'border' => 'border-teal-100/90',    'hover_border' => 'hover:border-teal-500',    'glow' => 'bg-teal-200/40',    'btn' => 'bg-teal-600 group-hover:bg-teal-700',    'footer_border' => 'border-teal-100/80',    'heading_hover' => 'group-hover:text-teal-700',    'active_bg' => 'bg-teal-500',    'shadow_color' => 'rgba(13,148,136,0.25)'],
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-6">

        @forelse ($plantsCatalog as $plant)
            @php
                $t = $themeMap[$plant['theme']] ?? $themeMap['emerald'];
            @endphp

            <div class="plant-card group relative bg-surface {{ $t['hover_bg'] }} border-2 {{ $t['border'] }} {{ $t['hover_border'] }} rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden cursor-pointer select-none"
                 onclick="openPlantModal('{{ $plant['key'] }}')">

                {{-- Top Card Header --}}
                <div class="relative z-10 space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        @if(!empty($plant['cycle']) && $plant['cycle'] !== '—')
                            <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-[9px] sm:text-[11px] font-bold border whitespace-nowrap {{ $plant['badge_color'] }}">
                                {{ $plant['cycle'] }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-[9px] sm:text-[11px] font-medium border border-gray-200 bg-gray-50 text-gray-600 whitespace-nowrap">
                                {{ $plant['active_crop'] ? 'Tanaman Aktif' : 'Komoditas' }}
                            </span>
                        @endif

                        @if($plant['active_crop'])
                            <span class="inline-flex items-center gap-1 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md sm:rounded-xl text-[9px] sm:text-[11px] font-bold {{ $t['active_bg'] }} text-white shadow-sm flex-shrink-0 whitespace-nowrap" title="Ada tanaman aktif di kebun">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                {{ $plant['active_crop']->current_hst }} HST
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-medium bg-gray-100 text-gray-500 flex-shrink-0 whitespace-nowrap" title="Panduan katalog budidaya">
                                Katalog
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm sm:text-xl font-bold text-gray-900 leading-tight {{ $t['heading_hover'] }} transition-colors">
                        {{ $plant['name'] }}
                    </h3>
                </div>

                {{-- Center: Emoji Tanaman --}}
                <div class="my-3 sm:my-6 flex items-center justify-center relative py-2 sm:py-4">
                    <div class="absolute w-24 h-24 sm:w-40 sm:h-40 rounded-full {{ $t['glow'] }} blur-xl sm:blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <span class="relative text-[3.8rem] sm:text-[7rem] leading-none select-none drop-shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:-rotate-3 plant-sway"
                          style="filter: drop-shadow(0 8px 24px {{ $t['shadow_color'] }});">{{ $plant['emoji'] }}</span>
                </div>

                {{-- Card Footer --}}
                <div class="pt-3 sm:pt-4 border-t {{ $t['footer_border'] }} relative z-10">
                    <div class="w-full py-2 sm:py-2.5 px-3 sm:px-4 rounded-xl {{ $t['btn'] }} text-white text-[11px] sm:text-xs font-bold flex items-center justify-between transition-colors shadow-sm">
                        <span class="truncate">Menu & Panduan</span>
                        <span class="transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-span-2 md:col-span-3 bg-surface border-2 border-dashed border-gray-200 rounded-3xl p-8 sm:p-14 text-center shadow-xs">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl sm:text-4xl mb-4 shadow-inner">
                    🌱
                </div>
                <h3 class="text-base sm:text-xl font-bold text-gray-900">Belum Ada Tanaman Aktif</h3>
                <p class="text-xs sm:text-sm text-text-muted max-w-md mx-auto mt-1.5 mb-6 leading-relaxed">
                    Saat ini tidak ada tanaman yang sedang ditanam di kebun. Tambahkan tanaman baru melalui Kalender HST untuk memantau perawatan harian dan siklus panen.
                </p>
                <a href="{{ route('kalender-hst.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold transition-all shadow-sm hover:shadow active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Mulai Tanam di Kalender HST</span>
                </a>
            </div>
        @endforelse

    </div>

</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL 1: Dialog Pilihan Menu saat Kartu Tanaman Diklik
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="plantMenuModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="selectedPlantTitle">

    <div id="plantMenuCard"
         class="bg-surface w-full max-w-lg rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 p-5 sm:p-7 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 relative overflow-hidden">

        {{-- Mobile drag pill indicator --}}
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        {{-- Background glow --}}
        <div id="modalBackdropGlow" class="absolute -top-12 -right-12 w-36 h-36 rounded-full bg-emerald-100/50 blur-2xl pointer-events-none"></div>

        {{-- Header --}}
        <div class="flex items-start justify-between pb-4 border-b border-gray-100 relative z-10">
            <div class="flex items-center gap-3">
                <div id="selectedPlantIcon" class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-2xl shadow-inner">
                </div>
                <div>
                    <span id="selectedPlantBadge" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 mb-0.5">
                        Komoditas
                    </span>
                    <h3 id="selectedPlantTitle" class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">Nama Tanaman</h3>
                    <p id="selectedPlantSubtitle" class="text-xs text-text-muted">Pilih menu perawatan atau jadwal</p>
                </div>
            </div>
            <button type="button"
                    onclick="closePlantMenuModal()"
                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors"
                    aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- 2 Menu Options --}}
        <div class="mt-5 space-y-3 relative z-10">

            {{-- 1. Kalender HST --}}
            <a id="linkKalenderHst"
               href="/kalender-hst"
               class="group flex items-start gap-3.5 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border-2 border-emerald-100 hover:border-primary bg-emerald-50/40 hover:bg-emerald-50/90 transition-all duration-200 shadow-sm hover:shadow-md">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-primary transition-colors">1. Kalender HST</h4>
                        <span id="kalenderHstStatusTag" class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">Jadwal Harian</span>
                    </div>
                    <p id="kalenderHstDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Pantau umur tanaman hari demi hari, kelola jadwal penyemprotan & pemupukan harian, serta tandai panen.
                    </p>
                    <div class="mt-2.5 flex items-center text-xs font-semibold text-primary">
                        <span>Buka Kalender HST</span>
                        <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </div>
            </a>

            {{-- 2. Panduan Pemupukan --}}
            <button type="button"
                    id="btnOpenFertilizer"
                    onclick="openFertilizerGuideForCurrentPlant()"
                    class="w-full text-left group flex items-start gap-3.5 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border-2 border-amber-100 hover:border-amber-400 bg-amber-50/40 hover:bg-amber-50/90 transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-amber-800 transition-colors">2. Panduan Pemupukan</h4>
                        <span id="fertilizerStatusTag" class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-amber-800 bg-amber-100/80 px-2 py-0.5 rounded-md">Dosis & Nutrisi</span>
                    </div>
                    <p id="fertilizerGuideDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Pedoman lengkap nutrisi N-P-K, dosis per fase (dasar, vegetatif, hingga pembungaan/buah), serta cara kocor & semprot.
                    </p>
                    <div class="mt-2.5 flex items-center text-xs font-semibold text-amber-700">
                        <span id="fertilizerBtnText">Lihat Panduan Pemupukan</span>
                        <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </div>
            </button>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL 2: Panduan Pemupukan Lengkap Spesifik per Tanaman
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="fertilizerGuideModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="guideTitle">

    <div id="fertilizerGuideCard"
         class="bg-surface w-full max-w-3xl h-[88vh] sm:h-auto sm:max-h-[90vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">

        {{-- Mobile drag pill indicator --}}
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto my-2.5 sm:hidden flex-shrink-0"></div>

        {{-- Header --}}
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-100 bg-gray-50 flex-shrink-0">
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                    <div id="guidePlantIcon" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl sm:text-2xl shadow-md shadow-amber-500/20 flex-shrink-0">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 id="guideTitle" class="text-sm sm:text-lg font-bold text-gray-900 leading-snug truncate">Panduan Pemupukan Tanaman</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span id="guideSubtitle" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-white/90 border border-gray-200 text-text-secondary">70 – 95 HST</span>
                            <span class="text-[11px] text-text-muted hidden sm:inline">• Pedoman nutrisi & dosis berdasar HST</span>
                        </div>
                    </div>
                </div>

                {{-- Actions on Desktop --}}
                <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                    <a id="btnEditGuide"
                       href="#"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-xl transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Panduan
                    </a>
                    <button type="button"
                            onclick="backToPlantMenu()"
                            class="px-2.5 py-1.5 text-xs font-semibold text-text-secondary hover:text-text bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        ← Kembali
                    </button>
                    <button type="button"
                            onclick="closeFertilizerGuideModal()"
                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors"
                            aria-label="Tutup">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Close button on Mobile --}}
                <button type="button"
                        onclick="backToPlantMenu()"
                        class="sm:hidden w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors flex-shrink-0"
                        aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Mobile Action Bar --}}
            <div class="flex sm:hidden items-center justify-between gap-2 mt-2.5 pt-2 border-t border-gray-200/60">
                <a id="btnEditGuideMobile"
                   href="#"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-200/80 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Panduan
                </a>
            </div>
        </div>

        

        {{-- Phase Navigation Tabs --}}
        <div id="guidePhaseTabs" class="px-4 sm:px-6 py-2 bg-white border-b border-gray-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar flex-shrink-0">
            {{-- Injected dynamically by JS --}}
        </div>

        {{-- Modal Body: Phase Details Panels --}}
        <div id="guidePhaseContent" class="p-6 overflow-y-auto flex-1 space-y-4">
            {{-- Injected dynamically by JS --}}
        </div>

        

    </div>
</div>


{{-- Pass plants catalog into Javascript (keyed by plant key) --}}
<script>
    // Build lookup keyed by plant.key for fast JS access
    const PLANTS_CATALOG = {};
    @foreach ($plantsCatalog as $plant)
    PLANTS_CATALOG[{{ Js::from($plant['key']) }}] = @json($plant);
    @endforeach

    let currentSelectedPlantKey = '{{ ($plantsCatalog->first()['key'] ?? 'timun') }}';

    function openPlantModal(plantKey) {
        const plant = PLANTS_CATALOG[plantKey];
        if (!plant) return;

        currentSelectedPlantKey = plantKey;

        // Set modal data
        const titleEl    = document.getElementById('selectedPlantTitle');
        const subtitleEl = document.getElementById('selectedPlantSubtitle');
        const iconEl     = document.getElementById('selectedPlantIcon');
        const badgeEl    = document.getElementById('selectedPlantBadge');
        const hstDescEl  = document.getElementById('kalenderHstDesc');
        const fertDescEl = document.getElementById('fertilizerGuideDesc');
        const linkKalenderHst      = document.getElementById('linkKalenderHst');
        const kalenderHstStatusTag = document.getElementById('kalenderHstStatusTag');

        if (titleEl)    titleEl.innerText    = plant.name;
        if (subtitleEl) subtitleEl.innerText = 'Pilih menu perawatan atau jadwal';
        if (badgeEl)    badgeEl.innerText    = (plant.cycle && plant.cycle !== '—') ? plant.cycle : (plant.active_crop ? 'Tanaman Aktif' : 'Komoditas');
        if (iconEl)     iconEl.innerText     = plant.emoji;

        // Kalender HST link & status
        const currentHst = (plant.active_crop && plant.active_crop.current_hst !== undefined)
            ? plant.active_crop.current_hst
            : (plant.active_crop ? 0 : null);

        if (plant.active_crop && plant.active_crop.id) {
            linkKalenderHst.href = '/kalender-hst/tanaman/' + plant.active_crop.id;
            kalenderHstStatusTag.innerText = currentHst + ' HST (Aktif)';
            kalenderHstStatusTag.className = 'inline-flex items-center text-[11px] font-bold text-white bg-primary px-2 py-0.5 rounded-md shadow-sm';
            hstDescEl.innerText = 'Tanaman ' + plant.name + ' saat ini berada di ' + currentHst + ' HST. Buka jadwal perawatan spesifik tanaman ini.';
        } else {
            linkKalenderHst.href = '/kalender-hst';
            kalenderHstStatusTag.innerText = 'Buka Kalender';
            kalenderHstStatusTag.className = 'inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md';
            hstDescEl.innerText = 'Pantau umur tanaman, buat jadwal perawatan baru untuk ' + plant.name + ', dan catat panen.';
        }

        // URL Tambah / Edit Panduan
        const addGuideUrl = plant.catalog_id
            ? '/tanaman-katalog/' + plant.catalog_id + '/edit'
            : '/tanaman-katalog/create?name=' + encodeURIComponent(plant.name) + '&emoji=' + encodeURIComponent(plant.emoji || '🌱') + '&theme=' + encodeURIComponent(plant.theme || 'emerald');

        // Update tombol Panduan Pemupukan
        const fertBtn = document.getElementById('btnOpenFertilizer');
        const fertStatusTag = document.getElementById('fertilizerStatusTag');
        const fertBtnText = document.getElementById('fertilizerBtnText');

        if (fertBtn && fertStatusTag) {
            fertBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            if (plant.guides && plant.guides.length > 0) {
                fertBtn.onclick = openFertilizerGuideForCurrentPlant;
                fertStatusTag.innerText = plant.guides.length + ' Fase';
                fertStatusTag.className = 'inline-flex items-center text-[11px] font-semibold text-amber-800 bg-amber-100/80 px-2 py-0.5 rounded-md';
                if (fertDescEl) fertDescEl.innerText = 'Pedoman dosis nutrisi N-P-K, metode kocor/semprot, dan fase tumbuh khusus tanaman ' + plant.name + '.';
                if (fertBtnText) fertBtnText.innerText = 'Lihat Panduan Pemupukan';
            } else {
                fertBtn.onclick = function() {
                    window.location.href = addGuideUrl;
                };
                fertStatusTag.innerText = '+ Tambah Panduan';
                fertStatusTag.className = 'inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100/90 px-2.5 py-0.5 rounded-md hover:bg-emerald-200 transition-colors shadow-xs';
                if (fertDescEl) fertDescEl.innerText = 'Panduan pemupukan untuk ' + plant.name + ' belum tersedia. Klik di sini untuk menyusun fase dan dosis pupuk.';
                if (fertBtnText) fertBtnText.innerText = 'Tambah Panduan Pemupukan';
            }
        }

        // Show Modal
        const modal = document.getElementById('plantMenuModal');
        const card = document.getElementById('plantMenuCard');
        if (modal && card) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            card.classList.remove('translate-y-8', 'sm:scale-95');
            card.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function closePlantMenuModal() {
        const modal = document.getElementById('plantMenuModal');
        const card = document.getElementById('plantMenuCard');
        if (modal && card) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
            card.classList.remove('translate-y-0', 'sm:scale-100');
            card.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    function openFertilizerGuideForCurrentPlant() {
        closePlantMenuModal();
        loadFertilizerGuide(currentSelectedPlantKey);

        const guideModal = document.getElementById('fertilizerGuideModal');
        const guideCard = document.getElementById('fertilizerGuideCard');
        if (guideModal && guideCard) {
            guideModal.classList.remove('opacity-0', 'pointer-events-none');
            guideModal.classList.add('opacity-100');
            guideCard.classList.remove('translate-y-8', 'sm:scale-95');
            guideCard.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function closeFertilizerGuideModal() {
        const guideModal = document.getElementById('fertilizerGuideModal');
        const guideCard = document.getElementById('fertilizerGuideCard');
        if (guideModal && guideCard) {
            guideModal.classList.remove('opacity-100');
            guideModal.classList.add('opacity-0', 'pointer-events-none');
            guideCard.classList.remove('translate-y-0', 'sm:scale-100');
            guideCard.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    function backToPlantMenu() {
        closeFertilizerGuideModal();
        setTimeout(function() {
            openPlantModal(currentSelectedPlantKey);
        }, 150);
    }

    function loadFertilizerGuide(plantKey) {
        const plant = PLANTS_CATALOG[plantKey];
        if (!plant) return;

        currentSelectedPlantKey = plantKey;

        // Update crop tabs inside modal
        document.querySelectorAll('.crop-guide-tab').forEach(tab => {
            tab.className = 'crop-guide-tab px-3 py-1 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-200 transition-all';
        });
        const activeCropTab = document.getElementById('cropTab-' + plantKey);
        if (activeCropTab) {
            activeCropTab.className = 'crop-guide-tab px-3 py-1 rounded-xl text-xs font-bold bg-gray-900 text-white shadow-sm';
        }

        document.getElementById('guideTitle').innerText    = 'Panduan Pemupukan ' + plant.name;
        document.getElementById('guideSubtitle').innerText = plant.cycle;
        document.getElementById('guidePlantIcon').innerText = plant.emoji;

        // Update link tombol Edit/Tambah (Desktop & Mobile)
        const addGuideUrl = plant.catalog_id
            ? '/tanaman-katalog/' + plant.catalog_id + '/edit'
            : '/tanaman-katalog/create?name=' + encodeURIComponent(plant.name) + '&emoji=' + encodeURIComponent(plant.emoji || '🌱') + '&theme=' + encodeURIComponent(plant.theme || 'emerald');

        const hasGuides = plant.guides && plant.guides.length > 0;
        const btnLabel = hasGuides ? 'Edit Panduan' : 'Tambah Panduan';
        const btnIcon = hasGuides
            ? '<svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>'
            : '<svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>';

        const editBtn = document.getElementById('btnEditGuide');
        const editBtnMobile = document.getElementById('btnEditGuideMobile');
        if (editBtn) {
            editBtn.href = addGuideUrl;
            editBtn.innerHTML = btnIcon + ' ' + btnLabel;
            editBtn.classList.remove('hidden');
        }
        if (editBtnMobile) {
            editBtnMobile.href = addGuideUrl;
            editBtnMobile.innerHTML = btnIcon + ' ' + btnLabel;
            editBtnMobile.classList.remove('hidden');
        }

        // Render phase tabs & panels
        const tabsContainer = document.getElementById('guidePhaseTabs');
        const contentContainer = document.getElementById('guidePhaseContent');
        tabsContainer.innerHTML = '';
        contentContainer.innerHTML = '';

        // Empty state: belum ada panduan
        if (!hasGuides) {
            contentContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-14 text-center px-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-800 text-3xl flex items-center justify-center mb-4 shadow-inner">
                        ${plant.emoji}
                    </div>
                    <p class="text-base font-bold text-gray-800">Panduan Belum Tersedia</p>
                    <p class="text-sm text-text-muted mt-1 mb-5">Belum ada panduan pemupukan untuk <strong>${plant.name}</strong>.<br>Susun panduan fase pemupukan (dasar, vegetatif, hingga panen) sekarang.</p>
                    <a href="${addGuideUrl}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Panduan Pemupukan
                    </a>
                </div>
            `;
            return;
        }

        plant.guides.forEach((item, idx) => {
            // Tab button
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = 'guide-tab-btn-' + item.id;
            btn.className = 'guide-phase-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all ' +
                (idx === 0 ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-gray-100 hover:text-text');
            btn.innerText = item.phase;
            btn.onclick = () => switchGuidePhase(item.id);
            tabsContainer.appendChild(btn);

            // Content panel
            const panel = document.createElement('div');
            panel.id = 'guide-panel-' + item.id;
            panel.className = 'guide-phase-panel space-y-3.5 sm:space-y-4 ' + (idx === 0 ? 'block' : 'hidden');

            let nutrientsHtml = item.nutrients.map(n =>
                `<span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-xs font-semibold bg-surface border border-emerald-200 text-gray-800 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ${n}
                </span>`
            ).join('');

            panel.innerHTML = `
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3.5 sm:p-4 rounded-2xl bg-gray-50 border border-gray-200">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ${plant.badge_color}">
                            ${item.phase}
                        </span>
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 mt-1">${item.focus}</h4>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 self-start sm:self-auto flex-shrink-0">
                        <span class="text-xs font-semibold text-emerald-800">Target:</span>
                        <span class="text-xs font-bold text-primary-dark">${item.hst}</span>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-text-muted mb-2">Nutrisi & Pupuk Utama</p>
                    <div class="flex flex-wrap gap-1.5 sm:gap-2">
                        ${nutrientsHtml}
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                        <div class="flex items-center gap-2 text-amber-900 text-xs font-bold uppercase tracking-wider mb-1.5">
                            Dosis &amp; Takaran
                        </div>
                        <p class="text-xs sm:text-sm text-gray-800 leading-relaxed">
                            ${item.dosis}
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80">
                        <div class="flex items-center gap-2 text-blue-900 text-xs font-bold uppercase tracking-wider mb-1.5">
                            Metode Aplikasi
                        </div>
                        <p class="text-xs sm:text-sm text-gray-800 leading-relaxed">
                            ${item.metode}
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                        !
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-900">Tips Agronomi Khusus ${plant.name}</p>
                        <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">
                            ${item.tips}
                        </p>
                    </div>
                </div>
            `;
            contentContainer.appendChild(panel);
        });
    }

    function switchGuidePhase(phaseId) {
        document.querySelectorAll('.guide-phase-tab').forEach(btn => {
            btn.className = 'guide-phase-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all text-text-secondary hover:bg-gray-100 hover:text-text';
        });
        const activeBtn = document.getElementById('guide-tab-btn-' + phaseId);
        if (activeBtn) {
            activeBtn.className = 'guide-phase-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-primary text-white shadow-sm';
        }

        document.querySelectorAll('.guide-phase-panel').forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('block');
        });
        const activePanel = document.getElementById('guide-panel-' + phaseId);
        if (activePanel) {
            activePanel.classList.remove('hidden');
            activePanel.classList.add('block');
        }
    }

    // Close on click outside card
    document.addEventListener('click', function(e) {
        const menuModal = document.getElementById('plantMenuModal');
        if (menuModal && !menuModal.classList.contains('pointer-events-none') && e.target === menuModal) {
            closePlantMenuModal();
        }

        const guideModal = document.getElementById('fertilizerGuideModal');
        if (guideModal && !guideModal.classList.contains('pointer-events-none') && e.target === guideModal) {
            closeFertilizerGuideModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePlantMenuModal();
            closeFertilizerGuideModal();
        }
    });
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
