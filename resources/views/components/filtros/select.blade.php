@props([
    'placeholder' => 'Seleccionar',
    'variant' => 'default',
    'searchable' => null,
    'autoInit' => true,
    'allowEmptyOption' => false,
])

@php
    $selectAttributes = [
        'data-placeholder' => $placeholder,
        'data-select-variant' => $variant,
    ];

    if ($autoInit) {
        $selectAttributes['data-filter-select'] = '';
    }

    if (! is_null($searchable)) {
        $selectAttributes['data-searchable'] = $searchable ? 'true' : 'false';
    }

    if ($allowEmptyOption) {
        $selectAttributes['data-allow-empty-option'] = 'true';
    }
@endphp

<select
    {{ $attributes->class(['app-filter-select', 'app-filter-select--'.$variant])->merge($selectAttributes) }}
>
    {{ $slot }}
</select>
