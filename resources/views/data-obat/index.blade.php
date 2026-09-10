@extends('layouts.app')

@section('title', 'Data Obat Tanaman')

@section('content')

<div class="w-full max-w-full min-w-0 overflow-x-hidden">

    {{-- ── Header Section (Sleek, Clean, Solid Colors, No Gradient) ── --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-gray-200 w-full min-w-0">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Data Obat Tanaman</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                    {{ $medicines->total() }} Obat
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 truncate">Kelola fungisida, insektisida, pupuk, dan vitamin kebun.</p>
        </div>

        <a href="/data-obat/tambah"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-xs sm:text-sm font-bold hover:bg-primary-dark active:scale-95 transition-all shadow-sm shrink-0 box-border">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Obat Baru
        </a>
    </div>

    {{-- ── Flash message ── --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold animate-slide-up shadow-sm w-full min-w-0">
            <div class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
            <span class="flex-1 min-w-0 truncate">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 p-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- ── Search & Filter Bar (Mobile-Native UX, No Clutter, No Overflow) ── --}}
    <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-3.5 sm:p-4 mb-5 space-y-3 w-full max-w-full min-w-0 overflow-hidden box-border">
        {{-- Search Bar --}}
        <form method="GET" action="/data-obat" class="flex gap-2 w-full min-w-0">
            <input type="hidden" name="jenis" value="{{ request('jenis') }}">
            <input type="hidden" name="cara_kerja" value="{{ request('cara_kerja') }}">
            <div class="relative flex-1 min-w-0">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text"
                       id="search-input"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama obat, sasaran, bahan..."
                       class="w-full pl-10 pr-9 py-2 rounded-xl border border-gray-300 bg-page text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-primary focus:bg-white transition-all box-border">
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all shrink-0">
                Cari
            </button>
        </form>

        {{-- Horizontal Scrollable Category Filter Pills --}}
        <div class="w-full max-w-full min-w-0 overflow-hidden">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar w-full max-w-full">
                @php
                    $currentJenis = request('jenis');
                    $kategoriList = [
                        '' => 'Semua',
                        'Fungisida' => 'Fungisida',
                        'Insektisida' => 'Insektisida',
                        'Pupuk' => 'Pupuk',
                        'Vitamin' => 'Vitamin',
                        'Bibit' => 'Bibit',
                        'Perlengkapan' => 'Perlengkapan',
                        'Peralatan' => 'Peralatan',
                    ];
                @endphp

                <!-- <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider shrink-0 mr-1">Kategori:</span> -->

                @foreach($kategoriList as $key => $label)
                    @php $isActive = ($currentJenis === $key) || ($key === '' && empty($currentJenis)); @endphp
                    <a href="{{ request()->fullUrlWithQuery(['jenis' => $key ?: null]) }}"
                       class="px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 {{ $isActive ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Horizontal Scrollable Cara Kerja Filter Pills --}}
        <div class="w-full max-w-full min-w-0 overflow-hidden pt-1 border-t border-gray-100">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar w-full max-w-full">
                @php
                    $currentCk = request('cara_kerja');
                    $ckList = [
                        '' => 'Semua Cara Kerja',
                        'Sistemik' => 'Sistemik',
                        'Kontak' => 'Kontak',
                        'Sistemik + Kontak' => 'Sistemik + Kontak',
                    ];
                @endphp

                <!-- <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider shrink-0 mr-1">Cara Kerja:</span>
                  -->

                @foreach($ckList as $key => $label)
                    @php $isActive = ($currentCk === $key) || ($key === '' && empty($currentCk)); @endphp
                    <a href="{{ request()->fullUrlWithQuery(['cara_kerja' => $key ?: null]) }}"
                       class="px-2.5 py-1 rounded-lg font-semibold transition-all shrink-0 {{ $isActive ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach

                @if(request('search') || request('jenis') || request('cara_kerja'))
                    <a href="/data-obat" class="ml-auto text-xs font-semibold text-red-600 hover:underline shrink-0 pl-2">
                        Reset Filter
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Data Listing ── --}}
    @if ($medicines->isEmpty())
        {{-- Empty state --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-8 text-center w-full min-w-0">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Data Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 max-w-xs mx-auto mb-4">
                Tidak ada obat yang cocok dengan kata kunci atau filter yang Anda pilih.
            </p>
            <a href="/data-obat" class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200">
                Tampilkan Semua Obat
            </a>
        </div>

    @else

        {{-- Helper function for badge colors (Solid Colors Only, No Gradient) --}}
        @php
            $getCategoryBadge = function($jenis) {
                return match($jenis) {
                    'Fungisida'    => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                    'Insektisida'  => ['bg' => 'bg-rose-100',   'text' => 'text-rose-800'],
                    'Pupuk'        => ['bg' => 'bg-emerald-100','text' => 'text-emerald-800'],
                    'Vitamin'      => ['bg' => 'bg-teal-100',   'text' => 'text-teal-800'],
                    'Bibit'        => ['bg' => 'bg-lime-100',   'text' => 'text-lime-800'],
                    'Perlengkapan' => ['bg' => 'bg-amber-100',  'text' => 'text-amber-800'],
                    'Peralatan'    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800'],
                    default        => ['bg' => 'bg-gray-100',   'text' => 'text-gray-800'],
                };
            };

            $getCaraKerjaBadge = function($ck) {
                return match($ck) {
                    'Sistemik'          => ['bg' => 'bg-sky-100',   'text' => 'text-sky-800'],
                    'Kontak'            => ['bg' => 'bg-orange-100','text' => 'text-orange-800'],
                    'Sistemik + Kontak' => ['bg' => 'bg-indigo-100','text' => 'text-indigo-800'],
                    default             => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600'],
                };
            };
        @endphp

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ── MOBILE VIEW: High-End Native Mobile Cards (Solid, Clean) ── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:hidden space-y-3 w-full max-w-full min-w-0" id="mobile-cards-list">
            @foreach ($medicines as $medicine)
                @php
                    $catBadge = $getCategoryBadge($medicine->jenis);
                    $ckBadge  = $getCaraKerjaBadge($medicine->cara_kerja);
                @endphp

                <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-4 space-y-3 relative w-full max-w-full min-w-0 overflow-hidden box-border"
                     id="medicine-card-{{ $medicine->id }}">

                    {{-- Row 1: Badges Kategori + Cara Kerja & Harga Obat (Kuning Prioritas) --}}
                    <div class="flex items-center justify-between gap-2 w-full min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap min-w-0 flex-1">
                            {{-- Kategori (Kuning) --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $catBadge['bg'] }} {{ $catBadge['text'] }} shrink-0">
                                {{ $medicine->jenis }}
                            </span>

                            {{-- Cara Kerja (Kuning) --}}
                            @if ($medicine->cara_kerja)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $ckBadge['bg'] }} {{ $ckBadge['text'] }} shrink-0">
                                    {{ $medicine->cara_kerja }}
                                </span>
                            @endif
                        </div>

                        {{-- Harga Obat (Kuning Prioritas) --}}
                        <div class="text-right shrink-0">
                            @if ($medicine->purchases && $medicine->purchases->count() > 1)
                                <div class="space-y-1 text-right">
                                    @foreach ($medicine->purchases as $p)
                                        <div class="flex items-center justify-end gap-1">
                                            <span class="text-xs font-bold text-green-700 tabular-nums">
                                                {{ $p->formatted_harga ?: '—' }}
                                            </span>
                                            @if ($p->toko_obat)
                                                <span class="text-[10px] text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200/60">
                                                    {{ $p->toko_obat }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($medicine->purchases && $medicine->purchases->count() === 1)
                                @php $p = $medicine->purchases->first(); @endphp
                                <span class="text-sm font-bold text-green-600 tracking-tight tabular-nums whitespace-nowrap block">
                                    {{ $p->formatted_harga ?: ($medicine->formatted_harga ?: '—') }}
                                </span>
                                @if ($p->toko_obat ?? $medicine->toko_obat)
                                    <span class="text-[10px] text-gray-500 block">{{ $p->toko_obat ?? $medicine->toko_obat }}</span>
                                @endif
                            @elseif ($medicine->harga)
                                <span class="text-sm font-bold text-green-600 tracking-tight tabular-nums whitespace-nowrap block">
                                    {{ $medicine->formatted_harga }}
                                </span>
                                @if ($medicine->toko_obat)
                                    <span class="text-[10px] text-gray-500 block">{{ $medicine->toko_obat }}</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs font-medium block">
                                    Harga: —
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: Nama Obat (Kuning Prioritas) & Sasaran Obat --}}
                    <div class="w-full min-w-0 space-y-1.5">
                        <h2 class="text-base font-bold text-gray-900 leading-snug tracking-tight break-words">
                            {{ $medicine->nama }}
                        </h2>
                        @if ($medicine->sasaran_obat)
                            <div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 border border-emerald-200/80 text-emerald-900 break-words">
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase">Sasaran:</span>
                                    {{ $medicine->sasaran_obat }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Row 3: Grid 2 Kolom Prioritas (Tanaman Sasaran & Dosis Pemakaian) --}}
                    <div class="bg-page rounded-xl p-3 border border-gray-200/70 grid grid-cols-2 gap-2.5 w-full min-w-0 overflow-hidden box-border">
                        {{-- Rekomendasi Tanaman --}}
                        <div class="min-w-0 overflow-hidden">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tanaman Sasaran</span>
                            <p class="text-xs font-bold text-gray-800 mt-0.5 break-words line-clamp-2" title="{{ $medicine->tanaman_sasaran }}">
                                {{ $medicine->tanaman_sasaran }}
                            </p>
                        </div>

                        {{-- Dosis Pemakaian --}}
                        <div class="min-w-0 overflow-hidden">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Dosis</span>
                            <p class="text-xs font-bold text-gray-800 mt-0.5 break-words font-mono line-clamp-2" title="{{ $medicine->dosis_anjuran ?? '—' }}">
                                {{ $medicine->dosis_anjuran ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Row 4: Expandable Accordion untuk Detail Sekunder --}}
                    @php
                        $hasExtraInfo = $medicine->unsur_bahan || $medicine->fase || $medicine->toko_obat || $medicine->tanggal_beli || $medicine->keterangan || ($medicine->purchases && $medicine->purchases->count() > 0) || $medicine->foto_nota;
                    @endphp

                    @if ($hasExtraInfo)
                        <div class="w-full min-w-0">
                            <button type="button"
                                    onclick="toggleDetails({{ $medicine->id }})"
                                    class="w-full flex items-center justify-between py-1 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                                <span>Info Lengkap & Riwayat Toko</span>
                                <svg id="chevron-{{ $medicine->id }}" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <div id="details-{{ $medicine->id }}" class="hidden mt-2 p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-2.5 text-xs w-full min-w-0 box-border">
                                @php $mPhotoUrls = $medicine->foto_urls; @endphp
                                @if (!empty($mPhotoUrls))
                                    <div class="mb-2">
                                        <span class="font-bold text-gray-500 block mb-1">Foto / Nota ({{ count($mPhotoUrls) }}):</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($mPhotoUrls as $pIndex => $pUrl)
                                                <a href="{{ $pUrl }}" target="_blank" class="inline-block relative rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 hover:scale-105 transition-all shadow-2xs">
                                                    <img src="{{ $pUrl }}" alt="Nota {{ $medicine->nama }} {{ $pIndex + 1 }}" class="h-20 w-20 object-cover rounded-lg">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($medicine->unsur_bahan)
                                    <div>
                                        <span class="font-bold text-gray-500">Bahan:</span>
                                        <span class="text-gray-800 ml-1">{{ $medicine->unsur_bahan }}</span>
                                    </div>
                                @endif

                                @if ($medicine->fase)
                                    <div>
                                        <span class="font-bold text-gray-500">Fase Tanaman:</span>
                                        <span class="text-gray-800 ml-1">{{ $medicine->fase }}</span>
                                    </div>
                                @endif

                                {{-- Daftar Toko Pembelian --}}
                                @if ($medicine->purchases && $medicine->purchases->count() > 0)
                                    <div class="pt-2 border-t border-gray-200">
                                        <span class="font-bold text-gray-500 block mb-1.5">Riwayat Pembelian & Toko:</span>
                                        <div class="space-y-1.5">
                                            @foreach ($medicine->purchases as $p)
                                                <div class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-gray-200/80">
                                                    <span class="font-semibold text-gray-800">{{ $p->toko_obat ?? 'Toko tidak dicatat' }}</span>
                                                    <div class="text-right">
                                                        <span class="font-bold text-green-700 block">{{ $p->formatted_harga ?: '—' }}</span>
                                                        @if ($p->tanggal_beli)
                                                            <span class="text-[10px] text-gray-400 block">{{ $p->tanggal_beli->translatedFormat('d M Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif ($medicine->toko_obat || $medicine->tanggal_beli)
                                    <div>
                                        <span class="font-bold text-gray-500">Beli di:</span>
                                        <span class="text-gray-800 ml-1">
                                            {{ $medicine->toko_obat ?? 'Toko tidak dicatat' }}
                                            @if($medicine->tanggal_beli)
                                                <span class="text-gray-500">({{ $medicine->tanggal_beli->translatedFormat('d M Y') }})</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                @if ($medicine->keterangan)
                                    <div class="pt-1.5 border-t border-gray-200">
                                        <span class="font-bold text-gray-500 block mb-0.5">Keterangan:</span>
                                        <p class="text-gray-700 break-words">{{ $medicine->keterangan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Row 5: Action Buttons (Grid 3 Kolom) --}}
                    <div class="grid grid-cols-3 gap-1.5 pt-2 border-t border-gray-100 w-full min-w-0 box-border">
                        <button type="button"
                                onclick="openMedicineDetail({{ $medicine->id }})"
                                class="w-full py-2 px-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold text-center transition-colors flex items-center justify-center gap-1 border border-emerald-200/60 box-border">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Detail</span>
                        </button>

                        <a href="/data-obat/{{ $medicine->id }}/edit"
                           class="w-full py-2 px-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold text-center transition-colors flex items-center justify-center gap-1 box-border">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                            <span>Edit</span>
                        </a>

                        <form method="POST" action="/data-obat/{{ $medicine->id }}" class="w-full m-0 p-0"
                              data-confirm-delete="{{ $medicine->nama }}"
                              data-confirm-title="Hapus Obat?">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full py-2 px-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold transition-colors flex items-center justify-center gap-1 border border-red-200 box-border">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ── DESKTOP VIEW: Clean Solid Table ── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="hidden lg:block bg-surface rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6 w-full">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3.5">Nama Obat</th>
                            <th class="px-4 py-3.5">Kategori</th>
                            <th class="px-4 py-3.5">Cara Kerja</th>
                            <th class="px-4 py-3.5">Sasaran</th>
                            <th class="px-5 py-3.5">Tanaman Sasaran</th>
                            <th class="px-4 py-3.5">Dosis</th>
                            <th class="px-5 py-3.5 whitespace-nowrap">Harga & Toko</th>
                            <th class="px-5 py-3.5 text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($medicines as $medicine)
                            @php
                                $catBadge = $getCategoryBadge($medicine->jenis);
                                $ckBadge  = $getCaraKerjaBadge($medicine->cara_kerja);
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors group">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900 leading-snug">{{ $medicine->nama }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $catBadge['bg'] }} {{ $catBadge['text'] }}">
                                        {{ $medicine->jenis }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($medicine->cara_kerja)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $ckBadge['bg'] }} {{ $ckBadge['text'] }}">
                                            {{ $medicine->cara_kerja }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 max-w-[180px]">
                                    @if ($medicine->sasaran_obat)
                                        <span class="inline-block text-xs font-semibold text-emerald-900 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-lg break-words line-clamp-2" title="{{ $medicine->sasaran_obat }}">
                                            {{ $medicine->sasaran_obat }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-gray-800 font-medium">{{ $medicine->tanaman_sasaran }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-800 font-mono text-xs">{{ $medicine->dosis_anjuran ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($medicine->purchases && $medicine->purchases->count() > 1)
                                        <div class="space-y-1.5">
                                            @foreach ($medicine->purchases as $p)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs font-bold text-green-700 tabular-nums">
                                                        {{ $p->formatted_harga ?: '—' }}
                                                    </span>
                                                    @if ($p->toko_obat)
                                                        <span class="text-[11px] font-medium text-gray-600 bg-gray-100 border border-gray-200/80 px-1.5 py-0.5 rounded">
                                                            {{ $p->toko_obat }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($medicine->purchases && $medicine->purchases->count() === 1)
                                        @php $p = $medicine->purchases->first(); @endphp
                                        <div class="whitespace-nowrap space-y-0.5">
                                            <span class="text-sm font-bold text-green-600 tabular-nums block">
                                                {{ $p->formatted_harga ?: ($medicine->formatted_harga ?: '—') }}
                                            </span>
                                            @if ($p->toko_obat ?? $medicine->toko_obat)
                                                <span class="text-[11px] font-medium text-gray-400 block">
                                                    {{ $p->toko_obat ?? $medicine->toko_obat }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif ($medicine->harga)
                                        <div class="whitespace-nowrap space-y-0.5">
                                            <span class="text-sm font-bold text-green-600 tabular-nums block">
                                                {{ $medicine->formatted_harga }}
                                            </span>
                                            @if ($medicine->toko_obat)
                                                <span class="text-[11px] font-medium text-gray-400 block">
                                                    {{ $medicine->toko_obat }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs font-medium">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 pr-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Tombol Detail: Hanya gambar mata saja --}}
                                        <button type="button"
                                                onclick="openMedicineDetail({{ $medicine->id }})"
                                                title="Lihat Detail Lengkap"
                                                aria-label="Lihat Detail {{ $medicine->nama }}"
                                                class="p-2 rounded-lg text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>

                                        {{-- Tombol Titik Tiga (Aksi) --}}
                                        <button type="button"
                                                data-action-menu-btn
                                                onclick="toggleActionMenu(event, {{ $medicine->id }})"
                                                title="Menu Aksi"
                                                aria-label="Pilihan aksi untuk {{ $medicine->nama }}"
                                                class="p-2 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                            </svg>
                                        </button>

                                        {{-- Dropdown Menu: Edit dan Hapus --}}
                                        <div id="action-dropdown-{{ $medicine->id }}"
                                             class="medicine-action-dropdown hidden fixed w-36 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50 text-left transition-all">
                                            <a href="/data-obat/{{ $medicine->id }}/edit"
                                               class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                                <span>Edit</span>
                                            </a>
                                            <form method="POST" action="/data-obat/{{ $medicine->id }}" class="block m-0 p-0"
                                                  data-confirm-delete="{{ $medicine->nama }}"
                                                  data-confirm-title="Hapus Obat?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($medicines->hasPages())
            <div class="mt-4 mb-8 w-full min-w-0">
                {{ $medicines->links() }}
            </div>
        @endif

    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL DETAIL LENGKAP OBAT PERTANIAN ── --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div id="medicine-detail-modal" 
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs items-center justify-center p-4 transition-opacity duration-200"
         onclick="if(event.target === this) closeMedicineDetail()">
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-2xl max-w-xl w-full overflow-hidden animate-slide-up"
             onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <h3 class="text-base font-bold text-gray-900">Detail Lengkap Obat Pertanian</h3>
                </div>
                <button type="button" 
                        onclick="closeMedicineDetail()"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[82vh] overflow-y-auto">
                {{-- Foto / Nota Preview Gallery --}}
                <div id="modal-foto-container" class="hidden space-y-2">
                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-900/5 relative group">
                        <img id="modal-foto-img" src="" alt="Foto / Nota Obat" class="w-full max-h-64 object-contain mx-auto bg-gray-900/5 transition-all">
                        <div id="modal-foto-counter" class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-lg bg-black/65 text-white text-[11px] font-semibold backdrop-blur-xs shadow-xs">
                            Foto 1
                        </div>
                        <a id="modal-foto-link" href="#" target="_blank" class="absolute bottom-2.5 right-2.5 px-3 py-1.5 rounded-xl bg-black/75 hover:bg-black text-white text-xs font-semibold backdrop-blur-xs flex items-center gap-1.5 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                            </svg>
                            <span>Lihat Ukuran Penuh</span>
                        </a>
                    </div>
                    {{-- Strip thumbnail bila foto lebih dari 1 --}}
                    <div id="modal-foto-thumbnails" class="hidden flex items-center gap-2 overflow-x-auto pb-1"></div>
                </div>

                {{-- Header Obat --}}
                <div class="bg-page/70 rounded-xl p-4 border border-gray-200/80 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <h2 id="modal-nama" class="text-xl font-bold text-gray-900 leading-tight"></h2>
                    </div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span id="modal-jenis-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold"></span>
                        <span id="modal-cara-kerja-badge" class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold"></span>
                        <span id="modal-fase-badge" class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold hidden"></span>
                    </div>
                </div>

                {{-- Sasaran Obat (Highlight Box) --}}
                <div id="modal-sasaran-container" class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 space-y-1 hidden">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Sasaran / Fungsi Utama Obat</span>
                    <p id="modal-sasaran" class="text-xs font-bold text-emerald-950 break-words leading-relaxed"></p>
                </div>

                {{-- Grid Info Teknis --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tanaman Sasaran</span>
                        <p id="modal-tanaman" class="text-xs font-bold text-gray-800 break-words"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Dosis Pemakaian</span>
                        <p id="modal-dosis" class="text-xs font-bold text-gray-800 font-mono break-words"></p>
                    </div>
                    <div id="modal-fase-container" class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1 hidden">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Fase Rekomendasi</span>
                        <p id="modal-fase" class="text-xs font-semibold text-gray-800 break-words"></p>
                    </div>
                    <div id="modal-bahan-container" class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Unsur Bahan</span>
                        <p id="modal-bahan" class="text-xs font-medium text-gray-800 break-words"></p>
                    </div>
                </div>

                {{-- Riwayat Toko & Harga Pembelian --}}
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-600 uppercase tracking-wider block flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25A2.25 2.25 0 010 18.75V8.25A2.25 2.25 0 012.25 6h19.5A2.25 2.25 0 0124 8.25v10.5A2.25 2.25 0 0121.75 21h-4.5"/>
                            </svg>
                            Riwayat Pembelian & Toko
                        </span>
                        <span id="modal-purchases-count" class="text-[11px] font-semibold text-gray-400"></span>
                    </div>

                    <div id="modal-purchases-list" class="space-y-2">
                        {{-- Diisi secara dinamis via JavaScript --}}
                    </div>
                </div>

                {{-- Keterangan & Catatan Keamanan --}}
                <div id="modal-keterangan-container" class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1 text-xs hidden">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Keterangan & Catatan Tambahan</span>
                    <p id="modal-keterangan" class="text-xs text-gray-700 leading-relaxed break-words"></p>
                </div>
            </div>
            <div class="px-6 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between">
                <button type="button"
                        onclick="closeMedicineDetail()"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Tutup
                </button>
                <a id="modal-edit-link" 
                   href="#" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                    <span>Edit Data</span>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
window.toggleDetails = function(id) {
    const details = document.getElementById(`details-${id}`);
    const chevron = document.getElementById(`chevron-${id}`);
    if (!details) return;

    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    } else {
        details.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
};

// Modal Detail Obat Functions
const medicinesData = @json($medicines->items() ? collect($medicines->items())->keyBy('id') : []);

window.openMedicineDetail = function(id) {
    const data = medicinesData[id];
    if (!data) return;

    // Nama Obat
    document.getElementById('modal-nama').textContent = data.nama || '—';

    // Foto / Nota Preview Gallery
    const fotoContainer = document.getElementById('modal-foto-container');
    const fotoImg = document.getElementById('modal-foto-img');
    const fotoLink = document.getElementById('modal-foto-link');
    const fotoCounter = document.getElementById('modal-foto-counter');
    const fotoThumbnails = document.getElementById('modal-foto-thumbnails');

    const photos = Array.isArray(data.foto_urls) && data.foto_urls.length > 0
        ? data.foto_urls
        : (data.foto_url ? [data.foto_url] : []);

    if (photos.length > 0) {
        fotoContainer.classList.remove('hidden');

        function selectModalPhoto(idx) {
            fotoImg.src = photos[idx];
            fotoLink.href = photos[idx];
            if (fotoCounter) {
                fotoCounter.textContent = photos.length > 1 ? `Foto ${idx + 1} dari ${photos.length}` : 'Foto Nota';
            }
            if (fotoThumbnails) {
                fotoThumbnails.querySelectorAll('.modal-thumb-btn').forEach((btn, bIdx) => {
                    if (bIdx === idx) {
                        btn.className = 'modal-thumb-btn shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 border-emerald-500 ring-2 ring-emerald-400/30 transition-all';
                    } else {
                        btn.className = 'modal-thumb-btn shrink-0 w-14 h-14 rounded-lg overflow-hidden border border-gray-200 opacity-60 hover:opacity-100 transition-all';
                    }
                });
            }
        }

        if (photos.length > 1 && fotoThumbnails) {
            fotoThumbnails.innerHTML = '';
            photos.forEach((url, pIdx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'modal-thumb-btn shrink-0 w-14 h-14 rounded-lg overflow-hidden border border-gray-200 opacity-60 hover:opacity-100 transition-all cursor-pointer';
                btn.onclick = () => selectModalPhoto(pIdx);
                btn.innerHTML = `<img src="${url}" alt="Thumbnail ${pIdx + 1}" class="w-full h-full object-cover">`;
                fotoThumbnails.appendChild(btn);
            });
            fotoThumbnails.classList.remove('hidden');
        } else if (fotoThumbnails) {
            fotoThumbnails.innerHTML = '';
            fotoThumbnails.classList.add('hidden');
        }

        selectModalPhoto(0);
    } else {
        fotoImg.src = '';
        fotoContainer.classList.add('hidden');
        if (fotoThumbnails) {
            fotoThumbnails.innerHTML = '';
            fotoThumbnails.classList.add('hidden');
        }
    }

    // Kategori Badge
    const jenisBadge = document.getElementById('modal-jenis-badge');
    jenisBadge.textContent = data.jenis || '—';
    const catColors = {
        'Fungisida': { bg: 'bg-purple-100', text: 'text-purple-800' },
        'Insektisida': { bg: 'bg-rose-100', text: 'text-rose-800' },
        'Pupuk': { bg: 'bg-emerald-100', text: 'text-emerald-800' },
        'Vitamin': { bg: 'bg-teal-100', text: 'text-teal-800' },
        'Bibit': { bg: 'bg-lime-100', text: 'text-lime-800' },
        'Perlengkapan': { bg: 'bg-amber-100', text: 'text-amber-800' },
        'Peralatan': { bg: 'bg-blue-100', text: 'text-blue-800' }
    };
    const cat = catColors[data.jenis] || { bg: 'bg-gray-100', text: 'text-gray-800' };
    jenisBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold ${cat.bg} ${cat.text}`;

    // Cara Kerja Badge
    const ckBadge = document.getElementById('modal-cara-kerja-badge');
    if (data.cara_kerja) {
        const ckColors = {
            'Sistemik': { bg: 'bg-sky-100', text: 'text-sky-800' },
            'Kontak': { bg: 'bg-orange-100', text: 'text-orange-800' },
            'Sistemik + Kontak': { bg: 'bg-indigo-100', text: 'text-indigo-800' }
        };
        const ck = ckColors[data.cara_kerja] || { bg: 'bg-gray-100', text: 'text-gray-800' };
        ckBadge.textContent = data.cara_kerja;
        ckBadge.className = `inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold ${ck.bg} ${ck.text}`;
        ckBadge.classList.remove('hidden');
    } else {
        ckBadge.classList.add('hidden');
    }

    // Sasaran / Fungsi Utama
    const sasaranContainer = document.getElementById('modal-sasaran-container');
    if (data.sasaran_obat) {
        document.getElementById('modal-sasaran').textContent = data.sasaran_obat;
        sasaranContainer.classList.remove('hidden');
    } else {
        sasaranContainer.classList.add('hidden');
    }

    // Tanaman Sasaran & Dosis
    document.getElementById('modal-tanaman').textContent = data.tanaman_sasaran || '—';
    document.getElementById('modal-dosis').textContent = data.dosis_anjuran || '—';

    // Fase Tanaman
    const faseBadge = document.getElementById('modal-fase-badge');
    const faseContainer = document.getElementById('modal-fase-container');
    if (data.fase) {
        const faseColors = {
            'Vegetatif': { bg: 'bg-emerald-100', text: 'text-emerald-800' },
            'Generatif': { bg: 'bg-amber-100', text: 'text-amber-800' },
            'Semua Fase': { bg: 'bg-sky-100', text: 'text-sky-800' }
        };
        const fCol = faseColors[data.fase] || { bg: 'bg-gray-100', text: 'text-gray-800' };
        faseBadge.textContent = 'Fase: ' + data.fase;
        faseBadge.className = `inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold ${fCol.bg} ${fCol.text}`;
        faseBadge.classList.remove('hidden');

        document.getElementById('modal-fase').textContent = data.fase;
        faseContainer.classList.remove('hidden');
    } else {
        faseBadge.classList.add('hidden');
        faseContainer.classList.add('hidden');
    }

    // Unsur Bahan
    const bahanContainer = document.getElementById('modal-bahan-container');
    document.getElementById('modal-bahan').textContent = data.unsur_bahan || '—';

    // Riwayat Toko & Harga Pembelian (Multi-store support)
    const purchasesList = document.getElementById('modal-purchases-list');
    const purchasesCount = document.getElementById('modal-purchases-count');
    purchasesList.innerHTML = '';

    const purchases = Array.isArray(data.purchases) && data.purchases.length > 0 ? data.purchases : [];

    if (purchases.length > 0) {
        purchasesCount.textContent = `${purchases.length} Riwayat`;
        purchases.forEach((p, idx) => {
            let pDate = '—';
            if (p.tanggal_beli) {
                try {
                    const d = new Date(p.tanggal_beli);
                    if (!isNaN(d.getTime())) {
                        pDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    }
                } catch(e) {
                    pDate = p.tanggal_beli;
                }
            }

            const itemDiv = document.createElement('div');
            itemDiv.className = 'bg-white p-3 rounded-xl border border-gray-200/90 shadow-xs flex items-center justify-between gap-3 text-xs';
            itemDiv.innerHTML = `
                <div class="space-y-0.5 min-w-0">
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="truncate">${p.toko_obat || 'Toko tidak dicatat'}</span>
                    </div>
                    <div class="text-[11px] text-gray-500">
                        Tanggal: <span class="font-medium text-gray-700">${pDate}</span>
                        ${p.catatan ? `<span class="block text-gray-500 italic mt-0.5">"${p.catatan}"</span>` : ''}
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-sm font-bold text-green-700 tabular-nums block">
                        ${p.formatted_harga || (p.harga ? 'Rp ' + Number(p.harga).toLocaleString('id-ID') : '—')}
                    </span>
                </div>
            `;
            purchasesList.appendChild(itemDiv);
        });
    } else if (data.toko_obat || data.harga || data.tanggal_beli) {
        purchasesCount.textContent = '1 Riwayat';
        let pDate = '—';
        if (data.tanggal_beli) {
            try {
                const d = new Date(data.tanggal_beli);
                if (!isNaN(d.getTime())) {
                    pDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                }
            } catch(e) {
                pDate = data.tanggal_beli;
            }
        }
        let formattedPrice = data.formatted_harga || (data.harga ? 'Rp ' + Number(data.harga).toLocaleString('id-ID') : '—');

        const itemDiv = document.createElement('div');
        itemDiv.className = 'bg-white p-3 rounded-xl border border-gray-200/90 shadow-xs flex items-center justify-between gap-3 text-xs';
        itemDiv.innerHTML = `
            <div class="space-y-0.5 min-w-0">
                <div class="font-bold text-gray-900 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="truncate">${data.toko_obat || 'Toko tidak dicatat'}</span>
                </div>
                <div class="text-[11px] text-gray-500">
                    Tanggal: <span class="font-medium text-gray-700">${pDate}</span>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-sm font-bold text-green-700 tabular-nums block">
                    ${formattedPrice}
                </span>
            </div>
        `;
        purchasesList.appendChild(itemDiv);
    } else {
        purchasesCount.textContent = 'Belum ada data';
        purchasesList.innerHTML = '<p class="text-xs text-gray-400 italic py-1">Belum ada riwayat pembelian toko yang dicatat.</p>';
    }

    // Keterangan & Catatan
    const ketContainer = document.getElementById('modal-keterangan-container');
    if (data.keterangan) {
        document.getElementById('modal-keterangan').textContent = data.keterangan;
        ketContainer.classList.remove('hidden');
    } else {
        ketContainer.classList.add('hidden');
    }

    // Link Edit
    document.getElementById('modal-edit-link').href = `/data-obat/${data.id}/edit`;

    // Tampilkan Modal
    const modal = document.getElementById('medicine-detail-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
};

window.closeMedicineDetail = function() {
    const modal = document.getElementById('medicine-detail-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.body.classList.remove('overflow-hidden');
};

window.toggleActionMenu = function(event, id) {
    event.stopPropagation();
    const menu = document.getElementById(`action-dropdown-${id}`);
    const btn = event.currentTarget;
    if (!menu) return;

    const isHidden = menu.classList.contains('hidden');
    
    // Tutup semua menu aksi lainnya terlebih dahulu
    document.querySelectorAll('.medicine-action-dropdown').forEach(el => el.classList.add('hidden'));

    if (isHidden) {
        const rect = btn.getBoundingClientRect();
        const menuWidth = 144; // w-36 = 144px
        const menuHeight = 84;
        
        let top = rect.bottom + 4;
        if (top + menuHeight > window.innerHeight) {
            top = Math.max(10, rect.top - menuHeight - 4);
        }
        
        let left = rect.right - menuWidth;
        if (left < 10) left = 10;

        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.classList.remove('hidden');
    }
};

// Tutup menu dropdown saat klik di luar
document.addEventListener('click', (e) => {
    if (!e.target.closest('.medicine-action-dropdown') && !e.target.closest('[data-action-menu-btn]')) {
        document.querySelectorAll('.medicine-action-dropdown').forEach(el => el.classList.add('hidden'));
    }
});

// Tutup menu dropdown saat halaman discroll
window.addEventListener('scroll', () => {
    document.querySelectorAll('.medicine-action-dropdown').forEach(el => el.classList.add('hidden'));
}, { passive: true });

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.medicine-action-dropdown').forEach(el => el.classList.add('hidden'));
        closeMedicineDetail();
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    if (!window.AgriOfflineStore) return;
    @if(isset($medicines) && $medicines->count() > 0)
        try {
            const currentServerData = @json($medicines->items());
            await window.AgriOfflineStore.cacheServerMedicines(currentServerData);
        } catch (e) {
            console.warn('Cache server data error:', e);
        }
    @endif
    try {
        const allLocal = await window.AgriOfflineStore.getAllMedicines();
        const unsynced = allLocal.filter(m => m.is_synced === false);
        if (unsynced.length > 0) {
            const mobileContainer = document.getElementById('mobile-cards-list');
            unsynced.forEach(item => {
                if (document.querySelector(`[data-client-id="${item.client_id}"]`)) return;
                if (mobileContainer) {
                    const card = document.createElement('div');
                    card.className = 'bg-amber-50/50 rounded-2xl border-2 border-amber-300 p-4 space-y-3 w-full min-w-0 box-border';
                    card.setAttribute('data-client-id', item.client_id);
                    card.innerHTML = `
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-amber-200 text-amber-900">
                                ⚡ Draft Offline
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-700">${item.jenis}</span>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">${item.nama}</h2>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-white p-2 rounded-lg border border-amber-200">
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Sasaran</span>
                                <p class="font-semibold text-gray-800">${item.tanaman_sasaran || '-'}</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-amber-200">
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Dosis</span>
                                <p class="font-semibold text-gray-800">${item.dosis_anjuran || '-'}</p>
                            </div>
                        </div>
                        <div class="pt-1 flex justify-end">
                            <button type="button" onclick="deleteOfflineItem('${item.client_id}', '${item.nama}')"
                                    class="px-3 py-1.5 rounded-xl border border-red-200 text-xs font-bold text-red-600 bg-white hover:bg-red-50">
                                Hapus Draft
                            </button>
                        </div>
                    `;
                    mobileContainer.prepend(card);
                }
            });
        }
    } catch (e) {
        console.warn('Render offline error:', e);
    }
});

window.deleteOfflineItem = function(clientId, name) {
    if (!window.AgriSwal) return;
    AgriSwal.confirmDelete('Hapus Draft Offline?', name, async () => {
        await window.AgriOfflineStore.deleteMedicineOffline(null, clientId);
        document.querySelectorAll(`[data-client-id="${clientId}"]`).forEach(el => el.remove());
        AgriSwal.toastSuccess(`Draft "${name}" dihapus.`);
    });
};
</script>
@endpush
