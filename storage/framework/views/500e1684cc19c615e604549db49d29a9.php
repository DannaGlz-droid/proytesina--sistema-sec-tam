<?php
    $config = [
        'tipo' => 'alcoholimetria',
        'titulo' => 'Reporte de Alcoholimetría', 
        'colorBadge' => 'bg-[#9D2449]',
        'colorBorder' => 'border-[#470202]',
        'modalId' => 'modalAlcoholimetria'
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
    
    <!-- SECCIÓN ESPECÍFICA DE ALCOHOLIMETRÍA -->
    
    <!-- RESULTADOS DEL OPERATIVO -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Resultados del Operativo</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map-marker-alt text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Puntos de Revisión</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-puntos-revision">5</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Puntos instalados en el operativo</p>
            </div>

            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-vial text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Pruebas Realizadas</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-pruebas-realizadas">20</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Total de pruebas aplicadas</p>
            </div>

            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-user-times text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Conductores No Aptos</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-conductores-no-aptos">12</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Total de conductores detectados</p>
            </div>
        </div>
    </div>

    <!-- LÍNEA SEPARADORA -->
    <div class="h-px bg-gray-300 mb-6"></div>

    <!-- CONDUCTORES NO APTOS -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Conductores no aptos</h4>
        
        <!-- Por género -->
        <div class="mb-6">
            <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por género</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-female text-[#404041] text-lg"></i>
                        <span class="text-sm text-gray-700 font-semibold font-lora">Mujeres</span>
                    </div>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-mujeres-no-aptas">5</div>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-male text-[#404041] text-lg"></i>
                        <span class="text-sm text-gray-700 font-semibold font-lora">Hombres</span>
                    </div>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-hombres-no-aptos">6</div>
                </div>
            </div>
        </div>

        <!-- Por tipo de vehículo -->
        <div>
            <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por tipo de vehículo</h5>
            
            <!-- Primera fila de 3 cuadros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-car text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-automoviles-no-aptos">5</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Automóviles y camionetas</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-motorcycle text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-motocicletas-no-aptas">2</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Motocicletas</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-bus text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-colectivo-no-apto">0</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte público colectivo</p>
                </div>
            </div>
            
            <!-- Segunda fila de 3 cuadros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-taxi text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-individual-no-apto">1</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte público individual</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-truck text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-carga-no-apto">1</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte de carga</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-ambulance text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-emergencia-no-apto">0</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Vehículos de emergencia</p>
                </div>
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
<?php endif; ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/modal-alcoholimetria.blade.php ENDPATH**/ ?>