<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgriTrack — @yield('title', 'Data Obat')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    </style>
</head>
<body class="bg-page font-sans text-text antialiased">
    <div class="flex min-h-screen">

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

                <a href="/data-obat"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('data-obat*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('data-obat*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                        </svg>
                    </span>
                    <span>Data Obat Tanaman</span>
                    @if(request()->is('data-obat*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                <a href="/kalender-hst"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->is('kalender-hst*') ? 'nav-active text-primary-dark' : 'text-text-secondary hover:bg-gray-50 hover:text-text' }}">
                    <span class="w-8 h-8 rounded-lg {{ request()->is('kalender-hst*') ? 'bg-primary/10' : 'bg-gray-100' }} flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </span>
                    <span>Kalender HST</span>
                    @if(request()->is('kalender-hst*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>
            </nav>
        </aside>

        {{-- ── Main content ── --}}
        <div class="flex-1 lg:ml-60 flex flex-col min-h-screen">

            {{-- Mobile header --}}
            <header class="lg:hidden sticky top-0 z-20 flex items-center justify-between px-4 py-3.5 bg-surface/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-sm">
                        <img src="/images/icon.png" alt="icon" class="w-full h-full rounded-xl object-cover">
                    </div>
                    <span class="text-base font-semibold text-text">AgriTrack</span>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 lg:p-8 pb-24 lg:pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ── Mobile bottom navigation ── --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-surface/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-around px-2 py-1">
            <a href="/data-obat"
               class="flex flex-col items-center gap-0.5 px-4 py-2 rounded-xl transition-all
                      {{ request()->is('data-obat*') ? 'text-primary' : 'text-text-muted' }}">
                <div class="relative">
                    @if(request()->is('data-obat*'))
                        <div class="absolute -inset-1.5 rounded-xl bg-primary-light/70"></div>
                    @endif
                    <svg class="w-5 h-5 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold">Data Obat</span>
            </a>

            <a href="/kalender-hst"
               class="flex flex-col items-center gap-0.5 px-4 py-2 rounded-xl transition-all
                      {{ request()->is('kalender-hst*') ? 'text-primary' : 'text-text-muted' }}">
                <div class="relative">
                    @if(request()->is('kalender-hst*'))
                        <div class="absolute -inset-1.5 rounded-xl bg-primary-light/70"></div>
                    @endif
                    <svg class="w-5 h-5 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold">Kalender HST</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
