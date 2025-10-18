<x-filtros.base titulo="Filtros">
    <x-slot name="headerActions">
        <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
            <i class="fas fa-redo text-xs"></i>
            Limpiar
        </button>
    </x-slot>

    <!-- Estado -->
    <x-filtros.seccion icono="user-check" titulo="Estado">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Estado de cuenta:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" id="estadoCuenta">
                <option>Todos</option>
                <option>Activo</option>
                <option>Inactivo</option>
                <option>Suspendido</option>
                <option>Pendiente</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Última sesión:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="ultimaSesion">
                <option>Cualquier momento</option>
                <option>Hoy</option>
                <option>Últimos 7 días</option>
                <option>Últimos 30 días</option>
                <option>Más de 30 días</option>
                <option>Nunca</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Fechas -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fecha de registro">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent" id="dateRange">
                <option>Todas las fechas</option>
                <option>Últimos 7 días</option>
                <option>Últimos 30 días</option>
                <option>Últimos 90 días</option>
                <option>Este año</option>
                <option>Personalizado</option>
            </select>
        </div>
        
        <!-- Selector condicional para fechas personalizadas -->
        <div class="filter-group" id="fechaPersonalizada" style="display: none;">
            <label class="block text-xs text-gray-600 font-lora mb-1">Fechas:</label>
            <div class="flex gap-2">
                <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                <span class="flex items-center text-gray-500 text-xs">a</span>
                <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Cargo -->
    <x-filtros.seccion icono="briefcase" titulo="Cargo">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Cargo:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="cargo">
                <option>Todos los cargos</option>
                <option>Administrador</option>
                <option>Coordinador</option>
                <option>Supervisor</option>
                <option>Analista</option>
                <option>Técnico</option>
                <option>Operador</option>
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
                <option>Jurisdicción Sanitaria IV</option>
                <option>Jurisdicción Sanitaria V</option>
            </select>
        </div>
    </x-filtros.seccion>

    <!-- Rol -->
    <x-filtros.seccion icono="user-tag" titulo="Rol">
        <div class="filter-group">
            <label class="block text-xs text-gray-600 font-lora mb-1">Tipo:</label>
            <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" id="rol">
                <option>Todos</option>
                <option>Administrador</option>
                <option>Editor</option>
                <option>Usuario</option>
                <option>Invitado</option>
                <option>Supervisor</option>
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
    // Lógica para selectores de fecha condicionales
    const dateRangeSelect = document.getElementById('dateRange');
    const fechaPersonalizada = document.getElementById('fechaPersonalizada');
    
    if (dateRangeSelect && fechaPersonalizada) {
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'Personalizado') {
                fechaPersonalizada.style.display = 'block';
            } else {
                fechaPersonalizada.style.display = 'none';
            }
        });
    }

    // Limpiar filtros
    document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
        document.querySelectorAll('select, input').forEach(element => {
            if (element.type !== 'button') {
                if (element.tagName === 'SELECT') {
                    element.selectedIndex = 0;
                } else {
                    element.value = '';
                }
            }
        });
        if (fechaPersonalizada) {
            fechaPersonalizada.style.display = 'none';
        }
        console.log('Filtros limpiados');
    });

    // Aplicar filtros
    document.getElementById('aplicarFiltros')?.addEventListener('click', function() {
        console.log('Filtros aplicados a la tabla de usuarios');
        // Aquí iría la lógica para aplicar filtros a la tabla
    });
});
</script>