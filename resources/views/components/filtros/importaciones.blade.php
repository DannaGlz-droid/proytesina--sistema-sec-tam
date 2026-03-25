@props([
    'usuarios' => null,
])

<x-filtros.base titulo="Filtros">
    <x-slot name="headerActions">
        <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltrosImportaciones">
            <i class="fas fa-redo text-xs"></i>
            Limpiar
        </button>
    </x-slot>

    <!-- Fecha de carga -->
    <x-filtros.seccion icono="calendar-alt" titulo="Fecha de carga" abierto="true">
        <div class="space-y-2">
            <div class="filter-group">
                <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                <select id="dateRangeImports" name="dateRangeImports" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                    <option value="all">Todas</option>
                    <option value="today">Hoy</option>
                    <option value="week">Últimos 7 días</option>
                    <option value="month">Últimos 30 días</option>
                    <option value="year">Último año</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>

            <!-- Selectores condicionales -->
            <div id="customRangeSelectorImports" style="display: none;">
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                    <input type="date" id="startDateImports" name="startDateImports" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
                <div class="filter-group">
                    <label class="block text-xs text-gray-600 font-lora mb-1">Hasta:</label>
                    <input type="date" id="endDateImports" name="endDateImports" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                </div>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Estado -->
    <x-filtros.seccion icono="check-circle" titulo="Estado">
        <div class="space-y-2">
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="statusCompleted" name="statuses" value="completed" class="status-checkbox rounded">
                    <span class="text-xs text-gray-600 font-lora">Completado</span>
                </label>
            </div>
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="statusReversed" name="statuses" value="reversed" class="status-checkbox rounded">
                    <span class="text-xs text-gray-600 font-lora">Revertido</span>
                </label>
            </div>
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="statusFailed" name="statuses" value="failed" class="status-checkbox rounded">
                    <span class="text-xs text-gray-600 font-lora">Fallido</span>
                </label>
            </div>
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="statusProcessing" name="statuses" value="processing" class="status-checkbox rounded">
                    <span class="text-xs text-gray-600 font-lora">Procesando</span>
                </label>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Usuario que cargó -->
    <x-filtros.seccion icono="user-circle" titulo="Usuario">
        <div class="space-y-2">
            <div class="filter-group">
                <input type="text" id="usuarioImports" name="usuarioImports" placeholder="Buscar usuario..." class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132]">
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Con registros fallidos -->
    <x-filtros.seccion icono="exclamation-circle" titulo="Registros fallidos">
        <div class="space-y-2">
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="conFallidos" name="conFallidos" class="rounded">
                    <span class="text-xs text-gray-600 font-lora">Solo las que tienen fallidos</span>
                </label>
            </div>
        </div>
    </x-filtros.seccion>
</x-filtros.base>

<style>
    .filter-group {
        margin: 0;
    }

    .filter-group input[type="text"],
    .filter-group input[type="date"],
    .filter-group select {
        transition: all 0.2s;
    }

    .filter-group input[type="checkbox"] {
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar selector de fechas personalizado
    const dateRangeImports = document.getElementById('dateRangeImports');
    const customRangeSelectorImports = document.getElementById('customRangeSelectorImports');

    if (dateRangeImports) {
        dateRangeImports.addEventListener('change', function() {
            customRangeSelectorImports.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    }

    // Limpiar filtros
    document.getElementById('limpiarFiltrosImportaciones').addEventListener('click', function() {
        // Reset date range
        if (dateRangeImports) dateRangeImports.value = 'all';
        customRangeSelectorImports.style.display = 'none';
        
        // Reset status checkboxes
        document.querySelectorAll('.status-checkbox').forEach(cb => cb.checked = false);
        
        // Reset usuario
        document.getElementById('usuarioImports').value = '';
        
        // Reset con fallidos
        document.getElementById('conFallidos').checked = false;
        
        // Trigger filter
        window.filterImports && window.filterImports();
    });
});
</script>
