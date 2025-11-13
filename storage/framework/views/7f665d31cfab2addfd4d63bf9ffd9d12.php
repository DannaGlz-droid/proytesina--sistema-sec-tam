<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'jurisdictions' => null,
    'municipalities' => null,
    'causes' => null,
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
    'jurisdictions' => null,
    'municipalities' => null,
    'causes' => null,
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

    <!-- Fechas (usando la lógica del demo que funciona) -->
    <form id="filters-form" method="GET" action="<?php echo e(route('statistic.data')); ?>">
    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'calendar-alt','titulo' => 'Fecha de defunción','abierto' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'calendar-alt','titulo' => 'Fecha de defunción','abierto' => 'true']); ?>
        <div class="space-y-2">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                <select id="dateRange" name="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="all">Todas</option>
                    <option value="year">Año específico</option>
                    <option value="month">Mes específico</option>
                    <option value="multiple-months">Múltiples meses</option>
                    <option value="quarter">Trimestre</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>

            <!-- Selectores condicionales: empezamos con display:none (como el demo que funciona) -->
                <div class="filter-group" id="yearSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Año de defunción:</label>
                <?php $currentYear = now()->year; $minYear = 1950; ?>
                <input type="number" id="year" name="year" min="<?php echo e($minYear); ?>" max="<?php echo e($currentYear); ?>" value="<?php echo e(request('year')); ?>" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" placeholder="Ej: <?php echo e($currentYear); ?>">
            </div>

            <div class="filter-group" id="monthSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Mes de defunción:</label>
                <input type="hidden" id="monthHidden" name="month" value="<?php echo e(request('month')); ?>">
                <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                    <?php
                        $months = [
                            '01' => 'Ene','02' => 'Feb','03' => 'Mar','04' => 'Abr','05' => 'May','06' => 'Jun',
                            '07' => 'Jul','08' => 'Ago','09' => 'Sep','10' => 'Oct','11' => 'Nov','12' => 'Dic'
                        ];
                        $selectedMonths = (array) request('selectedMonths', []);
                        $singleMonth = request('month');
                    ?>
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mval => $mlabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <input type="checkbox" id="month-<?php echo e($mval); ?>" name="selectedMonths[]" class="month-checkbox" value="<?php echo e($mval); ?>" <?php echo e(in_array($mval, $selectedMonths) || ($singleMonth === $mval) ? 'checked' : ''); ?>>
                            <label for="month-<?php echo e($mval); ?>" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200"><?php echo e($mlabel); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- multiple months selector removed: monthSelector grid is used for both single and multi modes -->

            <div class="filter-group" id="quarterSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Trimestre de defunción:</label>
                <select id="quarter" name="quarter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar trimestre</option>
                    <option value="1">Q1 (Ene-Mar)</option>
                    <option value="2">Q2 (Abr-Jun)</option>
                    <option value="3">Q3 (Jul-Sep)</option>
                    <option value="4">Q4 (Oct-Dic)</option>
                </select>
            </div>

            <div id="customRangeSelector" style="display: none;">
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Desde (fecha de defunción):</label>
                    <input type="date" id="startDate" name="startDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Hasta (fecha de defunción):</label>
                    <input type="date" id="endDate" name="endDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
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

    <!-- Resto de secciones... (sin cambios funcionales, solo estilos y estructura) -->
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
            <select id="jurisdiccion" name="jurisdiccion" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todas</option>
                <?php if($jurisdictions): ?>
                    <?php $__currentLoopData = $jurisdictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($j->name); ?>" <?php echo e(request('jurisdiccion') === $j->name ? 'selected' : ''); ?>><?php echo e($j->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="norte">Jurisdicción Norte</option>
                    <option value="sur">Jurisdicción Sur</option>
                <?php endif; ?>
            </select>
        </div>

            <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Municipio:</label>
            <select id="municipio" name="municipio" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                <?php if($municipalities): ?>
                    <?php $__currentLoopData = $municipalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->name); ?>" <?php echo e(request('municipio') === $m->name ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="allende">Allende</option>
                    <option value="monterrey">Monterrey</option>
                <?php endif; ?>
            </select>
        </div>

            <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Municipio de defunción:</label>
            <select id="municipioDefuncion" name="municipioDefuncion" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                <?php if($municipalities): ?>
                    <?php $__currentLoopData = $municipalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->name); ?>" <?php echo e(request('municipioDefuncion') === $m->name ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="allende">Allende</option>
                    <option value="monterrey">Monterrey</option>
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

    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'users','titulo' => 'Demográficos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'users','titulo' => 'Demográficos']); ?>
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Sexo:</label>
            <select id="sexo" name="sexo" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                <option value="F" <?php echo e(request('sexo') === 'F' ? 'selected' : (request('sexo') === 'f' ? 'selected' : '')); ?>>Femenino</option>
                <option value="M" <?php echo e(request('sexo') === 'M' ? 'selected' : (request('sexo') === 'm' ? 'selected' : '')); ?>>Masculino</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Edad:</label>
            <input type="text" id="edad" name="edad" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" 
                   placeholder="Ej: 25 o 20-30 o 5,10,15">
            <div class="text-xs text-gray-500 mt-1">Edad específica, rango o múltiples valores separados por coma</div>
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

    <?php if (isset($component)) { $__componentOriginal05946ab4158a4a56cc8ba494d36225d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal05946ab4158a4a56cc8ba494d36225d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.seccion','data' => ['icono' => 'heartbeat','titulo' => 'Causas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.seccion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icono' => 'heartbeat','titulo' => 'Causas']); ?>
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Causa de defunción:</label>
            <select id="causa" name="causa" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todas</option>
                <?php if($causes): ?>
                    <?php $__currentLoopData = $causes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e(request('causa') == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="cardiopatia">Enfermedades del corazón</option>
                    <option value="cancer">Cáncer</option>
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
    <div class="mt-6 pt-4 border-t border-gray-300">
        <button type="submit" form="filters-form" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Inicializar colapsables (toggle de secciones con chevron y aria) ---
    document.querySelectorAll('.filter-section').forEach(section => {
        const header = section.querySelector('.filter-section-header');
        const content = section.querySelector('.filter-section-content');
        const chevron = header?.querySelector('.fa-chevron-down');

        if (!header || !content) return;

        // Asegura que header pueda recibir focus/keyboard si no es button
        if (header.getAttribute('role') === null && header.tagName.toLowerCase() !== 'button') {
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '0');
        }

        // Estado inicial (si el contenido tiene max-height inline o está abierto por defecto)
        const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px' && content.style.maxHeight !== '0';
        if (isOpen) {
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.opacity = '1';
            header.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        } else {
            // Si no está abierto explícitamente, colapsar
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            header.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.style.transform = 'rotate(-90deg)';
        }

        function toggleSection() {
            const opened = content.style.maxHeight && content.style.maxHeight !== '0px';
            if (opened) {
                // cerrar
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                header.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.style.transform = 'rotate(-90deg)';
            } else {
                // abrir
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                header.setAttribute('aria-expanded', 'true');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            // Recalcula alturas de secciones abiertas al terminar la animación
            setTimeout(() => {
                document.querySelectorAll('.filter-section-content').forEach(c => {
                    if (c.style.maxHeight && c.style.maxHeight !== '0px') {
                        c.style.maxHeight = c.scrollHeight + 'px';
                    }
                });
            }, 320);
        }

        header.addEventListener('click', toggleSection);
        header.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSection();
            }
        });
    });

    // Basado en el demo que funciona: toggling con display:block/none para los condicionales
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
                // reuse monthSelector grid for multiple months too
                if (monthSelector) monthSelector.style.display = 'block';
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

        // Si la sección padre es colapsable, forzamos recalcular su maxHeight (evita solapamientos)
        const sectionContent = dateRange.closest('.filter-section')?.querySelector('.filter-section-content');
        if (sectionContent && sectionContent.style.maxHeight && sectionContent.style.maxHeight !== '0px') {
            // recalcula con pequeño delay para que paint aplique
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

    // Spinner behavior for year input: if empty, set to current year on first interaction so arrows start from current year
    (function() {
        const yearInput = document.getElementById('year');
        const currentYear = <?php echo e($currentYear ?? now()->year); ?>;
        if (!yearInput) return;

        function ensureCurrentYear() {
            if (yearInput.value === '' || yearInput.value === null) {
                yearInput.value = currentYear;
            }
        }

    yearInput.addEventListener('focus', ensureCurrentYear, { once: true });
    yearInput.addEventListener('click', ensureCurrentYear, { once: true });
    yearInput.addEventListener('mousedown', ensureCurrentYear, { once: true });
    yearInput.addEventListener('touchstart', ensureCurrentYear, { once: true });
    })();

    // Mantener comportamiento del demo para labels de meses (input + label)
    document.querySelectorAll('.month-checkbox').forEach(cb => {
        const label = document.querySelector(`label[for="${cb.id}"]`);

        // click handler: enforce single-select when dateRange === 'month'
        cb.addEventListener('click', (e) => {
            const mode = dateRange?.value;
            if (mode === 'month') {
                // uncheck all other checkboxes
                document.querySelectorAll('.month-checkbox').forEach(other => {
                    if (other === cb) return;
                    other.checked = false;
                    const otherLabel = document.querySelector(`label[for="${other.id}"]`);
                    if (otherLabel) {
                        otherLabel.classList.remove('bg-[#611132]','text-white','border-[#611132]');
                        otherLabel.classList.add('bg-gray-100','border-gray-300');
                    }
                });

                // set the hidden month field to this value
                const monthHidden = document.getElementById('monthHidden');
                if (monthHidden) monthHidden.value = cb.checked ? cb.value : '';
            }

            // If in multiple-months mode, keep monthHidden cleared (controller uses selectedMonths[])
            if (mode === 'multiple-months') {
                const monthHidden = document.getElementById('monthHidden');
                if (monthHidden) monthHidden.value = '';
            }
        });

        cb.addEventListener('change', () => {
            if (!label) return;
            if (cb.checked) {
                label.classList.add('bg-[#611132]','text-white','border-[#611132]');
                label.classList.remove('bg-gray-100','border-gray-300');
            } else {
                label.classList.remove('bg-[#611132]','text-white','border-[#611132]');
                label.classList.add('bg-gray-100','border-gray-300');
            }

            // recalcular sección abierta si corresponde
            const secContent = cb.closest('.filter-section')?.querySelector('.filter-section-content');
            if (secContent && secContent.style.maxHeight && secContent.style.maxHeight !== '0px') {
                secContent.style.maxHeight = secContent.scrollHeight + 'px';
            }
        });
    });

    // Edad: legacy single input 'edad' is used; no operator UI

        // Limpiar filtros: usa lógica simple (como en demo)
    document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
        if (dateRange) dateRange.selectedIndex = 0;
        hideAll();

        // Reset other controls
        document.querySelectorAll('select:not(#dateRange), input[type="text"], input[type="number"], input[type="date"]').forEach(el => {
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else el.value = '';
        });

        document.querySelectorAll('.month-checkbox').forEach(cb => {
            cb.checked = false;
            const label = document.querySelector(`label[for="${cb.id}"]`);
            if (label) {
                label.classList.remove('bg-[#611132]','text-white','border-[#611132]');
                label.classList.add('bg-gray-100','border-gray-300');
            }
        });

        // Reajusta secciones abiertas
        document.querySelectorAll('.filter-section-content').forEach(content => {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });

        console.log('Filtros limpiados');
    });

    // Aplicar filtros (colección simple)
    document.getElementById('aplicarFiltros')?.addEventListener('click', function() {
        const filtros = {
            dateRange: dateRange?.value,
            year: document.getElementById('year')?.value,
            month: document.getElementById('month')?.value,
            selectedMonths: Array.from(document.querySelectorAll('.month-checkbox:checked')).map(i => i.value),
            quarter: document.getElementById('quarter')?.value,
            startDate: document.getElementById('startDate')?.value,
            endDate: document.getElementById('endDate')?.value,
            jurisdiccion: document.getElementById('jurisdiccion')?.value,
            municipio: document.getElementById('municipio')?.value,
            municipioDefuncion: document.getElementById('municipioDefuncion')?.value,
            sexo: document.getElementById('sexo')?.value,
            edad: document.getElementById('edad')?.value,
            causa: document.getElementById('causa')?.value
        };
        console.log('Aplicando filtros:', filtros);
        // aquí va la lógica real de filtrado / fetch
    });

    // Recalcula alturas de secciones abiertas al hacer resize (previene solapamiento)
    window.addEventListener('resize', () => {
        document.querySelectorAll('.filter-section-content').forEach(content => {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
});
</script>

<style>
/* Tomé la organización y estilos del demo que ya funciona y los adapté a tu componente */
.filter-section { margin-bottom: 0.5rem; }
.filter-group { margin-bottom: 0.75rem; }

/* months visuals (igual que demo) */
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

/* evitar gaps excesivos cuando mostramos/ocultamos: content flow natural */
.filter-section-content { position: relative; }

/* reduce gaps entre secciones en el panel */
.x-filtros-base-panel, .filters-panel { /* placeholder selectors if used */ }

/* chevron in seccion component already handles rotation; esta règle asegura transición */
.filter-section-header .fa-chevron-down { transition: transform 300ms ease; }
</style><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/filtros/defunciones.blade.php ENDPATH**/ ?>