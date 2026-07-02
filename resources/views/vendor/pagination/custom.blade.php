@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="Navegacion de paginacion" class="app-pagination">
        @if ($paginator->onFirstPage())
            <span class="app-page-item is-disabled" aria-hidden="true">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="app-page-item" aria-label="Pagina anterior">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
        @endif

        @if ($paginator->hasPages())
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="app-page-ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="app-page-item is-active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="app-page-item">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        @else
            <span aria-current="page" class="app-page-item is-active">1</span>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="app-page-item" aria-label="Pagina siguiente">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        @else
            <span class="app-page-item is-disabled" aria-hidden="true">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </span>
        @endif
    </nav>
@endif
