<?php if (isset($component)) { $__componentOriginalfcb8ead5baf63da144e640a174656b50 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfcb8ead5baf63da144e640a174656b50 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.base','data' => ['titulo' => 'Filtros']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['titulo' => 'Filtros']); ?>
    
    <form id="filtersForm" method="GET" action="<?php echo e(route('user.user-gestion')); ?>">
         <?php $__env->slot('headerActions', null, []); ?> 
            <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
                <i class="fas fa-redo text-xs"></i>
                Limpiar
            </button>
         <?php $__env->endSlot(); ?>

    <!-- Estado -->
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'user-check','titulo' => 'Estado']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'user-check','titulo' => 'Estado']); ?>
        <div class="space-y-2 estado-inner">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Estado de cuenta:</label>
                <select name="is_active" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" id="estadoCuenta">
                    <option value="" <?php echo e(request('is_active') === null || request('is_active') === '' ? 'selected' : ''); ?>>Todos</option>
                    <option value="1" <?php echo e(request('is_active') === '1' ? 'selected' : ''); ?>>Activo</option>
                    <option value="0" <?php echo e(request('is_active') === '0' ? 'selected' : ''); ?>>Inactivo</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Última sesión:</label>
                <select name="last_session" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="ultimaSesion">
                    <option value="" <?php echo e(request('last_session') === null || request('last_session') === '' ? 'selected' : ''); ?>>Cualquier momento</option>
                    <option value="today" <?php echo e(request('last_session') === 'today' ? 'selected' : ''); ?>>Hoy</option>
                    <option value="7" <?php echo e(request('last_session') === '7' ? 'selected' : ''); ?>>Últimos 7 días</option>
                    <option value="30" <?php echo e(request('last_session') === '30' ? 'selected' : ''); ?>>Últimos 30 días</option>
                    <option value="90" <?php echo e(request('last_session') === '90' ? 'selected' : ''); ?>>Últimos 90 días</option>
                    <option value="never" <?php echo e(request('last_session') === 'never' ? 'selected' : ''); ?>>Nunca</option>
                </select>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $attributes = $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $component = $__componentOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>

    <!-- Fechas (match con estadisticas) -->
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'calendar-alt','titulo' => 'Fecha alta']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'calendar-alt','titulo' => 'Fecha alta']); ?>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                <select name="date_range" id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="all" <?php echo e(request('date_range', 'all') === 'all' ? 'selected' : ''); ?>>Todas las fechas</option>
                    <option value="year" <?php echo e(request('date_range') === 'year' ? 'selected' : ''); ?>>Año específico</option>
                    <option value="month" <?php echo e(request('date_range') === 'month' ? 'selected' : ''); ?>>Mes específico</option>
                    <option value="multiple-months" <?php echo e(request('date_range') === 'multiple-months' ? 'selected' : ''); ?>>Múltiples meses</option>
                    <option value="quarter" <?php echo e(request('date_range') === 'quarter' ? 'selected' : ''); ?>>Trimestre</option>
                    <option value="custom" <?php echo e(request('date_range') === 'custom' ? 'selected' : ''); ?>>Personalizado</option>
                </select>
            </div>

            <div class="filter-group year-selector-group" id="yearSelector" style="display: <?php echo e(request('date_range') === 'year' ? 'block' : 'none'); ?>;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Año:</label>
                <select name="year" id="year" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                    <option value="">Seleccionar año</option>
                    <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="filter-group month-selector-group" id="monthSelector" style="display: <?php echo e(request('date_range') === 'month' ? 'block' : 'none'); ?>;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Mes:</label>
                <select name="month" id="month" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                    <option value="">Seleccionar mes</option>
                    <option value="01" <?php echo e(request('month') === '01' ? 'selected' : ''); ?>>Enero</option>
                    <option value="02" <?php echo e(request('month') === '02' ? 'selected' : ''); ?>>Febrero</option>
                    <option value="03" <?php echo e(request('month') === '03' ? 'selected' : ''); ?>>Marzo</option>
                    <option value="04" <?php echo e(request('month') === '04' ? 'selected' : ''); ?>>Abril</option>
                    <option value="05" <?php echo e(request('month') === '05' ? 'selected' : ''); ?>>Mayo</option>
                    <option value="06" <?php echo e(request('month') === '06' ? 'selected' : ''); ?>>Junio</option>
                    <option value="07" <?php echo e(request('month') === '07' ? 'selected' : ''); ?>>Julio</option>
                    <option value="08" <?php echo e(request('month') === '08' ? 'selected' : ''); ?>>Agosto</option>
                    <option value="09" <?php echo e(request('month') === '09' ? 'selected' : ''); ?>>Septiembre</option>
                    <option value="10" <?php echo e(request('month') === '10' ? 'selected' : ''); ?>>Octubre</option>
                    <option value="11" <?php echo e(request('month') === '11' ? 'selected' : ''); ?>>Noviembre</option>
                    <option value="12" <?php echo e(request('month') === '12' ? 'selected' : ''); ?>>Diciembre</option>
                </select>
            </div>

            <div class="filter-group multiple-months-group" id="multipleMonthsSelector" style="display: <?php echo e(request('date_range') === 'multiple-months' ? 'block' : 'none'); ?>;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Meses:</label>
                <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                    <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $mm = str_pad($m,2,'0',STR_PAD_LEFT); ?>
                        <div>
                            <input type="checkbox" id="month-<?php echo e($mm); ?>" name="months[]" class="month-checkbox" value="<?php echo e($mm); ?>" <?php echo e(is_array(request('months')) && in_array($mm, request('months')) ? 'checked' : ''); ?>>
                            <label for="month-<?php echo e($mm); ?>" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200"><?php echo e(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$m-1]); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="filter-group quarter-selector-group" id="quarterSelector" style="display: <?php echo e(request('date_range') === 'quarter' ? 'block' : 'none'); ?>;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Trimestre:</label>
                <select name="quarter" id="quarter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                    <option value="">Seleccionar trimestre</option>
                    <option value="1" <?php echo e(request('quarter') === '1' ? 'selected' : ''); ?>>Q1 (Ene-Mar)</option>
                    <option value="2" <?php echo e(request('quarter') === '2' ? 'selected' : ''); ?>>Q2 (Abr-Jun)</option>
                    <option value="3" <?php echo e(request('quarter') === '3' ? 'selected' : ''); ?>>Q3 (Jul-Sep)</option>
                    <option value="4" <?php echo e(request('quarter') === '4' ? 'selected' : ''); ?>>Q4 (Oct-Dic)</option>
                </select>
            </div>

            <div id="customRangeSelector" class="custom-range-group" style="display: <?php echo e(request('date_range') === 'custom' ? 'block' : 'none'); ?>;">
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                    <input type="date" name="date_from" id="startDate" value="<?php echo e(request('date_from')); ?>" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                </div>
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Hasta:</label>
                    <input type="date" name="date_to" id="endDate" value="<?php echo e(request('date_to')); ?>" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                </div>
            </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $attributes = $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $component = $__componentOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>

    <!-- Cargo -->
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'briefcase','titulo' => 'Cargo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'briefcase','titulo' => 'Cargo']); ?>
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Cargo:</label>
            <select name="position_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="cargo">
                <option value="" <?php echo e(request('position_id') === null || request('position_id') === '' ? 'selected' : ''); ?>>Todos los cargos</option>
                <?php if(isset($positions) && $positions->isNotEmpty()): ?>
                    <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pos->id); ?>" <?php echo e((string) request('position_id') === (string) $pos->id ? 'selected' : ''); ?>><?php echo e($pos->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option>Administrador</option>
                    <option>Coordinador</option>
                    <option>Supervisor</option>
                    <option>Analista</option>
                    <option>Técnico</option>
                    <option>Operador</option>
                <?php endif; ?>
            </select>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $attributes = $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $component = $__componentOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>

    <!-- Ubicación -->
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'map-marker-alt','titulo' => 'Ubicación']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'map-marker-alt','titulo' => 'Ubicación']); ?>
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción:</label>
            <select name="jurisdiction_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="jurisdiccion">
                <option value="" <?php echo e(request('jurisdiction_id') === null || request('jurisdiction_id') === '' ? 'selected' : ''); ?>>Todas</option>
                <?php if(isset($jurisdictions) && $jurisdictions->isNotEmpty()): ?>
                    <?php $__currentLoopData = $jurisdictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($j->id); ?>" <?php echo e((string) request('jurisdiction_id') === (string) $j->id ? 'selected' : ''); ?>><?php echo e($j->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option>Jurisdicción Sanitaria I</option>
                    <option>Jurisdicción Sanitaria II</option>
                    <option>Jurisdicción Sanitaria III</option>
                <?php endif; ?>
            </select>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $attributes = $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $component = $__componentOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>

    <!-- Rol -->
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'user-tag','titulo' => 'Rol']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'user-tag','titulo' => 'Rol']); ?>
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Tipo:</label>
            <select name="role_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="rol">
                <option value="" <?php echo e(request('role_id') === null || request('role_id') === '' ? 'selected' : ''); ?>>Todos</option>
                <?php if(isset($roles) && $roles->isNotEmpty()): ?>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($r->id); ?>" <?php echo e((string) request('role_id') === (string) $r->id ? 'selected' : ''); ?>><?php echo e($r->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option>Administrador</option>
                    <option>Editor</option>
                    <option>Usuario</option>
                    <option>Invitado</option>
                <?php endif; ?>
            </select>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $attributes = $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__attributesOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3)): ?>
<?php $component = $__componentOriginal05946ab4158a4a56cc8ba494d36225d3; ?>
<?php unset($__componentOriginal05946ab4158a4a56cc8ba494d36225d3); ?>
<?php endif; ?>

    <!-- Botón Filtrar -->
    <div class="mt-4 pt-4 border-t border-gray-300">
            <button type="submit" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2" id="aplicarFiltros">
                <i class="fas fa-filter text-sm"></i>
                Aplicar Filtros
            </button>
        </div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfcb8ead5baf63da144e640a174656b50)): ?>
<?php $attributes = $__attributesOriginalfcb8ead5baf63da144e640a174656b50; ?>
<?php unset($__attributesOriginalfcb8ead5baf63da144e640a174656b50); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfcb8ead5baf63da144e640a174656b50)): ?>
<?php $component = $__componentOriginalfcb8ead5baf63da144e640a174656b50; ?>
<?php unset($__componentOriginalfcb8ead5baf63da144e640a174656b50); ?>
<?php endif; ?>

<style>
/* Consolidated spacing rules for usuarios filters
   - kept intentionally small gaps to match `estadisticas` behavior
   - avoid multiple conflicting blocks that produced larger gaps */

/* Sections */
.filter-section { margin-bottom: 0 !important; }
.filter-section + .filter-section { margin-top: 0.25rem !important; }

/* Controls */
.filter-group { margin-bottom: 0.375rem; }
.filter-section .filter-group:last-child,
.filter-group.last-visible { margin-bottom: 0.5rem !important; }

/* Estado inner: slightly smaller internal gap but same last-child spacing */
.estado-inner .filter-group { margin-bottom: 0.25rem; }
.estado-inner .filter-group:last-child { margin-bottom: 0.5rem; }

/* ESPACIO ESPECÍFICO PARA TODOS LOS SELECTORES DE FECHA - CORRECCIÓN DEL PROBLEMA */
.year-selector-group,
.month-selector-group, 
.quarter-selector-group,
.multiple-months-group,
.custom-range-group { 
    margin-bottom: 0.5rem !important; 
}

/* Espacio adicional para el contenedor de meses */
.months-container { 
    margin-bottom: 0.5rem !important; 
}

/* months visuals */
.month-checkbox { display: none; }
.month-label {
    display: block;
    padding: 6px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
    cursor: pointer;
    font-size: 12px;
}
.month-checkbox:checked + .month-label {
    background: #611132;
    color: white;
    border-color: #611132;
}

/* chevron transition */
.filter-section-header .fa-chevron-down { transition: transform 300ms ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date-range toggles
    const dateRange = document.getElementById('dateRange');
    const yearSelector = document.getElementById('yearSelector');
    const monthSelector = document.getElementById('monthSelector');
    const multipleMonthsSelector = document.getElementById('multipleMonthsSelector');
    const quarterSelector = document.getElementById('quarterSelector');
    const customRangeSelector = document.getElementById('customRangeSelector');

    function hideAll() {
        [yearSelector, monthSelector, multipleMonthsSelector, quarterSelector, customRangeSelector].forEach(el => {
            if (!el) return;
            el.style.display = 'none';
        });
    }

    function showFor(value) {
        hideAll();
        switch(value) {
            case 'year':
                if (yearSelector) yearSelector.style.display = 'block';
                break;
            case 'month':
                if (yearSelector) yearSelector.style.display = 'block';
                if (monthSelector) monthSelector.style.display = 'block';
                break;
            case 'multiple-months':
                if (yearSelector) yearSelector.style.display = 'block';
                if (multipleMonthsSelector) multipleMonthsSelector.style.display = 'block';
                break;
            case 'quarter':
                if (yearSelector) yearSelector.style.display = 'block';
                if (quarterSelector) quarterSelector.style.display = 'block';
                break;
            case 'custom':
                if (customRangeSelector) customRangeSelector.style.display = 'block';
                break;
            default:
                // all
                break;
        }

        const section = dateRange?.closest('.filter-section');
        const sectionContent = section?.querySelector('.filter-section-content');

        // Asegurar espaciado consistente para el último elemento visible
        if (section) {
            const groups = Array.from(section.querySelectorAll('.filter-group'));
            const visible = groups.filter(g => window.getComputedStyle(g).display !== 'none');
            groups.forEach(g => g.classList.remove('last-visible'));
            if (visible.length) {
                visible[visible.length - 1].classList.add('last-visible');
            }
            
            // Asegurar espaciado para todos los selectores
            const selectors = section.querySelectorAll('.year-selector-group, .month-selector-group, .quarter-selector-group, .multiple-months-group, .custom-range-group');
            selectors.forEach(selector => {
                if (window.getComputedStyle(selector).display !== 'none') {
                    selector.style.marginBottom = '0.5rem';
                }
            });
        }

        if (sectionContent && sectionContent.style.maxHeight && sectionContent.style.maxHeight !== '0px') {
            setTimeout(() => {
                sectionContent.style.maxHeight = sectionContent.scrollHeight + 'px';
            }, 10);
        }
    }

    if (dateRange) {
        dateRange.addEventListener('change', function() {
            showFor(this.value);
        });
        // init
        showFor(dateRange.value);
    }

    // months checkbox visual behavior
    document.querySelectorAll('.month-checkbox').forEach(cb => {
        const label = document.querySelector(`label[for="${cb.id}"]`);
        cb.addEventListener('change', () => {
            if (!label) return;
            if (cb.checked) {
                label.classList.add('bg-[#611132]','text-white','border-[#611132]');
                label.classList.remove('bg-gray-100','border-gray-300');
            } else {
                label.classList.remove('bg-[#611132]','text-white','border-[#611132]');
                label.classList.add('bg-gray-100','border-gray-300');
            }

            const secContent = cb.closest('.filter-section')?.querySelector('.filter-section-content');
            if (secContent && secContent.style.maxHeight && secContent.style.maxHeight !== '0px') {
                secContent.style.maxHeight = secContent.scrollHeight + 'px';
            }
        });
    });

    // Limpiar filtros: resetear el formulario y reenviarlo (GET)
    const form = document.getElementById('filtersForm');
    document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
        if (!form) return;
        // reset selects/inputs
        form.querySelectorAll('select:not([name="date_range"]), input:not(.month-checkbox), input[type="checkbox"]').forEach(element => {
            if (!element.name) return;
            if (element.tagName === 'SELECT') element.selectedIndex = 0;
            else if (element.type === 'checkbox') element.checked = false;
            else element.value = '';
        });

        // reset date_range to default 'all'
        if (dateRange) dateRange.value = 'all';
        hideAll();

        // reset month labels
        document.querySelectorAll('.month-checkbox').forEach(cb => {
            cb.checked = false;
            const label = document.querySelector(`label[for="${cb.id}"]`);
            if (label) {
                label.classList.remove('bg-[#611132]','text-white','border-[#611132]');
                label.classList.add('bg-gray-100','border-gray-300');
            }
        });

        // submit form to update list
        form.submit();
    });

    // Aplicar filtros: enviar formulario (GET)
    document.getElementById('aplicarFiltros')?.addEventListener('click', function(e) {
        // leave default submit behavior (button type=submit) - but ensure form exists
        if (!form) e.preventDefault();
    });

    // Recalcula alturas de secciones abiertas al hacer resize
    window.addEventListener('resize', () => {
        document.querySelectorAll('.filter-section-content').forEach(content => {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
});
</script><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/filtros/usuarios.blade.php ENDPATH**/ ?>