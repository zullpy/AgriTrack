@extends('layouts.app')

@section('title', isset($medicine) ? 'Edit Obat' : 'Tambah Obat')

@section('content')

{{-- ── Breadcrumb / Back ── --}}
<div class="flex items-center gap-2 text-sm text-text-muted mb-6">
    <a href="/data-obat" class="hover:text-primary transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
        </svg>
        Data Obat Tanaman
    </a>
    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    <span class="text-text font-medium">{{ isset($medicine) ? 'Edit Obat' : 'Tambah Obat Baru' }}</span>
</div>

{{-- ── Page Header ── --}}
<div class="flex items-center gap-4 mb-8">
    <a href="/data-obat"
       class="p-2.5 rounded-xl border border-gray-200 bg-surface hover:bg-gray-50 hover:border-gray-300 text-text-muted hover:text-text transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-text">{{ isset($medicine) ? 'Edit Obat' : 'Tambah Obat Baru' }}</h1>
        <p class="text-sm text-text-secondary mt-0.5">
            {{ isset($medicine) ? 'Perbarui informasi data obat tanaman.' : 'Isi informasi lengkap obat tanaman baru.' }}
        </p>
    </div>
</div>

{{-- ── Form ── --}}
<div class="max-w-2xl">
    <form method="POST"
          action="{{ isset($medicine) ? '/data-obat/' . $medicine->id : '/data-obat' }}"
          class="space-y-5">
        @csrf
        @if (isset($medicine))
            @method('PUT')
        @endif

        {{-- Card: Informasi Dasar --}}
        <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-primary-light flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-primary-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-text">Informasi Dasar</p>
            </div>
            <div class="p-5 space-y-4">
                {{-- Nama Obat --}}
                <div>
                    <label for="nama" class="block text-sm font-medium text-text mb-1.5">
                        Nama Obat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama', $medicine->nama ?? '') }}"
                           placeholder="Contoh: Decis 25 EC"
                           class="field-input w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-page/50 text-sm text-text placeholder:text-text-muted transition-all @error('nama') !border-red-400 bg-red-50/30 @enderror"
                           required>
                    @error('nama')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Jenis --}}
                <div>
                    <label for="jenis" class="block text-sm font-medium text-text mb-1.5">
                        Jenis <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="jenis-radio-group">
                        @foreach ([
                            'Pestisida' => ['color' => 'text-red-600 border-red-200 bg-red-50', 'check' => 'bg-red-500'],
                            'Fungisida' => ['color' => 'text-purple-600 border-purple-200 bg-purple-50', 'check' => 'bg-purple-500'],
                            'Pupuk'     => ['color' => 'text-primary-dark border-primary/30 bg-primary-light', 'check' => 'bg-primary'],
                            'Herbisida' => ['color' => 'text-amber-600 border-amber-200 bg-amber-50', 'check' => 'bg-amber-500'],
                        ] as $j => $style)
                            @php $selected = old('jenis', $medicine->jenis ?? '') === $j; @endphp
                            <label class="jenis-option relative flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition-all select-none
                                         {{ $selected ? $style['color'] . ' border-opacity-100' : 'border-gray-200 hover:border-gray-300 bg-gray-50/50' }}"
                                   data-color="{{ $style['color'] }}">
                                <input type="radio" name="jenis" value="{{ $j }}" class="sr-only" {{ $selected ? 'checked' : '' }} required>
                                <div class="w-3 h-3 rounded-full {{ $style['check'] }} {{ $selected ? 'opacity-100' : 'opacity-20' }} transition-opacity"></div>
                                <span class="text-xs font-semibold {{ $selected ? '' : 'text-text-secondary' }}">{{ $j }}</span>
                                @if($selected)
                                    <div class="absolute top-1.5 right-1.5 w-3 h-3 rounded-full {{ $style['check'] }} flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('jenis')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tanaman Sasaran --}}
                <div>
                    <label for="tanaman_sasaran" class="block text-sm font-medium text-text mb-1.5">
                        Tanaman Sasaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 007.92 12.446A9 9 0 1112 3z"/>
                        </svg>
                        <input type="text"
                               id="tanaman_sasaran"
                               name="tanaman_sasaran"
                               value="{{ old('tanaman_sasaran', $medicine->tanaman_sasaran ?? '') }}"
                               placeholder="Contoh: Padi, Jagung, Cabai"
                               class="field-input w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-page/50 text-sm text-text placeholder:text-text-muted transition-all @error('tanaman_sasaran') !border-red-400 bg-red-50/30 @enderror"
                               required>
                    </div>
                    @error('tanaman_sasaran')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Card: Detail Penggunaan --}}
        <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-text">Detail Penggunaan</p>
                <span class="ml-auto text-xs text-text-muted">Opsional</span>
            </div>
            <div class="p-5 space-y-4">
                {{-- Dosis & Interval (2 cols) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="dosis_anjuran" class="block text-sm font-medium text-text mb-1.5">Dosis Anjuran</label>
                        <input type="text"
                               id="dosis_anjuran"
                               name="dosis_anjuran"
                               value="{{ old('dosis_anjuran', $medicine->dosis_anjuran ?? '') }}"
                               placeholder="Contoh: 2 ml/liter air"
                               class="field-input w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-page/50 text-sm text-text placeholder:text-text-muted transition-all">
                    </div>
                    <div>
                        <label for="interval_aplikasi" class="block text-sm font-medium text-text mb-1.5">Interval Aplikasi</label>
                        <input type="text"
                               id="interval_aplikasi"
                               name="interval_aplikasi"
                               value="{{ old('interval_aplikasi', $medicine->interval_aplikasi ?? '') }}"
                               placeholder="Contoh: Setiap 7 hari"
                               class="field-input w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-page/50 text-sm text-text placeholder:text-text-muted transition-all">
                    </div>
                </div>

                {{-- Stok --}}
                <div>
                    <label for="stok" class="block text-sm font-medium text-text mb-1.5">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-0 rounded-xl border border-gray-200 bg-page/50 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all @error('stok') !border-red-400 @enderror">
                        <button type="button" id="stok-minus"
                                class="px-4 py-2.5 text-text-muted hover:text-primary hover:bg-primary-light/40 transition-colors border-r border-gray-200 font-semibold text-lg">−</button>
                        <input type="number"
                               id="stok"
                               name="stok"
                               value="{{ old('stok', $medicine->stok ?? 0) }}"
                               min="0"
                               class="flex-1 px-4 py-2.5 bg-transparent text-sm text-text text-center focus:outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                               required>
                        <button type="button" id="stok-plus"
                                class="px-4 py-2.5 text-text-muted hover:text-primary hover:bg-primary-light/40 transition-colors border-l border-gray-200 font-semibold text-lg">+</button>
                    </div>
                    <p class="text-[11px] text-text-muted mt-1.5">Masukkan jumlah stok yang tersedia saat ini.</p>
                    @error('stok')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Catatan Keamanan --}}
                <div>
                    <label for="catatan_keamanan" class="block text-sm font-medium text-text mb-1.5">Catatan Keamanan</label>
                    <textarea id="catatan_keamanan"
                              name="catatan_keamanan"
                              rows="3"
                              placeholder="Informasi keamanan penggunaan, efek samping, atau peringatan..."
                              class="field-input w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-page/50 text-sm text-text placeholder:text-text-muted transition-all resize-none">{{ old('catatan_keamanan', $medicine->catatan_keamanan ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Action Buttons ── --}}
        <div class="flex items-center gap-3 pt-1">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark active:scale-95 transition-all duration-150 shadow-md shadow-primary/25">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                {{ isset($medicine) ? 'Simpan Perubahan' : 'Simpan Obat' }}
            </button>

            <a href="/data-obat"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-text-secondary hover:bg-gray-50 hover:text-text transition-all">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Stok stepper
    const stokInput = document.getElementById('stok');
    document.getElementById('stok-minus').addEventListener('click', () => {
        const v = parseInt(stokInput.value) || 0;
        if (v > 0) stokInput.value = v - 1;
    });
    document.getElementById('stok-plus').addEventListener('click', () => {
        stokInput.value = (parseInt(stokInput.value) || 0) + 1;
    });

    // Jenis radio card UI
    document.querySelectorAll('.jenis-option').forEach(label => {
        label.addEventListener('click', () => {
            // Reset all
            document.querySelectorAll('.jenis-option').forEach(l => {
                l.className = l.className
                    .replace(/text-\S+ border-\S+ bg-\S+/, '')
                    .replace(/border-opacity-100/, '');
                l.classList.add('border-gray-200', 'hover:border-gray-300', 'bg-gray-50/50');
                l.querySelectorAll('[class*="opacity"]').forEach(el => {
                    el.classList.replace('opacity-100', 'opacity-20');
                });
                const checkMark = l.querySelector('.absolute');
                if (checkMark) checkMark.remove();
                const txt = l.querySelector('span');
                if (txt) txt.className = txt.className.replace(/text-\S+/, 'text-text-secondary');
            });

            // Activate selected
            const colorClass = label.dataset.color;
            label.classList.remove('border-gray-200', 'hover:border-gray-300', 'bg-gray-50/50');
            colorClass.split(' ').forEach(c => label.classList.add(c));
            label.querySelectorAll('[class*="opacity-20"]').forEach(el => {
                el.classList.replace('opacity-20', 'opacity-100');
            });
        });
    });
</script>
@endpush
