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
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            🌾 {{ $crop->harvest_count }}x Panen
                        </span>
                        @if ($crop->total_pendapatan_kotor > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-emerald-600 text-white shadow-xs">
                                💰 Total Kotor: {{ $crop->formatted_total_pendapatan_kotor }}
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
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-3.5">
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

                <div class="col-span-2 sm:col-span-1 p-3 rounded-xl bg-gray-50/80 border border-gray-100">
                    <span class="text-[11px] font-semibold text-text-muted uppercase tracking-wider block">Tanggal Menanam</span>
                    <span class="text-xs sm:text-sm font-bold text-gray-900 mt-0.5 block">
                        {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->locale('id')->isoFormat('dddd') }}, {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->format('d-m-Y') }}
                    </span>
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
            <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Log Kegiatan Per HST</span>
            @if ($crop->status === 'Sedang Ditanam')
                <button type="button"
                        onclick="openAddActivityModal({{ $currentHst }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-all shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Catat Kegiatan</span>
                </button>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-gray-100 border border-gray-200 text-text-secondary font-medium text-[11px]">
                    🔒 Arsip Log HST (Riwayat)
                </span>
            @endif
        </div>
    </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── TABEL LOG HST PER TANAMAN (PERSIS SESUAI DESAIN GAMBAR)          ── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
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
                                    <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                                        🌱 Hari Pertama Tanam
                                    </div>
                                @endif
                                @if (!empty($row['harvests']) && $row['harvests']->isNotEmpty())
                                    <div class="mt-1 space-y-1">
                                        @foreach ($row['harvests'] as $hrv)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-bold">
                                                <span>🌾 Panen ke-{{ $hrv->panen_ke }}</span>
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
                                                            onclick="openEditActivityModal({{ $act->id }}, '{{ addslashes($act->nama_kegiatan) }}', {{ $act->target_hst }}, '{{ $row['date']->toDateString() }}', `{{ addslashes($act->aplikasi_obat ?? '') }}`, `{{ addslashes($act->sasaran ?? '') }}`, `{{ addslashes($act->keterangan ?? $act->catatan ?? '') }}`, {{ json_encode($act->foto_urls) }})"
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
                                    </div>
                                @elseif ($isPlantingDay)
                                    <div class="text-emerald-800 font-bold flex items-center gap-1.5">
                                        <span>Menanam Bibit</span>
                                    </div>
                                @endif

                                @if (!empty($row['harvests']) && $row['harvests']->isNotEmpty())
                                    <div class="space-y-1.5 {{ $hasAct || $isPlantingDay ? 'mt-2 pt-2 border-t border-gray-200' : '' }}">
                                        @foreach ($row['harvests'] as $hrv)
                                            <div class="p-2 rounded-lg bg-amber-50/90 border border-amber-200 text-xs text-amber-950">
                                                <div class="font-bold flex items-center justify-between gap-2 flex-wrap">
                                                    <span class="flex items-center gap-1">
                                                        <span>🌾 Panen ke-{{ $hrv->panen_ke }}</span>
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
                                            class="{{ $isToday ? 'inline-flex font-bold text-emerald-800 bg-white/90 px-2.5 py-1 rounded-md border border-emerald-300 shadow-xs' : 'opacity-0 group-hover:opacity-100 focus:opacity-100 inline-flex' }} items-center gap-1 text-xs text-primary font-semibold hover:underline transition-opacity {{ !empty($row['harvests']) && $row['harvests']->isNotEmpty() ? 'mt-2' : '' }}">
                                        <span>+ Catat</span>
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
                                                        <div class="space-y-1 obat-group-container">
                                                            @foreach ($obatItems as $oIdx => $oItem)
                                                                <div class="text-xs leading-snug flex items-start gap-1.5 {{ $oIdx >= 2 ? 'obat-extra-item hidden' : '' }}">
                                                                    <span class="text-emerald-600 font-bold shrink-0 mt-0.5">•</span>
                                                                    <span class="font-medium {{ $isToday ? 'text-emerald-950 font-semibold' : 'text-gray-900' }} break-words">{{ $oItem }}</span>
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
                                                        <div class="text-xs font-medium leading-relaxed {{ $isToday ? 'text-emerald-950 font-semibold' : 'text-gray-900' }} break-words">
                                                            {{ $rawObat }}
                                                        </div>
                                                    @endif
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

            {{-- Aplikasi Obat --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="add-aplikasi-obat" class="block text-xs font-semibold text-gray-900">
                        Aplikasi Obat (Opsional)
                    </label>
                </div>
                <textarea id="add-aplikasi-obat"
                          name="aplikasi_obat"
                          rows="2"
                          placeholder="Contoh: Fungisida : Dithane 2 sendok (16 liter), Insektisida : Curacron 15 ml (16 liter), Vitamin : Gandasil D 2 sendok (16 liter)"
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary font-mono text-xs"></textarea>
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

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="edit-aplikasi-obat" class="block text-xs font-semibold text-gray-900">
                        Aplikasi Obat
                    </label>    
                </div>
                <textarea id="edit-aplikasi-obat"
                          name="aplikasi_obat"
                          rows="2"
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary font-mono text-xs"></textarea>
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

    function insertMedicineToTextarea(prefix, selectEl) {
        const opt = selectEl.selectedOptions[0];
        if (!opt || !opt.value) return;

        const textarea = document.getElementById(`${prefix}-aplikasi-obat`);
        const sasaranInput = document.getElementById(`${prefix}-sasaran`);
        if (!textarea) return;

        const medName = opt.value;
        const jenis = opt.dataset.jenis || 'Obat';
        const dosis = opt.dataset.dosis || '1-2 ml';
        const sasaran = opt.dataset.sasaran || '';

        const itemStr = `${jenis} : ${medName} ${dosis} (16 liter)`;

        if (textarea.value.trim() === '') {
            textarea.value = itemStr;
        } else {
            textarea.value += `, ${itemStr}`;
        }

        if (sasaran && sasaranInput && !sasaranInput.value) {
            sasaranInput.value = sasaran;
        }

        // Reset selector
        selectEl.value = '';
    }

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
        const obatInput = document.getElementById('add-aplikasi-obat');
        const sasaranInput = document.getElementById('add-sasaran');
        const ketInput = document.getElementById('add-keterangan');

        if (namaInput) namaInput.value = '';
        if (obatInput) obatInput.value = '';
        if (sasaranInput) sasaranInput.value = '';
        if (ketInput) ketInput.value = '';

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

    function openEditActivityModal(id, nama, targetHst, targetDate, obat, sasaran, ket, existingPhotos = []) {
        currentEditActivityId = id;
        editExistingPhotos = Array.isArray(existingPhotos) ? [...existingPhotos] : [];
        editNewFiles = [];

        const form = document.getElementById('form-edit-activity');
        form.action = `/kalender-hst/kegiatan/${id}`;

        document.getElementById('edit-nama-kegiatan').value = nama;
        document.getElementById('edit-target-hst').value = targetHst;
        document.getElementById('edit-tanggal-kegiatan').value = targetDate;
        document.getElementById('edit-aplikasi-obat').value = obat || '';
        document.getElementById('edit-sasaran').value = sasaran || '';
        document.getElementById('edit-keterangan').value = ket || '';

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
    });

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
                        ⚡ Draft Offline
                    </span>
                    <button type="button"
                            onclick="deleteOfflineActivityDraft('${act.client_id}', '${escapeHtml(act.nama_kegiatan)}')"
                            class="text-amber-700 hover:text-red-600 p-0.5 text-xs font-bold leading-none cursor-pointer"
                            title="Hapus Draft Offline">
                        ✕
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
                draftObat.innerHTML = `<span class="font-bold text-[10px] uppercase text-amber-800 block">⚡ Draft Obat:</span>${escapeHtml(act.aplikasi_obat)}`;
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
                draftSasaran.innerHTML = `<span class="inline-block px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-200 text-[11px]">⚡ ${escapeHtml(act.sasaran)}</span>`;
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

        // Intercept form tambah kegiatan saat offline
        const addForm = document.getElementById('form-add-activity');
        if (addForm) {
            addForm.addEventListener('submit', async (e) => {
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
                            sasaran: fd.get('sasaran') || null,
                            keterangan: fd.get('keterangan') || null,
                            photos_base64: photosBase64
                        };

                        const record = await window.AgriOfflineStore.addCropActivityOffline({{ $crop->id }}, data);
                        renderDraftActivityInRow(record);

                        // Reset input form
                        addSelectedFiles = [];
                        syncAddFileInput();
                        renderAddPhotoPreviews();
                        addForm.reset();
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
