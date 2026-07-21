@props([
    'title',
    'icon',
])

<section {{ $attributes->merge(['class' => 'users-form-section mb-6 lg:mb-8']) }}>
    <div class="users-form-section-header flex items-center mb-4">
        <ion-icon name="{{ $icon }}" aria-hidden="true"></ion-icon>
        <h2>{{ $title }}</h2>
        <div class="flex-1 h-px bg-[#404041] ml-3" aria-hidden="true"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
        {{ $slot }}
    </div>
</section>
