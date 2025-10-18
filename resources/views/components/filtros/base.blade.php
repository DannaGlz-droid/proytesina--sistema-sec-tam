<div class="border border-[#404041] rounded-lg p-4 bg-white {{ $attributes->get('class') }}">
    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
        <h3 class="font-semibold text-[#404041] text-lg font-lora">{{ $titulo ?? 'Filtros' }}</h3>
        {{ $headerActions ?? '' }}
    </div>
    <div class="space-y-3">
        {{ $slot }}
    </div>
</div>