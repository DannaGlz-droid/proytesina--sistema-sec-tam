@props([
    'title',
    'description',
    'backHref' => null,
    'backLabel' => null,
    'preferHistoryBack' => false,
])

<div {{ $attributes->class(['app-page-header', 'users-form-header']) }}>
    <div>
        @if($backHref && $backLabel)
            <a href="{{ $backHref }}"
               class="users-form-back-link"
               @if($preferHistoryBack) data-history-back="true" @endif>
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>{{ $backLabel }}</span>
            </a>
        @endif

        <h1 class="app-page-title">{{ $title }}</h1>
        <p class="app-page-subtitle">{{ $description }}</p>
    </div>

    @isset($actions)
        <div class="app-page-header-actions">
            {{ $actions }}
        </div>
    @endisset
</div>
