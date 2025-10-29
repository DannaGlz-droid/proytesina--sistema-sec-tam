<x-filtros.base titulo="Filtros">
    <x-slot name="headerActions">
        <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
            <i class="fas fa-redo text-xs"></i>
            Limpiar
        </button>
    </x-slot>

    <!-- Fechas (usando la lógica del demo que funciona) -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fechas" abierto="true">
        <div class="space-y-2">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                <select id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="all">Todas las fechas</option>
                    <option value="year">Año específico</option>
                    <option value="month">Mes específico</option>
                    <option value="multiple-months">Múltiples meses</option>
                    <option value="quarter">Trimestre</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>

            <!-- Selectores condicionales: empezamos con display:none (como el demo que funciona) -->
            <div class="filter-group" id="yearSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Año:</label>
                <select id="year" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar año</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                </select>
            </div>

            <div class="filter-group" id="monthSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Mes:</label>
                <select id="month" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar mes</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

            <div class="filter-group" id="multipleMonthsSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Meses:</label>
                <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                    <!-- Input seguido de label como en el demo para que el selector sibling funcione -->
                    <div>
                        <input type="checkbox" id="month-01" class="month-checkbox" value="01">
                        <label for="month-01" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Ene</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-02" class="month-checkbox" value="02">
                        <label for="month-02" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Feb</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-03" class="month-checkbox" value="03">
                        <label for="month-03" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Mar</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-04" class="month-checkbox" value="04">
                        <label for="month-04" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Abr</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-05" class="month-checkbox" value="05">
                        <label for="month-05" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">May</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-06" class="month-checkbox" value="06">
                        <label for="month-06" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Jun</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-07" class="month-checkbox" value="07">
                        <label for="month-07" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Jul</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-08" class="month-checkbox" value="08">
                        <label for="month-08" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Ago</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-09" class="month-checkbox" value="09">
                        <label for="month-09" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Sep</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-10" class="month-checkbox" value="10">
                        <label for="month-10" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Oct</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-11" class="month-checkbox" value="11">
                        <label for="month-11" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Nov</label>
                    </div>
                    <div>
                        <input type="checkbox" id="month-12" class="month-checkbox" value="12">
                        <label for="month-12" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">Dic</label>
                    </div>
                </div>
            </div>

            <div class="filter-group" id="quarterSelector" style="display: none;">
                <label class="block text-xs text-gray-600 font-lora mb-1">Trimestre:</label>
                <select id="quarter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                    <option value="">Seleccionar trimestre</option>
                    <option value="1">Q1 (Ene-Mar)</option>
                    <option value="2">Q2 (Abr-Jun)</option>
                    <option value="3">Q3 (Jul-Sep)</option>
                    <option value="4">Q4 (Oct-Dic)</option>
                </select>
            </div>

            <div id="customRangeSelector" style="display: none;">
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                    <input type="date" id="startDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Hasta:</label>
                    <input type="date" id="endDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Ubicación -->
    <x-filtros.seccion icono="map-marker-alt" titulo="Ubicación">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción:</label>
            <select id="jurisdiccion" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todas</option>
                @isset($jurisdictions)
                    @foreach($jurisdictions as $j)
                        <option value="{{ $j->id }}">{{ $j->name }}</option>
                    @endforeach
                @else
                    <option value="norte">Jurisdicción Norte</option>
                    <option value="sur">Jurisdicción Sur</option>
                    <option value="centro">Jurisdicción Centro</option>
                    <option value="este">Jurisdicción Este</option>
                    <option value="oeste">Jurisdicción Oeste</option>
                @endisset
            </select>
        </div>

        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Municipio de residencia:</label>
            <select id="municipio" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                @isset($municipalities)
                    @foreach($municipalities as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                @else
                    <option value="allende">Allende</option>
                    <option value="monterrey">Monterrey</option>
                    <option value="guadalupe">Guadalupe</option>
                    <option value="apodaca">Apodaca</option>
                    <option value="san_nicolas">San Nicolás</option>
                    <option value="santa_catarina">Santa Catarina</option>
                @endisset
            </select>
        </div>

        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Municipio de defunción:</label>
            <select id="municipioDefuncion" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                @isset($municipalities)
                    @foreach($municipalities as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                @else
                    <option value="allende">Allende</option>
                    <option value="monterrey">Monterrey</option>
                    <option value="guadalupe">Guadalupe</option>
                    <option value="apodaca">Apodaca</option>
                    <option value="san_nicolas">San Nicolás</option>
                @endisset
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Demográficos -->
    <x-filtros.seccion icono="users" titulo="Demográficos">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Sexo:</label>
            <select id="sexo" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todos</option>
                @isset($sexes)
                    @foreach($sexes as $s)
                        <option value="{{ $s->value }}">{{ $s->label }}</option>
                    @endforeach
                @else
                    <option value="M">Hombre</option>
                    <option value="F">Mujer</option>
                @endisset
            </select>
        </div>

        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Edad:</label>
            <input type="text" id="edad" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" 
                   placeholder="Ej: 25 o 20-30 o 5,10,15">
            <div class="text-xs text-gray-500 mt-1">Edad específica, rango o múltiples valores separados por coma</div>
        </div>
    </x-filtros.seccion>

    <!-- Causas -->
    <x-filtros.seccion icono="heartbeat" titulo="Causas">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Causa de defunción:</label>
            <select id="causa" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="">Todas</option>
                @isset($causes)
                    @foreach($causes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                @else
                    <option value="cardiopatia">Enfermedades del corazón</option>
                    <option value="cancer">Cáncer</option>
                    <option value="covid">COVID-19</option>
                    <option value="accidente">Accidentes</option>
                    <option value="diabetes">Diabetes</option>
                    <option value="respiratorias">Enfermedades respiratorias</option>
                    <option value="ahogamiento">Ahogamiento</option>
                    <option value="violencia">Violencia</option>
                @endisset
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Resultados -->
    <x-filtros.seccion icono="chart-bar" titulo="Resultados">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Límite de resultados:</label>
            <select id="chartLimit" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <option value="5">Top 5</option>
                <option value="10" selected>Top 10</option>
                <option value="15">Top 15</option>
                <option value="all">Todos</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Botón Filtrar -->
    <div class="mt-6 pt-4 border-t border-gray-300">
        <button type="button" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2" id="aplicarFiltros">
            <i class="fas fa-filter text-sm"></i>
            Aplicar Filtros
        </button>
    </div>
</x-filtros.base>

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

    // Mantener comportamiento del demo para labels de meses (input + label)
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

            // recalcular sección abierta si corresponde
            const secContent = cb.closest('.filter-section')?.querySelector('.filter-section-content');
            if (secContent && secContent.style.maxHeight && secContent.style.maxHeight !== '0px') {
                secContent.style.maxHeight = secContent.scrollHeight + 'px';
            }
        });
    });

    // Limpiar filtros: usa lógica simple (como en demo)
    document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
        if (dateRange) dateRange.selectedIndex = 0;
        hideAll();

        // Reset other controls
        document.querySelectorAll('select:not(#dateRange), input[type="text"], input[type="date"]').forEach(el => {
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

        // Reset chart limit
        const chartLimit = document.getElementById('chartLimit');
        if (chartLimit) chartLimit.value = '10';

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
        // Disparar carga de gráficos usando la función global expuesta desde la vista de gráficos
        if (typeof window.loadCharts === 'function') {
            window.loadCharts();
        } else {
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
                causa: document.getElementById('causa')?.value,
                chartLimit: document.getElementById('chartLimit')?.value
            };
            console.log('Aplicando filtros a gráficos (fallback):', filtros);
        }
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
</style>