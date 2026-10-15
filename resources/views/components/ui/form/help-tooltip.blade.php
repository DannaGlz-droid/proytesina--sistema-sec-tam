@props([
    'id',
    'label',
])

<span class="statistics-form-help-popover">
    <button
        type="button"
        class="statistics-form-help-trigger"
        aria-label="{{ $label }}"
        aria-describedby="{{ $id }}"
    >
        <i class="fas fa-circle-question" aria-hidden="true"></i>
    </button>
    <span id="{{ $id }}" class="statistics-form-tooltip" role="tooltip">
        {{ $slot }}
    </span>
</span>
