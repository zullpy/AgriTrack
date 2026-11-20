@extends('layouts.app')

@section('title', 'Konfigurasi Fonnte')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center gap-2.5 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm flex items-center gap-2.5 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-2.5 shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <span class="font-medium block mb-1">Periksa kembali formulir:</span>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-amber-800">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Konfigurasi Fonnte --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-7">
        <div class="pb-4 mb-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900">Konfigurasi Fonnte</h1>
                <p class="text-xs text-text-muted mt-0.5">Pengaturan akun WhatsApp gateway Fonnte.</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.312l-.72 2.628 2.688-.705c1.015.556 1.78.859 2.775.859h.001c3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.769-5.766zm3.376 8.21c-.14.394-.716.728-1.002.776-.285.048-.567.073-1.802-.436-1.584-.652-2.614-2.264-2.693-2.369-.079-.105-.642-.855-.642-1.632 0-.776.406-1.158.55-1.316.144-.158.314-.197.419-.197.105 0 .21 0 .301.006.096.006.223-.037.349.266.126.303.432 1.05.47 1.127.038.077.063.167.012.268-.051.101-.077.163-.153.251-.076.088-.161.196-.23.264-.078.076-.159.158-.068.314.091.156.405.669.868 1.082.597.531 1.1.695 1.256.772.156.077.248.064.34-.041.092-.105.394-.458.5-.615.105-.157.21-.131.352-.079.141.052.898.423 1.052.5.154.077.256.115.294.18.038.065.038.378-.102.772z"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.98-1.306A9.957 9.957 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.2c-1.63 0-3.14-.49-4.4-1.33l-.316-.21-2.96.776.79-2.886-.233-.37C4.46 14.88 4 13.49 4 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/>
                </svg>
            </div>
        </div>

        <form id="form-fonnte-config" method="POST" action="{{ route('fonnte.config.update') }}" onsubmit="saveFonnteSettings(event)" class="space-y-4">
            @csrf

            {{-- Token Fonnte --}}
            <div>
                <label for="fonnte-token" class="block text-xs font-semibold text-gray-800 mb-1.5">
                    Token Fonnte <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="fonnte-token" name="token"
                           value="{{ old('token', $config['token']) }}"
                           placeholder="Masukkan token perangkat Fonnte..."
                           autocomplete="off"
                           required
                           class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-xs font-mono text-gray-900 transition-all">
                    <button type="button" onclick="toggleTokenVisibility()"
                            title="Tampilkan / Sembunyikan Token"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer">
                        <svg id="token-eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Nomor WhatsApp Tujuan --}}
            <div>
                <label for="fonnte-target-phone" class="block text-xs font-semibold text-gray-800 mb-1.5">
                    Nomor WhatsApp Tujuan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="fonnte-target-phone" name="target_phone"
                       value="{{ old('target_phone', $config['target_phone']) }}"
                       placeholder="Contoh: 08123456789 atau 628123456789"
                       required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-xs text-gray-900 transition-all">
            </div>

            {{-- Toggle Aktifkan Notifikasi Otomatis --}}
            <div class="pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="fonnte-enabled" name="enabled" value="1"
                           {{ old('enabled', $config['enabled']) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary cursor-pointer">
                    <span class="text-xs font-medium text-gray-800">Aktifkan Pengiriman Notifikasi Otomatis</span>
                </label>
            </div>

            {{-- Webhook URL --}}
            <div class="pt-2">
                <label for="fonnte-webhook-url" class="block text-xs font-semibold text-gray-800 mb-1.5">
                    Webhook URL (Fonnte)
                </label>
                <div class="flex items-center gap-1.5">
                    <input type="text" id="fonnte-webhook-url" readonly value="{{ $webhookUrl }}"
                           class="w-full bg-gray-50 px-3 py-2 rounded-xl border border-gray-200 text-xs font-mono text-gray-600 select-all focus:outline-none">
                    <button type="button" onclick="copyWebhookUrl()" id="btn-copy-webhook"
                            class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-700 border border-gray-200 rounded-xl text-xs font-semibold cursor-pointer shrink-0 transition-all">
                        Salin
                    </button>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" id="btn-save-fonnte"
                        class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs sm:text-sm font-semibold shadow-sm transition-all inline-flex items-center gap-2 cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Konfigurasi</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleTokenVisibility() {
        const input = document.getElementById('fonnte-token');
        const icon = document.getElementById('token-eye-icon');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }
    }

    function copyWebhookUrl() {
        const input = document.getElementById('fonnte-webhook-url');
        if (!input) return;
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            if (window.AgriSwal) {
                window.AgriSwal.toastSuccess('URL Webhook berhasil disalin!');
            } else if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'URL Webhook berhasil disalin!',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else if (window.AgriSwal) {
                window.AgriSwal.toastSuccess('URL Webhook berhasil disalin!');
            }
        });
    }

    function saveFonnteSettings(e) {
        e.preventDefault();
        const token = document.getElementById('fonnte-token')?.value.trim() || '';
        const target_phone = document.getElementById('fonnte-target-phone')?.value.trim() || '';
        const enabled = document.getElementById('fonnte-enabled')?.checked ? 1 : 0;
        const btn = document.getElementById('btn-save-fonnte');

        if (!token) {
            if (window.AgriSwal && typeof window.AgriSwal.toastError === 'function') {
                window.AgriSwal.toastError('Token Fonnte tidak boleh kosong.');
            }
            return;
        }

        if (!target_phone) {
            if (window.AgriSwal && typeof window.AgriSwal.toastError === 'function') {
                window.AgriSwal.toastError('Nomor WhatsApp tujuan tidak boleh kosong.');
            }
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyimpan...</span>`;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('{{ route("fonnte.config.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token, target_phone, enabled })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span>Simpan Konfigurasi</span>`;
            }
            if (data.success) {
                if (window.AgriSwal) {
                    window.AgriSwal.toastSuccess(data.message || 'Konfigurasi berhasil disimpan!');
                } else if (window.Swal) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message });
                }
            } else {
                if (window.AgriSwal) {
                    window.AgriSwal.toastError(data.message || 'Gagal menyimpan konfigurasi.');
                }
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span>Simpan Konfigurasi</span>`;
            }
            if (window.AgriSwal) {
                window.AgriSwal.toastError('Terjadi kesalahan saat menyimpan konfigurasi.');
            }
        });
    }
</script>
@endpush
