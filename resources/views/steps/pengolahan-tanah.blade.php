@extends('layouts.app')

@section('title', 'Tahapan Pengolahan Tanah')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

    {{-- ── Alert Notifikasi Flash ── --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-base font-bold px-1.5">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-medium flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 text-base font-bold px-1.5">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm shadow-xs space-y-1">
            <div class="font-bold flex items-center gap-2 text-rose-950">
                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Mohon periksa kembali isian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-1 text-rose-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Breadcrumb & Title Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="/steps" class="hover:text-emerald-700 transition-colors font-medium">Tahapan</a>
                <span>/</span>
                <span class="text-emerald-700 font-semibold">Pengolahan Tanah</span>
            </nav>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Tahapan Pengolahan Tanah</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    Pra-Tanam (Fase 1)
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                    {{ count($steps) }} Langkah Tersimpan
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Standar operasional penyiapan lahan gembur, subur, bebas patogen, dan memiliki drainase optimal.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">

            <a href="/steps/penanaman-bibit"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-semibold hover:bg-emerald-700 active:scale-95 transition-all shadow-xs">
                <span>Lanjut: Penanaman Bibit</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- ── Quick Navigation Tabs ── --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit max-w-full overflow-x-auto no-scrollbar">
        <a href="/steps"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            Ikhtisar Tahapan
        </a>
        <a href="/steps/pengolahan-tanah"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-amber-800 shadow-xs whitespace-nowrap transition-all">
            1. Pengolahan Tanah
        </a>
        <a href="/steps/penanaman-bibit"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            2. Penanaman Bibit
        </a>
    </div>

    {{-- ── Petunjuk Praktis Foto Kamera HP ── --}}
    <div class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-amber-900">
        <div class="flex items-start sm:items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                </svg>
            </span>
            <div>
                <p class="font-bold text-amber-950">Dokumentasi Foto Lapangan & Kamera Langsung di HP</p>
                <p class="text-amber-800 mt-0.5">Anda bisa mengedit seluruh isi pengolahan tanah, mengubah atau menambah nomor tahapan, serta mengunggah foto dari galeri atau <strong>menjepret langsung dengan kamera HP</strong>.</p>
            </div>
        </div>
        <button type="button"
                onclick="openAddStepModal()"
                class="self-start sm:self-auto shrink-0 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-colors">
            + Tambah Langkah Baru
        </button>
    </div>

    {{-- ── Step by Step Details ── --}}
    <div class="space-y-4">

        @forelse($steps as $step)
            <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-amber-300 transition-all group">
                <div class="flex items-start gap-4">

                    {{-- Badge Nomor Langkah --}}
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200 shadow-2xs">
                        {{ $step->nomor }}
                    </div>

                    <div class="flex-1 min-w-0">
                        {{-- Baris Judul & Aksi --}}
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-amber-900 transition-colors">
                                    {!! $step->judul !!}
                                </h2>
                                @if($step->waktu)
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                                        Waktu: {{ $step->waktu }}
                                    </span>
                                @endif
                            </div>

                            {{-- Tombol Aksi: Edit & Hapus --}}
                            <div class="flex items-center gap-1.5 self-start shrink-0 flex-wrap">
                                {{-- Tombol Edit Semua --}}
                                <button type="button"
                                        onclick="openEditStepModal({{ json_encode($step) }})"
                                        title="Edit seluruh isi pengolahan tanah ini"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 text-xs font-semibold transition-all">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                {{-- Tombol Hapus --}}
                                <button type="button"
                                        onclick="confirmDeleteStep({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                        title="Hapus tahapan ini"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($step->deskripsi)) !!}
                        </div>

                        {{-- Spesifikasi Teknis (jika ada) --}}
                        @if(!empty($step->spesifikasi) && is_array($step->spesifikasi))
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3 text-center">
                                @foreach($step->spesifikasi as $spec)
                                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                                        <span class="text-[11px] text-gray-500 block">{{ $spec['label'] ?? '' }}</span>
                                        <span class="text-xs font-bold text-gray-800">{{ $spec['nilai'] ?? '' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Tips Praktisi --}}
                        @if($step->tips)
                            <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                                <p class="font-semibold text-amber-950 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-700 inline shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.002 6.002 0 00-4-5.659V5a2 2 0 114 0v2.091a6.002 6.002 0 004 5.659M12 12.75v5.25m-3 0h6"/>
                                    </svg>
                                    <span>Tips Praktisi:</span>
                                </p>
                                <p>{!! nl2br(e($step->tips)) !!}</p>
                            </div>
                        @endif

                        {{-- ── Baris Tombol Foto: Lihat Foto (Jumlah) ── --}}
                        <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-center justify-between gap-2 flex-wrap">
                            @if(count($step->foto_urls) > 0)
                                <button type="button"
                                        onclick="openStepPhotosModal({{ json_encode($step) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-semibold transition-all shadow-2xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                    <span>Lihat Foto ({{ count($step->foto_urls) }})</span>
                                </button>

                                <button type="button"
                                        onclick="openQuickPhotoModal({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-emerald-700 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    <span>Tambah Foto</span>
                                </button>
                            @else
                                <span class="text-[11px] text-gray-400 italic">Belum ada foto</span>
                                <button type="button"
                                        onclick="openQuickPhotoModal({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:text-emerald-800 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                                    </svg>
                                    <span>Ambil / Upload Foto</span>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface rounded-2xl border border-dashed border-gray-300 p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747M3 12h18"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Belum Ada Tahapan Pengolahan Tanah</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto">Mulai tambahkan langkah atau nomor pengolahan tanah pertama Anda lengkap dengan estimasi waktu dan foto dokumentasi.</p>
                <button type="button"
                        onclick="openAddStepModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 shadow-xs">
                    + Tambah Tahapan Pertama
                </button>
            </div>
        @endforelse

    </div>

    {{-- ── Tombol Tambah Nomor / Langkah di Bawah ── --}}
    <div class="flex items-center justify-center pt-2">
        <button type="button"
                onclick="openAddStepModal()"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white border-2 border-dashed border-amber-300 text-amber-800 hover:bg-amber-50 hover:border-amber-400 font-bold text-xs sm:text-sm transition-all shadow-xs active:scale-95">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Nomor / Langkah Pengolahan Baru</span>
        </button>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1: TAMBAH TAHAPAN / NOMOR BARU                           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-step" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Tambah Tahapan / Nomor Pengolahan Tanah</h3>
                <p class="text-xs text-gray-500 mt-0.5">Buat langkah operasional baru lengkap dengan nomor urut & dokumentasi foto.</p>
            </div>
            <button type="button" onclick="closeModal('modal-add-step')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">✕</button>
        </div>

        <form id="form-add-step"
              action="{{ route('steps.pengolahan-tanah.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="mt-4 space-y-4">
            @csrf

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
                           value="{{ count($steps) + 1 }}"
                           placeholder="Misal: 7"
                           class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-amber-900 bg-amber-50/50 focus:bg-white focus:border-amber-500">
                </div>

                {{-- Waktu / Estimasi --}}
                <div class="sm:col-span-2">
                    <label for="add-waktu" class="block text-xs font-semibold text-gray-700 mb-1">
                        Estimasi Waktu Pelaksanaan
                    </label>
                    <input type="text"
                           id="add-waktu"
                           name="waktu"
                           placeholder="Misal: H-5 s/d H-3 Sebelum Tanam"
                           class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
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
                       placeholder="Misal: Pemasangan Selang Drip Irigasi Tetes"
                       class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
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
                          placeholder="Jelaskan secara rinci tindakan pengolahan tanah yang dilakukan..."
                          class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Tips Praktisi --}}
            <div>
                <label for="add-tips" class="block text-xs font-semibold text-gray-700 mb-1">
                    Tips Praktisi Lapangan (Opsional)
                </label>
                <textarea id="add-tips"
                          name="tips"
                          rows="2"
                          placeholder="Tips tambahan, anjuran cuaca, atau catatan kehati-hatian..."
                          class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Upload & Ambil Kamera Foto --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Dokumentasi Foto (Kamera HP Langsung / Unggah Galeri)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {{-- Opsi 1: Jepret Kamera Langsung di HP --}}
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
                               onchange="previewFiles(this, 'add-photo-preview')"
                               class="hidden">
                    </label>

                    {{-- Opsi 2: Unggah dari Galeri / Berkas --}}
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 bg-gray-50 hover:bg-emerald-50/40 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">Pilih dari Galeri / File</span>
                        <input type="file"
                               name="foto[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewFiles(this, 'add-photo-preview')"
                               class="hidden">
                    </label>
                </div>

                {{-- Pratinjau Foto --}}
                <div id="add-photo-preview-wrap" class="hidden pt-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5 px-0.5">
                        <span id="add-photo-preview-count" class="font-semibold text-gray-700">Foto Terpilih:</span>
                        <button type="button" onclick="clearAllPreviewFiles('add-photo-preview')" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold text-[11px] cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>
                    <div id="add-photo-preview" class="grid grid-cols-4 gap-2"></div>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-add-step')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs">
                    Simpan Tahapan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2: EDIT SEMUA ISI PENGOLAHAN TANAH                       --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-step" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Edit Tahapan Pengolahan Tanah</h3>
                <p class="text-xs text-gray-500 mt-0.5">Ubah nomor langkah, judul, estimasi waktu, deskripsi, tips, dan foto.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-step')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">✕</button>
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
                           placeholder="Misal: 1"
                           class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-amber-900 bg-amber-50/50 focus:bg-white focus:border-amber-500">
                </div>

                {{-- Waktu / Estimasi --}}
                <div class="sm:col-span-2">
                    <label for="edit-waktu" class="block text-xs font-semibold text-gray-700 mb-1">
                        Estimasi Waktu Pelaksanaan
                    </label>
                    <input type="text"
                           id="edit-waktu"
                           name="waktu"
                           placeholder="Misal: H-30 s/d H-21 Sebelum Tanam"
                           class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
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
                       class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="edit-deskripsi" class="block text-xs font-bold text-gray-700 mb-1">
                    Deskripsi Lengkap Langkah <span class="text-rose-500">*</span>
                </label>
                <textarea id="edit-deskripsi"
                          name="deskripsi"
                          rows="4"
                          required
                          class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Tips Praktisi --}}
            <div>
                <label for="edit-tips" class="block text-xs font-semibold text-gray-700 mb-1">
                    Tips Praktisi Lapangan (Opsional)
                </label>
                <textarea id="edit-tips"
                          name="tips"
                          rows="2"
                          class="field-input w-full px-3 py-2 rounded-xl border border-gray-300 text-xs"></textarea>
            </div>

            {{-- Foto yang sudah ada --}}
            <div id="edit-existing-photos-section" class="pt-2 border-t border-gray-100 hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Foto yang Tersimpan (Centang merah untuk menghapus):
                </label>
                <div id="edit-existing-photos-grid" class="grid grid-cols-4 gap-2"></div>
                <div id="edit-deleted-photos-container"></div>
            </div>

            {{-- Tambah Foto Baru (Kamera / Galeri) --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Tambah Foto Baru (Kamera Langsung atau Galeri)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {{-- Opsi 1: Jepret Kamera Langsung di HP --}}
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
                               onchange="previewFiles(this, 'edit-photo-preview')"
                               class="hidden">
                    </label>

                    {{-- Opsi 2: Unggah dari Galeri / Berkas --}}
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 hover:border-emerald-500 bg-gray-50 hover:bg-emerald-50/40 rounded-xl cursor-pointer transition-colors text-center">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">Pilih dari Galeri / File</span>
                        <input type="file"
                               name="foto[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewFiles(this, 'edit-photo-preview')"
                               class="hidden">
                    </label>
                </div>

                {{-- Pratinjau Foto Baru --}}
                <div id="edit-photo-preview-wrap" class="hidden pt-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5 px-0.5">
                        <span id="edit-photo-preview-count" class="font-semibold text-gray-700">Foto Baru Terpilih:</span>
                        <button type="button" onclick="clearAllPreviewFiles('edit-photo-preview')" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold text-[11px] cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>
                    <div id="edit-photo-preview" class="grid grid-cols-4 gap-2"></div>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button"
                        onclick="closeModal('modal-edit-step')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 3: JEPRET KAMERA / UPLOAD FOTO CEPAT                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-quick-photo" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tambah Foto Dokumentasi</h3>
                <p id="quick-photo-subtitle" class="text-xs text-gray-500 mt-0.5">Jepret langsung atau pilih foto</p>
            </div>
            <button type="button" onclick="closeModal('modal-quick-photo')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">✕</button>
        </div>

        <form id="form-quick-photo"
              method="POST"
              enctype="multipart/form-data"
              onsubmit="return validateQuickPhotoSubmit()"
              class="mt-4 space-y-4">
            @csrf

            <div class="space-y-2.5">
                {{-- Opsi 1: Jepret Kamera Langsung di HP --}}
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

                {{-- Opsi 2: Unggah dari Galeri --}}
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
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">
                    Simpan Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 4: LIHAT FOTO TAHAPAN                                   --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-view-step-photos" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
            <div>
                <h3 id="view-photos-title" class="text-base sm:text-lg font-bold text-gray-900">Foto Dokumentasi</h3>
                <p id="view-photos-subtitle" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-view-step-photos')" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">✕</button>
        </div>

        {{-- Grid Foto --}}
        <div id="view-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 my-4 overflow-y-auto max-h-[60vh] p-1">
            {{-- Dimuat melalui JavaScript openStepPhotosModal --}}
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 shrink-0">
            <button type="button"
                    id="view-photos-add-btn"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah / Jepret Foto</span>
            </button>
            <button type="button"
                    onclick="closeModal('modal-view-step-photos')"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 5: KONFIRMASI HAPUS TAHAPAN                             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-delete-step" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-200 text-center">
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900">Hapus Tahapan Ini?</h3>
        <p id="delete-step-message" class="text-xs text-gray-500 mt-1">Langkah yang dihapus tidak dapat dipulihkan kembali.</p>

        <form id="form-delete-step" method="POST" class="mt-5 flex items-center justify-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button"
                    onclick="closeModal('modal-delete-step')"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold flex-1">
                Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs flex-1">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 5: LIGHTBOX PRATINJAU FOTO BESAR                         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm" onclick="closeLightbox(event)">
    <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center">
        <button type="button"
                onclick="closeModal('modal-lightbox')"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold p-1">
            ✕
        </button>
        <img id="lightbox-image"
             src=""
             alt="Foto Dokumentasi"
             class="max-w-full max-h-[80vh] rounded-2xl object-contain shadow-2xl border border-white/20">
        <p id="lightbox-title" class="text-white text-xs sm:text-sm font-semibold mt-3 text-center"></p>
    </div>
</div>

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
        if (id === 'modal-add-step') clearAllPreviewFiles('add-photo-preview');
        if (id === 'modal-edit-step') clearAllPreviewFiles('edit-photo-preview');
    }

    function openAddStepModal() {
        const form = document.getElementById('form-add-step');
        if (form) form.reset();
        clearAllPreviewFiles('add-photo-preview');
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
                    if (!u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/')) {
                        u = '/storage/' + u.replace(/^\/+/, '');
                    }
                    return u;
                }
                if (item && typeof item === 'object') {
                    let u = item.url || item.secure_url || item.path || '';
                    if (u && !u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/')) {
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
        if (form) form.reset();
        form.action = '/steps/pengolahan-tanah/' + step.id;

        document.getElementById('edit-nomor').value = step.nomor || '';
        document.getElementById('edit-judul').value = step.judul || '';
        document.getElementById('edit-waktu').value = step.waktu || '';
        document.getElementById('edit-deskripsi').value = step.deskripsi || '';
        document.getElementById('edit-tips').value = step.tips || '';

        // Reset pratinjau foto baru
        clearAllPreviewFiles('edit-photo-preview');

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
                            class="absolute top-1 right-1 px-1.5 py-0.5 rounded bg-black/60 hover:bg-rose-600 text-white text-[10px] font-bold transition-colors">
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
                <form action="/steps/pengolahan-tanah/${step.id}/foto"
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
                        class="absolute bottom-1.5 right-1.5 p-1.5 rounded-lg bg-black/60 hover:bg-black/80 text-white text-xs shadow-xs transition-colors z-10"
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
            openQuickPhotoModal(step.id, step.nomor, step.judul);
        };

        openModal('modal-view-step-photos');
    }

    function openQuickPhotoModal(stepId, nomor, judul) {
        const form = document.getElementById('form-quick-photo');
        if (form) form.reset();
        form.action = '/steps/pengolahan-tanah/' + stepId + '/foto';
        document.getElementById('quick-photo-subtitle').textContent = 'Langkah ' + nomor + ': ' + judul;
        clearAllPreviewFiles('quick-photo-preview');
        openModal('modal-quick-photo');
    }

    function confirmDeleteStepPhoto(btn) {
        const form = btn.closest('form');
        if (!form) return;

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Foto Dokumentasi?',
                'Foto dokumentasi ini beserta file fisiknya di server',
                function() {
                    form.submit();
                }
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
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return;
        }

        if (confirm('Hapus foto ini secara permanen dari penyimpanan server?')) {
            form.submit();
        }
    }

    function confirmDeleteStep(stepId, nomor, judul) {
        const form = document.getElementById('form-delete-step');
        form.action = '/steps/pengolahan-tanah/' + stepId;

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Tahapan Ini?',
                `Langkah ${nomor}: ${judul}`,
                function() {
                    form.submit();
                }
            );
            return;
        }

        if (window.Swal) {
            Swal.fire({
                title: 'Hapus Tahapan Ini?',
                html: `Apakah Anda yakin ingin menghapus <strong>Langkah ${nomor}: "${judul}"</strong> beserta seluruh foto dokumentasinya?<br><span class="text-xs text-gray-500">Langkah yang dihapus tidak dapat dipulihkan kembali.</span>`,
                icon: 'warning',
                iconColor: '#E4574C',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#E4574C',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return;
        }

        document.getElementById('delete-step-message').textContent = 'Langkah nomor ' + nomor + ' ("' + judul + '") beserta seluruh foto dokumentasinya akan dihapus.';
        openModal('modal-delete-step');
    }

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

    // Manajemen Foto Pratinjau & Pembatalan Pilihan Foto
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
            ['modal-add-step', 'modal-edit-step', 'modal-quick-photo', 'modal-view-step-photos', 'modal-delete-step', 'modal-lightbox'].forEach(closeModal);
        }
    });
</script>
@endsection
