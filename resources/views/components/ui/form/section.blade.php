@props([
    'title',
    'icon',
])

@php
    $iconClass = match ($icon) {
        'user', 'person-outline' => 'far fa-user',
        'work', 'business-outline' => 'far fa-building',
        'settings', 'settings-outline' => 'fas fa-cog',
        default => $icon,
    };
@endphp

<section {{ $attributes->merge(['class' => 'users-form-section mb-6 lg:mb-8']) }}>
    <div class="users-form-section-header flex items-center mb-4">
        <i class="{{ $iconClass }}" aria-hidden="true"></i>
        <h2>{{ $title }}</h2>
        <div class="users-form-section-rule flex-1 h-px ml-3" aria-hidden="true"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
        {{ $slot }}
    </div>
</section>
