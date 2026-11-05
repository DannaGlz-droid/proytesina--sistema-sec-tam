@props(['label' => null])

<div
    {{ $attributes->class(['ui-data-table', 'app-table-card']) }}
    @if($label) role="region" aria-label="{{ $label }}" @endif
>
    {{ $slot }}
</div>
