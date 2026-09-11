@props([
    'id',
    'titleId' => null,
    'clearId' => null,
    'clearHref' => null,
    'cancelId' => null,
    'applyId' => null,
    'applyType' => 'button',
    'title' => 'Filtros',
    'clearLabel' => 'Limpiar',
    'cancelLabel' => 'Cancelar',
    'applyLabel' => 'Aplicar filtros',
    'open' => false,
    'bodyClass' => '',
    'headerClass' => '',
    'clearClass' => '',
    'footerClass' => '',
    'cancelClass' => '',
    'applyClass' => '',
])

@php($resolvedTitleId = $titleId ?: $id . '-title')

<aside
    id="{{ $id }}"
    {{ $attributes->class(['users-filter-panel', 'users-filter-menu', 'is-collapsed' => !$open]) }}
    role="dialog"
    aria-modal="false"
    aria-labelledby="{{ $resolvedTitleId }}"
    data-filter-popover-panel
>
    <header class="users-filter-panel-header {{ $headerClass }}">
        <h2 id="{{ $resolvedTitleId }}">{{ $title }}</h2>
        @if($clearHref)
            <a href="{{ $clearHref }}" class="users-filter-clear {{ $clearClass }}" @if($clearId) id="{{ $clearId }}" @endif>{{ $clearLabel }}</a>
        @else
            <button type="button" class="users-filter-clear {{ $clearClass }}" @if($clearId) id="{{ $clearId }}" @endif>{{ $clearLabel }}</button>
        @endif
    </header>

    {{ $beforeBody ?? '' }}

    <div class="users-filter-panel-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @isset($footer)
        {{ $footer }}
    @else
        <footer class="users-filter-panel-footer {{ $footerClass }}">
            <button type="button" class="users-filter-secondary {{ $cancelClass }}" @if($cancelId) id="{{ $cancelId }}" @endif>{{ $cancelLabel }}</button>
            <button type="{{ $applyType }}" class="users-filter-apply {{ $applyClass }}" @if($applyId) id="{{ $applyId }}" @endif>{{ $applyLabel }}</button>
        </footer>
    @endisset
</aside>
