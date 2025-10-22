@props([
    'primaryText' => 'Guardar registro',
    'secondaryText' => 'Limpiar formulario',
    'primaryIcon' => null,
    'secondaryIcon' => null,
    'primaryType' => 'submit',
    'secondaryType' => 'button'
])

<div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4" {{ $attributes }}>
    <!-- Botón secundario -->
    <button type="{{ $secondaryType }}" 
            class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
        @if($secondaryIcon)
            <i class="fas {{ $secondaryIcon }} text-xs"></i>
        @endif
        {{ $secondaryText }}
    </button>
    
    <!-- Botón primario -->
    <button type="{{ $primaryType }}" 
            class="bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
        @if($primaryIcon)
            <i class="fas {{ $primaryIcon }} text-xs"></i>
        @endif
        {{ $primaryText }}
    </button>
</div>