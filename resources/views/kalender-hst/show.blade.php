@extends('layouts.app')

@section('title', 'Menu HST - ' . $crop->nama_tanaman . ' | AgriTrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-12">

    {{-- ── Breadcrumbs & Action Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-xs sm:text-sm text-text-muted">
            <a href="/kalender-hst?tab={{ $crop->status !== 'Sedang Ditanam' ? 'riwayat' : 'aktif' }}"
               class="inline-flex items-center gap-1 text-text-secondary hover:text-primary transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Kalender HST
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-text font-semibold truncate">{{ $crop->nama_tanaman }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($crop->status === 'Sedang Ditanam')
                <button type="button"
                        onclick="openHarvestModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ $crop->tanggal_tanam->toDateString() }}', {{ $crop->next_harvest_number }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-semibold transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Panen ke-{{ $crop->next_harvest_number }}
                </button>

                <button type="button"
                        onclick="openEndCropModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ $crop->tanggal_tanam->toDateString() }}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 bg-white hover:bg-amber-50 hover:border-amber-300 text-text-secondary hover:text-amber-800 text-xs font-semibold transition-all shadow-xs">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    Akhiri Tanaman
                </button>
            @endif


            <!-- <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-white text-text-secondary hover:text-text hover:bg-gray-50 text-xs font-medium transition-all shadow-xs print:hidden"
                    title="Cetak Jadwal">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m11.318-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656"/>
                </svg>
                <span>Cetak</span>
            </button> -->

            <form method="POST"
                  action="/kalender-hst/tanaman/{{ $crop->id }}"
                  data-confirm-delete="{{ $crop->nama_tanaman }}"
                  data-confirm-title="Hapus Tanaman?"
                  class="inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="redirect_tab" value="{{ in_array($crop->status, ['Sudah Dipanen', 'Diakhiri']) ? 'riwayat' : 'aktif' }}">
                <button type="submit"
                        class="p-2 rounded-xl border border-gray-200 bg-white text-text-muted hover:text-red-500 hover:bg-red-50 transition-all shadow-xs"
                        title="Hapus Tanaman">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Flash Message ── --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium shadow-xs">
            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── HEADER INFORMASI TANAMAN                                             ── --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5">
            {{-- Top Row: Identity, Status, and Edit Action --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50/90 border border-emerald-100/80 text-2xl flex items-center justify-center shadow-xs shrink-0 select-none">
                        <span class="leading-none">{{ $crop->emoji }}</span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight truncate">
                                {{ $crop->nama_tanaman }}
                            </h2>
                            @if($crop->varietas)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    {{ $crop->varietas }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-text-muted mt-0.5">Detail penanaman & log jadwal harian</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 flex-wrap">
                    @if ($crop->harvest_count > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18c-2 2-3 5-3 9 0 3.5 1 6.5 3 9m0-18c2 2 3 5 3 9 0 3.5-1 6.5-3 9M6.5 7.5c2 1 4.5 1.5 5.5 1.5m-5.5 5c2 1 4.5 1.5 5.5 1.5m11-6.5c-2 1-4.5 1.5-5.5 1.5m5.5 5c-2 1-4.5 1.5-5.5 1.5"/>
                            </svg>
                            <span>{{ $crop->harvest_count }}x Panen</span>
                        </span>
                        @if ($crop->total_pendapatan_kotor > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-emerald-600 text-white shadow-xs">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Total Kotor: {{ $crop->formatted_total_pendapatan_kotor }}</span>
                            </span>
                        @endif
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold {{ $crop->status === 'Sedang Ditanam' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($crop->status === 'Diakhiri' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                        @if ($crop->status === 'Sedang Ditanam')
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            HST {{ $currentHst }} • Sedang Ditanam
                        @elseif ($crop->status === 'Diakhiri')
                            Tanaman Diakhiri (HST {{ $crop->total_hst_panen ?? 0 }})
                        @else
                            Sudah Dipanen (HST {{ $crop->total_hst_panen ?? 0 }})
                        @endif
                    </span>
                    <button type="button"
                            onclick="openEditCropModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ addslashes($crop->varietas ?? '') }}', '{{ addslashes($crop->populasi ?? '') }}', '{{ $crop->tanggal_tanam->toDateString() }}', `{{ addslashes($crop->catatan ?? '') }}`)"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-semibold text-text-secondary hover:text-text transition-all shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Ubah</span>
                    </button>
                </div>
            </div>

            {{-- Bottom Row: Key Information Cards / Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3.5">
                <div class="p-3 rounded-xl bg-gray-50/80 border border-gray-100">
                    <span class="text-[11px] font-semibold text-text-muted uppercase tracking-wider block">Benih yang Ditanam</span>
                    <span class="text-xs sm:text-sm font-bold text-gray-900 mt-0.5 block truncate" title="{{ $crop->varietas ?: ($crop->nama_tanaman ?? '-') }}">
                        {{ $crop->varietas ? $crop->varietas : ($crop->nama_tanaman ?? '-') }}
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-gray-50/80 border border-gray-100">
                    <span class="text-[11px] font-semibold text-text-muted uppercase tracking-wider block">Populasi</span>
                    <span class="text-xs sm:text-sm font-bold text-gray-900 mt-0.5 block">
                        {{ $crop->populasi ? $crop->populasi : '-' }}
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-gray-50/80 border border-gray-100">
                    <span class="text-[11px] font-semibold text-text-muted uppercase tracking-wider block">Tanggal Menanam</span>
                    <span class="text-xs sm:text-sm font-bold text-gray-900 mt-0.5 block">
                        {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->locale('id')->isoFormat('dddd') }}, {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->format('d-m-Y') }}
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80 flex flex-col justify-between">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Biaya Obat</span>
                    <div class="flex items-center justify-between gap-1 mt-0.5">
                        <span class="text-xs sm:text-sm font-black text-emerald-950 truncate">
                            {{ $medicineSummary['formatted_total_biaya'] }}
                        </span>
                        <button type="button"
                                onclick="switchDetailTab('obat')"
                                class="text-[10px] text-emerald-800 bg-emerald-200/60 hover:bg-emerald-200 px-2 py-0.5 rounded-md font-extrabold transition-colors shrink-0 cursor-pointer">
                            {{ $medicineSummary['total_jenis_obat'] }} Obat
                        </button>
                    </div>
                </div>
            </div>

            @if ($crop->catatan)
                <div class="mt-3 p-3 rounded-xl bg-amber-50/60 border border-amber-200/60 text-xs text-amber-900 flex items-start gap-2">
                    <span class="font-bold shrink-0">Catatan:</span>
                    <span class="leading-relaxed">{{ $crop->catatan }}</span>
                </div>
            @endif
        </div>

        {{-- ── Action Bar ── --}}
        <div class="p-3 sm:p-4 bg-gray-50/90 border-t border-gray-200 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Log Kegiatan Per HST</span>
                @if ($medicineSummary['total_aplikasi_count'] > 0)
                    <button type="button"
                            onclick="switchDetailTab('obat')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors cursor-pointer">
                        <svg class="w-3 h-3 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="6" width="18" height="15" rx="3"/>
                            <path d="M8 2h8v4H8z"/>
                            <path d="M12 10v7m-3.5-3.5h7"/>
                        </svg>
                        <span>{{ $medicineSummary['total_jenis_obat'] }} Obat ({{ $medicineSummary['formatted_total_tank'] }})</span>
                    </button>
                @endif
            </div>
            @if ($crop->status === 'Sedang Ditanam')
                <button type="button"
                        onclick="openAddActivityModal({{ $currentHst }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-all shadow-xs shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Catat Kegiatan</span>
                </button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 border border-gray-200 text-text-secondary font-medium text-[11px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    <span>Arsip Log HST (Riwayat)</span>
                </span>
            @endif
        </div>
    </div>

    {{-- ── Sub-Tab Navigation Bar (Log Kalender HST vs Rangkuman Obat) ── --}}
    @php
        $detailTab = request('tab', 'kalender');
    @endphp
    <div class="flex items-center gap-2 mb-6 border-b border-gray-200/80 pb-3 overflow-x-auto">
        <button type="button"
                onclick="switchDetailTab('kalender')"
                id="btn-detail-tab-kalender"
                class="detail-tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $detailTab === 'kalender' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <span>Log Kalender HST</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">{{ count($tableRows) }} Hari</span>
        </button>

        <button type="button"
                onclick="switchDetailTab('obat')"
                id="btn-detail-tab-obat"
                class="detail-tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $detailTab === 'obat' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
            <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="6" width="18" height="15" rx="3"/>
                <path d="M8 2h8v4H8z"/>
                <path d="M12 10v7m-3.5-3.5h7"/>
            </svg>
            <span>Rangkuman Pemakaian Obat</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                {{ $medicineSummary['total_jenis_obat'] }} Obat ({{ $medicineSummary['formatted_total_biaya'] }})
            </span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── TAB PANE: RANGKUMAN OBAT                                            ── --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="detail-pane-obat" class="detail-pane {{ $detailTab === 'obat' ? '' : 'hidden' }}">
        <div id="section-rangkuman-obat" class="bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden mb-6 transition-all scroll-mt-6">
            <div class="p-4 sm:p-5">
                {{-- Top Row: Identity & Action --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="6" width="18" height="15" rx="3"/>
                                <path d="M8 2h8v4H8z"/>
                                <path d="M12 10v7m-3.5-3.5h7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">
                                    Rangkuman Pemakaian Obat
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $crop->emoji }} {{ $crop->nama_tanaman }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Akumulasi total dosis, frekuensi aplikasi, tangki semprot, dan estimasi biaya obat untuk tanaman ini.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" 
                                onclick="toggleMedicineSummarySection()" 
                                id="btn-toggle-med-summary"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-emerald-300 bg-gray-50 hover:bg-emerald-50 text-xs font-semibold text-gray-700 hover:text-emerald-800 transition-all cursor-pointer shadow-2xs">
                            <span id="label-toggle-med-summary">Ciutkan</span>
                            <svg id="icon-toggle-med-summary" class="w-3.5 h-3.5 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                        </button>
                    </div>
                </div>

            <div id="body-rangkuman-obat" class="space-y-4 pt-3.5">
                {{-- 3 Key Summary Metric Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- 1. Total Biaya Obat --}}
                    <div class="p-3.5 rounded-2xl bg-gradient-to-br from-emerald-50/80 to-emerald-100/40 border border-emerald-200/80 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800/90 block">Total Biaya Obat</span>
                            <span class="text-lg sm:text-xl font-black text-emerald-950 mt-0.5 block">
                                {{ $medicineSummary['formatted_total_biaya'] }}
                            </span>
                            <span class="text-[10px] text-emerald-700 font-medium">Estimasi pengeluaran obat</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- 2. Total Tangki & Aplikasi --}}
                    <div class="p-3.5 rounded-2xl bg-gradient-to-br from-amber-50/80 to-amber-100/40 border border-amber-200/80 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800/90 block">Tangki & Frekuensi</span>
                            <span class="text-lg sm:text-xl font-black text-amber-950 mt-0.5 block">
                                {{ $medicineSummary['formatted_total_tank'] }}
                            </span>
                            <span class="text-[10px] text-amber-700 font-medium">{{ $medicineSummary['total_aplikasi_count'] }}x Penyemprotan / Aplikasi</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- 3. Jenis Obat Digunakan --}}
                    <div class="p-3.5 rounded-2xl bg-gradient-to-br from-sky-50/80 to-sky-100/40 border border-sky-200/80 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-sky-800/90 block">Jenis Obat Terpakai</span>
                            <span class="text-lg sm:text-xl font-black text-sky-950 mt-0.5 block">
                                {{ $medicineSummary['total_jenis_obat'] }} Obat
                            </span>
                            <span class="text-[10px] text-sky-700 font-medium">Bahan & formulasi terdaftar</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="6" width="18" height="15" rx="3"/>
                                <path d="M8 2h8v4H8z"/>
                                <path d="M12 10v7m-3.5-3.5h7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Table of Medicines Breakdown --}}
                @if (empty($medicineSummary['items']))
                    <div class="p-6 rounded-2xl border border-dashed border-gray-200 text-center bg-gray-50/50">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12m0 0C12 7.58 8.42 4 4 4c0 4.42 3.58 8 8 8zm0 0c0-4.42 3.58-8 8-8 0 4.42-3.58 8-8 8z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-700">Belum ada pemakaian obat yang dicatat untuk {{ $crop->nama_tanaman }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Obat, dosis, dan jumlah tangki yang Anda masukkan saat mencatat kegiatan perawatan akan otomatis terakumulasi di sini.</p>
                    </div>
                @else
                    <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-2xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/90 border-b border-gray-200 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                                        <th class="py-2.5 px-3.5">Nama Obat</th>
                                        <th class="py-2.5 px-3.5">Kategori</th>
                                        <th class="py-2.5 px-3.5 text-center">Frekuensi & Tangki</th>
                                        <th class="py-2.5 px-3.5 text-center">Total Dosis</th>
                                        <th class="py-2.5 px-3.5 text-right">Total Estimasi Biaya</th>
                                        <th class="py-2.5 px-3.5 text-center">Riwayat HST</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($medicineSummary['items'] as $idx => $item)
                                        <tr class="hover:bg-emerald-50/30 transition-colors">
                                            <td class="py-3 px-3.5 font-bold text-gray-900">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                                    <span>{{ $item['nama'] }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3.5">
                                                @if ($item['jenis'])
                                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold {{ str_contains(strtolower($item['jenis']), 'insek') ? 'bg-amber-100 text-amber-800' : (str_contains(strtolower($item['jenis']), 'fungi') ? 'bg-blue-100 text-blue-800' : (str_contains(strtolower($item['jenis']), 'herbi') ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                                        {{ $item['jenis'] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-[11px]">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5 text-center">
                                                <span class="font-bold text-gray-900">{{ $item['frekuensi'] }}x</span>
                                                <span class="text-gray-400 mx-1">•</span>
                                                <span class="text-emerald-700 font-semibold">{{ $item['formatted_total_tank'] }}</span>
                                            </td>
                                            <td class="py-3 px-3.5 text-center font-bold text-gray-900">
                                                <span class="px-2 py-0.5 rounded-lg bg-gray-100 font-mono text-[11px] text-gray-800">{{ $item['total_dosis_text'] }}</span>
                                            </td>
                                            <td class="py-3 px-3.5 text-right font-black text-emerald-800">
                                                {{ $item['formatted_total_biaya'] }}
                                            </td>
                                            <td class="py-3 px-3.5 text-center">
                                                <div class="flex items-center justify-center gap-1 flex-wrap max-w-xs mx-auto">
                                                    @foreach ($item['riwayat'] as $r)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-800" 
                                                              title="{{ $r['nama_kegiatan'] }} ({{ $r['tanggal'] ?? 'HST '.$r['hst'] }}): {{ $r['dosis_per_tank_text'] }} ({{ $r['jumlah_tank'] }} Tank = {{ $r['total_dosis_text'] }}, {{ $r['formatted_biaya'] }})">
                                                            HST {{ $r['hst'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── TAB PANE: LOG KALENDER HST                                          ── --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="detail-pane-kalender" class="detail-pane {{ $detailTab === 'kalender' ? '' : 'hidden' }}">
        {{-- ── TABEL LOG HST PER TANAMAN ── --}}
        <div id="section-kalender-table" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0 font-sans text-xs sm:text-sm">
                    {{-- Table Header --}}
                    <thead>
                        <tr class="bg-gray-100 text-gray-900 font-bold uppercase tracking-wider text-center">
                            <th class="py-3 px-2 w-[76px] min-w-[76px] max-w-[76px] border-b-2 border-r border-gray-300 font-extrabold sticky left-0 z-30 bg-gray-100 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.14)]">HST</th>
                            <th class="py-3 px-4 w-44 sm:w-56 border-b-2 border-r border-gray-300 font-extrabold text-left bg-gray-100">KALENDER</th>
                            <th class="py-3 px-4 w-40 sm:w-48 border-b-2 border-r border-gray-300 font-extrabold text-left bg-gray-100">KEGIATAN</th>
                            <th class="py-3 px-4 min-w-[220px] max-w-[320px] border-b-2 border-r border-gray-300 font-extrabold text-left bg-gray-100">APLIKASI OBAT</th>
                            <th class="py-3 px-4 w-36 sm:w-44 border-b-2 border-r border-gray-300 font-extrabold text-left bg-gray-100">SASARAN</th>
                            <th class="py-3 px-4 min-w-[180px] max-w-[260px] border-b-2 border-r border-gray-300 font-extrabold text-left bg-gray-100">KETERANGAN</th>
                            <th class="py-3 px-3 w-28 sm:w-36 border-b-2 border-gray-300 font-extrabold text-center bg-gray-100">FOTO</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tableRows as $row)
                            @php
                                $hasAct = $row['activities']->isNotEmpty();
                                $hasPending = $row['activities']->where('status', 'Belum')->isNotEmpty();
                                $isToday = $row['is_today'];
                                $isTomorrow = $row['is_tomorrow'];
                                $isPlantingDay = $row['is_planting_day'];
                            @endphp
                            <tr id="hst-row-{{ $row['hst'] }}"
                                data-hst="{{ $row['hst'] }}"
                                data-has-activity="{{ $hasAct ? '1' : '0' }}"
                                data-is-today="{{ $isToday ? '1' : '0' }}"
                                data-is-tomorrow="{{ $isTomorrow ? '1' : '0' }}"
                                data-has-pending="{{ $hasPending ? '1' : '0' }}"
                                class="hst-table-row transition-colors group">

                                {{-- 1. Kolom HST (Sticky di Mobile saat Scroll ke Samping, Solid 100% Opaque) --}}
                                <td class="py-3 px-2 text-center align-top font-mono font-bold whitespace-nowrap sticky left-0 z-20 w-[76px] min-w-[76px] max-w-[76px] border-b border-r shadow-[3px_0_6px_-2px_rgba(0,0,0,0.14)] {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-500 text-emerald-950' : ($isTomorrow ? 'bg-[#fef3c7] border-b-[#fde68a] border-r-amber-400 text-gray-900' : ($isPlantingDay ? 'bg-[#ecfdf5] border-b-[#a7f3d0] border-r-emerald-400 text-gray-900' : 'bg-white group-hover:bg-gray-50 border-b-gray-200 border-r-gray-300 text-gray-900')) }}">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-sm sm:text-base font-black {{ $isToday ? 'text-emerald-900' : 'text-gray-900' }}">
                                            {{ $row['hst'] }}HST
                                        </span>
                                        @if ($isToday)
                                            <span class="mt-0.5 px-2 py-0.5 rounded text-[9px] font-black bg-[#16a34a] text-white tracking-wider shadow-xs">
                                                HARI INI
                                            </span>
                                        @elseif ($isTomorrow)
                                            <span class="mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500 text-white tracking-wider">
                                                BESOK
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 2. Kolom KALENDER --}}
                                <td class="py-3 px-4 align-top whitespace-nowrap border-b border-r border-gray-300 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-300 text-emerald-950 font-bold' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a]' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0]' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200')) }}">
                                <div class="{{ $isToday ? 'font-bold text-emerald-950 text-sm' : 'font-medium text-gray-800' }}">
                                    {{ $row['date']->locale('id')->isoFormat('dddd') }}, {{ $row['date']->format('d-m-Y') }}
                                </div>
                                @if ($isPlantingDay)
                                    <div class="text-[11px] text-emerald-700 font-semibold mt-0.5 inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12m0 0C12 7.58 8.42 4 4 4c0 4.42 3.58 8 8 8zm0 0c0-4.42 3.58-8 8-8 0 4.42-3.58 8-8 8z"/>
                                        </svg>
                                        <span>Hari Pertama Tanam</span>
                                    </div>
                                @endif
                                @if (!empty($row['harvests']) && $row['harvests']->isNotEmpty())
                                    <div class="mt-1 space-y-1">
                                        @foreach ($row['harvests'] as $hrv)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-bold">
                                                <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18c-2 2-3 5-3 9 0 3.5 1 6.5 3 9m0-18c2 2 3 5 3 9 0 3.5-1 6.5-3 9M6.5 7.5c2 1 4.5 1.5 5.5 1.5m-5.5 5c2 1 4.5 1.5 5.5 1.5m11-6.5c-2 1-4.5 1.5-5.5 1.5m5.5 5c-2 1-4.5 1.5-5.5 1.5"/>
                                                </svg>
                                                <span>Panen ke-{{ $hrv->panen_ke }}</span>
                                                @if ($hrv->total_panen)
                                                    <span class="text-amber-800 font-medium">({{ $hrv->total_panen }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- 3. Kolom KEGIATAN --}}
                            <td class="py-3 px-4 align-top border-b border-r border-gray-300 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-300 text-emerald-950' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a]' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0]' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200')) }}">
                                @if ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            @php
                                                $isDone = $act->status === 'Selesai';
                                            @endphp
                                            <div class="flex items-start justify-between gap-2 p-1.5 rounded-lg {{ $isToday ? 'bg-white/90 border border-emerald-300 shadow-xs' : ($isDone ? 'bg-gray-100/80 text-text-muted' : 'bg-white border border-gray-200') }}">
                                                <div class="min-w-0 flex-1">
                                                    <span class="font-semibold text-xs sm:text-sm {{ $isDone ? 'line-through text-gray-400' : 'text-gray-900' }} break-words">
                                                        {{ $act->nama_kegiatan }}
                                                    </span>
                                                </div>

                                                {{-- Tombol Edit / Hapus Kegiatan --}}
                                                <div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100">
                                                    <button type="button"
                                                            onclick="openEditActivityModal({{ $act->id }}, '{{ addslashes($act->nama_kegiatan) }}', {{ $act->target_hst }}, '{{ $row['date']->toDateString() }}', `{{ addslashes($act->aplikasi_obat ?? '') }}`, `{{ addslashes($act->sasaran ?? '') }}`, `{{ addslashes($act->keterangan ?? $act->catatan ?? '') }}`, {{ json_encode($act->foto_urls) }}, {{ (float) ($act->jumlah_tank ?? 1) }}, {{ (float) ($act->total_biaya_obat ?? 0) }})"
                                                            title="Edit kegiatan"
                                                            class="p-1 rounded hover:bg-gray-200 text-text-secondary">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                        </svg>
                                                    </button>
                                                    <form method="POST"
                                                          action="/kalender-hst/kegiatan/{{ $act->id }}"
                                                          data-confirm-delete="{{ $act->nama_kegiatan }}"
                                                          data-confirm-title="Hapus Kegiatan?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Hapus kegiatan dan file foto fisiknya" class="p-1 rounded hover:bg-red-100 text-red-500">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if ($crop->status === 'Sedang Ditanam')
                                            <button type="button"
                                                    onclick="openAddActivityModal({{ $row['hst'] }}, '{{ $row['date']->toDateString() }}')"
                                                    class="mt-1 text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-0.5 opacity-80 hover:opacity-100 cursor-pointer">
                                                <span>Catat lagi</span>
                                            </button>
                                        @endif
                                    </div>
                                @elseif ($isPlantingDay)
                                    <div class="space-y-1.5">
                                        <div class="text-emerald-800 font-bold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12m0 0C12 7.58 8.42 4 4 4c0 4.42 3.58 8 8 8zm0 0c0-4.42 3.58-8 8-8 0 4.42-3.58 8-8 8z"/>
                                            </svg>
                                            <span>Menanam Bibit</span>
                                        </div>
                                        @if ($crop->status === 'Sedang Ditanam')
                                            {{-- <button type="button"
                                                    onclick="openAddActivityModal({{ $row['hst'] }}, '{{ $row['date']->toDateString() }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                <span>Catat Kegiatan / Obat</span>
                                            </button> --}}
                                        @endif
                                    </div>
                                @endif

                                @if (!empty($row['harvests']) && $row['harvests']->isNotEmpty())
                                    <div class="space-y-1.5 {{ $hasAct || $isPlantingDay ? 'mt-2 pt-2 border-t border-gray-200' : '' }}">
                                        @foreach ($row['harvests'] as $hrv)
                                            <div class="p-2 rounded-lg bg-amber-50/90 border border-amber-200 text-xs text-amber-950">
                                                <div class="font-bold flex items-center justify-between gap-2 flex-wrap">
                                                    <span class="flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18c-2 2-3 5-3 9 0 3.5 1 6.5 3 9m0-18c2 2 3 5 3 9 0 3.5-1 6.5-3 9M6.5 7.5c2 1 4.5 1.5 5.5 1.5m-5.5 5c2 1 4.5 1.5 5.5 1.5m11-6.5c-2 1-4.5 1.5-5.5 1.5m5.5 5c-2 1-4.5 1.5-5.5 1.5"/>
                                                        </svg>
                                                        <span>Panen ke-{{ $hrv->panen_ke }}</span>
                                                        @if ($hrv->total_panen)
                                                             <span class="text-amber-800 font-semibold">: {{ $hrv->total_panen }}</span>
                                                        @endif
                                                    </span>
                                                    <div class="flex items-center gap-1.5 text-[11px]">
                                                        @if ($hrv->numeric_harga_kotor > 0)
                                                            <span class="text-emerald-800 font-extrabold bg-emerald-100/70 px-1.5 py-0.5 rounded border border-emerald-300">
                                                                Kotor: {{ $hrv->formatted_harga_kotor }}
                                                            </span>
                                                        @elseif ($hrv->harga_panen)
                                                            <span class="text-emerald-700 font-semibold">{{ str_starts_with($hrv->harga_panen, 'Rp') ? $hrv->harga_panen : 'Rp ' . $hrv->harga_panen }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($hrv->catatan)
                                                    <p class="text-[11px] text-amber-800 mt-0.5 italic">{{ $hrv->catatan }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($crop->status === 'Sedang Ditanam' && ! $hasAct && ! $isPlantingDay)
                                    <button type="button"
                                            onclick="openAddActivityModal({{ $row['hst'] }}, '{{ $row['date']->toDateString() }}')"
                                            class="{{ $isToday ? 'inline-flex font-bold text-emerald-800 bg-white px-2.5 py-1 rounded-md border border-emerald-300 shadow-xs' : 'inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-md text-emerald-800 bg-emerald-50/70 hover:bg-emerald-100 border border-emerald-200 shadow-2xs transition-colors' }} cursor-pointer {{ !empty($row['harvests']) && $row['harvests']->isNotEmpty() ? 'mt-2' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        <span>Catat</span>
                                    </button>
                                @endif
                            </td>

                            {{-- 4. Kolom APLIKASI OBAT (Berstruktur & Rapi) --}}
                            <td class="py-3 px-4 align-top leading-relaxed border-b border-r border-gray-300 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-300 text-emerald-950 font-medium' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a] text-gray-800' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0] text-gray-800' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200 text-gray-800')) }}">
                                @if ($hasAct)
                                    <div class="space-y-2.5">
                                        @foreach ($row['activities'] as $act)
                                            <div>
                                                @if ($act->aplikasi_obat)
                                                    @php
                                                        $rawObat = $act->aplikasi_obat;
                                                        // Pecah berdasarkan koma jika ada multi-obat
                                                        $obatItems = array_filter(array_map('trim', explode(',', $rawObat)));
                                                    @endphp
                                                    @if (count($obatItems) > 1)
                                                        <div class="space-y-1.5 obat-group-container">
                                                            @foreach ($obatItems as $oIdx => $oItem)
                                                                @php
                                                                    $itemText = $oItem;
                                                                    $itemPrice = null;
                                                                    if (preg_match('/^(.*?)\s*\((Rp\s*[\d.,]+)\)$/i', $oItem, $mPrice)) {
                                                                        $itemText = trim($mPrice[1]);
                                                                        $itemPrice = trim($mPrice[2]);
                                                                    }
                                                                @endphp
                                                                <div class="text-xs leading-snug flex items-start gap-1.5 {{ $oIdx >= 2 ? 'obat-extra-item hidden' : '' }}">
                                                                    <span class="text-emerald-600 font-bold shrink-0 mt-0.5">•</span>
                                                                    <div class="min-w-0 flex flex-wrap items-center gap-1">
                                                                        <span class="font-medium {{ $isToday ? 'text-emerald-950 font-semibold' : 'text-gray-900' }} break-words">{{ $itemText }}</span>
                                                                        @if ($itemPrice)
                                                                            <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100/90 text-emerald-800 border border-emerald-300 whitespace-nowrap">{{ $itemPrice }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if (count($obatItems) > 2)
                                                                <button type="button"
                                                                        onclick="toggleObatItems(this)"
                                                                        class="text-[10px] text-emerald-700 hover:text-emerald-900 font-bold underline cursor-pointer mt-0.5 block">
                                                                    + Lihat {{ count($obatItems) - 2 }} obat lainnya...
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        @php
                                                            $singleText = $rawObat;
                                                            $singlePrice = null;
                                                            if (preg_match('/^(.*?)\s*\((Rp\s*[\d.,]+)\)$/i', $rawObat, $mPrice)) {
                                                                $singleText = trim($mPrice[1]);
                                                                $singlePrice = trim($mPrice[2]);
                                                            }
                                                        @endphp
                                                        <div class="text-xs font-medium leading-relaxed {{ $isToday ? 'text-emerald-950 font-semibold' : 'text-gray-900' }} break-words flex flex-wrap items-center gap-1">
                                                            <span>{{ $singleText }}</span>
                                                            @if ($singlePrice)
                                                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100/90 text-emerald-800 border border-emerald-300 whitespace-nowrap">{{ $singlePrice }}</span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if (($act->jumlah_tank && $act->jumlah_tank > 1) || $act->total_biaya_obat)
                                                        <div class="flex items-center gap-1.5 flex-wrap mt-1.5 pt-1 border-t border-emerald-100/60">
                                                            @if ($act->jumlah_tank)
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200" title="Jumlah tank semprot">
                                                                    <svg class="w-3 h-3 text-blue-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                                                    </svg>
                                                                    <span>{{ $act->formatted_jumlah_tank }}</span>
                                                                </span>
                                                            @endif
                                                            @if ($act->total_biaya_obat)
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100/90 text-emerald-900 border border-emerald-300" title="Total biaya obat kegiatan ini">
                                                                    <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span>Total: {{ $act->formatted_total_biaya_obat }}</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @if ($crop->status === 'Sedang Ditanam')
                                        <button type="button"
                                                onclick="openAddActivityModal({{ $row['hst'] }}, '{{ $row['date']->toDateString() }}')"
                                                class="text-xs text-emerald-700 hover:text-emerald-900 font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer"
                                                title="Catat aplikasi obat pada HST ini">
                                            <span>+ Tambah Obat</span>
                                        </button>
                                    @else
                                        <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                    @endif
                                @endif
                            </td>

                            {{-- 5. Kolom SASARAN --}}
                            <td class="py-3 px-4 align-top border-b border-r border-gray-300 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-300 text-emerald-950' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a] text-gray-800' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0] text-gray-800' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200 text-gray-800')) }}">
                                @if ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            <div>
                                                @if ($act->sasaran)
                                                    <span class="inline-block px-2 py-0.5 rounded {{ $isToday ? 'bg-white/90 text-emerald-900 font-semibold border border-emerald-300' : 'bg-gray-100 text-gray-800 text-xs font-medium' }} break-words max-w-[200px]">
                                                        {{ $act->sasaran }}
                                                    </span>
                                                @else
                                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                @endif
                            </td>

                            {{-- 6. Kolom KETERANGAN (Expandable jika Panjang) --}}
                            <td class="py-3 px-4 align-top border-b border-r border-gray-300 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] border-r-emerald-300 text-emerald-950 font-medium' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a] text-gray-800' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0] text-gray-800' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200 text-gray-800')) }}">
                                @if ($isPlantingDay)
                                    <div class="font-bold text-emerald-800 flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 border border-emerald-300 text-emerald-900">
                                            Hari Menanam
                                        </span>
                                    </div>
                                    @if ($crop->catatan)
                                        <p class="text-xs text-text-muted mt-1 italic break-words">{{ $crop->catatan }}</p>
                                    @endif
                                @elseif ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            @php
                                                $ket = $act->keterangan ?? $act->catatan;
                                            @endphp
                                            @if ($ket)
                                                <div class="text-xs leading-relaxed {{ $isToday ? 'text-emerald-950 font-medium' : 'text-gray-700' }}">
                                                    <div class="{{ strlen($ket) > 80 ? 'line-clamp-2' : '' }} break-words transition-all">
                                                        {{ $ket }}
                                                    </div>
                                                    @if (strlen($ket) > 80)
                                                        <button type="button"
                                                                onclick="toggleLineClamp(this)"
                                                                class="text-[10px] text-emerald-700 hover:text-emerald-900 font-bold underline mt-0.5 block cursor-pointer">
                                                            Selengkapnya...
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
                                @endif
                            </td>

                            {{-- 7. Kolom FOTO (Kolom Khusus Dokumentasi Foto) --}}
                            <td class="py-3 px-3 align-top text-center border-b border-gray-200 {{ $isToday ? 'bg-[#dcfce7] border-b-[#86efac] text-emerald-950 font-medium' : ($isTomorrow ? 'bg-[#fffbeb] border-b-[#fde68a] text-gray-800' : ($isPlantingDay ? 'bg-[#f0fdf4] border-b-[#a7f3d0] text-gray-800' : 'bg-white group-hover:bg-gray-50/50 border-b-gray-200 text-gray-800')) }}">
                                @php
                                    $rowPhotos = [];
                                    if ($hasAct) {
                                        foreach ($row['activities'] as $aItem) {
                                            if (!empty($aItem->foto_urls)) {
                                                foreach ($aItem->foto_urls as $u) {
                                                    $rowPhotos[] = [
                                                        'url' => $u,
                                                        'name' => $aItem->nama_kegiatan,
                                                        'hst' => $aItem->target_hst,
                                                        'all' => $aItem->foto_urls,
                                                        'activity_id' => $aItem->id,
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                @if (!empty($rowPhotos))
                                    <div class="flex items-center justify-center">
                                        @php
                                            $firstPhoto = $rowPhotos[0];
                                            $lightboxItems = array_map(function($p) {
                                                return [
                                                    'url' => $p['url'],
                                                    'activityId' => $p['activity_id'],
                                                    'name' => $p['name'] . ' (HST ' . $p['hst'] . ')',
                                                ];
                                            }, $rowPhotos);
                                        @endphp
                                        <button type="button"
                                                onclick="openImageLightbox({{ json_encode($lightboxItems) }}, 0, '{{ addslashes($firstPhoto['name']) }} (HST {{ $firstPhoto['hst'] }})', {{ $firstPhoto['activity_id'] }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 active:scale-95 border border-emerald-300 text-emerald-800 text-xs font-bold transition-all shadow-2xs hover:shadow-xs cursor-pointer group whitespace-nowrap"
                                                title="Klik untuk melihat {{ count($rowPhotos) }} foto dokumentasi">
                                            <svg class="w-3.5 h-3.5 text-emerald-700 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                                            </svg>
                                            <span>Lihat Foto ({{ count($rowPhotos) }})</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }} text-center block">-</span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Footer Bar / Load More HST ── --}}
        <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="text-text-muted">
                @if ($crop->status === 'Sedang Ditanam' && $maxRowHst > $currentHst + 1)
                    Menampilkan baris HST 0 sampai HST {{ $maxRowHst }} <span class="text-gray-400 font-normal">(Hari ini: HST {{ $currentHst }}, Besok: HST {{ $currentHst + 1 }})</span>.
                @elseif ($crop->status === 'Sedang Ditanam')
                    Menampilkan baris HST 0 sampai besok (HST {{ $maxRowHst }}).
                @else
                    Menampilkan baris HST 0 sampai panen (HST {{ $maxRowHst }}).
                @endif
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                @if ($crop->status === 'Sedang Ditanam')
                    @if ($maxRowHst > $currentHst + 1)
                        <a href="/kalender-hst/tanaman/{{ $crop->id }}?limit_hst=reset"
                           class="px-3.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 font-semibold text-amber-800 transition-colors shadow-xs"
                           title="Kembalikan batas tampilan kalender ke besok (HST {{ $currentHst + 1 }})">
                            ↺ Reset ke Besok (HST {{ $currentHst + 1 }})
                        </a>
                    @endif
                    <a href="/kalender-hst/tanaman/{{ $crop->id }}?limit_hst={{ $maxRowHst + 15 }}#hst-row-{{ $maxRowHst }}"
                       class="px-3.5 py-1.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 font-semibold text-gray-800 transition-colors shadow-xs">
                        + Tambah 15 Baris HST
                    </a>
                    <a href="/kalender-hst/tanaman/{{ $crop->id }}?limit_hst=120#hst-row-{{ $maxRowHst }}"
                       class="px-3.5 py-1.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 font-semibold text-gray-800 transition-colors shadow-xs">
                        Tampilkan 120 HST (1 Musim Penuh)
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH KEGIATAN (KEGIATAN, OBAT, SASARAN, KETERANGAN) --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-activity" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Catat Log Kegiatan Perawatan</h3>
                <p class="text-xs text-text-muted mt-0.5">Komoditas: {{ $crop->nama_tanaman }} ({{ $crop->varietas ?? 'Tanpa Varietas' }})</p>
            </div>
            <button type="button" onclick="closeModal('modal-add-activity')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-add-activity" method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}/kegiatan" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="redirect_to" value="show">

            {{-- Synchronized HST and Date --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="add-target-hst" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Target HST (Hari Setelah Tanam)
                        </label>
                        <div class="relative flex items-center">
                            <input type="number"
                                   id="add-target-hst"
                                   name="target_hst"
                                   min="0"
                                   value="{{ $currentHst }}"
                                   oninput="syncFromHst('add')"
                                   class="field-input w-full px-3 py-2 rounded-xl border border-emerald-300 text-sm font-mono font-bold text-emerald-950 bg-white focus:border-primary">
                            <span class="absolute right-3 text-xs font-mono font-semibold text-emerald-700">HST</span>
                        </div>
                    </div>

                    <div>
                        <label for="add-tanggal-kegiatan" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Tanggal Kalender
                        </label>
                        <input type="date"
                               id="add-tanggal-kegiatan"
                               name="tanggal_kegiatan"
                               value="{{ now()->toDateString() }}"
                               oninput="syncFromDate('add')"
                               class="field-input w-full px-3 py-2 rounded-xl border border-emerald-300 text-sm bg-white focus:border-primary">
                    </div>
                </div>
                <p class="text-[11px] text-emerald-800">
                    HST 0 = Tanggal Menanam ({{ $crop->tanggal_tanam->format('d-m-Y') }}). Mengubah tanggal atau HST akan sinkron otomatis.
                </p>
            </div>

            {{-- Nama Kegiatan --}}
            <div>
                <label for="add-nama-kegiatan" class="block text-xs font-semibold text-gray-900 mb-1">
                    Nama Kegiatan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           id="add-nama-kegiatan"
                           name="nama_kegiatan"
                           placeholder="Contoh: Nyemprot, Pemupukan Susulan, Kocor, Penyiangan"
                           required
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                </div>
            </div>

            {{-- Aplikasi Obat & Dosis Terintegrasi --}}
            <div class="rounded-2xl border border-emerald-200/90 bg-emerald-50/40 p-3 sm:p-4 space-y-3" id="add-obat-section">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                            <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">
                                Aplikasi Obat & Dosis (Terintegrasi)
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                onclick="addObatRow('add')"
                                id="add-btn-add-mix"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>Tambah Obat</span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Rows Container --}}
                <div id="add-obat-rows-container" class="space-y-2.5"></div>

                {{-- Kontrol Jumlah Tank & Total Estimasi Biaya Obat --}}
                <div class="rounded-xl border border-emerald-200 bg-white p-3 space-y-2.5 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <label for="add-jumlah-tank" class="block text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                <span>Jumlah Tank Semprot</span>
                            </label>
                            <p class="text-[11px] text-gray-500">
                                Takaran dosis per baris obat di atas dihitung untuk 1 tangki semprot (±16 Liter).
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="inline-flex items-center rounded-xl border border-gray-300 bg-gray-50 p-1 shadow-2xs">
                                <button type="button"
                                        onclick="stepTank('add', -1)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-gray-200 text-gray-700 font-bold border border-gray-200 text-sm cursor-pointer transition-colors shadow-2xs"
                                        title="Kurangi 1 tank">
                                    −
                                </button>
                                <input type="number"
                                       id="add-jumlah-tank"
                                       name="jumlah_tank"
                                       value="1"
                                       min="0.1"
                                       step="any"
                                       class="w-14 text-center font-black text-sm text-gray-900 bg-transparent border-0 focus:ring-0 focus:outline-none p-0"
                                       oninput="updateObatSummary('add')">
                                <span class="text-xs font-bold text-gray-500 pr-2">Tank</span>
                                <button type="button"
                                        onclick="stepTank('add', 1)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-gray-200 text-gray-700 font-bold border border-gray-200 text-sm cursor-pointer transition-colors shadow-2xs"
                                        title="Tambah 1 tank">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Total Summary Bar --}}
                    <div id="add-obat-total-bar" class="p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shadow-2xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-xs flex-wrap">
                                <span class="text-gray-600 font-medium">Subtotal Dosis / Tank:</span>
                                <span id="add-obat-subtotal-display" class="font-bold text-gray-900">Rp 0</span>
                                <span id="add-obat-tank-display" class="text-xs font-bold text-emerald-800">x 1 Tank</span>
                                <span class="text-gray-400">•</span>
                                <span class="text-[10px] text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-full font-bold" id="add-obat-count-badge">
                                    0 Obat
                                </span>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-center gap-1 border-t sm:border-t-0 pt-2 sm:pt-0 border-emerald-200/60">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-900">Total Biaya Obat</span>
                            <span id="add-obat-total-display" class="font-black text-emerald-700 text-base sm:text-lg">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- Input tersembunyi untuk menyimpan rincian aplikasi_obat dan total_biaya_obat saat submit --}}
                <input type="hidden" id="add-aplikasi-obat" name="aplikasi_obat" value="">
                <input type="hidden" id="add-total-biaya-obat" name="total_biaya_obat" value="0">
            </div>

            {{-- Sasaran --}}
            <div>
                <label for="add-sasaran" class="block text-xs font-semibold text-gray-900 mb-1">
                    Sasaran (Hama / Penyakit / Tujuan)
                </label>
                <input type="text"
                       id="add-sasaran"
                       name="sasaran"
                       placeholder="Contoh: Ulat Grayak, Patek / Antraknosa, Pencegahan Jamur"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="add-keterangan" class="block text-xs font-semibold text-gray-900 mb-1">
                    Keterangan (Opsional)
                </label>
                <textarea id="add-keterangan"
                          name="keterangan"
                          rows="2"
                          placeholder="Catatan teknis, takaran air, kondisi tanaman, dsb."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary"></textarea>
            </div>

            {{-- Upload Dokumentasi Foto (Maksimal 5 Foto) --}}
            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50/60 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-gray-900">
                        Dokumentasi Foto Kegiatan <span class="text-text-muted font-normal">(Maks. 5 foto)</span>
                    </label>
                    <span id="add-photo-counter" class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        0/5 Foto
                    </span>
                </div>
                <p class="text-[11px] text-text-muted">
                    Format: JPG, PNG, WEBP. Foto otomatis dikompresi sebelum disimpan dan diunggah ke Cloudinary.
                </p>

                <div class="mt-2">
                    <label for="add-foto-kegiatan"
                           class="flex flex-col items-center justify-center p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl cursor-pointer bg-white transition-colors">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">+ Pilih / Jepret Foto (Maks. 5)</span>
                        <input type="file"
                               id="add-foto-kegiatan"
                               name="foto_kegiatan[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="handleAddPhotoChange(event)"
                               class="hidden">
                    </label>
                </div>

                {{-- Container Pratinjau Foto --}}
                <div id="add-photo-preview-grid" class="grid grid-cols-5 gap-2 pt-1 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-add-activity')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm">
                    Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT KEGIATAN                                         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-activity" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Edit Kegiatan Perawatan</h3>
                <p class="text-xs text-text-muted mt-0.5">Perbarui jadwal atau rincian aplikasi obat</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-activity')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-edit-activity" method="POST" action="" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            {{-- Synchronized HST and Date --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit-target-hst" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Target HST
                        </label>
                        <div class="relative flex items-center">
                            <input type="number"
                                   id="edit-target-hst"
                                   name="target_hst"
                                   min="0"
                                   required
                                   oninput="syncFromHst('edit')"
                                   class="field-input w-full px-3 py-2 rounded-xl border border-emerald-300 text-sm font-mono font-bold text-emerald-950 bg-white focus:border-primary">
                            <span class="absolute right-3 text-xs font-mono font-semibold text-emerald-700">HST</span>
                        </div>
                    </div>

                    <div>
                        <label for="edit-tanggal-kegiatan" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Tanggal Kalender
                        </label>
                        <input type="date"
                               id="edit-tanggal-kegiatan"
                               oninput="syncFromDate('edit')"
                               class="field-input w-full px-3 py-2 rounded-xl border border-emerald-300 text-sm bg-white focus:border-primary">
                    </div>
                </div>
            </div>

            <div>
                <label for="edit-nama-kegiatan" class="block text-xs font-semibold text-gray-900 mb-1">
                    Nama Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="edit-nama-kegiatan"
                       name="nama_kegiatan"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            {{-- Aplikasi Obat & Dosis Terintegrasi (Edit Mode) --}}
            <div class="rounded-2xl border border-emerald-200/90 bg-emerald-50/40 p-3 sm:p-4 space-y-3" id="edit-obat-section">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                            <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">
                                Aplikasi Obat & Dosis (Terintegrasi)
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                onclick="addObatRow('edit')"
                                id="edit-btn-add-mix"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>Tambah Obat</span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Rows Container --}}
                <div id="edit-obat-rows-container" class="space-y-2.5"></div>

                {{-- Kontrol Jumlah Tank & Total Estimasi Biaya Obat (Edit Mode) --}}
                <div class="rounded-xl border border-emerald-200 bg-white p-3 space-y-2.5 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <label for="edit-jumlah-tank" class="block text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                <span>Jumlah Tank Semprot</span>
                            </label>
                            <p class="text-[11px] text-gray-500">
                                Takaran dosis per baris obat di atas dihitung untuk 1 tangki semprot (±16 Liter).
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="inline-flex items-center rounded-xl border border-gray-300 bg-gray-50 p-1 shadow-2xs">
                                <button type="button"
                                        onclick="stepTank('edit', -1)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-gray-200 text-gray-700 font-bold border border-gray-200 text-sm cursor-pointer transition-colors shadow-2xs"
                                        title="Kurangi 1 tank">
                                    −
                                </button>
                                <input type="number"
                                       id="edit-jumlah-tank"
                                       name="jumlah_tank"
                                       value="1"
                                       min="0.1"
                                       step="any"
                                       class="w-14 text-center font-black text-sm text-gray-900 bg-transparent border-0 focus:ring-0 focus:outline-none p-0"
                                       oninput="updateObatSummary('edit')">
                                <span class="text-xs font-bold text-gray-500 pr-2">Tank</span>
                                <button type="button"
                                        onclick="stepTank('edit', 1)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-gray-200 text-gray-700 font-bold border border-gray-200 text-sm cursor-pointer transition-colors shadow-2xs"
                                        title="Tambah 1 tank">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Total Summary Bar --}}
                    <div id="edit-obat-total-bar" class="p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shadow-2xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-xs flex-wrap">
                                <span class="text-gray-600 font-medium">Subtotal Dosis / Tank:</span>
                                <span id="edit-obat-subtotal-display" class="font-bold text-gray-900">Rp 0</span>
                                <span id="edit-obat-tank-display" class="text-xs font-bold text-emerald-800">x 1 Tank</span>
                                <span class="text-gray-400">•</span>
                                <span class="text-[10px] text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-full font-bold" id="edit-obat-count-badge">
                                    0 Obat
                                </span>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-center gap-1 border-t sm:border-t-0 pt-2 sm:pt-0 border-emerald-200/60">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-900">Total Biaya Obat</span>
                            <span id="edit-obat-total-display" class="font-black text-emerald-700 text-base sm:text-lg">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- Input tersembunyi untuk menyimpan rincian aplikasi_obat dan total_biaya_obat saat submit --}}
                <input type="hidden" id="edit-aplikasi-obat" name="aplikasi_obat" value="">
                <input type="hidden" id="edit-total-biaya-obat" name="total_biaya_obat" value="0">
            </div>

            <div>
                <label for="edit-sasaran" class="block text-xs font-semibold text-gray-900 mb-1">
                    Sasaran (Hama / Penyakit / Tujuan)
                </label>
                <input type="text"
                       id="edit-sasaran"
                       name="sasaran"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div>
                <label for="edit-keterangan" class="block text-xs font-semibold text-gray-900 mb-1">
                    Keterangan (Catatan)
                </label>
                <textarea id="edit-keterangan"
                          name="keterangan"
                          rows="2"
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary"></textarea>
            </div>

            {{-- Manajemen Foto Dokumentasi (Maks. 5 Foto) --}}
            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50/60 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-gray-900">
                        Dokumentasi Foto Kegiatan <span class="text-text-muted font-normal">(Maks. 5 foto)</span>
                    </label>
                    <span id="edit-photo-quota-badge" class="text-[11px] font-semibold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full border border-emerald-300">
                        0/5 Foto
                    </span>
                </div>

                {{-- Foto yang Sudah Ada --}}
                <div id="edit-existing-photos-section" class="space-y-1.5 hidden">
                    <span class="text-[11px] font-semibold text-gray-700 block">Foto Tersimpan:</span>
                    <div id="edit-existing-photos-grid" class="grid grid-cols-5 gap-2"></div>
                </div>

                {{-- Input Foto Baru --}}
                <div id="edit-upload-box" class="space-y-1">
                    <label for="edit-foto-kegiatan"
                           class="flex flex-col items-center justify-center p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl cursor-pointer bg-white transition-colors">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span id="edit-upload-label-text" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">+ Tambah Foto Baru</span>
                        <input type="file"
                               id="edit-foto-kegiatan"
                               name="foto_kegiatan[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="handleEditPhotoChange(event)"
                               class="hidden">
                    </label>
                    <div id="edit-new-photo-preview-grid" class="grid grid-cols-5 gap-2 pt-1 hidden"></div>
                </div>

                {{-- Hidden input container untuk menandai foto yang dihapus saat submit form --}}
                <div id="edit-deleted-photos-container"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-edit-activity')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: LIGHTBOX PRATINJAU FOTO RESOLUSI PENUH                 --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-image-lightbox"
     onclick="if(event.target === this) closeImageLightbox()"
     class="fixed inset-0 z-60 hidden items-center justify-center p-2 sm:p-4 bg-slate-950/85 backdrop-blur-md select-none transition-all">
    <div class="relative max-w-4xl w-full flex flex-col items-center">
        <!-- Header Lightbox: Judul, Counter, Tombol Buka/Tutup -->
        <div class="w-full flex items-center justify-between text-white pb-2.5 px-2">
            <div>
                <h4 id="lightbox-title" class="text-sm sm:text-base font-bold text-white drop-shadow"></h4>
                <p id="lightbox-counter" class="text-xs text-gray-300 font-mono"></p>
            </div>
            <div class="flex items-center gap-2">
                <a id="lightbox-download-link"
                   href="#"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors text-xs flex items-center gap-1.5"
                   title="Buka Foto Asli Resolusi Penuh">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="hidden sm:inline">Buka Asli</span>
                </a>
                <button type="button"
                        id="lightbox-btn-delete"
                        onclick="deleteCurrentLightboxPhoto()"
                        class="px-2.5 py-2 rounded-xl bg-red-600/85 hover:bg-red-600 active:scale-95 text-white transition-all text-xs font-semibold flex items-center gap-1.5 shadow-sm"
                        title="Hapus foto ini (file fisik di Cloudinary/server langsung dihapus permanen)">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    <span>Hapus Foto</span>
                </button>
                <button type="button"
                        onclick="closeImageLightbox()"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/25 text-white transition-colors"
                        title="Tutup (Esc)">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Wadah Gambar Utama dengan Navigasi Sebelumnya / Berikutnya -->
        <div class="relative w-full flex items-center justify-center max-h-[78vh] overflow-hidden rounded-2xl bg-black/40">
            <button type="button"
                    id="lightbox-btn-prev"
                    onclick="prevLightboxImage()"
                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-black/60 hover:bg-black/90 text-white transition-all z-10 hidden shadow-lg focus:outline-none">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <img id="lightbox-img"
                 src=""
                 alt="Dokumentasi HST"
                 class="max-h-[78vh] w-auto max-w-full object-contain rounded-xl shadow-2xl transition-all duration-200">

            <button type="button"
                    id="lightbox-btn-next"
                    onclick="nextLightboxImage()"
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-black/60 hover:bg-black/90 text-white transition-all z-10 hidden shadow-lg focus:outline-none">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT DATA TANAMAN (TERMASUK POPULASI & VARIETAS)      --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-crop" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Edit Profil Tanaman</h3>
                <p class="text-xs text-text-muted mt-0.5">Perbarui benih, populasi, dan tanggal menanam</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-crop')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-crop-nama" class="block text-xs font-semibold text-gray-900 mb-1">
                    Nama Tanaman / Komoditas <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="edit-crop-nama"
                       name="nama_tanaman"
                       value="{{ $crop->nama_tanaman }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit-crop-varietas" class="block text-xs font-semibold text-gray-900 mb-1">
                        Benih yang Ditanam / Varietas
                    </label>
                    <input type="text"
                           id="edit-crop-varietas"
                           name="varietas"
                           value="{{ $crop->varietas }}"
                           placeholder="Contoh: Talenta F1, Ori 212"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                </div>

                <div>
                    <label for="edit-crop-populasi" class="block text-xs font-semibold text-gray-900 mb-1">
                        Populasi
                    </label>
                    <input type="text"
                           id="edit-crop-populasi"
                           name="populasi"
                           value="{{ $crop->populasi }}"
                           placeholder="Contoh: 2.000 Pohon / 1 Ha"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                </div>
            </div>

            <div>
                <label for="edit-crop-tanggal" class="block text-xs font-semibold text-gray-900 mb-1">
                    Tanggal Menanam (0 HST) <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="edit-crop-tanggal"
                       name="tanggal_tanam"
                       value="{{ $crop->tanggal_tanam->toDateString() }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div>
                <label for="edit-crop-catatan" class="block text-xs font-semibold text-gray-900 mb-1">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea id="edit-crop-catatan"
                          name="catatan"
                          rows="2"
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">{{ $crop->catatan }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-edit-crop')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TANDAI SUDAH DIPANEN                                  --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-harvest" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 id="harvest-modal-heading" class="text-base font-bold text-gray-900">Catat Panen ke-{{ $crop->next_harvest_number }}</h3>
                <p class="text-xs text-text-muted mt-0.5">{{ $crop->nama_tanaman }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-harvest')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-harvest" method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}/panen" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="harvest-date" class="block text-xs font-semibold text-gray-900 mb-1">
                    Tanggal Panen Aktual <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="harvest-date"
                       name="tanggal_panen"
                       value="{{ now()->toDateString() }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                <p class="text-[11px] text-text-muted mt-1">
                    Catatan panen/petikan akan dicatat pada HST ini. Tanaman tetap aktif hingga Anda memilih 'Akhiri Tanaman'.
                </p>
            </div>

            {{-- Total Panen, Harga Satuan, & Total Harga Kotor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="harvest-total" class="block text-xs font-semibold text-gray-900 mb-1">
                        Total Panen <span class="text-text-muted font-normal">(opsional)</span>
                    </label>
                    <input type="text"
                           id="harvest-total"
                           name="total_panen"
                           placeholder="Contoh: 100 kg"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                </div>
                <div>
                    <label for="harvest-price" class="block text-xs font-semibold text-gray-900 mb-1">
                        Harga Satuan Hari Ini <span class="text-text-muted font-normal">(opsional)</span>
                    </label>
                    <div class="flex items-center rounded-xl border border-gray-300 bg-white overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all">
                        <span class="px-3 py-2 text-xs font-bold text-gray-500 bg-gray-50 border-r border-gray-200 select-none shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               id="harvest-price"
                               name="harga_panen"
                               placeholder="3.000"
                               inputmode="numeric"
                               class="w-full px-3 py-2 text-sm border-0 focus:outline-none focus:ring-0 text-gray-900 font-semibold bg-transparent">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="harvest-total-kotor" class="block text-xs font-semibold text-gray-900">
                        Total Harga Kotor <span class="text-text-muted font-normal">(opsional)</span>
                    </label>
                    <span class="text-[11px] text-emerald-700 font-medium bg-emerald-50 px-2 py-0.5 rounded-md">Otomatis dihitung (Total Panen × Harga)</span>
                </div>
                <div class="flex items-center rounded-xl border border-emerald-300 bg-emerald-50/25 overflow-hidden focus-within:border-emerald-600 focus-within:ring-1 focus-within:ring-emerald-600 transition-all">
                    <span class="px-3.5 py-2 text-xs font-bold text-emerald-800 bg-emerald-100/70 border-r border-emerald-200 select-none shrink-0">
                        Rp
                    </span>
                    <input type="text"
                           id="harvest-total-kotor"
                           name="total_harga_kotor"
                           placeholder="300.000"
                           inputmode="numeric"
                           class="w-full px-3.5 py-2 text-sm border-0 focus:outline-none focus:ring-0 text-emerald-950 font-bold bg-transparent">
                </div>
            </div>

            <div>
                <label for="harvest-notes" class="block text-xs font-semibold text-gray-900 mb-1">
                    Keterangan / Catatan Panen <span class="text-text-muted font-normal">(opsional)</span>
                </label>
                <textarea id="harvest-notes"
                          name="catatan"
                          rows="2"
                          placeholder="Misal: Kualitas buah grade A, dijual langsung ke pedagang..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-harvest')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        id="harvest-modal-submit-btn"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm">
                    Simpan Panen ke-{{ $crop->next_harvest_number }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: AKHIRI TANAMAN INI                                      --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-end-crop" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Akhiri Tanaman Ini</h3>
                <p class="text-xs text-amber-700 font-semibold mt-0.5">{{ $crop->nama_tanaman }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-end-crop')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-end-crop" method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}/akhiri" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="end-date" class="block text-xs font-semibold text-gray-900 mb-1">
                    Tanggal Diakhiri <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="end-date"
                       name="tanggal_akhir"
                       value="{{ now()->toDateString() }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                <p class="text-[11px] text-text-muted mt-1">
                    HST akan dikunci pada tanggal ini dan tanaman dipindahkan ke Riwayat Tanam.
                </p>
            </div>

            <div>
                <label for="end-reason" class="block text-xs font-semibold text-gray-900 mb-1">
                    Alasan Diakhiri <span class="text-text-muted font-normal">(opsional)</span>
                </label>
                <select id="end-reason" name="alasan" class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                    <option value="">-- Pilih Alasan (Opsional) --</option>
                    <option value="Gagal Panen (Hama / Penyakit)">Gagal Panen (Hama / Penyakit)</option>
                    <option value="Gagal Panen (Cuaca / Bencana)">Gagal Panen (Cuaca / Bencana)</option>
                    <option value="Dibongkar / Ganti Tanaman">Dibongkar / Ganti Tanaman</option>
                    <option value="Masa Produktif Selesai">Masa Produktif Selesai</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label for="end-notes" class="block text-xs font-semibold text-gray-900 mb-1">
                    Keterangan Tambahan <span class="text-text-muted font-normal">(opsional)</span>
                </label>
                <textarea id="end-notes"
                          name="catatan"
                          rows="2"
                          placeholder="Tuliskan catatan kondisi akhir tanaman..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-end-crop')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm">
                    Ya, Akhiri Tanaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const PLANT_DATE_STRING = '{{ $crop->tanggal_tanam->toDateString() }}';
    const PLANT_DATE = new Date(PLANT_DATE_STRING + 'T00:00:00');

    function formatDateToString(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    }

    // Dual Sync: HST <-> Date (HST 0 = Tanggal Tanam)
    function syncFromHst(prefix) {
        const hstInput = document.getElementById(`${prefix}-target-hst`);
        const dateInput = document.getElementById(`${prefix}-tanggal-kegiatan`);
        if (!hstInput || !dateInput) return;

        const hstVal = parseInt(hstInput.value, 10);
        if (isNaN(hstVal) || hstVal < 0) return;

        const targetDate = new Date(PLANT_DATE.getTime());
        targetDate.setDate(targetDate.getDate() + hstVal);
        dateInput.value = formatDateToString(targetDate);
    }

    function syncFromDate(prefix) {
        const hstInput = document.getElementById(`${prefix}-target-hst`);
        const dateInput = document.getElementById(`${prefix}-tanggal-kegiatan`);
        if (!hstInput || !dateInput || !dateInput.value) return;

        const pickedDate = new Date(dateInput.value + 'T00:00:00');
        const diffTime = pickedDate.getTime() - PLANT_DATE.getTime();
        const diffDays = Math.max(0, Math.round(diffTime / (1000 * 60 * 60 * 24)));
        hstInput.value = diffDays;
    }

    function setQuickKegiatan(prefix, nama) {
        const input = document.getElementById(`${prefix}-nama-kegiatan`);
        if (input) input.value = nama;
    }

    const ALL_MEDICINES = @json($medicines);
    const OTHER_ACTIVE_CROPS = @json($otherActiveCrops ?? []);
    let isAddActivityConfirmed = false;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function highlightMatch(text, query) {
        if (!text) return '';
        if (!query) return escapeHtml(text);
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        const safeText = escapeHtml(text);
        return safeText.replace(regex, '<span class="text-emerald-700 bg-emerald-100 font-extrabold px-0.5 rounded-xs">$1</span>');
    }

    function addObatRow(prefix, medId = '', initialDose = '', customName = '') {
        const container = document.getElementById(`${prefix}-obat-rows-container`);
        if (!container) return;

        let selectedMed = null;
        if (medId) {
            selectedMed = ALL_MEDICINES.find(m => String(m.id) === String(medId));
        }
        if (!selectedMed && customName) {
            selectedMed = ALL_MEDICINES.find(m => m.nama.toLowerCase() === customName.trim().toLowerCase());
        }

        const medName = selectedMed ? selectedMed.nama : (customName || '');
        const medUnit = selectedMed ? (selectedMed.base_unit_label || 'gram') : 'gram';
        const medRate = selectedMed ? (selectedMed.harga_per_base_unit || 0) : 0;
        const medDosis = selectedMed ? (selectedMed.dosis_anjuran || '') : '';
        const medSasaran = selectedMed ? (selectedMed.sasaran_obat || '') : '';
        const medJenis = selectedMed ? (selectedMed.jenis || '') : '';

        const row = document.createElement('div');
        row.className = 'obat-row bg-white rounded-xl p-3 border border-emerald-100 shadow-2xs space-y-2 transition-all';
        
        row.innerHTML = `
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0 relative obat-combobox-wrapper">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            Nama Obat (Ketik untuk mencari)
                        </label>
                        <span class="selected-med-badge text-[10px] font-bold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full ${medJenis ? '' : 'hidden'}">
                            ${escapeHtml(medJenis)}
                        </span>
                    </div>
                    <div class="relative flex items-center">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-gray-400 z-10">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               class="obat-search-input field-input w-full rounded-xl border border-gray-300 text-xs font-bold text-gray-900 bg-white placeholder:text-gray-400 focus:border-primary focus:ring-1 focus:ring-emerald-500"
                               style="padding-left: 2.75rem !important; padding-right: 2.25rem !important; padding-top: 0.6rem; padding-bottom: 0.6rem;"
                               placeholder="Ketik nama obat (misal: Decis, Dithane, NPK...)"
                               value="${escapeHtml(medName)}"
                               autocomplete="off">
                        <button type="button" 
                                class="btn-clear-obat absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 cursor-pointer z-10 ${medName ? '' : 'hidden'}"
                                title="Hapus / Cari Ulang">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Hidden attributes --}}
                    <input type="hidden" class="obat-id-val" value="${selectedMed ? selectedMed.id : ''}">
                    <input type="hidden" class="obat-name-val" value="${escapeHtml(medName)}">
                    <input type="hidden" class="obat-unit-val" value="${escapeHtml(medUnit)}">
                    <input type="hidden" class="obat-rate-val" value="${medRate}">
                    <input type="hidden" class="obat-dosis-val" value="${escapeHtml(medDosis)}">
                    <input type="hidden" class="obat-sasaran-val" value="${escapeHtml(medSasaran)}">

                    {{-- Dropdown Container for search suggestions --}}
                    <div class="obat-dropdown-results absolute left-0 right-0 top-full mt-1.5 max-h-56 overflow-y-auto bg-white rounded-xl shadow-xl border border-gray-200 z-50 hidden divide-y divide-gray-100" style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);"></div>
                </div>

                <button type="button" 
                        onclick="removeObatRow(this, '${prefix}')" 
                        class="btn-remove-obat-row p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors cursor-pointer shrink-0" 
                        style="margin-top: 1.15rem;"
                        title="Hapus baris obat">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 items-end pt-1.5 border-t border-gray-100">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            Dosis Obat
                        </label>
                        <span class="dosis-hint text-[10px] text-emerald-800 font-medium truncate max-w-[150px]">
                            ${medDosis ? 'Anjuran: ' + escapeHtml(medDosis) : ''}
                        </span>
                    </div>
                    <div class="relative flex items-center">
                        <input type="number" 
                               step="any" 
                               min="0" 
                               value="${initialDose}" 
                               placeholder="Misal: 5" 
                               class="dosis-input field-input w-full rounded-xl border border-gray-300 text-xs font-bold text-gray-900 focus:border-primary"
                               style="padding-left: 0.875rem !important; padding-right: 4.5rem !important; padding-top: 0.6rem; padding-bottom: 0.6rem;">
                        <span class="dosis-unit-badge absolute right-2.5 top-1/2 -translate-y-1/2 px-2 py-0.5 rounded text-[10px] font-black tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200 select-none z-10">
                            ${escapeHtml(medUnit)}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Perkiraan Biaya Dosis
                    </label>
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 font-bold text-xs" style="min-height: 38px;">
                        <span class="cost-calc-detail text-[11px] text-emerald-800 font-medium truncate max-w-[150px]">0 ${escapeHtml(medUnit)} × Rp 0</span>
                        <span class="cost-amount font-black text-emerald-900 text-sm whitespace-nowrap ml-2">Rp 0</span>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(row);

        const searchInput = row.querySelector('.obat-search-input');
        const clearBtn = row.querySelector('.btn-clear-obat');
        const doseInput = row.querySelector('.dosis-input');

        // Typing in search input: filter medicines and show dropdown
        searchInput.addEventListener('focus', () => {
            renderObatSuggestions(row, searchInput.value, prefix);
        });

        searchInput.addEventListener('input', () => {
            const val = searchInput.value;
            const nameInput = row.querySelector('.obat-name-val');
            if (nameInput) nameInput.value = val;

            if (val.trim()) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            // Check if exact match
            const exactMatch = ALL_MEDICINES.find(m => m.nama.toLowerCase() === val.trim().toLowerCase());
            if (exactMatch) {
                bindObatDataToRow(row, exactMatch, prefix, false);
            } else {
                const idInput = row.querySelector('.obat-id-val');
                const rateInput = row.querySelector('.obat-rate-val');
                const badge = row.querySelector('.selected-med-badge');
                if (idInput) idInput.value = '';
                if (rateInput) rateInput.value = 0;
                if (badge) badge.classList.add('hidden');
            }

            renderObatSuggestions(row, val, prefix);
            calculateObatRowCost(row);
            updateObatSummary(prefix);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const dropdown = row.querySelector('.obat-dropdown-results');
                if (dropdown) dropdown.classList.add('hidden');
            }
        });

        // Clear button
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            const idInput = row.querySelector('.obat-id-val');
            const nameInput = row.querySelector('.obat-name-val');
            const unitInput = row.querySelector('.obat-unit-val');
            const rateInput = row.querySelector('.obat-rate-val');
            const dosisInputVal = row.querySelector('.obat-dosis-val');
            const sasaranInputVal = row.querySelector('.obat-sasaran-val');
            const badge = row.querySelector('.selected-med-badge');
            const unitBadge = row.querySelector('.dosis-unit-badge');
            const dosisHint = row.querySelector('.dosis-hint');

            if (idInput) idInput.value = '';
            if (nameInput) nameInput.value = '';
            if (unitInput) unitInput.value = 'gram';
            if (rateInput) rateInput.value = 0;
            if (dosisInputVal) dosisInputVal.value = '';
            if (sasaranInputVal) sasaranInputVal.value = '';
            if (badge) badge.classList.add('hidden');
            if (unitBadge) unitBadge.textContent = 'gram';
            if (dosisHint) dosisHint.textContent = '';

            calculateObatRowCost(row);
            updateObatSummary(prefix);
            renderObatSuggestions(row, '', prefix);
            searchInput.focus();
        });

        // Dosage input
        doseInput.addEventListener('input', () => {
            calculateObatRowCost(row);
            updateObatSummary(prefix);
        });

        calculateObatRowCost(row);
        updateObatSummary(prefix);
    }

    function renderObatSuggestions(row, query, prefix) {
        const dropdown = row.querySelector('.obat-dropdown-results');
        if (!dropdown) return;

        const q = (query || '').trim().toLowerCase();
        let filtered = [];

        if (!q) {
            filtered = ALL_MEDICINES;
        } else {
            filtered = ALL_MEDICINES.filter(m => {
                const name = (m.nama || '').toLowerCase();
                const jenis = (m.jenis || '').toLowerCase();
                const sasaran = (m.sasaran_obat || '').toLowerCase();
                const bahan = (m.bahan_aktif || '').toLowerCase();
                return name.includes(q) || jenis.includes(q) || sasaran.includes(q) || bahan.includes(q);
            });
        }

        if (filtered.length === 0) {
            dropdown.innerHTML = `
                <div class="p-3 text-center text-xs text-gray-500">
                    <p class="font-medium text-gray-700">Tidak ada obat dengan nama "<b>${escapeHtml(query)}</b>"</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Teks yang diketik tetap akan disimpan sebagai catatan obat.</p>
                </div>
            `;
            dropdown.classList.remove('hidden');
            return;
        }

        let itemsHtml = '';
        filtered.forEach(m => {
            const rateStr = m.formatted_harga_per_base_unit 
                ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-900 border border-emerald-200">${escapeHtml(m.formatted_harga_per_base_unit)}</span>` 
                : '';
            const kemasanStr = m.kemasan_label ? `<span class="text-[10px] text-gray-500">Kemasan: ${escapeHtml(m.kemasan_label)}</span>` : '';
            const jenisStr = m.jenis ? `<span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">${escapeHtml(m.jenis)}</span>` : '';
            const highlightedName = highlightMatch(m.nama, q);

            itemsHtml += `
                <div class="obat-suggestion-item px-3 py-2 hover:bg-emerald-50 cursor-pointer transition-colors flex items-center justify-between gap-2"
                     data-id="${m.id}"
                     data-nama="${escapeHtml(m.nama)}"
                     data-jenis="${escapeHtml(m.jenis || '')}"
                     data-unit="${escapeHtml(m.base_unit_label || 'gram')}"
                     data-rate="${m.harga_per_base_unit || 0}"
                     data-rate-formatted="${escapeHtml(m.formatted_harga_per_base_unit || '')}"
                     data-dosis="${escapeHtml(m.dosis_anjuran || '')}"
                     data-sasaran="${escapeHtml(m.sasaran_obat || '')}">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-bold text-gray-900 truncate">${highlightedName}</span>
                            ${jenisStr}
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            ${kemasanStr}
                            ${m.sasaran_obat ? `<span class="text-[10px] text-gray-500 truncate max-w-[180px]">Sasaran: ${escapeHtml(m.sasaran_obat)}</span>` : ''}
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        ${rateStr}
                    </div>
                </div>
            `;
        });

        dropdown.innerHTML = itemsHtml;
        dropdown.classList.remove('hidden');

        dropdown.querySelectorAll('.obat-suggestion-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                bindObatDataToRow(row, item.dataset, prefix, true);
            });
        });
    }

    function bindObatDataToRow(row, data, prefix, autoFocusDose = false) {
        const searchInput = row.querySelector('.obat-search-input');
        const clearBtn = row.querySelector('.btn-clear-obat');
        const dropdown = row.querySelector('.obat-dropdown-results');
        const badge = row.querySelector('.selected-med-badge');
        const unitBadge = row.querySelector('.dosis-unit-badge');
        const dosisHint = row.querySelector('.dosis-hint');
        const doseInput = row.querySelector('.dosis-input');

        const idInput = row.querySelector('.obat-id-val');
        const nameInput = row.querySelector('.obat-name-val');
        const unitInput = row.querySelector('.obat-unit-val');
        const rateInput = row.querySelector('.obat-rate-val');
        const dosisInputVal = row.querySelector('.obat-dosis-val');
        const sasaranInputVal = row.querySelector('.obat-sasaran-val');

        const nama = data.nama || '';
        const unit = data.base_unit_label || data.unit || 'gram';
        const rate = data.harga_per_base_unit || data.rate || 0;
        const dosis = data.dosis_anjuran || data.dosis || '';
        const sasaran = data.sasaran_obat || data.sasaran || '';
        const jenis = data.jenis || '';

        if (searchInput) searchInput.value = nama;
        if (idInput) idInput.value = data.id || '';
        if (nameInput) nameInput.value = nama;
        if (unitInput) unitInput.value = unit;
        if (rateInput) rateInput.value = rate;
        if (dosisInputVal) dosisInputVal.value = dosis;
        if (sasaranInputVal) sasaranInputVal.value = sasaran;

        if (unitBadge) unitBadge.textContent = unit;
        if (dosisHint) dosisHint.textContent = dosis ? `Anjuran: ${dosis}` : '';
        if (clearBtn) clearBtn.classList.remove('hidden');

        if (badge) {
            badge.textContent = jenis || 'Obat Terdaftar';
            badge.classList.remove('hidden');
        }

        if (sasaran) {
            const sasaranModalInput = document.getElementById(`${prefix}-sasaran`);
            if (sasaranModalInput && !sasaranModalInput.value.trim()) {
                sasaranModalInput.value = sasaran;
            }
        }

        if (dropdown) dropdown.classList.add('hidden');

        calculateObatRowCost(row);
        updateObatSummary(prefix);

        if (autoFocusDose && doseInput) {
            doseInput.focus();
            if (!doseInput.value) doseInput.select();
        }
    }

    function calculateObatRowCost(row) {
        const doseInput = row.querySelector('.dosis-input');
        const costCalc = row.querySelector('.cost-calc-detail');
        const costAmount = row.querySelector('.cost-amount');
        const rateInput = row.querySelector('.obat-rate-val');
        const unitInput = row.querySelector('.obat-unit-val');
        const nameInput = row.querySelector('.obat-name-val');
        const searchInput = row.querySelector('.obat-search-input');

        const medName = (nameInput?.value || searchInput?.value || '').trim();
        if (!medName) {
            if (costCalc) costCalc.textContent = 'Ketik & pilih obat';
            if (costAmount) costAmount.textContent = 'Rp 0';
            return 0;
        }

        const rate = parseFloat(rateInput?.value || '0');
        const unit = unitInput?.value || 'gram';
        const dose = parseFloat(doseInput?.value || '0');

        if (dose > 0 && rate > 0) {
            const cost = Math.round(dose * rate);
            const formattedCost = 'Rp ' + Number(cost).toLocaleString('id-ID');
            const formattedRate = 'Rp ' + (rate < 10 && rate % 1 !== 0 ? rate.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : Math.round(rate).toLocaleString('id-ID'));
            if (costCalc) costCalc.textContent = `${dose} ${unit} × ${formattedRate}/${unit}`;
            if (costAmount) costAmount.textContent = formattedCost;
            return cost;
        } else if (dose > 0) {
            if (costCalc) costCalc.textContent = `${dose} ${unit} (Harga belum diset)`;
            if (costAmount) costAmount.textContent = 'Rp —';
            return 0;
        } else {
            const formattedRate = rate > 0 ? `Rp ${Math.round(rate).toLocaleString('id-ID')}/${unit}` : 'Rp 0';
            if (costCalc) costCalc.textContent = `0 ${unit} × ${formattedRate}`;
            if (costAmount) costAmount.textContent = 'Rp 0';
            return 0;
        }
    }

    function stepTank(prefix, step) {
        const input = document.getElementById(`${prefix}-jumlah-tank`);
        if (!input) return;
        let val = parseFloat(input.value) || 1;
        val = Math.max(0.1, Math.round((val + step) * 10) / 10);
        input.value = val;
        updateObatSummary(prefix);
    }

    function setTank(prefix, val) {
        const input = document.getElementById(`${prefix}-jumlah-tank`);
        if (!input) return;
        input.value = val;
        updateObatSummary(prefix);
    }

    function removeObatRow(btn, prefix) {
        const row = btn.closest('.obat-row');
        if (row) {
            row.remove();
            updateObatSummary(prefix);
        }
    }

    function updateObatSummary(prefix) {
        const container = document.getElementById(`${prefix}-obat-rows-container`);
        const totalBar = document.getElementById(`${prefix}-obat-total-bar`);
        const subtotalDisplay = document.getElementById(`${prefix}-obat-subtotal-display`);
        const tankDisplay = document.getElementById(`${prefix}-obat-tank-display`);
        const totalDisplay = document.getElementById(`${prefix}-obat-total-display`);
        const countBadge = document.getElementById(`${prefix}-obat-count-badge`);
        const breakdownText = document.getElementById(`${prefix}-obat-calc-breakdown`);
        const hiddenAplikasiObat = document.getElementById(`${prefix}-aplikasi-obat`);
        const hiddenTotalBiaya = document.getElementById(`${prefix}-total-biaya-obat`);
        const tankInput = document.getElementById(`${prefix}-jumlah-tank`);

        if (!container) return;

        let jumlahTank = parseFloat(tankInput?.value || '1');
        if (isNaN(jumlahTank) || jumlahTank <= 0) {
            jumlahTank = 1;
        }

        const rows = container.querySelectorAll('.obat-row');
        let subtotalPerTank = 0;
        const summaryItems = [];

        rows.forEach(r => {
            const searchInput = r.querySelector('.obat-search-input');
            const nameInput = r.querySelector('.obat-name-val');
            const unitInput = r.querySelector('.obat-unit-val');
            const rateInput = r.querySelector('.obat-rate-val');
            const doseInput = r.querySelector('.dosis-input');

            const medName = (nameInput?.value || searchInput?.value || '').trim();
            if (medName) {
                const unit = unitInput?.value || 'gram';
                const dose = parseFloat(doseInput?.value || '0');
                const rate = parseFloat(rateInput?.value || '0');

                let itemCostPerTank = 0;
                if (dose > 0 && rate > 0) {
                    itemCostPerTank = Math.round(dose * rate);
                    subtotalPerTank += itemCostPerTank;
                }

                const itemGrandTotal = Math.round(itemCostPerTank * jumlahTank);

                if (dose > 0) {
                    if (itemGrandTotal > 0) {
                        summaryItems.push(`${medName}: ${dose} ${unit} (Rp ${itemGrandTotal.toLocaleString('id-ID')})`);
                    } else {
                        summaryItems.push(`${medName}: ${dose} ${unit}`);
                    }
                } else {
                    summaryItems.push(`${medName}`);
                }
            }
        });

        const grandTotal = Math.round(subtotalPerTank * jumlahTank);
        const fmtTank = (Math.round(jumlahTank * 100) / 100).toString().replace('.', ',');

        if (subtotalDisplay) subtotalDisplay.textContent = 'Rp ' + subtotalPerTank.toLocaleString('id-ID');
        if (tankDisplay) tankDisplay.textContent = `x ${fmtTank} Tank`;
        if (totalDisplay) totalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        if (countBadge) countBadge.textContent = `${summaryItems.length} Obat`;

        if (hiddenTotalBiaya) {
            hiddenTotalBiaya.value = grandTotal;
        }

        if (hiddenAplikasiObat) {
            hiddenAplikasiObat.value = summaryItems.join(', ');
        }
    }

    function parseObatItems(rawText) {
        if (!rawText || !rawText.trim()) return [];
        const parts = rawText.split(',').map(s => s.trim()).filter(Boolean);
        const parsed = [];

        parts.forEach(part => {
            let matchedMed = null;
            let foundDose = '';

            for (const m of ALL_MEDICINES) {
                if (part.toLowerCase().includes(m.nama.toLowerCase())) {
                    matchedMed = m;
                    break;
                }
            }

            const doseMatch = part.match(/(\d+(?:[.,]\d+)?)\s*(?:gram|gr|g|ml|liter|kg|sachet|sendok)/i);
            if (doseMatch) {
                foundDose = doseMatch[1].replace(',', '.');
            }

            if (matchedMed) {
                parsed.push({
                    medId: matchedMed.id,
                    medName: matchedMed.nama,
                    dose: foundDose
                });
            } else {
                let customName = part;
                if (part.includes(':')) {
                    customName = part.split(':')[0].trim();
                } else if (doseMatch) {
                    customName = part.substring(0, doseMatch.index).trim();
                }
                parsed.push({
                    medId: '',
                    medName: customName || part,
                    dose: foundDose
                });
            }
        });

        return parsed;
    }

    function initObatBuilder(prefix, existingText = '', initialTank = 1) {
        const container = document.getElementById(`${prefix}-obat-rows-container`);
        const hiddenAplikasiObat = document.getElementById(`${prefix}-aplikasi-obat`);
        const tankInput = document.getElementById(`${prefix}-jumlah-tank`);

        if (tankInput) tankInput.value = initialTank || 1;

        if (!container || !hiddenAplikasiObat) return;

        container.innerHTML = '';
        hiddenAplikasiObat.value = existingText || '';

        if (existingText && existingText.trim()) {
            const parsed = parseObatItems(existingText);
            if (parsed.length > 0) {
                parsed.forEach(item => {
                    addObatRow(prefix, item.medId, item.dose, item.medName);
                });
            } else {
                addObatRow(prefix, '', '', existingText.trim());
            }
        } else {
            addObatRow(prefix, '', '', '');
        }

        updateObatSummary(prefix);
    }

    // Global listener to close suggestions on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.obat-combobox-wrapper')) {
            document.querySelectorAll('.obat-dropdown-results').forEach(el => el.classList.add('hidden'));
        }
    });

    // ── Manajemen Upload Foto Tambah Kegiatan ──
    let addSelectedFiles = [];

    function handleAddPhotoChange(event) {
        const files = Array.from(event.target.files);
        if (addSelectedFiles.length + files.length > 5) {
            const msg = `Maksimal 5 foto per kegiatan. Anda telah memilih ${addSelectedFiles.length} foto.`;
            if (window.AgriSwal) {
                window.AgriSwal.toastError(msg);
            } else if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: msg });
            }
            return;
        }
        for (const f of files) {
            if (addSelectedFiles.length < 5) {
                addSelectedFiles.push(f);
            }
        }
        syncAddFileInput();
        renderAddPhotoPreviews();
    }

    function removeAddPhoto(index) {
        addSelectedFiles.splice(index, 1);
        syncAddFileInput();
        renderAddPhotoPreviews();
    }

    function syncAddFileInput() {
        const dt = new DataTransfer();
        addSelectedFiles.forEach(f => dt.items.add(f));
        const input = document.getElementById('add-foto-kegiatan');
        if (input) input.files = dt.files;
    }

    function renderAddPhotoPreviews() {
        const grid = document.getElementById('add-photo-preview-grid');
        const counter = document.getElementById('add-photo-counter');
        if (counter) counter.textContent = `${addSelectedFiles.length}/5 Foto`;
        if (!grid) return;
        grid.innerHTML = '';
        if (addSelectedFiles.length === 0) {
            grid.classList.add('hidden');
            return;
        }
        grid.classList.remove('hidden');
        addSelectedFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const item = document.createElement('div');
            item.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-2xs';
            item.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover">
                <button type="button" onclick="removeAddPhoto(${idx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs shadow transition-transform hover:scale-110" title="Batal foto ini">
                    &times;
                </button>
            `;
            grid.appendChild(item);
        });
    }

    function openAddActivityModal(targetHst = null, targetDate = null) {
        const hstInput = document.getElementById('add-target-hst');
        const dateInput = document.getElementById('add-tanggal-kegiatan');
        const namaInput = document.getElementById('add-nama-kegiatan');
        const sasaranInput = document.getElementById('add-sasaran');
        const ketInput = document.getElementById('add-keterangan');
        const tankInput = document.getElementById('add-jumlah-tank');

        isAddActivityConfirmed = false;
        const addForm = document.getElementById('form-add-activity');
        if (addForm) {
            addForm.querySelectorAll('input[name="other_crop_ids[]"]').forEach(el => el.remove());
        }

        if (namaInput) namaInput.value = '';
        if (sasaranInput) sasaranInput.value = '';
        if (ketInput) ketInput.value = '';
        if (tankInput) tankInput.value = '1';

        initObatBuilder('add', '', 1);

        // Reset foto
        addSelectedFiles = [];
        syncAddFileInput();
        renderAddPhotoPreviews();

        if (targetHst !== null) {
            hstInput.value = targetHst;
            syncFromHst('add');
        } else if (targetDate) {
            dateInput.value = targetDate;
            syncFromDate('add');
        } else {
            hstInput.value = '{{ $currentHst }}';
            syncFromHst('add');
        }

        openModal('modal-add-activity');
    }

    // ── Manajemen Foto Modal Edit Kegiatan ──
    let currentEditActivityId = null;
    let editExistingPhotos = [];
    let editNewFiles = [];

    function openEditActivityModal(id, nama, targetHst, targetDate, obat, sasaran, ket, existingPhotos = [], jumlahTank = 1, totalBiaya = 0) {
        currentEditActivityId = id;
        editExistingPhotos = Array.isArray(existingPhotos) ? [...existingPhotos] : [];
        editNewFiles = [];

        const form = document.getElementById('form-edit-activity');
        form.action = `/kalender-hst/kegiatan/${id}`;

        document.getElementById('edit-nama-kegiatan').value = nama;
        document.getElementById('edit-target-hst').value = targetHst;
        document.getElementById('edit-tanggal-kegiatan').value = targetDate;
        document.getElementById('edit-sasaran').value = sasaran || '';
        document.getElementById('edit-keterangan').value = ket || '';

        const tankInput = document.getElementById('edit-jumlah-tank');
        if (tankInput) tankInput.value = jumlahTank || 1;

        initObatBuilder('edit', obat || '', jumlahTank || 1);

        const deletedContainer = document.getElementById('edit-deleted-photos-container');
        if (deletedContainer) deletedContainer.innerHTML = '';

        const fileInput = document.getElementById('edit-foto-kegiatan');
        if (fileInput) fileInput.value = '';

        renderEditPhotosState();
        openModal('modal-edit-activity');
    }

    function renderEditPhotosState() {
        const existingSection = document.getElementById('edit-existing-photos-section');
        const existingGrid = document.getElementById('edit-existing-photos-grid');
        const quotaBadge = document.getElementById('edit-photo-quota-badge');
        const uploadBox = document.getElementById('edit-upload-box');
        const uploadText = document.getElementById('edit-upload-label-text');

        const totalCurrent = editExistingPhotos.length + editNewFiles.length;
        if (quotaBadge) {
            quotaBadge.textContent = `${totalCurrent}/5 Foto`;
        }

        if (uploadText) {
            const remaining = 5 - totalCurrent;
            uploadText.textContent = remaining > 0 ? `+ Tambah Foto Baru (Sisa: ${remaining})` : 'Kuota foto penuh (Maks. 5)';
        }

        if (uploadBox) {
            if (editExistingPhotos.length + editNewFiles.length >= 5) {
                uploadBox.classList.add('opacity-50', 'pointer-events-none');
            } else {
                uploadBox.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        // Render foto yang sudah ada
        if (existingGrid && existingSection) {
            existingGrid.innerHTML = '';
            if (editExistingPhotos.length > 0) {
                existingSection.classList.remove('hidden');
                editExistingPhotos.forEach((photoUrl, idx) => {
                    const item = document.createElement('div');
                    item.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-2xs';
                    item.innerHTML = `
                        <img src="${photoUrl}" class="w-full h-full object-cover">
                        <button type="button"
                                onclick="handleDeleteExistingPhoto('${encodeURIComponent(photoUrl)}', ${idx})"
                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs shadow-md transition-all hover:scale-110"
                                title="Hapus foto fisik ini secara permanen">
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    `;
                    existingGrid.appendChild(item);
                });
            } else {
                existingSection.classList.add('hidden');
            }
        }

        renderEditNewPhotoPreviews();
    }

    function handleEditPhotoChange(event) {
        const files = Array.from(event.target.files);
        const totalAllowed = 5 - editExistingPhotos.length;
        if (editNewFiles.length + files.length > totalAllowed) {
            const msg = `Maksimal 5 foto per kegiatan. Sisa slot yang dapat ditambahkan: ${totalAllowed - editNewFiles.length} foto.`;
            if (window.AgriSwal) {
                window.AgriSwal.toastError(msg);
            } else if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: msg });
            }
            return;
        }
        for (const f of files) {
            if (editExistingPhotos.length + editNewFiles.length < 5) {
                editNewFiles.push(f);
            }
        }
        syncEditFileInput();
        renderEditPhotosState();
    }

    function removeEditNewPhoto(index) {
        editNewFiles.splice(index, 1);
        syncEditFileInput();
        renderEditPhotosState();
    }

    function syncEditFileInput() {
        const dt = new DataTransfer();
        editNewFiles.forEach(f => dt.items.add(f));
        const input = document.getElementById('edit-foto-kegiatan');
        if (input) input.files = dt.files;
    }

    function renderEditNewPhotoPreviews() {
        const grid = document.getElementById('edit-new-photo-preview-grid');
        if (!grid) return;
        grid.innerHTML = '';
        if (editNewFiles.length === 0) {
            grid.classList.add('hidden');
            return;
        }
        grid.classList.remove('hidden');
        editNewFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const item = document.createElement('div');
            item.className = 'relative group aspect-square rounded-xl overflow-hidden border border-emerald-300 bg-gray-100 shadow-2xs';
            item.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover">
                <button type="button" onclick="removeEditNewPhoto(${idx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs shadow transition-transform hover:scale-110" title="Batal tambah foto">
                    &times;
                </button>
            `;
            grid.appendChild(item);
        });
    }

    async function handleDeleteExistingPhoto(encodedUrl, index) {
        const photoUrl = decodeURIComponent(encodedUrl);

        const executeDelete = async () => {
            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                const res = await fetch(`/kalender-hst/kegiatan/${currentEditActivityId}/foto`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ target: photoUrl })
                });
                const data = await res.json();
                if (data.success) {
                    editExistingPhotos.splice(index, 1);
                    renderEditPhotosState();
                    if (window.AgriSwal && typeof window.AgriSwal.toastSuccess === 'function') {
                        window.AgriSwal.toastSuccess('Foto berhasil dihapus.');
                    }
                    if (window.AgriSwal) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus file foto fisik.');
                    } else if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: data.message || 'Gagal menghapus file foto fisik.',
                            customClass: { popup: 'agri-swal-popup' },
                            buttonsStyling: false,
                            confirmButtonText: 'Tutup'
                        });
                    }
                }
            } catch (e) {
                // Fallback: tandai di hidden input untuk dihapus saat form disubmit
                const deletedContainer = document.getElementById('edit-deleted-photos-container');
                if (deletedContainer) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'deleted_photos[]';
                    hidden.value = photoUrl;
                    deletedContainer.appendChild(hidden);
                }
                editExistingPhotos.splice(index, 1);
                renderEditPhotosState();
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete('Hapus Foto Dokumentasi?', 'Foto ini', executeDelete);
        } else if (window.Swal) {
            Swal.fire({
                title: 'Hapus Foto Dokumentasi?',
                html: `Apakah Anda yakin ingin menghapus foto ini?<br><span class="agri-swal-subtitle">File fisik di server / Cloudinary akan langsung dimusnahkan.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'agri-swal-popup',
                    title: 'agri-swal-title',
                    htmlContainer: 'agri-swal-html',
                    confirmButton: 'agri-swal-btn-danger',
                    cancelButton: 'agri-swal-btn-cancel',
                    actions: 'agri-swal-actions',
                    icon: 'agri-swal-icon-warning'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm('Hapus foto ini? File fisik di server/Cloudinary juga akan langsung dihapus permanen.')) {
                executeDelete();
            }
        }
    }

    // ── Fungsi Lightbox Viewer ──
    let currentLightboxImages = [];
    let currentLightboxIndex = 0;
    let currentLightboxActivityId = null;
    let hasPhotosChangedInLightbox = false;

    function openImageLightbox(urls, initialIndex = 0, title = 'Dokumentasi Kegiatan', activityId = null) {
        if (!urls || urls.length === 0) return;
        currentLightboxImages = Array.isArray(urls) ? [...urls] : [urls];
        currentLightboxIndex = initialIndex;
        currentLightboxActivityId = activityId;
        hasPhotosChangedInLightbox = false;

        const modal = document.getElementById('modal-image-lightbox');
        const titleEl = document.getElementById('lightbox-title');
        const delBtn = document.getElementById('lightbox-btn-delete');
        if (titleEl) titleEl.textContent = title;
        if (delBtn) {
            delBtn.style.display = activityId ? 'inline-flex' : 'none';
        }

        updateLightboxView();
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        document.addEventListener('keydown', handleLightboxKeydown);
    }

    function closeImageLightbox() {
        const modal = document.getElementById('modal-image-lightbox');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.removeEventListener('keydown', handleLightboxKeydown);

        if (hasPhotosChangedInLightbox) {
            window.location.reload();
        }
    }

    function updateLightboxView() {
        const imgEl = document.getElementById('lightbox-img');
        const counterEl = document.getElementById('lightbox-counter');
        const downloadLink = document.getElementById('lightbox-download-link');
        const prevBtn = document.getElementById('lightbox-btn-prev');
        const nextBtn = document.getElementById('lightbox-btn-next');

        const currentItem = currentLightboxImages[currentLightboxIndex];
        const currentUrl = (typeof currentItem === 'object' && currentItem !== null) ? currentItem.url : currentItem;
        const currentTitle = (typeof currentItem === 'object' && currentItem !== null && currentItem.name) ? currentItem.name : null;

        if (typeof currentItem === 'object' && currentItem !== null && currentItem.activityId) {
            currentLightboxActivityId = currentItem.activityId;
        }

        if (currentTitle) {
            const titleEl = document.getElementById('lightbox-title');
            if (titleEl) titleEl.textContent = currentTitle;
        }

        if (imgEl) imgEl.src = currentUrl;
        if (downloadLink) downloadLink.href = currentUrl;
        if (counterEl) {
            counterEl.textContent = `Foto ${currentLightboxIndex + 1} dari ${currentLightboxImages.length}`;
        }

        if (prevBtn) {
            if (currentLightboxImages.length > 1) {
                prevBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden');
            }
        }
        if (nextBtn) {
            if (currentLightboxImages.length > 1) {
                nextBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.add('hidden');
            }
        }
    }

    function prevLightboxImage() {
        if (currentLightboxImages.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex - 1 + currentLightboxImages.length) % currentLightboxImages.length;
        updateLightboxView();
    }

    function nextLightboxImage() {
        if (currentLightboxImages.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex + 1) % currentLightboxImages.length;
        updateLightboxView();
    }

    async function deleteCurrentLightboxPhoto() {
        if (!currentLightboxActivityId || currentLightboxImages.length === 0) return;
        const currentItem = currentLightboxImages[currentLightboxIndex];
        const currentUrl = (typeof currentItem === 'object' && currentItem !== null) ? currentItem.url : currentItem;

        const executeDelete = async () => {
            const deleteBtn = document.getElementById('lightbox-btn-delete');
            const originalHtml = deleteBtn ? deleteBtn.innerHTML : '';
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menghapus...</span>
                `;
            }

            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                const res = await fetch(`/kalender-hst/kegiatan/${currentLightboxActivityId}/foto`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ target: currentUrl })
                });

                const data = await res.json();
                if (data.success) {
                    if (window.AgriSwal && typeof window.AgriSwal.toastSuccess === 'function') {
                        window.AgriSwal.toastSuccess('Foto berhasil dihapus.');
                    }
                    hasPhotosChangedInLightbox = true;

                    // Hapus foto yang dihapus dari array preview lightbox
                    currentLightboxImages.splice(currentLightboxIndex, 1);

                    // Jika masih ada foto tersisa (awalnya lebih dari 1 foto): tetap di preview
                    if (currentLightboxImages.length > 0) {
                        if (currentLightboxIndex >= currentLightboxImages.length) {
                            currentLightboxIndex = currentLightboxImages.length - 1;
                        }
                        updateLightboxView();

                        if (deleteBtn) {
                            deleteBtn.disabled = false;
                            deleteBtn.innerHTML = originalHtml;
                        }
                    } else {
                        // Jika foto habis (tadi hanya 1 foto lalu dihapus): kembali ke tabel
                        closeImageLightbox();
                        setTimeout(() => {
                            window.location.reload();
                        }, 400);
                    }
                } else {
                    if (window.AgriSwal) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus file foto fisik.');
                    } else if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: data.message || 'Gagal menghapus file foto fisik.',
                            customClass: { popup: 'agri-swal-popup' },
                            buttonsStyling: false,
                            confirmButtonText: 'Tutup'
                        });
                    }
                    if (deleteBtn) {
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                if (window.AgriSwal) {
                    window.AgriSwal.toastError('Terjadi kesalahan koneksi saat menghapus file foto.');
                } else if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan koneksi saat menghapus file foto.',
                        customClass: { popup: 'agri-swal-popup' },
                        buttonsStyling: false,
                        confirmButtonText: 'Tutup'
                    });
                }
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalHtml;
                }
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete('Hapus Foto Dokumentasi?', 'Foto ini', executeDelete);
        } else if (window.Swal) {
            Swal.fire({
                title: 'Hapus Foto Dokumentasi?',
                html: `Apakah Anda yakin ingin menghapus foto ini?<br><span class="agri-swal-subtitle">File fisik di server / Cloudinary akan langsung dimusnahkan.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'agri-swal-popup',
                    title: 'agri-swal-title',
                    htmlContainer: 'agri-swal-html',
                    confirmButton: 'agri-swal-btn-danger',
                    cancelButton: 'agri-swal-btn-cancel',
                    actions: 'agri-swal-actions',
                    icon: 'agri-swal-icon-warning'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm('Hapus foto ini? File fisik di server/Cloudinary akan langsung dihapus permanen.')) {
                executeDelete();
            }
        }
    }

    function handleLightboxKeydown(e) {
        if (e.key === 'Escape') closeImageLightbox();
        if (e.key === 'ArrowLeft') prevLightboxImage();
        if (e.key === 'ArrowRight') nextLightboxImage();
    }

    function openHarvestModal(cropId, cropName, plantDate, nextNumber) {
        const heading = document.getElementById('harvest-modal-heading');
        if (heading && nextNumber) {
            heading.textContent = `Catat Panen ke-${nextNumber}`;
        }
        const submitBtn = document.getElementById('harvest-modal-submit-btn');
        if (submitBtn && nextNumber) {
            submitBtn.textContent = `Simpan Panen ke-${nextNumber}`;
        }
        const totalInput = document.getElementById('harvest-total');
        if (totalInput) totalInput.value = '';
        const priceInput = document.getElementById('harvest-price');
        if (priceInput) priceInput.value = '';
        const kotorInput = document.getElementById('harvest-total-kotor');
        if (kotorInput) kotorInput.value = '';
        const notesInput = document.getElementById('harvest-notes');
        if (notesInput) notesInput.value = '';
        openModal('modal-harvest');
    }

    function openEndCropModal(cropId, cropName, plantDate) {
        const reasonInput = document.getElementById('end-reason');
        if (reasonInput) reasonInput.value = '';
        const notesInput = document.getElementById('end-notes');
        if (notesInput) notesInput.value = '';
        openModal('modal-end-crop');
    }

    function openEditCropModal(cropId, nama, varietas, populasi, tanggalTanam, catatan) {
        document.getElementById('edit-crop-nama').value = nama;
        document.getElementById('edit-crop-varietas').value = varietas || '';
        document.getElementById('edit-crop-populasi').value = populasi || '';
        document.getElementById('edit-crop-tanggal').value = tanggalTanam;
        document.getElementById('edit-crop-catatan').value = catatan || '';
        openModal('modal-edit-crop');
    }

    // Filter Table Rows
    function filterTable(type) {
        document.querySelectorAll('.filter-chip').forEach(btn => {
            btn.classList.remove('bg-gray-900', 'text-white');
            btn.classList.add('text-text-secondary');
        });

        const activeBtn = document.getElementById(`filter-btn-${type}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-text-secondary');
            activeBtn.classList.add('bg-gray-900', 'text-white');
        }

        const rows = document.querySelectorAll('.hst-table-row');
        rows.forEach(row => {
            if (type === 'all') {
                row.classList.remove('hidden');
            } else if (type === 'today') {
                if (row.dataset.isToday === '1') {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            } else if (type === 'tomorrow') {
                if (row.dataset.isTomorrow === '1') {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            } else if (type === 'has-activity') {
                if (row.dataset.hasActivity === '1') {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            } else if (type === 'pending') {
                if (row.dataset.hasPending === '1') {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            }
        });
    }

    // Jump to HST
    function jumpToHst() {
        const input = document.getElementById('jump-hst');
        if (!input || input.value === '') return;

        const hstVal = parseInt(input.value, 10);
        const targetRow = document.getElementById(`hst-row-${hstVal}`);
        if (targetRow) {
            filterTable('all');
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetRow.classList.add('bg-amber-100/80', 'ring-2', 'ring-amber-400');
            setTimeout(() => {
                targetRow.classList.remove('bg-amber-100/80', 'ring-2', 'ring-amber-400');
            }, 2500);
        } else {
            if (confirm(`Baris HST ${hstVal} belum masuk dalam rentang tampilan (saat ini sampai HST {{ $maxRowHst }}).\n\nMuat tabel sampai HST ${hstVal + 5}?`)) {
                window.location.href = `/kalender-hst/tanaman/{{ $crop->id }}?limit_hst=${hstVal + 5}#hst-row-${hstVal}`;
            }
        }
    }

    function bindPriceAutoDot(inputEl) {
        if (!inputEl) return;
        const formatNumberDot = (val) => {
            if (!val) return '';
            const digits = val.toString().replace(/[^0-9]/g, '');
            if (!digits) return '';
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        if (inputEl.value) {
            inputEl.value = formatNumberDot(inputEl.value);
        }

        inputEl.addEventListener('input', () => {
            const cursorPosition = inputEl.selectionStart;
            const prevVal = inputEl.value;
            const digitsBeforeCursor = prevVal.slice(0, cursorPosition).replace(/\D/g, '').length;

            const formatted = formatNumberDot(prevVal);
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
            if (digitCount < digitsBeforeCursor) newCursorPos = formatted.length;
            inputEl.setSelectionRange(newCursorPos, newCursorPos);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const totalInput = document.getElementById('harvest-total');
        const priceInput = document.getElementById('harvest-price');
        const kotorInput = document.getElementById('harvest-total-kotor');

        bindPriceAutoDot(priceInput);
        bindPriceAutoDot(kotorInput);

        const autoCalcGross = () => {
            if (!totalInput || !priceInput || !kotorInput) return;
            const totalVal = totalInput.value.trim().replace(',', '.');
            const match = totalVal.match(/[0-9]+(?:\.[0-9]+)?/);
            const qty = match ? parseFloat(match[0]) : 0;

            const priceDigits = priceInput.value.replace(/[^0-9]/g, '');
            const unitPrice = priceDigits ? parseFloat(priceDigits) : 0;

            if (qty > 0 && unitPrice > 0) {
                const gross = Math.round(qty * unitPrice);
                kotorInput.value = gross.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        };

        if (totalInput && priceInput) {
            totalInput.addEventListener('input', autoCalcGross);
            priceInput.addEventListener('input', autoCalcGross);
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'obat') {
            switchDetailTab('obat');
        }
    });

    // Sub-Tab detail switching (Log Kalender HST vs Rangkuman Obat)
    function switchDetailTab(tabKey) {
        const paneKalender = document.getElementById('detail-pane-kalender');
        const paneObat = document.getElementById('detail-pane-obat');
        const btnKalender = document.getElementById('btn-detail-tab-kalender');
        const btnObat = document.getElementById('btn-detail-tab-obat');

        if (tabKey === 'obat') {
            if (paneKalender) paneKalender.classList.add('hidden');
            if (paneObat) paneObat.classList.remove('hidden');
            if (btnKalender) {
                btnKalender.className = btnKalender.className
                    .replace(/bg-white text-primary-dark shadow-sm border border-gray-200/, 'text-text-secondary hover:text-text hover:bg-gray-100/60');
            }
            if (btnObat) {
                btnObat.className = btnObat.className
                    .replace('text-text-secondary hover:text-text hover:bg-gray-100/60', 'bg-white text-primary-dark shadow-sm border border-gray-200');
            }
        } else {
            if (paneObat) paneObat.classList.add('hidden');
            if (paneKalender) paneKalender.classList.remove('hidden');
            if (btnObat) {
                btnObat.className = btnObat.className
                    .replace(/bg-white text-primary-dark shadow-sm border border-gray-200/, 'text-text-secondary hover:text-text hover:bg-gray-100/60');
            }
            if (btnKalender) {
                btnKalender.className = btnKalender.className
                    .replace('text-text-secondary hover:text-text hover:bg-gray-100/60', 'bg-white text-primary-dark shadow-sm border border-gray-200');
            }
        }

        const url = new URL(window.location);
        url.searchParams.set('tab', tabKey);
        window.history.replaceState({}, '', url);
    }

    // Toggle collapse/expand section rangkuman obat
    function toggleMedicineSummarySection() {
        const body = document.getElementById('body-rangkuman-obat');
        const label = document.getElementById('label-toggle-med-summary');
        const icon = document.getElementById('icon-toggle-med-summary');
        if (!body) return;
        const isHidden = body.classList.toggle('hidden');
        if (label) label.textContent = isHidden ? 'Buka' : 'Ciutkan';
        if (icon) icon.style.transform = isHidden ? 'rotate(180deg)' : '';
    }

    // Toggle long list of obat items in table
    function toggleObatItems(btn) {
        const container = btn.previousElementSibling;
        if (!container) return;
        const isHidden = container.classList.contains('hidden');
        const count = btn.getAttribute('data-count') || '';
        if (isHidden) {
            container.classList.remove('hidden');
            btn.textContent = '▲ Sembunyikan sebagian';
        } else {
            container.classList.add('hidden');
            btn.textContent = `+ Lihat ${count} obat lainnya...`;
        }
    }

    // Toggle line-clamp for long descriptions in table
    function toggleLineClamp(btn) {
        const textEl = btn.previousElementSibling;
        if (!textEl) return;
        if (textEl.classList.contains('line-clamp-2')) {
            textEl.classList.remove('line-clamp-2');
            btn.textContent = 'Sembunyikan';
        } else {
            textEl.classList.add('line-clamp-2');
            btn.textContent = 'Selengkapnya...';
        }
    }

    // ── Dukungan Offline Kalender HST (IndexedDB & Drafts) ──
    async function fileToBase64(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = () => resolve(null);
            reader.readAsDataURL(file);
        });
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

    function renderDraftActivityInRow(act) {
        const row = document.getElementById('hst-row-' + act.target_hst);
        if (!row) return;

        // Cegah render ganda jika sudah ada
        if (document.getElementById('draft-act-' + act.client_id)) return;

        // Kolom 3: KEGIATAN
        const colKegiatan = row.children[2];
        if (colKegiatan) {
            const draftCard = document.createElement('div');
            draftCard.id = 'draft-act-' + act.client_id;
            draftCard.className = 'mt-2 p-2 rounded-xl bg-amber-50/90 border border-amber-300 text-amber-950 shadow-2xs';
            draftCard.innerHTML = `
                <div class="flex items-center justify-between gap-1 mb-1">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900 border border-amber-300">
                        <svg class="w-3 h-3 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        Draft Offline
                    </span>
                    <button type="button"
                            onclick="deleteOfflineActivityDraft('${act.client_id}', '${escapeHtml(act.nama_kegiatan)}')"
                            class="text-amber-700 hover:text-red-600 p-0.5 text-xs font-bold leading-none cursor-pointer"
                            title="Hapus Draft Offline">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="font-bold text-xs sm:text-sm text-gray-900 break-words">${escapeHtml(act.nama_kegiatan)}</div>
            `;
            colKegiatan.appendChild(draftCard);
        }

        // Kolom 4: APLIKASI OBAT
        if (act.aplikasi_obat) {
            const colObat = row.children[3];
            if (colObat) {
                const draftObat = document.createElement('div');
                draftObat.id = 'draft-obat-' + act.client_id;
                draftObat.className = 'mt-2 p-1.5 rounded-lg bg-amber-50 border border-amber-200 text-xs font-mono text-amber-900';
                let extraBadges = '';
                if (act.jumlah_tank || act.total_biaya_obat) {
                    extraBadges = '<div class="mt-1 flex items-center gap-1 flex-wrap">';
                    if (act.jumlah_tank) extraBadges += `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200"><svg class="w-2.5 h-2.5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>${act.jumlah_tank} Tank</span>`;
                    if (act.total_biaya_obat) extraBadges += `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200"><svg class="w-2.5 h-2.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Rp ${Number(act.total_biaya_obat).toLocaleString('id-ID')}</span>`;
                    extraBadges += '</div>';
                }
                draftObat.innerHTML = `<span class="font-bold text-[10px] uppercase text-amber-800 flex items-center gap-1"><svg class="w-2.5 h-2.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>Draft Obat:</span>${escapeHtml(act.aplikasi_obat)}${extraBadges}`;
                colObat.appendChild(draftObat);
            }
        }

        // Kolom 5: SASARAN
        if (act.sasaran) {
            const colSasaran = row.children[4];
            if (colSasaran) {
                const draftSasaran = document.createElement('div');
                draftSasaran.id = 'draft-sasaran-' + act.client_id;
                draftSasaran.className = 'mt-2 text-xs font-medium text-amber-900';
                draftSasaran.innerHTML = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-200 text-[11px]"><svg class="w-2.5 h-2.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>${escapeHtml(act.sasaran)}</span>`;
                colSasaran.appendChild(draftSasaran);
            }
        }

        // Kolom 6: KETERANGAN
        if (act.keterangan) {
            const colKet = row.children[5];
            if (colKet) {
                const draftKet = document.createElement('div');
                draftKet.id = 'draft-ket-' + act.client_id;
                draftKet.className = 'mt-2 text-xs text-amber-900 italic bg-amber-50/70 p-1.5 rounded-lg border border-amber-200';
                draftKet.innerHTML = escapeHtml(act.keterangan);
                colKet.appendChild(draftKet);
            }
        }

        // Kolom 7: FOTO
        if (act.photos_base64 && act.photos_base64.length > 0) {
            const colFoto = row.children[6];
            if (colFoto) {
                const draftFotoContainer = document.createElement('div');
                draftFotoContainer.id = 'draft-foto-' + act.client_id;
                draftFotoContainer.className = 'mt-1.5 flex items-center justify-center';
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 border border-amber-300 text-amber-900 text-xs font-bold transition-all shadow-2xs cursor-pointer whitespace-nowrap';
                btn.title = `Lihat ${act.photos_base64.length} foto draft offline`;
                btn.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                    </svg>
                    <span>Foto Draft (${act.photos_base64.length})</span>
                `;
                const lightboxItems = act.photos_base64.map(b => ({
                    url: b,
                    name: act.nama_kegiatan + ' (Draft Offline - HST ' + act.target_hst + ')'
                }));
                btn.onclick = () => openImageLightbox(lightboxItems, 0, act.nama_kegiatan + ' (Draft Offline)');
                draftFotoContainer.appendChild(btn);
                colFoto.appendChild(draftFotoContainer);
            }
        }
    }

    window.deleteOfflineActivityDraft = function(clientId, name) {
        if (window.AgriSwal) {
            AgriSwal.confirmDelete('Hapus Draft Offline?', name, async () => {
                await window.AgriOfflineStore.deleteCropActivityOffline(null, clientId);
                ['draft-act-', 'draft-obat-', 'draft-sasaran-', 'draft-ket-', 'draft-foto-'].forEach(prefix => {
                    const el = document.getElementById(prefix + clientId);
                    if (el) el.remove();
                });
                AgriSwal.toastSuccess(`Draft "${name}" dihapus.`);
                const p = await window.AgriOfflineStore.getPendingCount();
                if (window.updateConnectionBadges) window.updateConnectionBadges('offline', p);
            });
        }
    };

    document.addEventListener('DOMContentLoaded', async () => {
        // Render draft offline activities for this crop
        if (window.AgriOfflineStore) {
            try {
                const drafts = await window.AgriOfflineStore.getOfflineCropActivities({{ $crop->id }});
                drafts.forEach(act => renderDraftActivityInRow(act));
            } catch (err) {
                console.warn('Gagal memuat draft offline:', err);
            }
        }

        // Intercept form tambah kegiatan: SweetAlert konfirmasi tanaman & offline handling
        const addForm = document.getElementById('form-add-activity');
        if (addForm) {
            addForm.addEventListener('submit', async (e) => {
                // 1. Tahap Konfirmasi Pilihan Tanaman (Sebelum Simpan)
                if (!isAddActivityConfirmed) {
                    e.preventDefault();

                    if (!addForm.checkValidity()) {
                        addForm.reportValidity();
                        return;
                    }

                    const namaKegiatan = document.getElementById('add-nama-kegiatan')?.value || 'Kegiatan';

                    // Jika tidak ada tanaman aktif lain di kebun
                    if (!OTHER_ACTIVE_CROPS || OTHER_ACTIVE_CROPS.length === 0) {
                        isAddActivityConfirmed = true;
                        addForm.requestSubmit();
                        return;
                    }

                    // Susun tampilan pilihan tanaman dengan SweetAlert
                    let cropsListHtml = `
                        <div class="mt-1 text-left">
                            <p class="agri-swal-subtitle text-xs text-gray-600 leading-relaxed mb-3 text-center sm:text-left">
                                Apakah kegiatan <strong class="text-gray-900 font-semibold">"${escapeHtml(namaKegiatan)}"</strong> ini juga ingin dicatat untuk tanaman aktif lainnya?
                            </p>

                            <!-- Tanaman Saat Ini (Terkunci) -->
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 mb-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-xl leading-none shrink-0">{{ $crop->emoji }}</span>
                                    <div class="min-w-0 truncate">
                                        <div class="text-xs font-bold text-emerald-950 truncate">{{ $crop->nama_tanaman }}</div>
                                        <div class="text-[10px] text-emerald-700 font-medium">{{ $crop->varietas ? $crop->varietas . ' • ' : '' }}HST {{ $currentHst }}</div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-200/70 px-2 py-0.5 rounded-md shrink-0 ml-2">
                                    Tanaman Ini
                                </span>
                            </div>

                            <!-- Opsi Tanaman Lainnya -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Pilih Tanaman Tambahan:
                                </label>
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                    `;

                    OTHER_ACTIVE_CROPS.forEach(c => {
                        cropsListHtml += `
                            <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/40 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/50 cursor-pointer transition-all group select-none">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="checkbox" 
                                           class="swal-crop-checkbox w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300 cursor-pointer accent-emerald-600" 
                                           value="${c.id}" 
                                           id="swal-crop-${c.id}">
                                    <div class="min-w-0 truncate">
                                        <div class="text-xs font-bold text-gray-900 group-hover:text-emerald-950 truncate">${c.emoji || '🌱'} ${escapeHtml(c.nama_tanaman)}</div>
                                        <div class="text-[10px] text-gray-500 font-medium">${c.varietas ? escapeHtml(c.varietas) + ' • ' : ''}HST ${c.current_hst}</div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium group-hover:text-emerald-700 shrink-0 ml-2">Terapkan</span>
                            </label>
                        `;
                    });

                    cropsListHtml += `
                                </div>
                            </div>
                        </div>
                    `;

                    if (window.Swal) {
                        Swal.fire({
                            title: 'Terapkan Kegiatan Ini?',
                            html: cropsListHtml,
                            icon: 'question',
                            iconColor: '#059669',
                            showCancelButton: true,
                            showDenyButton: true,
                            confirmButtonText: 'Terapkan ke Pilihan',
                            denyButtonText: 'Hanya Ini',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            focusConfirm: false,
                            buttonsStyling: false,
                            customClass: {
                                popup: 'agri-swal-popup agri-swal-popup-wide',
                                title: 'agri-swal-title',
                                htmlContainer: 'agri-swal-html',
                                actions: 'agri-swal-actions',
                                confirmButton: 'agri-swal-btn-primary',
                                denyButton: 'agri-swal-btn-secondary',
                                cancelButton: 'agri-swal-btn-cancel',
                                icon: 'agri-swal-icon-question'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Terapkan ke pilihan: kumpulkan ID tanaman lain yang dicentang
                                const checkedBoxes = document.querySelectorAll('.swal-crop-checkbox:checked');
                                const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
                                
                                addForm.querySelectorAll('input[name="other_crop_ids[]"]').forEach(el => el.remove());
                                selectedIds.forEach(id => {
                                    const inp = document.createElement('input');
                                    inp.type = 'hidden';
                                    inp.name = 'other_crop_ids[]';
                                    inp.value = id;
                                    addForm.appendChild(inp);
                                });

                                isAddActivityConfirmed = true;
                                addForm.requestSubmit();
                            } else if (result.isDenied) {
                                // Hanya Ini: simpan hanya untuk tanaman saat ini
                                addForm.querySelectorAll('input[name="other_crop_ids[]"]').forEach(el => el.remove());
                                isAddActivityConfirmed = true;
                                addForm.requestSubmit();
                            }
                        });
                    } else {
                        isAddActivityConfirmed = true;
                        addForm.requestSubmit();
                    }

                    return;
                }

                // 2. Tahap Eksekusi Simpan (Reset flag untuk aksi berikutnya)
                isAddActivityConfirmed = false;

                if (!navigator.onLine) {
                    e.preventDefault();
                    const submitBtn = addForm.querySelector('button[type="submit"]');
                    const origBtnText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span>Menyimpan ke HP...</span>';
                    }

                    try {
                        const fd = new FormData(addForm);
                        const photosBase64 = [];
                        if (addSelectedFiles && addSelectedFiles.length > 0) {
                            for (const file of addSelectedFiles) {
                                const b64 = await fileToBase64(file);
                                if (b64) photosBase64.push(b64);
                            }
                        }

                        const hstVal = parseInt(fd.get('target_hst') || '{{ $currentHst }}', 10);
                        const data = {
                            nama_kegiatan: fd.get('nama_kegiatan') || '',
                            target_hst: hstVal,
                            tanggal_kegiatan: fd.get('tanggal_kegiatan') || '',
                            aplikasi_obat: fd.get('aplikasi_obat') || null,
                            jumlah_tank: fd.get('jumlah_tank') ? parseFloat(fd.get('jumlah_tank')) : 1.0,
                            total_biaya_obat: fd.get('total_biaya_obat') ? parseFloat(fd.get('total_biaya_obat')) : null,
                            sasaran: fd.get('sasaran') || null,
                            keterangan: fd.get('keterangan') || null,
                            photos_base64: photosBase64
                        };

                        const record = await window.AgriOfflineStore.addCropActivityOffline({{ $crop->id }}, data);
                        renderDraftActivityInRow(record);

                        // Simpan juga untuk tanaman lain jika ada
                        const otherCropIds = Array.from(fd.getAll('other_crop_ids[]'));
                        if (otherCropIds.length > 0) {
                            for (const otherId of otherCropIds) {
                                await window.AgriOfflineStore.addCropActivityOffline(parseInt(otherId, 10), data);
                            }
                        }

                        // Reset input form
                        addSelectedFiles = [];
                        syncAddFileInput();
                        renderAddPhotoPreviews();
                        addForm.reset();
                        addForm.querySelectorAll('input[name="other_crop_ids[]"]').forEach(el => el.remove());
                        closeModal('modal-add-activity');

                        if (window.AgriSwal) {
                            AgriSwal.toastSuccess('Kegiatan berhasil disimpan di HP (Mode Offline). Akan otomatis diunggah saat online.');
                        }
                        const pendingCount = await window.AgriOfflineStore.getPendingCount();
                        if (window.updateConnectionBadges) window.updateConnectionBadges('offline', pendingCount);
                    } catch (err) {
                        console.error('Offline save error:', err);
                        if (window.AgriSwal) {
                            AgriSwal.toastError('Gagal menyimpan draft offline: ' + err.message);
                        } else if (window.Swal) {
                            Swal.fire({ icon: 'error', title: 'Gagal Simpan', text: err.message });
                        }
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origBtnText;
                        }
                    }
                }
            });
        }

        // Intercept form catat panen saat offline
        const harvestForm = document.getElementById('form-harvest');
        if (harvestForm) {
            harvestForm.addEventListener('submit', async (e) => {
                if (!navigator.onLine) {
                    e.preventDefault();
                    try {
                        const fd = new FormData(harvestForm);
                        const totalGrossDigits = (fd.get('total_harga') || '').toString().replace(/[^0-9]/g, '');
                        const data = {
                            tanggal_panen: fd.get('tanggal_panen') || '',
                            total_panen: fd.get('total_panen') || null,
                            harga_panen: fd.get('harga_panen') || null,
                            total_harga: totalGrossDigits ? parseInt(totalGrossDigits, 10) : null,
                            catatan: fd.get('catatan') || null
                        };

                        const tx = await window.AgriOfflineStore.getTransaction(['sync_queue'], 'readwrite');
                        tx.objectStore('sync_queue').add({
                            type: 'crop_activity',
                            action: 'record_harvest',
                            crop_id: {{ $crop->id }},
                            data: data,
                            timestamp: Date.now()
                        });
                        await new Promise(r => { tx.oncomplete = r; });

                        closeModal('modal-harvest');
                        harvestForm.reset();

                        if (window.AgriSwal) {
                            AgriSwal.toastSuccess('Catatan panen disimpan di HP (Mode Offline). Akan otomatis diunggah saat online.');
                        }
                        const pendingCount = await window.AgriOfflineStore.getPendingCount();
                        if (window.updateConnectionBadges) window.updateConnectionBadges('offline', pendingCount);
                    } catch (err) {
                        console.error('Harvest offline error:', err);
                        if (window.AgriSwal) {
                            AgriSwal.toastError('Gagal menyimpan panen offline: ' + err.message);
                        } else if (window.Swal) {
                            Swal.fire({ icon: 'error', title: 'Gagal Simpan Panen', text: err.message });
                        }
                    }
                }
            });
        }

        // Pre-cache otomatis foto Cloudinary tanaman ini di latar belakang saat masih online
        @php
            $existingPhotoUrls = [];
            foreach ($tableRows as $r) {
                if ($r['activities']->isNotEmpty()) {
                    foreach ($r['activities'] as $a) {
                        if (!empty($a->foto_urls)) {
                            foreach ($a->foto_urls as $u) {
                                $existingPhotoUrls[] = $u;
                            }
                        }
                    }
                }
            }
        @endphp
        if (navigator.onLine && 'caches' in window) {
            const serverPhotos = @json($existingPhotoUrls);
            if (Array.isArray(serverPhotos) && serverPhotos.length > 0) {
                serverPhotos.forEach(url => {
                    const preloadImg = new Image();
                    preloadImg.src = url;
                });
            }
        }
    });
</script>
@endsection
