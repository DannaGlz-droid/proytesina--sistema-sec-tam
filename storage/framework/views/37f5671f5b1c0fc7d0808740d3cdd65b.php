<?php
    $config = [
        'tipo' => 'seguridad_vial',
        'titulo' => 'Reporte de Seguridad Vial', 
        'colorBadge' => 'bg-[#4C8CC4]',
        'colorBorder' => 'border-[#13264F]',
        'modalId' => 'modalSeguridadVial'
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
    
    <!-- SECCIÓN ESPECÍFICA DE SEGURIDAD VIAL -->
    
    <!-- DETALLES DE LA ACTIVIDAD -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Detalles de la Actividad</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-calendar-alt text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Actividad</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-actividad">-</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Tipo de actividad realizada</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map-marker-alt text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Lugar</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-lugar">Centro Comunitario Norte</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Ubicación del evento</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-user-tie text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Promotor</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-promotor">María González López</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Responsable de la actividad</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-users text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Participantes</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-participantes">45</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Asistentes registrados</p>
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
<?php endif; ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/modal-seguridad-vial.blade.php ENDPATH**/ ?>