@props(['id', 'clearId', 'placeholder' => 'Buscar…', 'label' => 'Buscar'])

<div {{ $attributes->class(['ui-data-table__search', 'users-filter-search']) }}>
    <i class="fas fa-search" aria-hidden="true"></i>
    <input type="search" id="{{ $id }}" placeholder="{{ $placeholder }}" aria-label="{{ $label }}"
        autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false">
    <span class="users-search-progress" aria-hidden="true"></span>
    <button type="button" id="{{ $clearId }}" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
</div>
