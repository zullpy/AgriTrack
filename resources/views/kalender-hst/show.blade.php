@extends('layouts.app')

@section('title', 'Menu HST - ' . $crop->nama_tanaman . ' | AgriTrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-12">

    {{-- ── Breadcrumbs & Action Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-xs sm:text-sm text-text-muted">
            <a href="/kalender-hst?tab={{ $crop->status === 'Sudah Dipanen' ? 'riwayat' : 'aktif' }}"
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
                        onclick="openHarvestModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ $crop->tanggal_tanam->toDateString() }}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-semibold transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tandai Panen
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

                <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold {{ $crop->status === 'Sedang Ditanam' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        @if ($crop->status === 'Sedang Ditanam')
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            HST {{ $currentHst }} • Sedang Ditanam
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
                        {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
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
            <button type="button"
                    onclick="openAddActivityModal({{ $currentHst }})"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-all shadow-xs shrink-0">
                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Catat Kegiatan</span>
            </button>
        </div>
    </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── TABEL LOG HST PER TANAMAN (PERSIS SESUAI DESAIN GAMBAR)          ── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse font-sans text-xs sm:text-sm">
                {{-- Table Header --}}
                <thead>
                    <tr class="bg-gray-100 text-gray-900 border-b-2 border-gray-400 font-bold uppercase tracking-wider text-center">
                        <th class="py-3 px-3 w-16 border-r border-gray-300 font-extrabold">HST</th>
                        <th class="py-3 px-4 w-44 sm:w-56 border-r border-gray-300 font-extrabold text-left">KALENDER</th>
                        <th class="py-3 px-4 w-40 sm:w-48 border-r border-gray-300 font-extrabold text-left">KEGIATAN</th>
                        <th class="py-3 px-4 border-r border-gray-300 font-extrabold text-left">APLIKASI OBAT</th>
                        <th class="py-3 px-4 w-36 sm:w-44 border-r border-gray-300 font-extrabold text-left">SASARAN</th>
                        <th class="py-3 px-4 w-40 sm:w-52 font-extrabold text-left">KETERANGAN</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
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
                            class="hst-table-row transition-colors group {{ $isToday ? 'bg-[#dcfce7] border-y-2 border-[#16a34a] text-emerald-950 font-medium' : ($isTomorrow ? 'bg-amber-50/40 hover:bg-amber-50/70' : ($isPlantingDay ? 'bg-green-50/40 hover:bg-green-50/70' : 'hover:bg-gray-50/80')) }}">

                            {{-- 1. Kolom HST --}}
                            <td class="py-3 px-3 text-center align-top font-mono font-bold whitespace-nowrap {{ $isToday ? 'bg-[#dcfce7] border-r border-emerald-300 text-emerald-950' : 'border-r border-gray-300' }}">
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
                            <td class="py-3 px-4 align-top whitespace-nowrap {{ $isToday ? 'bg-[#dcfce7] border-r border-emerald-300 text-emerald-950 font-bold' : 'border-r border-gray-300' }}">
                                <div class="{{ $isToday ? 'font-bold text-emerald-950 text-sm' : 'font-medium text-gray-800' }}">
                                    {{ $row['date']->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                                </div>
                                @if ($isPlantingDay)
                                    <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                                        🌱 Hari Pertama Tanam
                                    </div>
                                @endif
                            </td>

                            {{-- 3. Kolom KEGIATAN --}}
                            <td class="py-3 px-4 align-top {{ $isToday ? 'bg-[#dcfce7] border-r border-emerald-300 text-emerald-950' : 'border-r border-gray-300' }}">
                                @if ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            @php
                                                $isDone = $act->status === 'Selesai';
                                            @endphp
                                            <div class="flex items-start justify-between gap-2 p-1.5 rounded-lg {{ $isToday ? 'bg-white/90 border border-emerald-300 shadow-xs' : ($isDone ? 'bg-gray-100/80 text-text-muted' : 'bg-white border border-gray-200') }}">
                                                <div class="flex items-start gap-2 min-w-0 flex-1">
                                                    {{-- Checkbox Selesai --}}
                                                    <form method="POST" action="/kalender-hst/kegiatan/{{ $act->id }}/toggle" class="shrink-0 mt-0.5">
                                                        @csrf
                                                        <button type="submit"
                                                                title="{{ $isDone ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}"
                                                                class="w-4 h-4 rounded border flex items-center justify-center transition-colors {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-gray-400 hover:border-primary text-transparent' }}">
                                                            <svg class="w-3 h-3 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <span class="font-semibold text-xs sm:text-sm {{ $isDone ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                                        {{ $act->nama_kegiatan }}
                                                    </span>
                                                </div>

                                                {{-- Tombol Edit / Hapus Kegiatan --}}
                                                <div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100">
                                                    <button type="button"
                                                            onclick="openEditActivityModal({{ $act->id }}, '{{ addslashes($act->nama_kegiatan) }}', {{ $act->target_hst }}, '{{ $row['date']->toDateString() }}', `{{ addslashes($act->aplikasi_obat ?? '') }}`, `{{ addslashes($act->sasaran ?? '') }}`, `{{ addslashes($act->keterangan ?? $act->catatan ?? '') }}`)"
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
                                                        <button type="submit" title="Hapus kegiatan" class="p-1 rounded hover:bg-red-100 text-red-500">
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
                                @else
                                    <button type="button"
                                            onclick="openAddActivityModal({{ $row['hst'] }}, '{{ $row['date']->toDateString() }}')"
                                            class="{{ $isToday ? 'inline-flex font-bold text-emerald-800 bg-white/90 px-2.5 py-1 rounded-md border border-emerald-300 shadow-xs' : 'opacity-0 group-hover:opacity-100 focus:opacity-100 inline-flex' }} items-center gap-1 text-xs text-primary font-semibold hover:underline transition-opacity">
                                        <span>+ Catat</span>
                                    </button>
                                @endif
                            </td>

                            {{-- 4. Kolom APLIKASI OBAT --}}
                            <td class="py-3 px-4 align-top leading-relaxed {{ $isToday ? 'bg-[#dcfce7] border-r border-emerald-300 text-emerald-950 font-medium' : 'border-r border-gray-300 text-gray-800' }}">
                                @if ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            <div>
                                                @if ($act->aplikasi_obat)
                                                    <span class="font-medium {{ $isToday ? 'text-emerald-950 font-bold' : 'text-gray-900' }}">{{ $act->aplikasi_obat }}</span>
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
                            <td class="py-3 px-4 align-top {{ $isToday ? 'bg-[#dcfce7] border-r border-emerald-300 text-emerald-950' : 'border-r border-gray-300 text-gray-800' }}">
                                @if ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            <div>
                                                @if ($act->sasaran)
                                                    <span class="inline-block px-2 py-0.5 rounded {{ $isToday ? 'bg-white/90 text-emerald-900 font-semibold border border-emerald-300' : 'bg-gray-100 text-gray-800 text-xs font-medium' }}">
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

                            {{-- 6. Kolom KETERANGAN --}}
                            <td class="py-3 px-4 align-top {{ $isToday ? 'bg-[#dcfce7] text-emerald-950 font-medium' : 'text-gray-800' }}">
                                @if ($isPlantingDay)
                                    {{-- Sesuai instruksi: HST dari 0 tapi otomatis keterangannya hari menanam --}}
                                    <div class="font-bold text-emerald-800 flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 border border-emerald-300 text-emerald-900">
                                            Hari Menanam
                                        </span>
                                    </div>
                                    @if ($crop->catatan)
                                        <p class="text-xs text-text-muted mt-1 italic">{{ $crop->catatan }}</p>
                                    @endif
                                @elseif ($hasAct)
                                    <div class="space-y-2">
                                        @foreach ($row['activities'] as $act)
                                            @php
                                                $ket = $act->keterangan ?? $act->catatan;
                                            @endphp
                                            <div class="text-xs {{ $isToday ? 'text-emerald-950 font-medium' : 'text-gray-700' }}">
                                                {{ $ket ?: '-' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="{{ $isToday ? 'text-emerald-500' : 'text-gray-300' }}">-</span>
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
                Menampilkan baris HST 0 sampai besok (HST {{ $maxRowHst }}).
            </div>

            <div class="flex items-center gap-2">
                <a href="/kalender-hst/tanaman/{{ $crop->id }}?limit_hst={{ $maxRowHst + 15 }}"
                   class="px-3.5 py-1.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 font-semibold text-gray-800 transition-colors shadow-xs">
                    + Tambah 15 Baris HST
                </a>
                <a href="/kalender-hst/tanaman/{{ $crop->id }}?limit_hst=120"
                   class="px-3.5 py-1.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 font-semibold text-gray-800 transition-colors shadow-xs">
                    Tampilkan 120 HST (1 Musim Penuh)
                </a>
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

        <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}/kegiatan" class="mt-4 space-y-4">
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
                    HST 0 = Tanggal Menanam ({{ $crop->tanggal_tanam->format('d/m/Y') }}). Mengubah tanggal atau HST akan sinkron otomatis.
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
                {{-- Quick chips --}}
                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                    <span class="text-[11px] text-text-muted">Cepat:</span>
                    <button type="button" onclick="setQuickKegiatan('add', 'Nyemprot')" class="px-2 py-0.5 rounded text-[11px] bg-gray-100 hover:bg-gray-200 text-gray-800">+ Nyemprot</button>
                    <button type="button" onclick="setQuickKegiatan('add', 'Pemupukan')" class="px-2 py-0.5 rounded text-[11px] bg-gray-100 hover:bg-gray-200 text-gray-800">+ Pemupukan</button>
                    <button type="button" onclick="setQuickKegiatan('add', 'Kocor')" class="px-2 py-0.5 rounded text-[11px] bg-gray-100 hover:bg-gray-200 text-gray-800">+ Kocor</button>
                    <button type="button" onclick="setQuickKegiatan('add', 'Penyiangan')" class="px-2 py-0.5 rounded text-[11px] bg-gray-100 hover:bg-gray-200 text-gray-800">+ Penyiangan</button>
                </div>
            </div>

            {{-- Aplikasi Obat --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="add-aplikasi-obat" class="block text-xs font-semibold text-gray-900">
                        Aplikasi Obat (Opsional)
                    </label>
                    @if ($medicines->isNotEmpty())
                        <div class="relative">
                            <select onchange="insertMedicineToTextarea('add', this)"
                                    class="text-[11px] py-0.5 px-2 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-900 font-medium">
                                <option value="">+ Sisipkan dari Data Obat...</option>
                                @foreach ($medicines as $med)
                                    <option value="{{ $med->nama }}"
                                            data-jenis="{{ $med->jenis ?? 'Obat' }}"
                                            data-dosis="{{ $med->dosis_anjuran ?? '' }}"
                                            data-sasaran="{{ $med->sasaran_obat ?? '' }}">
                                        {{ $med->nama }} ({{ $med->jenis ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
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

        <form id="form-edit-activity" method="POST" action="" class="mt-4 space-y-4">
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
                    @if ($medicines->isNotEmpty())
                        <select onchange="insertMedicineToTextarea('edit', this)"
                                class="text-[11px] py-0.5 px-2 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-900 font-medium">
                            <option value="">+ Sisipkan dari Data Obat...</option>
                            @foreach ($medicines as $med)
                                <option value="{{ $med->nama }}"
                                        data-jenis="{{ $med->jenis ?? 'Obat' }}"
                                        data-dosis="{{ $med->dosis_anjuran ?? '' }}"
                                        data-sasaran="{{ $med->sasaran_obat ?? '' }}">
                                    {{ $med->nama }} ({{ $med->jenis ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    @endif
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
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tandai Tanaman Selesai Dipanen</h3>
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
                    HST final akan dikunci berdasarkan selisih tanggal panen dengan tanggal tanam.
                </p>
            </div>

            <div>
                <label for="harvest-notes" class="block text-xs font-semibold text-gray-900 mb-1">
                    Catatan Hasil Panen (Opsional)
                </label>
                <textarea id="harvest-notes"
                          name="catatan"
                          rows="2"
                          placeholder="Misal: Hasil 4.5 Ton, kualitas gabah sangat bagus..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-harvest')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-text-secondary hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm">
                    Kunci HST & Arsipkan
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

    function openEditActivityModal(id, nama, targetHst, targetDate, obat, sasaran, ket) {
        const form = document.getElementById('form-edit-activity');
        form.action = `/kalender-hst/kegiatan/${id}`;

        document.getElementById('edit-nama-kegiatan').value = nama;
        document.getElementById('edit-target-hst').value = targetHst;
        document.getElementById('edit-tanggal-kegiatan').value = targetDate;
        document.getElementById('edit-aplikasi-obat').value = obat || '';
        document.getElementById('edit-sasaran').value = sasaran || '';
        document.getElementById('edit-keterangan').value = ket || '';

        openModal('modal-edit-activity');
    }

    function openHarvestModal(cropId, cropName, plantDate) {
        openModal('modal-harvest');
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
            alert(`Baris HST ${hstVal} belum masuk dalam rentang tampilan. Klik "+ Tambah 15 Baris HST" di bawah.`);
        }
    }
</script>
@endsection
