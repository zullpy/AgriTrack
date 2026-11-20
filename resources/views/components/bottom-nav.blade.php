@props([
    'tabs' => null,
    'actions' => null,
])

@php
    // Default 4 tabs if not provided via props
    $defaultTabs = [
        [
            'label' => 'Dashboard',
            'url' => route('dashboard'),
            'route' => ['dashboard*', 'tanaman-katalog*'],
            'pattern' => ['dashboard*', 'tanaman-katalog*'],
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Obat',
            'url' => url('/data-obat'),
            'route' => ['data-obat*'],
            'pattern' => ['data-obat*', 'stok-obat*'],
            'icon' => 'beaker',
        ],
        [
            'label' => 'Tanaman',
            'url' => route('tanaman-aktif.index'),
            'route' => ['tanaman-aktif*', 'kalender*'],
            'pattern' => ['tanaman-aktif*', 'kalender*'],
            'icon' => 'plant',
        ],
        [
            'label' => 'Keuangan',
            'url' => route('keuangan.index'),
            'route' => ['keuangan*'],
            'pattern' => ['keuangan*'],
            'icon' => 'wallet',
        ],
    ];

    $tabItems = $tabs ?? $defaultTabs;

    // Helper function to resolve active state via request()->routeIs() with pattern fallback
    $isTabActive = function ($tab) {
        if (isset($tab['active']) && is_bool($tab['active'])) {
            return $tab['active'];
        }

        if (!empty($tab['route'])) {
            $routes = (array) $tab['route'];
            foreach ($routes as $r) {
                if (request()->routeIs($r)) {
                    return true;
                }
            }
        }

        if (!empty($tab['pattern'])) {
            $patterns = (array) $tab['pattern'];
            foreach ($patterns as $p) {
                if (request()->is($p)) {
                    return true;
                }
            }
        }

        return false;
    };

    // Default quick actions for center submenu if not provided
    $defaultActions = [
        [
            'label' => 'Tambah Obat',
            'desc' => 'Daftarkan obat atau stok baru',
            'url' => url('/data-obat/tambah'),
            'icon' => 'plus-beaker',
            'color' => 'emerald',
        ],
        [
            'label' => 'Catat Keuangan',
            'desc' => 'Pemasukan & pengeluaran kebun',
            'url' => route('keuangan.index'),
            'icon' => 'currency',
            'color' => 'amber',
        ],
        [
            'label' => 'Mulai Tanam (HST)',
            'desc' => 'Jadwal tanaman aktif baru',
            'url' => route('kalender-hst.index'),
            'icon' => 'calendar',
            'color' => 'blue',
        ],
        [
            'label' => 'Tahapan Budidaya',
            'desc' => 'Pupuk, olah tanah, & bibit',
            'url' => route('steps.index'),
            'icon' => 'steps',
            'color' => 'teal',
        ],
        [
            'label' => 'Informasi Penting',
            'desc' => 'Catatan budidaya & lapangan',
            'url' => route('steps.catatan'),
            'icon' => 'notes',
            'color' => 'purple',
        ],
    ];

    $quickActions = $actions ?? $defaultActions;

    // Split 4 tabs into 2 left and 2 right
    $leftTabs = array_slice($tabItems, 0, 2);
    $rightTabs = array_slice($tabItems, 2, 2);
@endphp

<div x-data="{ open: false }"
     @keydown.escape.window="open = false"
     class="lg:hidden"
     {{ $attributes }}>

    {{-- Backdrop Overlay when Submenu is open --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-xs"
         aria-hidden="true"></div>

    {{-- Expandable Submenu Sheet --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         @click.outside="open = false"
         class="fixed inset-x-4 z-50 mx-auto max-w-md bg-[#161920]/95 backdrop-blur-xl border border-white/10 rounded-3xl p-4 sm:p-5 shadow-[0_20px_50px_rgba(0,0,0,0.6)]"
         style="bottom: calc(88px + env(safe-area-inset-bottom, 0px));"
         role="dialog"
         aria-modal="true"
         aria-label="Menu Aksi Cepat">

        {{-- Submenu Header --}}
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#2F6BFF] animate-pulse"></span>
                <span class="text-xs font-bold text-white tracking-wider uppercase">Aksi Cepat</span>
            </div>
            <button type="button"
                    @click="open = false"
                    class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer"
                    aria-label="Tutup menu">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Submenu Items --}}
        @if(isset($slot) && $slot->isNotEmpty())
            {{ $slot }}
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[60vh] overflow-y-auto pr-0.5">
                @foreach($quickActions as $act)
                    <a href="{{ $act['url'] }}"
                       @click="open = false"
                       class="group flex items-center gap-3 p-2.5 rounded-2xl bg-white/[0.04] hover:bg-white/[0.09] border border-white/[0.06] hover:border-white/[0.15] transition-all active:scale-[0.98]">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105
                            @if(($act['color'] ?? '') === 'emerald') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                            @elseif(($act['color'] ?? '') === 'amber') bg-amber-500/20 text-amber-400 border border-amber-500/30
                            @elseif(($act['color'] ?? '') === 'blue') bg-[#2F6BFF]/20 text-[#6898ff] border border-[#2F6BFF]/30
                            @elseif(($act['color'] ?? '') === 'teal') bg-teal-500/20 text-teal-400 border border-teal-500/30
                            @elseif(($act['color'] ?? '') === 'purple') bg-purple-500/20 text-purple-400 border border-purple-500/30
                            @else bg-gray-500/20 text-gray-300 border border-gray-500/30
                            @endif">
                            @if(($act['icon'] ?? '') === 'plus-beaker')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                                </svg>
                            @elseif(($act['icon'] ?? '') === 'currency')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif(($act['icon'] ?? '') === 'calendar')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            @elseif(($act['icon'] ?? '') === 'steps')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h3.5v-3.5h3.5v-3.5h3.5v-3.5H18V6" />
                                </svg>
                            @elseif(($act['icon'] ?? '') === 'notes')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-white group-hover:text-[#6898ff] transition-colors truncate">
                                {{ $act['label'] }}
                            </h4>
                            <p class="text-[11px] text-gray-400 leading-tight truncate mt-0.5">
                                {{ $act['desc'] }}
                            </p>
                        </div>
                        <span class="text-gray-500 group-hover:text-white transition-colors text-xs shrink-0">
                            →
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Floating Bottom Navigation Bar (Pill Shape) ── --}}
    <nav class="fixed inset-x-0 z-40 px-4 pointer-events-none"
         style="bottom: calc(16px + env(safe-area-inset-bottom, 0px));"
         aria-label="Navigasi Utama Bawah">

        <div class="pointer-events-auto mx-auto max-w-md w-full h-16 bg-[#111317] rounded-full shadow-[0_12px_36px_rgba(0,0,0,0.45)] border border-white/[0.08] flex items-center justify-between px-2 relative">

            {{-- Slot 1 & 2: Left Tabs --}}
            @foreach($leftTabs as $tab)
                @php $active = $isTabActive($tab); @endphp
                <a href="{{ $tab['url'] }}"
                   class="flex-1 flex flex-col items-center justify-center h-full py-1 group transition-all select-none relative focus:outline-none"
                   aria-current="{{ $active ? 'page' : 'false' }}">
                    <div class="transition-transform duration-150 group-active:scale-90 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-gray-200' }}">
                        @if(($tab['icon'] ?? '') === 'dashboard')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                        @elseif(($tab['icon'] ?? '') === 'beaker')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6M10 3v6.5L4.8 19.2A1.5 1.5 0 006.1 21h11.8a1.5 1.5 0 001.3-2.2L14 9.5V3" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z" />
                            </svg>
                        @endif
                    </div>
                    <span class="text-[10px] mt-0.5 tracking-tight transition-colors {{ $active ? 'text-white font-semibold' : 'text-gray-400 group-hover:text-gray-200 font-medium' }}">
                        {{ $tab['label'] }}
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full mt-0.5 transition-all duration-150 {{ $active ? 'bg-[#2F6BFF] scale-100' : 'bg-transparent scale-0' }}"></span>
                </a>
            @endforeach

            {{-- Slot 3: Center Action Button (+) --}}
            <div class="flex-1 flex items-center justify-center h-full px-1">
                <button type="button"
                        @click="open = !open"
                        :aria-expanded="open"
                        aria-label="Buka Menu Aksi Cepat"
                        class="w-12 h-12 rounded-full bg-[#2F6BFF] hover:bg-[#2558d4] text-white flex items-center justify-center shadow-lg shadow-[#2F6BFF]/35 active:scale-95 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#2F6BFF]/50">
                    <svg class="w-5 h-5 transition-transform duration-300 ease-out transform"
                         :class="open ? 'rotate-45' : 'rotate-0'"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>

            {{-- Slot 4 & 5: Right Tabs --}}
            @foreach($rightTabs as $tab)
                @php $active = $isTabActive($tab); @endphp
                <a href="{{ $tab['url'] }}"
                   class="flex-1 flex flex-col items-center justify-center h-full py-1 group transition-all select-none relative focus:outline-none"
                   aria-current="{{ $active ? 'page' : 'false' }}">
                    <div class="transition-transform duration-150 group-active:scale-90 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-gray-200' }}">
                        @if(($tab['icon'] ?? '') === 'plant')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8m0 0c0-3.866 3.134-7 7-7-1.5 4-4 7-7 7zm0 0c0-3.866-3.134-7-7-7 1.5 4 4 7 7 7z" />
                            </svg>
                        @elseif(($tab['icon'] ?? '') === 'wallet')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $active ? '2.2' : '1.8' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        @endif
                    </div>
                    <span class="text-[10px] mt-0.5 tracking-tight transition-colors {{ $active ? 'text-white font-semibold' : 'text-gray-400 group-hover:text-gray-200 font-medium' }}">
                        {{ $tab['label'] }}
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full mt-0.5 transition-all duration-150 {{ $active ? 'bg-[#2F6BFF] scale-100' : 'bg-transparent scale-0' }}"></span>
                </a>
            @endforeach

        </div>
    </nav>
</div>
