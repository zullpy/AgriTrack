@extends('layouts.app')

@section('title', 'Tambah Tanaman ke Katalog')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    {{-- Breadcrumb & Title --}}
    <div class="pb-3 border-b border-gray-200">
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Tambah Tanaman</span>
        </nav>

        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">
            Tambah Panduan Tanaman {{ $catalog->name ? ($catalog->emoji . ' ' . $catalog->name) : 'Baru' }} ke Katalog
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Lengkapi data tanaman beserta panduan spesifik: Tahap Semai, Penanaman, Pemeliharaan, dan Tips Penting.
        </p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
            <p class="font-semibold text-xs sm:text-sm">Harap perbaiki kesalahan berikut:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('plant-catalog._form', [
        'action'  => route('tanaman-katalog.store'),
        'method'  => 'POST',
        'catalog' => $catalog,
    ])

</div>
@endsection
