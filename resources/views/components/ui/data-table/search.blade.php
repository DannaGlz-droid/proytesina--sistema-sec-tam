@props([
    'id',
    'clearId',
    'placeholder' => 'Buscar…',
    'label' => 'Buscar',
    'name' => null,
    'value' => null,
    'inputClass' => '',
    'progressClass' => '',
    'clearClass' => '',
    'clearVisible' => false,
])

<div {{ $attributes->class(['ui-data-table__search', 'users-filter-search']) }}>
    <i class="fas fa-search" aria-hidden="true"></i>
    <input type="search" id="{{ $id }}" @if($name) name="{{ $name }}" @endif value="{{ $value }}" class="{{ $inputClass }}"
        placeholder="{{ $placeholder }}" aria-label="{{ $label }}"
        autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false">
    <span class="users-search-progress {{ $progressClass }}" aria-hidden="true"></span>
    <button type="button" id="{{ $clearId }}" class="{{ $clearVisible ? '' : 'hidden' }} {{ $clearClass }}" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
</div>
