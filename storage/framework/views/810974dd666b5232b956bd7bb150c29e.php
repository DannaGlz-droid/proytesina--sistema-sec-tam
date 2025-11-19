<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4" <?php echo e($attributes); ?>>
    <!-- Botón secundario (o enlace si se pasó secondaryHref) -->
    <?php if($secondaryHref): ?>
        <a href="<?php echo e($secondaryHref); ?>" class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
            <?php if($secondaryIcon): ?>
                <i class="fas <?php echo e($secondaryIcon); ?> text-xs"></i>
            <?php endif; ?>
            <?php echo e($secondaryText); ?>

        </a>
    <?php elseif($secondaryText && !request()->is('reportes/*/*/edit')): ?>
        <button type="<?php echo e($secondaryType); ?>" 
                <?php if($secondaryOnclick): ?> onclick="<?php echo $secondaryOnclick; ?>" <?php endif; ?>
                class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
            <?php if($secondaryIcon): ?>
                <i class="fas <?php echo e($secondaryIcon); ?> text-xs"></i>
            <?php endif; ?>
            <?php echo e($secondaryText); ?>

        </button>
    <?php endif; ?>
    
    
    <?php if($tertiaryText): ?>
        <?php if($tertiaryHref): ?>
            <a href="<?php echo e($tertiaryHref); ?>" class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                <?php echo e($tertiaryText); ?>

            </a>
        <?php else: ?>
            <button type="<?php echo e($tertiaryType); ?>" 
                    <?php if($tertiaryOnclick): ?> onclick="<?php echo $tertiaryOnclick; ?>" <?php endif; ?>
                    class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                <?php echo e($tertiaryText); ?>

            </button>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Botón primario -->
    <button type="<?php echo e($primaryType); ?>" 
            class="bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
        <?php if($primaryIcon): ?>
            <i class="fas <?php echo e($primaryIcon); ?> text-xs"></i>
        <?php endif; ?>
        <?php echo e($primaryText); ?>

    </button>
</div><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/form-buttons.blade.php ENDPATH**/ ?>