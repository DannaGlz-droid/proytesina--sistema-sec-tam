<div class="border border-gray-200 rounded-xl p-4 bg-gray-50 shadow-md shadow-gray-200/70 border-t-4 border-t-[#611132] {{ $attributes->get('class') }}">
    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
        <h3 class="font-semibold text-[#404041] text-lg font-lora">{{ $titulo ?? 'Filtros' }}</h3>
        {{ $headerActions ?? '' }}
    </div>
    <div class="space-y-3">
        {{ $slot }}
    </div>
</div>
