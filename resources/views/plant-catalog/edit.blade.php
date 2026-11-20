@extends('layouts.app')

@section('title', 'Edit Tanaman — ' . $catalog->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    {{-- Breadcrumb & Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <span>/</span>
                <a href="{{ route('tanaman-katalog.show', $catalog) }}" class="hover:text-emerald-700 transition-colors font-medium">
                    {{ $catalog->name }}
                </a>
                <span>/</span>
                <span class="text-emerald-800 font-semibold">Edit</span>
            </nav>

            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                <span>{{ $catalog->emoji }}</span>
                <span>Edit Panduan {{ $catalog->name }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Perbarui informasi dan panduan budidaya khusus untuk tanaman ini.</p>
        </div>

        <form method="POST" action="{{ route('tanaman-katalog.destroy', $catalog) }}"
              data-confirm-delete="{{ $catalog->name }}"
              data-confirm-title="Hapus Tanaman dari Katalog?">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <span>Hapus Tanaman</span>
            </button>
        </form>
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
        'action'  => route('tanaman-katalog.update', $catalog),
        'method'  => 'PUT',
        'catalog' => $catalog,
    ])

</div>
@endsection
