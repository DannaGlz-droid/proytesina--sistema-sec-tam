@props([
    'id',
    'controls',
    'countId' => null,
    'count' => 0,
    'label' => 'Filtros',
    'icon' => 'fa-sliders-h',
    'expanded' => false,
    'countClass' => '',
])

<button
    type="button"
    id="{{ $id }}"
    {{ $attributes->class(['users-filter-toggle']) }}
    aria-expanded="{{ $expanded ? 'true' : 'false' }}"
    aria-controls="{{ $controls }}"
    aria-haspopup="dialog"
    data-filter-popover-toggle
>
    <i class="fas {{ $icon }}" aria-hidden="true"></i>
    <span>{{ $label }}</span>
    @if($countId)
        <span id="{{ $countId }}" class="users-filter-count {{ $countClass }} {{ (int) $count > 0 ? '' : 'hidden' }}" aria-label="Filtros aplicados">{{ (int) $count }}</span>
    @endif
</button>
