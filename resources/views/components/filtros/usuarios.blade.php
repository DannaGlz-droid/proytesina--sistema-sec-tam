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
                <select name="last_session" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="ultimaSesion">
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

    <!-- Fechas - Simplificado -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fecha alta">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
            <select name="date_range" id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                <option value="all" {{ request('date_range', 'all') === 'all' ? 'selected' : '' }}>Todas las fechas</option>
                <option value="7days" {{ request('date_range') === '7days' ? 'selected' : '' }}>Últimos 7 días</option>
                <option value="30days" {{ request('date_range') === '30days' ? 'selected' : '' }}>Últimos 30 días</option>
                <option value="90days" {{ request('date_range') === '90days' ? 'selected' : '' }}>Últimos 90 días</option>
                <option value="6months" {{ request('date_range') === '6months' ? 'selected' : '' }}>Últimos 6 meses</option>
                <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Personalizado</option>
            </select>
        </div>

        <div id="customRangeSelector" class="custom-range-group" style="display: {{ request('date_range') === 'custom' ? 'block' : 'none' }};">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                <input type="date" name="date_from" id="startDate" value="{{ request('date_from') }}" max="{{ now()->toDateString() }}" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
            </div>
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Hasta:</label>
                <input type="date" name="date_to" id="endDate" value="{{ request('date_to') }}" max="{{ now()->toDateString() }}" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Cargo -->
    <x-filtros.seccion icono="briefcase" titulo="Cargo">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Cargo:</label>
            <select name="position_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="cargo">
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
            <select name="jurisdiction_id" class="tomselect-select" id="jurisdiccion">
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
            <select name="role_id" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]" id="rol">
                <option value="" {{ request('role_id') === null || request('role_id') === '' ? 'selected' : '' }}>Todos</option>
                @php
                    // Only show canonical roles used by the application. This prevents showing
                    // lookup values such as 'Invitado' or generic 'Usuario' if they exist.
                    $allowed = ['Administrador', 'Coordinador', 'Operador'];
                    $filteredRoles = collect($roles ?? [])->filter(function($r) use ($allowed) {
                        return in_array(trim((string)($r->name ?? '')), $allowed, true);
                    });
                @endphp

                @if($filteredRoles->isNotEmpty())
                    @foreach($filteredRoles as $r)
                        <option value="{{ $r->id }}" {{ (string) request('role_id') === (string) $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                @else
                    <option>Administrador</option>
                    <option>Coordinador</option>
                    <option>Operador</option>
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

/* TomSelect Styles */
select.tomselect-select {
    position: absolute !important;
    left: -9999px !important;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    border: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    display: none !important;
}

select.tomselect-select::-ms-expand { display: none !important; }
select.tomselect-select { 
    background-image: none !important;
    visibility: hidden !important;
}

.ts-wrapper { 
    display: block; 
    width: 100%;
    position: relative;
    z-index: 9999 !important;
    margin: 0 !important;
    padding: 0 !important;
}

.ts-control {
    z-index: 9999 !important;
    position: relative;
    border: 1px solid #404041 !important;
    border-radius: 0.5rem !important;
    padding: 6px 12px !important;
    background: #ffffff !important;
    font-family: inherit;
    font-size: 0.75rem;
    line-height: 1.25rem !important;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    box-sizing: border-box;
    margin: 0 !important;
    box-shadow: none !important;
    height: auto !important;
    min-height: 32px !important;
    transition: all 0.2s ease;
}

.ts-control:focus-within {
    border-color: #404041 !important;
    outline: none !important;
    box-shadow: 0 0 0 1px #611132 !important;
}

.ts-control .item, .ts-control input {
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
    line-height: 1.25rem !important;
    font-size: inherit;
    font-family: inherit;
}

.ts-control .dropdown-toggle,
.ts-control .ts-dropdown-toggle,
.ts-control .dropdown_toggle,
.ts-control .ts-clear {
    display: none !important;
}

.ts-dropdown {
    border: 1px solid #404041;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-height: 250px;
    overflow-y: auto;
    z-index: 999999 !important;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    background: white;
    margin-top: 2px;
}

.ts-dropdown .ts-option {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.ts-dropdown .ts-option:hover {
    background-color: #f3f4f6;
}

.ts-dropdown .ts-option.selected {
    background-color: #e5e7eb;
    color: #404041;
}

.ts-control::after {
    content: "";
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 12px 12px;
    pointer-events: none;
    opacity: 0.92;
}

.ts-wrapper, .ts-control { vertical-align: middle; }

/* Asegurar que TomSelect dropdown tenga muy alto z-index */
.filter-section-content {
    position: relative;
}
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

    function clampToToday(input) {
        if (!input || !input.value) return true;
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const chosen = new Date(input.value + 'T00:00:00');
        if (chosen > today) {
            input.value = '';
            alert('La fecha no puede ser mayor a la fecha actual.');
            return false;
        }
        return true;
    }

    document.getElementById('startDate')?.addEventListener('change', function() {
        clampToToday(this);
    });

    document.getElementById('endDate')?.addEventListener('change', function() {
        clampToToday(this);
    });

    // Recalcula alturas de secciones abiertas al hacer resize
    window.addEventListener('resize', () => {
        document.querySelectorAll('.filter-section-content').forEach(content => {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // --- Inicializar TomSelect para el campo de Jurisdicción ---
    function initTomSelectJurisdiccion() {
        const selectEl = document.getElementById('jurisdiccion');
        if (selectEl && typeof TomSelect !== 'undefined') {
            const tomSelectInstance = new TomSelect(selectEl, {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                maxOptions: 100,
                maxItems: 1,
                create: false,
                placeholder: 'Seleccione una jurisdicción...',
                onChange: () => {
                    // Recalcular altura de la sección si está abierta
                    const section = selectEl.closest('.filter-section');
                    const content = section?.querySelector('.filter-section-content');
                    if (content && content.style.maxHeight && content.style.maxHeight !== '0px') {
                        setTimeout(() => {
                            content.style.maxHeight = content.scrollHeight + 'px';
                        }, 10);
                    }
                }
            });

            // Guardar instancia globalmente
            window.tomSelect_jurisdiccion = tomSelectInstance;
        }
    }

    // Try to initialize immediately
    if (typeof TomSelect !== 'undefined') {
        initTomSelectJurisdiccion();
    } else {
        // If TomSelect not available yet, wait for it
        let attempts = 0;
        const checkTomSelect = setInterval(() => {
            if (typeof TomSelect !== 'undefined') {
                clearInterval(checkTomSelect);
                initTomSelectJurisdiccion();
            }
            attempts++;
            if (attempts > 50) { // Stop after 5 seconds (50 * 100ms)
                clearInterval(checkTomSelect);
                console.warn('TomSelect did not load in time');
            }
        }, 100);
    }
});
</script>