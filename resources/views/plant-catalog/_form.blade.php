{{--
    Partial: _form.blade.php
    Dipakai oleh create.blade.php dan edit.blade.php
    Variable yang dibutuhkan:
      - $action: URL form action
      - $method: 'POST' (create) atau 'PUT' (edit)
      - $catalog (opsional, untuk edit): PlantCatalog model
--}}

@php
    $isEdit  = ($method ?? 'POST') === 'PUT';
    $guides  = ($isEdit && isset($catalog)) ? $catalog->guides : collect();
@endphp

<form id="catalogForm" method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Section: Info Dasar --}}
    @if ($isEdit)
        <input type="hidden" name="name" value="{{ old('name', $catalog->name) }}">
        <input type="hidden" name="key" value="{{ old('key', $catalog->key) }}">
        <input type="hidden" name="emoji" value="{{ old('emoji', $catalog->emoji) }}">
        <input type="hidden" name="cycle" value="{{ old('cycle', $catalog->cycle) }}">
        <input type="hidden" name="theme" value="{{ old('theme', $catalog->theme) }}">
        <input type="hidden" name="urutan" value="{{ old('urutan', $catalog->urutan) }}">
        <input type="hidden" name="keywords" value="{{ old('keywords', $catalog->keywords) }}">
        <input type="hidden" name="aktif" value="{{ old('aktif', $catalog->aktif ? '1' : '0') }}">
    @else
        <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Informasi Tanaman</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Tanaman *</label>
                    <input type="text" name="name" id="catalogNameInput"
                           value="{{ old('name', $catalog->name ?? '') }}"
                           placeholder="e.g. Tomat"
                           required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Emoji Tanaman *</label>
                    <input type="text" name="emoji"
                           value="{{ old('emoji', $catalog->emoji ?? '🌱') }}"
                           placeholder="e.g. 🍅"
                           required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Estimasi Siklus Panen *</label>
                    <input type="text" name="cycle"
                           value="{{ old('cycle', $catalog->cycle ?? '60 – 80 HST') }}"
                           placeholder="e.g. 60 – 80 HST"
                           required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Warna Tema *</label>
                    <select name="theme" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition bg-white">
                        @foreach(['rose' => 'Merah (Rose/Tomat/Cabe)', 'emerald' => 'Hijau Emerald (Timun)', 'amber' => 'Kuning Amber (Jagung)', 'teal' => 'Teal (Hijau Kebiruan)', 'lime' => 'Lime (Hijau Muda)', 'sky' => 'Sky (Biru Langit)'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('theme', $catalog->theme ?? 'emerald') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <input type="hidden" name="key" id="catalogKeyInput" value="{{ old('key', $catalog->key ?: 'tanaman_' . time()) }}">
            <input type="hidden" name="urutan" value="{{ old('urutan', $catalog->urutan ?? 99) }}">
            <input type="hidden" name="keywords" value="{{ old('keywords', $catalog->keywords ?? '') }}">
            <input type="hidden" name="aktif" value="1">
        </div>
    @endif

    {{-- Section: Panduan Pemupukan (Guide Phases) --}}
    <div class="bg-surface rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h2 class="text-base font-bold text-gray-900">Panduan Pemupukan (Fase-fase)</h2>
            <button type="button"
                    onclick="addGuidePhase()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold transition-colors">
                + Tambah Fase
            </button>
        </div>

        <div id="guidePhasesList" class="space-y-4">
            @forelse ($guides as $index => $guide)
                <div class="guide-phase-block border border-gray-200 rounded-2xl p-5 space-y-4 relative bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Fase {{ $index + 1 }}</span>
                        <button type="button" onclick="removeGuidePhase(this)"
                                class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center text-sm transition-colors">×</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Nama Fase *</label>
                            <input type="text" name="guides[{{ $index }}][phase]"
                                   value="{{ old("guides.{$index}.phase", $guide->phase) }}"
                                   placeholder="e.g. Pemupukan Dasar"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Rentang HST *</label>
                            <input type="text" name="guides[{{ $index }}][hst]"
                                   value="{{ old("guides.{$index}.hst", $guide->hst) }}"
                                   placeholder="e.g. 0 HST (Sebelum Tanam)"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Fokus Nutrisi *</label>
                            <input type="text" name="guides[{{ $index }}][focus]"
                                   value="{{ old("guides.{$index}.focus", $guide->focus) }}"
                                   placeholder="e.g. Membangun struktur tanah & stok hara awal"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Daftar Pupuk/Nutrisi * <span class="font-normal text-text-muted">(satu baris = satu item)</span></label>
                            <textarea name="guides[{{ $index }}][nutrients]"
                                      rows="3"
                                      placeholder="NPK 16-16-16&#10;Urea&#10;Asam Humat"
                                      class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none font-mono">{{ old("guides.{$index}.nutrients", implode("\n", $guide->nutrients ?? [])) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Dosis & Takaran *</label>
                            <textarea name="guides[{{ $index }}][dosis]"
                                      rows="2"
                                      placeholder="e.g. 5 gram NPK per liter air, kocor 200 ml/tanaman."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none">{{ old("guides.{$index}.dosis", $guide->dosis) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Metode Aplikasi *</label>
                            <textarea name="guides[{{ $index }}][metode]"
                                      rows="2"
                                      placeholder="e.g. Kocor di sekitar perakaran, hindari terkena batang."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none">{{ old("guides.{$index}.metode", $guide->metode) }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Tips Praktis <span class="font-normal text-text-muted">(opsional)</span></label>
                            <textarea name="guides[{{ $index }}][tips]"
                                      rows="2"
                                      placeholder="e.g. Pastikan pH tanah 6.0–6.8 agar pupuk terserap optimal."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none">{{ old("guides.{$index}.tips", $guide->tips) }}</textarea>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Jika create baru, mulai kosong --}}
            @endforelse
        </div>

        @if ($guides->isEmpty())
            <p id="emptyGuideNote" class="text-sm text-text-muted text-center py-4">
                Belum ada fase panduan. Klik <strong>"+ Tambah Fase"</strong> untuk menambahkan.
            </p>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('dashboard') }}"
           class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold transition-colors">
            Batal
        </a>
    </div>
</form>

<script>
    let guideIndex = {{ $guides->count() }};

    function addGuidePhase() {
        const emptyNote = document.getElementById('emptyGuideNote');
        if (emptyNote) emptyNote.remove();

        const idx = guideIndex++;
        const container = document.getElementById('guidePhasesList');
        const block = document.createElement('div');
        block.className = 'guide-phase-block border border-gray-200 rounded-2xl p-5 space-y-4 relative bg-gray-50/50';
        block.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Fase Baru</span>
                <button type="button" onclick="removeGuidePhase(this)"
                        class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center text-sm transition-colors">×</button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Fase *</label>
                    <input type="text" name="guides[${idx}][phase]" placeholder="e.g. Pemupukan Dasar"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Rentang HST *</label>
                    <input type="text" name="guides[${idx}][hst]" placeholder="e.g. 0 HST (Sebelum Tanam)"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Fokus Nutrisi *</label>
                    <input type="text" name="guides[${idx}][focus]" placeholder="e.g. Membangun struktur tanah & stok hara awal"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Daftar Pupuk/Nutrisi * <span class="font-normal text-text-muted">(satu baris = satu item)</span></label>
                    <textarea name="guides[${idx}][nutrients]" rows="3" placeholder="NPK 16-16-16&#10;Urea&#10;Asam Humat"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none font-mono"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Dosis & Takaran *</label>
                    <textarea name="guides[${idx}][dosis]" rows="2" placeholder="e.g. 5 gram NPK per liter air."
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Metode Aplikasi *</label>
                    <textarea name="guides[${idx}][metode]" rows="2" placeholder="e.g. Kocor di sekitar perakaran."
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Tips Praktis <span class="font-normal text-text-muted">(opsional)</span></label>
                    <textarea name="guides[${idx}][tips]" rows="2" placeholder="e.g. Pastikan pH tanah 6.0–6.8."
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary transition resize-none"></textarea>
                </div>
            </div>
        `;
        container.appendChild(block);
        block.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function removeGuidePhase(btn) {
        btn.closest('.guide-phase-block').remove();
    }

</script>
