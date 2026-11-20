@extends('layouts.app')

@section('title', 'Panduan Budidaya ' . $catalog->name)

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6 pb-16">

    {{-- ── Breadcrumb & Top Controls ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <span>/</span>
                <span class="text-emerald-800 font-semibold">{{ $catalog->name }}</span>
            </nav>

            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">
                    Panduan Budidaya {{ $catalog->name }}
                </h1>
            </div>

            @if($catalog->deskripsi)
                <p class="text-xs sm:text-sm text-gray-600 mt-2 max-w-3xl leading-relaxed">
                    {{ $catalog->deskripsi }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <form method="POST" action="{{ route('tanaman-katalog.destroy', $catalog) }}"
                  data-confirm-delete="{{ $catalog->name }}"
                  data-confirm-title="Hapus Tanaman dari Katalog?">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Quick Stage Tabs & Filter Toolbar (Halaman Penuh, Bukan Modal) ── --}}
    <div class="bg-white rounded-2xl border border-gray-200/90 p-3 sm:p-4 shadow-xs space-y-3">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0">
            <button type="button"
                    onclick="selectStageTab('semai')"
                    id="tab-btn-semai"
                    class="stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-emerald-800 hover:bg-emerald-50 border border-transparent cursor-pointer">
                <span>1. Tahap Semai</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ count($catalog->structured_semai) }}</span>
            </button>
            <button type="button"
                    onclick="selectStageTab('penanaman')"
                    id="tab-btn-penanaman"
                    class="stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-amber-800 hover:bg-amber-50 border border-transparent cursor-pointer">
                <span>2. Tahap Penanaman</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ count($catalog->structured_penanaman) }}</span>
            </button>
            <button type="button"
                    onclick="selectStageTab('pemeliharaan')"
                    id="tab-btn-pemeliharaan"
                    class="stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-sky-800 hover:bg-sky-50 border border-transparent cursor-pointer">
                <span>3. Pemeliharaan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-sky-100 text-sky-800 font-extrabold">{{ count($catalog->structured_pemeliharaan) }}</span>
            </button>
            <button type="button"
                    onclick="selectStageTab('tips')"
                    id="tab-btn-tips"
                    class="stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-indigo-800 hover:bg-indigo-50 border border-transparent cursor-pointer">
                <span>4. Tips &amp; Catatan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-extrabold">{{ count($catalog->structured_tips) }}</span>
            </button>
            <button type="button"
                    onclick="selectStageTab('hama')"
                    id="tab-btn-hama"
                    class="stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-rose-800 hover:bg-rose-50 border border-transparent cursor-pointer">
                <span>5. Problem &amp; Solusi Hama</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800 font-extrabold">{{ count($catalog->structured_hama) }}</span>
            </button>
        </div>

        {{-- Filter Input --}}
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text"
                   id="showSearchInput"
                   oninput="filterShowContent(this.value)"
                   placeholder="Cari langkah SOP, tips penting, hama, atau solusi obat {{ $catalog->name }}..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 text-xs sm:text-sm text-gray-900 placeholder:text-gray-400 bg-gray-50/50 focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
        </div>
    </div>

    {{-- ── 5 Pilar Utama Budidaya Khusus Tanaman Ini ── --}}
    <div class="space-y-6">

        {{-- 1. TAHAP SEMAI --}}
        <div id="section-semai" class="stage-section bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="bg-emerald-50/70 border-b border-emerald-100 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Pilar 1</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Tahap Semai &amp; Pembibitan {{ $catalog->name }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white border border-emerald-200 text-emerald-800">
                        Khusus {{ $catalog->name }}
                    </span>
                    <button type="button"
                            onclick="openAddStep('semai')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-2xs cursor-pointer"
                            title="Tambah nomor / langkah semai baru">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Nomor Semai</span>
                    </button>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-4" id="steps-container-semai">
                @if(count($catalog->structured_semai) > 0)
                    @foreach($catalog->structured_semai as $step)
                        <div class="bg-surface rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs hover:border-emerald-300 transition-all group">
                            <div class="flex items-start gap-3.5 sm:gap-4">
                                {{-- Badge Nomor Langkah --}}
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border border-emerald-200 shadow-2xs">
                                    {{ $step['nomor'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-2">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-emerald-900 transition-colors">
                                            {{ $step['judul'] }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 w-fit shrink-0">
                                                {{ $step['badge'] }}
                                            </span>
                                            <button type="button"
                                                    data-step="{{ json_encode($step, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                    onclick="openEditStepFromBtn(this, 'semai', {{ $loop->index }})"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-emerald-800 hover:bg-emerald-50 transition-colors cursor-pointer"
                                                    title="Edit Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDeleteStep('semai', {{ $loop->index }}, '{{ addslashes($step['judul']) }}')"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!empty($step['waktu']))
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                Waktu: {{ $step['waktu'] }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                                        {!! nl2br(e($step['deskripsi'])) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-6 text-center text-gray-400 space-y-2">
                        <p class="text-xs sm:text-sm">Panduan tahap semai untuk {{ $catalog->name }} belum diisi.</p>
                        <button type="button" onclick="openAddStep('semai')" class="text-xs font-bold text-emerald-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>+ Tambah Langkah Semai</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. TAHAP PENANAMAN --}}
        <div id="section-penanaman" class="stage-section bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="bg-amber-50/70 border-b border-amber-100 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pilar 2</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Tahap Penanaman & Jarak Tanam {{ $catalog->name }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-amber-800">
                        Khusus {{ $catalog->name }}
                    </span>
                    <button type="button"
                            onclick="openAddStep('penanaman')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-2xs cursor-pointer"
                            title="Tambah nomor / langkah penanaman baru">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Nomor Penanaman</span>
                    </button>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-4" id="steps-container-penanaman">
                @if(count($catalog->structured_penanaman) > 0)
                    @foreach($catalog->structured_penanaman as $step)
                        <div class="bg-surface rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs hover:border-amber-300 transition-all group">
                            <div class="flex items-start gap-3.5 sm:gap-4">
                                {{-- Badge Nomor Langkah --}}
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border border-amber-200 shadow-2xs">
                                    {{ $step['nomor'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-2">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-amber-900 transition-colors">
                                            {{ $step['judul'] }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/60 w-fit shrink-0">
                                                {{ $step['badge'] }}
                                            </span>
                                            <button type="button"
                                                    data-step="{{ json_encode($step, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                    onclick="openEditStepFromBtn(this, 'penanaman', {{ $loop->index }})"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-amber-800 hover:bg-amber-50 transition-colors cursor-pointer"
                                                    title="Edit Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDeleteStep('penanaman', {{ $loop->index }}, '{{ addslashes($step['judul']) }}')"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!empty($step['waktu']))
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                Waktu: {{ $step['waktu'] }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                                        {!! nl2br(e($step['deskripsi'])) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-6 text-center text-gray-400 space-y-2">
                        <p class="text-xs sm:text-sm">Panduan tahap penanaman untuk {{ $catalog->name }} belum diisi.</p>
                        <button type="button" onclick="openAddStep('penanaman')" class="text-xs font-bold text-amber-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>+ Tambah Langkah Penanaman</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. TAHAP PEMELIHARAAN --}}
        <div id="section-pemeliharaan" class="stage-section bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="bg-sky-50/70 border-b border-sky-100 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-sky-800 uppercase tracking-wider">Pilar 3</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Tahap Pemeliharaan, Pemangkasan & Nutrisi {{ $catalog->name }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white border border-sky-200 text-sky-800">
                        Khusus {{ $catalog->name }}
                    </span>
                    <button type="button"
                            onclick="openAddStep('pemeliharaan')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white transition-colors shadow-2xs cursor-pointer"
                            title="Tambah nomor / langkah pemeliharaan baru">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Nomor Pemeliharaan</span>
                    </button>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-4" id="steps-container-pemeliharaan">
                @if(count($catalog->structured_pemeliharaan) > 0)
                    @foreach($catalog->structured_pemeliharaan as $step)
                        <div class="bg-surface rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs hover:border-sky-300 transition-all group">
                            <div class="flex items-start gap-3.5 sm:gap-4">
                                {{-- Badge Nomor Langkah --}}
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border border-sky-200 shadow-2xs">
                                    {{ $step['nomor'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-2">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-sky-900 transition-colors">
                                            {{ $step['judul'] }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200/60 w-fit shrink-0">
                                                {{ $step['badge'] }}
                                            </span>
                                            <button type="button"
                                                    data-step="{{ json_encode($step, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                    onclick="openEditStepFromBtn(this, 'pemeliharaan', {{ $loop->index }})"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-sky-800 hover:bg-sky-50 transition-colors cursor-pointer"
                                                    title="Edit Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDeleteStep('pemeliharaan', {{ $loop->index }}, '{{ addslashes($step['judul']) }}')"
                                                    class="p-1 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Langkah Ini">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!empty($step['waktu']))
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                                Waktu: {{ $step['waktu'] }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                                        {!! nl2br(e($step['deskripsi'])) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-6 text-center text-gray-400 space-y-2">
                        <p class="text-xs sm:text-sm">Panduan tahap pemeliharaan untuk {{ $catalog->name }} belum diisi.</p>
                        <button type="button" onclick="openAddStep('pemeliharaan')" class="text-xs font-bold text-sky-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>+ Tambah Langkah Pemeliharaan</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- 4. TIPS PENTING --}}
        <div id="section-tips" class="stage-section bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="bg-amber-50/80 border-b border-amber-200/80 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-base shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider">Pilar 4</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Tips Penting & Pantangan {{ $catalog->name }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white border border-amber-300 text-amber-900">
                        Wajib Diperhatikan
                    </span>
                    <button type="button"
                            onclick="openAddTip()"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-2xs cursor-pointer"
                            title="Tambah tips penting baru">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Tips Penting</span>
                    </button>
                </div>
            </div>

            <div class="p-5 sm:p-6" id="tipsCardsContainer">
                @if(!empty(trim($catalog->tips_penting ?? '')) && count($catalog->structured_tips) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($catalog->structured_tips as $tipCard)
                            @php
                                $cleanTip = [
                                    'title' => $tipCard['title'] ?? '',
                                    'subtitle' => $tipCard['subtitle'] ?? '',
                                    'content' => $tipCard['content'] ?? '',
                                ];
                            @endphp
                            <div class="note-card bg-surface rounded-2xl border border-gray-200 p-5 shadow-xs hover:border-amber-400 hover:shadow-md transition-all flex flex-col justify-between group">
                                <div>
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                                                </svg>
                                            </span>
                                            <div class="min-w-0">
                                                <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-amber-900 transition-colors leading-snug">
                                                    {{ $tipCard['title'] }}
                                                </h3>
                                                <span class="text-[10px] text-gray-400 mt-0.5 block">
                                                    {{ $tipCard['subtitle'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                    data-tip="{{ json_encode($cleanTip, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                    onclick="openEditTipFromBtn(this, {{ $loop->index }})"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:text-amber-800 hover:bg-amber-50 transition-colors cursor-pointer"
                                                    title="Edit Tips Ini">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDeleteTip({{ $loop->index }}, '{{ addslashes($tipCard['title']) }}')"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Tips Ini">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-xs sm:text-sm text-gray-700 mt-3.5 leading-relaxed whitespace-pre-line">
                                        {{ $tipCard['content'] }}
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                    <span class="flex items-center gap-1 text-emerald-700 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>SOP Terverifikasi</span>
                                    </span>
                                    <span>Tersimpan di Sistem</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-6 text-center text-gray-400 space-y-2">
                        <p class="text-xs sm:text-sm">Tips penting untuk {{ $catalog->name }} belum diisi.</p>
                        <button type="button" onclick="openAddTip()" class="text-xs font-bold text-amber-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>+ Tambah Tips Penting</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- 5. PROBLEM & SOLUSI HAMA --}}
        <div id="section-hama" class="stage-section bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="bg-rose-50/80 border-b border-rose-200/80 px-5 py-4 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-rose-900 uppercase tracking-wider">Pilar 5</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Problem &amp; Solusi Hama {{ $catalog->name }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button"
                            onclick="openModalAddHama()"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white transition-all shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Problem &amp; Solusi Hama</span>
                    </button>
                </div>
            </div>

            <div class="p-5 sm:p-6" id="hamaCardsContainer">
                @if(count($catalog->structured_hama) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($catalog->structured_hama as $hamaCard)
                            @php
                                $gejalaVal = $hamaCard['gejala'] ?? null;
                                $solusiVal = $hamaCard['solusi'] ?? null;
                                if ((empty($gejalaVal) || empty($solusiVal)) && !empty($hamaCard['content'])) {
                                    $rawCont = $hamaCard['content'];
                                    if (empty($solusiVal) && preg_match('/(?:^|\n)\s*(?:[-•*]\s*)?Solusi(?:\s+(?:Pengendalian|Pencegahan|Penanganan|Atasi))?[:\s]+([\s\S]+)$/iu', $rawCont, $sMatch)) {
                                        $solusiVal = trim($sMatch[1]);
                                    }
                                    if (empty($gejalaVal) && preg_match('/(?:^|\n)\s*(?:[-•*]\s*)?Gejala(?:\s+Serangan)?[:\s]+([\s\S]*?)(?=(?:\n\s*(?:[-•*]\s*)?Solusi)|$)/iu', $rawCont, $gMatch)) {
                                        $gejalaVal = trim($gMatch[1]);
                                    }
                                }
                                $cleanHama = [
                                    'id' => $hamaCard['id'] ?? null,
                                    'judul' => $hamaCard['judul'] ?: ($hamaCard['title'] ?? ''),
                                    'kategori' => $hamaCard['kategori'] ?? 'Hama Utama',
                                    'gejala' => $gejalaVal ?: ($hamaCard['content'] ?? ''),
                                    'solusi' => $solusiVal ?? '',
                                    'foto_urls' => $hamaCard['foto_urls'] ?? [],
                                ];
                            @endphp
                            <div class="note-card bg-surface rounded-2xl border border-gray-200 p-5 shadow-xs hover:border-rose-400 hover:shadow-md transition-all flex flex-col justify-between group">
                                <div>
                                    {{-- Card Header --}}
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200 mt-0.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                                </svg>
                                            </span>
                                            <div class="min-w-0">
                                                <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-rose-900 transition-colors leading-snug">
                                                    {{ $hamaCard['judul'] ?: $hamaCard['title'] }}
                                                </h3>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60 mt-1">
                                                    {{ $hamaCard['kategori'] ?? 'Hama & Penyakit' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Header Actions --}}
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                    onclick="openQuickPhotoHama({{ $hamaCard['id'] ?? 'null' }}, '{{ addslashes($hamaCard['judul'] ?: $hamaCard['title']) }}', '{{ addslashes($cleanHama['gejala']) }}', '{{ addslashes($cleanHama['solusi']) }}', '{{ addslashes($cleanHama['kategori']) }}')"
                                                    class="p-1.5 rounded-lg text-rose-700 hover:text-rose-900 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Upload Foto Gejala / Hama">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                                </svg>
                                            </button>

                                            <button type="button"
                                                    data-hama="{{ json_encode($cleanHama, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                    onclick="openEditHamaFromBtn(this)"
                                                    class="p-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors cursor-pointer"
                                                    title="Edit Problem & Solusi">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>

                                            @if(!empty($hamaCard['id']))
                                                <button type="button"
                                                        onclick="confirmDeleteHama({{ $hamaCard['id'] }}, '{{ addslashes($hamaCard['judul'] ?: $hamaCard['title']) }}')"
                                                        class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                        title="Hapus Problem & Solusi Ini">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Section: Gejala Serangan --}}
                                    <div class="mt-3.5 rounded-xl p-3.5 bg-amber-50/70 border border-amber-200/80 text-amber-950">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 mb-1">
                                            <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                            </svg>
                                            <span>Gejala Serangan:</span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line">{{ $cleanHama['gejala'] ?: 'Belum ada rincian gejala serangan.' }}</p>
                                    </div>

                                    {{-- Section: Solusi & Pengendalian --}}
                                    <div class="mt-2.5 rounded-xl p-3.5 bg-emerald-50/70 border border-emerald-200/80 text-emerald-950">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-900 mb-1">
                                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Solusi &amp; Pengendalian:</span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line">{{ $cleanHama['solusi'] ?: 'Gunakan penanganan sesuai rekomendasi insektisida / fungisida atau agen hayati berizin.' }}</p>
                                    </div>

                                    {{-- Section: Dokumentasi Foto --}}
                                    @if(!empty($hamaCard['foto_urls']) && count($hamaCard['foto_urls']) > 0)
                                        <div class="mt-3.5 pt-3 border-t border-gray-100">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                                    </svg>
                                                    <span>Foto Dokumentasi ({{ count($hamaCard['foto_urls']) }})</span>
                                                </span>
                                                <button type="button"
                                                        onclick="openQuickPhotoHama({{ $hamaCard['id'] ?? 'null' }}, '{{ addslashes($hamaCard['judul'] ?: $hamaCard['title']) }}', '{{ addslashes($hamaCard['gejala'] ?? '') }}', '{{ addslashes($hamaCard['solusi'] ?? '') }}', '{{ addslashes($hamaCard['kategori'] ?? '') }}')"
                                                        class="text-[11px] font-bold text-rose-700 hover:text-rose-800 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                    <span>+ Tambah Foto</span>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                                @foreach($hamaCard['foto_urls'] as $photoUrl)
                                                    <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 group/img cursor-pointer"
                                                         onclick="openLightbox('{{ $photoUrl }}', '{{ addslashes($hamaCard['judul'] ?: $hamaCard['title']) }}')">
                                                        <img src="{{ $photoUrl }}" alt="{{ $hamaCard['judul'] ?: $hamaCard['title'] }}" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-200" loading="lazy">
                                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3.5 pt-3 border-t border-gray-100">
                                            <button type="button"
                                                    onclick="openQuickPhotoHama({{ $hamaCard['id'] ?? 'null' }}, '{{ addslashes($hamaCard['judul'] ?: $hamaCard['title']) }}', '{{ addslashes($hamaCard['gejala'] ?? '') }}', '{{ addslashes($hamaCard['solusi'] ?? '') }}', '{{ addslashes($hamaCard['kategori'] ?? '') }}')"
                                                    class="w-full py-2.5 px-3 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/50 hover:bg-rose-50 text-rose-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                                </svg>
                                                <span>+ Ambil / Upload Foto Gejala / Hama Ini</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- Card Footer --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                    <span class="flex items-center gap-1 text-rose-700 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Solusi Terverifikasi</span>
                                    </span>
                                    @if(!empty($hamaCard['foto_urls']) && count($hamaCard['foto_urls']) > 0)
                                        <span class="font-semibold text-rose-700">{{ count($hamaCard['foto_urls']) }} Foto Tersimpan</span>
                                    @else
                                        <span>Tersimpan di Sistem</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400 space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-600 font-medium">Problem &amp; solusi hama untuk {{ $catalog->name }} belum ditambahkan.</p>
                        <button type="button"
                                onclick="openModalAddHama()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            <span>+ Tambah Problem &amp; Solusi Hama</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Bottom Navigation --}}
    <div class="pt-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold transition-colors">
            &larr; Kembali ke Dashboard
        </a>

        <a href="{{ route('tanaman-katalog.edit', $catalog) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-xs transition-all">
            <span>Ubah Panduan {{ $catalog->name }}</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

</div>

{{-- ── MODAL EDIT / TAMBAH LANGKAH PER NOMOR (SEMAI / PENANAMAN / PEMELIHARAAN) ── --}}
<div id="modalEditStep" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-gray-200 shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        <div id="modalEditStepHeader" class="px-5 py-4 bg-emerald-50/80 border-b border-emerald-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div id="modalEditStepIcon" class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug" id="modalEditStepTitle">Edit Langkah</h3>
                    <p class="text-[11px] text-gray-500" id="modalEditStepSubtitle">Khusus Tanaman {{ $catalog->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeModalEditStep()" class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formEditStep" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="stage" id="stepFormStage" value="semai">
            <input type="hidden" name="step_index" id="stepFormIndex" value="">

            <div class="grid grid-cols-3 gap-3">
                {{-- Nomor Langkah --}}
                <div class="col-span-1">
                    <label for="stepFormNomor" class="block text-xs font-bold text-gray-700 mb-1">
                        Nomor <span class="text-rose-600">*</span>
                    </label>
                    <input type="number"
                           name="nomor"
                           id="stepFormNomor"
                           required
                           min="1"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
                </div>

                {{-- Waktu / Periode --}}
                <div class="col-span-2">
                    <label for="stepFormWaktu" class="block text-xs font-bold text-gray-700 mb-1">
                        Waktu / Periode (Opsional)
                    </label>
                    <input type="text"
                           name="waktu"
                           id="stepFormWaktu"
                           placeholder="Contoh: 1-3 HSS, Pagi Hari"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
                </div>
            </div>

            {{-- Judul Langkah --}}
            <div>
                <label for="stepFormJudul" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Langkah <span class="text-rose-600">*</span>
                </label>
                <input type="text"
                       name="judul"
                       id="stepFormJudul"
                       required
                       placeholder="Contoh: Rendam Benih dengan Air Hangat"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
            </div>

            {{-- Deskripsi / SOP Penanganan --}}
            <div>
                <label for="stepFormDeskripsi" class="block text-xs font-bold text-gray-700 mb-1">
                    Deskripsi / SOP Langkah <span class="text-rose-600">*</span>
                </label>
                <textarea name="deskripsi"
                          id="stepFormDeskripsi"
                          rows="4"
                          required
                          placeholder="Jelaskan instruksi teknis langkah ini secara rinci..."
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Actions --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeModalEditStep()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        id="btnSubmitStep"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Simpan Langkah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT / TAMBAH TIPS PENTING PER KARTU ── --}}
<div id="modalEditTip" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-gray-200 shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 bg-amber-50/80 border-b border-amber-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug" id="modalEditTipTitle">Edit Tips Penting</h3>
                    <p class="text-[11px] text-gray-500">Khusus Tanaman {{ $catalog->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeModalEditTip()" class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formEditTip" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="tip_index" id="tipFormIndex" value="">

            {{-- Judul Tips --}}
            <div>
                <label for="tipFormTitle" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Tips / Pantangan <span class="text-rose-600">*</span>
                </label>
                <input type="text"
                       name="title"
                       id="tipFormTitle"
                       required
                       placeholder="Contoh: Jangan Menyiram di Siang Terik"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs">
            </div>

            {{-- Subtitle / Label Tambahan --}}
            <div>
                <label for="tipFormSubtitle" class="block text-xs font-bold text-gray-700 mb-1">
                    Subtitle / Keterangan Singkat (Opsional)
                </label>
                <input type="text"
                       name="subtitle"
                       id="tipFormSubtitle"
                       placeholder="Contoh: Khusus pencegahan jamur & busuk akar"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs">
            </div>

            {{-- Isi Tips --}}
            <div>
                <label for="tipFormContent" class="block text-xs font-bold text-gray-700 mb-1">
                    Isi Rincian Tips / Pantangan <span class="text-rose-600">*</span>
                </label>
                <textarea name="content"
                          id="tipFormContent"
                          rows="4"
                          required
                          placeholder="Jelaskan tips, pantangan, dan SOP penting..."
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Actions --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeModalEditTip()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        id="btnSubmitTip"
                        class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Simpan Tips
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL TAMBAH PROBLEM & SOLUSI HAMA ── --}}
<div id="modalAddHama" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-gray-200 shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 bg-rose-50/80 border-b border-rose-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug">Tambah Problem &amp; Solusi Hama</h3>
                    <p class="text-[11px] text-gray-500">Khusus Tanaman {{ $catalog->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAddHama()" class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formAddHama" action="{{ route('tanaman-katalog.hama.store', $catalog) }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf

            {{-- Judul Hama / Penyakit --}}
            <div>
                <label for="addHamaJudul" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Problem / Hama <span class="text-rose-600">*</span>
                </label>
                <input type="text"
                       name="judul"
                       id="addHamaJudul"
                       required
                       placeholder="Contoh: Kutu Kebul (Bemisia tabaci) &amp; Thrips"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-2xs">
            </div>

            {{-- Kategori --}}
            <div>
                <label for="addHamaKategori" class="block text-xs font-bold text-gray-700 mb-1">
                    Kategori
                </label>
                <select name="kategori"
                        id="addHamaKategori"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-2xs">
                    <option value="Hama Utama">Hama Utama</option>
                    <option value="Penyakit Utama">Penyakit Utama</option>
                    <option value="Fisiologis &amp; Cuaca">Fisiologis &amp; Cuaca</option>
                    <option value="Gulma &amp; Lainnya">Gulma &amp; Lainnya</option>
                </select>
            </div>

            {{-- Gejala Serangan --}}
            <div>
                <label for="addHamaGejala" class="block text-xs font-bold text-amber-900 mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <span>Gejala Serangan <span class="text-rose-600">*</span></span>
                </label>
                <textarea name="gejala"
                          id="addHamaGejala"
                          rows="3"
                          required
                          placeholder="Deskripsikan tanda/gejala pada daun, batang, atau buah. Contoh: Daun melengkung ke bawah, keriting kaku, muncul jelaga hitam..."
                          class="w-full px-3.5 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs sm:text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Solusi & Pengendalian --}}
            <div>
                <label for="addHamaSolusi" class="block text-xs font-bold text-emerald-900 mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Solusi &amp; Pengendalian <span class="text-rose-600">*</span></span>
                </label>
                <textarea name="solusi"
                          id="addHamaSolusi"
                          rows="3"
                          required
                          placeholder="SOP penanganan teknis / obat / fungisida / insektisida. Contoh: Semprot Abamektin 0.5 ml/L di sore hari diselingi Imidakloprid..."
                          class="w-full px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs sm:text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Upload Foto Dokumentasi --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                    <span>Foto Dokumentasi (Opsional)</span>
                    <span class="text-[10px] text-gray-400 font-normal">Bisa langsung kamera HP &amp; galeri</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {{-- Kamera HP --}}
                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/40 hover:bg-rose-50 text-rose-800 text-xs font-bold cursor-pointer transition-all">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                        <span>Foto dari Kamera HP</span>
                        <input type="file"
                               name="foto_kamera[]"
                               accept="image/*"
                               capture="environment"
                               multiple
                               class="hidden"
                               onchange="handleImagePreview(this, 'addHamaPreviewGrid')">
                    </label>

                    {{-- Galeri File --}}
                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-dashed border-gray-300 hover:border-gray-500 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold cursor-pointer transition-all">
                        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span>Pilih dari Galeri</span>
                        <input type="file"
                               name="foto[]"
                               accept="image/*"
                               multiple
                               class="hidden"
                               onchange="handleImagePreview(this, 'addHamaPreviewGrid')">
                    </label>
                </div>

                {{-- Preview Container --}}
                <div id="addHamaPreviewGrid" class="grid grid-cols-4 gap-2 mt-2.5 hidden"></div>
            </div>

            {{-- Actions --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeModalAddHama()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Simpan Problem &amp; Solusi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT PROBLEM & SOLUSI HAMA ── --}}
<div id="modalEditHama" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-gray-200 shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 bg-rose-50/80 border-b border-rose-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug">Edit Problem &amp; Solusi Hama</h3>
                    <p class="text-[11px] text-gray-500" id="editHamaSubtitle">Khusus Tanaman {{ $catalog->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeEditHama()" class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formEditHama" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            @method('PUT')

            {{-- Judul Hama / Penyakit --}}
            <div>
                <label for="editHamaJudul" class="block text-xs font-bold text-gray-700 mb-1">
                    Judul Problem / Hama <span class="text-rose-600">*</span>
                </label>
                <input type="text"
                       name="judul"
                       id="editHamaJudul"
                       required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-2xs">
            </div>

            {{-- Kategori --}}
            <div>
                <label for="editHamaKategori" class="block text-xs font-bold text-gray-700 mb-1">
                    Kategori
                </label>
                <select name="kategori"
                        id="editHamaKategori"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-2xs">
                    <option value="Hama Utama">Hama Utama</option>
                    <option value="Penyakit Utama">Penyakit Utama</option>
                    <option value="Fisiologis &amp; Cuaca">Fisiologis &amp; Cuaca</option>
                    <option value="Gulma &amp; Lainnya">Gulma &amp; Lainnya</option>
                </select>
            </div>

            {{-- Gejala Serangan --}}
            <div>
                <label for="editHamaGejala" class="block text-xs font-bold text-amber-900 mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <span>Gejala Serangan <span class="text-rose-600">*</span></span>
                </label>
                <textarea name="gejala"
                          id="editHamaGejala"
                          rows="3"
                          required
                          class="w-full px-3.5 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Solusi & Pengendalian --}}
            <div>
                <label for="editHamaSolusi" class="block text-xs font-bold text-emerald-900 mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Solusi &amp; Pengendalian <span class="text-rose-600">*</span></span>
                </label>
                <textarea name="solusi"
                          id="editHamaSolusi"
                          rows="3"
                          required
                          class="w-full px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs leading-relaxed"></textarea>
            </div>

            {{-- Foto Tersimpan Saat Ini --}}
            <div id="editHamaExistingPhotosContainer" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Dokumentasi Tersimpan</label>
                <div id="editHamaExistingPhotosGrid" class="grid grid-cols-4 gap-2"></div>
            </div>

            {{-- Tambah Foto Baru --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                    <span>Tambah Foto Baru (Opsional)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/40 hover:bg-rose-50 text-rose-800 text-xs font-bold cursor-pointer transition-all">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                        <span>Foto dari Kamera HP</span>
                        <input type="file"
                               name="foto_kamera[]"
                               accept="image/*"
                               capture="environment"
                               multiple
                               class="hidden"
                               onchange="handleImagePreview(this, 'editHamaPreviewGrid')">
                    </label>

                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-dashed border-gray-300 hover:border-gray-500 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold cursor-pointer transition-all">
                        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <span>Pilih dari Galeri</span>
                        <input type="file"
                               name="foto[]"
                               accept="image/*"
                               multiple
                               class="hidden"
                               onchange="handleImagePreview(this, 'editHamaPreviewGrid')">
                    </label>
                </div>

                {{-- Preview Container --}}
                <div id="editHamaPreviewGrid" class="grid grid-cols-4 gap-2 mt-2.5 hidden"></div>
            </div>

            {{-- Actions --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeEditHama()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL QUICK PHOTO UPLOAD ── --}}
<div id="modalQuickPhotoHama" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-md w-full border border-gray-200 shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 bg-rose-50/80 border-b border-rose-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug truncate" id="quickPhotoTitle">Upload Foto Dokumentasi</h3>
                    <p class="text-[11px] text-gray-500">Problem &amp; Solusi Hama {{ $catalog->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeQuickPhotoHama()" class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formQuickPhotoHama" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf

            <div class="space-y-2">
                <label class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/50 hover:bg-rose-50 text-rose-800 text-xs font-bold cursor-pointer transition-all">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                    <span>Ambil Foto Langsung dari Kamera HP</span>
                    <input type="file"
                           name="foto_kamera[]"
                           accept="image/*"
                           capture="environment"
                           multiple
                           class="hidden"
                           onchange="handleImagePreview(this, 'quickPhotoPreviewGrid')">
                </label>

                <label class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl border border-dashed border-gray-300 hover:border-gray-500 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold cursor-pointer transition-all">
                    <svg class="w-5 h-5 text-gray-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span>Pilih Foto dari Galeri HP</span>
                    <input type="file"
                           name="foto[]"
                           accept="image/*"
                           multiple
                           class="hidden"
                           onchange="handleImagePreview(this, 'quickPhotoPreviewGrid')">
                </label>
            </div>

            {{-- Preview Container --}}
            <div id="quickPhotoPreviewGrid" class="grid grid-cols-3 gap-2 hidden"></div>

            {{-- Actions --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeQuickPhotoHama()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL LIGHTBOX FOTO ── --}}
<div id="modalLightboxHama" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6" onclick="closeLightbox()">
    <div class="relative max-w-3xl w-full max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
        <button type="button"
                onclick="closeLightbox()"
                class="absolute -top-11 right-0 sm:right-0 w-9 h-9 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition-colors cursor-pointer">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="rounded-2xl overflow-hidden border border-white/20 bg-black/50 shadow-2xl max-h-[80vh] flex items-center justify-center">
            <img id="lightboxImage" src="" alt="Dokumentasi Hama" class="max-h-[80vh] max-w-full object-contain">
        </div>
        <p id="lightboxCaption" class="text-white/90 text-xs sm:text-sm font-semibold mt-3 text-center px-4"></p>
    </div>
</div>

{{-- ── FORM HAPUS HAMA (HIDDEN) ── --}}
<form id="formDeleteHama" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

{{-- ── FORM HAPUS FOTO HAMA (HIDDEN) ── --}}
<form id="formDeletePhotoHama" method="POST" class="hidden">
    @csrf
    @method('DELETE')
    <input type="hidden" name="photo_url" id="deletePhotoUrlInput">
</form>
@endsection

@push('scripts')
<script>
    let currentStage = 'all';
    const catalogId = {{ $catalog->id }};
    const catalogPlantName = @json($catalog->name);
    let currentHamas = @json($catalog->structured_hama);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatHamaItem(hama) {
        return {
            id: hama.id,
            judul: hama.judul || hama.title || '',
            title: hama.judul || hama.title || '',
            kategori: hama.kategori || 'Hama Utama',
            gejala: hama.gejala || '',
            solusi: hama.solusi || '',
            foto_urls: hama.foto_urls || []
        };
    }

    function selectStageTab(tabKey, updateUrl = true) {
        if (tabKey === currentStage && tabKey !== 'all') {
            tabKey = 'all';
        }
        currentStage = tabKey;

        const tabs = ['semai', 'penanaman', 'pemeliharaan', 'tips', 'hama'];
        const sections = {
            'semai': document.getElementById('section-semai'),
            'penanaman': document.getElementById('section-penanaman'),
            'pemeliharaan': document.getElementById('section-pemeliharaan'),
            'tips': document.getElementById('section-tips'),
            'hama': document.getElementById('section-hama')
        };

        const activeClasses = 'stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-emerald-600 text-white shadow-xs cursor-pointer';
        const inactiveClasses = 'stage-tab-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-gray-600 hover:text-emerald-800 hover:bg-emerald-50 border border-transparent cursor-pointer';

        tabs.forEach(t => {
            const btn = document.getElementById('tab-btn-' + t);
            if (!btn) return;
            btn.className = (t === tabKey) ? activeClasses : inactiveClasses;
        });

        Object.keys(sections).forEach(key => {
            const sec = sections[key];
            if (!sec) return;
            if (tabKey === 'all' || tabKey === key) {
                sec.classList.remove('hidden');
            } else {
                sec.classList.add('hidden');
            }
        });

        if (updateUrl) {
            const url = new URL(window.location);
            if (tabKey === 'all') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tabKey);
            }
            window.history.replaceState({}, '', url);
        }
    }

    function filterShowContent(query) {
        const q = (query || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.stage-section .note-card, .stage-section [class*="bg-surface rounded-2xl border"]');

        cards.forEach(card => {
            if (!q) {
                card.classList.remove('hidden');
                return;
            }
            const text = card.textContent.toLowerCase();
            if (text.includes(q)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // ── STEP MODAL HANDLERS (SEMAI / PENANAMAN / PEMELIHARAAN) ──
    const stageMeta = {
        semai: {
            title: 'Tahap Semai',
            color: 'emerald',
            headerBg: 'bg-emerald-50/80',
            headerBorder: 'border-emerald-200/80',
            iconBg: 'bg-emerald-600',
            btnBg: 'bg-emerald-600 hover:bg-emerald-700'
        },
        penanaman: {
            title: 'Tahap Penanaman',
            color: 'amber',
            headerBg: 'bg-amber-50/80',
            headerBorder: 'border-amber-200/80',
            iconBg: 'bg-amber-600',
            btnBg: 'bg-amber-600 hover:bg-amber-700'
        },
        pemeliharaan: {
            title: 'Tahap Pemeliharaan',
            color: 'sky',
            headerBg: 'bg-sky-50/80',
            headerBorder: 'border-sky-200/80',
            iconBg: 'bg-sky-600',
            btnBg: 'bg-sky-600 hover:bg-sky-700'
        }
    };

    function openModalStep(stage, step = null, index = null) {
        const modal = document.getElementById('modalEditStep');
        const header = document.getElementById('modalEditStepHeader');
        const icon = document.getElementById('modalEditStepIcon');
        const titleEl = document.getElementById('modalEditStepTitle');
        const submitBtn = document.getElementById('btnSubmitStep');
        if (!modal) return;

        const meta = stageMeta[stage] || stageMeta.semai;

        header.className = `px-5 py-4 ${meta.headerBg} border-b ${meta.headerBorder} flex items-center justify-between`;
        icon.className = `w-8 h-8 rounded-xl ${meta.iconBg} text-white flex items-center justify-center shrink-0`;
        submitBtn.className = `px-5 py-2 rounded-xl ${meta.btnBg} text-white text-xs font-bold shadow-xs transition-all cursor-pointer`;

        document.getElementById('stepFormStage').value = stage;
        document.getElementById('stepFormIndex').value = (index !== null && index !== undefined) ? index : '';

        if (step && index !== null && index !== undefined) {
            titleEl.textContent = `Edit Langkah ${step.nomor || (index + 1)} (${meta.title})`;
            submitBtn.textContent = 'Simpan Perubahan';
            document.getElementById('stepFormNomor').value = step.nomor || (index + 1);
            document.getElementById('stepFormWaktu').value = step.waktu || '';
            document.getElementById('stepFormJudul').value = step.judul || '';
            document.getElementById('stepFormDeskripsi').value = step.deskripsi || '';
        } else {
            titleEl.textContent = `Tambah Langkah Baru (${meta.title})`;
            submitBtn.textContent = 'Simpan Langkah';
            const existingCount = document.querySelectorAll(`#section-${stage} .group`).length;
            document.getElementById('stepFormNomor').value = existingCount + 1;
            document.getElementById('stepFormWaktu').value = '';
            document.getElementById('stepFormJudul').value = '';
            document.getElementById('stepFormDeskripsi').value = '';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function openAddStep(stage) {
        openModalStep(stage, null, null);
    }

    function openEditStepFromBtn(btn, stage, index) {
        try {
            const raw = btn.getAttribute('data-step');
            const step = JSON.parse(raw);
            openModalStep(stage, step, index);
        } catch (e) {
            console.error('Error opening step modal:', e);
        }
    }

    function closeModalEditStep() {
        const modal = document.getElementById('modalEditStep');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function updateStageTabCount(stage, count) {
        const tabBtn = document.getElementById(`tab-btn-${stage}`);
        if (tabBtn) {
            const badge = tabBtn.querySelector('span:last-child');
            if (badge) badge.textContent = count;
        }
    }

    function renderStepsContainer(stage, steps) {
        updateStageTabCount(stage, steps ? steps.length : 0);
        const container = document.getElementById(`steps-container-${stage}`);
        if (!container) return;

        const meta = stageMeta[stage] || stageMeta.semai;
        const color = meta.color;
        const colorTheme = {
            emerald: {
                badge: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                pill: 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
                hoverText: 'group-hover:text-emerald-900',
                hoverBtn: 'hover:text-emerald-800 hover:bg-emerald-50'
            },
            amber: {
                badge: 'bg-amber-100 text-amber-800 border-amber-200',
                pill: 'bg-amber-50 text-amber-800 border-amber-200/60',
                hoverText: 'group-hover:text-amber-900',
                hoverBtn: 'hover:text-amber-800 hover:bg-amber-50'
            },
            sky: {
                badge: 'bg-sky-100 text-sky-800 border-sky-200',
                pill: 'bg-sky-50 text-sky-800 border-sky-200/60',
                hoverText: 'group-hover:text-sky-900',
                hoverBtn: 'hover:text-sky-800 hover:bg-sky-50'
            }
        }[color] || {
            badge: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            pill: 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
            hoverText: 'group-hover:text-emerald-900',
            hoverBtn: 'hover:text-emerald-800 hover:bg-emerald-50'
        };

        if (!steps || steps.length === 0) {
            container.innerHTML = `
                <div class="py-6 text-center text-gray-400 space-y-2">
                    <p class="text-xs sm:text-sm">Panduan ${escapeHtml(meta.title.toLowerCase())} belum diisi.</p>
                    <button type="button" onclick="openAddStep('${stage}')" class="text-xs font-bold text-${color}-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                        <span>+ Tambah Nomor ${escapeHtml(stage.charAt(0).toUpperCase() + stage.slice(1))}</span>
                    </button>
                </div>
            `;
            return;
        }

        let html = '';
        steps.forEach((step, index) => {
            const stepJson = JSON.stringify(step).replace(/"/g, '&quot;');
            const safeJudul = (step.judul || '').replace(/'/g, "\\'");
            const badgeText = step.badge || ('Langkah ' + (step.nomor || (index + 1)));

            html += `
                <div class="bg-surface rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs hover:border-${color}-300 transition-all group">
                    <div class="flex items-start gap-3.5 sm:gap-4">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl ${colorTheme.badge} flex items-center justify-center font-black text-xs sm:text-sm shrink-0 border shadow-2xs">
                            ${escapeHtml(step.nomor || (index + 1))}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 ${colorTheme.hoverText} transition-colors">
                                    ${escapeHtml(step.judul)}
                                </h3>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold ${colorTheme.pill} border w-fit shrink-0">
                                        ${escapeHtml(badgeText)}
                                    </span>
                                    <button type="button"
                                            data-step="${stepJson}"
                                            onclick="openEditStepFromBtn(this, '${stage}', ${index})"
                                            class="p-1 rounded-lg text-gray-400 ${colorTheme.hoverBtn} transition-colors cursor-pointer"
                                            title="Edit Langkah Ini">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                            onclick="confirmDeleteStep('${stage}', ${index}, '${safeJudul}')"
                                            class="p-1 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Hapus Langkah Ini">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            ${step.waktu ? `
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-gray-100 text-gray-700">
                                        Waktu: ${escapeHtml(step.waktu)}
                                    </span>
                                </div>
                            ` : ''}
                            <div class="text-xs sm:text-sm text-gray-600 mt-2.5 leading-relaxed whitespace-pre-line">
                                ${escapeHtml(step.deskripsi)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function confirmDeleteStep(stage, index, judul) {
        const stageLabel = (stageMeta[stage]?.title || stage);
        const executeDelete = async () => {
            try {
                const res = await fetch(`/tanaman-katalog/${catalogId}/step`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ stage: stage, index: index })
                });

                const data = await res.json();
                if (data.success) {
                    renderStepsContainer(data.stage || stage, data.steps);
                    if (window.AgriSwal?.toastSuccess) {
                        window.AgriSwal.toastSuccess(data.message || 'Langkah berhasil dihapus.');
                    }
                } else {
                    if (window.AgriSwal?.toastError) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus langkah.');
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError('Terjadi kesalahan koneksi.');
                }
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(`Hapus Langkah ${stageLabel}?`, `"${judul}"`, executeDelete);
        } else {
            executeDelete();
        }
    }

    // Submit handler for formEditStep
    document.getElementById('formEditStep')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('btnSubmitStep');
        const origBtnText = btn.innerHTML;
        const stage = document.getElementById('stepFormStage').value;

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...</span>`;

        try {
            const formData = new FormData(form);
            const payload = {
                stage: stage,
                index: formData.get('step_index') !== '' ? parseInt(formData.get('step_index')) : null,
                nomor: formData.get('nomor'),
                waktu: formData.get('waktu'),
                judul: formData.get('judul'),
                deskripsi: formData.get('deskripsi')
            };

            const res = await fetch(`/tanaman-katalog/${catalogId}/step`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success) {
                closeModalEditStep();
                renderStepsContainer(data.stage || stage, data.steps);
                if (window.AgriSwal?.toastSuccess) {
                    window.AgriSwal.toastSuccess(data.message || 'Langkah berhasil disimpan!');
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menyimpan.');
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError(errMsg);
                }
            }
        } catch (err) {
            console.error(err);
            if (window.AgriSwal?.toastError) {
                window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = origBtnText;
        }
    });

    // ── TIPS PENTING MODAL HANDLERS ──
    function renderTipsContainer(tips) {
        updateStageTabCount('tips', tips ? tips.length : 0);
        const container = document.getElementById('tipsCardsContainer');
        if (!container) return;

        if (!tips || tips.length === 0) {
            container.innerHTML = `
                <div class="py-6 text-center text-gray-400 space-y-2">
                    <p class="text-xs sm:text-sm">Tips penting untuk ${escapeHtml(catalogPlantName)} belum diisi.</p>
                    <button type="button" onclick="openAddTip()" class="text-xs font-bold text-amber-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                        <span>+ Tambah Tips Penting</span>
                    </button>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        tips.forEach((tipCard, index) => {
            const cleanTip = {
                title: tipCard.title || tipCard.judul || '',
                subtitle: tipCard.subtitle || '',
                content: tipCard.content || ''
            };
            const tipJson = JSON.stringify(cleanTip).replace(/"/g, '&quot;');
            const safeTitle = (cleanTip.title).replace(/'/g, "\\'");

            html += `
                <div class="note-card bg-surface rounded-2xl border border-gray-200 p-5 shadow-xs hover:border-amber-400 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.439a1.5 1.5 0 01-3 0m4.5-9.75a4.5 4.5 0 10-9 0c0 1.632.88 3.06 2.193 3.844.204.122.307.367.307.606V16.5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-1.55c0-.239.103-.484.307-.606A4.5 4.5 0 0016.5 9z"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-amber-900 transition-colors leading-snug">
                                        ${escapeHtml(cleanTip.title)}
                                    </h3>
                                    <span class="text-[10px] text-gray-400 mt-0.5 block">
                                        ${escapeHtml(cleanTip.subtitle)}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button"
                                        data-tip="${tipJson}"
                                        onclick="openEditTipFromBtn(this, ${index})"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-amber-800 hover:bg-amber-50 transition-colors cursor-pointer"
                                        title="Edit Tips Ini">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="confirmDeleteTip(${index}, '${safeTitle}')"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Hapus Tips Ini">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-xs sm:text-sm text-gray-700 mt-3.5 leading-relaxed whitespace-pre-line">
                            ${escapeHtml(cleanTip.content)}
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                        <span class="flex items-center gap-1 text-emerald-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>SOP Terverifikasi</span>
                        </span>
                        <span>Tersimpan di Sistem</span>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        container.innerHTML = html;
    }

    function openModalTip(tip = null, index = null) {
        const modal = document.getElementById('modalEditTip');
        const titleEl = document.getElementById('modalEditTipTitle');
        const submitBtn = document.getElementById('btnSubmitTip');
        if (!modal) return;

        document.getElementById('tipFormIndex').value = (index !== null && index !== undefined) ? index : '';

        if (tip && index !== null && index !== undefined) {
            titleEl.textContent = 'Edit Tips Penting';
            submitBtn.textContent = 'Simpan Perubahan';
            document.getElementById('tipFormTitle').value = tip.title || tip.judul || '';
            document.getElementById('tipFormSubtitle').value = tip.subtitle || '';
            document.getElementById('tipFormContent').value = tip.content || '';
        } else {
            titleEl.textContent = 'Tambah Tips Penting Baru';
            submitBtn.textContent = 'Simpan Tips';
            document.getElementById('tipFormTitle').value = '';
            document.getElementById('tipFormSubtitle').value = '';
            document.getElementById('tipFormContent').value = '';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function openAddTip() {
        openModalTip(null, null);
    }

    function openEditTipFromBtn(btn, index) {
        try {
            const raw = btn.getAttribute('data-tip');
            const tip = JSON.parse(raw);
            openModalTip(tip, index);
        } catch (e) {
            console.error('Error opening tip modal:', e);
        }
    }

    function closeModalEditTip() {
        const modal = document.getElementById('modalEditTip');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmDeleteTip(index, title) {
        const executeDelete = async () => {
            try {
                const res = await fetch(`/tanaman-katalog/${catalogId}/tip`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ index: index })
                });

                const data = await res.json();
                if (data.success) {
                    renderTipsContainer(data.tips);
                    if (window.AgriSwal?.toastSuccess) {
                        window.AgriSwal.toastSuccess(data.message || 'Tips berhasil dihapus.');
                    }
                } else {
                    if (window.AgriSwal?.toastError) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus tips.');
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError('Terjadi kesalahan koneksi.');
                }
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete('Hapus Tips Penting?', `"${title}"`, executeDelete);
        } else {
            executeDelete();
        }
    }

    // Submit handler for formEditTip
    document.getElementById('formEditTip')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('btnSubmitTip');
        const origBtnText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = `<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...</span>`;

        try {
            const formData = new FormData(form);
            const payload = {
                index: formData.get('tip_index') !== '' ? parseInt(formData.get('tip_index')) : null,
                title: formData.get('title'),
                subtitle: formData.get('subtitle'),
                content: formData.get('content')
            };

            const res = await fetch(`/tanaman-katalog/${catalogId}/tip`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success) {
                closeModalEditTip();
                renderTipsContainer(data.tips);
                if (window.AgriSwal?.toastSuccess) {
                    window.AgriSwal.toastSuccess(data.message || 'Tips berhasil disimpan!');
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menyimpan.');
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError(errMsg);
                }
            }
        } catch (err) {
            console.error(err);
            if (window.AgriSwal?.toastError) {
                window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = origBtnText;
        }
    });

    // ── HAMA & PENYAKIT HANDLERS ──
    function extractGejalaSolusiFallback(obj) {
        let gejalaText = obj.gejala || '';
        let solusiText = obj.solusi || '';
        if (!solusiText && (obj.content || gejalaText)) {
            const raw = (obj.content || gejalaText);
            const solMatch = raw.match(/(?:^|\n)\s*(?:[-•*]\s*)?Solusi(?:\s+(?:Pengendalian|Pencegahan|Penanganan|Atasi))?[:\s]+([\s\S]+)$/i);
            if (solMatch) {
                solusiText = solMatch[1].trim();
                const gejMatch = raw.match(/(?:^|\n)\s*(?:[-•*]\s*)?Gejala(?:\s+Serangan)?[:\s]+([\s\S]*?)(?=(?:\n\s*(?:[-•*]\s*)?Solusi)|$)/i);
                if (gejMatch) {
                    gejalaText = gejMatch[1].trim();
                } else {
                    gejalaText = raw.substring(0, solMatch.index).replace(/^(?:[-•*]\s*)?Gejala(?:\s+Serangan)?[:\s]*/i, '').trim();
                }
            }
        }
        if (!gejalaText && obj.content) {
            gejalaText = obj.content;
        }
        return { gejala: gejalaText, solusi: solusiText };
    }

    function renderHamaContainer(hamas) {
        updateStageTabCount('hama', hamas ? hamas.length : 0);
        const container = document.getElementById('hamaCardsContainer');
        if (!container) return;

        if (!hamas || hamas.length === 0) {
            container.innerHTML = `
                <div class="py-8 text-center text-gray-400 space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">Problem &amp; solusi hama untuk ${escapeHtml(catalogPlantName)} belum ditambahkan.</p>
                    <button type="button"
                            onclick="openModalAddHama()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>+ Tambah Problem &amp; Solusi Hama</span>
                    </button>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        hamas.forEach((hamaCard) => {
            const extracted = extractGejalaSolusiFallback(hamaCard);
            const judul = hamaCard.judul || hamaCard.title || '';
            const safeJudul = judul.replace(/'/g, "\\'");
            const kategori = hamaCard.kategori || 'Hama & Penyakit';
            const cleanHama = {
                id: hamaCard.id || null,
                judul: judul,
                kategori: kategori,
                gejala: extracted.gejala,
                solusi: extracted.solusi,
                foto_urls: hamaCard.foto_urls || []
            };
            const hamaJson = JSON.stringify(cleanHama).replace(/"/g, '&quot;');
            const photoCount = (cleanHama.foto_urls || []).length;
            const safeGejala = (cleanHama.gejala || '').replace(/'/g, "\\'").replace(/\n/g, '\\n');
            const safeSolusi = (cleanHama.solusi || '').replace(/'/g, "\\'").replace(/\n/g, '\\n');
            const safeKategori = (cleanHama.kategori || '').replace(/'/g, "\\'");

            html += `
                <div class="note-card bg-surface rounded-2xl border border-gray-200 p-5 shadow-xs hover:border-rose-400 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-rose-900 transition-colors leading-snug">
                                        ${escapeHtml(judul)}
                                    </h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60 mt-1">
                                        ${escapeHtml(kategori)}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button"
                                        onclick="openQuickPhotoHama(${cleanHama.id || 'null'}, '${safeJudul}', '${safeGejala}', '${safeSolusi}', '${safeKategori}')"
                                        class="p-1.5 rounded-lg text-rose-700 hover:text-rose-900 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Upload Foto Gejala / Hama">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                    </svg>
                                </button>

                                <button type="button"
                                        data-hama="${hamaJson}"
                                        onclick="openEditHamaFromBtn(this)"
                                        class="p-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors cursor-pointer"
                                        title="Edit Problem & Solusi">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </button>

                                ${cleanHama.id ? `
                                    <button type="button"
                                            onclick="confirmDeleteHama(${cleanHama.id}, '${safeJudul}')"
                                            class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Hapus Problem & Solusi Ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                ` : ''}
                            </div>
                        </div>

                        <div class="mt-3.5 rounded-xl p-3.5 bg-amber-50/70 border border-amber-200/80 text-amber-950">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 mb-1">
                                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                                <span>Gejala Serangan:</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line">${escapeHtml(cleanHama.gejala || 'Belum ada rincian gejala serangan.')}</p>
                        </div>

                        <div class="mt-2.5 rounded-xl p-3.5 bg-emerald-50/70 border border-emerald-200/80 text-emerald-950">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-900 mb-1">
                                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Solusi &amp; Pengendalian:</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line">${escapeHtml(cleanHama.solusi || 'Gunakan penanganan sesuai rekomendasi insektisida / fungisida atau agen hayati berizin.')}</p>
                        </div>

                        ${photoCount > 0 ? `
                            <div class="mt-3.5 pt-3 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                        </svg>
                                        <span>Foto Dokumentasi (${photoCount})</span>
                                    </span>
                                    <button type="button"
                                            onclick="openQuickPhotoHama(${cleanHama.id || 'null'}, '${safeJudul}', '${safeGejala}', '${safeSolusi}', '${safeKategori}')"
                                            class="text-[11px] font-bold text-rose-700 hover:text-rose-800 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Foto</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    ${cleanHama.foto_urls.map(url => `
                                        <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 group/img cursor-pointer"
                                             onclick="openLightbox('${url}', '${safeJudul}')">
                                            <img src="${url}" alt="${escapeHtml(judul)}" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-200" loading="lazy">
                                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                                </svg>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : `
                            <div class="mt-3.5 pt-3 border-t border-gray-100">
                                <button type="button"
                                        onclick="openQuickPhotoHama(${cleanHama.id || 'null'}, '${safeJudul}', '${safeGejala}', '${safeSolusi}', '${safeKategori}')"
                                        class="w-full py-2.5 px-3 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/50 hover:bg-rose-50 text-rose-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                    </svg>
                                    <span>+ Ambil / Upload Foto Gejala / Hama Ini</span>
                                </button>
                            </div>
                        `}
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                        <span class="flex items-center gap-1 text-rose-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Solusi Terverifikasi</span>
                        </span>
                        ${photoCount > 0 ? `<span class="font-semibold text-rose-700">${photoCount} Foto Tersimpan</span>` : `<span>Tersimpan di Sistem</span>`}
                    </div>
                </div>
            `;
        });
        html += '</div>';

        container.innerHTML = html;
    }

    function openEditHamaFromBtn(btn) {
        try {
            const raw = btn.getAttribute('data-hama');
            const hama = JSON.parse(raw);
            openEditHama(hama);
        } catch (e) {
            console.error('Error opening hama modal from button:', e);
        }
    }

    // Modal Add Hama
    function openModalAddHama(prefill = {}) {
        const modal = document.getElementById('modalAddHama');
        if (!modal) return;

        const extracted = extractGejalaSolusiFallback(prefill);

        if (prefill.judul || prefill.title) document.getElementById('addHamaJudul').value = prefill.judul || prefill.title || '';
        if (prefill.kategori) document.getElementById('addHamaKategori').value = prefill.kategori;
        document.getElementById('addHamaGejala').value = extracted.gejala;
        document.getElementById('addHamaSolusi').value = extracted.solusi;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModalAddHama() {
        const modal = document.getElementById('modalAddHama');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Modal Edit Hama
    function openEditHama(hama) {
        if (!hama.id) {
            openModalAddHama(hama);
            return;
        }

        const modal = document.getElementById('modalEditHama');
        const form = document.getElementById('formEditHama');
        if (!modal || !form) return;

        const extracted = extractGejalaSolusiFallback(hama);

        form.action = `/tanaman-katalog/hama/${hama.id}`;
        document.getElementById('editHamaJudul').value = hama.judul || hama.title || '';
        document.getElementById('editHamaKategori').value = hama.kategori || 'Hama Utama';
        document.getElementById('editHamaGejala').value = extracted.gejala;
        document.getElementById('editHamaSolusi').value = extracted.solusi;

        // Render existing photos with delete button
        const existingPhotosContainer = document.getElementById('editHamaExistingPhotosContainer');
        const existingPhotosGrid = document.getElementById('editHamaExistingPhotosGrid');

        if (hama.foto_urls && hama.foto_urls.length > 0) {
            existingPhotosGrid.innerHTML = '';
            hama.foto_urls.forEach(url => {
                const item = document.createElement('div');
                item.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 group';
                item.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover">
                    <button type="button"
                            onclick="confirmDeletePhotoHama(${hama.id}, '${url}')"
                            class="absolute top-1 right-1 w-6 h-6 rounded-lg bg-rose-600/90 hover:bg-rose-700 text-white flex items-center justify-center transition-colors cursor-pointer shadow-xs"
                            title="Hapus foto ini">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                existingPhotosGrid.appendChild(item);
            });
            existingPhotosContainer.classList.remove('hidden');
        } else {
            existingPhotosContainer.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditHama() {
        const modal = document.getElementById('modalEditHama');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Modal Quick Photo
    function openQuickPhotoHama(hamaId, judul, gejala = '', solusi = '', kategori = '') {
        if (!hamaId) {
            openModalAddHama({
                judul: judul,
                gejala: gejala,
                solusi: solusi,
                kategori: kategori
            });
            return;
        }

        const modal = document.getElementById('modalQuickPhotoHama');
        const form = document.getElementById('formQuickPhotoHama');
        const titleEl = document.getElementById('quickPhotoTitle');
        if (!modal || !form) return;

        form.action = `/tanaman-katalog/hama/${hamaId}/foto`;
        if (titleEl) titleEl.innerText = `Foto: ${judul}`;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeQuickPhotoHama() {
        const modal = document.getElementById('modalQuickPhotoHama');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Lightbox
    function openLightbox(url, caption) {
        const modal = document.getElementById('modalLightboxHama');
        const img = document.getElementById('lightboxImage');
        const cap = document.getElementById('lightboxCaption');
        if (!modal || !img) return;

        img.src = url;
        if (cap) cap.innerText = caption || '';
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeLightbox() {
        const modal = document.getElementById('modalLightboxHama');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Confirm Delete Hama
    function confirmDeleteHama(id, judul) {
        const executeDelete = async () => {
            try {
                const res = await fetch(`/tanaman-katalog/hama/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    currentHamas = currentHamas.filter(h => h.id != id);
                    renderHamaContainer(currentHamas);
                    if (window.AgriSwal?.toastSuccess) {
                        window.AgriSwal.toastSuccess(data.message || 'Problem & solusi hama berhasil dihapus.');
                    }
                } else {
                    if (window.AgriSwal?.toastError) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus problem & solusi hama.');
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
                }
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete('Hapus Problem & Solusi?', `"${judul}"`, executeDelete);
        } else {
            executeDelete();
        }
    }

    // Confirm Delete Photo
    function confirmDeletePhotoHama(hamaId, photoUrl) {
        const executeDelete = async () => {
            try {
                const res = await fetch(`/tanaman-katalog/hama/${hamaId}/foto`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ photo_url: photoUrl })
                });

                const data = await res.json();
                if (data.success) {
                    if (data.hama) {
                        const formatted = formatHamaItem(data.hama);
                        const idx = currentHamas.findIndex(h => h.id == formatted.id);
                        if (idx !== -1) currentHamas[idx] = formatted;
                        renderHamaContainer(currentHamas);
                        openEditHama(formatted);
                    }
                    if (window.AgriSwal?.toastSuccess) {
                        window.AgriSwal.toastSuccess(data.message || 'Foto dokumentasi hama berhasil dihapus.');
                    }
                } else {
                    if (window.AgriSwal?.toastError) {
                        window.AgriSwal.toastError(data.message || 'Gagal menghapus foto.');
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError('Terjadi kesalahan koneksi.');
                }
            }
        };

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete('Hapus Foto Dokumentasi?', 'Foto ini akan dihapus permanen.', executeDelete);
        } else {
            executeDelete();
        }
    }

    // Form Add Hama AJAX submit
    document.getElementById('formAddHama')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const origBtnText = btn ? btn.innerHTML : 'Simpan';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...</span>`;
        }

        try {
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                closeModalAddHama();
                form.reset();
                const preview = document.getElementById('addHamaImagePreview');
                if (preview) { preview.innerHTML = ''; preview.classList.add('hidden'); }

                if (data.hama) {
                    const formatted = formatHamaItem(data.hama);
                    currentHamas.push(formatted);
                    renderHamaContainer(currentHamas);
                }

                if (window.AgriSwal?.toastSuccess) {
                    window.AgriSwal.toastSuccess(data.message || 'Problem & solusi berhasil disimpan!');
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menyimpan.');
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError(errMsg);
                }
            }
        } catch (err) {
            console.error(err);
            if (window.AgriSwal?.toastError) {
                window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origBtnText;
            }
        }
    });

    // Form Edit Hama AJAX submit
    document.getElementById('formEditHama')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const origBtnText = btn ? btn.innerHTML : 'Simpan';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...</span>`;
        }

        try {
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                closeEditHama();
                form.reset();
                const preview = document.getElementById('editHamaImagePreview');
                if (preview) { preview.innerHTML = ''; preview.classList.add('hidden'); }

                if (data.hama) {
                    const formatted = formatHamaItem(data.hama);
                    const idx = currentHamas.findIndex(h => h.id == formatted.id);
                    if (idx !== -1) currentHamas[idx] = formatted;
                    else currentHamas.push(formatted);
                    renderHamaContainer(currentHamas);
                }

                if (window.AgriSwal?.toastSuccess) {
                    window.AgriSwal.toastSuccess(data.message || 'Problem & solusi berhasil diperbarui!');
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal memperbarui.');
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError(errMsg);
                }
            }
        } catch (err) {
            console.error(err);
            if (window.AgriSwal?.toastError) {
                window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origBtnText;
            }
        }
    });

    // Form Quick Photo Hama AJAX submit
    document.getElementById('formQuickPhotoHama')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const origBtnText = btn ? btn.innerHTML : 'Upload';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mengupload...</span>`;
        }

        try {
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                closeQuickPhotoHama();
                form.reset();
                const preview = document.getElementById('quickPhotoImagePreview');
                if (preview) { preview.innerHTML = ''; preview.classList.add('hidden'); }

                if (data.hama) {
                    const formatted = formatHamaItem(data.hama);
                    const idx = currentHamas.findIndex(h => h.id == formatted.id);
                    if (idx !== -1) currentHamas[idx] = formatted;
                    else currentHamas.push(formatted);
                    renderHamaContainer(currentHamas);
                }

                if (window.AgriSwal?.toastSuccess) {
                    window.AgriSwal.toastSuccess(data.message || 'Foto berhasil diunggah!');
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal mengupload foto.');
                if (window.AgriSwal?.toastError) {
                    window.AgriSwal.toastError(errMsg);
                }
            }
        } catch (err) {
            console.error(err);
            if (window.AgriSwal?.toastError) {
                window.AgriSwal.toastError('Terjadi kesalahan jaringan.');
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origBtnText;
            }
        }
    });

    // Helper: Preview images before upload
    function handleImagePreview(input, containerId) {
        const container = document.getElementById(containerId);
        if (!container || !input.files || input.files.length === 0) return;

        container.innerHTML = '';
        container.classList.remove('hidden');

        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumb = document.createElement('div');
                thumb.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100';
                thumb.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1 right-1 text-[9px] bg-black/70 text-white font-bold px-1.5 py-0.5 rounded">
                        ${index + 1}
                    </span>
                `;
                container.appendChild(thumb);
            };
            reader.readAsDataURL(file);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const tabParam = params.get('tab') || window.location.hash.replace('#section-', '').replace('#section_', '').replace('#', '');
        if (['semai', 'penanaman', 'pemeliharaan', 'tips', 'hama'].includes(tabParam)) {
            selectStageTab(tabParam, false);
            const toolbar = document.getElementById('tab-btn-' + tabParam);
            if (toolbar) {
                toolbar.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    });
</script>
@endpush

