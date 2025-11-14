
<?php $__env->startSection('title', 'Reportes'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-reportes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Centro de Control</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Monitoreo y administración centralizada de reportes.</p>

        <!-- Mensajes de éxito y error -->
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-sm text-green-800 font-lora font-medium"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm text-red-800 font-lora font-medium"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <!-- PESTAÑAS INTEGRADAS AL CONTENEDOR -->
            <div class="border-b border-gray-300 bg-gray-50 px-4 lg:px-6 pt-4">
                <nav class="flex space-x-1" aria-label="Tabs">
                    <button data-tipo="todos" class="tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-[#404041] text-white border border-b-0 border-gray-300 transition-all duration-200 hover:bg-[#2a2a2a]">
                        Todos los tipos
                    </button>
                    <button data-tipo="seguridad_vial" class="tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Seguridad Vial
                    </button>
                    <button data-tipo="observatorio" class="tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Observatorio
                    </button>
                    <button data-tipo="alcoholimetria" class="tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Alcoholimetría
                    </button>
                </nav>
            </div>

            <!-- CONTENIDO INTERIOR DEL CONTENEDOR -->
            <div class="p-4 lg:p-6">
                <!-- FILTROS MEJORADOS - ORDEN UX OPTIMIZADO -->
                <div class="flex flex-col lg:flex-row gap-3 lg:gap-3 items-start lg:items-end mb-6">
                    <!-- Búsqueda por texto - PRIMERO (más importante) -->
                    <div class="flex-1 min-w-0 lg:max-w-[280px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Buscar</label>
                        <div class="relative">
                            <input type="text" placeholder="Títulos, usuarios..." class="w-full border border-[#404041] rounded-lg px-3 py-1.5 pl-9 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Rango de fechas compacto -->
                    <div class="flex-1 min-w-0 lg:max-w-[300px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Rango de fechas</label>
                        <div class="flex gap-2">
                            <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <span class="flex items-center text-gray-500 text-xs">a</span>
                            <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                        </div>
                    </div>

                    <!-- Ordenar por compacto -->
                    <div class="flex-1 min-w-0 lg:max-w-[180px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Ordenar</label>
                        <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <option value="fecha-desc">Más recientes</option>
                            <option value="fecha-asc">Más antiguos</option>
                            <option value="titulo">Título A-Z</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>

                    <!-- Botones de acción compactos -->
                    <div class="flex gap-2 mt-2 lg:mt-0">
                        <button class="bg-[#611132] text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-filter text-xs"></i>
                            Aplicar
                        </button>
                        <button class="border border-[#404041] text-[#404041] px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-redo text-xs"></i>
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- CONTADOR DE RESULTADOS -->
                <div class="flex justify-between items-center mb-6">
                    <div class="text-xs text-gray-600 font-lora">
                        <span class="font-semibold text-[#404041]"><?php echo e($publications->count()); ?></span> resultados encontrados
                    </div>
                    <div class="text-xs text-gray-500 font-lora">
                        <!-- Espacio para filtros adicionales si se necesitan -->
                    </div>
                </div>

                <!-- Grid de reportes - 4 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 items-stretch">
                    <?php $__empty_1 = true; $__currentLoopData = $publications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // Determinar tipo de reporte y sus datos específicos
                            $tipoDisplay = '';
                            $badgeClass = 'bg-[#4C8CC4] text-white';
                            $badgeBorderClass = 'border-[#13264F]';
                            $claseModal = '';
                            $dataAttributes = [];
                            $activityInfo = ''; // Para mostrar debajo del título
                            $editRoute = '#'; // Ruta de edición
                            
                            if ($pub->publication_type === 'seguridad_vial') {
                                $tipoDisplay = 'Seguridad Vial';
                                $badgeClass = 'bg-[#4C8CC4] text-white';
                                $badgeBorderClass = 'border-[#13264F]';
                                $claseModal = 'ver-detalle-seguridad';
                                $editRoute = route('reportes.seguridad-vial.edit', $pub);
                                $reporte = $pub->roadSafetyReports->first();
                                if ($reporte) {
                                    // Mostrar la línea de Actividad sólo para Seguridad Vial
                                    $activityInfo = 'Actividad: ' . ($reporte->activityType->name ?? 'No especificado');
                                    $dataAttributes = [
                                        'data-lugar' => $reporte->location ?? '',
                                        'data-promotor' => $reporte->promoter ?? '',
                                        'data-participantes' => $reporte->participants ?? '',
                                        'data-actividad' => $reporte->activityType->name ?? '',
                                    ];
                                }
                            } elseif ($pub->publication_type === 'observatorio') {
                                $tipoDisplay = 'Observatorio de lesiones';
                                $badgeClass = 'bg-[#75A84E] text-white';
                                $badgeBorderClass = 'border-[#184823]';
                                $claseModal = 'ver-detalle-observatorio';
                                $editRoute = route('reportes.observatorio.edit', $pub);
                                $reporte = $pub->injuryObservatoryReports->first();
                                if ($reporte) {
                                    // No mostrar línea de "Actividad" para observatorio; mantener sólo "Subido por"
                                    $activityInfo = '';
                                    $dataAttributes = [
                                        'data-municipio' => $reporte->municipality->name ?? '',
                                        'data-jurisdiccion' => $reporte->jurisdiction->name ?? '',
                                    ];
                                }
                            } elseif ($pub->publication_type === 'alcoholimetria') {
                                $tipoDisplay = 'Alcoholimetría';
                                $badgeClass = 'bg-[#9D2449] text-white';
                                $badgeBorderClass = 'border-[#470202]';
                                $claseModal = 'ver-detalle-alcohol';
                                $editRoute = route('reportes.alcoholimetria.edit', $pub);
                                $reporte = $pub->breathalyzerReports->first();
                                if ($reporte) {
                                    // No mostrar línea de "Actividad" para alcoholimetría; mantener sólo "Subido por"
                                    $activityInfo = '';
                                    $dataAttributes = [
                                        'data-puntos-revision' => $reporte->checkpoints ?? '',
                                        'data-conductores-no-aptos' => $reporte->drivers_not_fit ?? '',
                                        'data-pruebas-realizadas' => $reporte->tests_performed ?? '',
                                        'data-mujeres-no-aptas' => $reporte->women ?? '',
                                        'data-hombres-no-aptos' => $reporte->men ?? '',
                                        'data-automoviles-no-aptos' => $reporte->cars_trucks ?? '',
                                        'data-motocicletas-no-aptas' => $reporte->motorcycles ?? '',
                                        'data-transporte-colectivo-no-apto' => $reporte->public_transport_collective ?? '',
                                        'data-transporte-individual-no-apto' => $reporte->public_transport_individual ?? '',
                                        'data-transporte-carga-no-apto' => $reporte->cargo_transport ?? '',
                                        'data-emergencia-no-apto' => $reporte->emergency_vehicles ?? '',
                                    ];
                                }
                            }
                            
                            // Archivos JSON - incluir ID y nombre original
                            $archivosArray = $pub->files->map(function($file) {
                                return [
                                    'id' => $file->id,
                                    'name' => $file->original_name
                                ];
                            })->toArray();
                            $archivosJson = json_encode($archivosArray);
                        ?>

                        <?php
                            $user = $pub->user ?? null;
                            $uGiven = $user->name ?? '';
                            $uFirst = $user->first_last_name ?? '';
                            $uSecond = $user->second_last_name ?? '';
                            $uFull = trim(implode(' ', array_filter([$uGiven, $uFirst, $uSecond])));
                            $uShort = trim($uGiven . ($uFirst ? ' ' . $uFirst : '')) ?: 'Usuario';
                        ?>

                        <?php
                            // Determine if there are any unread comments for current user
                            // Only count comments from OTHER users that current user hasn't read
                            $comentarios = $pub->comentarios_json ?? [];
                            $hasUnread = false;
                            $currentUserId = auth()->id();
                            
                            // Convert to array if it's a Collection
                            if ($comentarios instanceof \Illuminate\Support\Collection) {
                                $comentarios = $comentarios->toArray();
                            }
                            
                            // DEBUG: Log para publicación #10
                            if ($pub->id == 10) {
                                \Log::info("Publication #10 debug for user #{$currentUserId}:", [
                                    'total_comments' => count($comentarios),
                                    'comments_detail' => array_map(function($c) use ($currentUserId) {
                                        return [
                                            'id' => $c['id'] ?? 'N/A',
                                            'author_id' => $c['user']['id'] ?? 'N/A',
                                            'seen_by_current' => $c['seen_by_current_user'] ?? false,
                                            'is_own' => ($c['user']['id'] ?? null) == $currentUserId
                                        ];
                                    }, $comentarios)
                                ]);
                            }
                            
                            if (!empty($comentarios) && is_array($comentarios)) {
                                foreach ($comentarios as $cc) {
                                    // Skip comments written by current user (they don't count as "unread")
                                    $commentAuthorId = $cc['user']['id'] ?? null;
                                    if ($commentAuthorId == $currentUserId) {
                                        continue;
                                    }
                                    // Check if this comment from another user is unread
                                    if (!($cc['seen_by_current_user'] ?? false)) {
                                        $hasUnread = true;
                                        break;
                                    }
                                }
                            }
                            
                            // DEBUG: Log result
                            if ($pub->id == 10) {
                                \Log::info("Publication #10 hasUnread result for user #{$currentUserId}: " . ($hasUnread ? 'TRUE' : 'FALSE'));
                            }
                        ?>

                        <?php if (isset($component)) { $__componentOriginale6927a94816a78ea3a8d4a0fc9fc3d88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6927a94816a78ea3a8d4a0fc9fc3d88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.publicacion-card','data' => ['dataPublicationId' => ''.e($pub->id).'','tipo' => $tipoDisplay,'titulo' => $pub->topic,'fecha' => $pub->publication_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),'usuario' => $uShort,'usuarioFull' => $uFull,'descripcion' => $activityInfo,'archivosCount' => $pub->files->count(),'badgeClass' => $badgeClass,'badgeBorderClass' => $badgeBorderClass,'hasComments' => count($pub->comentarios_json ?? []) > 0,'hasUnread' => $hasUnread,'dataPublicationTipo' => ''.e($pub->publication_type).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('publicacion-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-publication-id' => ''.e($pub->id).'','tipo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tipoDisplay),'titulo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->topic),'fecha' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->publication_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')),'usuario' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uShort),'usuario_full' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uFull),'descripcion' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityInfo),'archivosCount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->files->count()),'badgeClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeClass),'badgeBorderClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeBorderClass),'has-comments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($pub->comentarios_json ?? []) > 0),'has-unread' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasUnread),'data-publication-tipo' => ''.e($pub->publication_type).'']); ?>

                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white <?php echo e($claseModal); ?>" 
                                        title="Ver detalles"
                                        data-tipo="<?php echo e($pub->publication_type); ?>"
                                        data-titulo="<?php echo e($pub->topic); ?>"
                                        data-fecha="<?php echo e($pub->publication_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')); ?>"
                                        data-fecha-actividad="<?php echo e($pub->activity_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')); ?>"
                                        data-usuario="<?php echo e($uFull ?: ($pub->user->name ?? 'Usuario')); ?>"
                                        data-position="<?php echo e($pub->user->position->name ?? ''); ?>"
                                        data-descripcion="<?php echo e($pub->description ?? ''); ?>"
                                        data-archivos='<?php echo e($archivosJson); ?>'
                                        data-comentarios='<?php echo json_encode($pub->comentarios_json, 15, 512) ?>'
                                        data-publication-id="<?php echo e($pub->id); ?>"
                                        data-is-owner="<?php echo e(auth()->id() === $pub->user_id ? 'true' : 'false'); ?>"
                                        <?php $__currentLoopData = $dataAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($key); ?>="<?php echo e($value); ?>"
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <a href="<?php echo e($editRoute); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('reportes.destroy', $pub)); ?>" onsubmit="return false;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" title="Eliminar"
                                        onclick="if(confirm('¿Eliminar esta publicación? Esta acción no se puede deshacer.')) { this.closest('form').submit(); }">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6927a94816a78ea3a8d4a0fc9fc3d88)): ?>
<?php $attributes = $__attributesOriginale6927a94816a78ea3a8d4a0fc9fc3d88; ?>
<?php unset($__attributesOriginale6927a94816a78ea3a8d4a0fc9fc3d88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6927a94816a78ea3a8d4a0fc9fc3d88)): ?>
<?php $component = $__componentOriginale6927a94816a78ea3a8d4a0fc9fc3d88; ?>
<?php unset($__componentOriginale6927a94816a78ea3a8d4a0fc9fc3d88); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-inbox text-6xl"></i>
                            </div>
                            <p class="text-lg font-lora text-gray-600">No hay publicaciones registradas</p>
                            <p class="text-sm text-gray-500 font-lora mt-2">Comienza creando un nuevo reporte</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="flex justify-center gap-2 mt-8">
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg opacity-50 cursor-not-allowed">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button class="px-3 py-2 border border-[#404041] bg-[#404041] text-white rounded-lg font-lora">1</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white font-lora">2</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white font-lora">3</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </div>
    </div>

    <!-- INCLUIR EL COMPONENTE DEL MODAL DE ALCOHOLIMETRÍA -->
<!-- AL FINAL DEL ARCHIVO hola.blade.php, DESPUÉS de incluir los modales -->

    <!-- INCLUIR TODOS LOS COMPONENTES DE MODALES -->
  <!-- INCLUIR TODOS LOS COMPONENTES DE MODALES -->
<?php echo $__env->make('components.modal-alcoholimetria', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.modal-seguridad-vial', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> 
<?php echo $__env->make('components.modal-observatorio', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- JAVASCRIPT SIMPLIFICADO Y FUNCIONAL -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIANDO SISTEMA DE MODALES ===');
    const CURRENT_USER_ID = <?php echo e(auth()->id() ?? 'null'); ?>;
    
    // Función simple para mostrar modal
    function showModal(modalId) {
        console.log('Intentando mostrar modal:', modalId);
        const modal = document.getElementById(modalId);
        
        if (!modal) {
            console.error('❌ Modal no encontrado:', modalId);
            return false;
        }
        
        console.log('✅ Modal encontrado, mostrando...');
        modal.classList.remove('hidden');
        
        // Pequeño delay para la animación
        setTimeout(() => {
            const content = modal.querySelector('div > div');
            if (content) {
                content.style.transform = 'scale(1)';
                content.style.opacity = '1';
            }
        }, 50);
        
        return true;
    }
    
    // Función para cerrar modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const content = modal.querySelector('div > div');
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }
    
    // Configurar eventos de cierre para todos los modales
    document.querySelectorAll('[id^="modal"]').forEach(modal => {
        const closeBtn = modal.querySelector('.modal-cerrar');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                closeModal(modal.id);
            });
        }
        
        // Cerrar al hacer click fuera del contenido
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
        
        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal(modal.id);
            }
        });
    });
    
    // === CONFIGURAR BOTONES DE APERTURA ===
    
    // Alcoholimetría
    document.querySelectorAll('.ver-detalle-alcohol').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Alcoholimetría');
            
            const modal = document.getElementById('modalAlcoholimetria');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos de alcoholimetría
                // Use kebab-case for class names and convert to camelCase to access dataset properties
                const specificFields = [
                    'puntos-revision', 'conductores-no-aptos', 'pruebas-realizadas',
                    'mujeres-no-aptas', 'hombres-no-aptos', 'automoviles-no-aptos',
                    'motocicletas-no-aptas', 'transporte-colectivo-no-apto',
                    'transporte-individual-no-apto', 'transporte-carga-no-apto', 'emergencia-no-apto'
                ];

                function kebabToCamel(s) {
                    return s.replace(/-([a-z])/g, function(_, c) { return c.toUpperCase(); });
                }

                specificFields.forEach(kebab => {
                    const element = modal.querySelector(`.modal-${kebab}`);
                    const key = kebabToCamel(kebab);
                    if (element && this.dataset[key] !== undefined && this.dataset[key] !== '') {
                        element.textContent = this.dataset[key];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalAlcoholimetria');
        });
    });
    
    // Seguridad Vial
    document.querySelectorAll('.ver-detalle-seguridad').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Seguridad Vial');
            
            const modal = document.getElementById('modalSeguridadVial');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos de seguridad vial
                const specificFields = ['lugar', 'promotor', 'participantes', 'actividad'];
                specificFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalSeguridadVial');
        });
    });
    
    // Observatorio
    document.querySelectorAll('.ver-detalle-observatorio').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Observatorio');
            
            const modal = document.getElementById('modalObservatorio');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos del observatorio
                const specificFields = [
                    'municipio', 'jurisdiccion', 'totalLesiones',
                    'lesionesGraves', 'lesionesModeradas', 'lesionesLeves'
                ];
                
                specificFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalObservatorio');
        });
    });
    
    // Función para llenar datos básicos
    function fillBasicData(modal, dataset) {
        // Datos básicos comunes
        const basicFields = {
            'modal-titulo': dataset.titulo,
            // Mostrar la fecha de la actividad directamente bajo el título (sin prefijo)
            'modal-fecha-actividad': dataset.fechaActividad || dataset.fecha,
            // La fecha de publicación se muestra en la zona superior derecha (reemplaza 'Subido por')
            'modal-fecha-publicacion': dataset.fecha,
            'modal-usuario': dataset.usuario,
            // Mostrar texto por defecto cuando no exista descripción o sólo contenga espacios
            'modal-descripcion': (dataset.descripcion && dataset.descripcion.trim()) ? dataset.descripcion : 'Sin descripción proporcionada.'
        };
        
        Object.entries(basicFields).forEach(([className, value]) => {
            const element = modal.querySelector(`.${className}`);
            if (element && value) {
                element.textContent = value;
            }
        });

        // Mostrar cargo del usuario (si se proporcionó)
        try {
            const usuarioCargoEl = modal.querySelector('.modal-usuario-cargo');
            if (usuarioCargoEl) {
                if (dataset.position && dataset.position.trim()) {
                    usuarioCargoEl.textContent = ` — ${dataset.position}`;
                } else {
                    usuarioCargoEl.textContent = '';
                }
            }
        } catch (e) {
            // ignore
        }
    }
    
    // Función para llenar archivos y comentarios
    function fillFilesAndComments(modal, dataset) {
        // Archivos adjuntos
        const archivosContainer = modal.querySelector('.modal-archivos');
        if (archivosContainer && dataset.archivos) {
            try {
                const archivos = JSON.parse(dataset.archivos);
                archivosContainer.innerHTML = '';
                
                // Guardar lista de archivos en el modal para el botón "Descargar Todos"
                modal.dataset.archivosJson = dataset.archivos;
                
                archivos.forEach((archivo) => {
                    const fileName = archivo.name || archivo; // Soporte para formato antiguo y nuevo
                    const fileId = archivo.id || null;
                    const extension = fileName.split('.').pop().toLowerCase();
                    const { icono, color } = obtenerEstiloArchivo(extension);
                    
                    archivosContainer.innerHTML += `
                        <div class="bg-white rounded-xl border border-[#404041] overflow-hidden transition-all duration-300 hover:shadow-lg group cursor-pointer">
                            <div class="${color} h-20 flex items-center justify-center">
                                <i class="${icono} text-3xl text-white"></i>
                            </div>
                            <div class="p-4">
                                <p class="text-sm font-semibold text-[#404041] font-lora truncate mb-1" title="${fileName}">
                                    ${fileName}
                                </p>
                                <p class="text-xs text-gray-500 font-lora mb-3">
                                    ${extension.toUpperCase()} • ${obtenerTamañoAleatorio()}
                                </p>
                                <button class="descargar-archivo w-full px-3 py-2 bg-[#404041] text-white text-xs font-semibold rounded-lg hover:bg-[#2a2a2a] transition-all duration-200 flex items-center justify-center gap-2" data-file-id="${fileId}">
                                    <i class="fas fa-download text-xs"></i>
                                    Descargar
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                // Configurar botón "Descargar Todos" si existe
                const btnDescargarTodos = modal.querySelector('.descargar-todos-archivos');
                if (btnDescargarTodos) {
                    btnDescargarTodos.replaceWith(btnDescargarTodos.cloneNode(true));
                    const newBtn = modal.querySelector('.descargar-todos-archivos');
                    newBtn.addEventListener('click', function() {
                        window.location.href = `/reportes/${dataset.publicationId}/download-all`;
                    });
                }
            } catch (e) {
                console.error('Error parsing archivos:', e);
            }
        }
        
        // Comentarios
        const comentariosContainer = modal.querySelector('.modal-comentarios');
        if (comentariosContainer && dataset.comentarios) {
            try {
                const comentarios = JSON.parse(dataset.comentarios);
                
                // Guardar comentarios originales en el modal para uso posterior
                modal.dataset.comentariosJson = dataset.comentarios;
                
                renderComments(comentariosContainer, comentarios);

                // Marcar como vistos los comentarios (si aplica) y actualizar la UI
                fetch(`/reportes/${dataset.publicationId}/comentarios/mark-seen`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && Array.isArray(data.updated_ids) && data.updated_ids.length > 0) {
                        // Marcar en la estructura local y re-renderizar (per-user flag)
                        comentarios.forEach(c => {
                            if (data.updated_ids.includes(c.id)) c.seen_by_current_user = true;
                        });
                        
                        // Actualizar también el dataset
                        modal.dataset.comentariosJson = JSON.stringify(comentarios);
                        
                        renderComments(comentariosContainer, comentarios);

                        // Also hide the unread dot on the publication card for this user
                        try {
                            const card = document.querySelector(`.publication-card[data-publication-id="${dataset.publicationId}"]`);
                            if (card) {
                                const dot = card.querySelector('.absolute.-top-0\.5.-right-0\.5');
                                if (dot) dot.remove();
                            }
                        } catch (e) {
                            // ignore
                        }
                    }
                })
                .catch(err => console.error('Error marcando comentarios como vistos:', err));
            } catch (e) {
                console.error('Error parsing comentarios:', e);
            }
        }

        // Guardar el publication ID en el modal para envío de comentarios
        modal.dataset.publicationId = dataset.publicationId;

        // Mostrar/ocultar formulario de comentarios según permisos
        const comentarioForm = modal.querySelector('.comentario-form-container');
        const isOwner = dataset.isOwner === 'true';
        const userRole = '<?php echo e(auth()->user()->role->name ?? ""); ?>';
        
        // Admin/Coordinador siempre pueden comentar, Operador solo en sus propias publicaciones
        if (comentarioForm) {
            if (userRole === 'Administrador' || userRole === 'Coordinador' || (userRole === 'Operador' && isOwner)) {
                comentarioForm.style.display = 'block';
            } else {
                comentarioForm.style.display = 'none';
            }
        }
    }

    // Función para renderizar comentarios
    function renderComments(container, comentarios) {
        container.innerHTML = '';
        
        if (comentarios.length > 0) {
                comentarios.forEach(comentario => {
                    // Prefer an ISO timestamp from the server and format it in the user's local timezone in the browser.
                    let dateStr = comentario.date || '';
                    let timeStr = comentario.time || '';
                    if (comentario.created_at_iso) {
                        try {
                            const dt = new Date(comentario.created_at_iso);
                            if (!isNaN(dt.getTime())) {
                                const d = String(dt.getDate()).padStart(2, '0');
                                const m = String(dt.getMonth() + 1).padStart(2, '0');
                                const y = dt.getFullYear();
                                const hh = String(dt.getHours()).padStart(2, '0');
                                const mm = String(dt.getMinutes()).padStart(2, '0');
                                dateStr = `${d}/${m}/${y}`;
                                timeStr = `${hh}:${mm}`;
                            }
                        } catch (e) {
                            // fallback to server-provided date/time
                        }
                    }

                    const tickHtml = (comentario.user && comentario.user.id == CURRENT_USER_ID)
                        ? (comentario.seen_by_current_user ? '<i class="fas fa-check-double text-blue-500 ml-2" title="Visto"></i>' : '<i class="fas fa-check text-gray-400 ml-2" title="Enviado"></i>')
                        : '';

                    container.innerHTML += `
                        <div class="bg-white border border-[#404041] rounded-lg p-3" data-comment-id="${comentario.id}" data-user-id="${comentario.user?.id}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-semibold text-[#404041] font-lora">
                                        ${comentario.user.name}
                                    </div>
                                    <div class="text-xs text-gray-500 font-lora">
                                        ${comentario.user.position}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-xs text-gray-500 font-lora whitespace-nowrap text-right">
                                        <div>${dateStr}</div>
                                        <div class="text-sm">${timeStr}</div>
                                    </div>
                                    ${tickHtml}
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm break-words font-lora">
                                ${comentario.comment}
                            </p>
                        </div>
                    `;
            });
        } else {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500 font-lora">
                    <i class="fas fa-comments text-3xl mb-3 text-gray-300"></i>
                    <p class="text-sm">No hay comentarios aún</p>
                </div>
            `;
        }
    }
    
    // Funciones auxiliares
    function obtenerEstiloArchivo(extension) {
        const estilos = {
            'pdf': { icono: 'fas fa-file-pdf', color: 'bg-red-500' },
            'xlsx': { icono: 'fas fa-file-excel', color: 'bg-green-500' },
            'jpg': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'jpeg': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'png': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'doc': { icono: 'fas fa-file-word', color: 'bg-blue-500' },
            'docx': { icono: 'fas fa-file-word', color: 'bg-blue-500' },
            'zip': { icono: 'fas fa-file-archive', color: 'bg-yellow-500' },
            'default': { icono: 'fas fa-file', color: 'bg-gray-500' }
        };
        
        return estilos[extension] || estilos.default;
    }
    
    function obtenerTamañoAleatorio() {
        const tamanios = ['2.1 MB', '1.5 MB', '3.2 MB', '856 KB', '4.7 MB'];
        return tamanios[Math.floor(Math.random() * tamanios.length)];
    }
    
    console.log('=== SISTEMA DE MODALES INICIALIZADO ===');
    
    // Función utilitaria para abrir un modal de publicación y navegar al comentario
    window.openPublicationFromNotification = function(publicationId, commentId) {
        try {
            const btn = document.querySelector(`button[data-publication-id="${publicationId}"]`);
            if (btn) {
                // Click the existing button to fill and show the modal
                btn.click();

                // Wait for modal to render comments and then scroll to the comment
                let tries = 0;
                const iv = setInterval(() => {
                    tries += 1;
                    // Find currently visible modal (not hidden)
                    const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden'));
                    if (modal) {
                        const commentEl = modal.querySelector(`[data-comment-id="${commentId}"]`);
                        if (commentEl) {
                            commentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            commentEl.style.transition = 'box-shadow 0.3s ease';
                            commentEl.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.25)';
                            setTimeout(() => { commentEl.style.boxShadow = 'none'; }, 3500);
                            clearInterval(iv);
                        }
                    }
                    if (tries > 30) clearInterval(iv);
                }, 200);
                return;
            }
        } catch (e) {
            console.error('openPublicationFromNotification error:', e);
        }

        // Fallback: navigate to page with query params
        window.location = '/reportes/publicaciones?publication=' + publicationId + (commentId ? ('&comment=' + commentId) : '');
    }
    
    // === SISTEMA DE FILTRADO POR PESTAÑAS ===
    const tabButtons = document.querySelectorAll('.tab-filter');
    const publicationCards = document.querySelectorAll('.publication-card');
    
    // Función para actualizar contador de resultados
    function updateResultsCounter() {
        const visibleCards = document.querySelectorAll('.publication-card:not([style*="display: none"])');
        const counterElement = document.querySelector('.text-xs.text-gray-600.font-lora');
        if (counterElement) {
            const countSpan = counterElement.querySelector('.font-semibold');
            if (countSpan) {
                countSpan.textContent = visibleCards.length;
            }
        }
    }
    
    // Función para filtrar publicaciones
    function filterPublications(tipo) {
        console.log('🔍 Filtrando por tipo:', tipo);
        
        publicationCards.forEach(card => {
            const cardTipo = card.getAttribute('data-publication-tipo');
            
            if (tipo === 'todos') {
                card.style.display = '';
            } else {
                if (cardTipo === tipo) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            }
        });
        
        // Actualizar contador
        updateResultsCounter();
    }
    
    // Función para actualizar estilos de pestañas activas
    function updateActiveTab(activeButton) {
        // Definir clases exactas para estado activo/inactivo para evitar mezclas de hover conflictivos
        const activeClasses = 'tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-[#404041] text-white border border-b-0 border-gray-300 transition-all duration-200 hover:bg-[#2a2a2a]';
        const inactiveClasses = 'tab-filter px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100';

        tabButtons.forEach(btn => {
            if (btn === activeButton) {
                btn.className = activeClasses;
            } else {
                btn.className = inactiveClasses;
            }
        });
    }
    
    // Agregar event listeners a las pestañas
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            filterPublications(tipo);
            updateActiveTab(this);
        });
    });
    
    console.log('=== SISTEMA DE FILTRADO INICIALIZADO ===');

    // === SISTEMA DE COMENTARIOS ===
    document.addEventListener('click', function(e) {
        // Enviar comentario
        if (e.target.closest('.enviar-comentario')) {
            e.preventDefault();
            console.log('🔔 Click en enviar comentario');
            
            const button = e.target.closest('.enviar-comentario');
            const modal = button.closest('[id^="modal"]');
            
            if (!modal) {
                console.error('❌ No se encontró el modal');
                return;
            }
            
            const textarea = modal.querySelector('.nuevo-comentario');
            const publicationId = modal.dataset.publicationId;
            const comment = textarea.value.trim();

            console.log('📝 Datos:', { publicationId, comment: comment.substring(0, 50) });

            if (!comment) {
                alert('Por favor escribe un comentario');
                return;
            }
            
            if (!publicationId) {
                alert('Error: ID de publicación no encontrado');
                return;
            }

            // Deshabilitar botón mientras se envía
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Enviando...';

            fetch(`/reportes/${publicationId}/comentarios`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment })
            })
            .then(response => {
                console.log('📡 Respuesta recibida:', response.status, response.statusText);
                // Si no es OK, leer el body como texto para más detalles y lanzar error
                if (!response.ok) {
                    return response.text().then(text => {
                        const short = text.length > 100 ? text.substring(0, 100) + '...' : text;
                        throw new Error(`HTTP ${response.status}: ${short}`);
                    });
                }

                // Intentar parsear JSON, pero si el content-type no es JSON, leer como texto y fallar con mensaje claro
                const contentType = response.headers.get('content-type') || '';
                if (contentType.indexOf('application/json') === -1) {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Respuesta inválida del servidor: ' + (text || response.statusText));
                        }
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('✅ Datos recibidos:', data);
                
                if (data.success) {
                    // Limpiar textarea
                    textarea.value = '';
                    textarea.style.height = 'auto';
                    
                    // Obtener el contenedor de comentarios
                    const comentariosContainer = modal.querySelector('.modal-comentarios');
                    
                    // Obtener los comentarios actuales almacenados en el modal
                    // (los almacenamos cuando se abre el modal en fillFilesAndComments)
                    let comentariosActuales = [];
                    try {
                        const comentariosData = modal.dataset.comentariosJson;
                        if (comentariosData) {
                            comentariosActuales = JSON.parse(comentariosData);
                        }
                    } catch (e) {
                        console.warn('No se pudieron cargar comentarios previos del dataset, continuando...');
                        comentariosActuales = [];
                    }

                    // Agregar el nuevo comentario al final (nueva entrada = más reciente)
                    comentariosActuales.push(data.comment);
                    
                    // Actualizar el dataset con los nuevos comentarios
                    modal.dataset.comentariosJson = JSON.stringify(comentariosActuales);

                    // Re-renderizar todos los comentarios
                    renderComments(comentariosContainer, comentariosActuales);
                    
                    // Remove unread dot for this publication (author has read their own comment)
                    try {
                        const card = document.querySelector(`.publication-card[data-publication-id="${publicationId}"]`);
                        if (card) {
                            const dot = card.querySelector('.absolute.-top-0\\.5.-right-0\\.5');
                            if (dot) dot.remove();
                        }
                    } catch (e) {
                        // ignore
                    }
                    
                    console.log('✅ Comentario agregado exitosamente');
                } else {
                    alert(data.message || 'Error al enviar el comentario');
                }
            })
            .catch(error => {
                console.error('❌ Error enviando comentario:', error);
                // Mostrar mensaje más informativo al usuario cuando sea posible
                alert('Error al enviar el comentario. ' + (error.message || 'Por favor, intenta de nuevo.'));
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-paper-plane text-xs"></i> Enviar';
            });
        }

        // Nota: la opción de eliminar comentarios está deshabilitada en la interfaz por ahora.
    });

    // Auto-resize textarea y habilitar/deshabilitar botón
    document.querySelectorAll('.nuevo-comentario').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            
            const button = this.closest('.flex').querySelector('.enviar-comentario');
            button.disabled = !this.value.trim();
        });
    });

    // === SISTEMA DE DESCARGA DE ARCHIVOS ===
    document.addEventListener('click', function(e) {
        // Descargar archivo individual
        if (e.target.closest('.descargar-archivo')) {
            e.preventDefault();
            const button = e.target.closest('.descargar-archivo');
            const fileId = button.dataset.fileId;
            
            if (fileId && fileId !== 'null') {
                window.location.href = `/reportes/file/${fileId}/download`;
            } else {
                alert('No se pudo identificar el archivo para descargar');
            }
        }
    });

    console.log('=== SISTEMA DE COMENTARIOS Y DESCARGAS INICIALIZADO ===');
});
</script>

    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/reportes/publicaciones.blade.php ENDPATH**/ ?>