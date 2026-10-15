@props([
    'title',
    'description',
    'backHref' => null,
    'backLabel' => null,
    'preferHistoryBack' => false,
    'pageClass' => null,
    'cardClass' => null,
])

<main {{ $attributes->class([
    'ui-form-page',
    'users-form-page',
    'px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10',
    $pageClass,
]) }}>
    <x-ui.page-header
        :title="$title"
        :description="$description"
        :back-href="$backHref"
        :back-label="$backLabel"
        :prefer-history-back="$preferHistoryBack"
    >
        @isset($headerActions)
            <x-slot:actions>
                {{ $headerActions }}
            </x-slot:actions>
        @endisset
    </x-ui.page-header>

    <div @class(['ui-form-card', 'users-form-card', $cardClass])>
        {{ $slot }}
    </div>
</main>
