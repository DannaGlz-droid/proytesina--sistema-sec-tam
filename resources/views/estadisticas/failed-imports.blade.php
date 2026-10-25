@extends('layouts.principal')
@section('title', 'Registros Fallidos de Importación')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-failed-imports-page px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <div id="failed-imports-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>
        <x-ui.page-header class="users-management-header statistics-failed-imports-header" title="Importaciones fallidas" description="Revise, corrija o descarte los registros que no pudieron importarse.">
            <x-slot:actions><a href="{{ route('statistic.import-history-view') }}" class="users-page-create-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al historial</a></x-slot:actions>
        </x-ui.page-header>

        <section class="app-table-card users-table-card failed-imports-card" aria-label="Registros fallidos de la importación">
            <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                <section class="users-filter-card failed-imports-controls" aria-label="Controles de registros fallidos">
                    <div class="users-filter-form">
                        <div class="users-filter-topbar">
                            <div class="failed-imports-file-context">
                                <span class="failed-imports-file-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                                <div>
                                    <strong title="{{ $importFileName }}">{{ $importFileName }}</strong>
                                    <small id="failed-imports-pending-count" aria-live="polite">Consultando registros pendientes…</small>
                                </div>
                            </div>
                            <div class="users-filter-search"><i class="fas fa-search" aria-hidden="true"></i><input id="search-failed-imports" type="search" placeholder="Buscar registros fallidos..." aria-label="Buscar registros fallidos" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false"><span class="users-search-progress" aria-hidden="true"></span><button id="clear-failed-imports-search" type="button" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            <div class="app-table-page-size users-filter-page-size"><select id="per-page-failed-imports" class="users-native-page-size" aria-hidden="true" tabindex="-1">@foreach([4,6,10] as $size)<option value="{{ $size }}" @selected($size === 4)>{{ $size }}</option>@endforeach</select><div class="users-page-size-dropdown"><button id="per-page-failed-imports-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false"><span>Mostrar</span><strong id="per-page-failed-imports-label">4</strong><i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i></button><div id="per-page-failed-imports-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="per-page-failed-imports-button">@foreach([4,6,10] as $size)<button type="button" role="option" class="users-page-size-option {{ $size === 4 ? 'is-active' : '' }}" data-value="{{ $size }}">{{ $size }}</button>@endforeach</div></div></div>
                        </div>
                    </div>
                </section>
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
    <article class="record-card" data-record-id="">
        
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
            <form class="correction-form ui-form-fields" novalidate data-ui-inline-validation>
                <section class="failed-correction-section failed-correction-section--identity">
                    <div class="failed-correction-section-heading">
                        <i class="far fa-user" aria-hidden="true"></i>
                        <h2>Información de la persona</h2>
                    </div>
                    <div class="failed-correction-grid failed-correction-grid--three">
                        <div class="failed-correction-field">
                            <div class="statistics-form-label-row">
                                <label>Folio <span class="text-red-600">*</span></label>
                                <span class="statistics-form-help-popover">
                                    <button type="button" class="statistics-form-help-trigger" aria-label="Cómo capturar el folio">
                                        <i class="fas fa-circle-question" aria-hidden="true"></i>
                                    </button>
                                    <span class="statistics-form-tooltip" role="tooltip">Capture 9 dígitos o el folio alfanumérico oficial.</span>
                                </span>
                            </div>
                            <input name="folio" type="text" value="" disabled required minlength="9" maxlength="17"
                                pattern="([0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})"
                                title="Capture 9 dígitos o el folio alfanumérico oficial" inputmode="text"
                                placeholder="Ej: 230787888 o 28M19673E00000007">
                        </div>
                        <div class="failed-correction-field">
                            <label>Nombre(s) <span class="text-red-600">*</span></label>
                            <input name="nombre" type="text" value="" disabled required minlength="2" maxlength="191" placeholder="Ej: Juan Diego" autocomplete="given-name">
                        </div>
                        <div class="failed-correction-field">
                            <label>Apellido paterno <span class="text-red-600">*</span></label>
                            <input name="primerapellido" type="text" value="" disabled required minlength="2" maxlength="191" placeholder="Ej: García" autocomplete="family-name">
                        </div>
                        <div class="failed-correction-field">
                            <label>Apellido materno</label>
                            <input name="segundoapellido" type="text" value="" disabled minlength="2" maxlength="191" placeholder="Ej: López">
                        </div>
                        <div class="failed-correction-field">
                            <label>Sexo <span class="text-red-600">*</span></label>
                            <select name="sexod" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione el sexo">
                                <option value="">Seleccione una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="failed-correction-field">
                            <label>Edad <span class="text-red-600">*</span></label>
                            <div class="failed-age-fields">
                                <input name="edad_valor" type="number" min="0" max="150" value="" required disabled inputmode="numeric" placeholder="Ej: 34" aria-label="Valor de la edad">
                                <select name="edad_unidad" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione la unidad">
                                    <option value="">Seleccione la unidad</option>
                                    <option value="anos">Años</option>
                                    <option value="meses">Meses</option>
                                    <option value="dias">Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="failed-correction-section failed-correction-section--location">
                    <div class="failed-correction-section-heading">
                        <i class="fas fa-location-dot" aria-hidden="true"></i>
                        <h2>Ubicación</h2>
                    </div>
                    <div class="failed-location-grid">
                        <div class="failed-location-column">
                            <div class="failed-correction-field">
                                <label>Municipio de residencia <span class="text-red-600">*</span></label>
                                <select name="municipioresidenciad" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione un municipio">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                            <div class="failed-correction-field">
                                <label>Municipio de defunción <span class="text-red-600">*</span></label>
                                <select name="municipiodefunciond" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione un municipio">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>
                        <div class="failed-location-column">
                            <div class="failed-correction-field">
                                <div class="statistics-form-label-row">
                                    <label>Distrito de residencia</label>
                                    <span class="statistics-form-help-popover">
                                        <button type="button" class="statistics-form-help-trigger" aria-label="Cómo se asigna el distrito de residencia">
                                            <i class="fas fa-circle-question" aria-hidden="true"></i>
                                        </button>
                                        <span class="statistics-form-tooltip" role="tooltip">Se asigna automáticamente según el municipio de residencia.</span>
                                    </span>
                                </div>
                                <input name="distrito" type="text" value="" readonly disabled data-derived-field aria-disabled="true" placeholder="Seleccione un municipio primero">
                            </div>
                            <div class="failed-correction-field">
                                <div class="statistics-form-label-row">
                                    <label>Distrito de defunción</label>
                                    <span class="statistics-form-help-popover">
                                        <button type="button" class="statistics-form-help-trigger" aria-label="Cómo se asigna el distrito de defunción">
                                            <i class="fas fa-circle-question" aria-hidden="true"></i>
                                        </button>
                                        <span class="statistics-form-tooltip" role="tooltip">Se asigna automáticamente según el municipio de defunción.</span>
                                    </span>
                                </div>
                                <input name="jurisdicciondefunciond" type="text" value="" readonly disabled data-derived-field aria-disabled="true" placeholder="Seleccione un municipio primero">
                            </div>
                        </div>
                        <div class="failed-correction-field failed-location-place">
                            <label>Lugar de defunción <span class="text-red-600">*</span></label>
                            <select name="sitiodefunciond" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione un lugar">
                                <option value="">Seleccione un lugar</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="failed-correction-section failed-correction-section--death">
                    <div class="failed-correction-section-heading">
                        <i class="far fa-clipboard" aria-hidden="true"></i>
                        <h2>Información de la defunción</h2>
                    </div>
                    <div class="failed-correction-grid failed-correction-grid--two">
                        <div class="failed-correction-field">
                            <label>Causa de la defunción <span class="text-red-600">*</span></label>
                            <select name="sheet" required disabled class="tomselect-select" data-select-variant="form" data-placeholder="Seleccione una causa">
                                <option value="">Seleccione una causa</option>
                            </select>
                        </div>
                        <div class="failed-correction-field">
                            <label>Fecha de defunción <span class="text-red-600">*</span></label>
                            <input name="fechadefuncion" type="date" value="" required disabled class="fecha-input" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                </section>

                <!-- Action Buttons - View Mode -->
                <div class="action-buttons-view flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="ui-button ui-button--secondary btn-discard">
                        <i class="fas fa-spinner fa-spin" data-button-spinner aria-hidden="true" hidden></i>
                        <span data-button-label>Descartar registro</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-toggle-edit">
                        <span data-button-label>Editar registro</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-import-corrected" hidden>
                        <i class="fas fa-spinner fa-spin" data-button-spinner aria-hidden="true" hidden></i>
                        <span data-button-label>Importar registro</span>
                    </button>
                </div>

                <!-- Action Buttons - Edit Mode -->
                <div class="action-buttons-edit hidden flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="ui-button ui-button--secondary btn-cancel-edit">
                        <span data-button-label>Cancelar</span>
                    </button>
                    <button type="button" class="ui-button ui-button--secondary btn-save-correction">
                        <i class="fas fa-spinner fa-spin" data-button-spinner aria-hidden="true" hidden></i>
                        <span data-button-label>Guardar corrección</span>
                    </button>
                    <button type="button" class="ui-button ui-button--primary btn-retry">
                        <i class="fas fa-spinner fa-spin" data-button-spinner aria-hidden="true" hidden></i>
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
        return [$m->name => $jur ? $jur->display_name : ''];
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

function selectedMunicipalityName(select) {
    const municipality = municipalitiesData.find(item => String(item.id) === String(select?.value));

    return municipality?.name
        || select?.dataset.originalText
        || select?.dataset.originalValue
        || '';
}

function syncDistrictFields(form) {
    const residenceMunicipality = form.querySelector('[name="municipioresidenciad"]');
    const residenceDistrict = form.querySelector('[name="distrito"]');
    const deathMunicipality = form.querySelector('[name="municipiodefunciond"]');
    const deathDistrict = form.querySelector('[name="jurisdicciondefunciond"]');

    if (residenceDistrict) {
        const districtName = districtNameForMunicipality(selectedMunicipalityName(residenceMunicipality));
        if (districtName) residenceDistrict.value = districtName;
    }

    if (deathDistrict) {
        const districtName = districtNameForMunicipality(selectedMunicipalityName(deathMunicipality));
        if (districtName) deathDistrict.value = districtName;
    }
}

@php
    $municipalitySelectData = $municipalities->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'display_name' => $item->display_name,
        ];
    })->values();
    $locationSelectData = $locations->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'display_name' => $item->display_name,
        ];
    })->values();
    $causeSelectData = $causes->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'display_name' => $item->display_name,
        ];
    })->values();
@endphp
// Build data for Tom Select. Keep stored names separate from presentation labels.
const municipalitiesData = @json($municipalitySelectData);
const locationsData = @json($locationSelectData);
const causesData = @json($causeSelectData);
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
    // Match the registration form exactly: only the two short fixed
    // catalogues omit search; municipalities, locations and causes keep it.
    return !['sexod', 'edad_unidad'].includes(name);
}

document.addEventListener('DOMContentLoaded', function() {
    const importId = {{ $importId }};
    let currentPage = 1;
    let isInitialLoad = true;
    let failedImportsPerPage = 4;
    let failedImportsSearch = '';
    let failedImportsPaginationState = {
        current_page: 1,
        last_page: 0,
        from: 0,
        to: 0,
        total: 0,
        total_pending: 0,
    };
    const failedImportsNavigationState = { navigating: false, confirming: false };

    function announceFailedImportStatus(message) {
        const announcer = document.getElementById('failed-imports-announcer');
        if (!announcer) return;
        announcer.textContent = '';
        window.requestAnimationFrame(() => { announcer.textContent = message; });
    }

    function notifyFailedImport(message, type = 'success', duration = 3000) {
        const toastMethod = window.AppToast?.[type];
        if (typeof toastMethod === 'function') {
            toastMethod(message, { duration });
            return;
        }

        if (typeof window.showToast === 'function') {
            window.showToast(message, type, duration);
            return;
        }

        const logger = type === 'error' ? console.error : console.log;
        logger(message);
    }

    function getUnsavedFailedImportCards() {
        return Array.from(document.querySelectorAll('.record-card.is-editing-fields')).filter(card => {
            const form = card.querySelector('.correction-form');
            if (!form) return false;

            let editingBaseline = {};
            try {
                editingBaseline = JSON.parse(
                    card.dataset.editingFormBaseline
                    || card.dataset.originalFormData
                    || '{}'
                );
            } catch (error) {
                editingBaseline = {};
            }

            return JSON.stringify(correctionFormSnapshot(form)) !== JSON.stringify(editingBaseline);
        });
    }

    function hasUnsavedFailedImportChanges() {
        return getUnsavedFailedImportCards().length > 0;
    }

    async function confirmDiscardFailedImportChanges(options = {}) {
        const unsavedCards = getUnsavedFailedImportCards();
        const unsavedCount = unsavedCards.length;
        if (unsavedCount === 0) return true;
        if (failedImportsNavigationState.confirming) return false;

        failedImportsNavigationState.confirming = true;
        try {
            const question = options.question || '¿Deseas continuar?';
            const description = options.description || (unsavedCount === 1
                ? 'Los cambios de la corrección que estás editando no se guardarán.'
                : `Los cambios de las ${unsavedCount} correcciones que estás editando no se guardarán.`);
            if (typeof window.confirmDialog !== 'function') {
                return window.confirm(`${question}\n\n${description}`);
            }

            return await window.confirmDialog({
                title: 'Descartar cambios',
                question,
                description,
                confirmText: options.confirmText || 'Continuar sin guardar',
                cancelText: 'Continuar editando',
                variant: 'warning',
            });
        } finally {
            failedImportsNavigationState.confirming = false;
        }
    }

    const failedSearchInput = document.getElementById('search-failed-imports');
    const failedSearchClear = document.getElementById('clear-failed-imports-search');
    const failedSearchControl = failedSearchInput?.closest('.users-filter-search');
    const failedPerPageSelect = document.getElementById('per-page-failed-imports');
    const failedPerPageButton = document.getElementById('per-page-failed-imports-button');
    const failedPerPageMenu = document.getElementById('per-page-failed-imports-menu');

    failedSearchInput?.setAttribute('aria-busy', 'false');
    const updateFailedSearchClear = () => failedSearchClear?.classList.toggle('hidden', !failedSearchInput?.value);
    const applyFailedSearch = async (nextSearch = failedSearchInput?.value || '') => {
        const shouldContinue = await confirmDiscardFailedImportChanges({
            question: nextSearch ? '¿Deseas aplicar otra búsqueda?' : '¿Deseas limpiar la búsqueda?',
            confirmText: nextSearch ? 'Buscar sin guardar' : 'Limpiar sin guardar',
        });
        if (!shouldContinue) {
            if (failedSearchInput) failedSearchInput.value = failedImportsSearch;
            updateFailedSearchClear();
            return false;
        }

        if (failedSearchInput) failedSearchInput.value = nextSearch;
        failedImportsSearch = nextSearch.trim();
        updateFailedSearchClear();
        failedSearchControl?.classList.add('is-searching');
        failedSearchInput?.setAttribute('aria-busy', 'true');
        isInitialLoad = false;
        loadFailedRecords(1);
        return true;
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
            void applyFailedSearch();
        }
    });
    failedSearchClear?.addEventListener('click', () => {
        void applyFailedSearch('');
    });
    document.getElementById('records-list')?.addEventListener('click', event => {
        const action = event.target.closest('[data-failed-state-action="search"]');
        if (!action) return;

        void applyFailedSearch('').then(applied => {
            if (applied) failedSearchInput.focus();
        });
    });

    failedPerPageButton?.addEventListener('click', () => {
        const open = failedPerPageMenu?.classList.contains('hidden');
        failedPerPageMenu?.classList.toggle('hidden', !open);
        failedPerPageButton.setAttribute('aria-expanded', String(open));
    });
    failedPerPageMenu?.querySelectorAll('[data-value]').forEach(option => option.addEventListener('click', async () => {
        const shouldContinue = await confirmDiscardFailedImportChanges({
            question: '¿Deseas cambiar la cantidad de registros?',
            confirmText: 'Cambiar sin guardar',
        });
        if (!shouldContinue) return;

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
            || normalize(option.display_name) === normalizedValue
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
            name: item.display_name || item.name,
            rawName: item.name,
            text: item.display_name || item.name
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
        if (!selectedId && !el.disabled) {
            el.dataset.originalText = '';
        }
        
        const ts = window.AppFilterSelect?.init(el, {
            options,
            items: selectedId ? [selectedId] : [],
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            searchable: selectIsSearchable(name),
            placeholder: el.dataset.placeholder || 'Seleccione una opción',
            maxOptions: 100,
            allowEmptyOption: false,
            hideSelected: false,
            disable: el.disabled,
            onChange(value) {
                const selected = data.find(item => String(item.id) === String(value));
                el.dataset.originalText = selected?.name || '';
                const form = el.closest('form');
                const ageValue = form?.querySelector('[name="edad_valor"]');
                const shouldRevalidate = form?.dataset.validationAttempted === 'true'
                    || el.hasAttribute('aria-invalid')
                    || (name === 'edad_unidad' && ageValue?.hasAttribute('aria-invalid'));

                if (name === 'edad_unidad') {
                    syncCorrectionAgeLimit(form);
                    if (shouldRevalidate) validateAgeFields(form);
                    else clearFieldError(el);
                    return;
                }

                if (shouldRevalidate) validateCorrectionField(el);
                else clearFieldError(el);
            },
        });
        if (!ts) return null;

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

        if (name === 'edad_unidad') syncCorrectionAgeLimit(el.closest('form'));
        
        return ts;
    }

    function friendlyRecordError(error) {
        const message = String(error || '').trim();
        const causeMatch = message.match(/^Causa no reconocida:\s*"([\s\S]+?)"\.\s*Use una de las causas permitidas\.?$/i);
        if (causeMatch) return `Causa de defunción: "${causeMatch[1]}" no es una opción válida. Seleccione una causa permitida.`;

        const legacyMessages = {
            'Nombre vacío': 'Nombre: este campo es obligatorio.',
            'Primer apellido vacío': 'Primer apellido: este campo es obligatorio.',
            'Edad vacía o inválida': 'Edad: Capture una edad válida.',
            'Lugar de defunción vacío': 'Lugar de defunción: este campo es obligatorio.',
            'Causa no indicada en la hoja ni en la fila.': 'Causa de defunción: no está indicada. Seleccione una causa permitida.',
            'Folio duplicado en el archivo (todas las ocurrencias rechazadas)': 'Folio: está repetido dentro del archivo. Corrija o elimine las filas duplicadas.',
            'Folio duplicado detectado en la base de datos': 'Folio: ya existe en el sistema. Ingrese uno diferente.',
            'El folio ya existe en la base de datos': 'Folio: ya existe en el sistema. Ingrese uno diferente.',
            'Edad inválida: meses debe ser mayor o igual a 0': 'Edad: Capture una edad válida.',
            'Edad inválida: meses debe ser menor a 12': 'Edad: Use años para edades de 12 meses o más.',
            'Edad inválida: días debe ser mayor o igual a 0': 'Edad: Capture una edad válida.',
            'Edad inválida: días debe ser menor o igual a 30': 'Edad: La edad en días no puede ser mayor a 30.',
            'Edad inválida: años debe ser mayor o igual a 0': 'Edad: Capture una edad válida.',
            'Edad inválida: años debe ser menor o igual a 150': 'Edad: La edad en años no puede ser mayor a 150.',
            'Fecha futura: la fecha de defunción no puede ser mayor a hoy': 'Fecha de defunción: no puede ser posterior a hoy.',
        };

        if (legacyMessages[message]) return legacyMessages[message];
        if (/^Folio gubernamental inválido o ausente/i.test(message)) {
            return 'Folio: ingrese 9 dígitos o un folio alfanumérico de defunción válido.';
        }
        if (/^Sexo inválido o ausente/i.test(message)) return 'Sexo: seleccione Masculino o Femenino.';
        if (/^Edad:\s*para meses,\s*ingrese un valor entre 0 y 11\.?$/i.test(message)) {
            return 'Edad: Use años para edades de 12 meses o más.';
        }
        if (/^Fecha (?:de defunción )?inválida/i.test(message)) return 'Fecha de defunción: ingrese una fecha válida.';

        return message;
    }

    function capitalizePrefixedErrorMessage(message) {
        return String(message).replace(
            /^([^:]+:\s*)([a-záéíóúüñ])/,
            (_, prefix, firstLetter) => prefix + firstLetter.toLocaleUpperCase('es-MX')
        );
    }

    function recordErrorDetails(record) {
        if (Array.isArray(record?.error_details) && record.error_details.length > 0) {
            return record.error_details
                .filter(error => typeof error === 'string' && error.trim() !== '')
                .map(error => capitalizePrefixedErrorMessage(friendlyRecordError(error)));
        }

        return String(record?.error_message || '')
            .split(/;\s*/)
            .map(error => error.trim())
            .filter(Boolean)
            .map(error => capitalizePrefixedErrorMessage(friendlyRecordError(error)));
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
        const visibleStatusMessages = isCorrected
            ? statusMessages
            : ['Edita el registro para revisar los campos señalados.'];

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

        renderRecordErrors(card, visibleStatusMessages);

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
            { message: 'Corrige los campos marcados en rojo.', kind: 'instruction' },
        ]);

        return isPending ? savedStatusMessages : [];
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

    function correctionFormSnapshot(form) {
        const snapshot = {};
        correctionFields(form).forEach(field => {
            snapshot[field.name] = field.classList.contains('tomselect-select')
                ? String(field.dataset.originalText || '').trim()
                : String(field.value || '').trim();
        });
        return snapshot;
    }

    function renderRecords(records, options = {}) {
        const container = document.getElementById('records-list');
        const append = options.append === true;
        const renderedCards = [];
        if (!append) container.innerHTML = '';

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
                            valueExists = findMatchingItemId(value, assignedData) !== '';
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
            renderedCards.push(card);
        });

        // Initialize only the cards created by this call. Existing cards may
        // contain in-progress edits and must keep their controls and baselines.
        setTimeout(() => {
            renderedCards.forEach(card => {
                card.querySelectorAll('select.tomselect-select').forEach((el) => {
                    if (!el.tomselect) {
                        const name = el.getAttribute('name');
                        const data = selectCatalog(name);

                        initializeTomSelectField(el, data);
                    }
                });

                // Save the baseline only for the newly rendered card.
                const form = card.querySelector('.correction-form');
                syncDistrictFields(form);
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
        const spinner = activeButton.querySelector('[data-button-spinner]');

        if (isBusy) {
            activeButton.style.width = `${activeButton.getBoundingClientRect().width}px`;
            activeButton.setAttribute('aria-busy', 'true');
            if (label) {
                label.dataset.defaultLabel = label.textContent.trim();
                label.textContent = busyLabel;
            }
            if (spinner) spinner.hidden = false;
        } else {
            if (label?.dataset.defaultLabel) {
                label.textContent = label.dataset.defaultLabel;
            }
            if (spinner) spinner.hidden = true;
            activeButton.removeAttribute('aria-busy');
            activeButton.style.removeProperty('width');
        }
    }

    const recordModeTransitionTimers = new WeakMap();

    function playRecordModeTransition(card, mode) {
        if (!card) return;

        const transitionClass = mode === 'edit'
            ? 'is-entering-edit-mode'
            : 'is-entering-view-mode';
        const previousTimer = recordModeTransitionTimers.get(card);

        if (previousTimer) window.clearTimeout(previousTimer);
        card.classList.remove('is-entering-edit-mode', 'is-entering-view-mode');

        // Restart the brief transition even when the same card changes mode twice.
        void card.offsetWidth;
        card.classList.add(transitionClass);

        const timer = window.setTimeout(() => {
            card.classList.remove(transitionClass);
            recordModeTransitionTimers.delete(card);
        }, 190);
        recordModeTransitionTimers.set(card, timer);
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
        delete card.dataset.editingFormBaseline;
        delete form.dataset.validationAttempted;
        card.classList.remove('is-editing-fields');
        card.querySelector('.action-buttons-edit')?.classList.add('hidden');
        card.querySelector('.action-buttons-view')?.classList.remove('hidden');
        playRecordModeTransition(card, 'view');
    }

    function fillFailedRecordPageGap(recordsList) {
        const query = new URLSearchParams({ page: currentPage, per_page: failedImportsPerPage });
        if (failedImportsSearch) query.set('search', failedImportsSearch);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(`/api/estadisticas/importaciones/${importId}/registros-fallidos?${query.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
        })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (!data?.ok || !data.data) throw new Error(data?.message || 'Invalid response');

                const records = Array.isArray(data.data) ? data.data : (data.data.data || []);
                const existingIds = new Set(
                    Array.from(recordsList.querySelectorAll('.record-card'))
                        .map(existingCard => String(existingCard.dataset.recordId || ''))
                );
                const missingRecords = records.filter(record => !existingIds.has(String(record.id)));

                if (missingRecords.length > 0) {
                    renderRecords(missingRecords, { append: true });
                }

                const paginationData = Array.isArray(data.data) ? null : data.data;
                if (paginationData) {
                    renderFailedPaginationPilot({
                        ...paginationData,
                        total_pending: Number(data.total_pending) || Number(paginationData.total) || 0,
                    });
                    currentPage = Number(paginationData.current_page) || currentPage;
                }
            })
            .catch(error => {
                // The primary action already succeeded. Preserve the remaining
                // cards and let the next global list load fill the open slot.
                console.error('Error filling failed-record page gap:', error);
            });
    }

    function removeRecordCardAndUpdateList(card) {
        card.style.transition = 'opacity 0.3s ease-out';
        card.style.opacity = '0';

        setTimeout(() => {
            card.remove();

            const recordsList = document.getElementById('records-list');
            const visibleCount = recordsList?.querySelectorAll('.record-card').length || 0;
            const total = Math.max(0, failedImportsPaginationState.total - 1);
            const totalPending = Math.max(0, failedImportsPaginationState.total_pending - 1);
            const lastPage = total > 0 ? Math.ceil(total / failedImportsPerPage) : 0;

            // Keep every remaining card node intact so in-progress edits survive
            // an import or discard performed on a different card.
            if (visibleCount > 0) {
                const from = failedImportsPaginationState.from || ((currentPage - 1) * failedImportsPerPage + 1);
                renderFailedPaginationPilot({
                    current_page: currentPage,
                    last_page: lastPage,
                    from,
                    to: from + visibleCount - 1,
                    total,
                    total_pending: totalPending,
                });
                const expectedVisibleCount = Math.min(
                    failedImportsPerPage,
                    Math.max(0, total - ((currentPage - 1) * failedImportsPerPage))
                );
                if (visibleCount < expectedVisibleCount) {
                    fillFailedRecordPageGap(recordsList);
                }
                return;
            }

            if (total === 0) {
                recordsList.classList.toggle('is-search-empty', Boolean(failedImportsSearch));
                recordsList.innerHTML = failedImportsEmptyStateMarkup();
                renderFailedPaginationPilot({
                    current_page: 1,
                    last_page: 0,
                    from: 0,
                    to: 0,
                    total: 0,
                    total_pending: totalPending,
                });
                return;
            }

            // An empty page has no remaining edit state to preserve.
            loadFailedRecords(Math.min(currentPage, lastPage));
        }, 300);
    }

    function correctionErrorElement(field) {
        const describedBy = field?.getAttribute('aria-describedby') || '';
        const describedError = describedBy
            .split(/\s+/)
            .filter(Boolean)
            .map(id => document.getElementById(id))
            .find(element => element?.classList.contains('failed-field-error')) || null;

        if (describedError) return describedError;

        // Age value and unit share one message. Other validation helpers may
        // update aria-describedby while typing, so keep a structural fallback
        // to prevent a visible message from becoming detached from its field.
        return field?.closest('.failed-correction-field')
            ?.querySelector('.failed-field-error') || null;
    }

    function clearFieldError(field) {
        if (!field) return;
        field.classList.remove('has-field-error');
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
        field.classList.add('has-field-error');
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
            edad_valor: 'Capture una edad válida.',
            edad_unidad: 'Seleccione la unidad de la edad.',
            municipioresidenciad: 'Seleccione el municipio de residencia.',
            municipiodefunciond: 'Seleccione el municipio de defunción.',
            sitiodefunciond: 'Seleccione el lugar de defunción.',
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

        form.querySelectorAll('.statistics-form-help-popover').forEach((popover, index) => {
            const trigger = popover.querySelector('.statistics-form-help-trigger');
            const tooltip = popover.querySelector('.statistics-form-tooltip');
            if (!trigger || !tooltip) return;
            const tooltipId = `failed-${recordId}-help-${index}`;
            tooltip.id = tooltipId;
            trigger.setAttribute('aria-describedby', tooltipId);
        });

        correctionFields(form).forEach(field => {
            const safeName = field.name.replace(/[^a-z0-9_-]/gi, '-');
            field.id = `failed-${recordId}-${safeName}`;

            const fieldGroup = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? field.closest('.failed-age-fields')?.parentElement
                : field.parentElement;
            const firstNamedField = fieldGroup?.querySelector('[name]');
            const label = fieldGroup?.querySelector('label');
            if (label && firstNamedField === field) label.htmlFor = field.id;
            if (field.name === 'edad_unidad') field.setAttribute('aria-label', 'Unidad de la edad');

            const errorId = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? ageErrorId
                : `${field.id}-error`;
            const helpTooltip = fieldGroup?.querySelector('.statistics-form-tooltip');
            field.setAttribute(
                'aria-describedby',
                [helpTooltip?.id, errorId].filter(Boolean).join(' ')
            );

            if (fieldGroup && !form.querySelector(`[id="${errorId}"]`)) {
                const error = document.createElement('p');
                error.id = errorId;
                error.className = 'failed-field-error hidden';
                error.setAttribute('role', 'alert');
                fieldGroup.appendChild(error);
            }

            field.addEventListener('input', () => {
                if (form.dataset.validationAttempted !== 'true' && !field.hasAttribute('aria-invalid')) return;
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

    function validateCorrectionForm(form, shouldFocus = true) {
        form.dataset.validationAttempted = 'true';
        let firstInvalid = null;
        correctionFields(form).forEach(field => {
            const valid = field.name === 'edad_valor' || field.name === 'edad_unidad'
                ? validateAgeFields(form)
                : validateCorrectionField(field);
            if (!valid && !firstInvalid) firstInvalid = field;
        });
        if (firstInvalid && shouldFocus) focusCorrectionField(firstInvalid);
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

                delete card.dataset.editingFormBaseline;
                card.classList.add('is-editing-fields');
                const pendingFieldErrors = renderRecordEditingStatus(card);
                correctionFields(form).forEach(field => {
                    field.disabled = field.hasAttribute('data-derived-field');
                });

                actionButtonsView.classList.add('hidden');
                actionButtonsEdit.classList.remove('hidden');
                
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
                    const deathMunicipalitySelect = form.querySelector('[name="municipiodefunciond"]');

                    residenceMunicipalitySelect?.tomselect?.on('change', () => syncDistrictFields(form));
                    deathMunicipalitySelect?.tomselect?.on('change', () => syncDistrictFields(form));
                    syncDistrictFields(form);
                    // Validate the complete current record as soon as editing begins.
                    // Stored import errors can be incomplete for legacy records, while
                    // server messages below preserve more specific source details.
                    validateCorrectionForm(form, false);
                    showServerFieldErrors(form, pendingFieldErrors, false);
                    // The editable baseline must reflect the normalized controls the
                    // user actually receives. Invalid source values can become an
                    // empty catalog select here and are not user-made changes.
                    card.dataset.editingFormBaseline = JSON.stringify(correctionFormSnapshot(form));
                    playRecordModeTransition(card, 'edit');
                    announceFailedImportStatus('Modo de edición activado.');
                }, 50);
            });
        }

        // Cancel Edit: Disable inputs, restore original values, destroy Tom Select, show view buttons
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', async (e) => {
                e.preventDefault();
                const originalData = JSON.parse(card.dataset.originalFormData || '{}');
                const editingBaseline = JSON.parse(
                    card.dataset.editingFormBaseline
                    || card.dataset.originalFormData
                    || '{}'
                );
                const hasChanges = JSON.stringify(correctionFormSnapshot(form)) !== JSON.stringify(editingBaseline);

                if (hasChanges) {
                    const confirmed = typeof window.confirmDialog === 'function'
                        ? await window.confirmDialog({
                            title: 'Descartar cambios',
                            question: '¿Deseas cancelar la edición?',
                            description: 'Los datos volverán al estado que tenían antes de editar este registro.',
                            confirmText: 'Descartar cambios',
                            cancelText: 'Continuar editando',
                            variant: 'warning',
                        })
                        : window.confirm('¿Deseas descartar los cambios de este registro?');
                    if (!confirmed) return;
                }

                delete form.dataset.validationAttempted;
                delete card.dataset.editingFormBaseline;
                card.classList.remove('is-editing-fields');
                
                // First, restore original form data
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
                    playRecordModeTransition(card, 'view');
                    announceFailedImportStatus('Edición cancelada.');
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
                if (!validateCorrectionForm(form)) return;
                saveCorrection(recordId, form, card, true, btnRetry);
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

    function syncCorrectionAgeLimit(form) {
        const edadValor = form?.querySelector('[name="edad_valor"]');
        const edadUnidad = form?.querySelector('[name="edad_unidad"]');
        if (!edadValor || !edadUnidad) return;

        const limits = { anos: 150, meses: 11, dias: 30 };
        edadValor.max = String(limits[edadUnidad.value] ?? 150);
    }

    function validateAgeFields(form) {
        const edadValor = form.querySelector('[name="edad_valor"]');
        const edadUnidad = form.querySelector('[name="edad_unidad"]');
        if (!edadValor || !edadUnidad) return true;

        syncCorrectionAgeLimit(form);

        const valor = edadValor.value.trim();
        const unidad = edadUnidad.value;

        clearFieldError(edadValor);
        clearFieldError(edadUnidad);

        if (valor === '') return showFieldError(edadValor, 'Capture una edad válida.');
        if (!unidad) return showFieldError(edadUnidad, 'Seleccione la unidad de la edad.');

        const valorNum = Number(valor);
        if (!Number.isInteger(valorNum) || valorNum < 0) {
            return showFieldError(edadValor, 'Capture una edad válida.');
        }
        if (unidad === 'anos' && valorNum > 150) {
            return showFieldError(edadValor, 'La edad en años no puede ser mayor a 150.');
        }
        if (unidad === 'meses' && valorNum > 11) {
            return showFieldError(edadValor, 'Use años para edades de 12 meses o más.');
        }
        if (unidad === 'dias' && valorNum > 30) {
            return showFieldError(edadValor, 'La edad en días no puede ser mayor a 30.');
        }

        clearFieldError(edadValor);
        clearFieldError(edadUnidad);
        return true;
    }

    function showServerFieldErrors(form, errors, shouldFocus = true) {
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
            const fieldMessage = String(message).replace(/^[^:]+:\s*/, '');
            showFieldError(field, fieldMessage.charAt(0).toLocaleUpperCase('es-MX') + fieldMessage.slice(1));
            firstInvalid ||= field;
        });
        if (firstInvalid) form.dataset.validationAttempted = 'true';
        if (firstInvalid && shouldFocus) focusCorrectionField(firstInvalid);
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
            shouldRetry ? 'Importando…' : 'Guardando…'
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
                    removeRecordCardAndUpdateList(card);
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

        setRecordActionBusy(card, activeButton, true, 'Descartando…');

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
                removeRecordCardAndUpdateList(card);
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
        failedImportsPaginationState = {
            current_page: Number(data.current_page) || 1,
            last_page: Number(data.last_page) || 0,
            from: Number(data.from) || 0,
            to: Number(data.to) || 0,
            total: Number(data.total) || 0,
            total_pending: Number(data.total_pending ?? data.total) || 0,
        };
        updateFailedPaginationInfo(failedImportsPaginationState);
        if (!container) return;
        const activePage = failedImportsPaginationState.current_page;
        const totalPages = failedImportsPaginationState.last_page;
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
            link.addEventListener('click', async event => {
                event.preventDefault();
                const shouldContinue = await confirmDiscardFailedImportChanges({
                    question: '¿Deseas cambiar de página?',
                    confirmText: 'Cambiar sin guardar',
                });
                if (!shouldContinue) return;

                loadFailedRecords(Number(link.dataset.page));
                document.querySelector('.failed-imports-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function updateFailedPaginationInfo(data) {
        const footerInfo = document.getElementById('dt-info');
        const from = Number(data.from) || 0;
        const to = Number(data.to) || 0;
        const total = Number(data.total) || 0;
        const totalPending = Number(data.total_pending ?? data.total) || 0;
        const pendingSummary = document.getElementById('failed-imports-pending-count');

        if (pendingSummary && data.total_pending !== undefined) {
            pendingSummary.textContent = totalPending === 1
                ? '1 registro pendiente de revisión'
                : `${totalPending} registros pendientes de revisión`;
        }

        if (!footerInfo) return;
        const unfilteredTotal = total !== totalPending
            ? ` <span class="text-sm text-gray-500">(de ${totalPending} totales)</span>`
            : '';
        footerInfo.classList.remove('is-loading');
        footerInfo.innerHTML = `<span class="users-table-info-main">Mostrando <span class="font-semibold text-gray-900">${from}-${to}</span> de <span class="font-semibold text-gray-900">${total}</span>${unfilteredTotal}</span>`;
    }

    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0) return;
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        const rawHref = link.getAttribute('href') || '';
        if (!rawHref || rawHref.startsWith('#') || rawHref.startsWith('javascript:')) return;

        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin || !hasUnsavedFailedImportChanges()) return;

        event.preventDefault();
        const shouldContinue = await confirmDiscardFailedImportChanges({
            question: '¿Deseas salir de esta página?',
            confirmText: 'Salir sin guardar',
        });
        if (!shouldContinue) return;

        failedImportsNavigationState.navigating = true;
        window.location.assign(destination.href);
    });

    window.addEventListener('beforeunload', event => {
        if (failedImportsNavigationState.navigating || !hasUnsavedFailedImportChanges()) return;
        event.preventDefault();
        event.returnValue = '';
    });

    loadFailedRecords(1);
});
</script>
@endpush
