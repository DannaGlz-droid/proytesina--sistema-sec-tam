<x-filtros.base titulo="Filtros">
    <x-slot name="headerActions">
        <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
            <i class="fas fa-redo text-xs"></i>
            Limpiar
        </button>
    </x-slot>

    <!-- Fechas -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fechas">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" id="dateRange">
                <option>Todas las fechas</option>
                <option>Año específico</option>
                <option>Mes específico</option>
                <option>Múltiples meses</option>
                <option>Trimestre</option>
                <option>Personalizado</option>
            </select>
        </div>
        
        <!-- Selectores condicionales -->
        <div class="filter-group" id="yearSelector" style="display: none;">
            <label class="block text-xs text-gray-600 font-lora mb-1">Año:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="year">
                <option>Seleccionar año</option>
                <option>2023</option>
                <option>2022</option>
                <option>2021</option>
                <option>2020</option>
                <option>2019</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Ubicación -->
    <x-filtros.seccion icono="map-marker-alt" titulo="Ubicación">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="jurisdiccion">
                <option>Todas</option>
                <option>Jurisdicción Sanitaria I</option>
                <option>Jurisdicción Sanitaria II</option>
                <option>Jurisdicción Sanitaria III</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Municipio:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="municipio">
                <option>Todos</option>
                <option>Municipio Centro</option>
                <option>Municipio Norte</option>
                <option>Municipio Sur</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Demográficos -->
    <x-filtros.seccion icono="users" titulo="Demográficos">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Sexo:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="sexo">
                <option>Todos</option>
                <option>Masculino</option>
                <option>Femenino</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Edad:</label>
            <input type="text" 
                   placeholder="Ej: 25;20-30; 5,10,15" 
                   class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent"
                   id="edad">
            <p class="text-xs text-gray-500 font-lora mt-1">Edad específica, rango o múltiples valores separados por coma.</p>
        </div>
    </x-filtros.seccion>

    <!-- Causas -->
    <x-filtros.seccion icono="heartbeat" titulo="Causas">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Causa de defunción:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="causa">
                <option>Todas</option>
                <option>Accidente de tránsito</option>
                <option>Enfermedad cardiovascular</option>
                <option>Cáncer</option>
                <option>Enfermedad respiratoria</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Resultados -->
    <x-filtros.seccion icono="chart-bar" titulo="Resultados">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Límite de resultados:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="chartLimit">
                <option>Top 5</option>
                <option>Top 10</option>
                <option>Top 15</option>
                <option>Todos</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Botón Filtrar CON MÁS ESPACIO -->
    <div class="mt-6 pt-4 border-t border-gray-300"> <!-- Agregado contenedor con borde superior y más margen -->
        <button type="button" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2" id="aplicarFiltros">
            <i class="fas fa-filter text-sm"></i>
            Aplicar Filtros
        </button>
    </div>
</x-filtros.base>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica para selectores de fecha condicionales
    const dateRangeSelect = document.getElementById('dateRange');
    const yearSelector = document.getElementById('yearSelector');
    
    if (dateRangeSelect && yearSelector) {
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'Año específico' || this.value === 'Mes específico' || this.value === 'Múltiples meses' || this.value === 'Trimestre') {
                yearSelector.style.display = 'block';
            } else {
                yearSelector.style.display = 'none';
            }
        });
    }

    // Limpiar filtros
    document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
        document.querySelectorAll('select, input').forEach(element => {
            if (element.type !== 'button') {
                element.value = '';
            }
        });
        yearSelector.style.display = 'none';
        alert('Filtros limpiados');
    });

    // Aplicar filtros
    document.getElementById('aplicarFiltros')?.addEventListener('click', function() {
        alert('Filtros aplicados');
        // Aquí iría la lógica para aplicar filtros a los gráficos
    });
});
</script>