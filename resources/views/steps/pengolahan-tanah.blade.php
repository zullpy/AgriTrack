@extends('layouts.app')

@section('title', 'Tahapan Pengolahan Tanah')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

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
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Standar operasional penyiapan lahan gembur, subur, bebas patogen, dan memiliki drainase optimal.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/steps/penanaman-bibit"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-semibold hover:bg-emerald-700 transition-all shadow-xs">
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

    {{-- ── Step by Step Details ── --}}
    <div class="space-y-4">

        {{-- Step 1 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    1
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Sanitasi & Pembersihan Lahan</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-30 s/d H-21 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Bersihkan lahan dari sisa-sisa tanaman musim tanam sebelumnya, tunggul kayu, semak, dan gulma berakar keras. Pembersihan ini bertujuan memutus siklus hidup hama (seperti ulat grayak, penggerek batang) dan inang penyakit jamur atau nematoda tanah.
                    </p>
                    <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                        <p class="font-semibold text-amber-950">💡 Tips Praktisi:</p>
                        <p>Gulma yang dibabat sebaiknya dikumpulkan dan dibuat kompos terpisah atau dibakar jika terindikasi terserang virus/bakteri tular tanah.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    2
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Pembalikan & Penggemburan Tanah (Bajak I)</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-21 s/d H-15 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Cangkul atau bajak tanah dengan kedalaman 25–30 cm. Balikkan bongkahan tanah agar lapisan bawah terangkat ke permukaan dan terpapar sinar matahari secara merata selama 5–7 hari (proses solarisasi alami) guna membunuh telur serangga hama dan spora patogen tanah.
                    </p>
                    <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                        <p class="font-semibold text-amber-950">💡 Tips Praktisi:</p>
                        <p>Lakukan pembajakan saat kondisi tanah tidak terlalu basah/becek (macak-macak ringan) agar struktur agregat tanah tidak rusak memadat.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    3
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Uji pH Tanah & Aplikasi Kapur Dolomit</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-15 s/d H-10 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Ukur pH tanah menggunakan pH meter tanah (pH ideal tanaman hortikultura adalah 6.0 – 6.8). Jika pH masam (&lt; 5.5), taburkan kapur pertanian (Dolomit CaMg(CO3)2) sebanyak 1.5 – 2 ton/ha atau 150–200 gram per meter panjang bedengan. Dolomit juga menyuplai unsur mikro Kalsium (Ca) dan Magnesium (Mg).
                    </p>
                    <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                        <p class="font-semibold text-amber-950">💡 Tips Praktisi:</p>
                        <p>Beri jeda minimal 5–7 hari antara pemberian kapur dolomit dengan pupuk nitrogen/kandang agar tidak terjadi pelepasan amonia yang mengurangi efisiensi pupuk.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    4
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Aplikasi Pupuk Dasar Organik & Agens Hayati</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-10 s/d H-7 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Taburkan pupuk kandang matang/kompos fermentasi (1–2 kg per meter bedengan) bersama pupuk dasar kimia lambat urai seperti SP-36 atau NPK seimbang. Inokulasi agens hayati <em>Trichoderma sp.</em> bersama pupuk kandang untuk mencegah serangan penyakit layu jamur fusarium.
                    </p>
                    <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                        <p class="font-semibold text-amber-950">💡 Tips Praktisi:</p>
                        <p>Pastikan pupuk kandang sudah terfermentasi sempurna (dingin, tidak berbau menyengat, warna cokelat kehitaman) agar tidak membakar akar bibit baru.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    5
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Penggemburan Halus (Rotary) & Pembuatan Bedengan</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-7 s/d H-4 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Cacah kembali tanah agar pupuk kandang tercampur rata. Bentuk bedengan membujur dari Timur ke Barat dengan spesifikasi standar:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3 text-center">
                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] text-gray-500 block">Lebar Bedengan</span>
                            <span class="text-xs font-bold text-gray-800">100 – 110 cm</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] text-gray-500 block">Tinggi Bedengan</span>
                            <span class="text-xs font-bold text-gray-800">30 – 40 cm</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] text-gray-500 block">Lebar Parit</span>
                            <span class="text-xs font-bold text-gray-800">50 – 60 cm</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] text-gray-500 block">Kemiringan Parit</span>
                            <span class="text-xs font-bold text-gray-800">1 – 2% (Lancar)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 6 --}}
        <div class="bg-surface rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs hover:border-gray-300 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0 border border-amber-200">
                    6
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Pemasangan Mulsa Plastik Hitam Perak (MPHP) & Pelubangan</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 w-fit">
                            Waktu: H-3 s/d H-1 Sebelum Tanam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 leading-relaxed">
                        Pasang mulsa plastik saat siang hari yang terik agar plastik memuai dan dapat ditarik dengan kencang tanpa mudah kendur. Bagian berwarna perak menghadap ke atas (memantulkan sinar UV untuk mengusir thrips/aphids) dan hitam ke bawah (menahan gulma). Buat lubang tanam menggunakan kaleng panas sesuai jarak tanam varietas (misal 50 x 60 cm zig-zag).
                    </p>
                    <div class="mt-3 bg-amber-50 rounded-xl p-3 border border-amber-100 text-xs text-amber-900 space-y-1">
                        <p class="font-semibold text-amber-950">💡 Tips Praktisi:</p>
                        <p>Setelah mulsa dilubangi, biarkan bedengan terkena udara selama 1–2 hari agar gas beracun sisa fermentasi di bawah mulsa dapat keluar sebelum bibit dimasukkan.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
