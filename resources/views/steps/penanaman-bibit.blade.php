@extends('layouts.app')

@section('title', 'Tahapan Penanaman Bibit')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6 pb-12">

    {{-- ── Breadcrumb & Title Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="/steps" class="hover:text-emerald-700 transition-colors font-medium">Tahapan</a>
                <span>/</span>
                <span class="text-emerald-700 font-semibold">Penanaman Bibit</span>
            </nav>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Tahapan Penanaman Bibit</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Multi-Komoditas Bibit (Fase 2)
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Panduan SOP pembibitan dan transplanting per komoditas bibit (Wortel, Cabai, Tomat, dll.) lengkap dengan dokumentasi foto dan tips lapangan.
            </p>
        </div>

        
    </div>

    {{-- ── Quick Navigation Tabs ── --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit max-w-full overflow-x-auto no-scrollbar">
        <a href="/steps/pengolahan-tanah"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            1. Pengolahan Tanah
        </a>
        <a href="/steps/penanaman-bibit"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-emerald-800 shadow-xs whitespace-nowrap transition-all">
            2. Penanaman Bibit
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAMPILAN 1: GRID KARTU BIBIT (CARD PER BIBIT)                 --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="seeds-grid-view" class="space-y-4">
        {{-- Section Subheader / Counter --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Katalog Komoditas Bibit</h2>
                <p class="text-xs text-gray-500">Pilih salah satu kartu komoditas bibit di bawah untuk melihat dan mengelola tahapan penanamannya.</p>
            </div>
            <span id="seeds-counter" class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shrink-0">
                {{ $seeds->count() }} Komoditas Bibit
            </span>
        </div>

        {{-- Grid Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="seeds-grid">
            @forelse($seeds as $seed)
                @php
                    $stepCount = $seed->steps->count();
                    $photoCount = $seed->steps->sum(function($st) {
                        return is_array($st->foto) ? count($st->foto) : 0;
                    });
                @endphp
                <div class="seed-card group relative bg-white rounded-2xl border border-gray-200/90 hover:border-emerald-500 hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col justify-between overflow-hidden"
                     onclick="openSeedDetail({{ $seed->id }})"
                     id="seed-card-{{ $seed->id }}"
                     data-seed-id="{{ $seed->id }}">

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            {{-- Row 1: Icon on Left, Actions on Right --}}
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center font-bold shrink-0 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>

                                {{-- Action buttons for bibit --}}
                                <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                    <button type="button"
                                            onclick="openEditSeedModal({{ json_encode($seed) }})"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                            title="Edit Informasi Bibit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                            onclick="confirmDeleteSeed({{ $seed->id }}, '{{ addslashes($seed->nama_bibit) }}')"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Hapus Bibit Ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Row 2: Full Width Seed Name --}}
                            <h3 class="font-extrabold text-base text-gray-900 group-hover:text-emerald-700 transition-colors leading-snug break-words">
                                {{ $seed->nama_bibit }}
                            </h3>

                            {{-- Row 3: Varietas Badge --}}
                            @if($seed->varietas)
                                <div class="mt-1.5 mb-2.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70 truncate max-w-full">
                                        {{ $seed->varietas }}
                                    </span>
                                </div>
                            @else
                                <div class="mb-2.5"></div>
                            @endif

                            {{-- Row 4: Description --}}
                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                {{ $seed->deskripsi ?: 'Belum ada catatan deskripsi SOP untuk bibit ini.' }}
                            </p>
                        </div>

                        {{-- Stats & CTA --}}
                        <div class="mt-4 pt-3.5 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    {{ $stepCount }} Tahapan
                                </span>
                                @if($photoCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                        </svg>
                                        {{ $photoCount }} Foto
                                    </span>
                                @endif
                            </div>

                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition-all">
                                <span>Lihat Tahapan</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-200 shadow-xs">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Belum Ada Jenis Bibit</h3>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">Tambahkan kartu komoditas bibit tanaman pertama Anda (misal: Bibit Wortel, Bibit Cabai, dll.).</p>
                    <button type="button"
                            onclick="openAddSeedModal()"
                            class="mt-4 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                        + Tambah Jenis Bibit Baru
                    </button>
                </div>
            @endforelse

            @if($seeds->count() > 0)
                {{-- Card Tambah Jenis Bibit Baru --}}
                <div onclick="openAddSeedModal()"
                     class="rounded-2xl border-2 border-dashed border-gray-300 hover:border-emerald-500 hover:bg-emerald-50/30 transition-all p-6 flex flex-col items-center justify-center text-center cursor-pointer min-h-[170px] group">
                    <div class="w-11 h-11 rounded-xl bg-gray-100 group-hover:bg-emerald-100 text-gray-400 group-hover:text-emerald-700 flex items-center justify-center transition-all mb-2.5">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-800 transition-colors">Tambah Jenis Bibit Baru</span>
                    <span class="text-xs text-gray-400 mt-0.5">Wortel, Cabai, Bawang, Tomat, dll.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAMPILAN 2: DETAIL TAHAPAN PER SETIAP BIBIT (SAAT CARD DIKLIK) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="seeds-detail-view" class="space-y-6 hidden">
        {{-- Top Bar Detail: Tombol Kembali & Switcher Bibit Cepat --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-2xl border border-gray-200 shadow-2xs">
            <button type="button"
                    onclick="backToSeedsGrid()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs sm:text-sm font-bold text-gray-800 transition-all cursor-pointer w-fit">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Kembali ke Daftar Semua Bibit</span>
            </button>
        </div>

        {{-- Panel Langkah untuk Setiap Bibit --}}
        <div id="seed-panels-container" class="space-y-6">
        @foreach($seeds as $seed)
            <div class="seed-steps-panel space-y-6 hidden"
                 id="seed-steps-panel-{{ $seed->id }}"
                 data-seed-id="{{ $seed->id }}">

                {{-- Header Detail Bibit Aktif --}}
                <div class="bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 bg-gradient-to-r from-emerald-50/70 via-white to-amber-50/40 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">
                                            Tahapan Penanaman: {{ $seed->nama_bibit }}
                                        </h2>
                                        @if($seed->varietas)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                Varietas: {{ $seed->varietas }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $seed->steps->count() }} Langkah SOP
                                        </span>
                                    </div>
                                    @if($seed->deskripsi)
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1.5 leading-relaxed max-w-4xl">
                                            {{ $seed->deskripsi }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Tombol Aksi Bibit --}}
                            <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                                <button type="button"
                                        onclick="openAddStepModal({{ $seed->id }}, '{{ addslashes($seed->nama_bibit) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    <span>Tambah Langkah</span>
                                </button>
                                <button type="button"
                                        onclick="openEditSeedModal({{ json_encode($seed) }})"
                                        class="p-1.5 rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                                        title="Edit Informasi Bibit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="confirmDeleteSeed({{ $seed->id }}, '{{ addslashes($seed->nama_bibit) }}')"
                                        class="p-1.5 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 transition-colors cursor-pointer"
                                        title="Hapus Kartu Bibit Ini">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Langkah Di Bawah Bibit Ini --}}
                    <div class="p-5 sm:p-6 space-y-4">
                        @forelse($seed->steps as $step)
                            <div class="p-4 sm:p-5 rounded-2xl border border-gray-200/90 bg-white hover:border-emerald-300 hover:shadow-xs transition-all space-y-3">
                                {{-- Header Langkah --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border border-emerald-200">
                                            {{ $step->nomor }}
                                        </div>
                                        <div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                                                <h3 class="text-sm sm:text-base font-bold text-gray-900">
                                                    {{ $step->judul }}
                                                </h3>
                                                @if($step->waktu)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                                                        Waktu: {{ $step->waktu }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Aksi Langkah --}}
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button"
                                                onclick="openEditStepModal({{ json_encode($step) }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-semibold transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                            <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <button type="button"
                                                onclick="confirmDeleteStep({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                                class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus langkah ini">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Deskripsi Langkah --}}
                                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                                    {!! nl2br(e($step->deskripsi)) !!}
                                </p>

                                {{-- Tips Praktisi --}}
                                @if($step->tips)
                                    <div class="bg-emerald-50/70 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-0.5">
                                        <div class="flex items-center gap-1.5 font-bold text-emerald-950">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.002 6.002 0 00-5.918-5.074A5.992 5.992 0 006 9c0 1.904.887 3.601 2.27 4.707L8.25 18h7.5l-.02-4.293A5.992 5.992 0 0018 9c0-.705-.121-1.382-.345-2.008A6.002 6.002 0 0012 12.75z"/>
                                            </svg>
                                            <span>Tips Praktisi Lapangan:</span>
                                        </div>
                                        <p class="text-emerald-900 leading-relaxed">{!! nl2br(e($step->tips)) !!}</p>
                                    </div>
                                @endif

                                {{-- Bagian Foto Dokumentasi & Aksi Foto --}}
                                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-gray-100">
                                    <div class="flex items-center gap-2">
                                        @if(count($step->foto_urls) > 0)
                                            <button type="button"
                                                    onclick="openStepPhotosModal({{ json_encode($step) }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-colors cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                                </svg>
                                                <span>Lihat Foto ({{ count($step->foto_urls) }})</span>
                                            </button>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">Belum ada foto</span>
                                        @endif
                                    </div>

                                    <button type="button"
                                            onclick="openQuickPhotoModal({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}', '{{ addslashes($seed->nama_bibit) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-300 text-amber-900 bg-amber-50 hover:bg-amber-100 text-[11px] font-bold transition-all w-fit cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                                        </svg>
                                        <span>Jepret / Unggah Foto</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                <p class="text-xs font-semibold text-gray-500">Belum ada langkah penanaman untuk {{ $seed->nama_bibit }}.</p>
                                <button type="button"
                                        onclick="openAddStepModal({{ $seed->id }}, '{{ addslashes($seed->nama_bibit) }}')"
                                        class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span>Tambah Langkah Pertama</span>
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH JENIS BIBIT BARU                                --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-seed" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tambah Jenis Bibit / Komoditas</h3>
                <p class="text-xs text-gray-500 mt-0.5">Buat kartu tahapan baru untuk komoditas benih tanaman tertentu.</p>
            </div>
            <button type="button" onclick="closeModal('modal-add-seed')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        <form id="form-add-seed"
              action="{{ route('steps.penanaman-bibit.seeds.store') }}"
              method="POST"
              class="mt-4 space-y-3.5">
            @csrf

            <div>
                <label for="add-seed-nama" class="block text-xs font-bold text-gray-700 mb-1">
                    Nama Komoditas Bibit <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="add-seed-nama"
                       name="nama_bibit"
                       required
                       placeholder="Misal: Bibit Wortel, Bibit Bawang Merah, Bibit Jagung"
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900 focus:bg-white focus:border-emerald-500">
            </div>

            <div>
                <label for="add-seed-varietas" class="block text-xs font-semibold text-gray-700 mb-1">
                    Varietas / Kultivar (Opsional)
                </label>
                <input type="text"
                       id="add-seed-varietas"
                       name="varietas"
                       placeholder="Misal: Kuroda, Chantenay, Ori 212, Servo F1"
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
            </div>

            <div>
                <label for="add-seed-deskripsi" class="block text-xs font-semibold text-gray-700 mb-1">
                    Catatan / Karakteristik Pembibitan (Opsional)
                </label>
                <textarea id="add-seed-deskripsi"
                          name="deskripsi"
                          rows="3"
                          placeholder="Jelaskan karakteristik benih atau catatan penting (misal: disemai tray atau ditabur langsung)..."
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-add-seed')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Bibit
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT JENIS BIBIT                                       --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-seed" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Edit Informasi Bibit</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui nama bibit, varietas, atau catatan pembibitan.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-seed')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        <form id="form-edit-seed"
              method="POST"
              class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-seed-nama" class="block text-xs font-bold text-gray-700 mb-1">
                    Nama Komoditas Bibit <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="edit-seed-nama"
                       name="nama_bibit"
                       required
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900 focus:bg-white focus:border-emerald-500">
            </div>

            <div>
                <label for="edit-seed-varietas" class="block text-xs font-semibold text-gray-700 mb-1">
                    Varietas / Kultivar (Opsional)
                </label>
                <input type="text"
                       id="edit-seed-varietas"
                       name="varietas"
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
            </div>

            <div>
                <label for="edit-seed-deskripsi" class="block text-xs font-semibold text-gray-700 mb-1">
                    Catatan / Karakteristik Pembibitan (Opsional)
                </label>
                <textarea id="edit-seed-deskripsi"
                          name="deskripsi"
                          rows="3"
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-edit-seed')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH LANGKAH PENANAMAN BIBIT                         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-step" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Tambah Langkah Penanaman Bibit</h3>
                <p id="add-step-subtitle" class="text-xs text-gray-500 mt-0.5">Tambahkan urutan tahapan SOP untuk bibit.</p>
            </div>
            <button type="button" onclick="closeModal('modal-add-step')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        <form id="form-add-step"
              method="POST"
              enctype="multipart/form-data"
              class="mt-4 space-y-4">
            @csrf

            {{-- Pilihan Bibit --}}
            <div>
                <label for="add-step-seed-id" class="block text-xs font-bold text-gray-700 mb-1">
                    Target Jenis Bibit <span class="text-rose-500">*</span>
                </label>
                <select id="add-step-seed-id"
                        onchange="updateAddStepFormAction(this.value)"
                        class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-800 bg-gray-50/50 focus:bg-white focus:border-emerald-500">
                    @foreach($seeds as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_bibit }} {{ $s->varietas ? '('.$s->varietas.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Nomor Langkah --}}
                <div class="sm:col-span-1">
                    <label for="add-nomor" class="block text-xs font-bold text-gray-700 mb-1">
                        Nomor Langkah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="add-nomor"
                           name="nomor"
                           required
                           placeholder="Misal: 1"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-emerald-900 bg-emerald-50/50 focus:bg-white focus:border-emerald-500">
                </div>

                {{-- Waktu / Estimasi --}}
                <div class="sm:col-span-2">
                    <label for="add-waktu" class="block text-xs font-semibold text-gray-700 mb-1">
                        Estimasi Waktu Pelaksanaan
                    </label>
                    <input type="text"
                           id="add-waktu"
                           name="waktu"
                           placeholder="Misal: H-25 s/d H-20 Sebelum Tanam"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                </div>
            </div>

            {{-- Judul Langkah --}}
            <div>
                <label for="add-judul" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Tahapan <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="add-judul"
                       name="judul"
                       required
                       placeholder="Misal: Seleksi & Pemilihan Benih Berkualitas"
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-semibold">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="add-deskripsi" class="block text-xs font-bold text-gray-700 mb-1">
                    Deskripsi Lengkap Langkah <span class="text-rose-500">*</span>
                </label>
                <textarea id="add-deskripsi"
                          name="deskripsi"
                          rows="3"
                          required
                          placeholder="Jelaskan secara rinci tindakan penanaman / pembibitan yang dilakukan..."
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Tips Praktisi --}}
            <div>
                <label for="add-tips" class="block text-xs font-semibold text-gray-700 mb-1">
                    Tips Praktisi Lapangan (Opsional)
                </label>
                <textarea id="add-tips"
                          name="tips"
                          rows="2"
                          placeholder="Tips pengalaman langsung petani di lapangan..."
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Upload Foto --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Dokumentasi Foto (Kamera HP Langsung / Unggah Galeri)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {{-- Opsi 1: Kamera HP Langsung --}}
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/50 hover:bg-amber-50 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-bold text-amber-900">Jepret Kamera HP Langsung</span>
                        <input type="file"
                               name="foto_kamera[]"
                               accept="image/*"
                               capture="environment"
                               onchange="previewFiles(this, 'add-step-preview')"
                               class="hidden">
                    </label>

                    {{-- Opsi 2: Dari Galeri --}}
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 bg-gray-50 hover:bg-emerald-50/40 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">Pilih dari Galeri / File</span>
                        <input type="file"
                               name="foto[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewFiles(this, 'add-step-preview')"
                               class="hidden">
                    </label>
                </div>

                {{-- Pratinjau Foto --}}
                <div id="add-step-preview-wrap" class="hidden pt-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5 px-0.5">
                        <span id="add-step-preview-count" class="font-semibold text-gray-700">Foto Terpilih:</span>
                        <button type="button" onclick="clearAllPreviewFiles('add-step-preview')" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold text-[11px] cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>
                    <div id="add-step-preview" class="grid grid-cols-4 gap-2"></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-add-step')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Tahapan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT LANGKAH PENANAMAN BIBIT                           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-step" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Edit Tahapan Penanaman</h3>
                <p class="text-xs text-gray-500 mt-0.5">Ubah nomor langkah, judul, estimasi waktu, deskripsi, tips, dan foto.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-step')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        <form id="form-edit-step"
              method="POST"
              enctype="multipart/form-data"
              class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Nomor Langkah --}}
                <div class="sm:col-span-1">
                    <label for="edit-nomor" class="block text-xs font-bold text-gray-700 mb-1">
                        Nomor Langkah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="edit-nomor"
                           name="nomor"
                           required
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-emerald-900 bg-emerald-50/50 focus:bg-white focus:border-emerald-500">
                </div>

                {{-- Waktu / Estimasi --}}
                <div class="sm:col-span-2">
                    <label for="edit-waktu" class="block text-xs font-semibold text-gray-700 mb-1">
                        Estimasi Waktu Pelaksanaan
                    </label>
                    <input type="text"
                           id="edit-waktu"
                           name="waktu"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                </div>
            </div>

            {{-- Judul Langkah --}}
            <div>
                <label for="edit-judul" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Tahapan <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="edit-judul"
                       name="judul"
                       required
                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-semibold">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="edit-deskripsi" class="block text-xs font-bold text-gray-700 mb-1">
                    Deskripsi Lengkap Langkah <span class="text-rose-500">*</span>
                </label>
                <textarea id="edit-deskripsi"
                          name="deskripsi"
                          rows="3"
                          required
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Tips Praktisi --}}
            <div>
                <label for="edit-tips" class="block text-xs font-semibold text-gray-700 mb-1">
                    Tips Praktisi Lapangan (Opsional)
                </label>
                <textarea id="edit-tips"
                          name="tips"
                          rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Foto yang sudah ada --}}
            <div id="edit-existing-photos-section" class="pt-2 border-t border-gray-100 hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Foto yang Tersimpan (Klik untuk menandai hapus):
                </label>
                <div id="edit-existing-photos-grid" class="grid grid-cols-4 gap-2"></div>
                <div id="edit-deleted-photos-container"></div>
            </div>

            {{-- Tambah Foto Baru --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Tambah Foto Baru (Kamera Langsung atau Galeri)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/50 hover:bg-amber-50 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-bold text-amber-900">Jepret Kamera HP Langsung</span>
                        <input type="file"
                               name="foto_kamera[]"
                               accept="image/*"
                               capture="environment"
                               onchange="previewFiles(this, 'edit-step-preview')"
                               class="hidden">
                    </label>

                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 bg-gray-50 hover:bg-emerald-50/40 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">Pilih dari Galeri / File</span>
                        <input type="file"
                               name="foto[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewFiles(this, 'edit-step-preview')"
                               class="hidden">
                    </label>
                </div>

                {{-- Pratinjau Foto Baru --}}
                <div id="edit-step-preview-wrap" class="hidden pt-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5 px-0.5">
                        <span id="edit-step-preview-count" class="font-semibold text-gray-700">Foto Baru Terpilih:</span>
                        <button type="button" onclick="clearAllPreviewFiles('edit-step-preview')" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold text-[11px] cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>
                    <div id="edit-step-preview" class="grid grid-cols-4 gap-2"></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-edit-step')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: JEPRET / UNGGAH FOTO CEPAT                             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-quick-photo" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tambah Foto Dokumentasi</h3>
                <p id="quick-photo-subtitle" class="text-xs text-gray-500 mt-0.5">Jepret langsung atau pilih foto</p>
            </div>
            <button type="button" onclick="closeModal('modal-quick-photo')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        <form id="form-quick-photo"
              method="POST"
              enctype="multipart/form-data"
              onsubmit="return validateQuickPhotoSubmit()"
              class="mt-4 space-y-4">
            @csrf

            <div class="space-y-2.5">
                {{-- Opsi 1: Kamera HP Langsung --}}
                <label class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/60 hover:bg-amber-50 rounded-2xl cursor-pointer transition-colors text-center group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-amber-950">Ambil Foto Pakai Kamera HP</span>
                    <span class="text-[11px] text-amber-700 mt-0.5">Langsung membuka kamera belakang perangkat</span>
                    <input type="file"
                           name="foto_kamera[]"
                           accept="image/*"
                           capture="environment"
                           onchange="previewFiles(this, 'quick-photo-preview')"
                           class="hidden">
                </label>

                {{-- Opsi 2: Dari Galeri --}}
                <label class="flex items-center justify-center gap-2 p-3 border border-gray-200 hover:border-gray-300 bg-gray-50 hover:bg-gray-100 rounded-xl cursor-pointer transition-colors text-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-700">Atau Pilih dari Galeri HP / Komputer</span>
                    <input type="file"
                           name="foto[]"
                           multiple
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           onchange="previewFiles(this, 'quick-photo-preview')"
                           class="hidden">
                </label>
            </div>

            {{-- Pratinjau Foto --}}
            <div id="quick-photo-preview-wrap" class="hidden pt-2">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5 px-0.5">
                    <span id="quick-photo-preview-count" class="font-semibold text-gray-700">Foto Terpilih:</span>
                    <button type="button" onclick="clearAllPreviewFiles('quick-photo-preview')" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold text-[11px] cursor-pointer">
                        Hapus Semua
                    </button>
                </div>
                <div id="quick-photo-preview" class="grid grid-cols-3 gap-2"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-quick-photo')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: LIHAT FOTO TAHAPAN                                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-view-step-photos" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
            <div>
                <h3 id="view-photos-title" class="text-base sm:text-lg font-bold text-gray-900">Foto Dokumentasi</h3>
                <p id="view-photos-subtitle" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-view-step-photos')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1 cursor-pointer">✕</button>
        </div>

        {{-- Grid Foto --}}
        <div id="view-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 my-4 overflow-y-auto max-h-[60vh] p-1"></div>

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 shrink-0">
            <button type="button"
                    id="view-photos-add-btn"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah / Jepret Foto</span>
            </button>
            <button type="button"
                    onclick="closeModal('modal-view-step-photos')"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: LIGHTBOX PRATINJAU FOTO BESAR                          --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/85 backdrop-blur-sm" onclick="closeLightbox(event)">
    <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center">
        <button type="button"
                onclick="closeModal('modal-lightbox')"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold p-1 cursor-pointer">
            ✕
        </button>
        <img id="lightbox-image"
             src=""
             alt="Foto Dokumentasi"
             class="max-w-full max-h-[80vh] rounded-2xl object-contain shadow-2xl border border-white/20">
        <p id="lightbox-title" class="text-white text-xs sm:text-sm font-semibold mt-3 text-center"></p>
    </div>
</div>

{{-- Hidden Form Delete untuk SweetAlert Helper --}}
<form id="generic-delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            el.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
            el.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
        if (id === 'modal-quick-photo') clearAllPreviewFiles('quick-photo-preview');
        if (id === 'modal-add-step') clearAllPreviewFiles('add-step-preview');
        if (id === 'modal-edit-step') clearAllPreviewFiles('edit-step-preview');
    }

    // ── Navigasi Card Bibit & Tampilan Detail Tahapan ──
    function openSeedDetail(seedId) {
        const gridView = document.getElementById('seeds-grid-view');
        const detailView = document.getElementById('seeds-detail-view');
        if (!gridView || !detailView) return;

        gridView.classList.add('hidden');
        detailView.classList.remove('hidden');

        // Sembunyikan semua panel langkah bibit
        document.querySelectorAll('.seed-steps-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Tampilkan panel langkah bibit yang dipilih
        const activePanel = document.getElementById('seed-steps-panel-' + seedId);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        // Perbarui highlight tab switcher bibit
        document.querySelectorAll('.seed-detail-tab').forEach(tab => {
            if (tab.getAttribute('data-seed-id') === String(seedId)) {
                tab.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                tab.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
            } else {
                tab.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                tab.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
            }
        });

        try {
            window.location.hash = 'seed-' + seedId;
        } catch (e) {}

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function backToSeedsGrid() {
        const gridView = document.getElementById('seeds-grid-view');
        const detailView = document.getElementById('seeds-detail-view');
        if (!gridView || !detailView) return;

        detailView.classList.add('hidden');
        gridView.classList.remove('hidden');

        try {
            history.replaceState(null, null, window.location.pathname + window.location.search);
        } catch (e) {}

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Auto-buka panel detail jika ada session flash active_seed_id atau URL hash
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('active_seed_id'))
            openSeedDetail({{ session('active_seed_id') }});
        @else
            const hash = window.location.hash;
            if (hash && hash.startsWith('#seed-')) {
                const sId = hash.replace('#seed-', '');
                if (document.getElementById('seed-steps-panel-' + sId)) {
                    openSeedDetail(sId);
                }
            }
        @endif
    });

    // ── Modal Bibit (Seed) ──
    function openAddSeedModal() {
        const form = document.getElementById('form-add-seed');
        if (form) form.reset();
        openModal('modal-add-seed');
    }

    function openEditSeedModal(seed) {
        const form = document.getElementById('form-edit-seed');
        if (form) {
            form.reset();
            form.action = '/steps/penanaman-bibit/seeds/' + seed.id;
        }
        document.getElementById('edit-seed-nama').value = seed.nama_bibit || '';
        document.getElementById('edit-seed-varietas').value = seed.varietas || '';
        document.getElementById('edit-seed-deskripsi').value = seed.deskripsi || '';
        openModal('modal-edit-seed');
    }

    function confirmDeleteSeed(seedId, seedName) {
        const form = document.getElementById('generic-delete-form');
        form.action = '/steps/penanaman-bibit/seeds/' + seedId;

        const handleDeleteSeedConfirmed = async () => {
            if (!navigator.onLine || String(seedId).startsWith('offline_')) {
                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.deletePlantingSeedOffline(seedId);
                }
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Komoditas bibit "${seedName}" berhasil dihapus secara offline.`);
                }
                await renderPlantingDataFromStore();
            } else {
                form.submit();
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Kartu Bibit Ini?',
                seedName,
                handleDeleteSeedConfirmed
            );
            return;
        }

        if (window.Swal) {
            Swal.fire({
                title: 'Hapus Kartu Bibit Ini?',
                html: `Apakah Anda yakin ingin menghapus <strong>"${seedName}"</strong> beserta seluruh tahapan dan foto dokumentasinya?<br><span class="text-xs text-gray-500">File foto fisik akan dihapus permanen dari server.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#E4574C',
                cancelButtonColor: '#6B7280'
            }).then((res) => {
                if (res.isConfirmed) handleDeleteSeedConfirmed();
            });
            return;
        }

        if (confirm('Hapus bibit ini beserta seluruh langkah dan foto dokumentasinya?')) {
            handleDeleteSeedConfirmed();
        }
    }

    // ── Modal Langkah (Step) ──
    function updateAddStepFormAction(seedId) {
        const form = document.getElementById('form-add-step');
        form.action = '/steps/penanaman-bibit/seeds/' + seedId + '/steps';
    }

    function openAddStepModal(seedId, seedName) {
        const form = document.getElementById('form-add-step');
        if (form) form.reset();
        clearAllPreviewFiles('add-step-preview');

        const select = document.getElementById('add-step-seed-id');
        if (seedId && select) {
            select.value = seedId;
            updateAddStepFormAction(seedId);
        } else if (select && select.value) {
            updateAddStepFormAction(select.value);
        }

        document.getElementById('add-step-subtitle').textContent = 'Tambahkan langkah SOP untuk ' + (seedName || 'bibit tanaman');
        openModal('modal-add-step');
    }

    function getStepPhotos(step) {
        if (!step) return [];
        if (step.foto_urls && Array.isArray(step.foto_urls) && step.foto_urls.length > 0) {
            return step.foto_urls;
        }
        if (step.foto && Array.isArray(step.foto)) {
            return step.foto.map(item => {
                if (typeof item === 'string') {
                    let u = item.trim();
                    if (!u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/') && !u.startsWith('data:')) {
                        u = '/storage/' + u.replace(/^\/+/, '');
                    }
                    return u;
                }
                if (item && typeof item === 'object') {
                    let u = item.url || item.secure_url || item.path || '';
                    if (u && !u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/') && !u.startsWith('data:')) {
                        u = '/storage/' + u.replace(/^\/+/, '');
                    }
                    return u;
                }
                return '';
            }).filter(Boolean);
        }
        return [];
    }

    function openEditStepModal(step) {
        const form = document.getElementById('form-edit-step');
        if (form) {
            form.reset();
            form.action = '/steps/penanaman-bibit/steps/' + step.id;
        }

        document.getElementById('edit-nomor').value = step.nomor || '';
        document.getElementById('edit-judul').value = step.judul || '';
        document.getElementById('edit-waktu').value = step.waktu || '';
        document.getElementById('edit-deskripsi').value = step.deskripsi || '';
        document.getElementById('edit-tips').value = step.tips || '';

        clearAllPreviewFiles('edit-step-preview');

        // Render foto yang sudah ada
        const existingSection = document.getElementById('edit-existing-photos-section');
        const existingGrid = document.getElementById('edit-existing-photos-grid');
        const deletedContainer = document.getElementById('edit-deleted-photos-container');
        deletedContainer.innerHTML = '';
        existingGrid.innerHTML = '';

        const photos = getStepPhotos(step);
        if (photos.length > 0) {
            existingSection.classList.remove('hidden');
            photos.forEach((url, i) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'relative rounded-xl overflow-hidden border border-gray-200 aspect-square group';
                itemDiv.id = 'existing-photo-item-' + i;
                itemDiv.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover">
                    <button type="button"
                            onclick="toggleDeleteExistingPhoto(this, '${url}', 'existing-photo-item-${i}')"
                            class="absolute top-1 right-1 px-1.5 py-0.5 rounded bg-black/60 hover:bg-rose-600 text-white text-[10px] font-bold transition-colors cursor-pointer">
                        Hapus
                    </button>
                `;
                existingGrid.appendChild(itemDiv);
            });
        } else {
            existingSection.classList.add('hidden');
        }

        openModal('modal-edit-step');
    }

    function toggleDeleteExistingPhoto(btn, url, elementId) {
        const deletedContainer = document.getElementById('edit-deleted-photos-container');
        const itemEl = document.getElementById(elementId);

        const existingInput = deletedContainer.querySelector(`input[value="${url}"]`);
        if (existingInput) {
            existingInput.remove();
            btn.classList.remove('bg-rose-600');
            btn.classList.add('bg-black/60');
            btn.textContent = 'Hapus';
            if (itemEl) itemEl.classList.remove('opacity-30', 'grayscale', 'ring-2', 'ring-rose-500');
        } else {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_photos[]';
            input.value = url;
            deletedContainer.appendChild(input);
            btn.classList.remove('bg-black/60');
            btn.classList.add('bg-rose-600');
            btn.textContent = 'Batal Hapus';
            if (itemEl) itemEl.classList.add('opacity-30', 'grayscale', 'ring-2', 'ring-rose-500');
        }
    }

    function confirmDeleteStep(stepId, nomor, judul) {
        const form = document.getElementById('generic-delete-form');
        form.action = '/steps/penanaman-bibit/steps/' + stepId;

        const handleDeleteStepConfirmed = async () => {
            if (!navigator.onLine || String(stepId).startsWith('offline_')) {
                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.deletePlantingStepOffline(stepId);
                }
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Langkah nomor ${nomor} ("${judul}") berhasil dihapus secara offline.`);
                }
                await renderPlantingDataFromStore();
            } else {
                form.submit();
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Tahapan Ini?',
                `Langkah ${nomor}: ${judul}`,
                handleDeleteStepConfirmed
            );
            return;
        }

        if (window.Swal) {
            Swal.fire({
                title: 'Hapus Tahapan Ini?',
                html: `Apakah Anda yakin ingin menghapus <strong>Langkah ${nomor}: "${judul}"</strong> beserta seluruh foto dokumentasinya?<br><span class="text-xs text-gray-500">File foto fisik akan dihapus permanen dari server.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#E4574C',
                cancelButtonColor: '#6B7280'
            }).then((res) => {
                if (res.isConfirmed) handleDeleteStepConfirmed();
            });
            return;
        }

        if (confirm(`Hapus langkah nomor ${nomor} ("${judul}")?`)) {
            handleDeleteStepConfirmed();
        }
    }

    // ── Modal Tambah Foto Cepat ──
    function openQuickPhotoModal(stepId, nomor, judul, seedName) {
        const form = document.getElementById('form-quick-photo');
        if (form) form.reset();
        form.action = '/steps/penanaman-bibit/steps/' + stepId + '/foto';
        document.getElementById('quick-photo-subtitle').textContent = (seedName ? seedName + ' - ' : '') + 'Langkah ' + nomor + ': ' + judul;
        clearAllPreviewFiles('quick-photo-preview');
        openModal('modal-quick-photo');
    }

    // ── Modal Lihat Foto & Hapus SweetAlert ──
    function openStepPhotosModal(step) {
        document.getElementById('view-photos-title').textContent = 'Foto Langkah ' + step.nomor + ': ' + step.judul;
        const photos = getStepPhotos(step);
        document.getElementById('view-photos-subtitle').textContent = photos.length + ' foto dokumentasi tersimpan';

        const grid = document.getElementById('view-photos-grid');
        grid.innerHTML = '';

        if (photos.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full py-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <p class="text-xs font-semibold text-gray-600">Belum ada foto dokumentasi untuk tahapan ini</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Klik tombol "Tambah / Jepret Foto" di bawah untuk menambahkan.</p>
                </div>
            `;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        photos.forEach((url, idx) => {
            const item = document.createElement('div');
            item.className = 'relative group/photo rounded-xl overflow-hidden border border-gray-200 bg-gray-100 aspect-square';
            const escapedTitle = (step.judul || '').replace(/'/g, "\\'");
            item.innerHTML = `
                <img src="${url}"
                     alt="Foto ${idx + 1}"
                     loading="lazy"
                     class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300"
                     onclick="openLightbox('${url}', 'Langkah ${step.nomor}: ${escapedTitle}')">

                {{-- Tombol Hapus Langsung (SweetAlert) --}}
                <form action="/steps/penanaman-bibit/steps/${step.id}/foto"
                      method="POST"
                      class="absolute top-1.5 right-1.5 z-20">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="photo_url" value="${url}">
                    <button type="button"
                            onclick="confirmDeleteStepPhoto(this)"
                            class="w-7 h-7 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer"
                            title="Hapus foto ini secara permanen">
                        <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                    </button>
                </form>

                {{-- Tombol Perbesar Foto --}}
                <button type="button"
                        onclick="openLightbox('${url}', 'Langkah ${step.nomor}: ${escapedTitle}')"
                        class="absolute bottom-1.5 right-1.5 p-1.5 rounded-lg bg-black/60 hover:bg-black/80 text-white text-xs shadow-xs transition-colors z-10 cursor-pointer"
                        title="Perbesar foto">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                    </svg>
                </button>
            `;
            grid.appendChild(item);
        });

        const addBtn = document.getElementById('view-photos-add-btn');
        addBtn.onclick = function() {
            closeModal('modal-view-step-photos');
            openQuickPhotoModal(step.id, step.nomor, step.judul, '');
        };

        openModal('modal-view-step-photos');
    }

    function confirmDeleteStepPhoto(btn) {
        const form = btn.closest('form');
        if (!form) return;
        const photoUrl = form.querySelector('input[name="photo_url"]')?.value;
        const actionUrl = form.getAttribute('action') || '';
        const parts = actionUrl.split('/');
        const stepId = parts[parts.length - 2];

        const doDeletePhoto = async () => {
            if (!navigator.onLine || String(stepId).startsWith('offline_')) {
                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.deletePlantingStepPhotoOffline(stepId, photoUrl);
                }
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess('Foto berhasil dihapus secara offline.');
                }
                closeModal('modal-view-step-photos');
                await renderPlantingDataFromStore();
            } else {
                form.submit();
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Foto Dokumentasi?',
                'Foto dokumentasi ini beserta file fisiknya di server',
                doDeletePhoto
            );
            return;
        }

        if (window.Swal) {
            Swal.fire({
                title: 'Hapus Foto Dokumentasi?',
                html: 'Apakah Anda yakin ingin menghapus foto dokumentasi ini?<br><span class="text-xs text-gray-500">File fisik foto akan dihapus permanen dari server penyimpanan.</span>',
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#E4574C',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) doDeletePhoto();
            });
            return;
        }

        if (confirm('Hapus foto ini dari dokumentasi tahapan?')) {
            doDeletePhoto();
        }
    }

    // ── Lightbox ──
    function openLightbox(url, title) {
        document.getElementById('lightbox-image').src = url;
        document.getElementById('lightbox-title').textContent = title || '';
        openModal('modal-lightbox');
    }

    function closeLightbox(event) {
        if (event.target.id === 'modal-lightbox') {
            closeModal('modal-lightbox');
        }
    }

    // ── Preview File Upload & Pembatalan via DataTransfer ──
    window.previewStore = window.previewStore || {};

    function previewFiles(input, containerId) {
        if (!input.files || input.files.length === 0) return;

        if (!window.previewStore[containerId]) {
            window.previewStore[containerId] = [];
        }

        const validFiles = Array.from(input.files).filter(file => file.type.startsWith('image/'));
        if (validFiles.length === 0) return;

        let loaded = 0;
        const newItems = validFiles.map((file, idx) => ({
            id: 'f_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9) + '_' + idx,
            file: file,
            input: input,
            name: file.name,
            url: ''
        }));

        newItems.forEach(item => {
            const reader = new FileReader();
            reader.onload = function(e) {
                item.url = e.target.result;
                loaded++;
                if (loaded === newItems.length) {
                    window.previewStore[containerId].push(...newItems);
                    syncInputFiles(containerId);
                    renderPreviews(containerId);
                }
            };
            reader.onerror = function() {
                loaded++;
                if (loaded === newItems.length) {
                    window.previewStore[containerId].push(...newItems.filter(i => i.url));
                    syncInputFiles(containerId);
                    renderPreviews(containerId);
                }
            };
            reader.readAsDataURL(item.file);
        });
    }

    function removePreviewFile(containerId, fileId) {
        if (!window.previewStore || !window.previewStore[containerId]) return;
        window.previewStore[containerId] = window.previewStore[containerId].filter(item => item.id !== fileId);
        syncInputFiles(containerId);
        renderPreviews(containerId);
    }

    function clearAllPreviewFiles(containerId) {
        if (!window.previewStore) window.previewStore = {};
        window.previewStore[containerId] = [];
        syncInputFiles(containerId);
        renderPreviews(containerId);
    }

    function syncInputFiles(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const form = container.closest('form');
        if (!form) return;

        const items = window.previewStore ? (window.previewStore[containerId] || []) : [];
        const fileInputs = form.querySelectorAll('input[type="file"]');

        fileInputs.forEach(fileInput => {
            const matchingItems = items.filter(item => item.input === fileInput);
            if (matchingItems.length > 0) {
                try {
                    const dt = new DataTransfer();
                    matchingItems.forEach(item => dt.items.add(item.file));
                    fileInput.files = dt.files;
                } catch (e) {
                    console.warn('DataTransfer not fully supported:', e);
                }
            } else {
                fileInput.value = '';
            }
        });
    }

    function renderPreviews(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const wrap = document.getElementById(containerId + '-wrap') || container;
        const countEl = document.getElementById(containerId + '-count');
        const items = window.previewStore ? (window.previewStore[containerId] || []) : [];

        container.innerHTML = '';

        if (items.length === 0) {
            wrap.classList.add('hidden');
            if (countEl) countEl.textContent = '';
            return;
        }

        wrap.classList.remove('hidden');
        if (countEl) {
            countEl.textContent = items.length + ' foto dipilih:';
        }

        items.forEach(item => {
            const wrapper = document.createElement('div');
            wrapper.className = 'group relative rounded-xl overflow-hidden border border-emerald-300 aspect-square shadow-2xs bg-gray-100';
            wrapper.innerHTML = `
                <img src="${item.url}" alt="${item.name}" class="w-full h-full object-cover">
                <button type="button"
                        onclick="removePreviewFile('${containerId}', '${item.id}')"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition-all hover:scale-110 active:scale-95 z-20 cursor-pointer"
                        title="Hapus foto ini dari pilihan">
                    <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-black/65 text-white text-[9px] font-semibold truncate max-w-[85%] backdrop-blur-2xs select-none">
                    ${item.name}
                </span>
            `;
            container.appendChild(wrapper);
        });
    }

    function validateQuickPhotoSubmit() {
        const items = window.previewStore ? (window.previewStore['quick-photo-preview'] || []) : [];
        if (items.length === 0) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    iconColor: '#D97706',
                    title: 'Foto Belum Dipilih',
                    text: 'Silakan pilih dari galeri atau jepret foto dokumentasi terlebih dahulu.',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#059669'
                });
            } else {
                alert('Silakan pilih atau jepret foto dokumentasi terlebih dahulu.');
            }
            return false;
        }
        return true;
    }

    // Tutup modal jika tombol ESC ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['modal-add-seed', 'modal-edit-seed', 'modal-add-step', 'modal-edit-step', 'modal-quick-photo', 'modal-view-step-photos', 'modal-lightbox'].forEach(closeModal);
        }
    });

    // ══════════════════════════════════════════════════════════════════
    // OFFLINE SUPPORT & CLIENT-SIDE RENDERING
    // ══════════════════════════════════════════════════════════════════
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    async function renderPlantingDataFromStore() {
        if (!window.AgriOfflineStore) return;
        const seeds = await AgriOfflineStore.getAllPlantingSeedsWithSteps();
        if (!seeds || seeds.length === 0) return;

        // 1. Update counter
        const counter = document.getElementById('seeds-counter');
        if (counter) {
            counter.textContent = seeds.length + ' Komoditas Bibit';
        }

        // Cari tahu bibit yang sedang aktif saat ini
        let currentActiveSeedId = null;
        document.querySelectorAll('.seed-steps-panel').forEach(p => {
            if (!p.classList.contains('hidden')) {
                currentActiveSeedId = p.getAttribute('data-seed-id');
            }
        });

        // 2. Render Seeds Grid
        const grid = document.getElementById('seeds-grid');
        if (grid) {
            let gridHtml = '';
            seeds.forEach(seed => {
                const steps = seed.steps || [];
                const stepCount = steps.length;
                let photoCount = 0;
                steps.forEach(st => {
                    const ph = getStepPhotos(st);
                    photoCount += ph.length;
                });

                const isDraft = seed.is_synced === false;
                const draftBadge = isDraft
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Offline Draft</span>`
                    : '';

                const varietasBadge = seed.varietas
                    ? `<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-100">${escapeHtml(seed.varietas)}</span>`
                    : '';

                const descText = seed.deskripsi
                    ? `<p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed">${escapeHtml(seed.deskripsi)}</p>`
                    : '';

                const photoBadge = photoCount > 0
                    ? `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600">
                           <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                           </svg>
                           ${photoCount} Foto
                       </span>`
                    : '';

                gridHtml += `
                    <div class="seed-card group relative bg-white rounded-2xl border border-gray-200/90 hover:border-emerald-500 hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col justify-between overflow-hidden"
                         onclick="openSeedDetail('${seed.id}')"
                         id="seed-card-${seed.id}"
                         data-seed-id="${seed.id}">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center font-bold shrink-0 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                        <button type="button"
                                                onclick='openEditSeedModal(${JSON.stringify(seed).replace(/'/g, "&#39;")})'
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                                title="Edit Informasi Bibit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                onclick="confirmDeleteSeed('${seed.id}', '${escapeHtml(seed.nama_bibit)}')"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus Bibit Ini">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition-colors break-words">
                                            ${escapeHtml(seed.nama_bibit)}
                                        </h3>
                                        ${draftBadge}
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                                        ${varietasBadge}
                                    </div>
                                    ${descText}
                                </div>
                            </div>
                            <div class="mt-4 pt-3.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 font-bold text-gray-700 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                        ${stepCount} Tahapan
                                    </span>
                                    ${photoBadge}
                                </div>
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition-all">
                                    <span>Lihat Tahapan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });

            gridHtml += `
                <div onclick="openAddSeedModal()"
                     class="rounded-2xl border-2 border-dashed border-gray-300 hover:border-emerald-500 hover:bg-emerald-50/30 transition-all p-6 flex flex-col items-center justify-center text-center cursor-pointer min-h-[170px] group">
                    <div class="w-11 h-11 rounded-xl bg-gray-100 group-hover:bg-emerald-100 text-gray-400 group-hover:text-emerald-700 flex items-center justify-center transition-all mb-2.5">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-800 transition-colors">Tambah Jenis Bibit Baru</span>
                    <span class="text-xs text-gray-400 mt-0.5">Wortel, Cabai, Bawang, Tomat, dll.</span>
                </div>
            `;
            grid.innerHTML = gridHtml;
        }

        // 3. Render Detail Panels
        const panelsContainer = document.getElementById('seed-panels-container');
        if (panelsContainer) {
            let panelsHtml = '';
            seeds.forEach(seed => {
                const steps = seed.steps || [];
                const isHidden = (String(seed.id) !== String(currentActiveSeedId)) ? 'hidden' : '';

                const varietasBadge = seed.varietas
                    ? `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">Varietas: ${escapeHtml(seed.varietas)}</span>`
                    : '';

                const descText = seed.deskripsi
                    ? `<p class="text-xs sm:text-sm text-gray-600 mt-2 max-w-2xl">${escapeHtml(seed.deskripsi)}</p>`
                    : '';

                let stepsHtml = '';
                if (steps.length === 0) {
                    stepsHtml = `
                        <div class="py-12 text-center text-gray-400">
                            <p class="text-sm font-semibold text-gray-600">Belum ada tahapan operasional untuk bibit ini.</p>
                            <p class="text-xs text-gray-400 mt-0.5">Klik tombol "+ Tambah Langkah Baru" di atas untuk mulai membuat SOP penanaman.</p>
                        </div>
                    `;
                } else {
                    steps.forEach(st => {
                        const photos = getStepPhotos(st);
                        const isDraftStep = st.is_synced === false;
                        const draftStepBadge = isDraftStep
                            ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Offline Draft</span>`
                            : '';

                        const waktuBadge = st.waktu
                            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">Waktu: ${escapeHtml(st.waktu)}</span>`
                            : '';

                        const tipsBlock = st.tips
                            ? `<div class="bg-emerald-50/70 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-0.5">
                                   <div class="flex items-center gap-1.5 font-bold text-emerald-950">
                                       <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.002 6.002 0 00-5.918-5.074A5.992 5.992 0 006 9c0 1.904.887 3.601 2.27 4.707L8.25 18h7.5l-.02-4.293A5.992 5.992 0 0018 9c0-.705-.121-1.382-.345-2.008A6.002 6.002 0 0012 12.75z"/>
                                       </svg>
                                       <span>Tips Praktisi Lapangan:</span>
                                   </div>
                                   <p class="text-emerald-900 leading-relaxed whitespace-pre-line">${escapeHtml(st.tips)}</p>
                               </div>`
                            : '';

                        const viewPhotosBtn = photos.length > 0
                            ? `<button type="button"
                                       onclick='openStepPhotosModal(${JSON.stringify(st).replace(/'/g, "&#39;")})'
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-colors cursor-pointer">
                                   <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                       <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                   </svg>
                                   <span>Lihat Foto (${photos.length})</span>
                               </button>`
                            : `<span class="text-[11px] text-gray-400 italic">Belum ada foto</span>`;

                        const quickPhotoBtn = `<button type="button"
                                   onclick="openQuickPhotoModal('${st.id}', '${escapeHtml(st.nomor)}', '${escapeHtml(st.judul)}', '${escapeHtml(seed.nama_bibit)}')"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-300 text-amber-900 bg-amber-50 hover:bg-amber-100 text-[11px] font-bold transition-all w-fit cursor-pointer">
                               <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                               </svg>
                               <span>+ Foto Cepat</span>
                           </button>`;

                        stepsHtml += `
                            <div class="p-4 sm:p-5 rounded-2xl border border-gray-200/90 bg-white hover:border-emerald-300 hover:shadow-xs transition-all space-y-3" id="step-card-${st.id}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border border-emerald-200">
                                            ${escapeHtml(st.nomor)}
                                        </div>
                                        <div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h3 class="text-sm sm:text-base font-bold text-gray-900">
                                                        ${escapeHtml(st.judul)}
                                                    </h3>
                                                    ${draftStepBadge}
                                                </div>
                                                ${waktuBadge}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button"
                                                onclick='openEditStepModal(${JSON.stringify(st).replace(/'/g, "&#39;")})'
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-semibold transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                            <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <button type="button"
                                                onclick="confirmDeleteStep('${st.id}', '${escapeHtml(st.nomor)}', '${escapeHtml(st.judul)}')"
                                                class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus langkah ini">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                                    ${escapeHtml(st.deskripsi)}
                                </p>
                                ${tipsBlock}
                                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-gray-100">
                                    <div class="flex items-center gap-2">
                                        ${viewPhotosBtn}
                                    </div>
                                    ${quickPhotoBtn}
                                </div>
                            </div>
                        `;
                    });
                }

                panelsHtml += `
                    <div class="seed-steps-panel space-y-6 ${isHidden}"
                         id="seed-steps-panel-${seed.id}"
                         data-seed-id="${seed.id}">
                        <div class="bg-surface rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                            <div class="p-5 sm:p-6 bg-gradient-to-r from-emerald-50/70 via-white to-amber-50/40 border-b border-gray-100">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">
                                                    Tahapan Penanaman: ${escapeHtml(seed.nama_bibit)}
                                                </h2>
                                                ${varietasBadge}
                                            </div>
                                            ${descText}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <button type="button"
                                                onclick="openAddStepModal('${seed.id}', '${escapeHtml(seed.nama_bibit)}')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                            <span>+ Tambah Langkah Baru</span>
                                        </button>
                                        <button type="button"
                                                onclick='openEditSeedModal(${JSON.stringify(seed).replace(/'/g, "&#39;")})'
                                                class="p-2 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-700 transition-colors cursor-pointer"
                                                title="Edit Data Bibit Ini">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                onclick="confirmDeleteSeed('${seed.id}', '${escapeHtml(seed.nama_bibit)}')"
                                                class="p-2 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 transition-colors cursor-pointer"
                                                title="Hapus Kartu Bibit Ini">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 sm:p-6 space-y-4">
                                ${stepsHtml}
                            </div>
                        </div>
                    </div>
                `;
            });

            panelsContainer.innerHTML = panelsHtml;
        }
    }

    // Intercept Add Seed Form
    const addSeedForm = document.getElementById('form-add-seed');
    if (addSeedForm) {
        addSeedForm.addEventListener('submit', async function(e) {
            if (!navigator.onLine) {
                e.preventDefault();
                const formData = new FormData(addSeedForm);
                const data = {
                    nama_bibit: formData.get('nama_bibit') || 'Bibit Baru',
                    varietas: formData.get('varietas') || '',
                    deskripsi: formData.get('deskripsi') || ''
                };
                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.addPlantingSeedOffline(data);
                }
                closeModal('modal-add-seed');
                addSeedForm.reset();
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Komoditas bibit "${data.nama_bibit}" berhasil disimpan di HP (Mode Offline).`);
                }
                await renderPlantingDataFromStore();
            }
        });
    }

    // Intercept Edit Seed Form
    const editSeedForm = document.getElementById('form-edit-seed');
    if (editSeedForm) {
        editSeedForm.addEventListener('submit', async function(e) {
            const actionUrl = editSeedForm.getAttribute('action') || '';
            const parts = actionUrl.split('/');
            const seedId = parts[parts.length - 1];

            if (!navigator.onLine || String(seedId).startsWith('offline_')) {
                e.preventDefault();
                const formData = new FormData(editSeedForm);
                const data = {
                    nama_bibit: formData.get('nama_bibit') || '',
                    varietas: formData.get('varietas') || '',
                    deskripsi: formData.get('deskripsi') || ''
                };
                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.updatePlantingSeedOffline(seedId, data);
                }
                closeModal('modal-edit-seed');
                editSeedForm.reset();
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Perubahan komoditas bibit "${data.nama_bibit}" berhasil disimpan di HP (Mode Offline).`);
                }
                await renderPlantingDataFromStore();
            }
        });
    }

    // Intercept Add Step Form
    const addPlantingStepForm = document.getElementById('form-add-step');
    if (addPlantingStepForm) {
        addPlantingStepForm.addEventListener('submit', async function(e) {
            const actionUrl = addPlantingStepForm.getAttribute('action') || '';
            const parts = actionUrl.split('/');
            // /steps/penanaman-bibit/seeds/{seed}/steps -> seedId is parts[parts.length - 2]
            const seedId = parts[parts.length - 2];

            if (!navigator.onLine || String(seedId).startsWith('offline_')) {
                e.preventDefault();
                const formData = new FormData(addPlantingStepForm);
                const photosBase64 = [];
                const items = window.previewStore ? (window.previewStore['add-step-preview'] || []) : [];
                items.forEach(item => {
                    if (item.url) photosBase64.push(item.url);
                });

                const data = {
                    planting_seed_id: seedId,
                    nomor: formData.get('nomor') || '1',
                    judul: formData.get('judul') || 'Langkah Baru',
                    waktu: formData.get('waktu') || null,
                    deskripsi: formData.get('deskripsi') || '-',
                    tips: formData.get('tips') || null,
                    photos_base64: photosBase64
                };

                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.addPlantingStepOffline(seedId, data);
                }
                closeModal('modal-add-step');
                addPlantingStepForm.reset();
                clearAllPreviewFiles('add-step-preview');
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Langkah nomor ${data.nomor} ("${data.judul}") berhasil disimpan di HP (Mode Offline).`);
                }
                await renderPlantingDataFromStore();
                openSeedDetail(seedId);
            }
        });
    }

    // Intercept Edit Step Form
    const editPlantingStepForm = document.getElementById('form-edit-step');
    if (editPlantingStepForm) {
        editPlantingStepForm.addEventListener('submit', async function(e) {
            const actionUrl = editPlantingStepForm.getAttribute('action') || '';
            const parts = actionUrl.split('/');
            const stepId = parts[parts.length - 1];

            if (!navigator.onLine || String(stepId).startsWith('offline_')) {
                e.preventDefault();
                const formData = new FormData(editPlantingStepForm);
                const photosBase64 = [];
                const items = window.previewStore ? (window.previewStore['edit-step-preview'] || []) : [];
                items.forEach(item => {
                    if (item.url) photosBase64.push(item.url);
                });

                const deletedPhotos = [];
                editPlantingStepForm.querySelectorAll('input[name="deleted_photos[]"]').forEach(inp => {
                    if (inp.value) deletedPhotos.push(inp.value);
                });

                const data = {
                    nomor: formData.get('nomor') || '1',
                    judul: formData.get('judul') || 'Langkah Baru',
                    waktu: formData.get('waktu') || null,
                    deskripsi: formData.get('deskripsi') || '-',
                    tips: formData.get('tips') || null,
                    deleted_photos: deletedPhotos,
                    photos_base64: photosBase64
                };

                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.updatePlantingStepOffline(stepId, data);
                }
                closeModal('modal-edit-step');
                editPlantingStepForm.reset();
                clearAllPreviewFiles('edit-step-preview');
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`Perubahan langkah nomor ${data.nomor} berhasil disimpan di HP (Mode Offline).`);
                }
                await renderPlantingDataFromStore();
            }
        });
    }

    // Intercept Quick Photo Form
    const quickPhotoStepForm = document.getElementById('form-quick-photo');
    if (quickPhotoStepForm) {
        quickPhotoStepForm.addEventListener('submit', async function(e) {
            const actionUrl = quickPhotoStepForm.getAttribute('action') || '';
            const parts = actionUrl.split('/');
            // /steps/penanaman-bibit/steps/{step}/foto -> stepId is parts[parts.length - 2]
            const stepId = parts[parts.length - 2];

            if (!navigator.onLine || String(stepId).startsWith('offline_')) {
                e.preventDefault();
                if (!validateQuickPhotoSubmit()) return;

                const photosBase64 = [];
                const items = window.previewStore ? (window.previewStore['quick-photo-preview'] || []) : [];
                items.forEach(item => {
                    if (item.url) photosBase64.push(item.url);
                });

                if (window.AgriOfflineStore) {
                    await AgriOfflineStore.uploadPlantingStepPhotosOffline(stepId, photosBase64);
                }
                closeModal('modal-quick-photo');
                quickPhotoStepForm.reset();
                clearAllPreviewFiles('quick-photo-preview');
                if (window.AgriSwal) {
                    AgriSwal.toastSuccess(`${photosBase64.length} foto berhasil ditambahkan ke langkah di HP (Mode Offline).`);
                }
                await renderPlantingDataFromStore();
            }
        });
    }

    // Cache server data & initialize offline listener
    document.addEventListener('DOMContentLoaded', async function() {
        if (window.AgriOfflineStore) {
            try {
                @if(isset($seeds) && count($seeds) > 0)
                    await AgriOfflineStore.cachePlantingData(@json($seeds));
                @endif

                const pending = await AgriOfflineStore.getPendingCount();
                if (!navigator.onLine || pending > 0) {
                    await renderPlantingDataFromStore();
                }
            } catch (err) {
                console.warn('Error syncing offline planting data:', err);
            }
        }
    });

    window.addEventListener('agri:data-changed', async () => {
        await renderPlantingDataFromStore();
    });
</script>
@endsection
