@props([
    'id',
    'labelledby',
    'describedby' => null,
    'size' => 'md',
    'panelClass' => '',
    'overlayAttributes' => [],
])

@php
    $overlayBag = new \Illuminate\View\ComponentAttributeBag($overlayAttributes);
@endphp

<div
    id="{{ $id }}"
    {{ $attributes->class(['ui-dialog', 'ui-dialog--'.$size, 'hidden']) }}
    aria-hidden="true"
>
    <div {{ $overlayBag->class(['ui-dialog__overlay']) }}></div>
    <section
        class="ui-dialog__panel {{ $panelClass }}"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $labelledby }}"
        @if($describedby) aria-describedby="{{ $describedby }}" @endif
    >
        {{ $slot }}
    </section>
</div>
