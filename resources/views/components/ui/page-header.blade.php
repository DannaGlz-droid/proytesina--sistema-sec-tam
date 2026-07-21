@props([
    'title',
    'description',
    'backHref' => null,
    'backLabel' => null,
])

<div class="users-form-header">
    <div>
        @if($backHref && $backLabel)
            <a href="{{ $backHref }}" class="users-form-back-link">
                <ion-icon name="arrow-back-outline" aria-hidden="true"></ion-icon>
                <span>{{ $backLabel }}</span>
            </a>
        @endif

        <h1 class="app-page-title">{{ $title }}</h1>
        <p class="app-page-subtitle">{{ $description }}</p>
    </div>
</div>
