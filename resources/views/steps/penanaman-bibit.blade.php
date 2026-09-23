@extends('layouts.app')

@section('title', 'Tahapan Penanaman Bibit')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

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
                    Pembibitan & Tanam (Fase 2)
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">SOP seleksi benih unggul, perlakuan bibit bebas layu, aklimatisasi, dan teknik transplanting presisi.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/steps/pengolahan-tanah"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-200 text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Tahapan Pengolahan Tanah</span>
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
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            1. Pengolahan Tanah
        </a>
        <a href="/steps/penanaman-bibit"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-emerald-800 shadow-xs whitespace-nowrap transition-all">
            2. Penanaman Bibit
        </a>
    </div>

    {{-- ── Step by Step Details ── --}}
    <div class="space-y-4">

        {{-- Step 1 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    1
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Seleksi & Pemilihan Benih Berkualitas</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-25 s/d H-20 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Gunakan benih unggul bersertifikat dari produsen resmi dengan daya berkecambah di atas 85% dan kemurnian varietas minimal 98%. Periksa tanggal kedaluwarsa benih pada kemasan kemurnian untuk memastikan vigor benih masih tinggi.
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Uji Sederhana Kualitas Benih:</p>
                        <p>Lakukan uji apung: rendam benih dalam wadah berisi air bersih. Benih yang tenggelam adalah benih bernas/padat dan ideal disemai, sedangkan benih mengapung sebaiknya diafkir.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    2
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Perlakuan Benih (Seed Treatment)</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-20 Sebelum Tanam (Hari Semai)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Rendam benih dalam air hangat kuku (sekitar 40–45°C) selama 2–4 jam (atau semalam sesuai jenis tanaman seperti cabai/tomat). Tambahkan larutan ZPT (Zat Pengatur Tumbuh) seperti ekstrak bawang merah dan fungisida perlakuan benih (seperti berbahan aktif karbendazim atau mankozeb dosis rendah) untuk mematahkan dormansi dan mencegah rebah semai (damping-off).
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Tips Praktisi:</p>
                        <p>Setelah perendaman, tiriskan benih di atas kain basah atau kertas tisu lembap di tempat gelap selama 24–48 jam hingga keluar titik radikula (calon akar putih kecil) sebelum dipindah ke tray semai.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    3
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Persiapan Media Semai & Penyemaian</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: Hari H Semai
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Campurkan media semai dengan perbandingan 1 bagian tanah halus (diayak), 1 bagian pupuk kandang matang/kompos halus, dan 1 bagian arang sekam atau cocopeat steril. Masukkan ke dalam tray semai (lubang 98 atau 128) atau polybag kecil. Buat lubang sedalam 0.5 cm, masukkan 1 butir benih berkecambah, lalu tutup tipis dengan media semai.
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Tips Praktisi:</p>
                        <p>Pastikan media semai tidak terlalu padat agar perakaran bibit muda dapat tumbuh bebas dan tidak melingkar kerdil.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    4
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Pemeliharaan Bibit di Rumah Pembibitan</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: Umur 1 s/d 18 Hari Semai
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Letakkan tray semai di bawah sungkup plastik transparan atau naungan paranet 50–65%. Siram media semai menggunakan sprayer semprot kabut halus setiap pagi (pukul 07.00 – 08.00). Jaga agar media tetap lembap namun tidak tergenang air untuk menghindari pembusukan pangkal batang.
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Tips Praktisi:</p>
                        <p>Setelah daun sejati pertama keluar, kenalkan bibit dengan sinar matahari pagi secara bertahap selama 2–3 jam setiap hari agar batang tidak mengalami etiolasi (tumbuh kurus tinggi / kutilang).</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    5
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Aklimatisasi (Hardening-off) Sebelum Tanam</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-4 s/d H-1 Sebelum Pindah Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Kurangi penyiraman secara bertahap dan buka naungan paranet agar bibit terpapar sinar matahari penuh selama 3–4 hari sebelum transplanting. Proses ini merangsang pengerasan jaringan dinding sel tanaman (kutikula menebal), sehingga bibit tidak layu atau mengalami syok lingkungan saat dipindah ke lahan bedengan terbuka.
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Indikator Siap Tanam:</p>
                        <p>Bibit memiliki 4–5 helai daun sejati berwarna hijau segar, tinggi tanaman 10–15 cm, batang kokoh kemerahan/kehijauan, dan akar memutih mengikat media dengan baik.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 6 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                    6
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Transplanting (Pindah Tanam ke Bedengan)</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-100 text-emerald-800 w-fit">
                            Hari H Tanam (HST 0)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Lakukan pindah tanam pada sore hari (pukul 15.30 – 17.00) atau pagi hari sejuk saat sinar matahari tidak terik. Siram lubang tanam di bedengan terlebih dahulu. Keluarkan bibit perlahan dari tray semai dengan menekan bagian bawah lubang tray agar bola perakaran tidak pecah. Masukkan bibit hingga sebatas leher akar, rapatkan tanah sekitarnya, lalu siram jenuh segera setelah penanaman.
                    </p>
                    <div class="mt-3 bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <p class="font-semibold text-emerald-950">💡 Tips Pasca-Tanam:</p>
                        <p>Jika cuaca sangat terik, pasang peneduh sementara (seperti pelepah pisang atau daun kelapa) di atas bibit baru selama 2–3 hari pertama pasca-tanam.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
