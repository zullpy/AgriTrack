@extends('layouts.app')

@section('title', 'Menu HST - ' . $crop->nama_tanaman . ' | AgriTrack')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-12">

    {{-- ── Breadcrumbs & Back Button ── --}}
    <div class="flex items-center justify-between gap-4 mb-6">
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

        <div class="flex items-center gap-2">
            @if ($crop->status === 'Sedang Ditanam')
                <button type="button"
                        onclick="openHarvestModal({{ $crop->id }}, '{{ $crop->nama_tanaman }}', '{{ $crop->tanggal_tanam->toDateString() }}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-semibold transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tandai Panen
                </button>
            @endif

            <button type="button"
                    onclick="openEditCropModal({{ $crop->id }}, '{{ $crop->nama_tanaman }}', '{{ $crop->varietas }}', '{{ $crop->tanggal_tanam->toDateString() }}', `{{ $crop->catatan }}`)"
                    class="p-2 rounded-xl border border-gray-200 text-text-secondary hover:text-text hover:bg-gray-100 transition-all"
                    title="Edit Profil Tanaman">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
            </button>

            <form method="POST"
                  action="/kalender-hst/tanaman/{{ $crop->id }}"
                  data-confirm-delete="{{ $crop->nama_tanaman }}"
                  data-confirm-title="Hapus Tanaman?">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="p-2 rounded-xl border border-gray-200 text-text-muted hover:text-red-500 hover:bg-red-50 transition-all"
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
        <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-primary-light border border-primary/20 text-primary-dark text-sm font-medium shadow-sm">
            <div class="w-7 h-7 rounded-full bg-primary/15 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Crop Overview Hero Banner ── --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl sm:rounded-3xl bg-primary-dark p-6 sm:p-8 text-white shadow-xl shadow-primary-dark/25"
         style="background: #1F7A3D;">
        {{-- Background decorative shapes --}}
        <div class="pointer-events-none absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
        <div class="pointer-events-none absolute right-20 top-0 w-32 h-32 bg-emerald-300/10 rounded-full blur-xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-md text-green-100 border border-white/20">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tanam: {{ \Carbon\Carbon::parse($crop->tanggal_tanam)->translatedFormat('d F Y') }}
                    </span>

                    @if ($crop->status === 'Sedang Ditanam')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sedang Ditanam
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-400/20 text-blue-200 border border-blue-400/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            Sudah Dipanen
                        </span>
                    @endif
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        {{ $crop->nama_tanaman }}
                        @if ($crop->varietas)
                            <span class="text-sm sm:text-base font-normal px-2.5 py-1 rounded-lg bg-white/10 text-green-100 border border-white/15">
                                {{ $crop->varietas }}
                            </span>
                        @endif
                    </h1>
                    @if ($crop->catatan)
                        <p class="text-sm text-green-100/90 mt-2 max-w-2xl bg-black/10 px-3 py-2 rounded-xl border border-white/10 italic">
                            "{{ $crop->catatan }}"
                        </p>
                    @endif
                </div>
            </div>

            {{-- HST Badge & Counter --}}
            <div class="flex flex-col sm:flex-row md:flex-col items-start md:items-end justify-between gap-3 shrink-0 bg-white/10 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-white/15">
                <div class="text-left md:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-200/90">
                        {{ $crop->status === 'Sedang Ditanam' ? 'HST Berjalan Hari Ini' : 'HST Akhir Saat Panen' }}
                    </p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl sm:text-4xl font-extrabold font-mono tracking-tight text-white">
                            HST {{ $currentHst }}
                        </span>
                    </div>
                </div>

                <div class="text-left md:text-right">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-green-200 bg-white/10 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                        Tanggal & HST otomatis sinkron setiap hari
                    </span>
                </div>
            </div>
        </div>

        {{-- Statistics Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-white/15">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <p class="text-[11px] text-green-200 font-medium">Umur Tanaman</p>
                <p class="text-lg font-bold text-white mt-0.5">{{ $currentHst }} Hari</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <p class="text-[11px] text-green-200 font-medium">Total Kegiatan Diinput</p>
                <p class="text-lg font-bold text-white mt-0.5">{{ $totalActivities }} Jadwal</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <p class="text-[11px] text-green-200 font-medium">Kegiatan Selesai</p>
                <p class="text-lg font-bold text-emerald-300 mt-0.5">{{ $completedActivities }} Selesai</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <p class="text-[11px] text-green-200 font-medium">Belum Dikerjakan</p>
                <p class="text-lg font-bold text-amber-300 mt-0.5">{{ $pendingActivities }} Menunggu</p>
            </div>
        </div>
    </div>

    {{-- ── Action Bar & Filters ── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-text flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                Jadwal & Log Kegiatan Per HST
            </h2>
            <p class="text-xs text-text-muted mt-0.5">
                Setiap HST memiliki tanggal kalender otomatis. Anda dapat mencatat kegiatan secara manual pada HST manapun.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                    onclick="openAddActivityModal({{ $currentHst }})"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-dark active:scale-95 text-white text-xs font-semibold transition-all shadow-sm">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Catat Manual</span>
            </button>
        </div>
    </div>

    {{-- ── Filter Chips ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-gray-200/80 shadow-xs mb-6">
        <div class="flex flex-wrap items-center gap-1.5" id="timeline-filters">
            <button type="button"
                    onclick="filterTimeline('all')"
                    id="filter-btn-all"
                    class="filter-chip px-3 py-1.5 rounded-xl text-xs font-semibold bg-primary text-white transition-all">
                Semua HST
            </button>
            <button type="button"
                    onclick="filterTimeline('today')"
                    id="filter-btn-today"
                    class="filter-chip px-3 py-1.5 rounded-xl text-xs font-semibold text-text-secondary hover:text-text hover:bg-gray-100 transition-all">
                Hari Ini (HST {{ $currentHst }})
            </button>
            @if ($crop->status === 'Sedang Ditanam')
                <button type="button"
                        onclick="filterTimeline('tomorrow')"
                        id="filter-btn-tomorrow"
                        class="filter-chip px-3 py-1.5 rounded-xl text-xs font-semibold text-text-secondary hover:text-text hover:bg-gray-100 transition-all">
                    Besok (HST {{ $currentHst + 1 }})
                </button>
            @endif
            <button type="button"
                    onclick="filterTimeline('has-activity')"
                    id="filter-btn-has-activity"
                    class="filter-chip px-3 py-1.5 rounded-xl text-xs font-semibold text-text-secondary hover:text-text hover:bg-gray-100 transition-all">
                Ada Kegiatan Saja ({{ $totalActivities }})
            </button>
            <button type="button"
                    onclick="filterTimeline('pending')"
                    id="filter-btn-pending"
                    class="filter-chip px-3 py-1.5 rounded-xl text-xs font-semibold text-text-secondary hover:text-text hover:bg-gray-100 transition-all">
                Belum Selesai ({{ $pendingActivities }})
            </button>
        </div>

        {{-- Jump to HST input --}}
        <div class="flex items-center gap-2">
            <label for="jump-hst" class="text-xs text-text-muted whitespace-nowrap">Lompat ke:</label>
            <div class="relative flex items-center">
                <input type="number"
                       id="jump-hst"
                       placeholder="HST..."
                       min="0"
                       class="w-20 px-2.5 py-1 text-xs rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
                <button type="button"
                        onclick="jumpToHst()"
                        class="ml-1.5 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-medium text-text transition-colors">
                    Pergi
                </button>
            </div>
        </div>
    </div>

    {{-- ── Day-by-Day HST Timeline Stream ── --}}
    <div class="space-y-3" id="timeline-container">
        @foreach ($timelineDays as $day)
            @php
                $hasActivities = $day['activities']->isNotEmpty();
                $hasPending = $day['activities']->where('status', 'Belum')->isNotEmpty();
                $isToday = $day['is_today'];
                $isTomorrow = $crop->status === 'Sedang Ditanam' && $day['hst'] === $currentHst + 1;
            @endphp
            <div id="hst-card-{{ $day['hst'] }}"
                 data-hst="{{ $day['hst'] }}"
                 data-has-activity="{{ $hasActivities ? '1' : '0' }}"
                 data-is-today="{{ $isToday ? '1' : '0' }}"
                 data-is-tomorrow="{{ $isTomorrow ? '1' : '0' }}"
                 data-has-pending="{{ $hasPending ? '1' : '0' }}"
                 class="timeline-card rounded-2xl border transition-all p-4 sm:p-5 {{ $isToday ? 'bg-emerald-50/40 border-primary shadow-xs ring-1 ring-primary/20' : ($isTomorrow ? 'bg-amber-50/20 border-amber-300 shadow-xs' : ($day['is_past'] ? 'bg-white/80 border-gray-200' : 'bg-white border-gray-200/90')) }}">

                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    {{-- Left Column: HST and Date Info --}}
                    <div class="flex items-start gap-3 sm:gap-4 shrink-0 md:w-64">
                        <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center shrink-0 font-mono shadow-xs {{ $isToday ? 'bg-primary text-white font-black' : ($isTomorrow ? 'bg-amber-50 text-amber-900 border border-amber-200 font-bold' : ($day['is_past'] ? 'bg-gray-100 text-text border border-gray-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-100')) }}">
                            <span class="text-[10px] uppercase tracking-wider font-semibold opacity-80 leading-none">HST</span>
                            <span class="text-xl font-extrabold leading-none mt-0.5">{{ $day['hst'] }}</span>
                        </div>

                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-sm font-bold text-text">
                                    {{ $day['date']->translatedFormat('l, d M Y') }}
                                </span>
                            </div>

                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                @if ($isToday)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                                        HARI INI
                                    </span>
                                @elseif ($isTomorrow)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        BESOK
                                    </span>
                                @elseif ($day['is_past'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-text-muted">
                                        Sudah Lewat ({{ $currentHst - $day['hst'] }} hari lalu)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-blue-50 text-blue-700">
                                        Jadwal Mendatang ({{ $day['hst'] - $currentHst }} hari lagi)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Center Column: Kegiatan List for this HST --}}
                    <div class="flex-1 min-w-0">
                        @if ($hasActivities)
                            <div class="space-y-2.5">
                                @foreach ($day['activities'] as $act)
                                    @php
                                        $isDone = $act->status === 'Selesai';
                                    @endphp
                                    <div class="flex items-start justify-between gap-3 p-3 rounded-xl border transition-all {{ $isDone ? 'bg-gray-50/70 border-gray-200 text-text-muted' : 'bg-white border-gray-200 text-text shadow-xs' }}">
                                        <div class="flex items-start gap-3 min-w-0 flex-1">
                                            {{-- Toggle Checkbox --}}
                                            <form method="POST" action="/kalender-hst/kegiatan/{{ $act->id }}/toggle" class="shrink-0 mt-0.5">
                                                @csrf
                                                <button type="submit"
                                                        title="{{ $isDone ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}"
                                                        class="w-5 h-5 rounded-lg border flex items-center justify-center transition-colors {{ $isDone ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 hover:border-primary text-transparent hover:text-primary/30' }}">
                                                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-sm font-semibold {{ $isDone ? 'line-through text-text-muted' : 'text-text' }}">
                                                        {{ $act->nama_kegiatan }}
                                                    </h4>
                                                    @if ($isDone)
                                                        <span class="px-2 py-0.2 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            Selesai
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.2 rounded text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                            Belum
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($act->catatan)
                                                    <p class="text-xs text-text-secondary mt-1 italic">
                                                        "{{ $act->catatan }}"
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Activity Actions --}}
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                    onclick="openEditActivityModal({{ $act->id }}, '{{ addslashes($act->nama_kegiatan) }}', {{ $act->target_hst }}, '{{ $day['date']->toDateString() }}', `{{ addslashes($act->catatan ?? '') }}`)"
                                                    title="Edit kegiatan"
                                                    class="p-1.5 rounded-lg text-text-secondary hover:text-text hover:bg-gray-100 transition-colors">
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
                                                <button type="submit"
                                                        title="Hapus kegiatan"
                                                        class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-2.5 px-3 rounded-xl bg-gray-50/60 border border-dashed border-gray-200 text-xs text-text-muted flex items-center justify-between">
                                <span>Belum ada kegiatan manual yang dicatat pada HST {{ $day['hst'] }}.</span>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column: Quick Add Button for this HST --}}
                    <div class="shrink-0 self-center md:self-start">
                        <button type="button"
                                onclick="openAddActivityModal({{ $day['hst'] }}, '{{ $day['date']->toDateString() }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-primary hover:bg-primary-light/50 text-text-secondary hover:text-primary-dark text-xs font-medium transition-all">
                            <svg class="w-3.5 h-3.5 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            <span>+ Kegiatan</span>
                        </button>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH KEGIATAN (DENGAN SINKRONISASI HST & TANGGAL)    --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-activity" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-text">Catat Kegiatan Manual</h3>
                <p class="text-xs text-text-muted mt-0.5">Untuk Tanaman: {{ $crop->nama_tanaman }}</p>
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

            <div>
                <label for="add-nama-kegiatan" class="block text-xs font-semibold text-text mb-1">
                    Nama Kegiatan Perawatan <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="add-nama-kegiatan"
                       name="nama_kegiatan"
                       placeholder="Contoh: Pemupukan NPK, Semprot Hama, Penyiangan"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            {{-- Dual Synchronized Inputs: HST and Date --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="add-target-hst" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Target HST (Hari ke-)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="add-target-hst"
                                   name="target_hst"
                                   min="0"
                                   value="{{ $currentHst }}"
                                   oninput="syncFromHst('add')"
                                   class="field-input w-full px-3.5 py-2 rounded-xl border border-emerald-200 text-sm font-mono font-bold text-emerald-950 focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label for="add-tanggal-kegiatan" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Tanggal Pelaksanaan
                        </label>
                        <input type="date"
                               id="add-tanggal-kegiatan"
                               name="tanggal_kegiatan"
                               value="{{ now()->toDateString() }}"
                               oninput="syncFromDate('add')"
                               class="field-input w-full px-3.5 py-2 rounded-xl border border-emerald-200 text-sm focus:border-primary">
                    </div>
                </div>

                <p class="text-[11px] text-emerald-800 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    Ubah HST atau Tanggal, keduanya akan otomatis saling sinkron berdasarkan tanggal tanam ({{ $crop->tanggal_tanam->format('d/m/Y') }}).
                </p>
            </div>

            <div>
                <label for="add-catatan" class="block text-xs font-semibold text-text mb-1">
                    Catatan / Instruksi Teknis (Opsional)
                </label>
                <textarea id="add-catatan"
                          name="catatan"
                          rows="2"
                          placeholder="Dosis pupuk, jenis obat, takaran air, dsb."
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
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-text">Edit Kegiatan Perawatan</h3>
                <p class="text-xs text-text-muted mt-0.5">Perbarui jadwal atau catatan kegiatan</p>
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

            <div>
                <label for="edit-nama-kegiatan" class="block text-xs font-semibold text-text mb-1">
                    Nama Kegiatan Perawatan <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="edit-nama-kegiatan"
                       name="nama_kegiatan"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            {{-- Dual Synchronized Inputs: HST and Date --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit-target-hst" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Target HST (Hari ke-)
                        </label>
                        <input type="number"
                               id="edit-target-hst"
                               name="target_hst"
                               min="0"
                               required
                               oninput="syncFromHst('edit')"
                               class="field-input w-full px-3.5 py-2 rounded-xl border border-emerald-200 text-sm font-mono font-bold text-emerald-950 focus:border-primary">
                    </div>

                    <div>
                        <label for="edit-tanggal-kegiatan" class="block text-xs font-semibold text-emerald-950 mb-1">
                            Tanggal Pelaksanaan
                        </label>
                        <input type="date"
                               id="edit-tanggal-kegiatan"
                               oninput="syncFromDate('edit')"
                               class="field-input w-full px-3.5 py-2 rounded-xl border border-emerald-200 text-sm focus:border-primary">
                    </div>
                </div>
            </div>

            <div>
                <label for="edit-catatan" class="block text-xs font-semibold text-text mb-1">
                    Catatan (Opsional)
                </label>
                <textarea id="edit-catatan"
                          name="catatan"
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
{{-- MODAL: TANDAI SUDAH DIPANEN                                  --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-harvest" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-text">Tandai Tanaman Selesai Dipanen</h3>
                <p class="text-xs text-text-muted mt-0.5" id="harvest-crop-name">{{ $crop->nama_tanaman }}</p>
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
                <label for="harvest-date" class="block text-xs font-semibold text-text mb-1">
                    Tanggal Panen Aktual <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="harvest-date"
                       name="tanggal_panen"
                       value="{{ now()->toDateString() }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
                <p class="text-[11px] text-text-muted mt-1">
                    HST final akan dikunci pada selisih tanggal panen ini dengan tanggal tanam.
                </p>
            </div>

            <div>
                <label for="harvest-notes" class="block text-xs font-semibold text-text mb-1">
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

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT DATA TANAMAN                                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-crop" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-text">Edit Informasi Tanaman</h3>
                <p class="text-xs text-text-muted mt-0.5">Perbarui nama, varietas, atau tanggal tanam</p>
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
                <label for="edit-crop-nama" class="block text-xs font-semibold text-text mb-1">
                    Nama Tanaman <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="edit-crop-nama"
                       name="nama_tanaman"
                       value="{{ $crop->nama_tanaman }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div>
                <label for="edit-crop-varietas" class="block text-xs font-semibold text-text mb-1">
                    Varietas / Bibit (Opsional)
                </label>
                <input type="text"
                       id="edit-crop-varietas"
                       name="varietas"
                       value="{{ $crop->varietas }}"
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div>
                <label for="edit-crop-tanggal" class="block text-xs font-semibold text-text mb-1">
                    Tanggal Tanam <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="edit-crop-tanggal"
                       name="tanggal_tanam"
                       value="{{ $crop->tanggal_tanam->toDateString() }}"
                       required
                       class="field-input w-full px-3.5 py-2 rounded-xl border border-gray-300 text-sm focus:border-primary">
            </div>

            <div>
                <label for="edit-crop-catatan" class="block text-xs font-semibold text-text mb-1">
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

<script>
    const PLANT_DATE_STRING = '{{ $crop->tanggal_tanam->toDateString() }}';
    const PLANT_DATE = new Date(PLANT_DATE_STRING + 'T00:00:00');

    // Utility: Format Date to YYYY-MM-DD
    function formatDateToString(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Modal helpers
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

    // Interactive Dual Sync: HST <-> Date
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

    function openAddActivityModal(targetHst = null, targetDate = null) {
        const hstInput = document.getElementById('add-target-hst');
        const dateInput = document.getElementById('add-tanggal-kegiatan');
        const namaInput = document.getElementById('add-nama-kegiatan');

        if (namaInput) namaInput.value = '';

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

    function openEditActivityModal(id, nama, targetHst, targetDate, catatan) {
        const form = document.getElementById('form-edit-activity');
        form.action = `/kalender-hst/kegiatan/${id}`;

        document.getElementById('edit-nama-kegiatan').value = nama;
        document.getElementById('edit-target-hst').value = targetHst;
        document.getElementById('edit-tanggal-kegiatan').value = targetDate;
        document.getElementById('edit-catatan').value = catatan;

        openModal('modal-edit-activity');
    }

    function openHarvestModal(cropId, cropName, plantDate) {
        openModal('modal-harvest');
    }

    function openEditCropModal(cropId, nama, varietas, tanggalTanam, catatan) {
        openModal('modal-edit-crop');
    }

    // Filter Timeline Cards
    function filterTimeline(type) {
        // Active buttons
        document.querySelectorAll('.filter-chip').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('text-text-secondary');
        });

        const activeBtn = document.getElementById(`filter-btn-${type}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-text-secondary');
            activeBtn.classList.add('bg-primary', 'text-white');
        }

        const cards = document.querySelectorAll('.timeline-card');
        cards.forEach(card => {
            if (type === 'all') {
                card.classList.remove('hidden');
            } else if (type === 'today') {
                if (card.dataset.isToday === '1') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            } else if (type === 'tomorrow') {
                if (card.dataset.isTomorrow === '1') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            } else if (type === 'has-activity') {
                if (card.dataset.hasActivity === '1') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            } else if (type === 'pending') {
                if (card.dataset.hasPending === '1') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            }
        });
    }

    // Quick Jump to HST
    function jumpToHst() {
        const input = document.getElementById('jump-hst');
        if (!input || !input.value) return;

        const hstVal = parseInt(input.value, 10);
        const targetCard = document.getElementById(`hst-card-${hstVal}`);
        if (targetCard) {
            // reset filter to all so card is visible
            filterTimeline('all');
            targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetCard.classList.add('ring-2', 'ring-primary');
            setTimeout(() => {
                targetCard.classList.remove('ring-2', 'ring-primary');
            }, 2500);
        } else {
            if (window.AgriSwal && typeof window.AgriSwal.toastError === 'function') {
                window.AgriSwal.toastError(`HST ${hstVal} belum masuk dalam rentang tampilan.`);
            } else if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi HST',
                    text: `HST ${hstVal} belum masuk dalam rentang tampilan.`,
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'agri-swal-popup',
                        confirmButton: 'agri-swal-btn-cancel'
                    },
                    buttonsStyling: false
                });
            }
        }
    }
</script>
@endsection
