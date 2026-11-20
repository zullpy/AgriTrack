<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgriTrack — @yield('title', 'Data Obat')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2FB344">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="AgriTrack">
    <link rel="apple-touch-icon" href="/images/icon.png">
    {{-- Dexie.js for Offline-First IndexedDB Client Persistence --}}
    <script src="https://cdn.jsdelivr.net/npm/dexie@4.0.11/dist/dexie.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Sidebar active glow */
        .nav-active {
            background: #D6F5E3;
            box-shadow: 0 2px 8px rgba(47, 179, 68, 0.15);
        }
        /* Sidebar header */
        .sidebar-brand {
            background: #1F7A3D;
        }
        /* Page scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1dbd7; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #adbdb6; }

        /* Input focus ring override */
        .field-input:focus {
            outline: none;
            border-color: #2FB344;
            box-shadow: 0 0 0 3px rgba(47,179,68,0.12);
        }

        /* Smooth card entrance */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slideUp 0.35s ease both; }

        /* SweetAlert2 AgriTrack Design System */
        /* Modal Backdrop ONLY (never for toasts) */
        body:not(.swal2-toast-shown) .swal2-container.swal2-backdrop-show {
            backdrop-filter: blur(4px) !important;
            background: rgba(18, 38, 26, 0.45) !important;
        }

        /* Toast Container must be completely transparent without backdrop */
        body.swal2-toast-shown .swal2-container,
        .swal2-container:has(.swal2-toast) {
            background: transparent !important;
            backdrop-filter: none !important;
            pointer-events: none !important;
        }

        /* Toast Container Positioning */
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top {
            top: 16px !important;
            right: 16px !important;
            left: auto !important;
            bottom: auto !important;
            padding: 0 !important;
            z-index: 99999 !important;
        }

        @media (max-width: 640px) {
            .swal2-container.swal2-top-end,
            .swal2-container.swal2-top {
                top: 12px !important;
                right: 12px !important;
                left: 12px !important;
                width: calc(100% - 24px) !important;
                display: flex !important;
                justify-content: center !important;
            }
        }

        /* Modal Dialog */
        .swal2-popup.agri-swal-popup {
            border-radius: 20px !important;
            padding: 24px 22px 22px !important;
            font-family: 'Inter', sans-serif !important;
            box-shadow: 0 25px 50px -12px rgba(15, 45, 25, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.04) !important;
            border: none !important;
            background: #ffffff !important;
            max-width: 420px !important;
        }
        .swal2-title.agri-swal-title {
            font-family: 'Inter', sans-serif !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #1E2A24 !important;
            padding: 0.25rem 0 0 !important;
            line-height: 1.35 !important;
        }
        .swal2-html-container.agri-swal-html {
            font-size: 0.875rem !important;
            color: #4A5B53 !important;
            line-height: 1.55 !important;
            margin: 0.5rem 0 1.25rem !important;
        }
        .agri-swal-subtitle {
            font-size: 0.8125rem;
            color: #8A9A94;
            display: inline-block;
            margin-top: 0.35rem;
        }
        .swal2-icon.swal2-warning.agri-swal-icon-warning {
            border-color: #FED7AA !important;
            color: #E4574C !important;
            background: #FEF2F2 !important;
            width: 60px !important;
            height: 60px !important;
            margin: 0.5rem auto 1rem !important;
        }
        .swal2-icon.swal2-warning.agri-swal-icon-warning .swal2-icon-content {
            font-size: 32px !important;
            color: #E4574C !important;
            font-weight: 600 !important;
        }
        .swal2-icon.swal2-question.agri-swal-icon-question {
            border-color: #A7F3D0 !important;
            color: #059669 !important;
            background: #ECFDF5 !important;
            width: 60px !important;
            height: 60px !important;
            margin: 0.5rem auto 1rem !important;
            border-width: 3px !important;
        }
        .swal2-icon.swal2-question.agri-swal-icon-question .swal2-icon-content {
            font-size: 32px !important;
            color: #059669 !important;
            font-weight: 700 !important;
            font-family: 'Inter', sans-serif !important;
        }
        .agri-swal-actions {
            display: flex !important;
            gap: 8px !important;
            width: 100% !important;
            margin-top: 0.85rem !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .agri-swal-btn-primary {
            flex: 1.25 1 auto !important;
            background-color: #059669 !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.28) !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }
        .agri-swal-btn-primary:hover {
            background-color: #047857 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35) !important;
        }
        .agri-swal-btn-primary:active {
            transform: scale(0.97) !important;
        }
        .agri-swal-btn-secondary {
            flex: 1 1 auto !important;
            background-color: #ECFDF5 !important;
            color: #065F46 !important;
            border: 1.5px solid #A7F3D0 !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }
        .agri-swal-btn-secondary:hover {
            background-color: #D1FAE5 !important;
            border-color: #6EE7B7 !important;
            color: #064E3B !important;
            transform: translateY(-1px) !important;
        }
        .agri-swal-btn-secondary:active {
            transform: scale(0.97) !important;
        }
        .agri-swal-btn-danger {
            flex: 1 !important;
            background-color: #E4574C !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            padding: 10px 18px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 12px rgba(228, 87, 76, 0.28) !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .agri-swal-btn-danger:hover {
            background-color: #cf4338 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(228, 87, 76, 0.35) !important;
        }
        .agri-swal-btn-danger:active {
            transform: scale(0.97) !important;
        }
        .agri-swal-btn-cancel {
            flex: 0.85 1 auto !important;
            background-color: #F3F5F4 !important;
            color: #4A5B53 !important;
            border: 1px solid #E2E8E5 !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }
        .agri-swal-btn-cancel:hover {
            background-color: #E7ECE9 !important;
            color: #1E2A24 !important;
        }
        .agri-swal-btn-cancel:active {
            transform: scale(0.97) !important;
        }

        /* ── Responsive Mobile SweetAlert: Compact & Proporsional ── */
        @media (max-width: 640px) {
            .swal2-popup.agri-swal-popup {
                width: 88% !important;
                max-width: 320px !important;
                border-radius: 18px !important;
                padding: 16px 14px 14px !important;
            }
            .swal2-popup.agri-swal-popup.agri-swal-popup-wide {
                max-width: 380px !important;
            }
            .swal2-title.agri-swal-title {
                font-size: 1.05rem !important;
                line-height: 1.3 !important;
                padding-top: 0.1rem !important;
            }
            .swal2-html-container.agri-swal-html {
                font-size: 0.8rem !important;
                line-height: 1.45 !important;
                margin: 0.35rem 0 0.85rem !important;
            }
            .agri-swal-subtitle {
                font-size: 0.75rem !important;
                margin-top: 0.2rem !important;
            }
            .swal2-icon.swal2-warning.agri-swal-icon-warning,
            .swal2-icon.swal2-question.agri-swal-icon-question {
                width: 44px !important;
                height: 44px !important;
                margin: 0.2rem auto 0.5rem !important;
                border-width: 2.5px !important;
            }
            .swal2-icon.swal2-warning.agri-swal-icon-warning .swal2-icon-content,
            .swal2-icon.swal2-question.agri-swal-icon-question .swal2-icon-content {
                font-size: 22px !important;
            }
            .agri-swal-actions {
                gap: 6px !important;
                margin-top: 0.5rem !important;
            }
            .agri-swal-btn-primary,
            .agri-swal-btn-secondary,
            .agri-swal-btn-danger,
            .agri-swal-btn-cancel {
                padding: 8px 10px !important;
                font-size: 0.75rem !important;
                border-radius: 10px !important;
            }
        }

        /* ── Compact Modern Toast ── */
        .swal2-popup.swal2-toast {
            position: relative !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            flex-direction: row !important;
            width: auto !important;
            min-width: 280px !important;
            max-width: 440px !important;
            padding: 12px 18px !important;
            border-radius: 14px !important;
            background: #ffffff !important;
            box-shadow: 0 10px 30px -4px rgba(15, 45, 25, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.06) !important;
            border: none !important;
            border-left: 4px solid #2FB344 !important;
            pointer-events: auto !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        .swal2-popup.swal2-toast.agri-swal-toast-error {
            border-left: 4px solid #E4574C !important;
        }
        .swal2-popup.swal2-toast .swal2-icon {
            margin: 0 12px 0 0 !important;
            transform: scale(0.65) !important;
            transform-origin: center center !important;
            flex-shrink: 0 !important;
        }
        .swal2-popup.swal2-toast .swal2-title.agri-swal-toast-title,
        .swal2-popup.swal2-toast .swal2-title {
            color: #1E2A24 !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.4 !important;
            text-align: left !important;
            flex: 1 1 auto !important;
        }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar-container,
        .swal2-popup.swal2-toast .swal2-timer-progress-bar {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 3.5px !important;
            background: #2FB344 !important;
        }
        .swal2-popup.swal2-toast.agri-swal-toast-error .swal2-timer-progress-bar {
            background: #E4574C !important;
        }
    </style>
</head>
<body class="bg-page font-sans text-text antialiased overflow-x-hidden w-full max-w-full">
    <div class="flex min-h-screen w-full max-w-full overflow-x-hidden">

        {{-- ── Sidebar (desktop) ── --}}
        <aside class="hidden lg:flex flex-col w-60 fixed inset-y-0 left-0 z-30 bg-surface border-r border-gray-100 shadow-[2px_0_16px_rgba(0,0,0,0.04)]">

            {{-- Brand header --}}
            <div class="sidebar-brand px-5 py-5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-inner">
                    <img src="/images/icon.png" alt="icon" class="w-full h-full rounded-xl object-cover">
                </div>
                <div>
                    <p class="text-[15px] font-semibold text-white leading-tight">AgriTrack</p>
                    <p class="text-[10px] text-green-200/80 font-medium tracking-wide uppercase">Farm Manager</p>
                </div>
            </div>

            {{-- Divider --}}
            <div class="mx-4 h-px bg-gray-100 mt-1"></div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5">
                <p class="px-3 text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-2">Menu</p>

                <a href="/dashboard"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('dashboard*') || request()->is('tanaman-katalog*') ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('dashboard*') || request()->is('tanaman-katalog*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z"/>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                    @if(request()->is('dashboard*') || request()->is('tanaman-katalog*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                {{-- 2. Obat Tanaman (with submenus: Data Obat & Stok Obat) --}}
                @php
                    $isObatActive = request()->is('data-obat*') || request()->is('stok-obat*');
                    $isDataObatPage = request()->is('data-obat*') && !request()->is('stok-obat*');
                    $isStokObatPage = request()->is('stok-obat*');
                @endphp
                <div class="space-y-1" id="obat-menu-wrapper">
                    <button type="button"
                            onclick="toggleObatSubmenu()"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer
                                   {{ $isObatActive ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg {{ $isObatActive ? 'bg-primary/10 text-primary-dark' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                                </svg>
                            </span>
                            <span>Obat Tanaman</span>
                        </div>
                        <svg id="obat-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $isObatActive ? 'rotate-180 text-primary' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="obat-submenu" class="pl-11 pr-2 space-y-1 {{ $isObatActive ? 'block' : 'hidden' }}">
                        <a href="/data-obat"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isDataObatPage ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isDataObatPage ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Data Obat</span>
                        </a>

                        <a href="/stok-obat"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isStokObatPage ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isStokObatPage ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Stok Obat</span>
                        </a>
                    </div>
                </div>

                {{-- 3. Tahapan (with submenus) --}}
                @php
                    $isCatatan = request()->is('steps/catatan*');
                    $isStepsActive = request()->is('steps*') && !$isCatatan;
                    $isFermentasiPupuk = request()->is('steps/fermentasi-pupuk*');
                    $isPengolahanTanah = request()->is('steps/pengolahan-tanah*');
                    $isPenanamanBibit = request()->is('steps/penanaman-bibit*');
                @endphp
                <div class="space-y-1" id="tahapan-menu-wrapper">
                    <button type="button"
                            onclick="toggleTahapanSubmenu()"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                                   {{ $isStepsActive ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg {{ $isStepsActive ? 'bg-emerald-100 text-primary-dark' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 20h3.5v-3.5h3.5v-3.5h3.5v-3.5H18V6" />
                                </svg>
                            </span>
                            <span>Tahapan</span>
                        </div>
                        <svg id="tahapan-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $isStepsActive ? 'rotate-180 text-primary' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="tahapan-submenu" class="pl-11 pr-2 space-y-1 {{ $isStepsActive ? 'block' : 'hidden' }}">
                        <a href="/steps/fermentasi-pupuk"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isFermentasiPupuk ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isFermentasiPupuk ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Tahapan Fermentasi Pupuk</span>
                        </a>

                        <a href="/steps/pengolahan-tanah"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isPengolahanTanah ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isPengolahanTanah ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Tahapan Pengolahan Tanah</span>
                        </a>

                        <a href="/steps/penanaman-bibit"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isPenanamanBibit ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isPenanamanBibit ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Tahapan Penanaman Bibit</span>
                        </a>
                    </div>
                </div>

                {{-- 4. Informasi Penting Pertanian --}}
                <a href="/steps/catatan"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $isCatatan ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ $isCatatan ? 'bg-primary/10 text-primary-dark' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.53-4h7.5m-7.5-4h3.75m-6.75-6.75H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                    <span>Informasi Penting</span>
                    @if($isCatatan)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                {{-- 5. Tanaman Aktif & Schedule (with submenus) --}}
                @php
                    $isTanamanAktifPage = request()->is('tanaman-aktif*');
                    $isKalenderHss = request()->is('kalender-hss*');
                    $isKalenderHst = request()->is('kalender-hst*') || (request()->is('kalender*') && !$isKalenderHss);
                    $isKalenderActive = $isTanamanAktifPage || $isKalenderHst || $isKalenderHss;
                @endphp
                <div class="space-y-1" id="kalender-menu-wrapper">
                    <button type="button"
                            onclick="toggleKalenderSubmenu()"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer
                                   {{ $isKalenderActive ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg {{ $isKalenderActive ? 'bg-emerald-100 text-primary-dark' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-68-76a12,12,0,1,1-12-12A12,12,0,0,1,140,132Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,132ZM96,172a12,12,0,1,1-12-12A12,12,0,0,1,96,172Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,140,172Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,172Z"></path>
                                </svg>
                            </span>
                            <span>Tanaman Aktif & Schedule</span>
                        </div>
                        <svg id="kalender-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $isKalenderActive ? 'rotate-180 text-primary' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="kalender-submenu" class="pl-11 pr-2 space-y-1 {{ $isKalenderActive ? 'block' : 'hidden' }}">
                        <a href="/tanaman-aktif"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isTanamanAktifPage ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isTanamanAktifPage ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Tanaman Aktif</span>
                        </a>

                        <a href="/kalender-hst"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isKalenderHst ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isKalenderHst ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>HST (Hari Setelah Tanam)</span>
                        </a>

                        <a href="/kalender-hss"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isKalenderHss ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isKalenderHss ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>HSS (Hari Setelah Semai)</span>
                        </a>
                    </div>
                </div>

                {{-- 6. Keuangan (with submenus) --}}
                @php
                    $isKeuanganActive = request()->is('keuangan*');
                    $activeKeuanganTab = request()->query('tab', 'riwayat');
                    $isKeuanganLaporan = $isKeuanganActive && $activeKeuanganTab === 'laporan';
                    $isKeuanganStatistik = $isKeuanganActive && $activeKeuanganTab === 'statistik';
                    $isKeuanganRiwayat = $isKeuanganActive && $activeKeuanganTab !== 'statistik' && $activeKeuanganTab !== 'laporan';
                @endphp
                <div class="space-y-1" id="keuangan-menu-wrapper">
                    <button type="button"
                            onclick="toggleKeuanganSubmenu()"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer
                                   {{ $isKeuanganActive ? 'nav-active text-primary-dark font-semibold' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg {{ $isKeuanganActive ? 'bg-emerald-100 text-primary-dark' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span>Keuangan</span>
                        </div>
                        <svg id="keuangan-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $isKeuanganActive ? 'rotate-180 text-primary' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="keuangan-submenu" class="pl-11 pr-2 space-y-1 {{ $isKeuanganActive ? 'block' : 'hidden' }}">
                        <a href="/keuangan?tab=riwayat"
                           id="sidebar-link-keuangan-riwayat"
                           onclick="if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('riwayat'); return false; }"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isKeuanganRiwayat ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isKeuanganRiwayat ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Riwayat</span>
                        </a>

                        <a href="/keuangan?tab=statistik"
                           id="sidebar-link-keuangan-statistik"
                           onclick="if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('statistik'); return false; }"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isKeuanganStatistik ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isKeuanganStatistik ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Statistik</span>
                        </a>

                        <a href="/keuangan?tab=laporan"
                           id="sidebar-link-keuangan-laporan"
                           onclick="if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('laporan'); return false; }"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors
                                  {{ $isKeuanganLaporan ? 'text-primary-dark font-semibold bg-emerald-100' : 'text-text-secondary hover:text-text hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isKeuanganLaporan ? 'bg-primary' : 'bg-gray-300' }}"></span>
                            <span>Laporan Keuangan</span>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Connection Indicator (Desktop) --}}
            <div class="p-3 border-t border-gray-100 mt-auto">
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-gray-50/80 border border-gray-100">
                    <span class="connection-badge inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                    </span>
                </div>
            </div>
        </aside>

        {{-- ── Main content ── --}}
        <div class="flex-1 lg:ml-60 flex flex-col min-h-screen min-w-0 w-full max-w-full overflow-x-hidden">

            {{-- Mobile header --}}
            <header class="lg:hidden sticky top-0 z-20 flex items-center justify-between px-4 py-3 bg-surface/95 backdrop-blur-sm border-b border-gray-100 shadow-sm w-full max-w-full">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-sm">
                        <img src="/images/icon.png" alt="icon" class="w-full h-full rounded-xl object-cover">
                    </div>
                    <span class="text-base font-semibold text-text">AgriTrack</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="connection-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-3.5 pt-3.5 sm:px-4 sm:pt-4 lg:px-8 lg:pt-8 pb-28 sm:pb-32 lg:pb-8 min-w-0 w-full max-w-full overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Backdrop Overlay when any mobile bottom sub-menu is open --}}
    <div id="mobile-submenu-backdrop"
         onclick="closeAllMobileSubmenus()"
         class="hidden fixed inset-0 bg-black/25 backdrop-blur-xs transition-opacity duration-200"
         style="z-index: 99980;"
         aria-hidden="true"></div>

    {{-- Variables for Mobile Navigation & Submenus --}}
    @php
        $isDashboard = request()->is('dashboard*') || request()->is('/') || request()->is('tanaman-katalog*');
        $isDataObat = request()->is('data-obat*') || request()->is('stok-obat*');
        $isDataObatPage = request()->is('data-obat*') && !request()->is('stok-obat*');
        $isStokObatPage = request()->is('stok-obat*');
        $isCatatanNav = request()->is('steps/catatan*');
        $isTahapan = request()->is('steps*') && !$isCatatanNav;
        $isFermentasiPupuk = request()->is('steps/fermentasi-pupuk*');
        $isPengolahanTanah = request()->is('steps/pengolahan-tanah*');
        $isPenanamanBibit = request()->is('steps/penanaman-bibit*');
        $isKalenderNav = request()->is('kalender*') || request()->is('kalender-hst*') || request()->is('kalender-hss*') || request()->is('tanaman-aktif*');
        $isTanamanAktifPage = request()->is('tanaman-aktif*');
        $isKalenderHss = request()->is('kalender-hss*');
        $isKalenderHst = request()->is('kalender-hst*') || (request()->is('kalender*') && !$isKalenderHss && !$isTanamanAktifPage);
        $isKeuangan = request()->is('keuangan*');
        $activeKeuanganTab = request()->query('tab', 'riwayat');
        $isKeuanganLaporan = $isKeuangan && $activeKeuanganTab === 'laporan';
        $isKeuanganStatistik = $isKeuangan && $activeKeuanganTab === 'statistik';
        $isKeuanganRiwayat = $isKeuangan && $activeKeuanganTab !== 'statistik' && $activeKeuanganTab !== 'laporan';
    @endphp

    {{-- ── Mobile bottom navigation ── --}}
    <nav id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 inset-x-0 w-full bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.04)]" style="width: 100%; left: 0; right: 0; z-index: 99985;">
        <div class="flex items-center justify-around w-full px-1 pt-1.5 pb-2 relative" style="width: 100%; display: flex;">
            {{-- 1. Dashboard (No submenu) --}}
            <a href="/dashboard"
               onclick="closeAllMobileSubmenus()"
               class="flex-1 min-w-0 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isDashboard ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isDashboard ? '2' : '1.8' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z"/>
                    </svg>
                </div>
                <div class="h-[22px] flex items-center justify-center text-center">
                    <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isDashboard ? 'font-semibold' : '' }}"
                          style="{{ $isDashboard ? 'color: #16a34a;' : 'color: #6b7280;' }}">Dashboard</span>
                </div>
            </a>

            {{-- 2. Data Obat / Stok (Has Submenu) --}}
            <div class="flex-1 min-w-0 flex flex-col items-center justify-center" style="flex: 1;">
                <button type="button"
                        id="mobile-btn-obat"
                        onclick="toggleMobileSubmenu('obat', event)"
                        class="w-full flex flex-col items-center justify-center py-1 transition-all focus:outline-none"
                        aria-label="Menu Obat Tanaman">
                    <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                         style="{{ $isDataObat ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isDataObat ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                        </svg>
                    </div>
                    <div class="h-[22px] flex items-center justify-center text-center gap-0.5">
                        <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isDataObat ? 'font-semibold' : '' }}"
                              style="{{ $isDataObat ? 'color: #16a34a;' : 'color: #6b7280;' }}">Obat</span>
                        <svg id="mobile-chevron-obat" class="w-2.5 h-2.5 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                    </div>
                </button>
            </div>

            {{-- 3. Tahapan (Has Submenu) --}}
            <div class="flex-1 min-w-0 flex flex-col items-center justify-center" style="flex: 1;">
                <button type="button"
                        id="mobile-btn-tahapan"
                        onclick="toggleMobileSubmenu('tahapan', event)"
                        class="w-full flex flex-col items-center justify-center py-1 transition-all focus:outline-none"
                        aria-label="Menu Tahapan">
                    <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                         style="{{ $isTahapan ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isTahapan ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20h3.5v-3.5h3.5v-3.5h3.5v-3.5H18V6" />
                        </svg>
                    </div>
                    <div class="h-[22px] flex items-center justify-center text-center gap-0.5">
                        <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isTahapan ? 'font-semibold' : '' }}"
                              style="{{ $isTahapan ? 'color: #16a34a;' : 'color: #6b7280;' }}">Tahapan</span>
                        <svg id="mobile-chevron-tahapan" class="w-2.5 h-2.5 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                    </div>
                </button>
            </div>

            {{-- 4. Informasi Penting (No submenu) --}}
            <a href="/steps/catatan"
               onclick="closeAllMobileSubmenus()"
               class="flex-1 min-w-0 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isCatatanNav ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isCatatanNav ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.53-4h7.5m-7.5-4h3.75m-6.75-6.75H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div class="h-[22px] flex items-center justify-center text-center">
                    <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isCatatanNav ? 'font-semibold' : '' }}"
                          style="{{ $isCatatanNav ? 'color: #16a34a;' : 'color: #6b7280;' }}">Informasi Penting</span>
                </div>
            </a>

            {{-- 5. Tanaman Aktif & Schedule (Has Submenu) --}}
            <div class="flex-1 min-w-0 flex flex-col items-center justify-center" style="flex: 1;">
                <button type="button"
                        id="mobile-btn-kalender"
                        onclick="toggleMobileSubmenu('kalender', event)"
                        class="w-full flex flex-col items-center justify-center py-1 transition-all focus:outline-none"
                        aria-label="Menu Tanaman Aktif & Schedule">
                    <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                         style="{{ $isKalenderNav ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isKalenderNav ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </div>
                    <div class="h-[22px] flex items-center justify-center text-center gap-0.5">
                        <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isKalenderNav ? 'font-semibold' : '' }}"
                              style="{{ $isKalenderNav ? 'color: #16a34a;' : 'color: #6b7280;' }}">Tanaman</span>
                        <svg id="mobile-chevron-kalender" class="w-2.5 h-2.5 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                    </div>
                </button>
            </div>

            {{-- 6. Keuangan (Has Submenu) --}}
            <div class="flex-1 min-w-0 flex flex-col items-center justify-center" style="flex: 1;">
                <button type="button"
                        id="mobile-btn-keuangan"
                        onclick="toggleMobileSubmenu('keuangan', event)"
                        class="w-full flex flex-col items-center justify-center py-1 transition-all focus:outline-none"
                        aria-label="Menu Keuangan">
                    <div class="px-2.5 py-1 rounded-full flex items-center justify-center transition-all"
                         style="{{ $isKeuangan ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isKeuangan ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="h-[22px] flex items-center justify-center text-center gap-0.5">
                        <span class="text-[9px] sm:text-[10px] font-medium leading-tight {{ $isKeuangan ? 'font-semibold' : '' }}"
                              style="{{ $isKeuangan ? 'color: #16a34a;' : 'color: #6b7280;' }}">Keuangan</span>
                        <svg id="mobile-chevron-keuangan" class="w-2.5 h-2.5 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </nav>

    {{-- ── Mobile Floating Submenus (Anchored dynamically to bottom navbar buttons) ── --}}
    <div id="mobile-submenus-portal" class="lg:hidden" style="position: relative; z-index: 99990;">
        {{-- Popover Submenu Obat --}}
        <div id="mobile-submenu-obat"
             onclick="event.stopPropagation()"
             class="hidden fixed bg-white/95 backdrop-blur-xl border border-gray-200/90 shadow-2xl rounded-2xl p-1.5 text-left transition-all"
             style="z-index: 99990; width: 220px; max-width: calc(100vw - 16px);"
             role="menu"
             aria-label="Sub-menu Obat">
            <div id="mobile-arrow-obat"
                 class="absolute w-3 h-3 bg-white border-b border-r border-gray-200/90 pointer-events-none transition-all"
                 style="bottom: -6px; width: 12px; height: 12px; transform: rotate(45deg); z-index: 99991;"></div>
            <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1 flex items-center justify-between">
                <span>Menu Obat</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="space-y-0.5">
                <a href="/data-obat"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isDataObatPage ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isDataObatPage ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                        </svg>
                    </div>
                    <span class="truncate">Data Obat</span>
                    @if($isDataObatPage)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/stok-obat"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isStokObatPage ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isStokObatPage ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <span class="truncate">Stok Obat</span>
                    @if($isStokObatPage)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Popover Submenu Tahapan --}}
        <div id="mobile-submenu-tahapan"
             onclick="event.stopPropagation()"
             class="hidden fixed bg-white/95 backdrop-blur-xl border border-gray-200/90 shadow-2xl rounded-2xl p-1.5 text-left transition-all"
             style="z-index: 99990; width: 220px; max-width: calc(100vw - 16px);"
             role="menu"
             aria-label="Sub-menu Tahapan Budidaya">
            <div id="mobile-arrow-tahapan"
                 class="absolute w-3 h-3 bg-white border-b border-r border-gray-200/90 pointer-events-none transition-all"
                 style="bottom: -6px; width: 12px; height: 12px; transform: rotate(45deg); z-index: 99991;"></div>
            <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1 flex items-center justify-between">
                <span>Tahapan Budidaya</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="space-y-0.5">
                <a href="/steps/fermentasi-pupuk"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isFermentasiPupuk ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isFermentasiPupuk ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                        </svg>
                    </div>
                    <span class="truncate">Fermentasi Pupuk</span>
                    @if($isFermentasiPupuk)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/steps/pengolahan-tanah"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isPengolahanTanah ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isPengolahanTanah ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21V3m0 0a9 9 0 018.716 6.747M12 3a9 9 0 00-8.716 6.747" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        </svg>
                    </div>
                    <span class="truncate">Pengolahan Tanah</span>
                    @if($isPengolahanTanah)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/steps/penanaman-bibit"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isPenanamanBibit ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isPenanamanBibit ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="truncate">Penanaman Bibit</span>
                    @if($isPenanamanBibit)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Popover Submenu Tanaman Aktif & Schedule --}}
        <div id="mobile-submenu-kalender"
             onclick="event.stopPropagation()"
             class="hidden fixed bg-white/95 backdrop-blur-xl border border-gray-200/90 shadow-2xl rounded-2xl p-1.5 text-left transition-all"
             style="z-index: 99990; width: 220px; max-width: calc(100vw - 16px);"
             role="menu"
             aria-label="Sub-menu Tanaman & Schedule">
            <div id="mobile-arrow-kalender"
                 class="absolute w-3 h-3 bg-white border-b border-r border-gray-200/90 pointer-events-none transition-all"
                 style="bottom: -6px; width: 12px; height: 12px; transform: rotate(45deg); z-index: 99991;"></div>
            <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1 flex items-center justify-between">
                <span>Tanaman &amp; Schedule</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="space-y-0.5">
                <a href="/tanaman-aktif"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isTanamanAktifPage ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isTanamanAktifPage ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z" />
                        </svg>
                    </div>
                    <span class="truncate">Tanaman Aktif</span>
                    @if($isTanamanAktifPage)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/kalender-hst"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isKalenderHst ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isKalenderHst ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <span class="truncate">HST (Setelah Tanam)</span>
                    @if($isKalenderHst)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/kalender-hss"
                   onclick="closeAllMobileSubmenus()"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isKalenderHss ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isKalenderHss ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="truncate">HSS (Setelah Semai)</span>
                    @if($isKalenderHss)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Popover Submenu Keuangan --}}
        <div id="mobile-submenu-keuangan"
             onclick="event.stopPropagation()"
             class="hidden fixed bg-white/95 backdrop-blur-xl border border-gray-200/90 shadow-2xl rounded-2xl p-1.5 text-left transition-all"
             style="z-index: 99990; width: 220px; max-width: calc(100vw - 16px);"
             role="menu"
             aria-label="Sub-menu Keuangan">
            <div id="mobile-arrow-keuangan"
                 class="absolute w-3 h-3 bg-white border-b border-r border-gray-200/90 pointer-events-none transition-all"
                 style="bottom: -6px; width: 12px; height: 12px; transform: rotate(45deg); z-index: 99991;"></div>
            <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1 flex items-center justify-between">
                <span>Menu Keuangan</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="space-y-0.5">
                <a href="/keuangan?tab=riwayat"
                   onclick="closeAllMobileSubmenus(); if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('riwayat'); return false; }"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isKeuanganRiwayat ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isKeuanganRiwayat ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="truncate">Riwayat Transaksi</span>
                    @if($isKeuanganRiwayat)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/keuangan?tab=statistik"
                   onclick="closeAllMobileSubmenus(); if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('statistik'); return false; }"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isKeuanganStatistik ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isKeuanganStatistik ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <span class="truncate">Statistik Keuangan</span>
                    @if($isKeuanganStatistik)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
                <a href="/keuangan?tab=laporan"
                   onclick="closeAllMobileSubmenus(); if (window.location.pathname === '/keuangan' && typeof setKeuanganSubmenu === 'function') { setKeuanganSubmenu('laporan'); return false; }"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isKeuanganLaporan ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-100 active:bg-gray-100' }}">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isKeuanganLaporan ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <span class="truncate">Laporan Keuangan</span>
                    @if($isKeuanganLaporan)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 CDN Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Ensure AgriSwal is available if bundled module runs later
        if (!window.AgriSwal && window.Swal) {
            window.AgriSwal = {
                confirmDelete: function(title, itemName, onConfirm) {
                    return Swal.fire({
                        title: title || 'Hapus Data?',
                        html: `Apakah Anda yakin ingin menghapus <strong>"${itemName}"</strong>?<br><span class="agri-swal-subtitle">Data yang dihapus tidak dapat dipulihkan kembali.</span>`,
                        icon: 'warning',
                        iconColor: '#E4574C',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'agri-swal-popup',
                            title: 'agri-swal-title',
                            htmlContainer: 'agri-swal-html',
                            confirmButton: 'agri-swal-btn-danger',
                            cancelButton: 'agri-swal-btn-cancel',
                            actions: 'agri-swal-actions',
                            icon: 'agri-swal-icon-warning'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed && typeof onConfirm === 'function') {
                            onConfirm();
                        }
                        return result;
                    });
                },

                toastSuccess: function(message) {
                    return Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        iconColor: '#2FB344',
                        title: message,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'agri-swal-toast',
                            title: 'agri-swal-toast-title',
                            timerProgressBar: 'agri-swal-progress'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                },

                toastError: function(message) {
                    return Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        iconColor: '#E4574C',
                        title: message,
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'agri-swal-toast agri-swal-toast-error',
                            title: 'agri-swal-toast-title'
                        }
                    });
                },

                alert: function(title, message, icon = 'info') {
                    const isError = icon === 'error';
                    const isSuccess = icon === 'success';
                    return Swal.fire({
                        title: title || (isError ? 'Terjadi Kesalahan' : (isSuccess ? 'Berhasil' : 'Pemberitahuan')),
                        html: message,
                        icon: icon,
                        iconColor: isError ? '#E4574C' : (isSuccess ? '#2FB344' : '#059669'),
                        confirmButtonText: 'Mengerti',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'agri-swal-popup',
                            title: 'agri-swal-title',
                            htmlContainer: 'agri-swal-html',
                            confirmButton: isError ? 'agri-swal-btn-danger' : 'agri-swal-btn-primary',
                            actions: 'agri-swal-actions'
                        }
                    });
                },

                alertSuccess: function(title, message) {
                    return this.alert(title, message, 'success');
                },

                alertError: function(title, message) {
                    return this.alert(title, message, 'error');
                },

                confirm: function(title, message, onConfirm, confirmText = 'Lanjutkan', isDanger = false) {
                    return Swal.fire({
                        title: title || 'Konfirmasi Tindakan',
                        html: message,
                        icon: isDanger ? 'warning' : 'question',
                        iconColor: isDanger ? '#E4574C' : '#059669',
                        showCancelButton: true,
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        buttonsStyling: false,
                        customClass: {
                            popup: 'agri-swal-popup',
                            title: 'agri-swal-title',
                            htmlContainer: 'agri-swal-html',
                            confirmButton: isDanger ? 'agri-swal-btn-danger' : 'agri-swal-btn-primary',
                            cancelButton: 'agri-swal-btn-cancel',
                            actions: 'agri-swal-actions',
                            icon: isDanger ? 'agri-swal-icon-warning' : 'agri-swal-icon-question'
                        }
                    }).then((result) => {
                        if (result.isConfirmed && typeof onConfirm === 'function') {
                            onConfirm();
                        }
                        return result;
                    });
                }
            };

            // Global native alert override to guarantee consistent SweetAlert theme across all scripts
            window.alert = function(message) {
                if (window.AgriSwal && typeof window.AgriSwal.toastInfo === 'function') {
                    window.AgriSwal.toastInfo(String(message));
                }
            };

            // Global listener for forms with data-confirm-delete
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('form[data-confirm-delete]');
                if (!form || form.dataset.confirmed === 'true') return;

                e.preventDefault();
                const itemName = form.dataset.confirmDelete || 'data ini';
                const title = form.dataset.confirmTitle || 'Hapus Data?';

                window.AgriSwal.confirmDelete(title, itemName, async function() {
                    const actionPath = (new URL(form.action, window.location.origin)).pathname;
                    const isDetailPage = form.hasAttribute('data-detail-redirect') ||
                        actionPath === window.location.pathname ||
                        (window.location.pathname.startsWith(actionPath) && window.location.pathname.length > actionPath.length);

                    if (isDetailPage) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                        return;
                    }

                    const csrfToken = form.querySelector('input[name="_token"]')?.value
                        || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        || '';

                    const formData = new FormData(form);
                    if (!formData.has('_method')) {
                        const methodInput = form.querySelector('input[name="_method"]');
                        if (methodInput) formData.append('_method', methodInput.value);
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });

                        const result = await response.json().catch(() => null);

                        if (response.ok && result && result.success !== false) {
                            if (window.AgriSwal) {
                                window.AgriSwal.toastSuccess(result.message || 'Data berhasil dihapus.');
                            }

                            const targetId = result.id || form.action.split('/').filter(Boolean).pop();
                            const possibleElements = [
                                document.getElementById(`medicine-card-${targetId}`),
                                document.getElementById(`medicine-row-${targetId}`),
                                document.getElementById(`crop-card-${targetId}`),
                                document.getElementById(`harvested-crop-card-${targetId}`),
                                document.getElementById(`activity-item-${targetId}`),
                                form.closest('tr'),
                                form.closest('.medicine-card'),
                                form.closest('.crop-card'),
                                form.closest('[data-item-card]')
                            ];

                            let removed = false;
                            possibleElements.forEach(el => {
                                if (el) {
                                    el.style.transition = 'all 0.3s ease-out';
                                    el.style.opacity = '0';
                                    el.style.transform = 'scale(0.95)';
                                    setTimeout(() => el.remove(), 300);
                                    removed = true;
                                }
                            });

                            if (!removed && result.redirect) {
                                window.location.href = result.redirect;
                            }
                        } else if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    } catch (err) {
                        console.warn('Network error during delete, fallback to form submit:', err);
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            });
        }

        // Auto-trigger Toast on Laravel Flash Sessions
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                if (window.AgriSwal) {
                    window.AgriSwal.toastSuccess(@json(session('success')));
                }
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                if (window.AgriSwal) {
                    window.AgriSwal.toastError(@json(session('error')));
                }
            });
        @endif

        // Toggle Obat Submenu in Desktop Sidebar
        window.toggleObatSubmenu = function() {
            const submenu = document.getElementById('obat-submenu');
            const chevron = document.getElementById('obat-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                    chevron.classList.toggle('text-primary');
                }
            }
        };

        // Toggle Tahapan Submenu in Desktop Sidebar
        window.toggleTahapanSubmenu = function() {
            const submenu = document.getElementById('tahapan-submenu');
            const chevron = document.getElementById('tahapan-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                    chevron.classList.toggle('text-primary');
                }
            }
        };

        // Toggle Kalender Submenu in Desktop Sidebar
        window.toggleKalenderSubmenu = function() {
            const submenu = document.getElementById('kalender-submenu');
            const chevron = document.getElementById('kalender-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                    chevron.classList.toggle('text-primary');
                }
            }
        };

        // Toggle Keuangan Submenu in Desktop Sidebar
        window.toggleKeuanganSubmenu = function() {
            const submenu = document.getElementById('keuangan-submenu');
            const chevron = document.getElementById('keuangan-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                    chevron.classList.toggle('text-primary');
                }
            }
        };

        // Mobile Bottom Navigation Submenus (Popover dynamically anchored to bottom nav buttons)
        const mobileSubmenuKeys = ['obat', 'tahapan', 'kalender', 'keuangan'];

        window.closeAllMobileSubmenus = function() {
            const backdrop = document.getElementById('mobile-submenu-backdrop');
            if (backdrop) backdrop.classList.add('hidden');

            mobileSubmenuKeys.forEach(key => {
                const submenu = document.getElementById(`mobile-submenu-${key}`);
                const chevron = document.getElementById(`mobile-chevron-${key}`);
                if (submenu) submenu.classList.add('hidden');
                if (chevron) {
                    chevron.classList.remove('rotate-180', 'text-emerald-600');
                    chevron.classList.add('text-gray-400');
                }
            });
        };

        window.positionMobileSubmenu = function(menuKey) {
            const popover = document.getElementById(`mobile-submenu-${menuKey}`);
            const button = document.getElementById(`mobile-btn-${menuKey}`);
            const arrow = document.getElementById(`mobile-arrow-${menuKey}`);
            const nav = document.getElementById('mobile-bottom-nav');
            if (!popover || !button) return;

            const btnRect = button.getBoundingClientRect();
            const buttonCenter = btnRect.left + (btnRect.width / 2);
            const viewportWidth = window.innerWidth;
            const popoverWidth = popover.offsetWidth || 220;

            // Clamped horizontal position with 8px edge safety margin
            const margin = 8;
            const idealLeft = buttonCenter - (popoverWidth / 2);
            const clampedLeft = Math.max(margin, Math.min(idealLeft, viewportWidth - popoverWidth - margin));

            popover.style.left = `${clampedLeft}px`;
            popover.style.right = 'auto';

            // Vertical position: reliably 8px above bottom navbar
            const navHeight = nav ? (nav.offsetHeight || Math.round(nav.getBoundingClientRect().height)) : 60;
            popover.style.bottom = `${navHeight + 8}px`;

            // Position pointer arrow directly aiming at button center
            if (arrow) {
                const arrowCenterInPopover = buttonCenter - clampedLeft;
                // Arrow diamond is 12px wide (w-3 h-3 rotated 45deg). Half width is 6px.
                // Keep clamped inside popover rounded border radius
                const arrowLeft = Math.max(16, Math.min(arrowCenterInPopover - 6, popoverWidth - 22));
                arrow.style.left = `${arrowLeft}px`;
                arrow.style.right = 'auto';
            }
        };

        window.toggleMobileSubmenu = function(menuKey, event) {
            const ev = event || window.event;
            if (ev && typeof ev.stopPropagation === 'function') {
                ev.stopPropagation();
            }
            const targetSubmenu = document.getElementById(`mobile-submenu-${menuKey}`);
            const targetChevron = document.getElementById(`mobile-chevron-${menuKey}`);
            if (!targetSubmenu) return;

            const isCurrentlyOpen = !targetSubmenu.classList.contains('hidden');

            // Close all first
            closeAllMobileSubmenus();

            // If it was closed, open it now and calculate its dynamic position
            if (!isCurrentlyOpen) {
                targetSubmenu.classList.remove('hidden');
                window.positionMobileSubmenu(menuKey);
                if (targetChevron) {
                    targetChevron.classList.add('rotate-180', 'text-emerald-600');
                    targetChevron.classList.remove('text-gray-400');
                }
                const backdrop = document.getElementById('mobile-submenu-backdrop');
                if (backdrop) backdrop.classList.remove('hidden');
            }
        };

        // Reposition currently open submenu on window resize or orientation change
        window.addEventListener('resize', function() {
            mobileSubmenuKeys.forEach(function(key) {
                const popover = document.getElementById(`mobile-submenu-${key}`);
                if (popover && !popover.classList.contains('hidden')) {
                    window.positionMobileSubmenu(key);
                }
            });
        });

        // Close mobile submenus on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllMobileSubmenus();
            }
        });

        // Close when clicking outside of nav and submenus
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#mobile-submenus-portal') && !e.target.closest('#mobile-bottom-nav')) {
                closeAllMobileSubmenus();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
