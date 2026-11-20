{{--
    Partial: _form.blade.php
    Dipakai oleh create.blade.php dan edit.blade.php
    Variable yang dibutuhkan:
      - $action: URL form action
      - $method: 'POST' (create) atau 'PUT' (edit)
      - $catalog: PlantCatalog model
--}}

@php
    $isEdit = ($method ?? 'POST') === 'PUT';

    // Tahapan Semai (Per Nomor)
    $semaiSteps = old('semai_steps', $catalog->structured_semai ?? []);
    if (empty($semaiSteps) && !empty($catalog->tahap_semai)) {
        $semaiSteps = \App\Models\PlantCatalog::parseTextToSteps($catalog->tahap_semai, 'semai', $catalog->name ?? '');
    }
    if (empty($semaiSteps)) {
        $semaiSteps = [
            ['nomor' => '1', 'judul' => '', 'deskripsi' => '']
        ];
    }

    // Tahapan Penanaman (Per Nomor)
    $penanamanSteps = old('penanaman_steps', $catalog->structured_penanaman ?? []);
    if (empty($penanamanSteps) && !empty($catalog->tahap_penanaman)) {
        $penanamanSteps = \App\Models\PlantCatalog::parseTextToSteps($catalog->tahap_penanaman, 'penanaman', $catalog->name ?? '');
    }
    if (empty($penanamanSteps)) {
        $penanamanSteps = [
            ['nomor' => '1', 'judul' => '', 'deskripsi' => '']
        ];
    }

    // Tahapan Pemeliharaan (Per Nomor)
    $pemeliharaanSteps = old('pemeliharaan_steps', $catalog->structured_pemeliharaan ?? []);
    if (empty($pemeliharaanSteps) && !empty($catalog->tahap_pemeliharaan)) {
        $pemeliharaanSteps = \App\Models\PlantCatalog::parseTextToSteps($catalog->tahap_pemeliharaan, 'pemeliharaan', $catalog->name ?? '');
    }
    if (empty($pemeliharaanSteps)) {
        $pemeliharaanSteps = [
            ['nomor' => '1', 'judul' => '', 'deskripsi' => '']
        ];
    }

    // Tips Penting (Per Judul)
    $tipsItems = old('tips_items', $catalog->structured_tips ?? []);
    if (empty($tipsItems) && !empty($catalog->tips_penting)) {
        $tipsItems = \App\Models\PlantCatalog::parseTextToCards($catalog->tips_penting, 'tips', $catalog->name ?? '');
    }
    if (empty($tipsItems)) {
        $tipsItems = [
            ['judul' => '', 'title' => '', 'content' => '']
        ];
    }

    // Problem & Solusi Hama (Per Judul)
    $hamaItems = old('hama_items', $catalog->structured_hama ?? []);
    if (empty($hamaItems) && !empty($catalog->hama_penyakit)) {
        $hamaItems = \App\Models\PlantCatalog::parseTextToCards($catalog->hama_penyakit, 'hama', $catalog->name ?? '');
    }
    if (empty($hamaItems)) {
        $hamaItems = [
            ['id' => null, 'judul' => '', 'title' => '', 'kategori' => 'Hama Utama', 'gejala' => '', 'solusi' => '']
        ];
    }
@endphp

<form id="catalogForm" method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <input type="hidden" name="redirect_to" value="{{ request('redirect_to', 'dashboard') }}">
    <input type="hidden" name="key" id="catalogKeyInput" value="{{ old('key', $catalog->key ?: 'tanaman_' . time()) }}">
    <input type="hidden" name="urutan" value="{{ old('urutan', $catalog->urutan ?? 99) }}">
    <input type="hidden" name="keywords" value="{{ old('keywords', $catalog->keywords ?? '') }}">
    <input type="hidden" name="aktif" value="1">

    {{-- Hidden full-text inputs untuk kompatibilitas database & sinkronisasi otomatis --}}
    <textarea name="tahap_semai" id="hidden_tahap_semai" class="hidden">{{ old('tahap_semai', $catalog->tahap_semai ?? '') }}</textarea>
    <textarea name="tahap_penanaman" id="hidden_tahap_penanaman" class="hidden">{{ old('tahap_penanaman', $catalog->tahap_penanaman ?? '') }}</textarea>
    <textarea name="tahap_pemeliharaan" id="hidden_tahap_pemeliharaan" class="hidden">{{ old('tahap_pemeliharaan', $catalog->tahap_pemeliharaan ?? '') }}</textarea>
    <textarea name="tips_penting" id="hidden_tips_penting" class="hidden">{{ old('tips_penting', $catalog->tips_penting ?? '') }}</textarea>
    <textarea name="hama_penyakit" id="hidden_hama_penyakit" class="hidden">{{ old('hama_penyakit', $catalog->hama_penyakit ?? '') }}</textarea>

    {{-- Section 1: Info Dasar Tanaman --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">1. Informasi Tanaman</h2>
                <p class="text-xs text-gray-500">Nama, karakter, varietas, dan identitas tanaman di katalog.</p>
            </div>
            <span class="text-2xl">{{ old('emoji', $catalog->emoji ?? '🌱') }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Tanaman <span class="text-rose-500">*</span></label>
                <input type="text"
                       name="name"
                       id="catalogNameInput"
                       value="{{ old('name', $catalog->name ?? '') }}"
                       placeholder="e.g. Timun, Cabe, Tomat, Jagung"
                       required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition bg-white font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Varietas / Jenis <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text"
                       name="varietas"
                       value="{{ old('varietas', $catalog->varietas ?? '') }}"
                       placeholder="e.g. Hibrida F1 (Hercules / Servo)"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition bg-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Emoji Karakter <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="text"
                           name="emoji"
                           id="catalogEmojiInput"
                           value="{{ old('emoji', $catalog->emoji ?? '🌱') }}"
                           placeholder="e.g. 🥒, 🌶️, 🍅, 🌽"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition bg-white text-center text-lg">
                </div>
            </div>

            <input type="hidden" name="cycle" value="{{ old('cycle', $catalog->cycle ?? '—') }}">
            <input type="hidden" name="theme" value="{{ old('theme', $catalog->theme ?? 'emerald') }}">

            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Singkat Tanaman <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="deskripsi"
                          rows="2"
                          placeholder="Ringkasan singkat tentang tanaman ini, keunggulan, serta kebutuhan habitat idealnya..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition resize-none">{{ old('deskripsi', $catalog->deskripsi ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Section 2: Tahap Semai Khusus Tanaman Ini (Per Nomor) --}}
    <div id="section_tahap_semai" class="bg-white rounded-2xl border border-emerald-200 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-emerald-100 pb-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                    🌱
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">2. Tahap Semai &amp; Pembibitan (Per Nomor)</h2>
                    <p class="text-xs text-gray-500">Edit per nomor langkah semai. Anda dapat menambah nomor baru kapan saja.</p>
                </div>
            </div>
            <button type="button"
                    onclick="addStepCard('semai')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Nomor Semai</span>
            </button>
        </div>

        <div id="semaiStepsContainer" class="space-y-3.5">
            @foreach ($semaiSteps as $index => $step)
                <div class="step-card bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200 p-4 transition-all shadow-2xs space-y-3" data-stage="semai">
                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-gray-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs border border-emerald-200 step-badge">
                                {{ $step['nomor'] ?? ($index + 1) }}
                            </span>
                            <span class="text-xs font-bold text-gray-700">Nomor Langkah:</span>
                            <input type="text"
                                   name="semai_steps[{{ $index }}][nomor]"
                                   value="{{ $step['nomor'] ?? ($index + 1) }}"
                                   onchange="syncStepBadge(this)"
                                   class="w-14 px-2 py-1 rounded-lg border border-gray-300 text-xs font-bold text-center text-gray-800 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 step-nomor-input">
                        </div>
                        <button type="button"
                                onclick="removeStepCard(this, 'semai')"
                                class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Hapus Nomor Ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Langkah</label>
                            <input type="text"
                                   name="semai_steps[{{ $index }}][judul]"
                                   value="{{ $step['judul'] ?? '' }}"
                                   placeholder="Contoh: Perendaman Benih &amp; Pemilihan Media"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Instruksi &amp; Deskripsi Detail</label>
                            <textarea name="semai_steps[{{ $index }}][deskripsi]"
                                      rows="2"
                                      placeholder="Tuliskan petunjuk teknis pelaksanaan..."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition leading-relaxed shadow-2xs">{{ $step['deskripsi'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button"
                onclick="addStepCard('semai')"
                class="w-full py-2.5 px-3 rounded-xl border border-dashed border-emerald-300 hover:border-emerald-500 bg-emerald-50/50 hover:bg-emerald-50 text-emerald-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Tambah Nomor / Langkah Semai</span>
        </button>
    </div>

    {{-- Section 3: Tahap Penanaman Khusus Tanaman Ini (Per Nomor) --}}
    <div id="section_tahap_penanaman" class="bg-white rounded-2xl border border-amber-200 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-amber-100 pb-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                    🌿
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">3. Tahap Penanaman &amp; Jarak Tanam (Per Nomor)</h2>
                    <p class="text-xs text-gray-500">Edit per nomor langkah penanaman. Anda dapat menambah nomor baru kapan saja.</p>
                </div>
            </div>
            <button type="button"
                    onclick="addStepCard('penanaman')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Nomor Penanaman</span>
            </button>
        </div>

        <div id="penanamanStepsContainer" class="space-y-3.5">
            @foreach ($penanamanSteps as $index => $step)
                <div class="step-card bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200 p-4 transition-all shadow-2xs space-y-3" data-stage="penanaman">
                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-gray-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xs border border-amber-200 step-badge">
                                {{ $step['nomor'] ?? ($index + 1) }}
                            </span>
                            <span class="text-xs font-bold text-gray-700">Nomor Langkah:</span>
                            <input type="text"
                                   name="penanaman_steps[{{ $index }}][nomor]"
                                   value="{{ $step['nomor'] ?? ($index + 1) }}"
                                   onchange="syncStepBadge(this)"
                                   class="w-14 px-2 py-1 rounded-lg border border-gray-300 text-xs font-bold text-center text-gray-800 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 step-nomor-input">
                        </div>
                        <button type="button"
                                onclick="removeStepCard(this, 'penanaman')"
                                class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Hapus Nomor Ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Langkah</label>
                            <input type="text"
                                   name="penanaman_steps[{{ $index }}][judul]"
                                   value="{{ $step['judul'] ?? '' }}"
                                   placeholder="Contoh: Jarak Tanam Ideal 50 cm x 60 cm"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Instruksi &amp; Deskripsi Detail</label>
                            <textarea name="penanaman_steps[{{ $index }}][deskripsi]"
                                      rows="2"
                                      placeholder="Tuliskan petunjuk teknis penanaman..."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition leading-relaxed shadow-2xs">{{ $step['deskripsi'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button"
                onclick="addStepCard('penanaman')"
                class="w-full py-2.5 px-3 rounded-xl border border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/50 hover:bg-amber-50 text-amber-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Tambah Nomor / Langkah Penanaman</span>
        </button>
    </div>

    {{-- Section 4: Tahap Pemeliharaan Khusus Tanaman Ini (Per Nomor) --}}
    <div id="section_tahap_pemeliharaan" class="bg-white rounded-2xl border border-sky-200 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-sky-100 pb-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-sm">
                    💧
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">4. Tahap Pemeliharaan &amp; Nutrisi (Per Nomor)</h2>
                    <p class="text-xs text-gray-500">Edit per nomor langkah pemeliharaan. Anda dapat menambah nomor baru kapan saja.</p>
                </div>
            </div>
            <button type="button"
                    onclick="addStepCard('pemeliharaan')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Nomor Pemeliharaan</span>
            </button>
        </div>

        <div id="pemeliharaanStepsContainer" class="space-y-3.5">
            @foreach ($pemeliharaanSteps as $index => $step)
                <div class="step-card bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200 p-4 transition-all shadow-2xs space-y-3" data-stage="pemeliharaan">
                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-gray-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-black text-xs border border-sky-200 step-badge">
                                {{ $step['nomor'] ?? ($index + 1) }}
                            </span>
                            <span class="text-xs font-bold text-gray-700">Nomor Langkah:</span>
                            <input type="text"
                                   name="pemeliharaan_steps[{{ $index }}][nomor]"
                                   value="{{ $step['nomor'] ?? ($index + 1) }}"
                                   onchange="syncStepBadge(this)"
                                   class="w-14 px-2 py-1 rounded-lg border border-gray-300 text-xs font-bold text-center text-gray-800 bg-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 step-nomor-input">
                        </div>
                        <button type="button"
                                onclick="removeStepCard(this, 'pemeliharaan')"
                                class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Hapus Nomor Ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Langkah</label>
                            <input type="text"
                                   name="pemeliharaan_steps[{{ $index }}][judul]"
                                   value="{{ $step['judul'] ?? '' }}"
                                   placeholder="Contoh: Pasang Ajir Bambu 1.8 Meter"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Instruksi &amp; Deskripsi Detail</label>
                            <textarea name="pemeliharaan_steps[{{ $index }}][deskripsi]"
                                      rows="2"
                                      placeholder="Tuliskan petunjuk teknis pemeliharaan..."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition leading-relaxed shadow-2xs">{{ $step['deskripsi'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button"
                onclick="addStepCard('pemeliharaan')"
                class="w-full py-2.5 px-3 rounded-xl border border-dashed border-sky-300 hover:border-sky-500 bg-sky-50/50 hover:bg-sky-50 text-sky-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Tambah Nomor / Langkah Pemeliharaan</span>
        </button>
    </div>

    {{-- Section 5: Tips Penting Khusus Tanaman Ini (Per Judul) --}}
    <div id="section_tips_penting" class="bg-white rounded-2xl border border-amber-300 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-amber-200 pb-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-sm">
                    💡
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">5. Tips Penting &amp; Pantangan (Per Judul)</h2>
                    <p class="text-xs text-gray-500">Edit per judul tips budidaya. Anda dapat menambah judul tips baru.</p>
                </div>
            </div>
            <button type="button"
                    onclick="addTipCard()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Judul Tips</span>
            </button>
        </div>

        <div id="tipsItemsContainer" class="space-y-3.5">
            @foreach ($tipsItems as $index => $tip)
                <div class="tip-card bg-amber-50/40 hover:bg-white rounded-2xl border border-amber-200/90 p-4 transition-all shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-amber-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-xs border border-amber-300">
                                💡
                            </span>
                            <span class="text-xs font-bold text-amber-900">Tips Budidaya #<span class="tip-badge-num">{{ $index + 1 }}</span></span>
                        </div>
                        <button type="button"
                                onclick="removeTipCard(this)"
                                class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Hapus Tips Ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Tips / Topik <span class="text-rose-500">*</span></label>
                            <input type="text"
                                   name="tips_items[{{ $index }}][judul]"
                                   value="{{ $tip['judul'] ?? ($tip['title'] ?? '') }}"
                                   placeholder="Contoh: Petik Buah Rutin 2 Hari Sekali"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Isi &amp; Penjelasan Tips <span class="text-rose-500">*</span></label>
                            <textarea name="tips_items[{{ $index }}][content]"
                                      rows="2"
                                      placeholder="Uraian rahasia budidaya, pantangan atau pencegahan..."
                                      class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition leading-relaxed shadow-2xs">{{ $tip['content'] ?? ($tip['deskripsi'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button"
                onclick="addTipCard()"
                class="w-full py-2.5 px-3 rounded-xl border border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/50 hover:bg-amber-50 text-amber-900 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Tambah Judul Tips Baru</span>
        </button>
    </div>

    {{-- Section 6: Problem & Solusi Hama Khusus Tanaman Ini (Per Judul) --}}
    <div id="section_hama_penyakit" class="bg-white rounded-2xl border border-rose-200 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-rose-100 pb-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-sm">
                    🐛
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">6. Problem &amp; Solusi Hama / Penyakit (Per Judul)</h2>
                    <p class="text-xs text-gray-500">Edit per judul problem dengan gejala dan solusi masing-masing.</p>
                </div>
            </div>
            <button type="button"
                    onclick="addHamaCard()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Judul Problem</span>
            </button>
        </div>

        <div id="hamaItemsContainer" class="space-y-4">
            @foreach ($hamaItems as $index => $hama)
                <div class="hama-card bg-rose-50/40 hover:bg-white rounded-2xl border border-rose-200/90 p-4 transition-all shadow-2xs space-y-3">
                    <input type="hidden" name="hama_items[{{ $index }}][id]" value="{{ $hama['id'] ?? '' }}">

                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-rose-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-xs border border-rose-200">
                                🐛
                            </span>
                            <span class="text-xs font-bold text-rose-900">Problem &amp; Solusi Hama #<span class="hama-badge-num">{{ $index + 1 }}</span></span>
                        </div>
                        <button type="button"
                                onclick="removeHamaCard(this)"
                                class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Hapus Problem Ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Hama / Problem <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       name="hama_items[{{ $index }}][judul]"
                                       value="{{ $hama['judul'] ?? ($hama['title'] ?? '') }}"
                                       placeholder="Contoh: Thrips &amp; Kutu Daun"
                                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">Kategori</label>
                                @php $kat = $hama['kategori'] ?? ($hama['category'] ?? 'Hama Utama'); @endphp
                                <select name="hama_items[{{ $index }}][kategori]"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition shadow-2xs">
                                    <option value="Hama Utama" {{ $kat === 'Hama Utama' ? 'selected' : '' }}>Hama Utama</option>
                                    <option value="Penyakit Utama" {{ $kat === 'Penyakit Utama' ? 'selected' : '' }}>Penyakit Utama</option>
                                    <option value="Fisiologis & Cuaca" {{ $kat === 'Fisiologis & Cuaca' ? 'selected' : '' }}>Fisiologis &amp; Cuaca</option>
                                    <option value="Gulma & Lainnya" {{ $kat === 'Gulma & Lainnya' ? 'selected' : '' }}>Gulma &amp; Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-amber-900 mb-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                                <span>Gejala Serangan</span>
                            </label>
                            <textarea name="hama_items[{{ $index }}][gejala]"
                                      rows="2"
                                      placeholder="Ciri-ciri serangan pada daun, batang, atau buah..."
                                      class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition leading-relaxed shadow-2xs">{{ $hama['gejala'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-emerald-900 mb-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Solusi &amp; Pengendalian</span>
                            </label>
                            <textarea name="hama_items[{{ $index }}][solusi]"
                                      rows="2"
                                      placeholder="Langkah penanganan, bahan aktif, dosis semprot..."
                                      class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition leading-relaxed shadow-2xs">{{ $hama['solusi'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button"
                onclick="addHamaCard()"
                class="w-full py-2.5 px-3 rounded-xl border border-dashed border-rose-300 hover:border-rose-500 bg-rose-50/50 hover:bg-rose-50 text-rose-800 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Tambah Judul Problem &amp; Solusi Baru</span>
        </button>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-200">
        <a href="{{ $isEdit ? route('tanaman-katalog.show', $catalog) : route('dashboard') }}"
           class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold transition-colors">
            Batal
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-sm transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span>{{ $isEdit ? 'Simpan Perubahan Tanaman' : 'Tambahkan ke Katalog' }}</span>
        </button>
    </div>
</form>

<script>
    // Sinkronisasi otomatis slug key dari nama jika create baru
    const nameInput = document.getElementById('catalogNameInput');
    const keyInput = document.getElementById('catalogKeyInput');
    const emojiInput = document.getElementById('catalogEmojiInput');

    if (nameInput && keyInput && !{{ $isEdit ? 'true' : 'false' }}) {
        nameInput.addEventListener('input', function() {
            if (!keyInput.dataset.manual) {
                keyInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            }

            // Tebak emoji jika masih default
            const val = this.value.toLowerCase();
            if (emojiInput && (emojiInput.value === '🌱' || emojiInput.value === '')) {
                if (val.includes('timun') || val.includes('mentimun')) emojiInput.value = '🥒';
                else if (val.includes('cabe') || val.includes('cabai') || val.includes('lombok')) emojiInput.value = '🌶️';
                else if (val.includes('tomat')) emojiInput.value = '🍅';
                else if (val.includes('jagung')) emojiInput.value = '🌽';
                else if (val.includes('semangka')) emojiInput.value = '🍉';
                else if (val.includes('melon')) emojiInput.value = '🍈';
                else if (val.includes('terong')) emojiInput.value = '🍆';
                else if (val.includes('wortel')) emojiInput.value = '🥕';
                else if (val.includes('bawang')) emojiInput.value = '🧅';
                else if (val.includes('kentang')) emojiInput.value = '🥔';
                else if (val.includes('kubis') || val.includes('kol') || val.includes('sawi')) emojiInput.value = '🥬';
            }
        });
    }

    // Sinkronisasi badge nomor langkah saat nomor diedit manual
    function syncStepBadge(input) {
        const card = input.closest('.step-card');
        if (card) {
            const badge = card.querySelector('.step-badge');
            if (badge) badge.textContent = input.value.trim() || '•';
        }
    }

    // Tambah Card Nomor / Langkah Baru untuk Semai, Penanaman, Pemeliharaan
    function addStepCard(stage) {
        const container = document.getElementById(stage + 'StepsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.step-card');
        const nextIndex = cards.length;
        const nextNomor = nextIndex + 1;

        const themeStyles = {
            semai: {
                badgeBg: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                focusRing: 'focus:border-emerald-500 focus:ring-emerald-500',
                placeholderJudul: 'Contoh: Langkah Perendaman Benih',
                placeholderDesk: 'Tuliskan petunjuk teknis langkah semai ini...'
            },
            penanaman: {
                badgeBg: 'bg-amber-100 text-amber-800 border-amber-200',
                focusRing: 'focus:border-amber-500 focus:ring-amber-500',
                placeholderJudul: 'Contoh: Langkah Jarak Tanam & Penancapan Ajir',
                placeholderDesk: 'Tuliskan petunjuk teknis penanaman...'
            },
            pemeliharaan: {
                badgeBg: 'bg-sky-100 text-sky-800 border-sky-200',
                focusRing: 'focus:border-sky-500 focus:ring-sky-500',
                placeholderJudul: 'Contoh: Langkah Pemupukan & Pruning Tunas',
                placeholderDesk: 'Tuliskan petunjuk teknis pemeliharaan...'
            }
        };

        const theme = themeStyles[stage] || themeStyles.semai;

        const card = document.createElement('div');
        card.className = 'step-card bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200 p-4 transition-all shadow-2xs space-y-3';
        card.dataset.stage = stage;
        card.innerHTML = `
            <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-gray-200/70">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg ${theme.badgeBg} flex items-center justify-center font-black text-xs border step-badge">
                        ${nextNomor}
                    </span>
                    <span class="text-xs font-bold text-gray-700">Nomor Langkah:</span>
                    <input type="text"
                           name="${stage}_steps[${nextIndex}][nomor]"
                           value="${nextNomor}"
                           onchange="syncStepBadge(this)"
                           class="w-14 px-2 py-1 rounded-lg border border-gray-300 text-xs font-bold text-center text-gray-800 bg-white focus:outline-none ${theme.focusRing} focus:ring-1 step-nomor-input">
                </div>
                <button type="button"
                        onclick="removeStepCard(this, '${stage}')"
                        class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                        title="Hapus Nomor Ini">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Langkah</label>
                    <input type="text"
                           name="${stage}_steps[${nextIndex}][judul]"
                           value=""
                           placeholder="${theme.placeholderJudul}"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none ${theme.focusRing} focus:ring-1 transition shadow-2xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Instruksi &amp; Deskripsi Detail</label>
                    <textarea name="${stage}_steps[${nextIndex}][deskripsi]"
                              rows="2"
                              placeholder="${theme.placeholderDesk}"
                              class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none ${theme.focusRing} focus:ring-1 transition leading-relaxed shadow-2xs"></textarea>
                </div>
            </div>
        `;

        container.appendChild(card);
        const judulInput = card.querySelector('input[type="text"]:not(.step-nomor-input)');
        if (judulInput) judulInput.focus();
    }

    // Hapus Card Nomor Langkah
    function removeStepCard(button, stage) {
        const container = document.getElementById(stage + 'StepsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.step-card');
        if (cards.length <= 1) {
            // Kosongkan card terakhir alih-alih menghapus
            const lastCard = cards[0];
            const inputs = lastCard.querySelectorAll('input:not(.step-nomor-input), textarea');
            inputs.forEach(el => el.value = '');
            return;
        }

        const card = button.closest('.step-card');
        if (card) {
            card.remove();
            renumberStepCards(stage);
        }
    }

    // Urutkan ulang nomor langkah setelah hapus
    function renumberStepCards(stage) {
        const container = document.getElementById(stage + 'StepsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.step-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.step-badge');
            const nomorInput = card.querySelector('.step-nomor-input');
            const judulInput = card.querySelector(`input[name^="${stage}_steps"][name$="[judul]"]`);
            const deskInput = card.querySelector(`textarea[name^="${stage}_steps"][name$="[deskripsi]"]`);

            const newNum = idx + 1;
            if (badge) badge.textContent = newNum;
            if (nomorInput) {
                nomorInput.value = newNum;
                nomorInput.name = `${stage}_steps[${idx}][nomor]`;
            }
            if (judulInput) judulInput.name = `${stage}_steps[${idx}][judul]`;
            if (deskInput) deskInput.name = `${stage}_steps[${idx}][deskripsi]`;
        });
    }

    // Tambah Card Tips Penting (Per Judul)
    function addTipCard() {
        const container = document.getElementById('tipsItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.tip-card');
        const nextIndex = cards.length;
        const nextNumber = nextIndex + 1;

        const card = document.createElement('div');
        card.className = 'tip-card bg-amber-50/40 hover:bg-white rounded-2xl border border-amber-200/90 p-4 transition-all shadow-2xs space-y-3';
        card.innerHTML = `
            <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-amber-200/70">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-xs border border-amber-300">
                        💡
                    </span>
                    <span class="text-xs font-bold text-amber-900">Tips Budidaya #<span class="tip-badge-num">${nextNumber}</span></span>
                </div>
                <button type="button"
                        onclick="removeTipCard(this)"
                        class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                        title="Hapus Tips Ini">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Tips / Topik <span class="text-rose-500">*</span></label>
                    <input type="text"
                           name="tips_items[${nextIndex}][judul]"
                           value=""
                           placeholder="Contoh: Petik Buah Rutin 2 Hari Sekali"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition shadow-2xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Isi &amp; Penjelasan Tips <span class="text-rose-500">*</span></label>
                    <textarea name="tips_items[${nextIndex}][content]"
                              rows="2"
                              placeholder="Uraian rahasia budidaya, pantangan atau pencegahan..."
                              class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition leading-relaxed shadow-2xs"></textarea>
                </div>
            </div>
        `;

        container.appendChild(card);
        const judulInput = card.querySelector('input[type="text"]');
        if (judulInput) judulInput.focus();
    }

    // Hapus Card Tips Penting
    function removeTipCard(button) {
        const container = document.getElementById('tipsItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.tip-card');
        if (cards.length <= 1) {
            const lastCard = cards[0];
            lastCard.querySelectorAll('input, textarea').forEach(el => el.value = '');
            return;
        }

        const card = button.closest('.tip-card');
        if (card) {
            card.remove();
            renumberTipCards();
        }
    }

    // Urutkan ulang nomor badge tips
    function renumberTipCards() {
        const container = document.getElementById('tipsItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.tip-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.tip-badge-num');
            const judulInput = card.querySelector('input[name^="tips_items"]');
            const contentInput = card.querySelector('textarea[name^="tips_items"]');

            if (badge) badge.textContent = idx + 1;
            if (judulInput) judulInput.name = `tips_items[${idx}][judul]`;
            if (contentInput) contentInput.name = `tips_items[${idx}][content]`;
        });
    }

    // Tambah Card Problem & Solusi Hama (Per Judul)
    function addHamaCard() {
        const container = document.getElementById('hamaItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.hama-card');
        const nextIndex = cards.length;
        const nextNumber = nextIndex + 1;

        const card = document.createElement('div');
        card.className = 'hama-card bg-rose-50/40 hover:bg-white rounded-2xl border border-rose-200/90 p-4 transition-all shadow-2xs space-y-3';
        card.innerHTML = `
            <input type="hidden" name="hama_items[${nextIndex}][id]" value="">

            <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-rose-200/70">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-xs border border-rose-200">
                        🐛
                    </span>
                    <span class="text-xs font-bold text-rose-900">Problem &amp; Solusi Hama #<span class="hama-badge-num">${nextNumber}</span></span>
                </div>
                <button type="button"
                        onclick="removeHamaCard(this)"
                        class="text-gray-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                        title="Hapus Problem Ini">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Hama / Problem <span class="text-rose-500">*</span></label>
                        <input type="text"
                               name="hama_items[${nextIndex}][judul]"
                               value=""
                               placeholder="Contoh: Thrips &amp; Kutu Daun"
                               class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs sm:text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Kategori</label>
                        <select name="hama_items[${nextIndex}][kategori]"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-900 bg-white focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition shadow-2xs">
                            <option value="Hama Utama">Hama Utama</option>
                            <option value="Penyakit Utama">Penyakit Utama</option>
                            <option value="Fisiologis &amp; Cuaca">Fisiologis &amp; Cuaca</option>
                            <option value="Gulma &amp; Lainnya">Gulma &amp; Lainnya</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-amber-900 mb-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <span>Gejala Serangan</span>
                    </label>
                    <textarea name="hama_items[${nextIndex}][gejala]"
                              rows="2"
                              placeholder="Ciri-ciri serangan pada daun, batang, atau buah..."
                              class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition leading-relaxed shadow-2xs"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-emerald-900 mb-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Solusi &amp; Pengendalian</span>
                    </label>
                    <textarea name="hama_items[${nextIndex}][solusi]"
                              rows="2"
                              placeholder="Langkah penanganan, bahan aktif, dosis semprot..."
                              class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs sm:text-sm text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition leading-relaxed shadow-2xs"></textarea>
                </div>
            </div>
        `;

        container.appendChild(card);
        const judulInput = card.querySelector('input[name$="[judul]"]');
        if (judulInput) judulInput.focus();
    }

    // Hapus Card Problem & Solusi
    function removeHamaCard(button) {
        const container = document.getElementById('hamaItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.hama-card');
        if (cards.length <= 1) {
            const lastCard = cards[0];
            lastCard.querySelectorAll('input:not([type="hidden"]), textarea').forEach(el => el.value = '');
            return;
        }

        const card = button.closest('.hama-card');
        if (card) {
            card.remove();
            renumberHamaCards();
        }
    }

    // Urutkan ulang nomor badge hama
    function renumberHamaCards() {
        const container = document.getElementById('hamaItemsContainer');
        if (!container) return;

        const cards = container.querySelectorAll('.hama-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.hama-badge-num');
            const idInput = card.querySelector('input[name^="hama_items"][name$="[id]"]');
            const judulInput = card.querySelector('input[name^="hama_items"][name$="[judul]"]');
            const katInput = card.querySelector('select[name^="hama_items"][name$="[kategori]"]');
            const gejalaInput = card.querySelector('textarea[name^="hama_items"][name$="[gejala]"]');
            const solusiInput = card.querySelector('textarea[name^="hama_items"][name$="[solusi]"]');

            if (badge) badge.textContent = idx + 1;
            if (idInput) idInput.name = `hama_items[${idx}][id]`;
            if (judulInput) judulInput.name = `hama_items[${idx}][judul]`;
            if (katInput) katInput.name = `hama_items[${idx}][kategori]`;
            if (gejalaInput) gejalaInput.name = `hama_items[${idx}][gejala]`;
            if (solusiInput) solusiInput.name = `hama_items[${idx}][solusi]`;
        });
    }

    // Kompilasi otomatis array repeater ke hidden textarea sebelum form submit
    function compileAllRepeatersToHiddenText() {
        ['semai', 'penanaman', 'pemeliharaan'].forEach(stage => {
            const container = document.getElementById(stage + 'StepsContainer');
            const hiddenTextarea = document.getElementById('hidden_tahap_' + stage);
            if (!container || !hiddenTextarea) return;

            const cards = container.querySelectorAll('.step-card');
            const blocks = [];
            let fallbackNum = 1;

            cards.forEach(card => {
                const nomorInput = card.querySelector('.step-nomor-input');
                const judulInput = card.querySelector(`input[name^="${stage}_steps"][name$="[judul]"]`);
                const deskInput = card.querySelector(`textarea[name^="${stage}_steps"][name$="[deskripsi]"]`);

                const nomor = (nomorInput ? nomorInput.value.trim() : '') || String(fallbackNum);
                const judul = judulInput ? judulInput.value.trim() : '';
                const deskripsi = deskInput ? deskInput.value.trim() : '';

                if (judul === '' && deskripsi === '') return;

                const header = judul !== '' ? `${nomor}. ${judul}:` : `${nomor}.`;
                if (deskripsi !== '') {
                    blocks.push(`${header}\n${deskripsi}`);
                } else {
                    blocks.push(header);
                }
                fallbackNum++;
            });

            hiddenTextarea.value = blocks.join("\n\n");
        });

        // Tips
        const tipsContainer = document.getElementById('tipsItemsContainer');
        const hiddenTips = document.getElementById('hidden_tips_penting');
        if (tipsContainer && hiddenTips) {
            const cards = tipsContainer.querySelectorAll('.tip-card');
            const blocks = [];
            let tipNum = 1;

            cards.forEach(card => {
                const judulInput = card.querySelector('input[name^="tips_items"][name$="[judul]"]');
                const contentInput = card.querySelector('textarea[name^="tips_items"][name$="[content]"]');

                const judul = judulInput ? judulInput.value.trim() : '';
                const content = contentInput ? contentInput.value.trim() : '';

                if (judul === '' && content === '') return;

                if (judul !== '' && content !== '') {
                    blocks.push(`${tipNum}. ${judul}:\n${content}`);
                } else if (judul !== '') {
                    blocks.push(`${tipNum}. ${judul}`);
                } else {
                    blocks.push(`• ${content}`);
                }
                tipNum++;
            });

            hiddenTips.value = blocks.join("\n\n");
        }

        // Hama
        const hamaContainer = document.getElementById('hamaItemsContainer');
        const hiddenHama = document.getElementById('hidden_hama_penyakit');
        if (hamaContainer && hiddenHama) {
            const cards = hamaContainer.querySelectorAll('.hama-card');
            const blocks = [];
            let hamaNum = 1;

            cards.forEach(card => {
                const judulInput = card.querySelector('input[name^="hama_items"][name$="[judul]"]');
                const gejalaInput = card.querySelector('textarea[name^="hama_items"][name$="[gejala]"]');
                const solusiInput = card.querySelector('textarea[name^="hama_items"][name$="[solusi]"]');

                const judul = judulInput ? judulInput.value.trim() : '';
                const gejala = gejalaInput ? gejalaInput.value.trim() : '';
                const solusi = solusiInput ? solusiInput.value.trim() : '';

                if (judul === '' && gejala === '' && solusi === '') return;

                const lines = [];
                if (gejala !== '') lines.push(`Gejala:\n${gejala}`);
                if (solusi !== '') lines.push(`Solusi:\n${solusi}`);

                const body = lines.join("\n\n");
                if (judul !== '' && body !== '') {
                    blocks.push(`${hamaNum}. ${judul}:\n${body}`);
                } else if (judul !== '') {
                    blocks.push(`${hamaNum}. ${judul}`);
                } else {
                    blocks.push(body);
                }
                hamaNum++;
            });

            hiddenHama.value = blocks.join("\n\n");
        }
    }

    const form = document.getElementById('catalogForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            compileAllRepeatersToHiddenText();

            AgriFormHelper.clearErrors(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            AgriFormHelper.setLoading(submitBtn, true, 'Menyimpan Panduan...');

            const fd = new FormData(form);
            const actionUrl = form.getAttribute('action');

            if (!navigator.onLine) {
                try {
                    const dataObj = {
                        name: fd.get('name') || '',
                        latin_name: fd.get('latin_name') || '',
                        emoji: fd.get('emoji') || '🌱',
                        deskripsi: fd.get('deskripsi') || '',
                        tahap_semai: fd.get('tahap_semai') || '',
                        tahap_penanaman: fd.get('tahap_penanaman') || '',
                        tahap_pemeliharaan: fd.get('tahap_pemeliharaan') || '',
                        tips_penting: fd.get('tips_penting') || '',
                        hama_penyakit: fd.get('hama_penyakit') || ''
                    };
                    await AgriSyncManager.save('plant_catalogs', dataObj, {
                        sync_status: 'pending',
                        sync_action: form.querySelector('input[name="_method"]')?.value === 'PUT' ? 'UPDATE' : 'CREATE'
                    });
                    AgriSwal.toastSuccess(`Panduan "${dataObj.name}" berhasil disimpan di HP (Mode Offline).`);
                    setTimeout(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    }, 800);
                } catch (err) {
                    AgriSwal.toastError('Gagal menyimpan offline: ' + err.message);
                } finally {
                    AgriFormHelper.setLoading(submitBtn, false);
                }
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: fd
                });

                const result = await response.json().catch(() => null);

                if (response.status === 422 && result && result.errors) {
                    AgriFormHelper.showErrors(form, result.errors);
                    AgriSwal.toastError('Mohon periksa data yang ditandai merah.');
                    // Scroll to first error
                    const firstErr = form.querySelector('.field-error-msg');
                    if (firstErr) {
                        firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else if (response.ok && result && result.success !== false) {
                    AgriSwal.toastSuccess(result.message || 'Panduan tanaman berhasil disimpan!');
                    const cat = result.catalog || {};
                    if (cat.id) {
                        await AgriSyncManager.save('plant_catalogs', cat, { sync_status: 'synced' }).catch(() => {});
                    }
                    setTimeout(() => {
                        const redirectInput = form.querySelector('input[name="redirect_to"]');
                        if (redirectInput && redirectInput.value === 'show' && cat.id) {
                            window.location.href = `/tanaman-katalog/${cat.id}`;
                        } else {
                            window.location.href = '{{ route("dashboard") }}';
                        }
                    }, 800);
                } else {
                    AgriSwal.toastError(result?.message || 'Gagal menyimpan panduan.');
                }
            } catch (err) {
                console.warn('Network error on catalog form:', err);
                AgriSwal.toastError('Gagal menyimpan. Periksa koneksi Anda.');
            } finally {
                AgriFormHelper.setLoading(submitBtn, false);
            }
        });
    }
</script>
