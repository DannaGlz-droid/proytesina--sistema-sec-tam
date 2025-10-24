<x-filtros.base titulo="Filtros">
    {{-- Enviamos por GET al listado para que el controlador filtre en servidor --}}
    <form id="filtersForm" method="GET" action="{{ route('user.user-gestion') }}">
        <x-slot name="headerActions">
            <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
                <i class="fas fa-redo text-xs"></i>
                Limpiar
            </button>
        </x-slot>

    <!-- Estado -->
    <x-filtros.seccion icono="user-check" titulo="Estado">
        <div class="space-y-2 estado-inner">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Estado de cuenta:</label>
                <select name="is_active" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" id="estadoCuenta">
                    <option value="" {{ request('is_active') === null || request('is_active') === '' ? 'selected' : '' }}>Todos</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Última sesión:</label>
                <select name="last_session" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="ultimaSesion">
                    <option value="" {{ request('last_session') === null || request('last_session') === '' ? 'selected' : '' }}>Cualquier momento</option>
                    <option value="today" {{ request('last_session') === 'today' ? 'selected' : '' }}>Hoy</option>
                    <option value="7" {{ request('last_session') === '7' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="30" {{ request('last_session') === '30' ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="90" {{ request('last_session') === '90' ? 'selected' : '' }}>Últimos 90 días</option>
                    <option value="never" {{ request('last_session') === 'never' ? 'selected' : '' }}>Nunca</option>
                </select>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Fechas (match con estadisticas) -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fecha alta">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                <select name="date_range" id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="all" {{ request('date_range', 'all') === 'all' ? 'selected' : '' }}>Todas las fechas</option>
                    <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>Año específico</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>Mes específico</option>
                    <option value="multiple-months" {{ request('date_range') === 'multiple-months' ? 'selected' : '' }}>Múltiples meses</option>
                    <option value="quarter" {{ request('date_range') === 'quarter' ? 'selected' : '' }}>Trimestre</option>
                    <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
            </div>

            <div class="filter-group year-selector-group" id="yearSelector" style="display: {{ request('date_range') === 'year' ? 'block' : 'none' }};">
                <label class="block text-xs text-gray-600 font-lora mb-1">Año:</label>
                <select name="year" id="year" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar año</option>
                    @for($y = date('Y'); $y >= date('Y')-5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="filter-group month-selector-group" id="monthSelector" style="display: {{ request('date_range') === 'month' ? 'block' : 'none' }};">
                <label class="block text-xs text-gray-600 font-lora mb-1">Mes:</label>
                <select name="month" id="month" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar mes</option>
                    <option value="01" {{ request('month') === '01' ? 'selected' : '' }}>Enero</option>
                    <option value="02" {{ request('month') === '02' ? 'selected' : '' }}>Febrero</option>
                    <option value="03" {{ request('month') === '03' ? 'selected' : '' }}>Marzo</option>
                    <option value="04" {{ request('month') === '04' ? 'selected' : '' }}>Abril</option>
                    <option value="05" {{ request('month') === '05' ? 'selected' : '' }}>Mayo</option>
                    <option value="06" {{ request('month') === '06' ? 'selected' : '' }}>Junio</option>
                    <option value="07" {{ request('month') === '07' ? 'selected' : '' }}>Julio</option>
                    <option value="08" {{ request('month') === '08' ? 'selected' : '' }}>Agosto</option>
                    <option value="09" {{ request('month') === '09' ? 'selected' : '' }}>Septiembre</option>
                    <option value="10" {{ request('month') === '10' ? 'selected' : '' }}>Octubre</option>
                    <option value="11" {{ request('month') === '11' ? 'selected' : '' }}>Noviembre</option>
                    <option value="12" {{ request('month') === '12' ? 'selected' : '' }}>Diciembre</option>
                </select>
            </div>

            <div class="filter-group multiple-months-group" id="multipleMonthsSelector" style="display: {{ request('date_range') === 'multiple-months' ? 'block' : 'none' }};">
                <label class="block text-xs text-gray-600 font-lora mb-1">Meses:</label>
                <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                    @foreach(range(1,12) as $m)
                        @php $mm = str_pad($m,2,'0',STR_PAD_LEFT); @endphp
                        <div>
                            <input type="checkbox" id="month-{{ $mm }}" name="months[]" class="month-checkbox" value="{{ $mm }}" {{ is_array(request('months')) && in_array($mm, request('months')) ? 'checked' : '' }}>
                            <label for="month-{{ $mm }}" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">{{ 
                                ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$m-1]
                            }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-group quarter-selector-group" id="quarterSelector" style="display: {{ request('date_range') === 'quarter' ? 'block' : 'none' }};">
                <label class="block text-xs text-gray-600 font-lora mb-1">Trimestre:</label>
                <select name="quarter" id="quarter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar trimestre</option>
                    <option value="1" {{ request('quarter') === '1' ? 'selected' : '' }}>Q1 (Ene-Mar)</option>
                    <option value="2" {{ request('quarter') === '2' ? 'selected' : '' }}>Q2 (Abr-Jun)</option>
                    <option value="3" {{ request('quarter') === '3' ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                    <option value="4" {{ request('quarter') === '4' ? 'selected' : '' }}>Q4 (Oct-Dic)</option>
                </select>
            </div>

            <div id="customRangeSelector" class="custom-range-group" style="display: {{ request('date_range') === 'custom' ? 'block' : 'none' }};">
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                    <input type="date" name="date_from" id="startDate" value="{{ request('date_from') }}" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Hasta:</label>
                    <input type="date" name="date_to" id="endDate" value="{{ request('date_to') }}" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
            </div>
    </x-filtros.seccion>

    <!-- Cargo -->
    <x-filtros.seccion icono="briefcase" titulo="Cargo">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Cargo:</label>
            <select name="position_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="cargo">
                <option value="" {{ request('position_id') === null || request('position_id') === '' ? 'selected' : '' }}>Todos los cargos</option>
                @if(isset($positions) && $positions->isNotEmpty())
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ (string) request('position_id') === (string) $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                @else
                    <option>Administrador</option>
                    <option>Coordinador</option>
                    <option>Supervisor</option>
                    <option>Analista</option>
                    <option>Técnico</option>
                    <option>Operador</option>
                @endif
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Ubicación -->
    <x-filtros.seccion icono="map-marker-alt" titulo="Ubicación">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción:</label>
            <select name="jurisdiction_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="jurisdiccion">
                <option value="" {{ request('jurisdiction_id') === null || request('jurisdiction_id') === '' ? 'selected' : '' }}>Todas</option>
                @if(isset($jurisdictions) && $jurisdictions->isNotEmpty())
                    @foreach($jurisdictions as $j)
                        <option value="{{ $j->id }}" {{ (string) request('jurisdiction_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                    @endforeach
                @else
                    <option>Jurisdicción Sanitaria I</option>
                    <option>Jurisdicción Sanitaria II</option>
                    <option>Jurisdicción Sanitaria III</option>
                @endif
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Rol -->
    <x-filtros.seccion icono="user-tag" titulo="Rol">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Tipo:</label>
            <select name="role_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="rol">
                <option value="" {{ request('role_id') === null || request('role_id') === '' ? 'selected' : '' }}>Todos</option>
                @if(isset($roles) && $roles->isNotEmpty())
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ (string) request('role_id') === (string) $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                @else
                    <option>Administrador</option>
                    <option>Editor</option>
                    <option>Usuario</option>
                    <option>Invitado</option>
                @endif
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Botón Filtrar -->
    <div class="mt-4 pt-4 border-t border-gray-300">
            <button type="submit" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2" id="aplicarFiltros">
                <i class="fas fa-filter text-sm"></i>
                Aplicar Filtros
            </button>
        </div>
    </form>
</x-filtros.base>

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
</script>