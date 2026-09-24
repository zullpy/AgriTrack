@extends('layouts.app')

@section('title', 'Kalender HST Tanaman')

@section('content')

{{-- ── Hero Header ── --}}
<div class="relative mb-6 overflow-hidden rounded-2xl bg-primary-dark p-5 lg:p-7 shadow-sm border border-emerald-900">

    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white/90 text-[11px] font-medium mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                Manajemen Umur & Jadwal
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-white leading-tight">Kalender HST Tanaman</h1>
            <p class="text-sm text-green-100/80 mt-1.5">Penghitungan Hari Setelah Tanam (HST) otomatis harian dan timeline perawatan.</p>
        </div>

        <button type="button"
                onclick="openModal('modal-tambah-tanaman')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-primary-dark text-sm font-semibold hover:bg-green-50 active:scale-95 transition-all duration-150 shadow-lg shadow-black/20 self-start shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Tanaman
        </button>
    </div>

    {{-- Stats Pill Bar --}}
    <!-- <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6 pt-5 border-t border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-green-200/80 font-medium">Tanaman Aktif</p>
                <p class="text-base font-bold text-white leading-tight">{{ $activeCrops->count() }} komoditas</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-green-200/80 font-medium">Jatuh Tempo</p>
                <p class="text-base font-bold text-white leading-tight">{{ $dueThisWeekCount }} kegiatan</p>
            </div>
        </div>

        <div class="flex items-center gap-3 col-span-2 sm:col-span-1">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-green-200/80 font-medium">Selesai Dipanen</p>
                <p class="text-base font-bold text-white leading-tight">{{ $harvestedCrops->total() }} riwayat</p>
            </div>
        </div>
    </div> -->
</div>

{{-- ── Flash message ── --}}
@if (session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-primary-light border border-primary/20 text-primary-dark text-sm font-medium shadow-sm">
        <div class="w-7 h-7 rounded-full bg-primary/15 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ── Navigation Tabs ── --}}
@php
    $currentTab = request('tab', 'kalender');
@endphp
<div class="flex items-center gap-2 mb-6 border-b border-gray-200/80 pb-3 overflow-x-auto">
    <button type="button"
            onclick="switchTab('kalender')"
            id="tab-btn-kalender"
            class="tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $currentTab === 'kalender' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
        </svg>
        <span>Kalender Bulanan</span>
    </button>

    <button type="button"
            onclick="switchTab('aktif')"
            id="tab-btn-aktif"
            class="tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $currentTab === 'aktif' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
        </svg>
        <span>Tanaman Aktif</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-primary-light text-primary-dark">{{ $activeCrops->count() }}</span>
    </button>

    <button type="button"
            onclick="switchTab('panen')"
            id="tab-btn-panen"
            class="tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $currentTab === 'panen' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Riwayat Panen</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">{{ $totalHarvestCount }} Petikan</span>
    </button>

    <button type="button"
            onclick="switchTab('riwayat')"
            id="tab-btn-riwayat"
            class="tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $currentTab === 'riwayat' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
        <span>Riwayat Tanaman</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-text-secondary">{{ $harvestedCrops->total() }}</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1: KALENDER BULANAN                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-content-kalender" class="tab-pane {{ $currentTab === 'kalender' ? '' : 'hidden' }}">
    <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        {{-- Calendar Header Bar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <a href="/kalender-hst?month={{ $prevMonth->month }}&year={{ $prevMonth->year }}&tab=kalender"
                   title="Bulan Sebelumnya"
                   class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-text-secondary hover:text-text transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>

                <h2 class="text-lg font-bold text-text min-w-[160px]">
                    {{ $currentMonth->translatedFormat('F Y') }}
                </h2>

                <a href="/kalender-hst?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}&tab=kalender"
                   title="Bulan Berikutnya"
                   class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-text-secondary hover:text-text transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a href="/kalender-hst?month={{ now()->month }}&year={{ now()->year }}&tab=kalender"
                   class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-semibold text-text-secondary transition-all">
                    Hari Ini
                </a>
                <span class="text-xs text-text-muted hidden md:inline">• Klik tanggal untuk melihat detail</span>
            </div>
        </div>

        {{-- Weekday Header --}}
        <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/70 text-center text-xs font-semibold text-text-muted py-2.5">
            <div>Sen</div>
            <div>Sel</div>
            <div>Rab</div>
            <div>Kam</div>
            <div>Jum</div>
            <div>Sab</div>
            <div>Min</div>
        </div>

        {{-- Calendar Grid Days --}}
        <div class="divide-y divide-gray-100">
            @foreach ($calendarWeeks as $week)
                <div class="grid grid-cols-7 divide-x divide-gray-100 min-h-[95px] md:min-h-[110px]">
                    @foreach ($week as $day)
                        @php
                            $isCurrentMonth = $day['isCurrentMonth'];
                            $isToday = $day['isToday'];
                            $isTomorrow = $day['isTomorrow'] ?? false;
                            $crops = $day['crops'];
                            $activities = $day['activities'];
                        @endphp
                        <div class="p-1.5 md:p-2.5 transition-colors flex flex-col justify-between {{ $isCurrentMonth ? 'bg-white hover:bg-gray-50/50' : 'bg-gray-50/30 text-gray-300' }} {{ $isToday ? '!bg-primary-light/15 ring-2 ring-inset ring-primary/30' : ($isTomorrow ? '!bg-emerald-50/30 ring-1 ring-inset ring-emerald-200/60' : '') }}">
                            {{-- Day number header --}}
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold {{ $isToday ? 'w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center' : ($isTomorrow ? 'w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center' : ($isCurrentMonth ? 'text-text' : 'text-gray-400')) }}">
                                    {{ $day['dayNumber'] }}
                                </span>
                                @if ($isToday)
                                    <span class="text-[9px] font-bold text-primary tracking-wider uppercase hidden md:inline">Hari Ini</span>
                                @elseif ($isTomorrow)
                                    <span class="text-[9px] font-bold text-emerald-700 tracking-wider uppercase hidden md:inline">Besok</span>
                                @endif
                            </div>

                            {{-- Crops and Activities Pills --}}
                            <div class="space-y-1 overflow-hidden">
                                {{-- Activities Due --}}
                                @foreach ($activities as $item)
                                    <div class="px-1.5 py-0.5 rounded text-[10px] font-medium leading-tight truncate flex items-center gap-1 {{ $item['activity']->status === 'Selesai' ? 'bg-gray-100 text-gray-500 line-through' : 'bg-amber-50 text-amber-800 border border-amber-200' }}"
                                         title="{{ $item['activity']->nama_kegiatan }} ({{ $item['crop']->nama_tanaman }})">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item['activity']->status === 'Selesai' ? 'bg-gray-400' : 'bg-amber-500' }} shrink-0"></span>
                                        <span class="truncate flex-1">{{ $item['activity']->nama_kegiatan }}</span>
                                        @if ($item['activity']->foto_count > 0)
                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-100/90 px-1 py-0.2 rounded shrink-0">📷{{ $item['activity']->foto_count }}</span>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Running Crops & HST --}}
                                @foreach ($crops as $item)
                                    <div class="px-1.5 py-0.5 rounded text-[10px] font-medium leading-tight truncate bg-emerald-50 text-emerald-800 border border-emerald-100"
                                         title="{{ $item['crop']->nama_tanaman }} — HST {{ $item['hst'] }}">
                                        <span class="font-bold">{{ $item['crop']->nama_tanaman }}</span>
                                        <span class="text-emerald-600 font-mono">HST {{ $item['hst'] }}</span>
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

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2: TANAMAN AKTIF (SEDANG DITANAM)                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-content-aktif" class="tab-pane {{ $currentTab === 'aktif' ? '' : 'hidden' }}">
    @if ($activeCrops->isEmpty())
        <div class="bg-surface rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-primary-light/60 flex items-center justify-center mx-auto mb-4 text-primary">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-text">Belum Ada Tanaman Aktif</h3>
            <p class="text-sm text-text-secondary mt-1 max-w-md mx-auto">Tambahkan tanaman baru untuk mulai menghitung umur HST harian dan menjadwalkan kegiatan perawatan.</p>
            <button type="button"
                    onclick="openModal('modal-tambah-tanaman')"
                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition-all shadow-md shadow-primary/25">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Tanaman Pertama
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($activeCrops as $crop)
                <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div>
                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50/80 border border-emerald-100/80 text-xl flex items-center justify-center shrink-0 select-none">
                                    {{ $crop->emoji }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-bold text-text">{{ $crop->nama_tanaman }}</h3>
                                        @if ($crop->varietas)
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-text-secondary">
                                                {{ $crop->varietas }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-text-secondary mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                        </svg>
                                        Tanam: <span class="font-medium text-text">{{ \Carbon\Carbon::parse($crop->tanggal_tanam)->translatedFormat('d M Y') }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- HST Running Counter Pill --}}
                            <div class="text-right shrink-0">
                                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-base font-extrabold font-mono leading-none">HST {{ $crop->current_hst }}</span>
                                </div>
                                <p class="text-[10px] text-text-muted mt-1 text-right">Otomatis Ter-update</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        {{-- Buka Menu HST Tanaman --}}
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <a href="/kalender-hst/tanaman/{{ $crop->id }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-all shadow-xs group">
                                <svg class="w-4 h-4 text-emerald-700 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                                </svg>
                                <span>Buka Menu HST Tanaman (Log Harian)</span>
                                <svg class="w-3.5 h-3.5 text-emerald-600 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Card Footer: Action Buttons --}}
                        <div class="pt-3 mt-3 border-t border-gray-100 space-y-2">
                            @if ($crop->harvest_count > 0)
                                <div class="flex items-center justify-between text-[11px] font-semibold text-emerald-800 bg-emerald-50/80 border border-emerald-200/80 px-2.5 py-1 rounded-lg">
                                    <span>🌾 Sudah {{ $crop->harvest_count }}x Panen</span>
                                    @if ($crop->total_pendapatan_kotor > 0)
                                        <span class="text-emerald-900 font-bold">Total Kotor: {{ $crop->formatted_total_pendapatan_kotor }}</span>
                                    @elseif ($crop->total_panen)
                                        <span class="text-text-muted font-normal">Terakhir: {{ $crop->total_panen }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center gap-2">
                                {{-- Tombol Panen ke-X --}}
                                <button type="button"
                                        onclick="openHarvestModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ $crop->tanggal_tanam->toDateString() }}', {{ $crop->next_harvest_number }})"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold active:scale-95 transition-all shadow-xs min-w-0">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="truncate">Panen ke-{{ $crop->next_harvest_number }}</span>
                                </button>

                                {{-- Tombol Akhiri Tanaman Ini (di pinggir tombol panen) --}}
                                <button type="button"
                                        onclick="openEndCropModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ $crop->tanggal_tanam->toDateString() }}')"
                                        title="Akhiri Tanaman Ini"
                                        class="inline-flex items-center justify-center gap-1.5 py-2 px-2.5 sm:px-3 rounded-xl border border-gray-200 hover:border-amber-300 bg-white hover:bg-amber-50 text-text-secondary hover:text-amber-800 text-xs font-semibold transition-all shadow-xs shrink-0">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                    </svg>
                                    <span>Akhiri Tanaman</span>
                                </button>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button"
                                            onclick="openEditCropModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ addslashes($crop->varietas ?? '') }}', '{{ addslashes($crop->populasi ?? '') }}', '{{ $crop->tanggal_tanam->toDateString() }}', `{{ addslashes($crop->catatan ?? '') }}`)"
                                            title="Edit data tanaman"
                                            class="p-2 rounded-xl border border-gray-200 text-text-secondary hover:text-text hover:bg-gray-50 transition-all shadow-xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </button>

                                    <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}"
                                          data-confirm-delete="{{ $crop->nama_tanaman }}"
                                          data-confirm-title="Hapus Tanaman?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus tanaman" class="p-2 rounded-xl border border-gray-200 text-text-muted hover:text-red-500 hover:bg-red-50 transition-all shadow-xs">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3: RIWAYAT PANEN (DIPISAH PER TANAMAN)                                --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-content-panen" class="tab-pane {{ $currentTab === 'panen' ? '' : 'hidden' }}">
    

    @if ($cropsWithHarvests->isEmpty())
        <div class="bg-surface rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-4 text-amber-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-text">Belum Ada Catatan Panen</h3>
            <p class="text-sm text-text-secondary mt-1 max-w-md mx-auto">Setiap kali Anda menekan tombol "Panen ke-X" dari menu Tanaman Aktif atau Kalender HST, rincian hasil panen akan otomatis dicatat dan dikelompokkan di sini per tanaman.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($cropsWithHarvests as $crop)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Header Tanaman --}}
                    <div class="p-4 sm:p-5 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/90 text-2xl flex items-center justify-center shrink-0 select-none shadow-xs">
                                {{ $crop->emoji }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $crop->nama_tanaman }}</h3>
                                    @if ($crop->varietas)
                                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-700">
                                            {{ $crop->varietas }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[11px] font-bold {{ $crop->status === 'Sedang Ditanam' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        @if ($crop->status === 'Sedang Ditanam')
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Sedang Ditanam (HST {{ $crop->current_hst }})
                                        @else
                                            Diakhiri (HST {{ $crop->total_hst_panen }})
                                        @endif
                                    </span>
                                </div>
                                <p class="text-xs text-text-muted mt-1">
                                    Tanam: <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($crop->tanggal_tanam)->translatedFormat('d F Y') }}</span>
                                    @if ($crop->populasi) • Populasi: <span class="font-medium text-gray-800">{{ $crop->populasi }}</span> @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 text-white text-xs font-bold shadow-xs">
                                🌾 {{ $crop->harvests->count() }}x Panen
                            </span>
                            @if ($crop->total_pendapatan_kotor > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-extrabold shadow-xs">
                                    💰 Total Kotor: {{ $crop->formatted_total_pendapatan_kotor }}
                                </span>
                            @endif
                            <a href="/kalender-hst/tanaman/{{ $crop->id }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-text-secondary hover:text-text text-xs font-semibold transition-all shadow-xs"
                               title="Buka Kalender HST Tanaman">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                                <span>Menu HST</span>
                            </a>
                        </div>
                    </div>

                    {{-- Tabel Rincian Panen Per Petikan --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-200/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3 px-4 w-32">Panen Ke-</th>
                                    <th class="py-3 px-4 w-44">Tanggal Panen</th>
                                    <th class="py-3 px-4 w-28">Umur (HST)</th>
                                    <th class="py-3 px-4 w-36">Total Panen</th>
                                    <th class="py-3 px-4 w-36">Harga Satuan</th>
                                    <th class="py-3 px-4 w-40">Total Harga Kotor</th>
                                    <th class="py-3 px-4">Keterangan / Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($crop->harvests as $hrv)
                                    <tr class="hover:bg-amber-50/20 transition-colors">
                                        <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-xs">
                                                🌾 Panen ke-{{ $hrv->panen_ke }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-gray-800 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($hrv->tanggal_panen)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700 whitespace-nowrap">
                                            HST {{ $hrv->hst_saat_panen ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                            {{ $hrv->total_panen ?: '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-gray-800 whitespace-nowrap">
                                            @if ($hrv->harga_panen)
                                                {{ str_starts_with($hrv->harga_panen, 'Rp') ? $hrv->harga_panen : 'Rp ' . $hrv->harga_panen }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 font-extrabold text-emerald-800 whitespace-nowrap">
                                            {{ $hrv->formatted_harga_kotor }}
                                        </td>
                                        <td class="py-3.5 px-4 text-text-secondary italic">
                                            {{ $hrv->catatan ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if ($crop->total_pendapatan_kotor > 0)
                                <tfoot class="bg-emerald-50/60 border-t-2 border-emerald-200/80 font-semibold text-xs">
                                    <tr>
                                        <td colspan="5" class="py-3 px-4 text-right text-gray-700 font-bold uppercase tracking-wider text-[11px]">
                                            Total Akumulasi Harga Kotor:
                                        </td>
                                        <td class="py-3 px-4 font-black text-emerald-900 text-sm whitespace-nowrap">
                                            {{ $crop->formatted_total_pendapatan_kotor }}
                                        </td>
                                        <td class="py-3 px-4 text-text-muted text-[11px]">
                                            Dari {{ $crop->harvests->count() }}x petikan tercatat
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 4: RIWAYAT TANAMAN (AKHIRI TANAMAN INI)                                --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-content-riwayat" class="tab-pane {{ $currentTab === 'riwayat' ? '' : 'hidden' }}">

    @if ($harvestedCrops->isEmpty())
        <div class="bg-surface rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4 text-text-muted">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-text">Belum Ada Riwayat Tanaman</h3>
            <p class="text-sm text-text-secondary mt-1 max-w-md mx-auto">Tanaman aktif yang telah Anda selesaikan melalui tombol "Akhiri Tanaman Ini" akan diarsipkan di sini. Seluruh riwayat HST akhir dan kegiatan perawatannya tetap tersimpan aman.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($harvestedCrops as $crop)
                @php
                    $doneActivities = $crop->activities->where('status', 'Selesai')->count();
                    $totalActivities = $crop->activities->count();
                @endphp
                <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                    <div>
                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-xl flex items-center justify-center shrink-0 select-none">
                                    {{ $crop->emoji }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-bold text-text">{{ $crop->nama_tanaman }}</h3>
                                        @if ($crop->varietas)
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-text-secondary">
                                                {{ $crop->varietas }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2 space-y-1 text-xs text-text-secondary">
                                        <p class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                            </svg>
                                            Tanam: <span class="font-medium text-text">{{ \Carbon\Carbon::parse($crop->tanggal_tanam)->translatedFormat('d M Y') }}</span>
                                        </p>
                                        @if ($crop->status === 'Diakhiri')
                                            <p class="flex items-center gap-1.5 text-amber-700">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                                </svg>
                                                Diakhiri: <span class="font-medium">{{ $crop->tanggal_panen ? \Carbon\Carbon::parse($crop->tanggal_panen)->translatedFormat('d M Y') : '—' }}</span>
                                            </p>
                                        @else
                                            <p class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Panen: <span class="font-medium text-emerald-800">{{ $crop->tanggal_panen ? \Carbon\Carbon::parse($crop->tanggal_panen)->translatedFormat('d M Y') : '—' }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Final Total HST Pill --}}
                            <div class="text-right shrink-0">
                                @if ($crop->status === 'Diakhiri')
                                    <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                        </svg>
                                        <span class="text-base font-extrabold font-mono leading-none">HST {{ $crop->total_hst_panen ?? '—' }}</span>
                                    </div>
                                    <p class="text-[10px] text-amber-700 font-semibold mt-1 text-right">Tanaman Diakhiri</p>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                        </svg>
                                        <span class="text-base font-extrabold font-mono leading-none">HST {{ $crop->total_hst_panen ?? '—' }}</span>
                                    </div>
                                    <p class="text-[10px] text-text-muted mt-1 text-right">Total Umur Panen</p>
                                @endif
                            </div>
                        </div>

                        @if ($crop->total_panen || $crop->harga_panen)
                            <div class="flex flex-wrap items-center gap-3 my-3 p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-xs">
                                @if ($crop->total_panen)
                                    <div class="flex items-center gap-1 font-semibold text-emerald-900">
                                        <span>⚖️ Total Panen:</span>
                                        <span class="font-bold text-emerald-950">{{ $crop->total_panen }}</span>
                                    </div>
                                @endif
                                @if ($crop->harga_panen)
                                    <div class="flex items-center gap-1 font-semibold text-emerald-900">
                                        <span>🏷️ Harga Hari Ini:</span>
                                        <span class="font-bold text-emerald-950">{{ str_starts_with($crop->harga_panen, 'Rp') ? $crop->harga_panen : 'Rp ' . $crop->harga_panen }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($crop->catatan)
                            <p class="text-xs text-text-secondary my-3 bg-gray-50/70 p-2.5 rounded-xl border border-gray-100 italic">
                                "{{ $crop->catatan }}"
                            </p>
                        @endif

                        {{-- Timeline Activities Summary --}}
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2.5">
                                <h4 class="text-xs font-semibold text-text-muted uppercase tracking-wider">Riwayat Perawatan ({{ $doneActivities }} / {{ $totalActivities }} Selesai)</h4>
                                <span class="text-[11px] font-medium text-text-muted">Arsip HST</span>
                            </div>

                            @if ($crop->activities->isEmpty())
                                <p class="text-xs text-text-muted py-3 text-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                    Tidak ada kegiatan terjadwal selama masa tanam.
                                </p>
                            @else
                                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                    @foreach ($crop->activities as $act)
                                        @php
                                            $isDone = $act->status === 'Selesai';
                                        @endphp
                                        <div class="flex items-center justify-between gap-3 p-2 rounded-xl border text-xs bg-gray-50/60 border-gray-200/80 text-text-muted">
                                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                                <div class="w-4 h-4 rounded-md flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                                    <svg class="w-3 h-3 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                </div>
                                                <p class="font-medium truncate {{ $isDone ? 'line-through' : '' }}">
                                                    {{ $act->nama_kegiatan }}
                                                </p>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-semibold bg-gray-100 text-text-secondary shrink-0">
                                                HST {{ $act->target_hst }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        {{-- Buka Menu HST Tanaman (Log Harian Masih Ada) --}}
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <a href="/kalender-hst/tanaman/{{ $crop->id }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs group">
                                <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                                <span>Buka Menu HST Tanaman (Log Harian)</span>
                                <svg class="w-3.5 h-3.5 text-emerald-200 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Card Footer --}}
                        <div class="flex items-center justify-between gap-3 pt-3 mt-3 border-t border-gray-100">
                            <span class="inline-flex items-center gap-1.5 text-xs text-text-muted">
                                @if ($crop->status === 'Diakhiri')
                                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                    </svg>
                                    <span>Diarsipkan (Tanaman Diakhiri)</span>
                                @else
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Diarsipkan (Selesai Panen)</span>
                                @endif
                            </span>

                            <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}"
                                  data-confirm-delete="{{ $crop->nama_tanaman }}"
                                  data-confirm-title="Hapus Riwayat Tanaman?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus riwayat" class="p-2 rounded-xl border border-gray-200 text-text-muted hover:text-red-500 hover:bg-red-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($harvestedCrops->hasPages())
            <div class="mt-6 p-4 rounded-2xl bg-surface border border-gray-100 shadow-sm">
                {{ $harvestedCrops->links() }}
            </div>
        @endif
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

{{-- 1. Modal Tambah Tanaman --}}
<div id="modal-tambah-tanaman" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-lg font-bold text-text">Tambah Tanaman Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-tanaman')" class="p-1.5 rounded-lg text-text-muted hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="/kalender-hst/tanaman" class="space-y-4">
            @csrf
            <div>
                <label for="crop-nama" class="block text-xs font-semibold text-text mb-1">Nama Tanaman <span class="text-red-500">*</span></label>
                <input type="text" id="crop-nama" name="nama_tanaman" required placeholder="Contoh: Jagung Manis, Padi Ciherang, Cabai Rawit"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="crop-varietas" class="block text-xs font-semibold text-text mb-1">Varietas (Opsional)</label>
                    <input type="text" id="crop-varietas" name="varietas" placeholder="Contoh: Talenta F1, Inpari 32"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label for="crop-populasi" class="block text-xs font-semibold text-text mb-1">Populasi (Opsional)</label>
                    <input type="text" id="crop-populasi" name="populasi" placeholder="Contoh: 2.000 Pohon"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label for="crop-tanggal-tanam" class="block text-xs font-semibold text-text mb-1">Tanggal Tanam <span class="text-red-500">*</span></label>
                    <input type="date" id="crop-tanggal-tanam" name="tanggal_tanam" required value="{{ now()->toDateString() }}"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
            </div>

            <div>
                <label for="crop-catatan" class="block text-xs font-semibold text-text mb-1">Catatan Lahan / Lokasi (Opsional)</label>
                <textarea id="crop-catatan" name="catatan" rows="2" placeholder="Contoh: Lahan Petak 2, jarak tanam 70x20 cm..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-tanaman')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm transition-all">
                    Simpan Tanaman
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Modal Edit Tanaman --}}
<div id="modal-edit-tanaman" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-lg font-bold text-text">Edit Data Tanaman</h3>
            <button type="button" onclick="closeModal('modal-edit-tanaman')" class="p-1.5 rounded-lg text-text-muted hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-tanaman" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit-crop-nama" class="block text-xs font-semibold text-text mb-1">Nama Tanaman <span class="text-red-500">*</span></label>
                <input type="text" id="edit-crop-nama" name="nama_tanaman" required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="edit-crop-varietas" class="block text-xs font-semibold text-text mb-1">Varietas (Opsional)</label>
                    <input type="text" id="edit-crop-varietas" name="varietas"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label for="edit-crop-populasi" class="block text-xs font-semibold text-text mb-1">Populasi (Opsional)</label>
                    <input type="text" id="edit-crop-populasi" name="populasi"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label for="edit-crop-tanggal-tanam" class="block text-xs font-semibold text-text mb-1">Tanggal Tanam <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-crop-tanggal-tanam" name="tanggal_tanam" required
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
            </div>

            <div>
                <label for="edit-crop-catatan" class="block text-xs font-semibold text-text mb-1">Catatan Lahan / Lokasi (Opsional)</label>
                <textarea id="edit-crop-catatan" name="catatan" rows="2"
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-edit-tanaman')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Modal Tambah Kegiatan HST --}}
<div id="modal-tambah-kegiatan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text">Tambah Kegiatan Perawatan</h3>
                <p id="modal-activity-crop-title" class="text-xs text-primary font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-tambah-kegiatan')" class="p-1.5 rounded-lg text-text-muted hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-tambah-kegiatan" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="act-nama" class="block text-xs font-semibold text-text mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" id="act-nama" name="nama_kegiatan" required placeholder="Contoh: Pemupukan Susulan 1, Semprot Fungisida, Penyiangan"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
            </div>

            <div>
                <label for="act-hst" class="block text-xs font-semibold text-text mb-1">Target HST (Hari Keberapa) <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="number" id="act-hst" name="target_hst" min="0" required placeholder="Contoh: 15"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm font-mono">
                    <span class="text-xs text-text-muted whitespace-nowrap font-medium">HST</span>
                </div>
                <p class="text-[11px] text-text-muted mt-1">Tentukan pada umur tanaman ke berapa kegiatan ini harus dilaksanakan.</p>
            </div>

            <div>
                <label for="act-catatan" class="block text-xs font-semibold text-text mb-1">Catatan / Dosis Bahan (Opsional)</label>
                <textarea id="act-catatan" name="catatan" rows="2" placeholder="Contoh: Pupuk NPK 15-15-15 dosis 2 sendok per tanaman..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            {{-- Upload Dokumentasi Foto (Maksimal 5 Foto) --}}
            <div class="p-3 rounded-xl border border-gray-200 bg-gray-50/70 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-text">
                        Dokumentasi Foto <span class="text-text-muted font-normal">(Maks. 5 foto)</span>
                    </label>
                    <span id="act-photo-counter" class="text-[11px] font-semibold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full border border-emerald-200">
                        0/5 Foto
                    </span>
                </div>
                <div>
                    <label for="act-foto-kegiatan"
                           class="flex flex-col items-center justify-center p-2.5 border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl cursor-pointer bg-white transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">+ Tambah Foto (Maks. 5)</span>
                        <input type="file"
                               id="act-foto-kegiatan"
                               name="foto_kegiatan[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="handleActPhotoChange(event)"
                               class="hidden">
                    </label>
                </div>
                <div id="act-photo-preview-grid" class="grid grid-cols-5 gap-2 pt-1 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-kegiatan')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm transition-all">
                    Jadwalkan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 4. Modal Konfirmasi Panen --}}
<div id="modal-panen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-panen-heading" class="text-lg font-bold text-text">Catat Hasil Panen</h3>
                <p id="modal-panen-crop-title" class="text-xs text-primary font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-panen')" class="p-1.5 rounded-lg text-text-muted hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-panen" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="panen-tanggal" class="block text-xs font-semibold text-text mb-1">Tanggal Panen Aktual <span class="text-red-500">*</span></label>
                <input type="date" id="panen-tanggal" name="tanggal_panen" required value="{{ now()->toDateString() }}"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                <p class="text-[11px] text-text-muted mt-1">Hasil panen akan dicatat pada riwayat panen dan HST tanaman tetap berjalan aktif.</p>
            </div>

            {{-- Total Panen, Harga Satuan, & Total Harga Kotor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="panen-total" class="block text-xs font-semibold text-text mb-1">Total Panen <span class="text-text-muted font-normal">(opsional)</span></label>
                    <input type="text" id="panen-total" name="total_panen" placeholder="Contoh: 100 kg"
                           class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                </div>
                <div>
                    <label for="panen-harga" class="block text-xs font-semibold text-text mb-1">Harga Satuan Hari Ini <span class="text-text-muted font-normal">(opsional)</span></label>
                    <div class="flex items-center rounded-xl border border-gray-200 bg-white overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all">
                        <span class="px-3 py-2 text-xs font-bold text-gray-500 bg-gray-50 border-r border-gray-200 select-none shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               id="panen-harga"
                               name="harga_panen"
                               placeholder="3.000"
                               inputmode="numeric"
                               class="w-full px-3 py-2 text-sm border-0 focus:outline-none focus:ring-0 text-gray-900 font-semibold bg-transparent">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="panen-total-kotor" class="block text-xs font-semibold text-text">Total Harga Kotor <span class="text-text-muted font-normal">(opsional)</span></label>
                    <span class="text-[11px] text-emerald-700 font-medium bg-emerald-50 px-2 py-0.5 rounded-md">Otomatis dihitung (Total Panen × Harga)</span>
                </div>
                <div class="flex items-center rounded-xl border border-emerald-300 bg-emerald-50/25 overflow-hidden focus-within:border-emerald-600 focus-within:ring-1 focus-within:ring-emerald-600 transition-all">
                    <span class="px-3.5 py-2 text-xs font-bold text-emerald-800 bg-emerald-100/70 border-r border-emerald-200 select-none shrink-0">
                        Rp
                    </span>
                    <input type="text"
                           id="panen-total-kotor"
                           name="total_harga_kotor"
                           placeholder="300.000"
                           inputmode="numeric"
                           class="w-full px-3.5 py-2 text-sm border-0 focus:outline-none focus:ring-0 text-emerald-950 font-bold bg-transparent">
                </div>
            </div>

            <div>
                <label for="panen-catatan" class="block text-xs font-semibold text-text mb-1">Keterangan / Catatan Panen <span class="text-text-muted font-normal">(opsional)</span></label>
                <textarea id="panen-catatan" name="catatan" rows="2" placeholder="Contoh: Kualitas buah grade A, dijual langsung ke pedagang..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-panen')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" id="modal-panen-submit-btn"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Simpan Data Panen
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 5. Modal Akhiri Tanaman Ini --}}
<div id="modal-end-crop" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text">Akhiri Tanaman Ini</h3>
                <p id="modal-end-crop-title" class="text-xs text-amber-700 font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-end-crop')" class="p-1.5 rounded-lg text-text-muted hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-end-crop" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="end-crop-tanggal" class="block text-xs font-semibold text-text mb-1">Tanggal Diakhiri <span class="text-red-500">*</span></label>
                <input type="date" id="end-crop-tanggal" name="tanggal_akhir" required value="{{ now()->toDateString() }}"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                <p class="text-[11px] text-text-muted mt-1">HST akan dikunci pada tanggal ini dan tanaman dipindahkan ke Riwayat Tanam.</p>
            </div>

            <div>
                <label for="end-crop-alasan" class="block text-xs font-semibold text-text mb-1">Alasan Diakhiri <span class="text-text-muted font-normal">(opsional)</span></label>
                <select id="end-crop-alasan" name="alasan" class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm">
                    <option value="">-- Pilih Alasan (Opsional) --</option>
                    <option value="Gagal Panen (Hama / Penyakit)">Gagal Panen (Hama / Penyakit)</option>
                    <option value="Gagal Panen (Cuaca / Bencana)">Gagal Panen (Cuaca / Bencana)</option>
                    <option value="Dibongkar / Ganti Tanaman">Dibongkar / Ganti Tanaman</option>
                    <option value="Masa Produktif Selesai">Masa Produktif Selesai</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label for="end-crop-catatan" class="block text-xs font-semibold text-text mb-1">Keterangan Tambahan <span class="text-text-muted font-normal">(opsional)</span></label>
                <textarea id="end-crop-catatan" name="catatan" rows="2" placeholder="Tuliskan catatan kondisi akhir tanaman..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-end-crop')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Ya, Akhiri Tanaman
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switching
    function switchTab(tabKey) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.className = btn.className
                .replace(/bg-white text-primary-dark shadow-sm border border-gray-200/, 'text-text-secondary hover:text-text hover:bg-gray-100/60');
        });

        const targetPane = document.getElementById('tab-content-' + tabKey);
        const targetBtn = document.getElementById('tab-btn-' + tabKey);

        if (targetPane) targetPane.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.className = targetBtn.className
                .replace('text-text-secondary hover:text-text hover:bg-gray-100/60', 'bg-white text-primary-dark shadow-sm border border-gray-200');
        }

        // Update URL query state without full reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabKey);
        window.history.replaceState({}, '', url);
    }

    // Modal helpers
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.remove('hidden');
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.add('hidden');
    }

    // Edit Crop Modal
    function openEditCropModal(id, nama, varietas, populasi, tanggalTanam, catatan) {
        const form = document.getElementById('form-edit-tanaman');
        form.action = '/kalender-hst/tanaman/' + id;
        document.getElementById('edit-crop-nama').value = nama;
        document.getElementById('edit-crop-varietas').value = varietas || '';
        document.getElementById('edit-crop-populasi').value = populasi || '';
        document.getElementById('edit-crop-tanggal-tanam').value = tanggalTanam;
        document.getElementById('edit-crop-catatan').value = catatan || '';
        openModal('modal-edit-tanaman');
    }

    // Add Activity Modal Photo Management
    let actSelectedFiles = [];

    function handleActPhotoChange(event) {
        const files = Array.from(event.target.files);
        if (actSelectedFiles.length + files.length > 5) {
            alert(`Maksimal 5 foto per kegiatan. Anda telah memilih ${actSelectedFiles.length} foto.`);
            return;
        }
        for (const f of files) {
            if (actSelectedFiles.length < 5) {
                actSelectedFiles.push(f);
            }
        }
        syncActFileInput();
        renderActPhotoPreviews();
    }

    function removeActPhoto(index) {
        actSelectedFiles.splice(index, 1);
        syncActFileInput();
        renderActPhotoPreviews();
    }

    function syncActFileInput() {
        const dt = new DataTransfer();
        actSelectedFiles.forEach(f => dt.items.add(f));
        const input = document.getElementById('act-foto-kegiatan');
        if (input) input.files = dt.files;
    }

    function renderActPhotoPreviews() {
        const grid = document.getElementById('act-photo-preview-grid');
        const counter = document.getElementById('act-photo-counter');
        if (counter) counter.textContent = `${actSelectedFiles.length}/5 Foto`;
        if (!grid) return;
        grid.innerHTML = '';
        if (actSelectedFiles.length === 0) {
            grid.classList.add('hidden');
            return;
        }
        grid.classList.remove('hidden');
        actSelectedFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const item = document.createElement('div');
            item.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-2xs';
            item.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover">
                <button type="button" onclick="removeActPhoto(${idx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs shadow transition-transform hover:scale-110" title="Batal foto ini">
                    &times;
                </button>
            `;
            grid.appendChild(item);
        });
    }

    function openModalActivity(cropId, cropName) {
        const form = document.getElementById('form-tambah-kegiatan');
        form.action = '/kalender-hst/tanaman/' + cropId + '/kegiatan';
        document.getElementById('modal-activity-crop-title').innerText = 'Untuk: ' + cropName;
        document.getElementById('act-nama').value = '';
        document.getElementById('act-hst').value = '';
        document.getElementById('act-catatan').value = '';

        // Reset foto
        actSelectedFiles = [];
        syncActFileInput();
        renderActPhotoPreviews();

        openModal('modal-tambah-kegiatan');
    }

    // Harvest Modal
    function openHarvestModal(cropId, cropName, tanggalTanam, nextNumber) {
        const form = document.getElementById('form-panen');
        form.action = '/kalender-hst/tanaman/' + cropId + '/panen';
        const num = nextNumber || 1;
        const titleHeading = document.getElementById('modal-panen-heading');
        if (titleHeading) titleHeading.innerText = 'Catat Panen ke-' + num;
        document.getElementById('modal-panen-crop-title').innerText = 'Tanaman: ' + cropName;
        document.getElementById('panen-tanggal').min = tanggalTanam;
        const totalEl = document.getElementById('panen-total');
        if (totalEl) totalEl.value = '';
        const hargaEl = document.getElementById('panen-harga');
        if (hargaEl) hargaEl.value = '';
        const kotorEl = document.getElementById('panen-total-kotor');
        if (kotorEl) kotorEl.value = '';
        document.getElementById('panen-catatan').value = '';
        const submitBtn = document.getElementById('modal-panen-submit-btn');
        if (submitBtn) submitBtn.innerText = 'Simpan Panen ke-' + num;
        openModal('modal-panen');
    }

    // End Crop Modal
    function openEndCropModal(cropId, cropName, tanggalTanam) {
        const form = document.getElementById('form-end-crop');
        form.action = '/kalender-hst/tanaman/' + cropId + '/akhiri';
        document.getElementById('modal-end-crop-title').innerText = 'Tanaman: ' + cropName;
        document.getElementById('end-crop-tanggal').min = tanggalTanam;
        const alasanEl = document.getElementById('end-crop-alasan');
        if (alasanEl) alasanEl.value = '';
        const catatanEl = document.getElementById('end-crop-catatan');
        if (catatanEl) catatanEl.value = '';
        openModal('modal-end-crop');
    }

    // Close modal on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
        }
    });

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
        const totalEl = document.getElementById('panen-total');
        const hargaEl = document.getElementById('panen-harga');
        const kotorEl = document.getElementById('panen-total-kotor');

        bindPriceAutoDot(hargaEl);
        bindPriceAutoDot(kotorEl);

        const autoCalcGross = () => {
            if (!totalEl || !hargaEl || !kotorEl) return;
            const totalVal = totalEl.value.trim().replace(',', '.');
            const match = totalVal.match(/[0-9]+(?:\.[0-9]+)?/);
            const qty = match ? parseFloat(match[0]) : 0;

            const priceDigits = hargaEl.value.replace(/[^0-9]/g, '');
            const unitPrice = priceDigits ? parseFloat(priceDigits) : 0;

            if (qty > 0 && unitPrice > 0) {
                const gross = Math.round(qty * unitPrice);
                kotorEl.value = gross.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        };

        if (totalEl && hargaEl) {
            totalEl.addEventListener('input', autoCalcGross);
            hargaEl.addEventListener('input', autoCalcGross);
        }
    });
</script>
@endpush
