<?php
    $config = [
        'tipo' => 'observatorio de lesiones',
        'titulo' => 'Reporte del Observatorio', 
        'colorBadge' => 'bg-[#75A84E]',
        'colorBorder' => 'border-[#184823]',
        'modalId' => 'modalObservatorio'
    ];
?>

<?php if (isset($component)) { $__componentOriginal8995d0c5cd3d3f03fd154e9c66f4c68f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8995d0c5cd3d3f03fd154e9c66f4c68f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-reporte-base','data' => ['tipo' => $config['tipo'],'titulo' => $config['titulo'],'colorBadge' => $config['colorBadge'],'colorBorder' => $config['colorBorder'],'modalId' => $config['modalId']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-reporte-base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tipo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['tipo']),'titulo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['titulo']),'colorBadge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['colorBadge']),'colorBorder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['colorBorder']),'modalId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['modalId'])]); ?>
    
    <!-- SECCIÓN ESPECÍFICA DEL OBSERVATORIO -->
    
    <!-- INFORMACIÓN GEOGRÁFICA -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Información Geográfica</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-city text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Municipio</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-municipio">Municipio Centro</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Área de cobertura del estudio</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Jurisdicción</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-jurisdiccion">Jurisdicción Sanitaria III</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Zona administrativa</p>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8995d0c5cd3d3f03fd154e9c66f4c68f)): ?>
<?php $attributes = $__attributesOriginal8995d0c5cd3d3f03fd154e9c66f4c68f; ?>
<?php unset($__attributesOriginal8995d0c5cd3d3f03fd154e9c66f4c68f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8995d0c5cd3d3f03fd154e9c66f4c68f)): ?>
<?php $component = $__componentOriginal8995d0c5cd3d3f03fd154e9c66f4c68f; ?>
<?php unset($__componentOriginal8995d0c5cd3d3f03fd154e9c66f4c68f); ?>
<?php endif; ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/modal-observatorio.blade.php ENDPATH**/ ?>