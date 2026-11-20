@extends('layouts.app')

@section('title', 'Tahapan Fermentasi Pupuk Organik & Kohe')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

    {{-- ── Notifikasi Sukses / Error ── --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-teal-700 hover:text-teal-950 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs sm:text-sm space-y-1 shadow-xs">
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
                <span class="text-teal-700 font-semibold">Tahapan Fermentasi Pupuk</span>
            </nav>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Tahapan Fermentasi Pupuk</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                    Fase 1: Pupuk Organik
                </span>
                <span id="steps-counter" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                    {{ count($steps) }} Langkah Tersimpan
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Standar operasional fermentasi kotoran hewan (kohe) & kompos organik agar matang sempurna, kaya hara, dan bebas jamur patogen.</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button"
                    onclick="openAddStepModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Langkah Baru</span>
            </button>
        </div>
    </div>

    {{-- ── Quick Navigation Tabs ── --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit max-w-full overflow-x-auto no-scrollbar">
        <a href="/steps/fermentasi-pupuk"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-teal-800 shadow-xs whitespace-nowrap transition-all">
            1. Fermentasi Pupuk
        </a>
        <a href="/steps/pengolahan-tanah"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            2. Pengolahan Tanah
        </a>
        <a href="/steps/penanaman-bibit"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            3. Penanaman Bibit
        </a>
    </div>

    {{-- ── Petunjuk Praktis Dokumentasi Lapangan ── --}}
    <div class="bg-teal-50/70 border border-teal-200/90 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-teal-950">
        <div class="flex items-start sm:items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                </svg>
            </span>
            <div>
                <p class="font-bold text-teal-950">Dokumentasi Fermentasi & Kamera Langsung HP</p>
                <p class="text-teal-800 mt-0.5">Seluruh SOP fermentasi dapat disunting bebas. Anda dapat menambahkan foto dokumentasi perubahan warna pupuk, uap suhu, atau menjepret langsung dengan kamera HP.</p>
            </div>
        </div>
        <button type="button"
                onclick="openAddStepModal()"
                class="self-start sm:self-auto shrink-0 px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs transition-colors">
            + Tambah Langkah Baru
        </button>
    </div>

    {{-- ── Step by Step Details ── --}}
    <div class="space-y-4" id="fermentasi-steps-list">

        @forelse($steps as $step)
            <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-teal-300 transition-all group" id="step-card-{{ $step->id }}">
                <div class="flex items-start gap-4">

                    {{-- Badge Nomor Langkah --}}
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-black text-sm shrink-0 border border-teal-200 shadow-2xs">
                        {{ $step->nomor }}
                    </div>

                    <div class="flex-1 min-w-0">
                        {{-- Baris Judul & Aksi --}}
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-teal-900 transition-colors">
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
                                {{-- Tombol Edit --}}
                                <button type="button"
                                        onclick="openEditStepModal({{ json_encode($step) }})"
                                        title="Edit tahapan ini"
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
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-3 text-center">
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
                            <div class="mt-3 bg-teal-50/60 rounded-xl p-3 border border-teal-100 text-xs text-teal-900 space-y-1">
                                <p class="font-semibold text-teal-950 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-teal-700 inline shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.002 6.002 0 00-4-5.659V5a2 2 0 114 0v2.091a6.002 6.002 0 004 5.659M12 12.75v5.25m-3 0h6"/>
                                    </svg>
                                    <span>Tips Fermentasi:</span>
                                </p>
                                <p>{!! nl2br(e($step->tips)) !!}</p>
                            </div>
                        @endif

                        {{-- ── Baris Tombol Foto ── --}}
                        <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-center justify-between gap-2 flex-wrap">
                            @if(count($step->foto_urls) > 0)
                                <button type="button"
                                        onclick="openStepPhotosModal({{ json_encode($step) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-semibold transition-all shadow-2xs">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                    <span>Lihat Foto ({{ count($step->foto_urls) }})</span>
                                </button>

                                <button type="button"
                                        onclick="openQuickPhotoModal({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-teal-700 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span>Tambah / Jepret Foto</span>
                                </button>
                            @else
                                <span class="text-[11px] text-gray-400 italic">Belum ada foto dokumentasi</span>
                                <button type="button"
                                        onclick="openQuickPhotoModal({{ $step->id }}, '{{ addslashes($step->nomor) }}', '{{ addslashes($step->judul) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-dashed border-teal-300 text-teal-800 hover:bg-teal-50 text-xs font-semibold transition-all">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                                    </svg>
                                    <span>Tambah / Jepret Foto</span>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface rounded-2xl border border-gray-200 p-12 text-center text-gray-500 shadow-xs">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <h3 class="text-base font-bold text-gray-800">Belum Ada Tahapan Fermentasi Pupuk</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Klik tombol "Tambah Langkah Baru" di atas untuk menambahkan panduan SOP fermentasi kompos atau kohe Anda.</p>
            </div>
        @endforelse

    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL TAMBAH LANGKAH FERMENTASI
══════════════════════════════════════════════════════════════════════ --}}
<div id="modal-add-step" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-surface w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden my-8 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/50">
            <div>
                <h3 class="font-extrabold text-base text-gray-900">Tambah Langkah Fermentasi Pupuk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi langkah panduan dekomposisi pupuk organik baru.</p>
            </div>
            <button type="button" onclick="closeModal('modal-add-step')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-add-step" action="/steps/fermentasi-pupuk" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Nomor Langkah <span class="text-rose-500">*</span></label>
                    <input type="text" name="nomor" required placeholder="Contoh: 1 atau 1.1" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                </div>
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Estimasi Waktu</label>
                    <input type="text" name="waktu" placeholder="Contoh: Hari ke-1 s/d Hari ke-3" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Judul Langkah <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" required placeholder="Contoh: Pencampuran Kohe & Aktivasi Bakteri" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" rows="4" required placeholder="Tuliskan instruksi langkah kerja fermentasi secara detail..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden"></textarea>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Tips Fermentasi</label>
                <textarea name="tips" rows="2" placeholder="Catatan kehati-hatian atau saran praktis..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden"></textarea>
            </div>

            {{-- Upload Foto (Galeri & Kamera Langsung) --}}
            <div class="space-y-2 pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700">Foto Dokumentasi (Opsional)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-teal-500 cursor-pointer bg-gray-50 hover:bg-teal-50/50 transition-colors">
                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Pilih dari Galeri</span>
                        <input type="file" name="foto[]" multiple accept="image/*" class="hidden" onchange="previewFiles(this, 'add-photo-preview')">
                    </label>

                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-dashed border-teal-300 hover:border-teal-600 cursor-pointer bg-teal-50/60 hover:bg-teal-100/50 transition-colors">
                        <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-semibold text-teal-900">Jepret Kamera HP</span>
                        <input type="file" name="foto_kamera[]" accept="image/*" capture="environment" class="hidden" onchange="previewFiles(this, 'add-photo-preview')">
                    </label>
                </div>
                <div id="add-photo-preview" class="grid grid-cols-3 sm:grid-cols-4 gap-2 pt-2"></div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-add-step')" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-xs">
                    Simpan Langkah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL EDIT LANGKAH FERMENTASI
══════════════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-step" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-surface w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden my-8 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/50">
            <div>
                <h3 class="font-extrabold text-base text-gray-900">Edit Langkah Fermentasi Pupuk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui isi instruksi, waktu, atau kelola foto dokumentasi.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-step')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-step" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Nomor Langkah <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-nomor" name="nomor" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                </div>
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Estimasi Waktu</label>
                    <input type="text" id="edit-waktu" name="waktu" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Judul Langkah <span class="text-rose-500">*</span></label>
                <input type="text" id="edit-judul" name="judul" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="4" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden"></textarea>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-700">Tips Fermentasi</label>
                <textarea id="edit-tips" name="tips" rows="2" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-hidden"></textarea>
            </div>

            {{-- Foto yang sudah tersimpan --}}
            <div id="edit-existing-photos-section" class="space-y-2 pt-2 border-t border-gray-100 hidden">
                <label class="block text-xs font-bold text-gray-700">Foto Tersimpan (Klik untuk menandai hapus)</label>
                <div id="edit-existing-photos-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-2"></div>
                <div id="edit-deleted-photos-container"></div>
            </div>

            {{-- Unggah Foto Baru --}}
            <div class="space-y-2 pt-2 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700">Tambah Foto Baru (Opsional)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-teal-500 cursor-pointer bg-gray-50 hover:bg-teal-50/50 transition-colors">
                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Galeri</span>
                        <input type="file" name="foto[]" multiple accept="image/*" class="hidden" onchange="previewFiles(this, 'edit-photo-preview')">
                    </label>

                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-dashed border-teal-300 hover:border-teal-600 cursor-pointer bg-teal-50/60 hover:bg-teal-100/50 transition-colors">
                        <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        <span class="text-xs font-semibold text-teal-900">Kamera HP</span>
                        <input type="file" name="foto_kamera[]" accept="image/*" capture="environment" class="hidden" onchange="previewFiles(this, 'edit-photo-preview')">
                    </label>
                </div>
                <div id="edit-photo-preview" class="grid grid-cols-3 sm:grid-cols-4 gap-2 pt-2"></div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-edit-step')" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL VIEW FOTO STEP
══════════════════════════════════════════════════════════════════════ --}}
<div id="modal-view-step-photos" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/60">
            <div>
                <h3 id="view-photos-title" class="font-extrabold text-base text-gray-900">Foto Dokumentasi Fermentasi</h3>
                <p id="view-photos-subtitle" class="text-xs text-gray-500 mt-0.5">Daftar foto dokumentasi tersimpan</p>
            </div>
            <button type="button" onclick="closeModal('modal-view-step-photos')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <div id="view-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
        </div>

        <div class="px-6 py-3.5 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between shrink-0">
            <button type="button" id="view-photos-add-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah / Jepret Foto</span>
            </button>
            <button type="button" onclick="closeModal('modal-view-step-photos')" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL QUICK PHOTO UPLOAD (KAMERA & GALERI)
══════════════════════════════════════════════════════════════════════ --}}
<div id="modal-quick-photo" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface w-full max-w-md rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/50">
            <div>
                <h3 class="font-extrabold text-base text-gray-900">Unggah Dokumentasi Foto</h3>
                <p id="quick-photo-subtitle" class="text-xs text-gray-500 mt-0.5">Langkah fermentasi pupuk</p>
            </div>
            <button type="button" onclick="closeModal('modal-quick-photo')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-quick-photo" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-dashed border-gray-300 hover:border-teal-500 cursor-pointer bg-gray-50 hover:bg-teal-50/50 transition-colors text-center">
                    <svg class="w-7 h-7 text-teal-600 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span class="text-xs font-bold text-gray-800">Buka Galeri</span>
                    <span class="text-[10px] text-gray-400">Pilih foto dari penyimpanan</span>
                    <input type="file" name="foto[]" multiple accept="image/*" class="hidden" onchange="previewFiles(this, 'quick-photo-preview')">
                </label>

                <label class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-dashed border-teal-300 hover:border-teal-600 cursor-pointer bg-teal-50/60 hover:bg-teal-100/50 transition-colors text-center">
                    <svg class="w-7 h-7 text-teal-700 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                    </svg>
                    <span class="text-xs font-bold text-teal-900">Jepret Kamera</span>
                    <span class="text-[10px] text-teal-700">Foto langsung pakai kamera HP</span>
                    <input type="file" name="foto_kamera[]" accept="image/*" capture="environment" class="hidden" onchange="previewFiles(this, 'quick-photo-preview')">
                </label>
            </div>

            <div id="quick-photo-preview" class="grid grid-cols-3 gap-2 pt-2"></div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-quick-photo')" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-xs">
                    Unggah Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL LIGHTBOX PERBESAR FOTO
══════════════════════════════════════════════════════════════════════ --}}
<div id="lightbox-modal" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4" onclick="closeLightbox()">
    <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
        <img id="lightbox-img" src="" alt="Pratinjau Foto" class="max-w-full max-h-[80vh] object-contain rounded-xl shadow-2xl">
        <p id="lightbox-caption" class="text-white text-xs sm:text-sm mt-3 text-center px-4 font-medium"></p>
        <button type="button" onclick="closeLightbox()" class="absolute -top-10 right-0 text-white/80 hover:text-white p-2 text-sm font-bold">
            ✕ Tutup
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     FORM TERSEMBUNYI UNTUK HAPUS LANGKAH
══════════════════════════════════════════════════════════════════════ --}}
<form id="form-delete-step" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.remove('hidden');
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.add('hidden');
        if (id === 'modal-add-step') clearAllPreviewFiles('add-photo-preview');
        if (id === 'modal-edit-step') clearAllPreviewFiles('edit-photo-preview');
        if (id === 'modal-quick-photo') clearAllPreviewFiles('quick-photo-preview');
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
                    if (!u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/') && !u.startsWith('data:')) {
                        u = '/storage/' + u.replace(/^\/+/, '');
                    }
                    return u;
                }
                return item.url || item.secure_url || item.path || '';
            }).filter(Boolean);
        }
        return [];
    }

    function openEditStepModal(step) {
        const form = document.getElementById('form-edit-step');
        form.action = '/steps/fermentasi-pupuk/' + step.id;

        document.getElementById('edit-nomor').value = step.nomor || '';
        document.getElementById('edit-waktu').value = step.waktu || '';
        document.getElementById('edit-judul').value = step.judul || '';
        document.getElementById('edit-deskripsi').value = step.deskripsi || '';
        document.getElementById('edit-tips').value = step.tips || '';

        clearAllPreviewFiles('edit-photo-preview');

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
                            class="absolute inset-x-0 bottom-0 py-1 bg-black/60 hover:bg-rose-600 text-white text-[10px] font-bold text-center transition-colors">
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

    function toggleDeleteExistingPhoto(btn, url, itemId) {
        const itemEl = document.getElementById(itemId);
        const deletedContainer = document.getElementById('edit-deleted-photos-container');
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
                
                {{-- Tombol Hapus Langsung --}}
                <form action="/steps/fermentasi-pupuk/${step.id}/foto"
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
        form.action = '/steps/fermentasi-pupuk/' + stepId + '/foto';
        document.getElementById('quick-photo-subtitle').textContent = 'Langkah ' + nomor + ': ' + judul;
        clearAllPreviewFiles('quick-photo-preview');
        openModal('modal-quick-photo');
    }

    function executeDeleteStepPhoto(form, btn) {
        const itemEl = btn.closest('.group\\/photo') || btn.closest('.relative');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>`;

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async (res) => {
            if (!res.ok) {
                let msg = 'Gagal menghapus foto.';
                try {
                    const d = await res.json();
                    if (d && d.message) msg = d.message;
                } catch(e) {}
                throw new Error(msg);
            }
            return res.json();
        })
        .then((data) => {
            if (data && data.step) {
                updateOrInsertStepCard(data.step);
            }
            if (itemEl) {
                itemEl.style.transition = 'all 0.25s ease';
                itemEl.style.opacity = '0';
                itemEl.style.transform = 'scale(0.85)';
                setTimeout(() => {
                    itemEl.remove();
                    const grid = document.getElementById('view-photos-grid');
                    const remaining = grid ? grid.querySelectorAll('img').length : 0;
                    const subtitle = document.getElementById('view-photos-subtitle');
                    if (subtitle) {
                        subtitle.textContent = remaining + ' foto dokumentasi tersimpan';
                    }
                    if (remaining === 0 && grid) {
                        grid.innerHTML = `
                            <div class="col-span-full py-10 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-600">Belum ada foto dokumentasi untuk tahapan ini</p>
                            </div>
                        `;
                    }
                }, 250);
            }
            if (window.AgriSwal) {
                AgriSwal.toastSuccess('Foto berhasil dihapus');
            } else if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Foto berhasil dihapus',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        })
        .catch((err) => {
            console.error('AJAX delete failed, fallback to form.submit():', err);
            form.submit();
        });
    }

    function updateStepsCounter() {
        const counter = document.getElementById('steps-counter');
        const list = document.getElementById('fermentasi-steps-list');
        if (counter && list) {
            const count = list.querySelectorAll('[id^="step-card-"]').length;
            counter.textContent = count + ' Langkah Tersimpan';
        }
    }

    function confirmDeleteStepPhoto(btn) {
        const form = btn.closest('form');
        if (!form) return;

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Foto Dokumentasi?',
                'Foto dokumentasi ini beserta file fisiknya di server',
                function() {
                    executeDeleteStepPhoto(form, btn);
                }
            );
            return;
        }

        executeDeleteStepPhoto(form, btn);
    }

    function confirmDeleteStep(stepId, nomor, judul) {
        const form = document.getElementById('form-delete-step');
        form.action = '/steps/fermentasi-pupuk/' + stepId;

        const handleDeleteConfirmed = async () => {
            const card = document.getElementById('step-card-' + stepId);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0.4';
                card.style.pointerEvents = 'none';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('_method', 'DELETE');

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    body: fd
                });

                const result = await response.json().catch(() => null);

                if (response.ok && result && result.success !== false) {
                    if (card) {
                        card.style.transform = 'scale(0.95)';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            updateStepsCounter();
                            const list = document.getElementById('fermentasi-steps-list');
                            if (list && list.querySelectorAll('[id^="step-card-"]').length === 0) {
                                list.innerHTML = `
                                    <div class="bg-surface rounded-2xl border border-gray-200 p-12 text-center text-gray-500 shadow-xs">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        <h3 class="text-base font-bold text-gray-800">Belum Ada Tahapan Fermentasi Pupuk</h3>
                                        <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Klik tombol "+ Tambah Langkah Baru" di atas untuk menambahkan panduan SOP fermentasi kompos atau kohe Anda.</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                    if (window.AgriSwal) {
                        AgriSwal.toastSuccess(result.message || 'Langkah berhasil dihapus.');
                    }
                } else {
                    if (card) {
                        card.style.opacity = '1';
                        card.style.pointerEvents = 'auto';
                    }
                    if (window.AgriSwal) {
                        AgriSwal.toastError(result?.message || 'Gagal menghapus langkah.');
                    }
                }
            } catch (err) {
                console.warn('Network error during delete step:', err);
                if (card) {
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
                form.submit();
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Langkah Nomor ' + nomor + '?',
                'Tahapan "' + judul + '" beserta semua foto dokumentasinya',
                handleDeleteConfirmed
            );
            return;
        }

        handleDeleteConfirmed();
    }

    function openLightbox(url, caption) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-caption').textContent = caption || '';
        document.getElementById('lightbox-modal').classList.remove('hidden');
    }

    function closeLightbox() {
        document.getElementById('lightbox-modal').classList.add('hidden');
    }

    function previewFiles(input, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';

        if (!input.files || input.files.length === 0) return;

        Array.from(input.files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative rounded-xl overflow-hidden border border-gray-200 aspect-square bg-gray-100 shadow-2xs';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md bg-black/70 text-white text-[9px] font-mono truncate max-w-[90%]">
                        ${(file.size / 1024).toFixed(0)}KB
                    </span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function clearAllPreviewFiles(containerId) {
        const container = document.getElementById(containerId);
        if (container) container.innerHTML = '';
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

    function renderStepCardHtml(step) {
        const rawPhotos = getStepPhotos(step);
        const photoCount = rawPhotos.length;
        const escapedNomor = escapeHtml(step.nomor || '');
        const escapedJudul = escapeHtml(step.judul || '');
        const escapedWaktu = escapeHtml(step.waktu || '');
        const escapedDeskripsi = escapeHtml(step.deskripsi || '').replace(/\n/g, '<br>');
        const escapedTips = escapeHtml(step.tips || '').replace(/\n/g, '<br>');
        const stepJsonAttr = escapeHtml(JSON.stringify(step));

        let waktuBadge = '';
        if (step.waktu) {
            waktuBadge = `<span class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">Waktu: ${escapedWaktu}</span>`;
        }

        let tipsBox = '';
        if (step.tips) {
            tipsBox = `
                <div class="mt-3 bg-teal-50/60 rounded-xl p-3 border border-teal-100 text-xs text-teal-900 space-y-1">
                    <p class="font-semibold text-teal-950 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-teal-700 inline shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.002 6.002 0 00-4-5.659V5a2 2 0 114 0v2.091a6.002 6.002 0 004 5.659M12 12.75v5.25m-3 0h6"/>
                        </svg>
                        <span>Tips Fermentasi:</span>
                    </p>
                    <p>${escapedTips}</p>
                </div>
            `;
        }

        let photoSection = '';
        if (photoCount > 0) {
            photoSection = `
                <button type="button"
                        onclick='openStepPhotosModal(${JSON.stringify(step)})'
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-semibold transition-all shadow-2xs">
                    <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span>Lihat Foto (${photoCount})</span>
                </button>
                <button type="button"
                        onclick="openQuickPhotoModal(${step.id}, '${escapedNomor.replace(/'/g, "\\'")}', '${escapedJudul.replace(/'/g, "\\'")}')"
                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-teal-700 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah / Jepret Foto</span>
                </button>
            `;
        } else {
            photoSection = `
                <span class="text-[11px] text-gray-400 italic">Belum ada foto dokumentasi</span>
                <button type="button"
                        onclick="openQuickPhotoModal(${step.id}, '${escapedNomor.replace(/'/g, "\\'")}', '${escapedJudul.replace(/'/g, "\\'")}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-dashed border-teal-300 text-teal-800 hover:bg-teal-50 text-xs font-semibold transition-all">
                    <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                    </svg>
                    <span>Tambah / Jepret Foto</span>
                </button>
            `;
        }

        return `
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-black text-sm shrink-0 border border-teal-200 shadow-2xs">
                    ${escapedNomor}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-teal-900 transition-colors">
                                ${escapedJudul}
                            </h2>
                            ${waktuBadge}
                        </div>
                        <div class="flex items-center gap-1.5 self-start shrink-0 flex-wrap">
                            <button type="button"
                                    onclick='openEditStepModal(${JSON.stringify(step)})'
                                    title="Edit tahapan ini"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 text-xs font-semibold transition-all">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button type="button"
                                    onclick="confirmDeleteStep(${step.id}, '${escapedNomor.replace(/'/g, "\\'")}', '${escapedJudul.replace(/'/g, "\\'")}')"
                                    title="Hapus tahapan ini"
                                    class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                        ${escapedDeskripsi}
                    </div>
                    ${tipsBox}
                    <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-center justify-between gap-2 flex-wrap">
                        ${photoSection}
                    </div>
                </div>
            </div>
        `;
    }

    function updateOrInsertStepCard(step) {
        const list = document.getElementById('fermentasi-steps-list');
        if (!list) return;

        const emptyPlaceholder = list.querySelector('.p-12.text-center');
        if (emptyPlaceholder) {
            emptyPlaceholder.remove();
        }

        let card = document.getElementById('step-card-' + step.id);
        if (card) {
            card.innerHTML = renderStepCardHtml(step);
        } else {
            card = document.createElement('div');
            card.className = 'bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-teal-300 transition-all group';
            card.id = 'step-card-' + step.id;
            card.innerHTML = renderStepCardHtml(step);
            list.appendChild(card);
        }
        updateStepsCounter();
    }

    // ── AJAX Submit (Zero Full Page Refresh, Inline Errors) ──
    document.addEventListener('DOMContentLoaded', () => {
        updateStepsCounter();
        const formAddStep = document.getElementById('form-add-step');
        if (formAddStep) {
            formAddStep.addEventListener('submit', async (e) => {
                e.preventDefault();
                AgriFormHelper.clearErrors(formAddStep);

                const submitBtn = formAddStep.querySelector('button[type="submit"]');
                AgriFormHelper.setLoading(submitBtn, true, 'Menyimpan...');

                const fd = new FormData(formAddStep);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const response = await fetch(formAddStep.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: fd
                    });

                    const result = await response.json().catch(() => null);

                    if (response.status === 422 && result && result.errors) {
                        AgriFormHelper.showErrors(formAddStep, result.errors);
                        AgriSwal.toastError('Mohon periksa input yang ditandai merah.');
                    } else if (response.ok && result && result.success !== false) {
                        closeModal('modal-add-step');
                        formAddStep.reset();
                        clearAllPreviewFiles('add-photo-preview');
                        if (result.step) {
                            updateOrInsertStepCard(result.step);
                        }
                        AgriSwal.toastSuccess(result.message || 'Langkah baru berhasil ditambahkan!');
                    } else {
                        AgriSwal.toastError(result?.message || 'Gagal menyimpan langkah.');
                    }
                } catch (err) {
                    console.warn('Network error, fallback:', err);
                    AgriSwal.toastError('Gagal menyimpan. Periksa koneksi Anda.');
                } finally {
                    AgriFormHelper.setLoading(submitBtn, false);
                }
            });
        }

        const formEditStep = document.getElementById('form-edit-step');
        if (formEditStep) {
            formEditStep.addEventListener('submit', async (e) => {
                e.preventDefault();
                AgriFormHelper.clearErrors(formEditStep);

                const submitBtn = formEditStep.querySelector('button[type="submit"]');
                AgriFormHelper.setLoading(submitBtn, true, 'Menyimpan...');

                const fd = new FormData(formEditStep);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const response = await fetch(formEditStep.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: fd
                    });

                    const result = await response.json().catch(() => null);

                    if (response.status === 422 && result && result.errors) {
                        AgriFormHelper.showErrors(formEditStep, result.errors);
                        AgriSwal.toastError('Mohon periksa input yang ditandai merah.');
                    } else if (response.ok && result && result.success !== false) {
                        closeModal('modal-edit-step');
                        if (result.step) {
                            updateOrInsertStepCard(result.step);
                        }
                        AgriSwal.toastSuccess(result.message || 'Langkah berhasil diperbarui!');
                    } else {
                        AgriSwal.toastError(result?.message || 'Gagal memperbarui langkah.');
                    }
                } catch (err) {
                    console.warn('Network error on edit step:', err);
                    AgriSwal.toastError('Gagal memperbarui. Periksa koneksi Anda.');
                } finally {
                    AgriFormHelper.setLoading(submitBtn, false);
                }
            });
        }

        const formQuickPhoto = document.getElementById('form-quick-photo');
        if (formQuickPhoto) {
            formQuickPhoto.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = formQuickPhoto.querySelector('button[type="submit"]');
                AgriFormHelper.setLoading(submitBtn, true, 'Mengunggah...');

                const fd = new FormData(formQuickPhoto);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const response = await fetch(formQuickPhoto.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: fd
                    });

                    const result = await response.json().catch(() => null);

                    if (response.ok && result && result.success !== false) {
                        closeModal('modal-quick-photo');
                        formQuickPhoto.reset();
                        clearAllPreviewFiles('quick-photo-preview');
                        if (result.step) {
                            updateOrInsertStepCard(result.step);
                        }
                        AgriSwal.toastSuccess(result.message || 'Foto berhasil diunggah!');
                    } else {
                        AgriSwal.toastError(result?.message || 'Gagal mengunggah foto.');
                    }
                } catch (err) {
                    console.warn('Network error on quick photo upload:', err);
                    AgriSwal.toastError('Gagal mengunggah. Periksa koneksi Anda.');
                } finally {
                    AgriFormHelper.setLoading(submitBtn, false);
                }
            });
        }
    });
</script>
@endpush

