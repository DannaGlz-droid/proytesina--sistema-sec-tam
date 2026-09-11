@props([
    'placeholder' => 'Seleccionar',
])

<select
    {{ $attributes->class(['app-filter-select'])->merge([
        'data-filter-select' => '',
        'data-placeholder' => $placeholder,
    ]) }}
>
    {{ $slot }}
</select>
