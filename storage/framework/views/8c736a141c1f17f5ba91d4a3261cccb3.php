<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['titulo', 'icono', 'gridCols' => 'grid-cols-1 gap-6']));

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

foreach (array_filter((['titulo', 'icono', 'gridCols' => 'grid-cols-1 gap-6']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="chart-category mb-8">
    <div class="chart-category-header mb-4 pb-2 border-b border-gray-300">
        <h2 class="chart-category-title text-xl font-semibold text-[#404041] font-lora flex items-center gap-2">
            <i class="fas fa-<?php echo e($icono); ?>"></i> <?php echo e($titulo); ?>

        </h2>
    </div>
    <div class="charts-container grid <?php echo e($gridCols); ?>">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/graficos/categoria.blade.php ENDPATH**/ ?>