@extends('layouts.app')

@section('title', 'Data Obat Tanaman')

@section('content')

{{-- ── Page Hero Header ── --}}
<div class="relative mb-8 overflow-hidden rounded-2xl bg-primary-dark p-6 lg:p-8 shadow-lg shadow-primary/20">
    {{-- Decorative blobs --}}
    <div class="pointer-events-none absolute -top-8 -right-8 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-12 -left-6 w-40 h-40 rounded-full bg-black/10 blur-3xl"></div>


    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white/90 text-[11px] font-medium mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-green-300 animate-pulse"></span>
                Manajemen Stok
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-white leading-tight">Data Obat Tanaman</h1>
            <p class="text-sm text-green-100/80 mt-1.5">Kelola pestisida, pupuk, dan obat tanaman dengan mudah.</p>
        </div>

        <a href="/data-obat/tambah"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-primary-dark text-sm font-semibold hover:bg-green-50 active:scale-95 transition-all duration-150 shadow-lg shadow-black/20 self-start shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Obat
        </a>
    </div>
</div>

{{-- ── Flash message ── --}}
@if (session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-primary-light border border-primary/20 text-primary-dark text-sm font-medium animate-slide-up shadow-sm">
        <div class="w-7 h-7 rounded-full bg-primary/15 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-auto text-primary-dark/50 hover:text-primary-dark transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

{{-- ── Filter & Search ── --}}
<div class="bg-surface rounded-2xl border border-gray-100 shadow-sm p-4 lg:p-5 mb-5">
    <form method="GET" action="/data-obat">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1 group">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text"
                       id="search-input"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama obat atau tanaman..."
                       class="field-input w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-page/60 text-sm text-text placeholder:text-text-muted transition-all">
            </div>

            {{-- Filter Jenis --}}
            <div class="relative">
                <select id="jenis-filter"
                        name="jenis"
                        class="field-input appearance-none pl-4 pr-9 py-2.5 rounded-xl border border-gray-200 bg-page/60 text-sm text-text-secondary transition-all min-w-[160px] cursor-pointer">
                    <option value="">Semua Jenis</option>
                    @foreach (['Pestisida', 'Fungisida', 'Pupuk', 'Herbisida'] as $j)
                        <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2">
                <button type="submit" id="filter-btn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary-dark active:scale-95 transition-all duration-150 shadow-sm shadow-primary/25">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                    </svg>
                    Filter
                </button>
                @if (request('search') || request('jenis'))
                    <a href="/data-obat"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-text-muted hover:text-text hover:border-gray-300 hover:bg-gray-50 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- Active filter chips --}}
        @if(request('search') || request('jenis'))
            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                @if(request('search'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-light text-primary-dark text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        "{{ request('search') }}"
                    </span>
                @endif
                @if(request('jenis'))
                    @php
                        $chipColors = ['Pestisida'=>'bg-red-50 text-red-600','Fungisida'=>'bg-purple-50 text-purple-600','Pupuk'=>'bg-primary-light text-primary-dark','Herbisida'=>'bg-amber-50 text-amber-600'];
                        $cc = $chipColors[request('jenis')] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $cc }} text-xs font-medium">
                        {{ request('jenis') }}
                    </span>
                @endif
            </div>
        @endif
    </form>
</div>

{{-- ── Data Table / Cards ── --}}
<div class="bg-surface rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    @if ($medicines->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
            <div class="relative mb-6">
                <div class="w-20 h-20 rounded-3xl bg-primary-light flex items-center justify-center shadow-inner">
                    <svg class="w-10 h-10 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                    </svg>
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-primary-light border-2 border-surface flex items-center justify-center">
                    <svg class="w-3 h-3 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
            </div>
            <p class="text-base font-semibold text-text mb-1">
                @if(request('search') || request('jenis'))
                    Tidak ada hasil ditemukan
                @else
                    Belum ada data obat
                @endif
            </p>
            <p class="text-sm text-text-muted max-w-xs">
                @if(request('search') || request('jenis'))
                    Coba ubah kata kunci atau filter yang digunakan.
                @else
                    Mulai tambahkan data pestisida, pupuk, atau obat tanaman untuk ditampilkan di sini.
                @endif
            </p>
            @if(!request('search') && !request('jenis'))
                <a href="/data-obat/tambah"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-all shadow-sm shadow-primary/25">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Obat Pertama
                </a>
            @endif
        </div>

    @else

        {{-- ── Desktop Table ── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-text-muted uppercase tracking-wider">Nama Obat</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-text-muted uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-text-muted uppercase tracking-wider">Tanaman Sasaran</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-text-muted uppercase tracking-wider">Dosis</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-text-muted uppercase tracking-wider">Stok</th>
                        <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-text-muted uppercase tracking-wider pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicines as $i => $medicine)
                        @php
                            $jenisConfig = [
                                'Pestisida' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'dot' => 'bg-red-400'],
                                'Fungisida' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'dot' => 'bg-purple-400'],
                                'Pupuk'     => ['bg' => 'bg-primary-light', 'text' => 'text-primary-dark', 'dot' => 'bg-primary'],
                                'Herbisida' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-400'],
                            ];
                            $jc = $jenisConfig[$medicine->jenis] ?? ['bg' => 'bg-gray-100', 'text' => 'text-text-secondary', 'dot' => 'bg-gray-400'];
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition-colors group">
                            {{-- Nama --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl {{ $jc['bg'] }} flex items-center justify-center shrink-0">
                                        <span class="{{ $jc['dot'] }} w-2 h-2 rounded-full block"></span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-text leading-snug">{{ $medicine->nama }}</p>
                                        @if ($medicine->catatan_keamanan)
                                            <p class="text-[11px] text-text-muted mt-0.5 line-clamp-1 max-w-[200px]">{{ $medicine->catatan_keamanan }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Jenis --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $jc['bg'] }} {{ $jc['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $jc['dot'] }}"></span>
                                    {{ $medicine->jenis }}
                                </span>
                            </td>

                            {{-- Tanaman sasaran --}}
                            <td class="px-5 py-4">
                                <span class="text-text-secondary">{{ $medicine->tanaman_sasaran }}</span>
                            </td>

                            {{-- Dosis --}}
                            <td class="px-5 py-4">
                                <span class="text-text-secondary font-mono text-[12px]">{{ $medicine->dosis_anjuran ?? '—' }}</span>
                            </td>

                            {{-- Stok --}}
                            <td class="px-5 py-4">
                                @if ($medicine->stok === 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                        Habis
                                    </span>
                                @elseif ($medicine->stok <= 5)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        {{ $medicine->stok }} unit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary-light text-primary-dark">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        {{ $medicine->stok }} unit
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <a href="/data-obat/{{ $medicine->id }}/edit"
                                       title="Edit"
                                       class="p-2 rounded-lg text-text-muted hover:text-primary hover:bg-primary-light/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="/data-obat/{{ $medicine->id }}" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus {{ $medicine->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="p-2 rounded-lg text-text-muted hover:text-red-500 hover:bg-red-50 transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Mobile Cards ── --}}
        <div class="md:hidden divide-y divide-gray-50">
            @foreach ($medicines as $medicine)
                @php
                    $jenisConfig = [
                        'Pestisida' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'dot' => 'bg-red-400'],
                        'Fungisida' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'dot' => 'bg-purple-400'],
                        'Pupuk'     => ['bg' => 'bg-primary-light', 'text' => 'text-primary-dark', 'dot' => 'bg-primary'],
                        'Herbisida' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-400'],
                    ];
                    $jc = $jenisConfig[$medicine->jenis] ?? ['bg' => 'bg-gray-100', 'text' => 'text-text-secondary', 'dot' => 'bg-gray-400'];
                @endphp
                <div class="p-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-xl {{ $jc['bg'] }} flex items-center justify-center shrink-0">
                                <span class="{{ $jc['dot'] }} w-3 h-3 rounded-full block"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-text truncate">{{ $medicine->nama }}</p>
                                <p class="text-xs text-text-secondary mt-0.5">{{ $medicine->tanaman_sasaran }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $jc['bg'] }} {{ $jc['text'] }} shrink-0">
                            {{ $medicine->jenis }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mt-3 ml-13 pl-0.5">
                        <div class="flex-1 flex items-center gap-3 text-xs text-text-muted">
                            @if ($medicine->dosis_anjuran)
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33"/></svg>
                                    {{ $medicine->dosis_anjuran }}
                                </div>
                            @endif
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                @if ($medicine->stok === 0)
                                    <span class="text-red-500 font-semibold">Habis</span>
                                @elseif ($medicine->stok <= 5)
                                    <span class="text-amber-600 font-semibold">{{ $medicine->stok }} unit ⚠</span>
                                @else
                                    <span class="text-primary-dark font-semibold">{{ $medicine->stok }} unit</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-3">
                        <a href="/data-obat/{{ $medicine->id }}/edit"
                           class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-gray-50/50 text-xs font-semibold text-text-secondary hover:bg-primary-light/50 hover:text-primary-dark hover:border-primary/20 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                            </svg>
                            Edit
                        </a>
                        <form method="POST" action="/data-obat/{{ $medicine->id }}" class="flex-1"
                              onsubmit="return confirm('Yakin ingin menghapus {{ $medicine->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-gray-50/50 text-xs font-semibold text-red-500 hover:bg-red-50 hover:border-red-200 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Pagination ── --}}
        @if ($medicines->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $medicines->links() }}
            </div>
        @else
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                <p class="text-xs text-text-muted">
                    Menampilkan <span class="font-semibold text-text">{{ $medicines->count() }}</span> data obat
                </p>
            </div>
        @endif

    @endif
</div>

@endsection
