@if ($paginator->hasPages() || $paginator->total() > 0)
    <div role="navigation" aria-label="Navegación de paginación" class="fb-pagination">
        @if ($paginator->onFirstPage())
            <span class="fb-page-btn fb-page-first fb-page-disabled" aria-disabled="true">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="fb-page-btn fb-page-first">Anterior</a>
        @endif

        @if ($paginator->hasPages())
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="fb-page-btn fb-page-num fb-page-ellipsis" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="fb-page-btn fb-page-num" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        @else
            <span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">1</span>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="fb-page-btn fb-page-last">Siguiente</a>
        @else
            <span class="fb-page-btn fb-page-last fb-page-disabled" aria-disabled="true">Siguiente</span>
        @endif
    </div>
@endif
