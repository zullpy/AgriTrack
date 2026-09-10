@extends('layouts.app')

@section('title', isset($medicine) ? 'Edit Obat Tanaman' : 'Tambah Obat Baru')

@section('content')

{{-- ── Breadcrumb ── --}}
<div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
    <a href="/data-obat" class="hover:text-primary transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Data Obat Tanaman
    </a>
    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    <span class="text-gray-900 font-semibold">{{ isset($medicine) ? 'Edit Obat' : 'Tambah Baru' }}</span>
</div>

{{-- ── Page Title ── --}}
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">
        {{ isset($medicine) ? 'Edit Obat Tanaman' : 'Tambah Obat Baru' }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        Isi detail formulir di bawah. Data pada bagian prioritas utama akan ditampilkan paling menonjol pada aplikasi.
    </p>
</div>

<div class="max-w-3xl">
    <form id="form-medicine"
          method="POST"
          action="{{ isset($medicine) ? '/data-obat/' . $medicine->id : '/data-obat' }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if (isset($medicine))
            @method('PUT')
        @endif

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ── KARTU 1: DATA PRIORITAS UTAMA (Kuning di Spreadsheet) ── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-bold text-gray-900">Informasi Prioritas Utama</h2>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Nama Obat --}}
                <div>
                    <label for="nama" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nama Obat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           list="existing-medicines-list"
                           autocomplete="off"
                           value="{{ old('nama', $medicine->nama ?? '') }}"
                           placeholder="Contoh: Curacron 500 EC, Dithane M-45, Antracol..."
                           class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all @error('nama') border-red-500 bg-red-50 @enderror"
                           required>

                    <datalist id="existing-medicines-list">
                        @foreach ($existingMedicines ?? [] as $em)
                            <option value="{{ $em->nama }}">{{ $em->jenis }} - {{ $em->tanaman_sasaran }}</option>
                        @endforeach
                    </datalist>

                    {{-- Banner Notifikasi Otomatis Terpanggil --}}
                    <div id="autofill-banner" class="hidden mt-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200/90 text-xs text-emerald-950 transition-all animate-fade-in">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1 shrink-0 animate-ping"></span>
                                <div>
                                    <p class="font-bold text-emerald-900">Data obat "<span id="autofill-medicine-name"></span>" ditemukan!</p>
                                    <p class="text-[11px] text-emerald-800 mt-0.5 leading-relaxed">
                                        Formulir otomatis terisi dari data yang sudah ada. Jika nama toko sama, yang terupdate otomatis adalah harga baru & tanggal belinya.
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('autofill-banner').classList.add('hidden')" class="text-emerald-700 hover:text-emerald-950 font-bold p-1 leading-none">&times;</button>
                        </div>
                    </div>

                    @error('nama')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori (Jenis) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5" id="kategori-group">
                        @php
                            $kategoriConfigs = [
                                'Fungisida' => [
                                    'label'        => 'Fungisida',
                                    'theme'        => 'purple',
                                    'hover_card'   => 'hover:border-purple-300 hover:bg-purple-50/50 hover:text-purple-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-purple-400 group-hover:scale-125',
                                    'checked_card' => 'border-purple-500 bg-purple-50/90 text-purple-900 font-bold ring-2 ring-purple-400/25 shadow-sm shadow-purple-500/10',
                                    'checked_dot'  => 'bg-purple-600 ring-2 ring-purple-200 scale-110',
                                    'badge_icon'   => 'text-purple-600',
                                ],
                                'Insektisida' => [
                                    'label'        => 'Insektisida',
                                    'theme'        => 'rose',
                                    'hover_card'   => 'hover:border-rose-300 hover:bg-rose-50/50 hover:text-rose-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-rose-400 group-hover:scale-125',
                                    'checked_card' => 'border-rose-500 bg-rose-50/90 text-rose-900 font-bold ring-2 ring-rose-400/25 shadow-sm shadow-rose-500/10',
                                    'checked_dot'  => 'bg-rose-600 ring-2 ring-rose-200 scale-110',
                                    'badge_icon'   => 'text-rose-600',
                                ],
                                'Pupuk' => [
                                    'label'        => 'Pupuk',
                                    'theme'        => 'emerald',
                                    'hover_card'   => 'hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-emerald-400 group-hover:scale-125',
                                    'checked_card' => 'border-emerald-500 bg-emerald-50/90 text-emerald-900 font-bold ring-2 ring-emerald-400/25 shadow-sm shadow-emerald-500/10',
                                    'checked_dot'  => 'bg-emerald-600 ring-2 ring-emerald-200 scale-110',
                                    'badge_icon'   => 'text-emerald-600',
                                ],
                                'Vitamin' => [
                                    'label'        => 'Vitamin',
                                    'theme'        => 'teal',
                                    'hover_card'   => 'hover:border-teal-300 hover:bg-teal-50/50 hover:text-teal-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-teal-400 group-hover:scale-125',
                                    'checked_card' => 'border-teal-500 bg-teal-50/90 text-teal-900 font-bold ring-2 ring-teal-400/25 shadow-sm shadow-teal-500/10',
                                    'checked_dot'  => 'bg-teal-600 ring-2 ring-teal-200 scale-110',
                                    'badge_icon'   => 'text-teal-600',
                                ],
                                'Bibit' => [
                                    'label'        => 'Bibit',
                                    'theme'        => 'lime',
                                    'hover_card'   => 'hover:border-lime-300 hover:bg-lime-50/50 hover:text-lime-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-lime-400 group-hover:scale-125',
                                    'checked_card' => 'border-lime-500 bg-lime-50/90 text-lime-900 font-bold ring-2 ring-lime-400/25 shadow-sm shadow-lime-500/10',
                                    'checked_dot'  => 'bg-lime-600 ring-2 ring-lime-200 scale-110',
                                    'badge_icon'   => 'text-lime-600',
                                ],
                                'Perlengkapan' => [
                                    'label'        => 'Perlengkapan',
                                    'theme'        => 'amber',
                                    'hover_card'   => 'hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-amber-400 group-hover:scale-125',
                                    'checked_card' => 'border-amber-500 bg-amber-50/90 text-amber-900 font-bold ring-2 ring-amber-400/25 shadow-sm shadow-amber-500/10',
                                    'checked_dot'  => 'bg-amber-600 ring-2 ring-amber-200 scale-110',
                                    'badge_icon'   => 'text-amber-600',
                                ],
                                'Peralatan' => [
                                    'label'        => 'Peralatan',
                                    'theme'        => 'blue',
                                    'hover_card'   => 'hover:border-blue-300 hover:bg-blue-50/50 hover:text-blue-900 hover:shadow-md hover:-translate-y-0.5',
                                    'hover_dot'    => 'group-hover:bg-blue-400 group-hover:scale-125',
                                    'checked_card' => 'border-blue-500 bg-blue-50/90 text-blue-900 font-bold ring-2 ring-blue-400/25 shadow-sm shadow-blue-500/10',
                                    'checked_dot'  => 'bg-blue-600 ring-2 ring-blue-200 scale-110',
                                    'badge_icon'   => 'text-blue-600',
                                ],
                            ];
                        @endphp
                        @foreach ($kategoriConfigs as $val => $cfg)
                            @php $isChecked = old('jenis', $medicine->jenis ?? 'Fungisida') === $val; @endphp
                            <label class="group kategori-pill relative flex items-center justify-between p-2.5 sm:p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 ease-out select-none active:scale-[0.98] active:translate-y-0 {{ $isChecked ? $cfg['checked_card'] : 'border-gray-200/90 bg-white text-gray-700 font-medium shadow-2xs ' . $cfg['hover_card'] }}"
                                   data-theme="{{ $cfg['theme'] }}"
                                   data-checked-card="{{ $cfg['checked_card'] }}"
                                   data-checked-dot="{{ $cfg['checked_dot'] }}"
                                   data-hover-card="{{ $cfg['hover_card'] }}"
                                   data-hover-dot="{{ $cfg['hover_dot'] }}">
                                <input type="radio" name="jenis" value="{{ $val }}" class="sr-only" {{ $isChecked ? 'checked' : '' }} required>
                                
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="pill-dot w-2.5 h-2.5 rounded-full transition-all duration-200 shrink-0 {{ $isChecked ? $cfg['checked_dot'] : 'bg-gray-300 ' . $cfg['hover_dot'] }}"></span>
                                    <span class="text-xs tracking-tight truncate">{{ $cfg['label'] }}</span>
                                </div>

                                {{-- Subtle checkmark indicator on selected --}}
                                <svg class="pill-check w-3.5 h-3.5 transition-all duration-200 shrink-0 {{ $isChecked ? 'opacity-100 scale-100 ' . $cfg['badge_icon'] : 'opacity-0 scale-75' }}" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cara Kerja --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Cara Kerja Obat
                    </label>
                    <div class="grid grid-cols-3 gap-2.5" id="cara-kerja-group">
                        @php
                            $caraKerjaConfigs = [
                                'Sistemik' => [
                                    'label'        => 'Sistemik',
                                    'theme'        => 'sky',
                                    'hover_card'   => 'hover:border-sky-300 hover:bg-sky-50/50 hover:text-sky-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-sky-500 bg-sky-50/90 text-sky-900 font-bold ring-2 ring-sky-400/25 shadow-sm shadow-sky-500/10',
                                    'badge_icon'   => 'text-sky-600',
                                ],
                                'Kontak' => [
                                    'label'        => 'Kontak',
                                    'theme'        => 'orange',
                                    'hover_card'   => 'hover:border-orange-300 hover:bg-orange-50/50 hover:text-orange-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-orange-500 bg-orange-50/90 text-orange-900 font-bold ring-2 ring-orange-400/25 shadow-sm shadow-orange-500/10',
                                    'badge_icon'   => 'text-orange-600',
                                ],
                                'Sistemik + Kontak' => [
                                    'label'        => 'Sistemik + Kontak',
                                    'theme'        => 'indigo',
                                    'hover_card'   => 'hover:border-indigo-300 hover:bg-indigo-50/50 hover:text-indigo-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-indigo-500 bg-indigo-50/90 text-indigo-900 font-bold ring-2 ring-indigo-400/25 shadow-sm shadow-indigo-500/10',
                                    'badge_icon'   => 'text-indigo-600',
                                ],
                            ];
                        @endphp
                        @foreach ($caraKerjaConfigs as $val => $cfg)
                            @php $isCkChecked = old('cara_kerja', $medicine->cara_kerja ?? '') === $val; @endphp
                            <label class="group cara-kerja-pill relative flex items-center justify-center gap-1.5 p-2.5 sm:p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 ease-out select-none text-center active:scale-[0.98] active:translate-y-0 {{ $isCkChecked ? $cfg['checked_card'] : 'border-gray-200/90 bg-white text-gray-700 font-medium shadow-2xs ' . $cfg['hover_card'] }}"
                                   data-theme="{{ $cfg['theme'] }}"
                                   data-checked-card="{{ $cfg['checked_card'] }}"
                                   data-hover-card="{{ $cfg['hover_card'] }}">
                                <input type="radio" name="cara_kerja" value="{{ $val }}" class="sr-only" {{ $isCkChecked ? 'checked' : '' }}>
                                
                                <span class="text-xs tracking-tight truncate">{{ $cfg['label'] }}</span>

                                {{-- Subtle checkmark indicator on selected --}}
                                <svg class="pill-check w-3.5 h-3.5 transition-all duration-200 shrink-0 {{ $isCkChecked ? 'opacity-100 scale-100 ' . $cfg['badge_icon'] : 'opacity-0 scale-75 hidden' }}" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </label>
                        @endforeach
                    </div>
                    @error('cara_kerja')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sasaran / Fungsi Obat --}}
                <div>
                    <label for="sasaran_obat" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Sasaran / Fungsi Obat
                    </label>
                    <input type="text"
                           id="sasaran_obat"
                           name="sasaran_obat"
                           value="{{ old('sasaran_obat', $medicine->sasaran_obat ?? '') }}"
                           placeholder="Contoh: Untuk menguatkan akar, pembesar umbi, pencegahan busuk daun..."
                           class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all @error('sasaran_obat') border-red-500 bg-red-50 @enderror">
                    <p class="text-[11px] text-gray-500 mt-1">Fokus manfaat atau target sasaran obat pada tanaman (contoh: untuk menguatkan akar).</p>
                    @error('sasaran_obat')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rekomendasi Tanaman & Dosis (2 Kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Rekomendasi Tanaman --}}
                    <div>
                        <label for="tanaman_sasaran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Rekomendasi Tanaman <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="tanaman_sasaran"
                               name="tanaman_sasaran"
                               value="{{ old('tanaman_sasaran', $medicine->tanaman_sasaran ?? '') }}"
                               placeholder="Contoh: Cabai, Tomat, Padi, Bawang"
                               class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all @error('tanaman_sasaran') border-red-500 bg-red-50 @enderror"
                               required>
                        @error('tanaman_sasaran')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dosis Pemakaian --}}
                    <div>
                        <label for="dosis_anjuran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Dosis Pemakaian
                        </label>
                        <input type="text"
                               id="dosis_anjuran"
                               name="dosis_anjuran"
                               value="{{ old('dosis_anjuran', $medicine->dosis_anjuran ?? '') }}"
                               placeholder="Contoh: 15 ml / 16 liter air, 2 gram/liter"
                               class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all">
                        @error('dosis_anjuran')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Harga Obat --}}
                <div>
                    <label for="harga" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Harga Obat (Rupiah)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-500 select-none">Rp</span>
                        @php
                            $rawHarga = old('harga', $medicine->harga ?? '');
                            $initialHarga = '';
                            if ($rawHarga !== '' && $rawHarga !== null) {
                                $cleanDigits = preg_replace('/\D/', '', (string) $rawHarga);
                                $initialHarga = $cleanDigits !== '' ? number_format((float) $cleanDigits, 0, ',', '.') : '';
                            }
                        @endphp
                        <input type="text"
                               inputmode="numeric"
                               id="harga"
                               name="harga"
                               value="{{ $initialHarga }}"
                               placeholder="Contoh: 65.000"
                               class="field-input w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-semibold transition-all">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Format otomatis Rupiah (contoh: ketik 65000 otomatis menjadi 65.000).</p>
                    @error('harga')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ── KARTU 2: DETAIL SEKUNDER / DATA TAMBAHAN ── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                    <h2 class="text-sm font-bold text-gray-900">Detail Tambahan & Pembelian</h2>
                </div>
                <span class="text-[11px] font-medium text-gray-500">
                    Opsional
                </span>
            </div>

            <div class="p-5 space-y-4">
                {{-- Unsur Bahan --}}
                <div>
                    <label for="unsur_bahan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Unsur Bahan 
                    </label>
                    <input type="text"
                           id="unsur_bahan"
                           name="unsur_bahan"
                           value="{{ old('unsur_bahan', $medicine->unsur_bahan ?? '') }}"
                           placeholder="Contoh: Mankozeb 80%, Profilofos 500 g/l"
                           class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all">
                </div>

                {{-- Fase Tanaman (Vegetatif / Generatif) --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Fase Aplikasi Tanaman
                        </label>
                        <span class="text-[11px] text-gray-400">Klik lagi untuk batal pilih</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5" id="fase-group">
                        @php
                            $faseConfigs = [
                                'Vegetatif' => [
                                    'label'        => 'Vegetatif',
                                    'desc'         => 'Akar, batang & daun (pertumbuhan)',
                                    'theme'        => 'emerald',
                                    'hover_card'   => 'hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-emerald-500 bg-emerald-50/90 text-emerald-900 font-bold ring-2 ring-emerald-400/25 shadow-sm shadow-emerald-500/10',
                                    'badge_icon'   => 'text-emerald-600',
                                ],
                                'Generatif' => [
                                    'label'        => 'Generatif',
                                    'desc'         => 'Bunga, buah & pengisian umbi',
                                    'theme'        => 'amber',
                                    'hover_card'   => 'hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-amber-500 bg-amber-50/90 text-amber-900 font-bold ring-2 ring-amber-400/25 shadow-sm shadow-amber-500/10',
                                    'badge_icon'   => 'text-amber-600',
                                ],
                                'Semua Fase' => [
                                    'label'        => 'Semua Fase',
                                    'desc'         => 'Fleksibel untuk seluruh periode',
                                    'theme'        => 'sky',
                                    'hover_card'   => 'hover:border-sky-300 hover:bg-sky-50/50 hover:text-sky-900 hover:shadow-md hover:-translate-y-0.5',
                                    'checked_card' => 'border-sky-500 bg-sky-50/90 text-sky-900 font-bold ring-2 ring-sky-400/25 shadow-sm shadow-sky-500/10',
                                    'badge_icon'   => 'text-sky-600',
                                ],
                            ];
                        @endphp
                        @foreach ($faseConfigs as $val => $cfg)
                            @php $isFaseChecked = old('fase', $medicine->fase ?? '') === $val; @endphp
                            <label class="group fase-pill relative flex flex-col items-start p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 ease-out select-none active:scale-[0.98] active:translate-y-0 {{ $isFaseChecked ? $cfg['checked_card'] : 'border-gray-200/90 bg-white text-gray-700 font-medium shadow-2xs ' . $cfg['hover_card'] }}"
                                   data-theme="{{ $cfg['theme'] }}"
                                   data-checked-card="{{ $cfg['checked_card'] }}"
                                   data-hover-card="{{ $cfg['hover_card'] }}">
                                <input type="radio" name="fase" value="{{ $val }}" class="sr-only" {{ $isFaseChecked ? 'checked' : '' }}>
                                
                                <div class="w-full flex items-center justify-between gap-1">
                                    <span class="text-xs font-bold tracking-tight">{{ $cfg['label'] }}</span>
                                    <svg class="pill-check w-3.5 h-3.5 transition-all duration-200 shrink-0 {{ $isFaseChecked ? 'opacity-100 scale-100 ' . $cfg['badge_icon'] : 'opacity-0 scale-75 hidden' }}" 
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </div>
                                <span class="text-[11px] text-gray-500 mt-1 font-normal leading-tight">{{ $cfg['desc'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('fase')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Foto / Nota Obat (Bisa Banyak Foto) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Foto Obat / Nota Pembelian
                        </label>
                    </div>

                    <div class="space-y-3">
                        {{-- Dropzone Area --}}
                        <div id="dropzone-container" 
                             class="relative border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-5 text-center transition-all bg-gray-50/50 hover:bg-emerald-50/30 cursor-pointer group">
                            <input type="file" 
                                   id="foto_nota" 
                                   name="foto_nota[]" 
                                   multiple
                                   accept="image/*"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="flex flex-col items-center justify-center pointer-events-none">
                                <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 shadow-xs flex items-center justify-center text-gray-400 group-hover:text-emerald-600 group-hover:scale-110 transition-all mb-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-700 group-hover:text-emerald-700 transition-colors">
                                    Klik atau Geser Foto ke Sini
                                </p>
                                <p class="text-[11px] text-gray-500 mt-1">
                                    Format JPG, PNG, WEBP 
                                </p>
                            </div>
                        </div>

                        {{-- Compression Status Notice --}}
                        <div id="foto-compress-progress" class="hidden text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200/60 rounded-xl px-3 py-2 flex items-center gap-2 animate-pulse">
                            <svg class="w-4 h-4 animate-spin text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span id="foto-progress-text">Sedang mengompres foto...</span>
                        </div>

                        {{-- Multi-photo Preview Section --}}
                        @php
                            $existingPhotos = isset($medicine) ? $medicine->foto_paths : [];
                        @endphp
                        <div id="foto-preview-section" class="{{ !empty($existingPhotos) ? '' : 'hidden' }} space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                                    Daftar Foto
                                </span>
                                <span id="foto-count-badge" class="text-[11px] font-semibold text-gray-500">
                                    {{ count($existingPhotos) > 0 ? count($existingPhotos) . ' foto tersimpan' : '' }}
                                </span>
                            </div>

                            {{-- Existing Photos (Edit Mode) --}}
                            <div id="existing-photos-container" class="space-y-2">
                                @foreach ($existingPhotos as $idx => $photoPath)
                                    <div class="existing-photo-card bg-white rounded-xl p-2.5 border border-gray-200 flex items-center justify-between gap-3 shadow-2xs">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img src="{{ Storage::disk('public')->url($photoPath) }}" 
                                                 alt="Foto {{ $idx + 1 }}" 
                                                 class="w-12 h-12 object-cover rounded-lg border border-gray-200 shrink-0">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate">
                                                    {{ basename($photoPath) }}
                                                </p>
                                                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    Foto Tersimpan
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                onclick="removeExistingFoto(this, '{{ $photoPath }}', {{ isset($medicine) && $medicine->id ? $medicine->id : 'null' }})"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="Hapus foto ini">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Container for Newly Selected & Compressed Photos --}}
                            <div id="new-photos-container" class="space-y-2"></div>
                        </div>

                        {{-- Autofill matched photos container (when user types existing medicine name) --}}
                        <div id="autofill-photos-container" class="hidden bg-emerald-50/60 rounded-xl p-3 border border-emerald-200/80"></div>
                    </div>
                    @error('foto_nota')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                    @error('foto_nota.*')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Riwayat Toko & Pembelian (Multi-Toko) --}}
                <div class="space-y-3 pt-3 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Toko & Riwayat Pembelian
                            </label>
                            <p class="text-[11px] text-gray-500 mt-0.5">
                                Jika obat dibeli di toko berbeda, tambah toko lain di sini tanpa membuat baris obat baru.
                            </p>
                        </div>
                        <button type="button"
                                onclick="addPurchaseRow()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 transition-colors self-start sm:self-auto">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Tambah Toko Lain</span>
                        </button>
                    </div>

                    <div id="purchases-container" class="space-y-3">
                        @php
                            $existingPurchases = isset($medicine) && $medicine->purchases->isNotEmpty()
                                ? $medicine->purchases
                                : collect([
                                    (object)[
                                        'id' => null,
                                        'toko_obat' => old('toko_obat', $medicine->toko_obat ?? ''),
                                        'harga' => old('harga', $medicine->harga ?? null),
                                        'tanggal_beli' => isset($medicine->tanggal_beli) ? $medicine->tanggal_beli->format('Y-m-d') : old('tanggal_beli', ''),
                                        'catatan' => '',
                                    ]
                                ]);
                        @endphp

                        @foreach($existingPurchases as $index => $purchase)
                            <div class="purchase-row bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 relative transition-all" data-index="{{ $index }}">
                                <input type="hidden" name="purchases[{{ $index }}][id]" value="{{ $purchase->id ?? '' }}">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider purchase-row-label">
                                        Toko #{{ $index + 1 }}
                                    </span>
                                    <button type="button" 
                                            onclick="removePurchaseRow(this)"
                                            class="btn-remove-purchase p-1 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors {{ count($existingPurchases) <= 1 ? 'hidden' : '' }}"
                                            title="Hapus toko ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama Toko</label>
                                        <input type="text"
                                               name="purchases[{{ $index }}][toko_obat]"
                                               value="{{ $purchase->toko_obat ?? '' }}"
                                               placeholder="Contoh: Toko Tani Makmur"
                                               class="purchase-toko-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Harga (Rupiah)</label>
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 select-none">Rp</span>
                                            <input type="text"
                                                   inputmode="numeric"
                                                   name="purchases[{{ $index }}][harga]"
                                                   value="{{ $purchase->harga ? number_format((float)$purchase->harga, 0, ',', '.') : '' }}"
                                                   placeholder="Contoh: 68.000"
                                                   class="purchase-harga-input w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-semibold">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tanggal Beli</label>
                                        <input type="date"
                                               name="purchases[{{ $index }}][tanggal_beli]"
                                               value="{{ $purchase->tanggal_beli instanceof \Carbon\Carbon ? $purchase->tanggal_beli->format('Y-m-d') : ($purchase->tanggal_beli ?? '') }}"
                                               class="purchase-tanggal-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
                                    </div>
                                </div>
                                <div class="store-match-hint text-[10px] text-amber-700 font-medium mt-1.5 hidden flex items-center gap-1"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Keterangan / Fungsi Khusus --}}
                <div>
                    <label for="keterangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Keterangan & Fungsi Khusus
                    </label>
                    <textarea id="keterangan"
                              name="keterangan"
                              rows="2"
                              placeholder="Keterangan fungsi, cara aplikasi bedengan/semprot, atau target hama..."
                              class="field-input w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 font-medium transition-all resize-none">{{ old('keterangan', $medicine->keterangan ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Action Buttons ── --}}
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark active:scale-95 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                {{ isset($medicine) ? 'Simpan Perubahan' : 'Simpan Obat' }}
            </button>

            <a href="/data-obat"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.AgriSwal) {
                window.AgriSwal.toastError('Periksa kembali kolom yang bertanda bintang (*)');
            }
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
// Setup Interactive Radio Selector Pills with fluid transitions
function setupPillGroup(selector, isKategori = false, isOptional = false) {
    const labels = document.querySelectorAll(selector);
    const unselectedCardBase = ['border-gray-200/90', 'bg-white', 'text-gray-700', 'font-medium', 'shadow-2xs'];

    labels.forEach(label => {
        const input = label.querySelector('input');

        if (isOptional) {
            let wasChecked = false;
            label.addEventListener('pointerdown', () => {
                wasChecked = input.checked;
            });
            label.addEventListener('click', () => {
                if (wasChecked) {
                    input.checked = false;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        input.addEventListener('change', () => {
            labels.forEach(l => {
                const inp = l.querySelector('input');
                const checkedCardClasses = (l.dataset.checkedCard || '').split(' ').filter(Boolean);
                const hoverCardClasses = (l.dataset.hoverCard || '').split(' ').filter(Boolean);
                const checkIcon = l.querySelector('.pill-check');
                const dot = l.querySelector('.pill-dot');

                if (inp.checked) {
                    // Activate selected styling
                    l.classList.remove(...unselectedCardBase, ...hoverCardClasses);
                    l.classList.add(...checkedCardClasses);

                    if (checkIcon) {
                        checkIcon.classList.remove('opacity-0', 'scale-75', 'hidden');
                        checkIcon.classList.add('opacity-100', 'scale-100');
                    }

                    if (dot && l.dataset.checkedDot) {
                        const checkedDotClasses = l.dataset.checkedDot.split(' ').filter(Boolean);
                        const hoverDotClasses = (l.dataset.hoverDot || '').split(' ').filter(Boolean);
                        dot.classList.remove('bg-gray-300', ...hoverDotClasses);
                        dot.classList.add(...checkedDotClasses);
                    }
                } else {
                    // Reset to unselected styling
                    l.classList.remove(...checkedCardClasses);
                    l.classList.add(...unselectedCardBase, ...hoverCardClasses);

                    if (checkIcon) {
                        checkIcon.classList.remove('opacity-100', 'scale-100');
                        checkIcon.classList.add('opacity-0', 'scale-75');
                        if (!isKategori) {
                            checkIcon.classList.add('hidden');
                        }
                    }

                    if (dot && l.dataset.checkedDot) {
                        const checkedDotClasses = l.dataset.checkedDot.split(' ').filter(Boolean);
                        const hoverDotClasses = (l.dataset.hoverDot || '').split(' ').filter(Boolean);
                        dot.classList.remove(...checkedDotClasses);
                        dot.classList.add('bg-gray-300', ...hoverDotClasses);
                    }
                }
            });
        });
    });
}

// Fungsi Kompresi Foto Otomatis (~80% kompresi) di Browser via Canvas
// Menjamin ukuran file selalu berkurang dan TIDAK PERNAH bertambah besar
async function compressImageFile(file) {
    if (!file || !file.type.startsWith('image/')) return { file, originalSize: file.size, compressedSize: file.size, dataUrl: null, savings: 0 };
    
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                // Resolusi optimal untuk nota/obat: maks 1280px agar teks tetap tajam terbaca tapi ukuran sangat hemat
                const maxWidth = 1280;
                const maxHeight = 1280;
                let width = img.width;
                let height = img.height;

                if (width > maxWidth || height > maxHeight) {
                    if (width > height) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    } else {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');

                // Latar belakang putih untuk gambar transparan/PNG saat convert ke JPEG
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, width, height);
                ctx.drawImage(img, 0, 0, width, height);

                const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;

                // Kompresi bertahap mulai dari kualitas 0.45 (~80% reduksi ukuran dari kamera HP)
                // Jika masih lebih besar dari aslinya, turunkan kualitas bertahap
                function attemptCompression(quality) {
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: e.target.result, savings: 0 });
                            return;
                        }

                        // Jika hasil kompresi ternyata masih lebih besar atau sama dengan file asli, coba turunkan kualitas jika masih > 0.2
                        if (blob.size >= file.size && quality > 0.25) {
                            attemptCompression(Math.max(0.2, quality - 0.15));
                            return;
                        }

                        // Jika berhasil lebih kecil dari file asli, gunakan file terkompresi
                        if (blob.size < file.size) {
                            const compressedFile = new File([blob], `${baseName}.jpg`, {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            });
                            const savings = Math.round(((file.size - blob.size) / file.size) * 100);
                            resolve({
                                file: compressedFile,
                                originalSize: file.size,
                                compressedSize: blob.size,
                                dataUrl: canvas.toDataURL('image/jpeg', quality),
                                savings: savings
                            });
                        } else {
                            // Jika file asli sudah sangat kecil/terkompresi (sehingga re-encode malah nambah),
                            // TETAP pertahankan file asli agar TIDAK PERNAH membesar!
                            resolve({
                                file: file,
                                originalSize: file.size,
                                compressedSize: file.size,
                                dataUrl: e.target.result,
                                savings: 0
                            });
                        }
                    }, 'image/jpeg', quality);
                }

                // Mulai kompresi dengan target kompresi kuat (~80% reduksi)
                attemptCompression(0.45);
            };
            img.onerror = () => resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: null, savings: 0 });
        };
        reader.onerror = () => resolve({ file, originalSize: file.size, compressedSize: file.size, dataUrl: null, savings: 0 });
    });
}

function bindRupiahInput(inputEl) {
    if (!inputEl) return;
    const formatRupiah = (val) => {
        if (!val) return '';
        const digits = val.toString().replace(/[^0-9]/g, '');
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    if (inputEl.value) {
        inputEl.value = formatRupiah(inputEl.value);
    }

    inputEl.addEventListener('input', () => {
        const cursorPosition = inputEl.selectionStart;
        const prevVal = inputEl.value;
        const digitsBeforeCursor = prevVal.slice(0, cursorPosition).replace(/\D/g, '').length;

        const formatted = formatRupiah(prevVal);
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
        if (digitCount < digitsBeforeCursor || formatted.length === 0) {
            newCursorPos = formatted.length;
        }
        inputEl.setSelectionRange(newCursorPos, newCursorPos);
    });

    inputEl.addEventListener('blur', () => {
        inputEl.value = formatRupiah(inputEl.value);
    });
}

// Array untuk menyimpan file yang baru dipilih & dikompres
let selectedCompressedFiles = [];
window.selectedCompressedFiles = selectedCompressedFiles;

function updatePhotoCountBadge() {
    const badge = document.getElementById('foto-count-badge');
    const existingCount = document.querySelectorAll('.existing-photo-card').length;
    const newCount = selectedCompressedFiles.length;
    const total = existingCount + newCount;

    if (badge) {
        if (total > 0) {
            let label = `${total} foto total`;
            if (existingCount > 0 && newCount > 0) {
                label = `${total} foto (${existingCount} tersimpan, ${newCount} baru)`;
            } else if (existingCount > 0) {
                label = `${existingCount} foto tersimpan`;
            } else {
                label = `${newCount} foto baru`;
            }
            badge.textContent = label;
        } else {
            badge.textContent = '';
        }
    }
}

function syncFileInputAndRender() {
    const fileInput = document.getElementById('foto_nota');
    if (fileInput) {
        const dt = new DataTransfer();
        selectedCompressedFiles.forEach(item => {
            dt.items.add(item.file);
        });
        fileInput.files = dt.files;
    }
    renderNewPhotoPreviews();
}

function renderNewPhotoPreviews() {
    const container = document.getElementById('new-photos-container');
    const previewSection = document.getElementById('foto-preview-section');
    if (!container) return;

    container.innerHTML = '';

    selectedCompressedFiles.forEach((item, index) => {
        const origKb = Math.max(1, Math.round(item.originalSize / 1024));
        const compKb = Math.max(1, Math.round(item.compressedSize / 1024));

        let badgeHtml = '';
        if (item.savings > 0 && compKb < origKb) {
            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">⚡ Hemat ${item.savings}% (${origKb} KB ➔ ${compKb} KB)</span>`;
        } else {
            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">✓ Asli (${origKb} KB)</span>`;
        }

        const card = document.createElement('div');
        card.className = 'new-photo-card bg-white rounded-xl p-2.5 border border-gray-200 flex items-center justify-between gap-3 shadow-2xs animate-slide-up';
        card.innerHTML = `
            <div class="flex items-center gap-3 min-w-0">
                <img src="${item.dataUrl || ''}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 shrink-0" alt="Preview Foto">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-800 truncate">${item.file.name}</p>
                    <div class="mt-0.5">${badgeHtml}</div>
                </div>
            </div>
            <button type="button" 
                    onclick="removeNewPhoto(${index})" 
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" 
                    title="Hapus foto ini">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        container.appendChild(card);
    });

    const hasExisting = document.querySelectorAll('.existing-photo-card').length > 0;
    if (selectedCompressedFiles.length === 0 && !hasExisting && previewSection) {
        previewSection.classList.add('hidden');
    } else if (previewSection) {
        previewSection.classList.remove('hidden');
    }
    updatePhotoCountBadge();
}

window.removeNewPhoto = function(index) {
    selectedCompressedFiles.splice(index, 1);
    syncFileInputAndRender();
};

window.removeExistingFoto = async function(btn, path, medicineId) {
    if (!medicineId) {
        const card = btn.closest('.existing-photo-card');
        if (card) card.remove();

        const form = document.querySelector('form');
        if (form) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_foto_paths[]';
            input.value = path;
            form.appendChild(input);
        }

        const hasExisting = document.querySelectorAll('.existing-photo-card').length > 0;
        const previewSection = document.getElementById('foto-preview-section');
        if (selectedCompressedFiles.length === 0 && !hasExisting && previewSection) {
            previewSection.classList.add('hidden');
        }
        updatePhotoCountBadge();
        return;
    }

    // Konfirmasi penghapusan permanen file foto
    let confirmed = false;
    if (window.Swal) {
        const result = await Swal.fire({
            title: 'Hapus foto ini?',
            text: 'File foto dan data tersimpan akan langsung dihapus dari server.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus File!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        });
        confirmed = result.isConfirmed;
    } else {
        confirmed = confirm('Yakin ingin menghapus file foto ini dari server?');
    }

    if (!confirmed) return;

    btn.disabled = true;
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin text-red-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || document.querySelector('input[name="_token"]')?.value;

        const response = await fetch(`/data-obat/${medicineId}/foto`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ path: path }),
        });

        const res = await response.json();
        if (res.success) {
            const card = btn.closest('.existing-photo-card');
            if (card) {
                card.style.transition = 'all 0.25s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    const hasExisting = document.querySelectorAll('.existing-photo-card').length > 0;
                    const previewSection = document.getElementById('foto-preview-section');
                    if (selectedCompressedFiles.length === 0 && !hasExisting && previewSection) {
                        previewSection.classList.add('hidden');
                    }
                    updatePhotoCountBadge();
                }, 250);
            }

            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'File foto berhasil dihapus',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        } else {
            alert(res.message || 'Gagal menghapus file foto.');
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    } catch (err) {
        console.error('Error menghapus foto:', err);
        alert('Terjadi kesalahan saat menghapus file foto dari server.');
        btn.disabled = false;
        btn.innerHTML = origHtml;
    }
};

const existingMedicines = @json($existingMedicines ?? []);
let currentMatchedMedicine = null;

function handleMedicineNameLookup(val) {
    const trimmed = (val || '').trim().toLowerCase();
    const banner = document.getElementById('autofill-banner');
    const autofillPhotos = document.getElementById('autofill-photos-container');
    if (!trimmed) {
        if (banner) banner.classList.add('hidden');
        if (autofillPhotos) {
            autofillPhotos.innerHTML = '';
            autofillPhotos.classList.add('hidden');
        }
        currentMatchedMedicine = null;
        return;
    }

    const matched = existingMedicines.find(m => m.nama && m.nama.trim().toLowerCase() === trimmed);
    if (matched) {
        currentMatchedMedicine = matched;
        applyMedicineAutofill(matched);
    } else {
        if (banner) banner.classList.add('hidden');
        if (autofillPhotos) {
            autofillPhotos.innerHTML = '';
            autofillPhotos.classList.add('hidden');
        }
        currentMatchedMedicine = null;
        // Bersihkan hint toko jika tidak ada obat yang cocok
        document.querySelectorAll('.store-match-hint').forEach(el => el.classList.add('hidden'));
    }
}

function applyMedicineAutofill(matched) {
    const banner = document.getElementById('autofill-banner');
    const nameSpan = document.getElementById('autofill-medicine-name');
    if (banner && nameSpan) {
        nameSpan.textContent = matched.nama;
        banner.classList.remove('hidden');
    }

    // 1. Kategori (Jenis)
    if (matched.jenis) {
        const catRadio = document.querySelector(`input[name="jenis"][value="${matched.jenis}"]`);
        if (catRadio) {
            catRadio.checked = true;
            catRadio.dispatchEvent(new Event('change'));
        }
    }

    // 2. Cara Kerja
    if (matched.cara_kerja) {
        const ckRadio = document.querySelector(`input[name="cara_kerja"][value="${matched.cara_kerja}"]`);
        if (ckRadio) {
            ckRadio.checked = true;
            ckRadio.dispatchEvent(new Event('change'));
        }
    }

    // 3. Sasaran Obat
    const sasaranInput = document.getElementById('sasaran_obat');
    if (sasaranInput && matched.sasaran_obat) {
        sasaranInput.value = matched.sasaran_obat;
    }

    // 4. Tanaman Sasaran
    const tanamanInput = document.getElementById('tanaman_sasaran');
    if (tanamanInput && matched.tanaman_sasaran) {
        tanamanInput.value = matched.tanaman_sasaran;
    }

    // 5. Dosis Anjuran
    const dosisInput = document.getElementById('dosis_anjuran');
    if (dosisInput && matched.dosis_anjuran) {
        dosisInput.value = matched.dosis_anjuran;
    }

    // 6. Unsur / Bahan Aktif
    const unsurInput = document.getElementById('unsur_bahan');
    if (unsurInput && matched.unsur_bahan) {
        unsurInput.value = matched.unsur_bahan;
    }

    // 7. Fase Tanaman
    if (matched.fase) {
        const faseRadio = document.querySelector(`input[name="fase"][value="${matched.fase}"]`);
        if (faseRadio) {
            faseRadio.checked = true;
            faseRadio.dispatchEvent(new Event('change'));
        }
    }

    // 8. Keterangan
    const ketInput = document.getElementById('keterangan');
    if (ketInput && matched.keterangan) {
        ketInput.value = matched.keterangan;
    }

    // 9. Foto-foto yang tersimpan di master obat
    const autofillPhotos = document.getElementById('autofill-photos-container');
    if (autofillPhotos) {
        const photoUrls = matched.foto_urls || (matched.foto_url ? [matched.foto_url] : []);
        if (photoUrls.length > 0) {
            autofillPhotos.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-900">
                        Foto Obat Tersimpan Sebelumnya (${photoUrls.length} Foto)
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                        Otomatis Digabung
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    ${photoUrls.map((url, idx) => `
                        <a href="${url}" target="_blank" class="inline-block relative rounded-lg overflow-hidden border border-emerald-300 hover:opacity-90 hover:scale-105 transition-all shadow-2xs">
                            <img src="${url}" class="w-12 h-12 object-cover rounded-lg" alt="Foto ${idx + 1}">
                        </a>
                    `).join('')}
                </div>
                <p class="text-[11px] text-emerald-800/80 mt-1.5 font-medium">
                    Foto yang sudah tersimpan di atas tidak akan hilang. Jika Anda mengunggah foto baru di form ini, foto baru akan ditambahkan ke daftar foto obat ini.
                </p>
            `;
            autofillPhotos.classList.remove('hidden');
        } else {
            autofillPhotos.innerHTML = '';
            autofillPhotos.classList.add('hidden');
        }
    }

    // 10. Purchases / Toko
    const purchases = Array.isArray(matched.purchases) && matched.purchases.length > 0
        ? matched.purchases
        : (matched.toko_obat ? [{ toko_obat: matched.toko_obat, harga: matched.harga, tanggal_beli: matched.tanggal_beli }] : []);

    if (purchases.length > 0) {
        const container = document.getElementById('purchases-container');
        if (container) {
            container.innerHTML = '';
            purchases.forEach((p, idx) => {
                let formattedPrice = '';
                if (p.formatted_harga) {
                    formattedPrice = p.formatted_harga.replace('Rp ', '');
                } else if (p.harga) {
                    formattedPrice = Number(p.harga).toLocaleString('id-ID');
                }

                let pDate = '';
                if (p.tanggal_beli) {
                    try {
                        const d = new Date(p.tanggal_beli);
                        if (!isNaN(d.getTime())) {
                            pDate = d.toISOString().split('T')[0];
                        }
                    } catch(e) {
                        pDate = p.tanggal_beli;
                    }
                }

                const row = document.createElement('div');
                row.className = 'purchase-row bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 relative transition-all animate-slide-up';
                row.dataset.index = idx;
                row.innerHTML = `
                    <input type="hidden" name="purchases[${idx}][id]" value="${p.id || ''}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider purchase-row-label">
                                Toko #${idx + 1}
                            </span>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.5 rounded">
                                Toko Terdaftar
                            </span>
                        </div>
                        <button type="button" 
                                onclick="removePurchaseRow(this)"
                                class="btn-remove-purchase p-1 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors ${purchases.length <= 1 ? 'hidden' : ''}"
                                title="Hapus toko ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama Toko</label>
                            <input type="text"
                                   name="purchases[${idx}][toko_obat]"
                                   value="${p.toko_obat || ''}"
                                   placeholder="Contoh: Toko Tani Makmur"
                                   class="purchase-toko-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Harga (Rupiah)</label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 select-none">Rp</span>
                                <input type="text"
                                       inputmode="numeric"
                                       name="purchases[${idx}][harga]"
                                       value="${formattedPrice}"
                                       placeholder="Contoh: 68.000"
                                       class="purchase-harga-input w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-semibold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date"
                                   name="purchases[${idx}][tanggal_beli]"
                                   value="${pDate}"
                                   class="purchase-tanggal-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
                        </div>
                    </div>
                    <div class="store-match-hint text-[10px] text-amber-700 font-medium mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>Toko ini sudah terdaftar. Jika harga atau tanggal diubah, data toko ini akan otomatis diperbarui.</span>
                    </div>
                `;
                container.appendChild(row);
                bindRupiahInput(row.querySelector('.purchase-harga-input'));
            });
            updatePurchaseRowLabels();
        }
    }
}

function checkStoreMatching(tokoInput) {
    const row = tokoInput.closest('.purchase-row');
    if (!row) return;

    const hintEl = row.querySelector('.store-match-hint');
    const storeVal = (tokoInput.value || '').trim().toLowerCase();

    if (!storeVal || !currentMatchedMedicine) {
        if (hintEl) hintEl.classList.add('hidden');
        return;
    }

    const purchases = currentMatchedMedicine.purchases || [];
    const matchedPurchase = purchases.find(p => p.toko_obat && p.toko_obat.trim().toLowerCase() === storeVal);

    if (matchedPurchase && hintEl) {
        const prevPrice = matchedPurchase.formatted_harga || (matchedPurchase.harga ? 'Rp ' + Number(matchedPurchase.harga).toLocaleString('id-ID') : '—');
        hintEl.innerHTML = `
            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>Toko "${matchedPurchase.toko_obat}" sudah terdaftar (Harga sebelumnya: ${prevPrice}). Jika harga atau tanggal diubah, data toko ini akan otomatis diperbarui.</span>
        `;
        hintEl.classList.remove('hidden');

        // Jika input harga saat ini kosong, otomatis isikan harga sebelumnya
        const hargaInput = row.querySelector('.purchase-harga-input');
        if (hargaInput && !hargaInput.value) {
            let pClean = prevPrice.replace('Rp ', '');
            hargaInput.value = pClean;
            hargaInput.dispatchEvent(new Event('blur'));
        }
    } else if (hintEl) {
        hintEl.classList.add('hidden');
    }
}

window.addPurchaseRow = function() {
    const container = document.getElementById('purchases-container');
    const rows = container.querySelectorAll('.purchase-row');
    const nextIndex = rows.length;

    const row = document.createElement('div');
    row.className = 'purchase-row bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 relative transition-all animate-slide-up';
    row.dataset.index = nextIndex;
    row.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider purchase-row-label">
                Toko #${nextIndex + 1}
            </span>
            <button type="button" 
                    onclick="removePurchaseRow(this)"
                    class="btn-remove-purchase p-1 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                    title="Hapus toko ini">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama Toko</label>
                <input type="text"
                       name="purchases[${nextIndex}][toko_obat]"
                       placeholder="Contoh: Kios Subur Tani"
                       class="purchase-toko-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Harga (Rupiah)</label>
                <div class="relative">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 select-none">Rp</span>
                    <input type="text"
                           inputmode="numeric"
                           name="purchases[${nextIndex}][harga]"
                           placeholder="Contoh: 70.000"
                           class="purchase-harga-input w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-semibold">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tanggal Beli</label>
                <input type="date"
                       name="purchases[${nextIndex}][tanggal_beli]"
                       class="purchase-tanggal-input w-full px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs text-gray-900 font-medium">
            </div>
        </div>
        <div class="store-match-hint text-[10px] text-amber-700 font-medium mt-1.5 hidden flex items-center gap-1"></div>
    `;

    container.appendChild(row);
    bindRupiahInput(row.querySelector('.purchase-harga-input'));
    updatePurchaseRowLabels();
};

window.removePurchaseRow = function(btn) {
    const row = btn.closest('.purchase-row');
    const container = document.getElementById('purchases-container');
    if (container.querySelectorAll('.purchase-row').length > 1) {
        row.remove();
        updatePurchaseRowLabels();
    }
};

function updatePurchaseRowLabels() {
    const container = document.getElementById('purchases-container');
    const rows = container.querySelectorAll('.purchase-row');
    rows.forEach((r, i) => {
        r.dataset.index = i;
        const label = r.querySelector('.purchase-row-label');
        if (label) label.textContent = `Toko #${i + 1}`;
        const removeBtn = r.querySelector('.btn-remove-purchase');
        if (removeBtn) {
            if (rows.length === 1) removeBtn.classList.add('hidden');
            else removeBtn.classList.remove('hidden');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupPillGroup('.kategori-pill', true, false);
    setupPillGroup('.cara-kerja-pill', false, true);
    setupPillGroup('.fase-pill', false, true);

    // Bind price formatters
    const hargaInput = document.getElementById('harga');
    if (hargaInput) bindRupiahInput(hargaInput);

    document.querySelectorAll('.purchase-harga-input').forEach(el => {
        bindRupiahInput(el);
    });

    // Sinkronisasi otomatis input harga utama dengan toko pertama jika hanya ada 1 toko
    if (hargaInput) {
        hargaInput.addEventListener('input', () => {
            const rows = document.querySelectorAll('.purchase-row');
            if (rows.length === 1) {
                const firstRowHarga = rows[0].querySelector('.purchase-harga-input');
                if (firstRowHarga) {
                    firstRowHarga.value = hargaInput.value;
                }
            }
        });
    }

    document.addEventListener('input', (e) => {
        if (e.target && e.target.classList.contains('purchase-harga-input')) {
            const rows = document.querySelectorAll('.purchase-row');
            if (rows.length === 1 && rows[0].contains(e.target) && hargaInput) {
                hargaInput.value = e.target.value;
            }
        }
    });

    // Auto-fill obat ketika nama obat diketik atau dipilih dari datalist
    const namaInput = document.getElementById('nama');
    if (namaInput) {
        namaInput.addEventListener('input', (e) => handleMedicineNameLookup(e.target.value));
        namaInput.addEventListener('change', (e) => handleMedicineNameLookup(e.target.value));

        // Jika saat load nama sudah terisi dan belum dalam mode edit khusus
        if (namaInput.value && !document.querySelector('input[name="_method"]')) {
            handleMedicineNameLookup(namaInput.value);
        }
    }

    // Monitor input nama toko untuk mengecek apakah toko sudah pernah dicatat
    document.addEventListener('input', (e) => {
        if (e.target && e.target.classList.contains('purchase-toko-input')) {
            checkStoreMatching(e.target);
        }
    });

    // Handle 80% Image Compression on Photo Selection (Multi-Photo)
    const fileInput = document.getElementById('foto_nota');
    const compressProgress = document.getElementById('foto-compress-progress');
    const progressText = document.getElementById('foto-progress-text');
    const previewSection = document.getElementById('foto-preview-section');

    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files || []);
            if (files.length === 0) return;

            if (compressProgress && progressText) {
                progressText.textContent = `Sedang mengompres ${files.length} foto (~80%)...`;
                compressProgress.classList.remove('hidden');
            }
            if (previewSection) {
                previewSection.classList.remove('hidden');
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (progressText) {
                    progressText.textContent = `Mengompres foto ${i + 1} dari ${files.length}...`;
                }

                try {
                    const compressed = await compressImageFile(file);
                    selectedCompressedFiles.push(compressed);
                } catch (err) {
                    console.error('Kompresi foto gagal:', err);
                    selectedCompressedFiles.push({
                        file: file,
                        originalSize: file.size,
                        compressedSize: file.size,
                        dataUrl: URL.createObjectURL(file),
                        savings: 0,
                    });
                }
            }

            if (compressProgress) {
                compressProgress.classList.add('hidden');
            }

            syncFileInputAndRender();
        });
    }
});
</script>
@endpush
