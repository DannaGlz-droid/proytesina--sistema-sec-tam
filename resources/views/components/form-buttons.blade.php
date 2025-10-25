@props([
    'primaryText' => 'Guardar registro',
    'secondaryText' => 'Limpiar formulario',
    'primaryIcon' => null,
    'secondaryIcon' => null,
    'primaryType' => 'submit',
    'secondaryType' => 'button',
    // if provided, secondaryHref will render an <a> instead of a <button>
    'secondaryHref' => null,
    // if provided, secondaryOnclick will be added as an onclick attribute to the button
    'secondaryOnclick' => null,
    // tertiary (e.g. volver al listado)
    'tertiaryText' => null,
    'tertiaryHref' => null,
    'tertiaryOnclick' => null,
    'tertiaryType' => 'button',
])

<div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4" {{ $attributes }}>
    <!-- Botón secundario (o enlace si se pasó secondaryHref) -->
    @if($secondaryHref)
        <a href="{{ $secondaryHref }}" class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
            @if($secondaryIcon)
                <i class="fas {{ $secondaryIcon }} text-xs"></i>
            @endif
            {{ $secondaryText }}
        </a>
    @elseif($secondaryText)
        <button type="{{ $secondaryType }}" 
                @if($secondaryOnclick) onclick="{!! $secondaryOnclick !!}" @endif
                class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
            @if($secondaryIcon)
                <i class="fas {{ $secondaryIcon }} text-xs"></i>
            @endif
            {{ $secondaryText }}
        </button>
    @endif
    
    {{-- Tertiary button/link (optional) --}}
    @if($tertiaryText)
        @if($tertiaryHref)
            <a href="{{ $tertiaryHref }}" class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                {{ $tertiaryText }}
            </a>
        @else
            <button type="{{ $tertiaryType }}" 
                    @if($tertiaryOnclick) onclick="{!! $tertiaryOnclick !!}" @endif
                    class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                {{ $tertiaryText }}
            </button>
        @endif
    @endif
    
    <!-- Botón primario -->
    <button type="{{ $primaryType }}" 
            class="bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
        @if($primaryIcon)
            <i class="fas {{ $primaryIcon }} text-xs"></i>
        @endif
        {{ $primaryText }}
    </button>
</div>