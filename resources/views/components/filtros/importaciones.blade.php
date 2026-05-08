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
                <select id="usuarioImports" name="usuarioImports" class="tomselect-select">
                    <option value="">Seleccione un usuario...</option>
                </select>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Registros fallidos -->
    <x-filtros.seccion icono="exclamation-triangle" titulo="Registros">
        <div class="space-y-2">
            <div class="filter-group">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                    <input type="checkbox" id="conFallidos" name="conFallidos" class="rounded">
                    <span class="text-xs text-gray-600 font-lora">Con errores en registros</span>
                </label>
            </div>
        </div>
    </x-filtros.seccion>

    <!-- Botón Aplicar filtros -->
    <div class="mt-4 pt-3 border-t border-gray-200">
        <button type="button" id="aplicarFiltrosImportaciones" class="w-full bg-[#611132] text-white px-3 py-3 rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2">
            <i class="fas fa-filter text-sm"></i>
            Aplicar Filtros
        </button>
    </div>
</x-filtros.base>

<style>
    .filter-group {
        margin: 0;
        padding: 0;
    }

    .filter-group input[type="text"],
    .filter-group input[type="date"],
    .filter-group select {
        transition: all 0.2s;
    }

    .filter-group input[type="checkbox"] {
        cursor: pointer;
    }

    /* Permitir que el dropdown de TomSelect aparezca fuera del contenedor */
    #usuarioImports + .ts-wrapper {
        position: relative;
        z-index: 100;
    }

    /* Z-index dinámico para dropdowns */
    .ts-wrapper.ts-dropdown-open {
        z-index: 999999 !important;
    }

    .ts-wrapper:not(.ts-dropdown-open) {
        z-index: 1 !important;
    }

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

    /* Ajustar sección de usuario para permitir dropdown */
    #usuarioImports ~ .ts-wrapper {
        position: relative;
        z-index: 100;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar selector de fechas personalizado
    const dateRangeImports = document.getElementById('dateRangeImports');
    const customRangeSelectorImports = document.getElementById('customRangeSelectorImports');

    if (dateRangeImports && customRangeSelectorImports) {
        dateRangeImports.addEventListener('change', function() {
            customRangeSelectorImports.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    }

    // Initialize TomSelect for usuario filter - with fallback for timing issues
    function initTomSelectUsuario() {
        const usuarioSelect = document.getElementById('usuarioImports');
        if (usuarioSelect && typeof TomSelect !== 'undefined') {
            const tomSelectInstance = new TomSelect(usuarioSelect, {
                valueField: 'id',
                labelField: 'display_name',
                searchField: ['name', 'username'],
                maxOptions: 20,
                maxItems: 1,
                create: false,
                preload: 'focus',
                placeholder: 'Seleccione un usuario...',
                render: {
                    option: function (data, escape) {
                        const name = data.full_name || data.name || '';
                        const username = data.username ? `@${data.username}` : '';
                        return `
                            <div class="py-1">
                                <div class="text-sm font-medium text-gray-900">${escape(name)}</div>
                                ${username ? `<div class="text-xs text-gray-500">${escape(username)}</div>` : ''}
                            </div>
                        `;
                    },
                    item: function (data, escape) {
                        const name = data.full_name || data.name || '';
                        const username = data.username ? `@${data.username}` : '';
                        return `<div>${escape(name)}${username ? ` <span class="text-gray-500">(${escape(username)})</span>` : ''}</div>`;
                    }
                },
                load: function(query, callback) {
                    // Allow empty query so TomSelect can preload all users
                    fetch('/api/users/search?q=' + encodeURIComponent(query))
                        .then(res => {
                            if (!res.ok) throw new Error('Network error');
                            return res.json();
                        })
                        .then(items => {
                            if (Array.isArray(items)) {
                                callback(items);
                            } else {
                                callback([]);
                            }
                        })
                        .catch(err => {
                            console.error('Error loading usuarios:', err);
                            callback([]);
                        });
                }
            });
            
            // Guardar instancia globalmente para limpiar después
            window.tomSelectUsuarioInstance = tomSelectInstance;
        }
    }

    // Try to initialize immediately
    if (typeof TomSelect !== 'undefined') {
        initTomSelectUsuario();
    } else {
        // If TomSelect not available yet, wait for it
        let attempts = 0;
        const checkTomSelect = setInterval(() => {
            if (typeof TomSelect !== 'undefined') {
                clearInterval(checkTomSelect);
                initTomSelectUsuario();
            }
            attempts++;
            if (attempts > 50) { // Stop after 5 seconds (50 * 100ms)
                clearInterval(checkTomSelect);
                console.warn('TomSelect did not load in time');
            }
        }, 100);
    }

    // Manager para z-index dinámico de TomSelect dropdowns
    setTimeout(() => {
        const tomSelectElements = document.querySelectorAll('.tomselect-select');
        
        tomSelectElements.forEach(select => {
            // Buscar la instancia de TomSelect asociada
            if(select.tomselect) {
                const tomSelectInstance = select.tomselect;
                
                // Cuando se abre el dropdown
                if (tomSelectInstance.on) {
                    tomSelectInstance.on('dropdown_open', function() {
                        const wrapper = tomSelectInstance.wrapper;
                        if(wrapper) {
                            wrapper.classList.add('ts-dropdown-open');
                        }
                    });
                    
                    // Cuando se cierra el dropdown
                    tomSelectInstance.on('dropdown_close', function() {
                        const wrapper = tomSelectInstance.wrapper;
                        if(wrapper) {
                            wrapper.classList.remove('ts-dropdown-open');
                        }
                    });
                }
            }
        });
    }, 200);


});
</script>
