@props([
    'title',
    'icon',
    'variant' => null,
    'contentClass' => null,
])

@php
    $iconClass = match ($icon) {
        'user', 'person-outline' => 'far fa-user',
        'work', 'business-outline' => 'far fa-building',
        'settings', 'settings-outline' => 'fas fa-cog',
        default => $icon,
    };
    $isReportRoad = $variant === 'report-road';
    $resolvedContentClass = $contentClass ?: 'grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4';
@endphp

<section {{ $attributes->class([
    'ui-form-section users-form-section mb-6 lg:mb-8',
    'report-road-section' => $isReportRoad,
]) }}>
    <div @class([
        'users-form-section-header flex items-center mb-4',
        'report-road-section-heading' => $isReportRoad,
    ])>
        <i class="{{ $iconClass }}" aria-hidden="true"></i>
        <h2>{{ $title }}</h2>
        <div class="users-form-section-rule flex-1 h-px ml-3" aria-hidden="true"></div>
    </div>

    <div class="{{ $resolvedContentClass }}">
        {{ $slot }}
    </div>
</section>
