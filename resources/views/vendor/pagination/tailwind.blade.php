@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3.5 pt-2">
        {{-- Mobile pagination control --}}
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 text-xs font-semibold cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50/50 text-xs font-semibold shadow-xs transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            <span class="text-xs font-medium text-gray-500">
                Hal. <span class="font-bold text-gray-800">{{ $paginator->currentPage() }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->lastPage() }}</span>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50/50 text-xs font-semibold shadow-xs transition-all active:scale-95">
                    <span>Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 text-xs font-semibold cursor-not-allowed select-none">
                    <span>Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop pagination control --}}
        <div class="hidden sm:flex items-center justify-between w-full">
            <div>
                <p class="text-xs sm:text-sm text-gray-500">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-gray-900">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-semibold text-gray-900">{{ $paginator->count() }}</span>
                    @endif
                    dari
                    <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <ul class="inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li aria-disabled="true">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed select-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50/50 shadow-xs transition-all active:scale-95"
                               title="Halaman Sebelumnya">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-xs text-gray-400 font-semibold select-none">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-xs select-none">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}"
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50/50 text-xs font-semibold shadow-xs transition-all active:scale-95">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50/50 shadow-xs transition-all active:scale-95"
                               title="Halaman Selanjutnya">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </li>
                    @else
                        <li aria-disabled="true">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed select-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
