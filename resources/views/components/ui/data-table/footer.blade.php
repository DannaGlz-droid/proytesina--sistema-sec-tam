@props([
    'infoId' => null,
    'paginationId' => null,
    'initialText' => 'Preparando tabla',
    'from' => null,
    'to' => null,
    'filteredTotal' => null,
    'overallTotal' => null,
])

@php
    $hasServerSummary = $filteredTotal !== null;
    $filteredCount = $hasServerSummary ? (int) $filteredTotal : null;
    $overallCount = $overallTotal !== null ? (int) $overallTotal : $filteredCount;
    $rangeFrom = $filteredCount > 0 ? (int) ($from ?? 0) : 0;
    $rangeTo = $filteredCount > 0 ? (int) ($to ?? 0) : 0;
@endphp

<nav {{ $attributes->class(['ui-data-table__footer', 'app-table-footer', 'flex', 'flex-row', 'flex-wrap', 'items-center', 'justify-between', 'gap-3', 'p-4', 'border-t', 'border-gray-100']) }} aria-label="Paginación de la tabla">
    @if($hasServerSummary)
        <p @if($infoId) id="{{ $infoId }}" @endif class="ui-data-table__info users-table-info-main text-sm font-normal text-gray-500 flex-1 min-w-0">
            Mostrando <span class="font-semibold text-gray-900">{{ $rangeFrom }}-{{ $rangeTo }}</span> de <span class="font-semibold text-gray-900">{{ $filteredCount }}</span>
            @if($overallCount !== $filteredCount)
                <span class="users-table-info-context reports-table-total text-sm text-gray-400">(de {{ $overallCount }} totales)</span>
            @endif
        </p>
    @else
        <span class="ui-data-table__info text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading" @if($infoId) id="{{ $infoId }}" @endif>{{ $initialText }}</span>
    @endif

    <div @if($paginationId) id="{{ $paginationId }}" @endif class="ui-data-table__pagination flex-none">
        {{ $slot }}
    </div>
</nav>
