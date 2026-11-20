@extends('layouts.app')

@section('title', 'Tanaman Aktif')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Bar / Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-gray-100">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Tanaman Aktif</h1>
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
                                {{ $plant['active_crop']->current_hst }} {{ $plant['active_crop']->label_hst_or_hss }}
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
         class="bg-surface w-full max-w-lg max-h-[92vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-6 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 relative overflow-hidden">

        {{-- Mobile drag pill indicator --}}
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden flex-shrink-0"></div>

        {{-- Background glow --}}
        <div id="modalBackdropGlow" class="absolute -top-12 -right-12 w-36 h-36 rounded-full bg-emerald-100/50 blur-2xl pointer-events-none"></div>

        {{-- Header --}}
        <div class="flex items-start justify-between pb-3 sm:pb-4 border-b border-gray-100 relative z-10 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="selectedPlantIcon" class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-2xl shadow-inner flex-shrink-0">
                </div>
                <div>
                    <span id="selectedPlantBadge" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 mb-0.5">
                        Komoditas
                    </span>
                    <h3 id="selectedPlantTitle" class="text-base sm:text-xl font-bold text-gray-900 leading-tight">Nama Tanaman</h3>
                    <p id="selectedPlantSubtitle" class="text-xs text-text-muted">Pilih menu tahapan budidaya atau pemeliharaan</p>
                </div>
            </div>
            <button type="button"
                    onclick="closePlantMenuModal()"
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
            <div id="btnMenuTahapSemai"
                 onclick="openDashboardPlantStage('semai')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openDashboardPlantStage('semai')"
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
                        <span id="semaiStatusTag" class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            SOP Semai
                        </span>
                    </div>
                    <p id="semaiDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Perlakuan benih, media tray semai steril, penyiraman, dan pemeliharaan bibit muda.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-emerald-700">
                        <span class="flex items-center">
                            <span>Buka Panduan Semai</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openDashboardPlantStage('semai', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-emerald-800 bg-emerald-100/90 hover:bg-emerald-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi panduan semai tanaman ini">
                                <svg class="w-3 h-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                            <a id="linkKalenderHssDirect"
                               href="/kalender-hss"
                               class="hidden items-center text-[10px] sm:text-[11px] font-bold text-emerald-800 bg-emerald-100/90 hover:bg-emerald-200 px-2 py-0.5 rounded-md transition-colors">
                                Kalender HSS →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Tahap Penanaman --}}
            <div id="btnMenuTahapPenanaman"
                 onclick="openDashboardPlantStage('penanaman')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openDashboardPlantStage('penanaman')"
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
                        <span id="penanamanStatusTag" class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Olah Tanah &amp; Tanam
                        </span>
                    </div>
                    <p id="penanamanDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        SOP persiapan lahan tanah gembur, pemupukan dasar organik, mulsa, dan penanaman bibit.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-amber-800">
                        <span class="flex items-center">
                            <span>Buka Panduan Penanaman</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openDashboardPlantStage('penanaman', true)"
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

            {{-- 3. Pemeliharaan --}}
            <div id="btnMenuPemeliharaan"
                 onclick="openDashboardPlantStage('pemeliharaan')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openDashboardPlantStage('pemeliharaan')"
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
                        <span id="pemeliharaanStatusTag" class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-teal-800 bg-teal-100/90 px-2 py-0.5 rounded-md flex-shrink-0">
                            Perawatan &amp; Nutrisi
                        </span>
                    </div>
                    <p id="pemeliharaanDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Panduan perawatan harian, pemasangan ajir, pemangkasan tunas air, penyiraman, dan pemupukan khusus.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold gap-2">
                        <span class="text-teal-700 flex items-center">
                            <span>Buka Panduan Pemeliharaan</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openDashboardPlantStage('pemeliharaan', true)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-teal-800 bg-teal-100/90 hover:bg-teal-200 transition-colors shadow-2xs cursor-pointer"
                                    title="Edit isi panduan pemeliharaan tanaman ini">
                                <svg class="w-3 h-3 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit Isi</span>
                            </button>
                            <a id="linkKalenderHst"
                               href="/kalender-hst"
                               class="text-[10px] sm:text-[11px] font-semibold text-teal-800 bg-teal-100/90 hover:bg-teal-200 px-2 py-0.5 rounded-md transition-colors">
                                <span id="pemeliharaanBtnKalenderText">Kalender HST →</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Tips dan Catatan Penting --}}
            <div id="linkTipsCatatan"
                 onclick="openDashboardPlantStage('tips')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openDashboardPlantStage('tips')"
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
                    <p id="tipsCatatanDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Instruksi waktu penyemprotan ideal, sanitasi tangki semprot, dosis aman, dan tips agronomi penting di kebun.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-indigo-700">
                        <span class="flex items-center">
                            <span>Buka Tips Penting</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openDashboardPlantStage('tips', true)"
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
            <div id="linkProblemHama"
                 onclick="openDashboardPlantStage('hama')"
                 role="button"
                 tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')openDashboardPlantStage('hama')"
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
                    <p id="hamaPenyakitDesc" class="text-xs text-text-secondary mt-1 leading-relaxed">
                        Identifikasi gejala hama, jamur/penyakit, serta solusi pengendalian dan bahan aktif obat.
                    </p>
                    <div class="mt-2 flex items-center justify-between text-xs font-semibold text-rose-700">
                        <span class="flex items-center">
                            <span>Buka Solusi Hama</span>
                            <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button"
                                    onclick="openDashboardPlantStage('hama', true)"
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

        {{-- Direct Catalog Link Footer --}}
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-2">

            <a id="linkEditKatalogModal"
               href="/tanaman-katalog"
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
     MODAL KHUSUS: Pembaca 5 Tahapan Spesifik Tanaman dari Tanaman Aktif
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="dashboardPlantStageModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true">

    <div id="dashboardPlantStageCard"
         class="bg-white w-full max-w-2xl max-h-[92vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-6 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 relative overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 flex-shrink-0">
            <button type="button"
                    onclick="backToPlantMenuFromStage()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Kembali ke Pilihan Menu</span>
            </button>
            <div class="flex items-center gap-2">
                <span id="dashStageIcon" class="text-xl">🌱</span>
                <span id="dashStagePlantTitle" class="font-bold text-sm sm:text-base text-gray-900">Nama Tanaman</span>
            </div>
            <button type="button"
                    onclick="closeDashboardPlantStageModal()"
                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- 5 Quick Switch Tabs --}}
        <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl my-3 overflow-x-auto no-scrollbar flex-shrink-0">
            <button type="button"
                    onclick="switchDashboardStage('semai')"
                    id="tabDashSemai"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                <span>Tahap Semai</span>
            </button>
            <button type="button"
                    onclick="switchDashboardStage('penanaman')"
                    id="tabDashPenanaman"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                </svg>
                <span>Penanaman</span>
            </button>
            <button type="button"
                    onclick="switchDashboardStage('pemeliharaan')"
                    id="tabDashPemeliharaan"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <span>Pemeliharaan</span>
            </button>
            <button type="button"
                    onclick="switchDashboardStage('tips')"
                    id="tabDashTips"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                </svg>
                <span>Tips Penting</span>
            </button>
            <button type="button"
                    onclick="switchDashboardStage('hama')"
                    id="tabDashHama"
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
            <div id="dashStageViewMode" class="flex-1 flex flex-col">
                <div class="mb-3 flex items-center justify-between gap-2 flex-wrap">
                    <span id="dashStagePillBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Tahap Semai &amp; Pembibitan
                    </span>
                    <div class="flex items-center gap-2">
                        <span id="dashStageSpecificTag" class="text-xs font-bold text-gray-400">
                            Khusus Tanaman Ini
                        </span>
                        <button type="button"
                                id="btnDashEditToggle"
                                onclick="toggleDashboardStageEdit(true)"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            <span>Edit Isi</span>
                        </button>
                    </div>
                </div>

                <div id="dashStageTextContent" class="hidden text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line font-sans selection:bg-emerald-100 p-4 rounded-2xl bg-gray-50/70 border border-gray-100 flex-1">
                    <!-- Konten dinamis khusus tanaman tersebut -->
                </div>

                {{-- Tampilan Kartu Terstruktur --}}
                <div id="dashStageCardsContainer" class="hidden flex-1 flex flex-col space-y-3 min-h-0">
                    <div class="flex items-center justify-between gap-3 shrink-0">
                        <div class="relative flex-1">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="text"
                                   id="dashStageSearchInput"
                                   oninput="filterDashStageCards(this.value)"
                                   placeholder="Cari langkah / catatan..."
                                   class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-gray-200 text-xs text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
                        </div>
                        <span id="dashStageCardCount" class="text-xs font-bold text-gray-500 whitespace-nowrap bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200">
                            0 Catatan
                        </span>
                    </div>
                    <div id="dashStageCardsGrid" class="overflow-y-auto pr-1">
                        <!-- Kartu hasil parsing -->
                    </div>
                </div>

                <div id="dashStageEmptyState" class="hidden text-center py-8 text-gray-400 space-y-3 bg-gray-50/60 rounded-2xl border border-dashed border-gray-200 p-6">
                    <p class="text-xs sm:text-sm">Panduan untuk tahapan ini belum diisi.</p>
                    <button type="button"
                            onclick="toggleDashboardStageEdit(true)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Tulis Panduan Tahap Ini Sekarang</span>
                    </button>
                </div>
            </div>

            {{-- EDIT MODE --}}
            <div id="dashStageEditMode" class="hidden flex-1 flex flex-col space-y-3">
                <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-gray-700">Edit Panduan:</span>
                        <span id="dashEditStageLabel" class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">Tahap Semai</span>
                    </div>
                    <span class="text-[11px] text-gray-400">Tersimpan ke database katalog</span>
                </div>

                <div class="flex-1 flex flex-col">
                    <textarea id="dashStageTextarea"
                              rows="9"
                              class="w-full flex-1 text-xs sm:text-sm text-gray-800 p-3.5 rounded-2xl bg-white border-2 border-emerald-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-200 outline-none font-sans leading-relaxed resize-y shadow-inner"
                              placeholder="Tuliskan panduan langkah demi langkah, SOP, takaran, jarak tanam, atau instruksi khusus tanaman ini..."></textarea>
                </div>

                <div id="dashStageEditAlert" class="hidden p-2.5 rounded-xl text-xs font-semibold"></div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button"
                            onclick="toggleDashboardStageEdit(false)"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            id="btnSaveDashStage"
                            onclick="saveDashboardStageContent()"
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
        <div id="dashStageFooter" class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <button type="button"
                    onclick="backToPlantMenuFromStage()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                ← Pilihan Menu
            </button>
            <div class="flex items-center gap-2">
                <a id="linkDashStageCatalogEdit"
                   href="/tanaman-katalog"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:text-emerald-700 bg-gray-50 hover:bg-emerald-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                    <span>Form Lengkap</span>
                </a>
                <a id="linkDashStageCatalog"
                   href="/tanaman-katalog"
                   class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-xs">
                    Buka di Katalog Tanaman →
                </a>
            </div>
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


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL 3: Panduan Semai (SOP Persemaian Bibit) Lengkap
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="semaiGuideModal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="semaiGuideTitle">

    <div id="semaiGuideCard"
         class="bg-surface w-full max-w-3xl h-[88vh] sm:h-auto sm:max-h-[90vh] flex flex-col rounded-t-[28px] sm:rounded-3xl shadow-2xl border border-gray-100 transform translate-y-8 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">

        {{-- Mobile drag pill indicator --}}
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto my-2.5 sm:hidden flex-shrink-0"></div>

        {{-- Header --}}
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-100 bg-emerald-50/60 flex-shrink-0">
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                    <div id="semaiGuidePlantIcon" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl sm:text-2xl shadow-md shadow-emerald-500/20 flex-shrink-0">
                        🌱
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 id="semaiGuideTitle" class="text-sm sm:text-lg font-bold text-gray-900 leading-snug truncate">Panduan Semai Tanaman</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span id="semaiGuideSubtitle" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-white border border-emerald-200 text-emerald-800">SOP Persemaian</span>
                            <span class="text-[11px] text-text-muted hidden sm:inline">• Panduan perlakuan benih & perawatan semai</span>
                        </div>
                    </div>
                </div>

                {{-- Actions on Desktop --}}
                <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                    <a id="btnOpenFullSemaiPage"
                       href="/steps/penanaman-bibit"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-200/80 rounded-xl transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        Buka SOP Lengkap & Foto
                    </a>
                    <button type="button"
                            onclick="backToPlantMenuFromSemai()"
                            class="px-2.5 py-1.5 text-xs font-semibold text-text-secondary hover:text-text bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        ← Kembali
                    </button>
                    <button type="button"
                            onclick="closeSemaiGuideModal()"
                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors"
                            aria-label="Tutup">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Close button on Mobile --}}
                <button type="button"
                        onclick="backToPlantMenuFromSemai()"
                        class="sm:hidden w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors flex-shrink-0"
                        aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Mobile Action Bar --}}
            <div class="flex sm:hidden items-center justify-between gap-2 mt-2.5 pt-2 border-t border-emerald-200/60">
                <a id="btnOpenFullSemaiPageMobile"
                   href="/steps/penanaman-bibit"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-200/80 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    Buka SOP Lengkap & Foto
                </a>
            </div>
        </div>

        {{-- Steps Navigation Tabs --}}
        <div id="semaiGuidePhaseTabs" class="px-4 sm:px-6 py-2 bg-white border-b border-gray-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar flex-shrink-0">
            {{-- Injected dynamically by JS --}}
        </div>

        {{-- Modal Body: Step Details Panels --}}
        <div id="semaiGuidePhaseContent" class="p-6 overflow-y-auto flex-1 space-y-4">
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

    function handlePenanamanCardClick() {
        const link = document.getElementById('linkPenanamanBibit');
        if (link && link.href) {
            window.location.href = link.href;
        } else {
            window.location.href = '/steps/penanaman-bibit';
        }
    }

    function handlePemeliharaanCardClick() {
        const link = document.getElementById('linkKalenderHst');
        if (link && link.href) {
            window.location.href = link.href;
        } else {
            window.location.href = '/kalender-hst';
        }
    }

    function openPlantModal(plantKey) {
        const plant = PLANTS_CATALOG[plantKey];
        if (!plant) return;

        currentSelectedPlantKey = plantKey;

        // Set modal data
        const titleEl    = document.getElementById('selectedPlantTitle');
        const subtitleEl = document.getElementById('selectedPlantSubtitle');
        const iconEl     = document.getElementById('selectedPlantIcon');
        const badgeEl    = document.getElementById('selectedPlantBadge');

        // Check if plant is in seedling / semai stage
        const isHss = Boolean(plant.is_semai || (plant.active_crop && (plant.active_crop.tipe === 'hss' || plant.active_crop.is_hss)));
        const labelCode = isHss ? 'HSS' : 'HST';
        const currentHst = (plant.active_crop && plant.active_crop.current_hst !== undefined)
            ? plant.active_crop.current_hst
            : (plant.active_crop ? 0 : null);
        const hasActiveCrop = Boolean(plant.active_crop && plant.active_crop.id);

        if (titleEl)    titleEl.innerText    = plant.name;
        if (subtitleEl) subtitleEl.innerText = isHss ? 'Pilih menu tahapan budidaya atau pemeliharaan semai' : 'Pilih menu tahapan budidaya atau pemeliharaan';
        if (badgeEl)    badgeEl.innerText    = (plant.cycle && plant.cycle !== '—') ? plant.cycle : (plant.active_crop ? (isHss ? 'Tanaman Semai' : 'Tanaman Aktif') : 'Komoditas');
        if (iconEl)     iconEl.innerText     = plant.emoji;

        // 1. Tahap Semai Bibit
        const semaiStatusTag = document.getElementById('semaiStatusTag');
        const semaiDesc = document.getElementById('semaiDesc');
        const linkKalenderHssDirect = document.getElementById('linkKalenderHssDirect');
        const hasSemaiSteps = plant.planting_seed && plant.planting_seed.steps && plant.planting_seed.steps.length > 0;

        if (semaiStatusTag && semaiDesc) {
            if (isHss && hasActiveCrop) {
                semaiStatusTag.innerText = currentHst + ' HSS (Aktif)';
                semaiStatusTag.className = 'inline-flex items-center text-[10px] sm:text-[11px] font-bold text-white bg-emerald-600 px-2 py-0.5 rounded-md shadow-xs flex-shrink-0';
                semaiDesc.innerText = 'Bibit semai ' + plant.name + ' saat ini berada di ' + currentHst + ' HSS. Buka jadwal perawatan semai dan SOP pembibitan.';
                if (linkKalenderHssDirect) {
                    linkKalenderHssDirect.href = '/kalender-hss/tanaman/' + plant.active_crop.id;
                    linkKalenderHssDirect.innerText = 'Kalender HSS (' + currentHst + ' HSS) →';
                    linkKalenderHssDirect.classList.remove('hidden');
                    linkKalenderHssDirect.classList.add('inline-flex');
                }
            } else {
                if (hasSemaiSteps) {
                    semaiStatusTag.innerText = plant.planting_seed.steps.length + ' Langkah SOP';
                    semaiStatusTag.className = 'inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-md flex-shrink-0';
                    semaiDesc.innerText = 'Pedoman lengkap persemaian bibit ' + plant.name + ', perlakuan benih, media tray semai steril, penyiraman, hingga siap pindah tanam.';
                } else {
                    semaiStatusTag.innerText = 'SOP Semai';
                    semaiStatusTag.className = 'inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-md flex-shrink-0';
                    semaiDesc.innerText = 'Panduan persemaian bibit untuk ' + plant.name + ', perlakuan benih, dan perawatan sebelum pindah tanam.';
                }
                if (linkKalenderHssDirect) {
                    linkKalenderHssDirect.classList.add('hidden');
                    linkKalenderHssDirect.classList.remove('inline-flex');
                }
            }
        }

        // 2. Tahap Penanaman
        const linkPenanamanBibit = document.getElementById('linkPenanamanBibit');
        const penanamanDesc = document.getElementById('penanamanDesc');
        const seedParam = plant.planting_seed ? '#seed-' + plant.planting_seed.id : '';
        if (linkPenanamanBibit) {
            linkPenanamanBibit.href = '/steps/penanaman-bibit' + seedParam;
        }
        if (penanamanDesc) {
            penanamanDesc.innerText = 'SOP persiapan lahan tanah gembur, pemupukan dasar organik, mulsa, dan penanaman ' + plant.name + '.';
        }

        // 3. Pemeliharaan (Kalender HST/HSS & Panduan Pemupukan)
        const linkKalenderHst = document.getElementById('linkKalenderHst');
        const pemeliharaanStatusTag = document.getElementById('pemeliharaanStatusTag');
        const pemeliharaanDesc = document.getElementById('pemeliharaanDesc');
        const pemeliharaanBtnKalenderText = document.getElementById('pemeliharaanBtnKalenderText');
        const pemeliharaanBtnPupukText = document.getElementById('pemeliharaanBtnPupukText');
        const btnOpenFertilizerDirect = document.getElementById('btnOpenFertilizerDirect');

        if (hasActiveCrop) {
            if (linkKalenderHst) linkKalenderHst.href = (isHss ? '/kalender-hss/tanaman/' : '/kalender-hst/tanaman/') + plant.active_crop.id;
            if (pemeliharaanStatusTag) {
                pemeliharaanStatusTag.innerText = currentHst + ' ' + labelCode + ' (Aktif)';
                pemeliharaanStatusTag.className = 'inline-flex items-center text-[10px] sm:text-[11px] font-bold text-white bg-teal-600 px-2 py-0.5 rounded-md shadow-xs flex-shrink-0';
            }
            if (pemeliharaanBtnKalenderText) {
                pemeliharaanBtnKalenderText.innerText = 'Buka Kalender ' + labelCode + ' (' + currentHst + ' ' + labelCode + ')';
            }
            if (pemeliharaanDesc) {
                pemeliharaanDesc.innerText = isHss
                    ? 'Bibit semai ' + plant.name + ' saat ini berada di ' + currentHst + ' HSS. Buka jadwal perawatan spesifik tanaman ini.'
                    : 'Tanaman ' + plant.name + ' saat ini berada di ' + currentHst + ' HST. Buka jadwal perawatan dan pemupukan spesifik tanaman ini.';
            }
        } else {
            if (linkKalenderHst) linkKalenderHst.href = isHss ? '/kalender-hss' : '/kalender-hst';
            if (pemeliharaanStatusTag) {
                pemeliharaanStatusTag.innerText = isHss ? 'Buka Kalender HSS' : 'Kalender & Nutrisi';
                pemeliharaanStatusTag.className = 'inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-teal-800 bg-teal-100/90 px-2 py-0.5 rounded-md flex-shrink-0';
            }
            if (pemeliharaanBtnKalenderText) {
                pemeliharaanBtnKalenderText.innerText = 'Buka Kalender ' + labelCode;
            }
            if (pemeliharaanDesc) {
                pemeliharaanDesc.innerText = isHss
                    ? 'Pantau umur bibit semai hari demi hari, kelola jadwal penyiraman & perlindungan benih.'
                    : 'Pantau umur tanaman, buat jadwal perawatan baru untuk ' + plant.name + ', dan catat panen.';
            }
        }

        // Action Panduan Pemupukan di menu Pemeliharaan
        const addGuideUrl = plant.catalog_id
            ? '/tanaman-katalog/' + plant.catalog_id + '/edit'
            : '/tanaman-katalog/create?name=' + encodeURIComponent(plant.name) + '&emoji=' + encodeURIComponent(plant.emoji || '🌱') + '&theme=' + encodeURIComponent(plant.theme || 'emerald');

        if (pemeliharaanBtnPupukText && btnOpenFertilizerDirect) {
            if (plant.guides && plant.guides.length > 0) {
                pemeliharaanBtnPupukText.innerText = 'Panduan Pemupukan (' + plant.guides.length + ' Fase)';
                btnOpenFertilizerDirect.onclick = function(e) {
                    e.stopPropagation();
                    openFertilizerGuideForCurrentPlant();
                };
            } else {
                pemeliharaanBtnPupukText.innerText = '+ Tambah Panduan Pupuk';
                btnOpenFertilizerDirect.onclick = function(e) {
                    e.stopPropagation();
                    window.location.href = addGuideUrl;
                };
            }
        }

        // 4. Tips dan Catatan Penting
        const tipsCatatanDesc = document.getElementById('tipsCatatanDesc');
        if (tipsCatatanDesc) {
            tipsCatatanDesc.innerText = 'Instruksi pencegahan penyakit, kehati-hatian, pantangan pemupukan, dan tips sukses panen khusus ' + plant.name + '.';
        }

        // 5. Problem & Solusi Hama
        const hamaPenyakitDesc = document.getElementById('hamaPenyakitDesc');
        if (hamaPenyakitDesc) {
            hamaPenyakitDesc.innerText = 'Identifikasi gejala hama, jamur/penyakit, serta solusi pengendalian dan bahan aktif obat untuk ' + plant.name + '.';
        }

        // 6. Direct Link ke Katalog Tanaman di Footer Modal
        const btnBukaKatalog = document.getElementById('btnBukaKatalogTanaman');
        const btnBukaKatalogText = document.getElementById('btnBukaKatalogTanamanText');
        const linkEditKatalogModal = document.getElementById('linkEditKatalogModal');
        const catalogDetailUrl = plant.catalog_url || (plant.catalog_id ? ('/tanaman-katalog/' + plant.catalog_id) : '/tanaman-katalog');
        const catalogEditUrl = plant.catalog_id ? ('/tanaman-katalog/' + plant.catalog_id + '/edit') : ('/tanaman-katalog/create?name=' + encodeURIComponent(plant.name));

        if (btnBukaKatalog) btnBukaKatalog.href = catalogDetailUrl;
        if (btnBukaKatalogText) btnBukaKatalogText.innerText = 'Buka Panduan ' + plant.name + ' di Katalog Tanaman';
        if (linkEditKatalogModal) linkEditKatalogModal.href = catalogEditUrl;

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

    // ── Fungsi Panduan Semai ──
    function openSemaiGuideForCurrentPlant() {
        closePlantMenuModal();
        loadSemaiGuide(currentSelectedPlantKey);

        const guideModal = document.getElementById('semaiGuideModal');
        const guideCard = document.getElementById('semaiGuideCard');
        if (guideModal && guideCard) {
            guideModal.classList.remove('opacity-0', 'pointer-events-none');
            guideModal.classList.add('opacity-100');
            guideCard.classList.remove('translate-y-8', 'sm:scale-95');
            guideCard.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function closeSemaiGuideModal() {
        const guideModal = document.getElementById('semaiGuideModal');
        const guideCard = document.getElementById('semaiGuideCard');
        if (guideModal && guideCard) {
            guideModal.classList.remove('opacity-100');
            guideModal.classList.add('opacity-0', 'pointer-events-none');
            guideCard.classList.remove('translate-y-0', 'sm:scale-100');
            guideCard.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    function backToPlantMenuFromSemai() {
        closeSemaiGuideModal();
        setTimeout(function() {
            openPlantModal(currentSelectedPlantKey);
        }, 150);
    }

    function loadSemaiGuide(plantKey) {
        const plant = PLANTS_CATALOG[plantKey];
        if (!plant) return;

        currentSelectedPlantKey = plantKey;

        const titleEl = document.getElementById('semaiGuideTitle');
        const subtitleEl = document.getElementById('semaiGuideSubtitle');
        const plantIconEl = document.getElementById('semaiGuidePlantIcon');
        const btnFullPage = document.getElementById('btnOpenFullSemaiPage');
        const btnFullPageMobile = document.getElementById('btnOpenFullSemaiPageMobile');

        const seed = plant.planting_seed;
        const seedUrl = seed ? ('/steps/penanaman-bibit#seed-' + seed.id) : '/steps/penanaman-bibit';

        if (titleEl) titleEl.innerText = 'Panduan Semai ' + plant.name;
        if (subtitleEl) subtitleEl.innerText = seed ? (seed.nama_bibit + (seed.varietas ? ' (' + seed.varietas + ')' : '')) : 'SOP Persemaian';
        if (plantIconEl) plantIconEl.innerText = plant.emoji || '🌱';
        if (btnFullPage) btnFullPage.href = seedUrl;
        if (btnFullPageMobile) btnFullPageMobile.href = seedUrl;

        const tabsContainer = document.getElementById('semaiGuidePhaseTabs');
        const contentContainer = document.getElementById('semaiGuidePhaseContent');
        if (!tabsContainer || !contentContainer) return;

        tabsContainer.innerHTML = '';
        contentContainer.innerHTML = '';

        const steps = (seed && seed.steps) ? seed.steps : [];

        if (steps.length === 0) {
            contentContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-14 text-center px-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-800 text-3xl flex items-center justify-center mb-4 shadow-inner">
                        ${plant.emoji || '🌱'}
                    </div>
                    <p class="text-base font-bold text-gray-800">Panduan Semai Belum Tersedia</p>
                    <p class="text-sm text-text-muted mt-1 mb-5">Belum ada langkah SOP persemaian khusus untuk <strong>${plant.name}</strong>.<br>Kelola SOP dan tahapan bibit baru sekarang di menu Tahapan Penanaman Bibit.</p>
                    <a href="${seedUrl}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Buka Tahapan Penanaman Bibit
                    </a>
                </div>
            `;
            return;
        }

        steps.forEach((st, idx) => {
            // Tab button
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = 'semai-step-tab-btn-' + st.id;
            btn.className = 'semai-step-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all ' +
                (idx === 0 ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-gray-100 hover:text-text');
            btn.innerText = 'Langkah ' + (st.nomor || (idx + 1));
            btn.onclick = () => switchSemaiStep(st.id);
            tabsContainer.appendChild(btn);

            // Content panel
            const panel = document.createElement('div');
            panel.id = 'semai-step-panel-' + st.id;
            panel.className = 'semai-step-panel space-y-3.5 sm:space-y-4 ' + (idx === 0 ? 'block' : 'hidden');

            panel.innerHTML = `
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3.5 sm:p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border bg-white border-emerald-300 text-emerald-800">
                            Langkah ${st.nomor || (idx + 1)}
                        </span>
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 mt-1">${st.judul}</h4>
                    </div>
                    ${st.waktu ? `
                        <div class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-emerald-200 self-start sm:self-auto flex-shrink-0 text-xs font-semibold text-emerald-800 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>${st.waktu}</span>
                        </div>
                    ` : ''}
                </div>

                <div class="p-4 rounded-2xl bg-white border border-gray-200">
                    <p class="text-xs font-bold uppercase tracking-wider text-text-muted mb-2">Instruksi & SOP Semai</p>
                    <p class="text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line">
                        ${st.deskripsi || 'Belum ada penjelasan detail untuk langkah ini.'}
                    </p>
                </div>

                ${st.tips ? `
                    <div class="p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200 flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            💡
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-900">Tips Persemaian</p>
                            <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">
                                ${st.tips}
                            </p>
                        </div>
                    </div>
                ` : ''}

                <div class="pt-2 flex justify-end">
                    <a href="${seedUrl}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                        <span>Lihat SOP Lengkap & Foto di Tahapan Bibit</span>
                        <span>→</span>
                    </a>
                </div>
            `;
            contentContainer.appendChild(panel);
        });
    }

    function switchSemaiStep(stepId) {
        document.querySelectorAll('.semai-step-tab').forEach(btn => {
            btn.className = 'semai-step-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all text-text-secondary hover:bg-gray-100 hover:text-text';
        });
        const activeBtn = document.getElementById('semai-step-tab-btn-' + stepId);
        if (activeBtn) {
            activeBtn.className = 'semai-step-tab flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-primary text-white shadow-sm';
        }

        document.querySelectorAll('.semai-step-panel').forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('block');
        });
        const activePanel = document.getElementById('semai-step-panel-' + stepId);
        if (activePanel) {
            activePanel.classList.remove('hidden');
            activePanel.classList.add('block');
        }
    }

    // ── Fungsi Pembaca & Editor 4 Tahapan Spesifik Tanaman dari Dashboard ──
    let currentDashboardStageType = 'semai';
    let isDashStageEditing = false;

    function openDashboardPlantStage(stageType, autoEdit = false) {
        closePlantMenuModal();
        currentDashboardStageType = stageType || 'semai';

        const plant = PLANTS_CATALOG[currentSelectedPlantKey];
        if (!plant) return;

        // Set Header
        const iconEl = document.getElementById('dashStageIcon');
        const titleEl = document.getElementById('dashStagePlantTitle');
        const specificTagEl = document.getElementById('dashStageSpecificTag');
        const linkCatalog = document.getElementById('linkDashStageCatalog');
        const linkCatalogEdit = document.getElementById('linkDashStageCatalogEdit');

        if (iconEl) iconEl.innerText = plant.emoji || '🌱';
        if (titleEl) titleEl.innerText = plant.name;
        if (specificTagEl) specificTagEl.innerText = 'Khusus Tanaman ' + plant.name;

        const catalogUrl = plant.catalog_url || (plant.catalog_id ? ('/tanaman-katalog/' + plant.catalog_id) : '/tanaman-katalog');
        const editUrl = plant.catalog_id ? ('/tanaman-katalog/' + plant.catalog_id + '/edit') : ('/tanaman-katalog/create?name=' + encodeURIComponent(plant.name));
        if (linkCatalog) linkCatalog.href = catalogUrl;
        if (linkCatalogEdit) linkCatalogEdit.href = editUrl;

        switchDashboardStage(currentDashboardStageType);

        if (autoEdit) {
            toggleDashboardStageEdit(true);
        } else {
            toggleDashboardStageEdit(false);
        }

        const modal = document.getElementById('dashboardPlantStageModal');
        const card = document.getElementById('dashboardPlantStageCard');
        if (modal && card) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            card.classList.remove('translate-y-8', 'sm:scale-95');
            card.classList.add('translate-y-0', 'sm:scale-100');
        }
    }

    function toggleDashboardStageEdit(isEditing) {
        isDashStageEditing = Boolean(isEditing);
        const viewMode = document.getElementById('dashStageViewMode');
        const editMode = document.getElementById('dashStageEditMode');
        const alertEl = document.getElementById('dashStageEditAlert');
        const textarea = document.getElementById('dashStageTextarea');
        const labelEl = document.getElementById('dashEditStageLabel');

        if (alertEl) {
            alertEl.classList.add('hidden');
            alertEl.innerText = '';
        }

        const plant = PLANTS_CATALOG[currentSelectedPlantKey];
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
            labelEl.innerText = (stageLabels[currentDashboardStageType] || currentDashboardStageType) + ' (' + plant.name + ')';
        }

        if (isDashStageEditing) {
            if (viewMode) viewMode.classList.add('hidden');
            if (editMode) editMode.classList.remove('hidden');
            if (textarea) {
                const currentVal = plant[stagePropMap[currentDashboardStageType]] || '';
                textarea.value = currentVal;
                setTimeout(() => textarea.focus(), 80);
            }
        } else {
            if (editMode) editMode.classList.add('hidden');
            if (viewMode) viewMode.classList.remove('hidden');
        }
    }

    async function saveDashboardStageContent() {
        const plant = PLANTS_CATALOG[currentSelectedPlantKey];
        if (!plant) return;

        const textarea = document.getElementById('dashStageTextarea');
        const btnSave = document.getElementById('btnSaveDashStage');
        const alertEl = document.getElementById('dashStageEditAlert');

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
                    catalog_id: plant.catalog_id || null,
                    plant_name: plant.name,
                    stage_type: currentDashboardStageType,
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
                plant[stagePropMap[currentDashboardStageType]] = newContent;
                if (data.catalog_id) {
                    plant.catalog_id = data.catalog_id;
                    plant.catalog_url = data.catalog_url;
                }

                // Update text in view mode
                switchDashboardStage(currentDashboardStageType);
                toggleDashboardStageEdit(false);

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

    function switchDashboardStage(type) {
        currentDashboardStageType = type;
        const plant = PLANTS_CATALOG[currentSelectedPlantKey];
        if (!plant) return;

        // If in edit mode, update textarea with current stage content
        if (isDashStageEditing) {
            toggleDashboardStageEdit(true);
        }

        // Reset and activate tab
        const stages = ['semai', 'penanaman', 'pemeliharaan', 'tips', 'hama'];
        stages.forEach(s => {
            const tabBtn = document.getElementById('tabDash' + s.charAt(0).toUpperCase() + s.slice(1));
            if (tabBtn) {
                if (s === type) {
                    if (s === 'hama') {
                        tabBtn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap bg-rose-600 text-white shadow-xs';
                    } else {
                        tabBtn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap bg-emerald-600 text-white shadow-xs';
                    }
                } else {
                    tabBtn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap text-gray-600 hover:text-gray-900 hover:bg-gray-200/80 transition-all';
                }
            }
        });

        const badgeEl = document.getElementById('dashStagePillBadge');
        const textEl = document.getElementById('dashStageTextContent');
        const emptyEl = document.getElementById('dashStageEmptyState');

        let content = '';
        let badgeText = '';
        let badgeClass = '';

        if (type === 'semai') {
            content = plant.tahap_semai || '';
            badgeText = 'Tahap Semai & Pembibitan ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else if (type === 'penanaman') {
            content = plant.tahap_penanaman || '';
            badgeText = 'Tahap Penanaman & Jarak Tanam ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200';
        } else if (type === 'pemeliharaan') {
            content = plant.tahap_pemeliharaan || '';
            badgeText = 'Tahap Pemeliharaan & Nutrisi ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200';
        } else if (type === 'tips') {
            content = plant.tips_penting || '';
            badgeText = 'Tips Penting & Aturan Lapangan ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200';
        } else if (type === 'hama') {
            content = plant.hama_penyakit || '';
            badgeText = 'Problem & Solusi Hama / Penyakit ' + plant.name;
            badgeClass = 'px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200';
        }

        if (badgeEl) {
            badgeEl.innerText = badgeText;
            badgeEl.className = badgeClass;
        }

        const cardsContainer = document.getElementById('dashStageCardsContainer');
        const cardsGrid = document.getElementById('dashStageCardsGrid');
        const searchInput = document.getElementById('dashStageSearchInput');
        const countEl = document.getElementById('dashStageCardCount');

        if (content && content.trim() !== '') {
            if (emptyEl) emptyEl.classList.add('hidden');
            if (textEl) textEl.classList.add('hidden');
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
                    const badgeBadge = isHama ? 'Solusi Terverifikasi' : 'SOP Terverifikasi';
                    const badgeColor = isHama ? 'text-rose-700' : 'text-emerald-700';

                    const iconSvg = isHama
                        ? `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.002A11.959 11.959 0 0112 2.714zm0 13.036h.008v.008H12v-.008z"/></svg>`
                        : `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/></svg>`;

                    html += `
                        <div class="dash-stage-card bg-surface rounded-2xl border border-gray-200 p-3.5 sm:p-5 shadow-xs ${hoverBorder} hover:shadow-md transition-all flex flex-col justify-between group"
                             data-title="${escapeHtml(card.title)}"
                             data-content="${escapeHtml(card.content)}">
                            <div>
                                <div class="flex items-start justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-gray-100">
                                    <div class="flex items-start gap-2.5 sm:gap-3 min-w-0">
                                        <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl ${iconBg} flex items-center justify-center shrink-0 border mt-0.5">
                                            ${iconSvg}
                                        </span>
                                        <div class="min-w-0">
                                            <h3 class="text-xs sm:text-base font-bold text-gray-900 ${hoverTitle} transition-colors leading-snug">
                                                ${escapeHtml(card.title)}
                                            </h3>
                                            <span class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5 block truncate">
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
                                                    <div class="mt-2.5 sm:mt-3 rounded-xl p-2.5 sm:p-3 bg-amber-50/70 border border-amber-200/80 text-amber-950">
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
                                                    <div class="mt-2 rounded-xl p-2.5 sm:p-3 bg-emerald-50/70 border border-emerald-200/80 text-emerald-950">
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
                                    return `<div class="text-xs sm:text-sm text-gray-700 mt-2.5 sm:mt-3 leading-relaxed whitespace-pre-line">${escapeHtml(card.content)}</div>`;
                                })()}
                            </div>
                            <div class="mt-3.5 pt-2.5 sm:mt-4 sm:pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] sm:text-[11px] text-gray-400">
                                <span class="flex items-center gap-1 ${badgeColor} font-medium truncate">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="truncate">${badgeBadge}</span>
                                </span>
                                <span class="hidden sm:inline shrink-0">Tersimpan di Sistem</span>
                                <span class="sm:hidden shrink-0">Sistem</span>
                            </div>
                        </div>
                    `;
                });
                if (cardsGrid) cardsGrid.innerHTML = html;
            } else {
                // Tampilan mirip Menu Tahapan (semai, penanaman, pemeliharaan)
                if (cardsGrid) cardsGrid.className = 'flex flex-col space-y-3 sm:space-y-3.5 overflow-y-auto pr-1';
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
                        <div class="dash-stage-card bg-surface rounded-2xl border border-gray-200 p-3.5 sm:p-5 shadow-xs ${t.hoverBorder} hover:shadow-sm transition-all group"
                             data-title="${escapeHtml(step.judul)}"
                             data-content="${escapeHtml(step.deskripsi)}">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl ${t.numBg} flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border shadow-2xs">
                                    ${escapeHtml(step.nomor)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 sm:gap-2">
                                        <h3 class="text-xs sm:text-base font-bold text-gray-900 ${t.hoverTitle} transition-colors leading-snug">
                                            ${escapeHtml(step.judul)}
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] sm:text-[11px] font-semibold ${t.badgeBg} w-fit shrink-0">
                                            ${escapeHtml(step.badge)}
                                        </span>
                                    </div>
                                    ${step.waktu ? `
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                Waktu: ${escapeHtml(step.waktu)}
                                            </span>
                                        </div>
                                    ` : ''}
                                    <div class="text-xs sm:text-sm text-gray-600 mt-2 sm:mt-2.5 leading-relaxed whitespace-pre-line">
                                        ${escapeHtml(step.deskripsi)}
                                    </div>
                                    <div class="mt-3 pt-2 sm:mt-3.5 sm:pt-2.5 border-t border-gray-100 flex items-center justify-between text-[10px] sm:text-[11px] text-gray-400">
                                        <span class="flex items-center gap-1 text-emerald-700 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>${t.sopBadge}</span>
                                        </span>
                                        <span class="hidden sm:inline">Standar Operasional Lapangan</span>
                                        <span class="sm:hidden">SOP Kebun</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                if (cardsGrid) cardsGrid.innerHTML = html;
            }
        } else {
            if (textEl) textEl.classList.add('hidden');
            if (cardsContainer) cardsContainer.classList.add('hidden');
            if (emptyEl) emptyEl.classList.remove('hidden');
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

            const bulletColon = trimmed.match(/^[•\-*]\s+([^:\n]{3,70}):(.*)$/);
            const numColon = trimmed.match(/^[0-9]+\.\s+([^:\n]{3,70}):(.*)$/);
            const bulletNoColon = trimmed.match(/^[•\-*]\s+(.+)$/);
            const numNoColon = trimmed.match(/^[0-9]+\.\s+(.+)$/);

            if (bulletColon || numColon) {
                if (currentCard) cards.push(finalize(currentCard));
                const m = bulletColon || numColon;
                currentCard = {
                    title: m[1].trim(),
                    category: currentCategory,
                    contentLines: m[2].trim() ? [m[2].trim()] : []
                };
            } else if (bulletNoColon || numNoColon) {
                const isIndented = /^\s{2,}[•\-*]/.test(line);
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

    function filterDashStageCards(query) {
        const q = (query || '').toLowerCase().trim();
        const grid = document.getElementById('dashStageCardsGrid');
        if (!grid) return;
        const cards = grid.querySelectorAll('.dash-stage-card');
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
        const countEl = document.getElementById('dashStageCardCount');
        if (countEl) {
            const unit = (currentDashboardStageType === 'tips' || currentDashboardStageType === 'hama') ? ' Catatan' : ' Langkah';
            countEl.innerText = count + unit;
        }
    }

    function closeDashboardPlantStageModal() {
        const modal = document.getElementById('dashboardPlantStageModal');
        const card = document.getElementById('dashboardPlantStageCard');
        if (modal && card) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
            card.classList.remove('translate-y-0', 'sm:scale-100');
            card.classList.add('translate-y-8', 'sm:scale-95');
        }
    }

    function backToPlantMenuFromStage() {
        closeDashboardPlantStageModal();
        setTimeout(function() {
            openPlantModal(currentSelectedPlantKey);
        }, 150);
    }

    // Close on click outside card
    document.addEventListener('click', function(e) {
        const menuModal = document.getElementById('plantMenuModal');
        if (menuModal && !menuModal.classList.contains('pointer-events-none') && e.target === menuModal) {
            closePlantMenuModal();
        }

        const stageModal = document.getElementById('dashboardPlantStageModal');
        if (stageModal && !stageModal.classList.contains('pointer-events-none') && e.target === stageModal) {
            closeDashboardPlantStageModal();
        }

        const guideModal = document.getElementById('fertilizerGuideModal');
        if (guideModal && !guideModal.classList.contains('pointer-events-none') && e.target === guideModal) {
            closeFertilizerGuideModal();
        }

        const semaiModal = document.getElementById('semaiGuideModal');
        if (semaiModal && !semaiModal.classList.contains('pointer-events-none') && e.target === semaiModal) {
            closeSemaiGuideModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePlantMenuModal();
            closeDashboardPlantStageModal();
            closeFertilizerGuideModal();
            closeSemaiGuideModal();
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
