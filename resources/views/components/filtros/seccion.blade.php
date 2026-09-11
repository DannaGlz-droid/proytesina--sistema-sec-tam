@props([
    'titulo',
    'abierto' => false,
    'icono' => null,
])

<section
    {{ $attributes->class(['users-filter-section', 'is-open' => $abierto]) }}
    data-filter-section
>
    <button
        type="button"
        class="users-filter-section-toggle"
        data-filter-section-toggle
        aria-expanded="{{ $abierto ? 'true' : 'false' }}"
    >
        <i class="fas {{ $abierto ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
        <span>{{ $titulo }}</span>
    </button>
    <div class="users-filter-section-content">
        {{ $slot }}
    </div>
</section>
