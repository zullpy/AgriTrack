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

        /* Bottom nav indicator */
        .bottom-nav-item.active .bottom-nav-dot {
            opacity: 1; transform: scaleX(1);
        }
        .bottom-nav-dot {
            opacity: 0; transform: scaleX(0.3);
            transition: all 0.2s ease;
        }

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
        .agri-swal-actions {
            display: flex !important;
            gap: 10px !important;
            width: 100% !important;
            margin-top: 0.75rem !important;
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
        .agri-swal-btn-cancel {
            flex: 1 !important;
            background-color: #F3F5F4 !important;
            color: #4A5B53 !important;
            border: 1px solid #E2E8E5 !important;
            border-radius: 12px !important;
            padding: 10px 18px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .agri-swal-btn-cancel:hover {
            background-color: #E7ECE9 !important;
            color: #1E2A24 !important;
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
                          {{ request()->is('dashboard*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('dashboard*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z"/>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                    @if(request()->is('dashboard*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                <a href="/data-obat"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('data-obat*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('data-obat*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                        </svg>
                    </span>
                    <span>Data Obat</span>
                    @if(request()->is('data-obat*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                {{-- 3. Tahapan (with submenus) --}}
                @php
                    $isStepsActive = request()->is('steps*');
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

                        <a href="/steps"
                           class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium text-text-muted hover:text-text transition-colors">
                            <span>Ikhtisar Tahapan</span>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- 4. Kalender HST --}}
                <a href="/kalender-hst"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('kalender-hst*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('kalender-hst*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-68-76a12,12,0,1,1-12-12A12,12,0,0,1,140,132Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,132ZM96,172a12,12,0,1,1-12-12A12,12,0,0,1,96,172Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,140,172Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,172Z"></path>
                        </svg>
                    </span>
                    <span>Kalender HST</span>
                    @if(request()->is('kalender-hst*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                {{-- 5. Keuangan --}}
                <a href="/keuangan"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('keuangan*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('keuangan*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span>Keuangan</span>
                    @if(request()->is('keuangan*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>
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
                <div class="connection-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-3.5 sm:p-4 lg:p-8 pb-24 lg:pb-8 min-w-0 w-full max-w-full overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ── Mobile bottom navigation ── --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 w-full z-30 bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.04)]" style="width: 100%; left: 0; right: 0;">
        <div class="flex items-center justify-around w-full px-1.5 pt-1.5 pb-2" style="width: 100%; display: flex;">
            {{-- 1. Dashboard --}}
            @php $isDashboard = request()->is('dashboard*') || request()->is('/'); @endphp
            <a href="/dashboard"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-3 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isDashboard ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isDashboard ? '2' : '1.8' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-0.5 {{ $isDashboard ? 'font-semibold' : '' }}"
                      style="{{ $isDashboard ? 'color: #16a34a;' : 'color: #6b7280;' }}">Dashboard</span>
            </a>

            {{-- 2. Data Obat --}}
            @php $isDataObat = request()->is('data-obat*'); @endphp
            <a href="/data-obat"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-3 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isDataObat ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isDataObat ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-0.5 {{ $isDataObat ? 'font-semibold' : '' }}"
                      style="{{ $isDataObat ? 'color: #16a34a;' : 'color: #6b7280;' }}">Obat</span>
            </a>

            {{-- 3. Tahapan --}}
            @php $isTahapan = request()->is('steps*'); @endphp
            <a href="/steps"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-3 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isTahapan ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isTahapan ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20h3.5v-3.5h3.5v-3.5h3.5v-3.5H18V6" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-0.5 {{ $isTahapan ? 'font-semibold' : '' }}"
                      style="{{ $isTahapan ? 'color: #16a34a;' : 'color: #6b7280;' }}">Tahapan</span>
            </a>

            <!-- 4. Kalender HST -->
            @php $isKalenderHst = request()->is('kalender-hst*'); @endphp
            <a href="/kalender-hst"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-3 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isKalenderHst ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isKalenderHst ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-0.5 {{ $isKalenderHst ? 'font-semibold' : '' }}"
                      style="{{ $isKalenderHst ? 'color: #16a34a;' : 'color: #6b7280;' }}">HST</span>
            </a>

            <!-- 5. Keuangan -->
            @php $isKeuangan = request()->is('keuangan*'); @endphp
            <a href="/keuangan"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-all"
               style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="px-3 py-1 rounded-full flex items-center justify-center transition-all"
                     style="{{ $isKeuangan ? 'background-color: #E1F7E8; color: #16a34a;' : 'color: #9ca3af;' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isKeuangan ? '2' : '1.8' }}" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-0.5 {{ $isKeuangan ? 'font-semibold' : '' }}"
                      style="{{ $isKeuangan ? 'color: #16a34a;' : 'color: #6b7280;' }}">Keuangan</span>
            </a>
        </div>
    </nav>

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
                }
            };

            // Global listener for forms with data-confirm-delete
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('form[data-confirm-delete]');
                if (!form || form.dataset.confirmed === 'true') return;

                e.preventDefault();
                const itemName = form.dataset.confirmDelete || 'data ini';
                const title = form.dataset.confirmTitle || 'Hapus Data Obat?';

                window.AgriSwal.confirmDelete(title, itemName, function() {
                    form.dataset.confirmed = 'true';
                    form.submit();
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
    </script>

    @stack('scripts')
</body>
</html>
