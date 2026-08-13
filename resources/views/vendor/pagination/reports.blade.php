@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="Navegación de paginación" class="reports-pagination">
        @if ($paginator->onFirstPage())
            <span class="reports-page-item reports-page-edge is-disabled" aria-disabled="true">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="reports-page-item reports-page-edge">Anterior</a>
        @endif

        @if ($paginator->hasPages())
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="reports-page-item reports-page-ellipsis" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="reports-page-item reports-page-number is-active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="reports-page-item reports-page-number" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        @else
            <span aria-current="page" class="reports-page-item reports-page-number is-active">1</span>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="reports-page-item reports-page-edge">Siguiente</a>
        @else
            <span class="reports-page-item reports-page-edge is-disabled" aria-disabled="true">Siguiente</span>
        @endif
    </nav>
@endif
