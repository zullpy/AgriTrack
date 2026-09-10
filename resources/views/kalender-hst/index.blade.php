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
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6 pt-5 border-t border-white/10">
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
    </div>
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
            onclick="switchTab('riwayat')"
            id="tab-btn-riwayat"
            class="tab-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $currentTab === 'riwayat' ? 'bg-white text-primary-dark shadow-sm border border-gray-200' : 'text-text-secondary hover:text-text hover:bg-gray-100/60' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
        <span>Riwayat Panen</span>
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
                                        <span class="truncate">{{ $item['activity']->nama_kegiatan }}</span>
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
                        <div class="flex items-center justify-between gap-3 pt-3 mt-3 border-t border-gray-100">
                            <button type="button"
                                    onclick="openHarvestModal({{ $crop->id }}, '{{ $crop->nama_tanaman }}', '{{ $crop->tanggal_tanam->toDateString() }}')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold active:scale-95 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sudah Dipanen
                            </button>

                            <div class="flex items-center gap-1">
                                <button type="button"
                                        onclick="openEditCropModal({{ $crop->id }}, '{{ addslashes($crop->nama_tanaman) }}', '{{ addslashes($crop->varietas ?? '') }}', '{{ addslashes($crop->populasi ?? '') }}', '{{ $crop->tanggal_tanam->toDateString() }}', `{{ addslashes($crop->catatan ?? '') }}`)"
                                        title="Edit data tanaman"
                                        class="p-2 rounded-xl border border-gray-200 text-text-secondary hover:text-text hover:bg-gray-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </button>

                                <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}"
                                      data-confirm-delete="{{ $crop->nama_tanaman }}"
                                      data-confirm-title="Hapus Tanaman?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus tanaman" class="p-2 rounded-xl border border-gray-200 text-text-muted hover:text-red-500 hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3: RIWAYAT TANAM (SUDAH DIPANEN)                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-content-riwayat" class="tab-pane {{ $currentTab === 'riwayat' ? '' : 'hidden' }}">
    @if ($harvestedCrops->isEmpty())
        <div class="bg-surface rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4 text-text-muted">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-text">Belum Ada Riwayat Panen</h3>
            <p class="text-sm text-text-secondary mt-1 max-w-md mx-auto">Tanaman aktif yang telah selesai dipanen akan diarsipkan di sini beserta riwayat HST akhir dan kegiatan perawatannya.</p>
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
                                    <p class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Panen: <span class="font-medium text-emerald-800">{{ $crop->tanggal_panen ? \Carbon\Carbon::parse($crop->tanggal_panen)->translatedFormat('d M Y') : '—' }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Final Total HST Pill --}}
                            <div class="text-right shrink-0">
                                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                    <span class="text-base font-extrabold font-mono leading-none">HST {{ $crop->total_hst_panen ?? '—' }}</span>
                                </div>
                                <p class="text-[10px] text-text-muted mt-1 text-right">Total Umur Panen</p>
                            </div>
                        </div>

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
                        {{-- Buka Menu HST Tanaman (Log Harian) --}}
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

                        {{-- Card Footer --}}
                        <div class="flex items-center justify-between gap-3 pt-3 mt-3 border-t border-gray-100">
                            <span class="inline-flex items-center gap-1.5 text-xs text-text-muted">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Diarsipkan (Selesai Panen)
                            </span>

                            <form method="POST" action="/kalender-hst/tanaman/{{ $crop->id }}"
                                  data-confirm-delete="{{ $crop->nama_tanaman }}"
                                  data-confirm-title="Hapus Riwayat Panen?">
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

        <form id="form-tambah-kegiatan" method="POST" action="" class="space-y-4">
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
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text">Tandai Sudah Dipanen</h3>
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
                <p class="text-[11px] text-text-muted mt-1">Penghitungan HST otomatis akan dikunci pada tanggal ini dan tanaman dipindahkan ke Riwayat Tanam.</p>
            </div>

            <div>
                <label for="panen-catatan" class="block text-xs font-semibold text-text mb-1">Catatan Hasil Panen (Opsional)</label>
                <textarea id="panen-catatan" name="catatan" rows="2" placeholder="Contoh: Hasil panen 2,5 ton, kualitas buah grade A..."
                          class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-panen')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-medium text-text-secondary hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Konfirmasi Selesai Panen
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

    // Add Activity Modal
    function openModalActivity(cropId, cropName) {
        const form = document.getElementById('form-tambah-kegiatan');
        form.action = '/kalender-hst/tanaman/' + cropId + '/kegiatan';
        document.getElementById('modal-activity-crop-title').innerText = 'Untuk: ' + cropName;
        document.getElementById('act-nama').value = '';
        document.getElementById('act-hst').value = '';
        document.getElementById('act-catatan').value = '';
        openModal('modal-tambah-kegiatan');
    }

    // Harvest Modal
    function openHarvestModal(cropId, cropName, tanggalTanam) {
        const form = document.getElementById('form-panen');
        form.action = '/kalender-hst/tanaman/' + cropId + '/panen';
        document.getElementById('modal-panen-crop-title').innerText = 'Tanaman: ' + cropName;
        document.getElementById('panen-tanggal').min = tanggalTanam;
        document.getElementById('panen-catatan').value = '';
        openModal('modal-panen');
    }

    // Close modal on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
        }
    });
</script>
@endpush
