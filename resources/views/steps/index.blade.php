@extends('layouts.app')

@section('title', 'Tahapan Budidaya Pertanian')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

    {{-- ── Header Section (Solid, Clean, No Gradient) ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Tahapan Budidaya</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    SOP & Alur Tanam
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Panduan langkah demi langkah dari fermentasi pupuk, persiapan lahan, hingga tanaman siap berproduksi.</p>
        </div>
    </div>

    {{-- ── Quick Navigation Tabs (Solid Colors) ── --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit max-w-full overflow-x-auto no-scrollbar">
        <a href="/steps/fermentasi-pupuk"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
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

    {{-- ── Main Cards (Solid Colors, Clean) ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6">

        {{-- Phase 1: Fermentasi Pupuk --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-7 flex flex-col justify-between hover:border-teal-400 hover:shadow-md transition-all group">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        Fase 1: Pupuk Organik
                    </span>
                    <span class="text-xs font-semibold text-gray-400">6 Langkah SOP</span>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform border border-teal-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>

                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 group-hover:text-teal-800 transition-colors">
                    Tahapan Fermentasi Pupuk
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                    Kunci penyedia hara alami dan pencegah penyakit tular tanah. Meliputi pemilihan bahan kohe, larutan aktivator EM4/M21, uji kepal kadar air 40-50%, penumpukan termofilik, pembalikan berkala, hingga inokulasi Trichoderma.
                </p>

                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Sanitasi Bahan Kohe & Arang Sekam</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Aktivasi EM4/M21 & Uji Kepal Air 40-50%</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Inkubasi Panas & Inokulasi Trichoderma</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">Estimasi: 3 – 4 Minggu</span>
                <a href="/steps/fermentasi-pupuk"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 text-white text-xs sm:text-sm font-bold hover:bg-teal-700 active:scale-95 transition-all shadow-xs">
                    <span>Buka Panduan</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Phase 2: Pengolahan Tanah --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-7 flex flex-col justify-between hover:border-amber-400 hover:shadow-md transition-all group">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Fase 2: Olah Lahan
                    </span>
                    <span class="text-xs font-semibold text-gray-400">6 Langkah SOP</span>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform border border-amber-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                        <path d="M3 12h18" />
                    </svg>
                </div>

                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 group-hover:text-amber-800 transition-colors">
                    Tahapan Pengolahan Tanah
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                    Fondasi utama keberhasilan budidaya. Meliputi pembersihan lahan, pembalikan tanah, pengaturan pH dengan dolomit, pemupukan dasar organik, pembuatan bedengan gembur, hingga pemasangan mulsa.
                </p>

                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Sanitasi & Pembajakan Tanah 25-30 cm</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Kapur Dolomit & Pupuk Dasar Matang</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Drainase Parit & Pemasangan Mulsa MPHP</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">Estimasi: 2 – 4 Minggu</span>
                <a href="/steps/pengolahan-tanah"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 text-white text-xs sm:text-sm font-bold hover:bg-amber-700 active:scale-95 transition-all shadow-xs">
                    <span>Buka Panduan</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Phase 3: Penanaman Bibit --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-7 flex flex-col justify-between hover:border-emerald-400 hover:shadow-md transition-all group">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Fase 3: Persemaian & Tanam
                    </span>
                    <span class="text-xs font-semibold text-gray-400">6 Langkah SOP</span>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform border border-emerald-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z" />
                    </svg>
                </div>

                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 group-hover:text-emerald-800 transition-colors">
                    Tahapan Penanaman Bibit
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                    Panduan menghasilkan bibit sehat dan tahan penyakit. Meliputi seleksi benih unggul, perlakuan benih (seed treatment), media persemaian steril, aklimatisasi, hingga transplanting tepat waktu.
                </p>

                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Seleksi Benih & Perendaman ZPT Alami</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Media Semai Tray & Penyiraman Kabut Halus</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Aklimatisasi & Pindah Tanam Sore Hari</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">Estimasi: 15 – 30 Hari</span>
                <a href="/steps/penanaman-bibit"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-bold hover:bg-emerald-700 active:scale-95 transition-all shadow-xs">
                    <span>Buka Panduan</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Phase 4: Informasi Penting Pertanian --}}
        <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-7 flex flex-col justify-between hover:border-emerald-400 hover:shadow-md transition-all group">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                        <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                        Informasi Pertanian
                    </span>
                    <span class="text-xs font-semibold text-gray-400">Aturan Lapangan</span>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-gray-50 text-gray-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform border border-gray-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.53-4h7.5m-7.5-4h3.75m-6.75-6.75H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>

                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 group-hover:text-emerald-800 transition-colors">
                    Informasi Penting Pertanian
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                    Kumpulan panduan kehati-hatian, waktu semprot, sanitasi tangki, dan instruksi penting lapangan yang dapat Anda tambah, edit, atau sesuaikan sewaktu-waktu.
                </p>

                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Waktu Aplikasi Semprot & Ketahanan Cuaca</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Standar Kualitas Air Pelarut Obat & Tangki</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Pencegahan Kontaminasi & Keracunan Herbisida</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">Kelola Bebas</span>
                <a href="/steps/catatan"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-800 text-white text-xs sm:text-sm font-bold hover:bg-gray-900 active:scale-95 transition-all shadow-xs">
                    <span>Buka Informasi</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
