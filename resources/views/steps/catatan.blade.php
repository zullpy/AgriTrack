@extends('layouts.app')

@section('title', 'Catatan Penting Budidaya')

@section('content')
<div class="w-full max-w-full min-w-0 overflow-x-hidden space-y-6">

    {{-- ── Header Section ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="/steps" class="hover:text-emerald-700 transition-colors font-medium">Tahapan</a>
                <span>/</span>
                <span class="text-emerald-700 font-semibold">Catatan Penting</span>
            </nav>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Catatan Penting Budidaya</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Fase 3: Aturan Lapangan
                </span>
                <span id="notes-counter" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                    {{ count($notes) }} Catatan Tersimpan
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kumpulan instruksi kehati-hatian, waktu semprot, sanitasi alat, dan catatan teknis lapangan yang fleksibel dikelola.</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button"
                    onclick="openAddNoteModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Catatan</span>
            </button>
        </div>
    </div>

    {{-- ── Quick Navigation Tabs ── --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit max-w-full overflow-x-auto no-scrollbar">
        <a href="/steps/pengolahan-tanah"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            1. Pengolahan Tanah
        </a>
        <a href="/steps/penanaman-bibit"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 whitespace-nowrap transition-all">
            2. Penanaman Bibit
        </a>
        <a href="/steps/catatan"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-emerald-800 shadow-xs whitespace-nowrap transition-all">
            3. Catatan Penting
        </a>
    </div>

    {{-- ── Filter / Search Bar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text"
                   id="search-notes-input"
                   oninput="filterNotesList(this.value)"
                   placeholder="Cari judul atau isi catatan..."
                   class="w-full pl-9 pr-8 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-2xs">
            <button type="button"
                    id="btn-clear-search"
                    onclick="clearSearchNotes()"
                    class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="text-xs text-gray-500 font-medium">
            Menampilkan <span id="visible-notes-count" class="font-bold text-gray-900">{{ count($notes) }}</span> catatan
        </div>
    </div>

    {{-- ── Daftar Catatan (Grid Card) ── --}}
    <div id="notes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($notes as $note)
            <div class="note-card bg-surface rounded-2xl border border-gray-200 p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all flex flex-col justify-between group"
                 id="note-card-{{ $note->id }}"
                 data-title="{{ strtolower($note->judul) }}"
                 data-content="{{ strtolower($note->catatan) }}">
                
                <div>
                    {{-- Card Header: Icon, Judul, Action Buttons --}}
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-emerald-900 transition-colors leading-snug">
                                    {{ $note->judul }}
                                </h3>
                                <span class="text-[10px] text-gray-400 mt-0.5 block">
                                    {{ $note->updated_at ? $note->updated_at->diffForHumans() : $note->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Action Buttons (Edit & Delete) --}}
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    onclick="openEditNoteModal({{ json_encode($note) }})"
                                    title="Edit Catatan"
                                    class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </button>

                            <button type="button"
                                    onclick="confirmDeleteNote({{ $note->id }}, '{{ addslashes($note->judul) }}')"
                                    title="Hapus Catatan"
                                    class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Isi Catatan --}}
                    <div class="text-xs sm:text-sm text-gray-700 mt-3.5 leading-relaxed whitespace-pre-line">
                        {!! nl2br(e($note->catatan)) !!}
                    </div>
                </div>

                {{-- Card Footer --}}
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
        @empty
            <div id="empty-notes-placeholder" class="col-span-full py-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.53-4h7.5m-7.5-4h3.75m-6.75-6.75H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-gray-900">Belum Ada Catatan Penting</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tambahkan instruksi teknis, aturan semprot, atau panduan khusus untuk membantu budidaya tanaman Anda.</p>
                <button type="button"
                        onclick="openAddNoteModal()"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    + Buat Catatan Pertama
                </button>
            </div>
        @endforelse

        {{-- Pesan Hasil Pencarian Tidak Ditemukan --}}
        <div id="no-search-results" class="hidden col-span-full py-10 text-center bg-gray-50 rounded-2xl border border-gray-200">
            <p class="text-sm font-semibold text-gray-700">Tidak ada catatan yang sesuai dengan kata kunci.</p>
            <button type="button" onclick="clearSearchNotes()" class="mt-2 text-xs font-bold text-emerald-600 hover:underline">
                Reset Pencarian
            </button>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1: TAMBAH CATATAN PENTING                              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-add-note" class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-gray-200 max-h-[92vh] flex flex-col overflow-hidden">
        {{-- Header Modal (Fixed / Diam tidak ikut ter-scroll) --}}
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tambah Catatan Penting</h3>
                <p class="text-xs text-gray-500 mt-0.5">Simpan panduan, aturan semprot, atau anjuran budidaya</p>
            </div>
            <button type="button"
                    onclick="closeModal('modal-add-note')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer"
                    title="Tutup">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form & Body --}}
        <form action="{{ route('steps.catatan.store') }}" method="POST" class="flex-1 flex flex-col min-h-0">
            @csrf

            <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-4 space-y-4">
                {{-- Judul Catatan --}}
                <div>
                    <label for="add-note-judul" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Judul Catatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="add-note-judul"
                           name="judul"
                           required
                           placeholder="Contoh: Waktu Terbaik Aplikasi Fungisida Kontak"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                </div>

                {{-- Isi Catatan --}}
                <div>
                    <label for="add-note-catatan" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Isi Catatan / Panduan Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="add-note-catatan"
                              name="catatan"
                              rows="6"
                              required
                              placeholder="Tuliskan catatan teknis, dosis kehati-hatian, petunjuk cuaca, atau SOP terkait..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs leading-relaxed"></textarea>
                </div>
            </div>

            {{-- Footer Modal (Fixed / Pinned) --}}
            <div class="flex items-center justify-end gap-2 px-5 sm:px-6 py-3.5 border-t border-gray-100 bg-gray-50/90 shrink-0">
                <button type="button"
                        onclick="closeModal('modal-add-note')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                    Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2: EDIT CATATAN PENTING                                --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modal-edit-note" class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-gray-200 max-h-[92vh] flex flex-col overflow-hidden">
        {{-- Header Modal (Fixed / Diam tidak ikut ter-scroll) --}}
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-900">Edit Catatan Penting</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi atau instruksi budidaya</p>
            </div>
            <button type="button"
                    onclick="closeModal('modal-edit-note')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer"
                    title="Tutup">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form & Body --}}
        <form id="form-edit-note" action="" method="POST" class="flex-1 flex flex-col min-h-0">
            @csrf
            @method('PUT')

            <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-4 space-y-4">
                {{-- Judul Catatan --}}
                <div>
                    <label for="edit-note-judul" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Judul Catatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="edit-note-judul"
                           name="judul"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                </div>

                {{-- Isi Catatan --}}
                <div>
                    <label for="edit-note-catatan" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Isi Catatan / Panduan Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="edit-note-catatan"
                              name="catatan"
                              rows="6"
                              required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm text-gray-900 bg-white placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs leading-relaxed"></textarea>
                </div>
            </div>

            {{-- Footer Modal (Fixed / Pinned) --}}
            <div class="flex items-center justify-end gap-2 px-5 sm:px-6 py-3.5 border-t border-gray-100 bg-gray-50/90 shrink-0">
                <button type="button"
                        onclick="closeModal('modal-edit-note')"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Hidden Form for Delete ── --}}
<form id="form-delete-note" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
            const scrollBody = m.querySelector('.overflow-y-auto');
            if (scrollBody) scrollBody.scrollTop = 0;
        }
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    }

    function openAddNoteModal() {
        const form = document.querySelector('#modal-add-note form');
        if (form) form.reset();
        openModal('modal-add-note');
        setTimeout(() => {
            document.getElementById('add-note-judul')?.focus();
        }, 100);
    }

    function openEditNoteModal(note) {
        if (!note) return;
        const form = document.getElementById('form-edit-note');
        if (!form) return;

        form.action = `/steps/catatan/${note.id}`;
        document.getElementById('edit-note-judul').value = note.judul || '';
        document.getElementById('edit-note-catatan').value = note.catatan || '';

        openModal('modal-edit-note');
        setTimeout(() => {
            document.getElementById('edit-note-judul')?.focus();
        }, 100);
    }

    function confirmDeleteNote(id, title) {
        const form = document.getElementById('form-delete-note');
        if (!form) return;

        form.action = `/steps/catatan/${id}`;

        if (window.AgriSwal && typeof window.AgriSwal.confirmDelete === 'function') {
            window.AgriSwal.confirmDelete(
                'Hapus Catatan Ini?',
                title,
                () => form.submit()
            );
            return;
        }

        if (confirm(`Hapus catatan "${title}"?`)) {
            form.submit();
        }
    }

    function filterNotesList(query) {
        const q = (query || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.note-card');
        const clearBtn = document.getElementById('btn-clear-search');
        const noResults = document.getElementById('no-search-results');
        const counter = document.getElementById('visible-notes-count');

        if (clearBtn) {
            if (q.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        let visibleCount = 0;
        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const content = card.getAttribute('data-content') || '';
            const isMatch = !q || title.includes(q) || content.includes(q);

            if (isMatch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (counter) counter.textContent = visibleCount;

        if (noResults) {
            if (visibleCount === 0 && cards.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    function clearSearchNotes() {
        const input = document.getElementById('search-notes-input');
        if (input) {
            input.value = '';
            filterNotesList('');
            input.focus();
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection
