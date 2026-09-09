@extends('layouts.principal')
@section('title', 'Registros Fallidos de Importación')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-failed-imports-page px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header class="users-management-header statistics-failed-imports-header" title="Importaciones fallidas" description="Revise, corrija o descarte los registros que no pudieron guardarse desde {{ $importFileName }}.">
            <x-slot:actions><a href="{{ route('statistic.import-history-view') }}" class="users-page-create-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al historial</a></x-slot:actions>
        </x-ui.page-header>

        <section class="app-table-card users-table-card failed-imports-card" aria-label="Registros fallidos de la importación">
            <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                <section class="users-filter-card failed-imports-controls" aria-label="Controles de registros fallidos">
                    <div class="users-filter-form">
                        <div class="users-filter-topbar">
                            <div class="failed-imports-file-context"><span class="failed-imports-file-icon"><i class="far fa-file-excel" aria-hidden="true"></i></span><div><strong>{{ $importFileName }}</strong><small>Registros pendientes de revisión</small></div></div>
                            <div class="users-filter-search"><i class="fas fa-search" aria-hidden="true"></i><input id="search-failed-imports" type="search" placeholder="Buscar registros fallidos..." aria-label="Buscar registros fallidos" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false"><span class="users-search-progress" aria-hidden="true"></span><button id="clear-failed-imports-search" type="button" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            <div class="app-table-page-size users-filter-page-size"><select id="per-page-failed-imports" class="users-native-page-size" aria-hidden="true" tabindex="-1">@foreach([4,6,10] as $size)<option value="{{ $size }}" @selected($size === 4)>{{ $size }}</option>@endforeach</select><div class="users-page-size-dropdown"><button id="per-page-failed-imports-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false"><span>Mostrar</span><strong id="per-page-failed-imports-label">4</strong><i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i></button><div id="per-page-failed-imports-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="per-page-failed-imports-button">@foreach([4,6,10] as $size)<button type="button" role="option" class="users-page-size-option {{ $size === 4 ? 'is-active' : '' }}" data-value="{{ $size }}">{{ $size }}</button>@endforeach</div></div></div>
                        </div>
                    </div>
                </section>
            </div>
        <!-- HEADER CON TÍTULO Y BOTONES -->
        <div class="failed-imports-legacy-header flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Registros Fallidos - "{{ $importFileName }}"</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Revise y corrija los registros que fallaron durante la importación para reintentar su guardado.
                </p>
            </div>

            <a href="{{ route('statistic.import-history-view') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-arrow-left text-xs"></i>
                Volver
            </a>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="failed-imports-state" role="status" aria-label="Cargando registros fallidos">
            <span class="sr-only">Cargando registros fallidos</span>
            <div class="failed-imports-loading-grid" aria-hidden="true">
                @for ($card = 0; $card < 2; $card++)
                    <article class="failed-imports-loading-card">
                        <div class="users-table-skeleton failed-imports-skeleton-alert">
                            <div></div>
                        </div>
                        <div class="users-table-skeleton failed-imports-skeleton-fields failed-imports-skeleton-fields--three">
                            <div></div><div></div><div></div>
                            <div></div><div></div><div></div>
                        </div>
                        <div class="users-table-skeleton failed-imports-skeleton-fields failed-imports-skeleton-fields--two failed-imports-skeleton-location">
                            <div></div><div></div>
                            <div></div><div></div>
                            <div></div>
                        </div>
                        <div class="users-table-skeleton failed-imports-skeleton-fields failed-imports-skeleton-fields--two failed-imports-skeleton-summary">
                            <div></div><div></div>
                        </div>
                        <div class="users-table-skeleton failed-imports-skeleton-actions">
                            <div></div><div></div>
                        </div>
                    </article>
                @endfor
            </div>
        </div>

        <!-- Records Container -->
        <div id="records-container" class="hidden space-y-6">
            <!-- Records List (with empty state) -->
            <div id="records-list" class="failed-imports-list">
                <!-- Empty State (shown when no records) -->
                <div id="empty-state" class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4"><i class="fas fa-check" aria-hidden="true"></i></div>
                    <p class="text-lg font-lora text-gray-600">No quedan registros por revisar</p>
                    <p class="text-sm text-gray-500 font-lora mt-2">Todos los registros fallidos de esta importación ya fueron atendidos.</p>
                </div>
            </div>

        </div>
        <nav class="users-table-footer failed-imports-footer flex flex-row flex-wrap items-center justify-between gap-3 p-4" aria-label="Paginación de registros fallidos"><span id="dt-info" class="text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading">Preparando tabla</span><div id="dt-pagination" class="flex-none"></div></nav>
        </section>
    </main>

@endsection

<!-- Template for failed record card -->
<template id="record-template">
    <article class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 shadow-md record-card" data-record-id="">
        
        <!-- Error Alert -->
        <div class="failed-record-error" data-record-status>
            <span class="failed-record-error-icon"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></span>
            <div>
                <p class="failed-record-error-title">Requiere corrección</p>
                <ul class="error-messages"></ul>
            </div>
        </div>

        <!-- VIEW MODE -->
        <div class="view-mode">
            <form class="correction-form ui-form-fields">
                <!-- Sección 1: Información del fallecido -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del fallecido</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4 items-start">
                        <!-- Row 1: Folio | Nombre | Ap. paterno -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Folio <span class="text-red-600">*</span></label>
                            <input name="folio" type="text" value="" disabled required minlength="9" maxlength="17"
                                pattern="([0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})"
                                title="Capture 9 dígitos o el folio alfanumérico oficial" inputmode="text"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                placeholder="Ej: 230787888">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                            <input name="nombre" type="text" value="" disabled required maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Juan Diego">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                            <input name="primerapellido" type="text" value="" disabled required maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Nava">
                        </div>

                        <!-- Row 2: Apellido materno | Sexo | Edad -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="segundoapellido" type="text" value="" disabled maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Reyes">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo <span class="text-red-600">*</span></label>
                            <select name="sexod" required disabled class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                <option value="">Seleccione una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad <span class="text-red-600">*</span></label>
                            <div class="failed-age-fields flex gap-2">
                                <input name="edad_valor" type="number" min="0" max="150" value="" required disabled inputmode="numeric"
                                       class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora"
                                       placeholder="Ej: 34">
                                <select name="edad_unidad" required disabled class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Unidad</option>
                                    <option value="anos">Años</option>
                                    <option value="meses">Meses</option>
                                    <option value="dias">Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 2: Ubicación -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="location-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Ubicación</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="failed-location-grid grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia <span class="text-red-600">*</span></label>
                                <select name="municipioresidenciad" required disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción <span class="text-red-600">*</span></label>
                                <select name="municipiodefunciond" required disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de residencia</label>
                                <input name="distrito" type="text" value="" readonly data-derived-field aria-disabled="true"
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-lora" 
                                       placeholder="Distrito">
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de defunción</label>
                                <input name="jurisdicciondefunciond" type="text" value="" readonly data-derived-field aria-disabled="true"
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-lora"
                                       placeholder="Distrito">
                            </div>
                        </div>

                        <div class="failed-location-place">
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico <span class="text-red-600">*</span></label>
                            <select name="sitiodefunciond" required disabled
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                <option value="">Seleccione lugar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 3: Información de la defunción -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="medical-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información de la defunción</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción <span class="text-red-600">*</span></label>
                                <select name="sheet" required disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione una causa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción <span class="text-red-600">*</span></label>
                                <input name="fechadefuncion" type="date" value="" required disabled
                                       class="fecha-input w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg text-gray-700 font-lora" 
                                       placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora para botones -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Action Buttons - View Mode -->
                <div class="action-buttons-view flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="ui-button ui-button--secondary btn-discard">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                        <span data-button-label>Descartar</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-toggle-edit">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                        <span data-button-label>Editar</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-import-corrected" hidden>
                        <i class="fas fa-check" aria-hidden="true"></i>
                        <span data-button-label>Importar</span>
                    </button>
                </div>

                <!-- Action Buttons - Edit Mode -->
                <div class="action-buttons-edit hidden flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="ui-button ui-button--secondary btn-cancel-edit">
                        <i class="fas fa-times" aria-hidden="true"></i>
                        <span data-button-label>Cancelar</span>
                    </button>
                    <button type="button" class="ui-button ui-button--secondary btn-save-correction">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        <span data-button-label>Guardar corrección</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-retry">
                        <i class="fas fa-check" aria-hidden="true"></i>
                        <span data-button-label>Guardar e importar</span>
                    </button>
                </div>
            </form>
        </div>

    </article>
</template>

<!-- Incluir Ionicons -->

<!-- Font Awesome -->

<!-- Tom Select CDN (single-select, styled to match inputs) -->

@push('scripts')
<script>
// Helper function to parse dates in various formats and convert to YYYY-MM-DD for HTML date input
function parseDateForInput(dateStr) {
    if (!dateStr) return '';
    
    let date = null;
    
    // Excel imports use the official DD/MM/YYYY format.
    if (dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
        }
    } else if (dateStr.includes('-')) {
        // Try YYYY-MM-DD or DD-MM-YYYY
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            if (parseInt(parts[0]) > 31) {
                // YYYY-MM-DD
                date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else {
                // DD-MM-YYYY
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
        }
    }
    
    // If parsing failed, try generic Date constructor
    if (!date || isNaN(date.getTime())) {
        date = new Date(dateStr);
    }
    
    // If still invalid, return empty
    if (!date || isNaN(date.getTime())) {
        return '';
    }
    
    // Format as YYYY-MM-DD
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Build a map municipality name -> jurisdiction name (for lookup)
const muniToJurName = @json(
    $municipalities->mapWithKeys(function($m) use ($jurisdictions) {
        $jur = $jurisdictions->find($m->district_id);
        return [$m->name => $jur ? $jur->name : ''];
    })
);

function districtNameForMunicipality(municipalityName) {
    const normalize = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim()
        .toUpperCase();
    const target = normalize(municipalityName);
    if (!target) return '';

    for (const [municipality, district] of Object.entries(muniToJurName)) {
        if (normalize(municipality) === target) {
            return district;
        }
    }

    return '';
}

// Build data for Tom Select
const municipalitiesData = @json($municipalities);
const locationsData = @json($locations);
const causesData = @json($causes);
const sexData = [
    { id: 'M', name: 'Masculino' },
    { id: 'F', name: 'Femenino' },
];
const ageUnitData = [
    { id: 'anos', name: 'Años' },
    { id: 'meses', name: 'Meses' },
    { id: 'dias', name: 'Días' },
];

function selectCatalog(name) {
    if (name === 'sexod') return sexData;
    if (name === 'edad_unidad') return ageUnitData;
    if (name === 'municipioresidenciad' || name === 'municipiodefunciond') return municipalitiesData;
    if (name === 'sitiodefunciond') return locationsData;
    if (name === 'sheet') return causesData;
    return [];
}

function selectIsSearchable(name) {
    return !['sexod', 'edad_unidad'].includes(name);
}

// Function to initialize Tom Select for a specific select element
function initializeTomSelect(selector, data, labelField = 'name', valueField = 'id') {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
        if (el.tomselect) return; // Already initialized
        
        new TomSelect(el, {
            options: data.map(item => ({
                [valueField]: item[valueField],
                [labelField]: item[labelField],
                text: item[labelField]
            })),
            items: el.value ? [el.value] : [],
            valueField: valueField,
            labelField: labelField,
            searchField: labelField,
            create: false,
            placeholder: el.getAttribute('data-placeholder') || 'Seleccione una opción',
            maxItems: 1,
            closeAfterSelect: true
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const importId = {{ $importId }};
    let currentPage = 1;
    let isInitialLoad = true;
    let failedImportsPerPage = 4;
    let failedImportsSearch = '';
    let activeAdaptiveTomSelect = null;
    let adaptiveDropdownFrame = null;

    function positionAdaptiveTomSelect(ts) {
        if (!ts?.isOpen || !ts.wrapper || !ts.control || !ts.dropdown) return;

        if (!document.documentElement.contains(ts.wrapper)) {
            activeAdaptiveTomSelect = null;
            return;
        }

        const viewportGap = 12;
        const maximumHeight = 240;
        const minimumUsefulHeight = 96;
        const controlRect = ts.control.getBoundingClientRect();
        const spaceBelow = Math.max(0, window.innerHeight - controlRect.bottom - viewportGap);
        const spaceAbove = Math.max(0, controlRect.top - viewportGap);
        const dropdownContent = ts.dropdown_content
            || ts.dropdown.querySelector('.ts-dropdown-content')
            || ts.dropdown;
        const desiredHeight = Math.min(
            maximumHeight,
            Math.max(minimumUsefulHeight, dropdownContent.scrollHeight || maximumHeight)
        );
        const openUpward = spaceBelow < desiredHeight && spaceAbove > spaceBelow;
        const availableHeight = openUpward ? spaceAbove : spaceBelow;
        const dropdownHeight = Math.min(maximumHeight, availableHeight);

        ts.wrapper.classList.toggle('dropdown-up', openUpward);
        ts.dropdown.style.setProperty('--failed-ts-available-height', `${Math.floor(dropdownHeight)}px`);
    }

    function scheduleAdaptiveTomSelectPosition() {
        if (!activeAdaptiveTomSelect || adaptiveDropdownFrame !== null) return;

        adaptiveDropdownFrame = window.requestAnimationFrame(() => {
            adaptiveDropdownFrame = null;
            positionAdaptiveTomSelect(activeAdaptiveTomSelect);
        });
    }

    function enableAdaptiveTomSelectDropdown(ts) {
        ts.on('dropdown_open', () => {
            activeAdaptiveTomSelect = ts;
            positionAdaptiveTomSelect(ts);
            window.requestAnimationFrame(() => positionAdaptiveTomSelect(ts));
        });
        ts.on('type', scheduleAdaptiveTomSelectPosition);
        ts.on('load', scheduleAdaptiveTomSelectPosition);
        ts.on('dropdown_close', () => {
            ts.wrapper?.classList.remove('dropdown-up');
            ts.dropdown?.style.removeProperty('--failed-ts-available-height');
            if (activeAdaptiveTomSelect === ts) activeAdaptiveTomSelect = null;
        });
    }

    window.addEventListener('resize', scheduleAdaptiveTomSelectPosition, { passive: true });
    document.addEventListener('scroll', scheduleAdaptiveTomSelectPosition, { passive: true, capture: true });

    function notifyFailedImport(message, type = 'success', duration = 3000) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type, duration);
            return;
        }

        const logger = type === 'error' ? console.error : console.log;
        logger(message);
    }

    const failedSearchInput = document.getElementById('search-failed-imports');
    const failedSearchClear = document.getElementById('clear-failed-imports-search');
    const failedSearchControl = failedSearchInput?.closest('.users-filter-search');
    const failedPerPageSelect = document.getElementById('per-page-failed-imports');
    const failedPerPageButton = document.getElementById('per-page-failed-imports-button');
    const failedPerPageMenu = document.getElementById('per-page-failed-imports-menu');

    failedSearchInput?.setAttribute('aria-busy', 'false');
    const updateFailedSearchClear = () => failedSearchClear?.classList.toggle('hidden', !failedSearchInput?.value);
    const applyFailedSearch = () => {
        failedImportsSearch = failedSearchInput?.value.trim() || '';
        failedSearchControl?.classList.add('is-searching');
        failedSearchInput?.setAttribute('aria-busy', 'true');
        isInitialLoad = false;
        loadFailedRecords(1);
    };

    const failedImportsEmptyStateMarkup = () => {
        if (failedImportsSearch) {
            return `
                <div class="users-table-state users-table-state--no-results failed-imports-search-empty" role="status">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <strong>No encontramos resultados</strong>
                    <span>Prueba con otra búsqueda.</span>
                    <button class="users-table-state-action" type="button" data-failed-state-action="search">Limpiar búsqueda</button>
                </div>
            `;
        }

        return `
            <div id="empty-state" class="col-span-full text-center py-12">
                <div class="text-gray-400 mb-4"><i class="fas fa-check" aria-hidden="true"></i></div>
                <p class="text-lg font-lora text-gray-600">No quedan registros por revisar</p>
                <p class="text-sm text-gray-500 font-lora mt-2">Todos los registros fallidos de esta importación ya fueron atendidos.</p>
            </div>
        `;
    };

    failedSearchInput?.addEventListener('input', updateFailedSearchClear);
    failedSearchInput?.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyFailedSearch();
        }
    });
    failedSearchClear?.addEventListener('click', () => {
        failedSearchInput.value = '';
        updateFailedSearchClear();
        applyFailedSearch();
    });
    document.getElementById('records-list')?.addEventListener('click', event => {
        const action = event.target.closest('[data-failed-state-action="search"]');
        if (!action) return;

        failedSearchInput.value = '';
        updateFailedSearchClear();
        applyFailedSearch();
        failedSearchInput.focus();
    });

    failedPerPageButton?.addEventListener('click', () => {
        const open = failedPerPageMenu?.classList.contains('hidden');
        failedPerPageMenu?.classList.toggle('hidden', !open);
        failedPerPageButton.setAttribute('aria-expanded', String(open));
    });
    failedPerPageMenu?.querySelectorAll('[data-value]').forEach(option => option.addEventListener('click', () => {
        failedPerPageSelect.value = option.dataset.value;
        document.getElementById('per-page-failed-imports-label').textContent = option.dataset.value;
        failedPerPageMenu.querySelectorAll('[data-value]').forEach(item => item.classList.toggle('is-active', item === option));
        failedPerPageMenu.classList.add('hidden');
        failedPerPageButton.setAttribute('aria-expanded', 'false');
        failedPerPageSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }));
    failedPerPageSelect?.addEventListener('change', event => {
        failedImportsPerPage = Number(event.target.value) || 4;
        isInitialLoad = false;
        loadFailedRecords(1);
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.users-page-size-dropdown')) {
            failedPerPageMenu?.classList.add('hidden');
            failedPerPageButton?.setAttribute('aria-expanded', 'false');
        }
    });

    function loadFailedRecords(page = 1) {
        const loadingState = document.getElementById('loading-state');
        const recordsContainer = document.getElementById('records-container');
        const recordsList = document.getElementById('records-list');
        
        // Only show loading state on initial load
        if (isInitialLoad) {
            loadingState.classList.remove('hidden');
            recordsContainer.classList.add('hidden');
        } else {
            // On pagination, add fade out transition
            recordsList.style.opacity = '0';
            recordsList.style.transition = 'opacity 0.2s ease-out';
        }

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const query = new URLSearchParams({ page, per_page: failedImportsPerPage });
        if (failedImportsSearch) query.set('search', failedImportsSearch);
        fetch(`/api/estadisticas/importaciones/${importId}/registros-fallidos?${query.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin' // Include cookies for authentication
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                loadingState.classList.add('hidden');
                recordsContainer.classList.remove('hidden');
                
                // On initial load, ensure opacity is 1 immediately
                if (isInitialLoad) {
                    recordsList.style.transition = 'none';
                    recordsList.style.opacity = '1';
                }
                isInitialLoad = false;

                // Check if response structure is valid
                if (!data) {
                    throw new Error('Empty response from API');
                }
                
                if (!data.ok) {
                    throw new Error(data.message || 'API returned error');
                }
                
                // Safely access nested data
                if (!data.data) {
                    recordsList.classList.toggle('is-search-empty', Boolean(failedImportsSearch));
                    recordsList.innerHTML = failedImportsEmptyStateMarkup();
                    renderFailedPaginationPilot({
                        current_page: 1,
                        last_page: 0,
                        from: 0,
                        to: 0,
                        total: 0,
                        total_pending: Number(data.total_pending) || 0,
                    });
                    window.requestAnimationFrame(() => {
                        recordsList.style.opacity = '1';
                    });
                    return;
                }
                // Check if this is a paginated response (Laravel pagination object)
                const records = Array.isArray(data.data) ? data.data : (data.data.data || []);
                
                if (records.length > 0) {
                    recordsList.classList.remove('is-search-empty');
                    recordsList.innerHTML = '';
                    
                    renderRecords(records);
                    
                    // Fade in the records list on pagination
                    if (!isInitialLoad) {
                        setTimeout(() => {
                            recordsList.style.opacity = '1';
                        }, 10);
                    }
                    
                    // If it's a paginated response, use the pagination object; otherwise use the raw data
                    const paginationData = Array.isArray(data.data) ? null : data.data;
                    if (paginationData) {
                        renderFailedPaginationPilot({
                            ...paginationData,
                            total_pending: Number(data.total_pending) || Number(paginationData.total) || 0,
                        });
                    }
                    currentPage = page;
                } else {
                    recordsList.classList.toggle('is-search-empty', Boolean(failedImportsSearch));
                    recordsList.innerHTML = failedImportsEmptyStateMarkup();
                    renderFailedPaginationPilot({
                        current_page: 1,
                        last_page: 0,
                        from: 0,
                        to: 0,
                        total: 0,
                        total_pending: Number(data.total_pending) || 0,
                    });
                    window.requestAnimationFrame(() => {
                        recordsList.style.opacity = '1';
                    });
                }
            })
            .catch(error => {
                console.error('Error loading failed records:', error);
                notifyFailedImport('No se pudieron cargar los registros fallidos. Intenta nuevamente.', 'error');
                loadingState.classList.add('hidden');
                recordsContainer.classList.remove('hidden');
                isInitialLoad = false;
                recordsList.style.opacity = '1';
                recordsList.classList.remove('is-search-empty');
                updateFailedPaginationInfo({ from: 0, to: 0, total: 0 });
                document.getElementById('dt-pagination').replaceChildren();
                recordsList.innerHTML = `
                    <div class="failed-imports-load-error" role="alert">
                        <span><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span>
                        <div><p>No se pudieron cargar los registros</p><p>Intente nuevamente en unos momentos.</p></div>
                    </div>
                `;
            })
            .finally(() => {
                failedSearchControl?.classList.remove('is-searching');
                failedSearchInput?.setAttribute('aria-busy', 'false');
                updateFailedSearchClear();
            });
    }

    function findMatchingItemId(textValue, data) {
        if (!textValue || !data || data.length === 0) return '';

        const normalize = value => String(value)
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim()
            .toUpperCase();
        const normalizedValue = normalize(textValue);
        const item = data.find(option =>
            String(option.id) === String(textValue)
            || normalize(option.name) === normalizedValue
        );

        return item ? String(item.id) : '';
    }

    function initializeTomSelectField(el, data) {
        const name = el.getAttribute('name');
        // Use originalValue if available (the original text from server), otherwise originalText
        const textValue = el.dataset.originalValue || el.dataset.originalText || el.value || '';
        
        if (!data || data.length === 0) {
            return;
        }
        
        // Find matching ID
        let selectedId = findMatchingItemId(textValue, data);
        
        // Create Tom Select options
        const options = data.map(item => ({
            id: String(item.id),
            name: item.name,
            text: item.name
        }));

        // In review mode, preserve the source value even when it is precisely
        // the invalid option that caused the import to fail.
        if (!selectedId && el.disabled && textValue) {
            selectedId = '__source_value__';
            options.unshift({
                id: selectedId,
                name: textValue,
                text: textValue
            });
        }
        
        const config = {
            options: options,
            items: selectedId ? [selectedId] : [],
            valueField: 'id',
            labelField: 'name',
            searchField: selectIsSearchable(name) ? ['name'] : [],
            create: false,
            placeholder: 'Seleccione una opción',
            maxItems: 1,
            maxOptions: 100,
            allowEmptyOption: false,
            hideSelected: false,
            closeAfterSelect: true,
            disable: el.disabled
        };
        
        const ts = new TomSelect(el, config);
        enableAdaptiveTomSelectDropdown(ts);
        const describedBy = el.getAttribute('aria-describedby');
        if (describedBy) ts.control_input?.setAttribute('aria-describedby', describedBy);
        if (el.disabled) ts.control_input?.setAttribute('aria-disabled', 'true');
        
        // Ensure value is set after initialization
        if (selectedId) {
            ts.setValue(selectedId, true);
            // Update the data attribute with the mapped name for consistency
            const item = data.find(d => String(d.id) === selectedId);
            if (item) {
                el.dataset.originalText = item.name;
            }
        }
        
        ts.on('change', value => {
            const selected = data.find(item => String(item.id) === String(value));
            el.dataset.originalText = selected?.name || '';
            clearFieldError(el);
        });

        ts.on('blur', () => {
            if (!el.disabled && el.required) validateCorrectionField(el);
        });

        // Function to apply/reapply styling
        const applyDisabledStyle = () => {
            const wrapper = el.closest('.ts-wrapper');
            if (wrapper) {
                const control = wrapper.querySelector('.ts-control');
                if (control) {
                    if (el.disabled) {
                        const isDerivedInEdit = el.hasAttribute('data-derived-field')
                            && el.closest('.record-card')?.classList.contains('is-editing-fields');
                        wrapper.classList.add('disabled');
                        control.classList.add('disabled');
                        control.style.setProperty('background-color', isDerivedInEdit ? '#f3f4f6' : 'transparent', 'important');
                        control.style.setProperty('color', '#10233f', 'important');
                        control.style.setProperty('cursor', isDerivedInEdit ? 'not-allowed' : 'default', 'important');
                        control.style.setProperty('opacity', '1', 'important');
                        
                        // Also style the input inside
                        const input = control.querySelector('input');
                        if (input) {
                            input.style.setProperty('background-color', 'transparent', 'important');
                            input.style.setProperty('cursor', 'default', 'important');
                        }
                    } else {
                        wrapper.classList.remove('disabled');
                        control.classList.remove('disabled');
                        control.style.setProperty('background-color', '#ffffff', 'important');
                        control.style.setProperty('color', '#000000', 'important');
                        control.style.setProperty('cursor', 'pointer', 'important');
                        control.style.setProperty('opacity', '1', 'important');
                        
                        // Also style the input inside
                        const input = control.querySelector('input');
                        if (input) {
                            input.style.setProperty('background-color', 'transparent', 'important');
                            input.style.setProperty('cursor', 'pointer', 'important');
                        }
                    }
                }
            }
        };
        
        // Apply styling immediately after Tom Select initialization
        applyDisabledStyle();
        
        // Reapply styling after a longer delay to ensure Tom Select is fully rendered
        setTimeout(applyDisabledStyle, 100);
        
        // Keep reapplying every time disabled status changes
        const originalDisabled = el.disabled;
        const observer = new MutationObserver(() => {
            if (el.disabled !== originalDisabled) {
                applyDisabledStyle();
            }
        });
        observer.observe(el, { attributes: true, attributeFilter: ['disabled'] });
        
        return ts;
    }

    function friendlyRecordError(error) {
        const message = String(error || '').trim();
        const causeMatch = message.match(/^Causa no reconocida:\s*"([\s\S]+?)"\.\s*Use una de las causas permitidas\.?$/i);
        if (causeMatch) return `Causa de defunción: "${causeMatch[1]}" no es una opción válida. Seleccione una causa permitida.`;

        const legacyMessages = {
            'Nombre vacío': 'Nombre: este campo es obligatorio.',
            'Primer apellido vacío': 'Primer apellido: este campo es obligatorio.',
            'Edad vacía o inválida': 'Edad: ingrese un número entero.',
            'Lugar de defunción vacío': 'Lugar de defunción: este campo es obligatorio.',
            'Causa no indicada en la hoja ni en la fila.': 'Causa de defunción: no está indicada. Seleccione una causa permitida.',
            'Folio duplicado en el archivo (todas las ocurrencias rechazadas)': 'Folio: está repetido dentro del archivo. Corrija o elimine las filas duplicadas.',
            'Folio duplicado detectado en la base de datos': 'Folio: ya existe en el sistema. Ingrese uno diferente.',
            'El folio ya existe en la base de datos': 'Folio: ya existe en el sistema. Ingrese uno diferente.',
            'Edad inválida: meses debe ser mayor o igual a 0': 'Edad: para meses, ingrese un valor entre 0 y 11.',
            'Edad inválida: meses debe ser menor a 12': 'Edad: para meses, ingrese un valor entre 0 y 11.',
            'Edad inválida: días debe ser mayor o igual a 0': 'Edad: para días, ingrese un valor entre 0 y 30.',
            'Edad inválida: días debe ser menor o igual a 30': 'Edad: para días, ingrese un valor entre 0 y 30.',
            'Edad inválida: años debe ser mayor o igual a 0': 'Edad: para años, ingrese un valor entre 0 y 150.',
            'Edad inválida: años debe ser menor o igual a 150': 'Edad: para años, ingrese un valor entre 0 y 150.',
            'Fecha futura: la fecha de defunción no puede ser mayor a hoy': 'Fecha de defunción: no puede ser posterior a hoy.',
        };

        if (legacyMessages[message]) return legacyMessages[message];
        if (/^Folio gubernamental inválido o ausente/i.test(message)) {
            return 'Folio: ingrese 9 dígitos o un folio alfanumérico de defunción válido.';
        }
        if (/^Sexo inválido o ausente/i.test(message)) return 'Sexo: seleccione Masculino o Femenino.';
        if (/^Fecha (?:de defunción )?inválida/i.test(message)) return 'Fecha de defunción: ingrese una fecha válida.';

        return message;
    }

    function recordErrorDetails(record) {
        if (Array.isArray(record?.error_details) && record.error_details.length > 0) {
            return record.error_details
                .filter(error => typeof error === 'string' && error.trim() !== '')
                .map(friendlyRecordError);
        }

        return String(record?.error_message || '')
            .split(/;\s*/)
            .map(error => error.trim())
            .filter(Boolean)
            .map(friendlyRecordError);
    }

    function renderRecordErrors(card, errors) {
        const list = card.querySelector('.error-messages');
        if (!list) return;

        list.innerHTML = '';
        errors.forEach(error => {
            const item = document.createElement('li');
            const isStructuredError = error && typeof error === 'object';
            item.textContent = isStructuredError ? error.message : error;
            if (isStructuredError && error.kind) item.classList.add(`is-${error.kind}`);
            list.appendChild(item);
        });
    }

    function renderRecordStatus(card, record) {
        const status = record?.status === 'corrected' ? 'corrected' : 'pending';
        const statusPanel = card.querySelector('[data-record-status]');
        const statusIcon = statusPanel?.querySelector('.failed-record-error-icon i');
        const statusTitle = statusPanel?.querySelector('.failed-record-error-title');
        const editButton = card.querySelector('.btn-toggle-edit');
        const importButton = card.querySelector('.btn-import-corrected');
        const isCorrected = status === 'corrected';
        const statusMessages = isCorrected
            ? ['El registro está listo para importarse.']
            : recordErrorDetails(record);

        card.dataset.status = status;
        card.dataset.statusMessages = JSON.stringify(statusMessages);
        card.classList.remove('is-editing-status');
        statusPanel?.classList.remove('is-editing');
        statusPanel?.classList.toggle('is-corrected', isCorrected);

        if (statusIcon) {
            statusIcon.className = isCorrected ? 'fas fa-circle-check' : 'fas fa-circle-exclamation';
        }
        if (statusTitle) {
            statusTitle.textContent = isCorrected ? 'Corrección guardada' : 'Requiere corrección';
        }

        renderRecordErrors(card, statusMessages);

        if (importButton) importButton.hidden = !isCorrected;
        editButton?.classList.toggle('ui-button--primary', !isCorrected);
        editButton?.classList.toggle('ui-button--secondary', isCorrected);
    }

    function renderRecordEditingStatus(card) {
        const statusPanel = card.querySelector('[data-record-status]');
        const statusIcon = statusPanel?.querySelector('.failed-record-error-icon i');
        const statusTitle = statusPanel?.querySelector('.failed-record-error-title');
        const isPending = card.dataset.status !== 'corrected';
        let savedStatusMessages = [];

        try {
            savedStatusMessages = JSON.parse(card.dataset.statusMessages || '[]');
        } catch (error) {
            savedStatusMessages = [];
        }

        card.classList.add('is-editing-status');
        statusPanel?.classList.remove('is-corrected');
        statusPanel?.classList.add('is-editing');

        if (statusIcon) statusIcon.className = 'fas fa-edit';
        if (statusTitle) statusTitle.textContent = 'Editando corrección';
        renderRecordErrors(card, [
            ...(isPending
                ? savedStatusMessages.map(message => ({ message, kind: 'error' }))
                : []),
            { message: 'Los cambios aún no se han guardado.', kind: 'note' },
        ]);
    }

    function restoreRecordStatus(card) {
        let statusMessages = [];

        try {
            statusMessages = JSON.parse(card.dataset.statusMessages || '[]');
        } catch (error) {
            statusMessages = [];
        }

        renderRecordStatus(card, {
            status: card.dataset.status,
            error_details: statusMessages,
        });
    }

    function correctionFields(form) {
        return form.querySelectorAll('input[name], select[name], textarea[name]');
    }

    function renderRecords(records) {
        const container = document.getElementById('records-list');
        container.innerHTML = '';

        records.forEach(record => {
            const template = document.getElementById('record-template');
            const clone = template.content.cloneNode(true);
            
            const card = clone.querySelector('.record-card');
            card.dataset.recordId = record.id;

            renderRecordStatus(card, record);

            const originalData = record.original_row_data || {};
            const formData = record.corrected_data || originalData;
            
            // Populate all form fields
            const form = clone.querySelector('.correction-form');
            correctionFields(form).forEach(field => {
                const fieldName = field.getAttribute('name');
                let value = formData[fieldName];
                
                // Map alternate field names
                if (!value && fieldName === 'folio') {
                    value = formData.folio_gob || originalData.folio_gob;
                }
                if (!value && fieldName === 'primerapellido') {
                    value = formData.primerapellid || originalData.primerapellid;
                }
                if (!value && fieldName === 'segundoapellido') {
                    value = formData.segundoapellid || originalData.segundoapellid;
                }
                
                // Special handling for sexo field - convert HOMBRE/MUJER to M/F
                if (fieldName === 'sexod' && value) {
                    if (value === 'HOMBRE') value = 'M';
                    else if (value === 'MUJER') value = 'F';
                }
                
                // Special handling for fechadefuncion - convert to YYYY-MM-DD format for date input
                if (fieldName === 'fechadefuncion' && value) {
                    value = parseDateForInput(value);
                }
                
                // Special handling for edad - split valor and unidad
                if (fieldName === 'edad_valor' || fieldName === 'edad_unidad') {
                    const edadStr = formData.edad_valor || originalData.edad_valor || formData.edad || originalData.edad || '';
                    if (edadStr) {
                        // Try to extract number and unit
                        const match = edadStr.match(/^(\d+)\s*(?:años|anos|years|a)?$/i);
                        if (match && fieldName === 'edad_valor') {
                            value = match[1];
                        } else if (fieldName === 'edad_unidad' && !value) {
                            const sourceUnit = String(
                                formData.claveedadd || originalData.claveedadd || formData.claveedad || originalData.claveedad || ''
                            ).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                            value = sourceUnit.includes('mes')
                                ? 'meses'
                                : (sourceUnit.includes('dia')
                                    ? 'dias'
                                    : (sourceUnit.includes('ano') || sourceUnit === 'a' ? 'anos' : ''));
                        }
                    }
                }
                
                if (fieldName === 'distrito') {
                    const residenceMunicipality =
                        formData.municipioresidenciad || originalData.municipioresidenciad || '';
                    value = districtNameForMunicipality(residenceMunicipality);
                }

                if (fieldName === 'jurisdicciondefunciond') {
                    const deathMunicipality = (formData.municipiodefunciond || originalData.municipiodefunciond || '').trim();
                    value = districtNameForMunicipality(deathMunicipality)
                        || formData.jurisdicciondefunciond
                        || originalData.jurisdicciondefunciond
                        || formData.distritodefunciond
                        || originalData.distritodefunciond
                        || '';
                }
                
                if (value !== undefined && value !== null && value !== '') {
                    field.value = value;
                    
                    // Para Tom Select fields, guardar el valor original ANTES de que Tom Select lo modifique
                    if (field.classList.contains('tomselect-select')) {
                        const fieldName = field.getAttribute('name');
                        const assignedData = selectCatalog(fieldName);
                        
                        // Check if this value exists in the available data
                        let valueExists = false;
                        if (assignedData.length > 0) {
                            const valUpper = String(value).toUpperCase().trim();
                            valueExists = assignedData.some(item => 
                                String(item.name).toUpperCase().trim() === valUpper
                            );
                        }
                        
                        // If value doesn't exist in data, set to "OTRO" for municipios
                        if (!valueExists && (fieldName === 'municipioresidenciad' || fieldName === 'municipiodefunciond')) {
                            value = 'OTRO';
                            field.value = value;
                        }
                        
                        // Guardar en dataset para recuperar más tarde
                        field.dataset.originalText = value;
                        field.dataset.originalValue = value;
                    }
                }
            });

            // Set the maximum date for fechadefuncion input to today (client timezone)
            const deathDateInput = form.querySelector('input[name="fechadefuncion"]');
            if (deathDateInput) {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayString = `${year}-${month}-${day}`;
                deathDateInput.max = todayString;
            }

            // Don't save originalFormData yet - we'll do it after Tom Select initialization
            // This prevents saving IDs instead of the text values we need for restoration

            attachRecordListeners(clone, record.id);
            container.appendChild(clone);
        });

        // Initialize Tom Select for all select elements in the new records
        setTimeout(() => {
            const allSelects = container.querySelectorAll('select.tomselect-select');
            allSelects.forEach((el) => {
                if (!el.tomselect) {
                    const name = el.getAttribute('name');
                    const data = selectCatalog(name);
                    
                    initializeTomSelectField(el, data);
                }
            });
            
            // NOW save originalFormData after Tom Select is initialized
            // Use originalValue (the original text from server) for Tom Select fields instead of the ID
            container.querySelectorAll('.record-card').forEach(card => {
                const form = card.querySelector('.correction-form');
                const originalFormData = {};
                correctionFields(form).forEach(field => {
                    if (field.classList.contains('tomselect-select')) {
                        // For Tom Select fields, use the ORIGINAL value from server (before any mapping)
                        originalFormData[field.getAttribute('name')] = field.dataset.originalValue || '';
                    } else {
                        originalFormData[field.getAttribute('name')] = field.value;
                    }
                });
                card.dataset.originalFormData = JSON.stringify(originalFormData);
                
            });
        }, 50);
    }

    function setRecordActionBusy(card, activeButton, isBusy, busyLabel = '') {
        card.setAttribute('aria-busy', isBusy ? 'true' : 'false');
        card.querySelectorAll('.action-buttons-view button, .action-buttons-edit button').forEach(button => {
            button.disabled = isBusy;
        });

        if (!activeButton) return;

        const label = activeButton.querySelector('[data-button-label]');
        const icon = activeButton.querySelector('i');

        if (isBusy) {
            if (label) {
                label.dataset.defaultLabel = label.textContent.trim();
                label.textContent = busyLabel;
            }
            if (icon) {
                icon.dataset.defaultClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
            }
        } else {
            if (label?.dataset.defaultLabel) {
                label.textContent = label.dataset.defaultLabel;
            }
            if (icon?.dataset.defaultClass) {
                icon.className = icon.dataset.defaultClass;
            }
        }
    }

    function finishRecordEditing(card, form) {
        const savedFormData = {};

        correctionFields(form).forEach(field => {
            const fieldName = field.getAttribute('name');
            const savedValue = field.classList.contains('tomselect-select')
                ? (field.dataset.originalText || '').trim()
                : (field.value || '').trim();

            savedFormData[fieldName] = savedValue;

            if (field.classList.contains('tomselect-select')) {
                field.dataset.originalValue = savedValue;
                field.dataset.originalText = savedValue;
                field.tomselect?.disable();
            }

            field.disabled = true;
        });

        card.dataset.originalFormData = JSON.stringify(savedFormData);
        card.classList.remove('is-editing-fields');
        card.querySelector('.action-buttons-edit')?.classList.add('hidden');
        card.querySelector('.action-buttons-view')?.classList.remove('hidden');
    }

    function correctionErrorElement(field) {
        const errorId = field?.getAttribute('aria-describedby');
        return errorId ? document.getElementById(errorId.split(/\s+/)[0]) : null;
    }

    function clearFieldError(field) {
        if (!field) return;
        field.removeAttribute('aria-invalid');
        field.tomselect?.control_input?.removeAttribute('aria-invalid');
        field.setCustomValidity?.('');
        field.tomselect?.control_input?.setCustomValidity('');
        const error = correctionErrorElement(field);
        if (error) {
            error.textContent = '';
            error.classList.add('hidden');
        }
    }

    function showFieldError(field, message) {
        if (!field) return false;
        field.setAttribute('aria-invalid', 'true');
        field.tomselect?.control_input?.setAttribute('aria-invalid', 'true');
        field.setCustomValidity?.(message);
        field.tomselect?.control_input?.setCustomValidity(message);
        const error = correctionErrorElement(field);
        if (error) {
            error.textContent = message;
            error.classList.remove('hidden');
        }
        return false;
    }

    function fieldRequiredMessage(field) {
        const messages = {
            folio: 'Ingrese el folio.',
            nombre: 'Ingrese el nombre.',
            primerapellido: 'Ingrese el apellido paterno.',
            sexod: 'Seleccione el sexo.',
            edad_valor: 'Ingrese la edad.',
            edad_unidad: 'Seleccione la unidad de la edad.',
            municipioresidenciad: 'Seleccione el municipio de residencia.',
            municipiodefunciond: 'Seleccione el municipio de defunción.',
            sitiodefunciond: 'Seleccione el lugar específico.',
            sheet: 'Seleccione la causa de la defunción.',
            fechadefuncion: 'Ingrese la fecha de defunción.',
        };
        return messages[field.name] || 'Complete este campo.';
    }

    function validateCorrectionField(field) {
        if (!field || field.disabled || field.hasAttribute('data-derived-field')) return true;
        const value = String(field.value || '').trim();

        if (field.required && value === '') return showFieldError(field, fieldRequiredMessage(field));
        if (value === '') {
            clearFieldError(field);
            return true;
        }

        if (field.name === 'folio' && !/^(?:[0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})$/.test(value)) {
            return showFieldError(field, 'Ingrese 9 dígitos o el folio alfanumérico oficial.');
        }
        if (field.minLength > 0 && value.length < field.minLength) {
            return showFieldError(field, `Ingrese al menos ${field.minLength} caracteres.`);
        }
        if (field.maxLength > 0 && value.length > field.maxLength) {
            return showFieldError(field, `Ingrese como máximo ${field.maxLength} caracteres.`);
        }
        if (field.name === 'fechadefuncion' && field.max && value > field.max) {
            return showFieldError(field, 'La fecha de defunción no puede ser posterior a hoy.');
        }

        clearFieldError(field);
        return true;
    }

    function prepareCorrectionForm(form, recordId) {
        const ageErrorId = `failed-${recordId}-age-error`;

        correctionFields(form).forEach(field => {
            const safeName = field.name.replace(/[^a-z0-9_-]/gi, '-');
            field.id = `failed-${recordId}-${safeName}`;

            const fieldGroup = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? field.closest('.flex')?.parentElement
                : field.parentElement;
            const firstNamedField = fieldGroup?.querySelector('[name]');
            const label = fieldGroup?.querySelector('label');
            if (label && firstNamedField === field) label.htmlFor = field.id;
            if (field.name === 'edad_unidad') field.setAttribute('aria-label', 'Unidad de la edad');

            const errorId = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? ageErrorId
                : `${field.id}-error`;
            field.setAttribute('aria-describedby', errorId);

            if (fieldGroup && !form.querySelector(`[id="${errorId}"]`)) {
                const error = document.createElement('p');
                error.id = errorId;
                error.className = 'failed-field-error hidden';
                error.setAttribute('role', 'alert');
                fieldGroup.appendChild(error);
            }

            field.addEventListener('blur', () => {
                if (field.name === 'edad_valor' || field.name === 'edad_unidad') validateAgeFields(form);
                else validateCorrectionField(field);
            });
            field.addEventListener('input', () => {
                if (!field.hasAttribute('aria-invalid')) return;
                if (field.name === 'edad_valor' || field.name === 'edad_unidad') validateAgeFields(form);
                else validateCorrectionField(field);
            });
        });
    }

    function focusCorrectionField(field) {
        field?.scrollIntoView?.({ behavior: 'smooth', block: 'center' });
        if (field?.tomselect) field.tomselect.focus();
        else field?.focus?.();
    }

    function validateCorrectionForm(form) {
        let firstInvalid = null;
        correctionFields(form).forEach(field => {
            const valid = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? validateAgeFields(form)
                : validateCorrectionField(field);
            if (!valid && !firstInvalid) firstInvalid = field;
        });
        if (firstInvalid) focusCorrectionField(firstInvalid);
        return !firstInvalid;
    }

    function attachRecordListeners(cardElement, recordId) {
        const card = cardElement.querySelector('.record-card');
        const form = cardElement.querySelector('.correction-form');
        const actionButtonsView = cardElement.querySelector('.action-buttons-view');
        const actionButtonsEdit = cardElement.querySelector('.action-buttons-edit');
        
        const btnToggleEdit = cardElement.querySelector('.btn-toggle-edit');
        const btnCancelEdit = cardElement.querySelector('.btn-cancel-edit');
        const btnSaveCorrection = cardElement.querySelector('.btn-save-correction');
        const btnRetry = cardElement.querySelector('.btn-retry');
        const btnImportCorrected = cardElement.querySelector('.btn-import-corrected');
        const btnDiscard = cardElement.querySelector('.btn-discard');

        prepareCorrectionForm(form, recordId);

        // Store original form state for cancel
        const originalFormData = {};
        correctionFields(form).forEach(field => {
            originalFormData[field.getAttribute('name')] = field.value;
        });
        card.dataset.originalFormData = JSON.stringify(originalFormData);

        // Toggle Edit: Enable inputs and show edit buttons
        if (btnToggleEdit) {
            btnToggleEdit.addEventListener('click', (e) => {
                e.preventDefault();

                card.classList.add('is-editing-fields');
                renderRecordEditingStatus(card);
                correctionFields(form).forEach(field => {
                    field.disabled = field.hasAttribute('data-derived-field');
                });
                
                // Destroy existing Tom Select instances and recreate with edit mode
                setTimeout(() => {
                    const editSelects = form.querySelectorAll('select.tomselect-select');
                    editSelects.forEach(el => {
                        // Destroy existing Tom Select if it exists
                        if (el.tomselect) {
                            el.tomselect.destroy();
                        }
                        
                        const name = el.getAttribute('name');
                        const data = selectCatalog(name);
                        
                        if (data.length > 0) {
                            el.disabled = el.hasAttribute('data-derived-field');
                            initializeTomSelectField(el, data);
                        }
                    });

                    const residenceMunicipalitySelect = form.querySelector('[name="municipioresidenciad"]');
                    const residenceDistrictInput = form.querySelector('[name="distrito"]');
                    const deathMunicipalitySelect = form.querySelector('[name="municipiodefunciond"]');
                    const deathDistrictInput = form.querySelector('[name="jurisdicciondefunciond"]');

                    const selectedMunicipalityName = select => {
                        const municipality = municipalitiesData.find(item => String(item.id) === String(select?.value));
                        return municipality?.name || select?.dataset.originalText || '';
                    };

                    const syncResidenceDistrict = () => {
                        const districtName = districtNameForMunicipality(selectedMunicipalityName(residenceMunicipalitySelect));
                        if (residenceDistrictInput) residenceDistrictInput.value = districtName;
                    };

                    const syncDeathDistrict = () => {
                        const districtName = districtNameForMunicipality(selectedMunicipalityName(deathMunicipalitySelect));
                        if (deathDistrictInput) deathDistrictInput.value = districtName;
                    };

                    residenceMunicipalitySelect?.tomselect?.on('change', syncResidenceDistrict);
                    deathMunicipalitySelect?.tomselect?.on('change', syncDeathDistrict);
                    syncResidenceDistrict();
                    syncDeathDistrict();
                }, 50);
                
                actionButtonsView.classList.add('hidden');
                actionButtonsEdit.classList.remove('hidden');
            });
        }

        // Cancel Edit: Disable inputs, restore original values, destroy Tom Select, show view buttons
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', (e) => {
                e.preventDefault();
                card.classList.remove('is-editing-fields');
                
                // First, restore original form data
                const originalData = JSON.parse(card.dataset.originalFormData || '{}');
                correctionFields(form).forEach(field => {
                    clearFieldError(field);
                    const fieldName = field.getAttribute('name');
                    const originalValue = originalData[fieldName] || '';
                    
                    // Restore the original value
                    field.value = originalValue;
                    
                    // For Tom Select fields, also restore the originalValue and originalText for later lookup
                    if (field.classList.contains('tomselect-select')) {
                        field.dataset.originalValue = originalValue;
                        field.dataset.originalText = originalValue;
                    }
                    
                    field.disabled = true;
                });
                
                // Then destroy and recreate Tom Select instances with restored values
                setTimeout(() => {
                    const editSelects = form.querySelectorAll('select.tomselect-select');
                    editSelects.forEach(el => {
                        // Destroy existing Tom Select if it exists
                        if (el.tomselect) {
                            el.tomselect.destroy();
                        }
                        
                        const name = el.getAttribute('name');
                        const data = selectCatalog(name);
                        
                        if (data.length > 0) {
                            el.disabled = true;
                            initializeTomSelectField(el, data);
                        }
                    });
                }, 50);
                
                actionButtonsEdit.classList.add('hidden');
                actionButtonsView.classList.remove('hidden');
                restoreRecordStatus(card);
            });
        }

        // Save Correction
        if (btnSaveCorrection) {
            btnSaveCorrection.addEventListener('click', (e) => {
                e.preventDefault();
                if (validateCorrectionForm(form)) {
                    saveCorrection(recordId, form, card, false, btnSaveCorrection);
                }
            });
        }

        // Retry Import
        if (btnRetry) {
            btnRetry.addEventListener('click', (e) => {
                e.preventDefault();
                if (validateCorrectionForm(form)) {
                    saveCorrection(recordId, form, card, true, btnRetry);
                }
            });
        }

        // Import a correction that was already validated and saved.
        if (btnImportCorrected) {
            btnImportCorrected.addEventListener('click', (e) => {
                e.preventDefault();
                saveCorrection(recordId, form, card, true, btnImportCorrected);
            });
        }

        // Discard Record
        if (btnDiscard) {
            btnDiscard.addEventListener('click', async (e) => {
                e.preventDefault();
                const confirmed = await window.confirmDeleteDialog({
                    title: 'Descartar registro',
                    subject: 'este registro fallido',
                    description: 'Se quitará de la lista y no podrá recuperarse.',
                    confirmText: 'Descartar'
                });

                if (!confirmed) {
                    return;
                }

                discardRecord(recordId, card, btnDiscard);
            });
        }
    }

    function validateAgeFields(form) {
        const edadValor = form.querySelector('[name="edad_valor"]');
        const edadUnidad = form.querySelector('[name="edad_unidad"]');
        if (!edadValor || !edadUnidad) return true;

        const valor = edadValor.value.trim();
        const unidad = edadUnidad.value;

        clearFieldError(edadValor);
        clearFieldError(edadUnidad);

        if (valor === '') return showFieldError(edadValor, 'Ingrese la edad.');
        if (!unidad) return showFieldError(edadUnidad, 'Seleccione la unidad de la edad.');

        const valorNum = Number(valor);
        if (!Number.isInteger(valorNum) || valorNum < 0) {
            return showFieldError(edadValor, 'Ingrese una edad con un número entero mayor o igual a 0.');
        }
        if (unidad === 'anos' && valorNum > 150) {
            return showFieldError(edadValor, 'Para años, ingrese un valor entre 0 y 150.');
        }
        if (unidad === 'meses' && valorNum > 11) {
            return showFieldError(edadValor, 'Para meses, ingrese un valor entre 0 y 11.');
        }
        if (unidad === 'dias' && valorNum > 30) {
            return showFieldError(edadValor, 'Para días, ingrese un valor entre 0 y 30.');
        }

        clearFieldError(edadValor);
        clearFieldError(edadUnidad);
        edadValor.max = String({ anos: 150, meses: 11, dias: 30 }[unidad] ?? 150);
        return true;
    }

    function showServerFieldErrors(form, errors) {
        const fieldByPrefix = [
            [/^Folio:/i, 'folio'],
            [/^Nombre:/i, 'nombre'],
            [/^Primer apellido:/i, 'primerapellido'],
            [/^Segundo apellido:/i, 'segundoapellido'],
            [/^Sexo:/i, 'sexod'],
            [/^(Edad|Unidad de edad):/i, 'edad_valor'],
            [/^Municipio de residencia:/i, 'municipioresidenciad'],
            [/^Municipio de defunción:/i, 'municipiodefunciond'],
            [/^Lugar de defunción:/i, 'sitiodefunciond'],
            [/^Causa de defunción:/i, 'sheet'],
            [/^Fecha de defunción:/i, 'fechadefuncion'],
        ];

        let firstInvalid = null;
        errors.forEach(message => {
            const match = fieldByPrefix.find(([pattern]) => pattern.test(message));
            if (!match) return;
            const field = form.querySelector(`[name="${match[1]}"]`);
            if (!field) return;
            showFieldError(field, String(message).replace(/^[^:]+:\s*/, ''));
            firstInvalid ||= field;
        });
        if (firstInvalid) focusCorrectionField(firstInvalid);
    }

    function saveCorrection(recordId, form, card, shouldRetry = false, activeButton = null) {
        const correctedData = {};
        correctionFields(form).forEach(field => {
            const fieldName = field.getAttribute('name');
            let value = '';
            
            // For Tom Select fields, use originalText (the name) instead of value (the ID)
            if (field.classList.contains('tomselect-select')) {
                value = field.dataset.originalText ? field.dataset.originalText.trim() : '';
            } else {
                value = field.value ? field.value.trim() : '';
            }
            
            correctedData[fieldName] = value;
        });

        const endpoint = shouldRetry 
            ? `/api/estadisticas/registros-fallidos/${recordId}/reintentar`
            : `/api/estadisticas/registros-fallidos/${recordId}/corregir`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        let cardWillBeRemoved = false;

        setRecordActionBusy(
            card,
            activeButton,
            true,
            shouldRetry ? 'Importando...' : 'Guardando...'
        );

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ corrected_data: correctedData }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                if (shouldRetry) {
                    cardWillBeRemoved = true;
                    // Animate removal without reloading page
                    card.style.transition = 'opacity 0.3s ease-out';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Check if there are any cards left on this page
                        const recordsList = document.getElementById('records-list');
                        if (recordsList.children.length === 0) {
                            // Reload current page or go to previous page if this was last item
                            const currentPageNum = currentPage;
                            loadFailedRecords(currentPageNum > 1 ? currentPageNum - 1 : currentPageNum);
                        }
                    }, 300);
                    notifyFailedImport('Registro importado.', 'success');
                } else {
                    finishRecordEditing(card, form);
                    renderRecordStatus(card, data.data || { status: 'corrected' });
                    notifyFailedImport('Cambios guardados.', 'success');
                }
            } else {
                if (data.errors && Array.isArray(data.errors)) {
                    renderRecordStatus(card, {
                        status: 'pending',
                        error_details: data.errors,
                    });
                    card.querySelector('.failed-record-error')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    showServerFieldErrors(form, data.errors);
                } else {
                    notifyFailedImport(data.message || 'No se pudieron guardar los cambios. Intenta nuevamente.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notifyFailedImport('No se pudieron guardar los cambios. Inténtalo nuevamente.', 'error');
        })
        .finally(() => {
            if (!cardWillBeRemoved && card.isConnected) {
                setRecordActionBusy(card, activeButton, false);
            }
        });
    }

    function discardRecord(recordId, card, activeButton = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        let cardWillBeRemoved = false;

        setRecordActionBusy(card, activeButton, true, 'Descartando...');

        fetch(`/api/estadisticas/registros-fallidos/${recordId}/descartar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                cardWillBeRemoved = true;
                notifyFailedImport('Registro descartado.', 'success');
                // Animate removal
                card.style.transition = 'opacity 0.3s ease-out';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    // Check if there are any cards left on this page
                    const recordsList = document.getElementById('records-list');
                    if (recordsList.children.length === 0) {
                        // Reload current page or go to previous page if this was last item
                        const currentPageNum = currentPage;
                        loadFailedRecords(currentPageNum > 1 ? currentPageNum - 1 : currentPageNum);
                    }
                }, 300);
            } else {
                notifyFailedImport(data.message || 'No se pudo descartar el registro. Inténtalo nuevamente.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notifyFailedImport('No se pudo descartar el registro. Inténtalo nuevamente.', 'error');
        })
        .finally(() => {
            if (!cardWillBeRemoved && card.isConnected) {
                setRecordActionBusy(card, activeButton, false);
            }
        });
    }

    function renderFailedPaginationPilot(data) {
        const container = document.getElementById('dt-pagination');
        updateFailedPaginationInfo(data);
        if (!container) return;
        const activePage = Number(data.current_page) || 1;
        const totalPages = Number(data.last_page) || 0;
        const pageButton = page => page === activePage
            ? `<span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">${page}</span>`
            : `<a href="#" data-page="${page}" class="pagination-link-failed-imports fb-page-btn fb-page-num">${page}</a>`;
        const ellipsis = () => '<span class="fb-page-btn fb-page-num fb-page-ellipsis">...</span>';
        let html = '<div class="fb-pagination" role="navigation" aria-label="Paginación de registros fallidos">';

        html += activePage === 1 || totalPages === 0
            ? '<span class="fb-page-btn fb-page-first fb-page-disabled">Anterior</span>'
            : `<a href="#" data-page="${activePage - 1}" class="pagination-link-failed-imports fb-page-btn fb-page-first">Anterior</a>`;

        if (totalPages <= 5) {
            for (let page = 1; page <= totalPages; page++) html += pageButton(page);
        } else if (activePage <= 3) {
            for (let page = 1; page <= 5; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        } else if (activePage >= totalPages - 2) {
            html += pageButton(1) + ellipsis();
            for (let page = totalPages - 4; page <= totalPages; page++) html += pageButton(page);
        } else {
            html += pageButton(1) + ellipsis();
            for (let page = activePage - 1; page <= activePage + 1; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        }

        html += activePage === totalPages || totalPages === 0
            ? '<span class="fb-page-btn fb-page-last fb-page-disabled">Siguiente</span>'
            : `<a href="#" data-page="${activePage + 1}" class="pagination-link-failed-imports fb-page-btn fb-page-last">Siguiente</a>`;
        html += '</div>';
        container.innerHTML = html;

        container.querySelectorAll('.pagination-link-failed-imports').forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();
                loadFailedRecords(Number(link.dataset.page));
                document.querySelector('.failed-imports-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function updateFailedPaginationInfo(data) {
        const footerInfo = document.getElementById('dt-info');
        if (!footerInfo) return;
        const from = Number(data.from) || 0;
        const to = Number(data.to) || 0;
        const total = Number(data.total) || 0;
        const totalPending = Number(data.total_pending ?? data.total) || 0;
        const unfilteredTotal = total !== totalPending
            ? ` <span class="text-sm text-gray-500">(de ${totalPending} totales)</span>`
            : '';
        footerInfo.classList.remove('is-loading');
        footerInfo.innerHTML = `<span class="users-table-info-main">Mostrando <span class="font-semibold text-gray-900">${from}-${to}</span> de <span class="font-semibold text-gray-900">${total}</span>${unfilteredTotal}</span>`;
    }

    loadFailedRecords(1);
});
</script>
@endpush
