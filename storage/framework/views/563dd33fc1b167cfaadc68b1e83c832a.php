<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'districts' => null,
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
    'districts' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

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
     <?php $__env->slot('headerActions', null, []); ?> 
        <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
            <i class="fas fa-redo text-xs"></i>
            Limpiar
        </button>
     <?php $__env->endSlot(); ?>

    <form id="filters-form" method="GET" action="<?php echo e(route('reportes.index')); ?>">
        <!-- Búsqueda -->
        <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'search','titulo' => 'Búsqueda','abierto' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'search','titulo' => 'Búsqueda','abierto' => 'true']); ?>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Por título o autor:</label>
                <input type="text" name="q" id="search" value="<?php echo e(request('q')); ?>" placeholder="Buscar..." 
                       class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
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

        <!-- Estado y Distrito -->
        <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'filter','titulo' => 'Filtros','abierto' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'filter','titulo' => 'Filtros','abierto' => 'true']); ?>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Estado:</label>
                <select name="status" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo e(request('status') === 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="aprobado" <?php echo e(request('status') === 'aprobado' ? 'selected' : ''); ?>>Aprobado</option>
                    <option value="rechazado" <?php echo e(request('status') === 'rechazado' ? 'selected' : ''); ?>>Rechazado</option>
                </select>
            </div>

            <div class="filter-group mt-3">
                <label class="block text-xs text-gray-600 font-lora mb-1">Distrito:</label>
                <select id="distrito" name="district_id" class="tomselect-select">
                    <option value="">Todos los distritos</option>
                    <?php if($districts): ?>
                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($district->id); ?>" <?php echo e(request('district_id') == $district->id ? 'selected' : ''); ?>><?php echo e($district->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

        <!-- Período y Ordenar -->
        <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'calendar-alt','titulo' => 'Ordenamiento']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'calendar-alt','titulo' => 'Ordenamiento']); ?>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Período:</label>
                <select name="date_filter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="">Todas las fechas</option>
                    <option value="hoy" <?php echo e(request('date_filter') === 'hoy' ? 'selected' : ''); ?>>Hoy</option>
                    <option value="semana" <?php echo e(request('date_filter') === 'semana' ? 'selected' : ''); ?>>Esta semana</option>
                    <option value="mes" <?php echo e(request('date_filter') === 'mes' ? 'selected' : ''); ?>>Este mes</option>
                    <option value="3meses" <?php echo e(request('date_filter') === '3meses' ? 'selected' : ''); ?>>Últimos 3 meses</option>
                    <option value="anio" <?php echo e(request('date_filter') === 'anio' ? 'selected' : ''); ?>>Este año</option>
                </select>
            </div>

            <div class="filter-group mt-3">
                <label class="block text-xs text-gray-600 font-lora mb-1">Ordenar por:</label>
                <select name="order_by" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="updated_at:desc" <?php echo e(request('order_by', 'updated_at:desc') === 'updated_at:desc' ? 'selected' : ''); ?>>Última actualización (recientes)</option>
                    <option value="updated_at:asc" <?php echo e(request('order_by') === 'updated_at:asc' ? 'selected' : ''); ?>>Última actualización (antiguos)</option>
                    <option value="created_at:desc" <?php echo e(request('order_by') === 'created_at:desc' ? 'selected' : ''); ?>>Fecha creación (recientes)</option>
                    <option value="created_at:asc" <?php echo e(request('order_by') === 'created_at:asc' ? 'selected' : ''); ?>>Fecha creación (antiguos)</option>
                    <option value="titulo:asc" <?php echo e(request('order_by') === 'titulo:asc' ? 'selected' : ''); ?>>Título (A → Z)</option>
                    <option value="titulo:desc" <?php echo e(request('order_by') === 'titulo:desc' ? 'selected' : ''); ?>>Título (Z → A)</option>
                    <option value="usuario:asc" <?php echo e(request('order_by') === 'usuario:asc' ? 'selected' : ''); ?>>Usuario (A → Z)</option>
                    <option value="usuario:desc" <?php echo e(request('order_by') === 'usuario:desc' ? 'selected' : ''); ?>>Usuario (Z → A)</option>
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

        <!-- Botón de aplicar filtros (opcional en la base) -->
        <div class="mt-4">
            <button type="submit" class="w-full bg-[#611132] text-white text-xs font-semibold py-2 rounded-lg hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i>
                Aplicar Filtros
            </button>
        </div>
    </form>

    <!-- JavaScript para limpiar filtros -->
    <script>
        document.getElementById('limpiarFiltros')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('filters-form').reset();
            
            // Reiniciar TomSelect si existe
            if (window.tom_distrito) {
                tom_distrito.clearOptions();
                tom_distrito.clear();
            }
            
            // Redirigir a la ruta sin parámetros
            window.location.href = '<?php echo e(route('reportes.index')); ?>';
        });
    </script>
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
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/filtros/reportes.blade.php ENDPATH**/ ?>