@props(['id', 'countId', 'context' => 'En esta página'])

<div id="{{ $id }}" {{ $attributes->class(['ui-data-table__bulk-actions', 'app-table-bulk-inline', 'hidden', 'items-center', 'gap-3']) }} role="status" aria-live="polite">
    <div class="ui-data-table__selection-summary flex items-center gap-2">
        <span class="app-table-selection-marker" aria-hidden="true"></span>
        <span id="{{ $countId }}" class="app-table-selection-count text-xs whitespace-nowrap"></span>
    </div>
    @if($context)
        <span class="ui-data-table__selection-context hidden xl:inline text-xs text-gray-500">{{ $context }}</span>
    @endif
    {{ $slot }}
</div>
