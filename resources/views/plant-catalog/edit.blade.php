@extends('layouts.app')

@section('title', 'Edit Tanaman — ' . $catalog->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="pb-2 border-b border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Tanaman — {{ $catalog->emoji }} {{ $catalog->name }}</h1>
        <p class="text-sm text-text-muted mt-0.5">Perubahan akan langsung tercermin di halaman Dashboard.</p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
            <p class="font-semibold">Harap perbaiki kesalahan berikut:</p>
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
