
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
                <!-- FILTROS MEJORADOS - SERVER SIDE -->
                <form method="GET" action="<?php echo e(route('reportes.index')); ?>" class="mb-6">
                    <div class="flex flex-col lg:flex-row gap-3 items-start lg:items-end">
                        <!-- Buscar (misma línea que los filtros) -->
                        <div class="flex-1 min-w-0 lg:max-w-[320px]">
                            <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Buscar</label>
                            <div class="relative">
                                <input type="text" name="q" id="search" value="<?php echo e(request('q')); ?>" placeholder="Buscar por título o autor..." class="w-full border border-[#404041] rounded-lg pl-10 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!-- Estado -->
                        <div class="flex-1 min-w-0 lg:max-w-[160px]">
                            <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Estado</label>
                            <select name="status" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" <?php echo e(request('status') === 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                                <option value="aprobado" <?php echo e(request('status') === 'aprobado' ? 'selected' : ''); ?>>Aprobado</option>
                                <option value="rechazado" <?php echo e(request('status') === 'rechazado' ? 'selected' : ''); ?>>Rechazado</option>
                            </select>
                        </div>

                        <!-- Periodo de fechas predefinido -->
                        <div class="flex-1 min-w-0 lg:max-w-[160px]">
                            <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Periodo</label>
                            <select name="date_filter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                <option value="">Todas las fechas</option>
                                <option value="hoy" <?php echo e(request('date_filter') === 'hoy' ? 'selected' : ''); ?>>Hoy</option>
                                <option value="semana" <?php echo e(request('date_filter') === 'semana' ? 'selected' : ''); ?>>Esta semana</option>
                                <option value="mes" <?php echo e(request('date_filter') === 'mes' ? 'selected' : ''); ?>>Este mes</option>
                                <option value="3meses" <?php echo e(request('date_filter') === '3meses' ? 'selected' : ''); ?>>Últimos 3 meses</option>
                                <option value="anio" <?php echo e(request('date_filter') === 'anio' ? 'selected' : ''); ?>>Este año</option>
                            </select>
                        </div>

                        <!-- Ordenar (incluye dirección) -->
                        <div class="flex-1 min-w-0 lg:max-w-[180px]">
                            <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Ordenar</label>
                            <select name="order_by" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                <option value="created_at:desc" <?php echo e(request('order_by', 'created_at:desc') === 'created_at:desc' ? 'selected' : ''); ?>>Fecha (más recientes)</option>
                                <option value="created_at:asc" <?php echo e(request('order_by') === 'created_at:asc' ? 'selected' : ''); ?>>Fecha (más antiguos)</option>
                                <option value="titulo:asc" <?php echo e(request('order_by') === 'titulo:asc' ? 'selected' : ''); ?>>Título (A → Z)</option>
                                <option value="titulo:desc" <?php echo e(request('order_by') === 'titulo:desc' ? 'selected' : ''); ?>>Título (Z → A)</option>
                                <option value="usuario:asc" <?php echo e(request('order_by') === 'usuario:asc' ? 'selected' : ''); ?>>Usuario (A → Z)</option>
                                <option value="usuario:desc" <?php echo e(request('order_by') === 'usuario:desc' ? 'selected' : ''); ?>>Usuario (Z → A)</option>
                            </select>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex gap-2 mt-2 lg:mt-0">
                            <button type="submit" class="bg-[#611132] text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                                <i class="fas fa-filter text-xs"></i>
                                Aplicar
                            </button>
                            <a href="<?php echo e(route('reportes.index')); ?>" class="border border-[#404041] text-[#404041] px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                                <i class="fas fa-redo text-xs"></i>
                                Limpiar
                            </a>
                        </div>
                    </div>

                    <!-- Campo oculto para mantener el tipo seleccionado en pestañas -->
                    <input type="hidden" name="tipo" id="filter-tipo-input" value="<?php echo e(request('tipo', 'todos')); ?>">
                </form>

                <!-- CONTADOR DE RESULTADOS -->
                <div class="flex justify-between items-center mb-6">
                    <div class="text-xs text-gray-600 font-lora">
                        <span class="font-semibold text-[#404041]"><?php echo e($publications->total()); ?></span> resultados encontrados
                        <?php if($publications->hasPages()): ?>
                            <span class="text-gray-500">• Mostrando <?php echo e($publications->firstItem()); ?>-<?php echo e($publications->lastItem()); ?></span>
                        <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.publicacion-card','data' => ['dataPublicationId' => ''.e($pub->id).'','tipo' => $tipoDisplay,'titulo' => $pub->topic,'fecha' => $pub->publication_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),'usuario' => $uShort,'usuarioFull' => $uFull,'descripcion' => $activityInfo,'archivosCount' => $pub->files->count(),'badgeClass' => $badgeClass,'badgeBorderClass' => $badgeBorderClass,'hasComments' => count($pub->comentarios_json ?? []) > 0,'hasUnread' => $hasUnread,'status' => $pub->status,'approvedBy' => optional($pub->approver)->name,'rejectedBy' => optional($pub->rejector)->name,'rejectionReason' => $pub->rejection_reason,'dataPublicationTipo' => ''.e($pub->publication_type).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('publicacion-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-publication-id' => ''.e($pub->id).'','tipo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tipoDisplay),'titulo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->topic),'fecha' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->publication_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')),'usuario' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uShort),'usuario_full' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uFull),'descripcion' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityInfo),'archivosCount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->files->count()),'badgeClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeClass),'badgeBorderClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeBorderClass),'has-comments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($pub->comentarios_json ?? []) > 0),'has-unread' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasUnread),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->status),'approvedBy' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($pub->approver)->name),'rejectedBy' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($pub->rejector)->name),'rejectionReason' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pub->rejection_reason),'data-publication-tipo' => ''.e($pub->publication_type).'']); ?>

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
                                        data-status="<?php echo e($pub->status); ?>"
                                        data-approved-by="<?php echo e(optional($pub->approver)->name); ?>"
                                        data-rejected-by="<?php echo e(optional($pub->rejector)->name); ?>"
                                        data-rejection-reason="<?php echo e($pub->rejection_reason); ?>"
                                        <?php $__currentLoopData = $dataAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($key); ?>="<?php echo e($value); ?>"
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <?php
                                    $canEdit = $pub->canBeEditedBy(auth()->id());
                                    $isApproved = $pub->status === 'aprobado';
                                    $canDelete = (auth()->id() === $pub->user_id) || auth()->user()->isAdmin();
                                ?>
                                <?php if($canEdit): ?>
                                    <a href="<?php echo e($editRoute); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if($canDelete): ?>
                                    <button type="button" 
                                            class="eliminar-reporte w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" 
                                            title="Eliminar"
                                            data-publication-id="<?php echo e($pub->id); ?>"
                                            data-publication-title="<?php echo e($pub->topic); ?>"
                                            data-delete-url="<?php echo e(route('reportes.destroy', $pub)); ?>">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                <?php endif; ?>
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
        <?php if($publications->hasPages()): ?>
            <div class="flex justify-center mt-8">
                <?php echo e($publications->onEachSide(1)->links('vendor.pagination.custom')); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- INCLUIR EL COMPONENTE DEL MODAL DE ALCOHOLIMETRÍA -->
<!-- AL FINAL DEL ARCHIVO hola.blade.php, DESPUÉS de incluir los modales -->

    <!-- INCLUIR TODOS LOS COMPONENTES DE MODALES -->
<!-- Modal de rechazo -->
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-[#404041] font-lora">Rechazar Reporte</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <input type="hidden" id="reject-modal-publication-id">
        
        <p class="text-sm text-gray-600 mb-2 font-lora">Reporte: <span id="reject-modal-title" class="font-semibold"></span></p>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#404041] mb-2 font-lora">Motivo del rechazo *</label>
            <textarea 
                id="rejection-reason"
                rows="4"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent font-lora"
                placeholder="Explique brevemente por qué se rechaza este reporte..."
                maxlength="500"
                required></textarea>
            <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeRejectModal()" 
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300 font-lora">
                Cancelar
            </button>
            <button onclick="submitRejection()" 
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all duration-300 font-lora">
                <i class="fas fa-times mr-2"></i>Rechazar
            </button>
        </div>
    </div>
</div>

<!-- Modal de eliminación -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-[#404041] font-lora">Eliminar Publicación</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="delete-form" method="POST" action="">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            
            <input type="hidden" id="delete-modal-publication-id">
            
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-700 font-lora">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ¿Estás seguro de eliminar esta publicación? Esta acción no se puede deshacer fácilmente.
                </p>
            </div>
            
            <p class="text-sm text-gray-600 mb-4 font-lora">
                Publicación: <span id="delete-modal-title" class="font-semibold"></span>
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-[#404041] mb-2 font-lora">Motivo de eliminación (opcional)</label>
                <textarea 
                    id="deletion-reason"
                    name="deletion_reason"
                    rows="3"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent font-lora"
                    placeholder="Opcionalmente, explica por qué eliminas esta publicación..."
                    maxlength="500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300 font-lora">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all duration-300 font-lora">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

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

        // Llenar información de estado de aprobación
        const statusContainer = modal.querySelector('.modal-status-container');
        if (statusContainer) {
            const status = dataset.status || 'publicado';
            let statusHTML = '';
            
            if (status === 'aprobado') {
                const approvedBy = dataset.approvedBy || 'Administrador';
                statusHTML = `
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-lg border border-green-300">
                            <i class="fas fa-check-circle mr-1"></i>Aprobado
                        </span>
                        <span class="text-sm text-gray-600 font-lora">por ${approvedBy}</span>
                    </div>
                `;
            } else if (status === 'rechazado') {
                const rejectedBy = dataset.rejectedBy || 'Administrador';
                const rejectionReason = dataset.rejectionReason || 'No se proporcionó motivo';
                statusHTML = `
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-lg border border-red-300">
                                <i class="fas fa-times-circle mr-1"></i>Rechazado
                            </span>
                            <span class="text-sm text-gray-600 font-lora">por ${rejectedBy}</span>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-sm text-gray-700 font-lora"><strong>Motivo:</strong> ${rejectionReason}</p>
                        </div>
                    </div>
                `;
            } else {
                statusHTML = `
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-lg border border-yellow-300">
                            <i class="fas fa-clock mr-1"></i>Pendiente de revisión
                        </span>
                    </div>
                `;
            }
            
            statusContainer.innerHTML = statusHTML;
        }

        // Configurar botones de aprobación/rechazo
        const approvalContainer = modal.querySelector('.approval-buttons-container');
        const aprobarBtn = modal.querySelector('.aprobar-reporte');
        const rechazarBtn = modal.querySelector('.rechazar-reporte');
        
        if (approvalContainer && aprobarBtn && rechazarBtn) {
            const userIsAdminOrCoord = <?php echo e(auth()->user()->isAdmin() || auth()->user()->isCoordinator() ? 'true' : 'false'); ?>;
            const status = dataset.status || 'publicado';
            const publicationId = dataset.publicationId;
            const isOwner = dataset.isOwner === 'true';
            
            // Limpiar contenido previo del contenedor
            approvalContainer.innerHTML = '';
            
            // Si es rechazado y el usuario es el autor, mostrar botón de reenvío
            if (status === 'rechazado' && isOwner) {
                approvalContainer.style.display = 'flex';
                approvalContainer.innerHTML = `
                    <button onclick="resubmitReport(${publicationId})" class="reenviar-reporte px-6 py-2.5 bg-[#611132] text-white rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora whitespace-nowrap shadow-md hover:shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Reenviar para revisión
                    </button>
                `;
            }
            // Si es pendiente o rechazado y el usuario es Admin/Coordinador, mostrar botones de aprobar/rechazar
            else if (userIsAdminOrCoord && status !== 'aprobado') {
                approvalContainer.style.display = 'flex';
                approvalContainer.innerHTML = `
                    <button class="aprobar-reporte px-6 py-2.5 bg-[#75A84E] text-white rounded-lg text-sm font-semibold hover:bg-[#5a8339] transition-all duration-300 font-lora whitespace-nowrap shadow-md hover:shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i>Aprobar
                    </button>
                    <button class="rechazar-reporte px-6 py-2.5 bg-[#9D2449] text-white rounded-lg text-sm font-semibold hover:bg-[#7a1c38] transition-all duration-300 font-lora whitespace-nowrap shadow-md hover:shadow-lg">
                        <i class="fas fa-times-circle mr-2"></i>Rechazar
                    </button>
                `;
                
                // Re-attach event handlers
                const newAprobarBtn = approvalContainer.querySelector('.aprobar-reporte');
                const newRechazarBtn = approvalContainer.querySelector('.rechazar-reporte');
                
                if (newAprobarBtn) {
                    newAprobarBtn.onclick = function() {
                        approveReport(publicationId);
                    };
                }
                
                if (newRechazarBtn) {
                    newRechazarBtn.onclick = function() {
                        const titulo = dataset.titulo || 'este reporte';
                        showRejectModal(publicationId, titulo);
                    };
                }
                
                // Ocultar botón de aprobar si ya está aprobado
                aprobarBtn.style.display = status === 'aprobado' ? 'none' : 'inline-flex';
                // Ocultar botón de rechazar si ya está rechazado
                rechazarBtn.style.display = status === 'rechazado' ? 'none' : 'inline-flex';
            } else {
                approvalContainer.style.display = 'none';
            }
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
    
    // === SISTEMA DE FILTRADO POR PESTAÑAS (SERVER-SIDE) ===
    const tabButtons = document.querySelectorAll('.tab-filter');
    const filterForm = document.querySelector('form[action*="publicaciones"]');
    const tipoInput = document.getElementById('filter-tipo-input');
    
    // Función para actualizar estilos de pestañas activas
    function updateActiveTab(activeButton) {
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
    
    // Inicializar pestaña activa según parámetro URL
    const currentTipo = new URLSearchParams(window.location.search).get('tipo') || 'todos';
    tabButtons.forEach(btn => {
        if (btn.getAttribute('data-tipo') === currentTipo) {
            updateActiveTab(btn);
        }
    });
    
    // Agregar event listeners a las pestañas
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tipo = this.getAttribute('data-tipo');
            
            // Actualizar input oculto y enviar formulario
            tipoInput.value = tipo;
            filterForm.submit();
        });
    });
    
    console.log('=== SISTEMA DE FILTRADO SERVER-SIDE INICIALIZADO ===');

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

    // === SISTEMA DE APROBACIÓN/RECHAZO DE REPORTES ===
    window.approveReport = function(publicationId) {
        if (!confirm('¿Aprobar este reporte?')) return;
        
        fetch(`/reportes/${publicationId}/aprobar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Reporte aprobado exitosamente');
                location.reload();
            } else {
                alert(data.message || 'Error al aprobar el reporte');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al aprobar el reporte');
        });
    };

    // Variable para guardar el modal anterior
    let previousModalId = null;

    window.showRejectModal = function(publicationId, publicationTitle) {
        // Guardar cuál modal de detalles estaba abierto
        document.querySelectorAll('[id^="modal"]').forEach(modal => {
            if (modal.id !== 'reject-modal' && modal.id !== 'delete-modal' && !modal.classList.contains('hidden')) {
                previousModalId = modal.id;
                closeModal(modal.id);
            }
        });
        
        // Pequeño delay para que se cierre el modal anterior antes de abrir el de rechazo
        setTimeout(() => {
            document.getElementById('reject-modal-publication-id').value = publicationId;
            document.getElementById('reject-modal-title').textContent = publicationTitle;
            document.getElementById('reject-modal').classList.remove('hidden');
        }, 350);
    };

    window.closeRejectModal = function(returnToPrevious = true) {
        document.getElementById('reject-modal').classList.add('hidden');
        document.getElementById('rejection-reason').value = '';
        
        // Si se cancela, regresar al modal anterior
        if (returnToPrevious && previousModalId) {
            setTimeout(() => {
                showModal(previousModalId);
                previousModalId = null;
            }, 350);
        } else {
            previousModalId = null;
        }
    };

    window.submitRejection = function() {
        const publicationId = document.getElementById('reject-modal-publication-id').value;
        const reason = document.getElementById('rejection-reason').value.trim();
        
        if (!reason) {
            alert('Por favor ingrese una razón de rechazo');
            return;
        }
        
        fetch(`/reportes/${publicationId}/rechazar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ rejection_reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Reporte rechazado');
                closeRejectModal(false); // No regresar al anterior porque vamos a recargar
                location.reload();
            } else {
                alert(data.message || 'Error al rechazar el reporte');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al rechazar el reporte');
        });
    };

    window.resubmitReport = function(publicationId) {
        if (!confirm('¿Reenviar este reporte para revisión? Asegúrate de haber corregido los problemas mencionados.')) return;
        
        fetch(`/reportes/${publicationId}/reenviar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Reporte reenviado para revisión exitosamente');
                location.reload();
            } else {
                alert(data.message || 'Error al reenviar el reporte');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al reenviar el reporte');
        });
    };

    // === SISTEMA DE ELIMINACIÓN CON MODAL ===
    document.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-reporte')) {
            e.preventDefault();
            const button = e.target.closest('.eliminar-reporte');
            const publicationId = button.dataset.publicationId;
            const publicationTitle = button.dataset.publicationTitle;
            const deleteUrl = button.dataset.deleteUrl;
            
            showDeleteModal(publicationId, publicationTitle, deleteUrl);
        }
    });

    window.showDeleteModal = function(publicationId, publicationTitle, deleteUrl) {
        // Guardar cuál modal de detalles estaba abierto
        document.querySelectorAll('[id^="modal"]').forEach(modal => {
            if (modal.id !== 'reject-modal' && modal.id !== 'delete-modal' && !modal.classList.contains('hidden')) {
                previousModalId = modal.id;
                closeModal(modal.id);
            }
        });
        
        // Pequeño delay para que se cierre el modal anterior antes de abrir el de eliminación
        setTimeout(() => {
            document.getElementById('delete-modal-publication-id').value = publicationId;
            document.getElementById('delete-modal-title').textContent = publicationTitle;
            document.getElementById('delete-form').action = deleteUrl;
            document.getElementById('delete-modal').classList.remove('hidden');
        }, 350);
    };

    window.closeDeleteModal = function(returnToPrevious = true) {
        document.getElementById('delete-modal').classList.add('hidden');
        document.getElementById('deletion-reason').value = '';
        
        // Si se cancela, regresar al modal anterior
        if (returnToPrevious && previousModalId) {
            setTimeout(() => {
                showModal(previousModalId);
                previousModalId = null;
            }, 350);
        } else {
            previousModalId = null;
        }
    };

    // === Abrir modal desde área de archivos o icono de comentarios ===
    document.addEventListener('click', function(e) {
        // Si se hace click en el bloque de archivos, abrir el modal de detalles
        const archivosEl = e.target.closest('.archivos-open');
        if (archivosEl) {
            e.preventDefault();
            const card = archivosEl.closest('.publication-card');
            if (card) {
                const eyeBtn = card.querySelector('button[title="Ver detalles"]');
                if (eyeBtn) eyeBtn.click();
            }
            return;
        }

        // Si se hace click en el icono de comentarios, abrir modal y llevar a la sección de comentarios
        const commentsBtn = e.target.closest('.open-comments');
        if (commentsBtn) {
            e.preventDefault();
            const card = commentsBtn.closest('.publication-card');
            if (card) {
                const eyeBtn = card.querySelector('button[title="Ver detalles"]');
                if (eyeBtn) {
                    eyeBtn.click();
                    // Después de abrir el modal, desplazar/focar la sección de comentarios
                    setTimeout(() => {
                        const visibleModal = document.querySelector('[id^="modal"]:not(.hidden)');
                        if (visibleModal) {
                            const comentariosContainer = visibleModal.querySelector('.modal-comentarios');
                            const textarea = visibleModal.querySelector('.nuevo-comentario');
                            if (comentariosContainer) comentariosContainer.scrollTop = comentariosContainer.scrollHeight;
                            if (textarea) {
                                textarea.focus();
                                textarea.style.height = 'auto';
                                textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                            }
                        }
                    }, 450);
                }
            }
            return;
        }
    });
});
</script>

    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/reportes/publicaciones.blade.php ENDPATH**/ ?>