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

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row justify-end gap-3 lg:gap-4']) }}>
    <!-- Botón secundario (o enlace si se pasó secondaryHref) -->
    @if($secondaryHref)
        <a href="{{ $secondaryHref }}" class="ui-button ui-button--secondary">
            @if($secondaryIcon)
                <i class="fas {{ $secondaryIcon }} text-xs"></i>
            @endif
            {{ $secondaryText }}
        </a>
    @elseif($secondaryText && !request()->is('reportes/*/*/edit'))
        <button type="{{ $secondaryType }}" 
                @if($secondaryOnclick) onclick="{!! $secondaryOnclick !!}" @endif
                class="ui-button ui-button--secondary">
            @if($secondaryIcon)
                <i class="fas {{ $secondaryIcon }} text-xs"></i>
            @endif
            {{ $secondaryText }}
        </button>
    @endif
    
    {{-- Tertiary button/link (optional) --}}
    @if($tertiaryText)
        @if($tertiaryHref)
            <a href="{{ $tertiaryHref }}" class="ui-button ui-button--secondary">
                {{ $tertiaryText }}
            </a>
        @else
                <button type="{{ $tertiaryType }}" 
                    @if($tertiaryOnclick) onclick="{!! $tertiaryOnclick !!}" @endif
                    class="ui-button ui-button--secondary">
                {{ $tertiaryText }}
            </button>
        @endif
    @endif
    
    <!-- Botón primario -->
    <button type="{{ $primaryType }}" 
            class="ui-button ui-button--primary">
        @if($primaryIcon)
            <i class="fas {{ $primaryIcon }} text-xs"></i>
        @endif
        {{ $primaryText }}
    </button>
</div>
