<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['titulo', 'tipos' => ['bar', 'pie', 'doughnut'], 'tipoInicial' => 'bar', 'descargable' => true, 'graficoId' => '']));

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

foreach (array_filter((['titulo', 'tipos' => ['bar', 'pie', 'doughnut'], 'tipoInicial' => 'bar', 'descargable' => true, 'graficoId' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="chart-card border border-[#404041] rounded-lg p-5 bg-white">
    <div class="chart-header flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
            <h3 class="chart-title font-semibold text-[#404041] text-lg font-lora"><?php echo e($titulo); ?></h3>
            <!-- Badge to show quick total/summary; JS will populate by data-chart-id -->
            <div class="chart-total-badge text-sm text-gray-600" data-chart-id="<?php echo e($graficoId); ?>" style="display:inline-block;">&nbsp;</div>
        </div>
        
        <div class="chart-controls flex gap-2">
            
            <?php if($graficoId === 'municipioChart'): ?>
                <div class="flex items-center mr-2">
                    <span class="text-xs text-gray-600 mr-2">Ver por:</span>
                    <div id="municipioKindToggle" class="inline-flex rounded-md bg-gray-100 p-0.5">
                        <button type="button" data-value="death" class="mun-kind-btn px-2 py-1 text-xs font-medium text-[#611132] bg-white rounded">Defunción</button>
                        <button type="button" data-value="residence" class="mun-kind-btn px-2 py-1 text-xs font-medium text-gray-600 rounded">Residencia</button>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Selector de tipo de gráfico -->
            <?php if(count($tipos) > 1): ?>
                <div class="chart-type-selector flex bg-gray-100 rounded-lg p-1">
                    <?php if(in_array('bar', $tipos)): ?>
                        <button class="chart-type-btn <?php echo e($tipoInicial == 'bar' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600'); ?> px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="bar" title="Gráfico de Barras">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if(in_array('line', $tipos)): ?>
                        <button class="chart-type-btn <?php echo e($tipoInicial == 'line' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600'); ?> px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="line" title="Gráfico de Líneas">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if(in_array('area', $tipos)): ?>
                        <button class="chart-type-btn <?php echo e($tipoInicial == 'area' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600'); ?> px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="area" title="Gráfico de Área">
                            <i class="fas fa-chart-area"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if(in_array('pie', $tipos)): ?>
                        <button class="chart-type-btn <?php echo e($tipoInicial == 'pie' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600'); ?> px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="pie" title="Gráfico Circular">
                            <i class="fas fa-chart-pie"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if(in_array('doughnut', $tipos)): ?>
                        <button class="chart-type-btn <?php echo e($tipoInicial == 'doughnut' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600'); ?> px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="doughnut" title="Gráfico de Donas">
                            <i class="fas fa-circle"></i>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Botón descargar -->
            <!-- En tu componente x-graficos.tarjeta -->
            <?php if($descargable): ?>
                <button class="chart-download-btn font-lora bg-[#611132] text-white px-3 py-1 rounded-lg text-xs flex items-center gap-1">
                    <i class="fas fa-download"></i> Descargar
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <?php echo e($slot); ?>

</div><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/graficos/tarjeta.blade.php ENDPATH**/ ?>