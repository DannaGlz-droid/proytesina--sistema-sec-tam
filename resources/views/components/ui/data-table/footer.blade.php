@props(['infoId', 'paginationId', 'initialText' => 'Preparando tabla'])

<nav {{ $attributes->class(['ui-data-table__footer', 'app-table-footer', 'flex', 'flex-row', 'flex-wrap', 'items-center', 'justify-between', 'gap-3', 'p-4', 'border-t', 'border-gray-100']) }} aria-label="Paginación de la tabla">
    <span class="ui-data-table__info text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading" id="{{ $infoId }}">{{ $initialText }}</span>
    <div id="{{ $paginationId }}" class="ui-data-table__pagination flex-none"></div>
</nav>
