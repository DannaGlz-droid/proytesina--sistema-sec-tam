@extends('layouts.principal')
@section('title', 'Datos de Defunciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTONES -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="app-page-title">Datos de Defunciones</h1>
                <p class="app-page-subtitle">
                    En esta sección puede cargar archivos Excel (.xlsx, .xls), aplicar filtros y consultar los registros en la tabla.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 self-start lg:self-auto">
                <a href="{{ route('statistic.import-history-view') }}" class="bg-[#404041] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#2a2a2a] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm">
                    <i class="fas fa-history text-xs"></i>
                    Historial
                </a>
                <a href="{{ route('statistic.create') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Registrar
                </a>
            </div>
           
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80 flex-shrink-0">
                <!-- SECCIÓN CARGAR ARCHIVO -->
                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 mb-6 shadow-md shadow-gray-200/70 border-t-4 border-t-[#611132]">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
                        <h3 class="font-semibold text-[#404041] text-lg font-lora">Cargar Archivo</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Compact Drag & Drop area (unified style with reportes, simpler) -->
                        <div id="deaths-drop-area" class="min-h-[76px] border-2 border-dashed border-gray-300 rounded-lg px-4 py-3 text-center cursor-pointer bg-white transition-all duration-200 hover:border-[#611132]/50 hover:bg-gray-50">
                            <input id="fileInput" type="file" name="file" accept=".xlsx,.xls" class="hidden" />
                            <div data-import-default class="flex items-center justify-center gap-3">
                                <i class="fas fa-cloud-upload-alt text-lg text-gray-400"></i>
                                <div class="text-left">
                                    <p class="text-sm text-gray-700 font-lora mb-0">Arrastre el archivo aquí o haga clic para seleccionar</p>
                                    <p class="text-xs text-gray-500">Solo Excel (.xlsx, .xls) · Máx. 10MB</p>
                                </div>
                            </div>
                            <div data-import-loading class="hidden items-center justify-center gap-3">
                                <span class="relative flex h-9 w-9 items-center justify-center rounded-full bg-[#f8f1f4] text-[#611132]">
                                    <span class="absolute h-9 w-9 animate-spin rounded-full border-2 border-[#611132]/20 border-t-[#611132]"></span>
                                    <i class="fas fa-file-excel text-sm"></i>
                                </span>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#404041] font-lora mb-0">Procesando archivo</p>
                                    <p class="text-xs text-gray-500">Validando columnas y guardando registros...</p>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>

                <!-- COMPONENTE DE FILTROS -->
                <x-filtros.defunciones :districts="$districts" :municipalities="$municipalities" :causes="$causes" />
            </div>

            <!-- Columna Derecha - Tabla -->
            <div class="flex-1 min-w-0">
                <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                    <!-- Custom search, per-page controls -->
                    <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                        <div class="flex-1 min-w-0 sm:w-1/3 lg:w-1/2">
                            <div class="app-table-search">
                                <div class="app-table-search-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <input type="text" id="dt-search-deaths" class="app-table-search-input" placeholder="Buscar en defunciones...">
                                <div class="app-table-search-actions">
                                    <button type="button" id="dt-clear-deaths-btn" class="app-table-clear-button hidden" title="Limpiar búsqueda">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="app-table-controls ml-0 sm:ml-auto flex flex-wrap items-center justify-end gap-3">
                            <div class="app-table-page-size">
                                <span>Mostrar</span>
                                <select id="dt-per-page-deaths">
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                </select>
                            </div>
                            <div id="bulk-selection-bar-deaths" class="app-table-bulk-inline hidden items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="app-table-selection-marker"></span>
                                    <span id="bulk-selected-count-deaths" class="app-table-selection-count text-xs font-lora whitespace-nowrap"></span>
                                </div>
                                <span class="hidden xl:inline text-xs font-lora text-gray-500">En esta página</span>
                                <button id="clear-selected-deaths" type="button" class="hidden text-xs font-semibold font-lora text-[#611132] hover:underline whitespace-nowrap" title="Quitar selección">
                                    Quitar selección
                                </button>
                                <button id="bulk-delete-deaths" type="button" class="app-table-bulk-danger items-center gap-2" title="Eliminar seleccionados" style="display: none;">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Eliminar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="app-table-shell overflow-x-auto min-w-0">
                    <table id="deaths-table" class="app-data-table min-w-full w-full text-sm text-left text-gray-500">
                        <thead class="text-xs border-b border-[#404041]">
                            <tr>
                                <th scope="col" class="app-cell-check font-lora whitespace-nowrap text-xs"><input id="select-all-deaths" type="checkbox" /></th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Folio</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre(s)</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido paterno</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido materno</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Edad</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Sexo</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha def.</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio (res.)</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio (def.)</th>
                                <th scope="col" title="Distrito de residencia" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Distrito (res.)</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Lugar</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Causa</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-right w-24" data-orderable="false">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this via AJAX -->
                        </tbody>
                    </table>
                    </div>
                        
                    <!-- Custom pagination -->
                    <nav class="flex flex-row flex-wrap items-center justify-between gap-3 p-4 border-t border-[#404041]">
                        <span class="text-sm font-normal text-gray-500 font-lora flex-1 min-w-0" id="dt-info-deaths">
                            Mostrando <span class="font-semibold text-gray-900">0-0</span> de <span class="font-semibold text-gray-900">0</span> entradas
                        </span>
                        <div id="dt-pagination-deaths" class="flex-none"></div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function notifyDeaths(message, type = 'success', duration = 3500) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type, duration);
            return;
        }

        console[type === 'error' ? 'error' : 'log'](message);
    }

    function pluralizeEs(count, singular, plural) {
        return Number(count) === 1 ? singular : plural;
    }

    function buildDeathImportErrorMessage(rawMessage) {
        var message = String(rawMessage || '').trim();
        if (!message) {
            return 'No se pudo importar el archivo. Revisa el formato e intenta nuevamente.';
        }

        var lower = message.toLowerCase();
        if (lower.includes('estructura requerida') || lower.includes('columnas obligatorias')) {
            return 'El archivo no tiene las columnas requeridas. Revisa que sea el formato oficial de defunciones.';
        }

        if (lower.includes('xlsx') || lower.includes('xls') || lower.includes('mimes')) {
            return 'El archivo debe ser Excel (.xlsx o .xls).';
        }

        if (lower.includes('10 mb') || lower.includes('10240') || lower.includes('tamaño') || lower.includes('size')) {
            return 'El archivo supera el límite de 10 MB.';
        }

        if (lower.includes('causa')) {
            return 'No se pudo reconocer la causa del archivo. Revisa el nombre de la hoja.';
        }

        if (message.length > 140) {
            return 'No se pudo importar el archivo. Revisa el formato e intenta nuevamente.';
        }

        return message;
    }

    async function confirmDeathAction(options) {
        if (typeof window.confirmDialog === 'function') {
            return window.confirmDialog(options);
        }

        return window.confirm(options.message || 'Confirma la accion para continuar.');
    }

    // File upload functionality
    var selectBtn = document.getElementById('selectFileBtn');
    var fileInput = document.getElementById('fileInput');
    var dropArea = document.getElementById('deaths-drop-area');
    var importDefault = dropArea ? dropArea.querySelector('[data-import-default]') : null;
    var importLoading = dropArea ? dropArea.querySelector('[data-import-loading]') : null;
    selectBtn && selectBtn.addEventListener('click', function () { fileInput.click(); });

    function setDeathImporting(isImporting) {
        if (importDefault) {
            importDefault.classList.toggle('hidden', isImporting);
            importDefault.classList.toggle('flex', !isImporting);
        }

        if (importLoading) {
            importLoading.classList.toggle('hidden', !isImporting);
            importLoading.classList.toggle('flex', isImporting);
        }

        if (fileInput) {
            fileInput.disabled = isImporting;
        }

        if (dropArea) {
            dropArea.classList.toggle('cursor-wait', isImporting);
            dropArea.classList.toggle('pointer-events-none', isImporting);
            dropArea.classList.toggle('border-[#611132]/60', isImporting);
            dropArea.classList.toggle('bg-[#f8f1f4]/40', isImporting);
            dropArea.setAttribute('aria-busy', isImporting ? 'true' : 'false');
        }
    }

    // Compact drag & drop handlers for the deaths upload area
    if (dropArea && fileInput) {
        // Click on drop area opens file picker
        dropArea.addEventListener('click', function (e) { fileInput.click(); });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function(e) { e.preventDefault(); e.stopPropagation(); }, false);
        });

        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function() { dropArea.classList.add('bg-gray-50', 'border-[#611132]'); }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function() { dropArea.classList.remove('bg-gray-50', 'border-[#611132]'); }, false);
        });

        dropArea.addEventListener('drop', function(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            if (!files || files.length === 0) return;
            // Only accept single file
            if (files.length > 1) {
                notifyDeaths('Solo se permite subir un archivo a la vez.', 'warning');
                return;
            }
            // Assign files to the hidden input and trigger change
            try {
                fileInput.files = files;
                var evt = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(evt);
            } catch (err) {
                // Some browsers may not allow setting fileInput.files; fallback to alert
                notifyDeaths('Arrastre detectado. Usa el botón para seleccionar el archivo.', 'info');
            }
        }, false);
    }

    fileInput && fileInput.addEventListener('change', async function (e) {
        var file = e.target.files[0];
        if (!file) return;
        if (file.size > 10 * 1024 * 1024) {
            notifyDeaths('El archivo supera el límite de 10 MB.', 'warning');
            e.target.value = '';
            return;
        }

        var confirmed = await confirmDeathAction({
            title: 'Importar archivo',
            message: 'Se procesara el archivo y se guardaran los registros validos en la base de datos.',
            confirmText: 'Importar',
            cancelText: 'Cancelar',
            tone: 'warning'
        });

        if (!confirmed) {
            e.target.value = '';
            return;
        }

        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '{{ csrf_token() }}');

        var url = '{{ route("statistic.import") }}';
        setDeathImporting(true);
        fetch(url, { method: 'POST', body: fd, headers: {} })
            .then(function (res) {
                return res.text().then(function (text) {
                    try { return JSON.parse(text); } catch (e) { return { ok: false, message: text || 'Respuesta no JSON del servidor' }; }
                });
            })
            .then(function (json) {
                if (!json) {
                    notifyDeaths('No se pudo confirmar la importación. Intenta nuevamente.', 'error');
                    return;
                }
                if (json.ok === false) {
                    var serverMsg = json.message || (json.error_message ? json.error_message : 'Error en el servidor');
                    console.warn('Detalle de importación:', serverMsg);
                    notifyDeaths(buildDeathImportErrorMessage(serverMsg), 'error', 5000);
                    if (json.errors_file) console.info('Archivo de errores:', json.errors_file);
                    return;
                }

                var total = typeof json.total !== 'undefined' ? json.total : 0;
                var imported = typeof json.imported !== 'undefined' ? json.imported : 0;
                var failed = typeof json.failed !== 'undefined' ? json.failed : 0;
                var skippedDuplicates = typeof json.skipped_duplicates !== 'undefined' ? Number(json.skipped_duplicates) : 0;
                var changedExisting = typeof json.changed_existing !== 'undefined' ? Number(json.changed_existing) : 0;

                var msg = failed > 0
                    ? 'Importación finalizada con observaciones: ' + imported + ' ' + pluralizeEs(imported, 'registro guardado', 'registros guardados') + ' y ' + failed + ' ' + pluralizeEs(failed, 'con error', 'con errores') + '.'
                    : 'Importación finalizada: ' + imported + ' ' + pluralizeEs(imported, 'registro guardado', 'registros guardados') + '.';
                if (skippedDuplicates > 0) msg += ' ' + skippedDuplicates + ' ' + pluralizeEs(skippedDuplicates, 'folio existente omitido', 'folios existentes omitidos') + '.';
                if (changedExisting > 0) msg += ' ' + changedExisting + ' ' + pluralizeEs(changedExisting, 'folio existente con cambios detectado', 'folios existentes con cambios detectados') + '.';
                if (failed > 0 || changedExisting > 0) msg += ' Revisa el historial.';
                notifyDeaths(msg, (failed > 0 || changedExisting > 0) ? 'warning' : 'success', 5000);
                // Reload DataTables instead of full page reload
                if (window.deathsTable) {
                    window.deathsTable.ajax.reload();
                } else {
                    notifyDeaths('Los datos se guardaron, pero la tabla no se actualizó. Recarga la vista para ver los cambios.', 'info', 4500);
                }
            })
            .catch(function (err) {
                console.error(err);
                notifyDeaths('No se pudo subir el archivo. Intenta nuevamente.', 'error');
            })
            .finally(function () {
                setDeathImporting(false);
                e.target.value = '';
            });
    });

    // Initialize DataTables
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    // Get current URL parameters for filters
    const urlParams = new URLSearchParams(window.location.search);
    const filterData = {};
    // Normalize repeated params like selectedMonths[] into arrays and strip bracket notation
    for (const [rawKey, value] of urlParams.entries()) {
        let key = rawKey;
        if (key.endsWith('[]')) {
            key = key.slice(0, -2);
            if (!Array.isArray(filterData[key])) filterData[key] = [];
            filterData[key].push(value);
            continue;
        }
        // handle keys like selectedMonths[0]=01
        const bracketIndex = key.indexOf('[');
        if (bracketIndex !== -1) {
            key = key.substring(0, bracketIndex);
            if (!Array.isArray(filterData[key])) filterData[key] = [];
            filterData[key].push(value);
            continue;
        }
        // single value
        filterData[key] = value;
    }

    function setDeathFilterValue(target, rawKey, value) {
        if (value === null || value === undefined || value === '') return;

        let key = rawKey;
        if (key.endsWith('[]')) {
            key = key.slice(0, -2);
        }

        const bracketIndex = key.indexOf('[');
        if (bracketIndex !== -1) {
            key = key.substring(0, bracketIndex);
        }

        if (target[key] !== undefined) {
            if (!Array.isArray(target[key])) {
                target[key] = [target[key]];
            }
            target[key].push(value);
            return;
        }

        target[key] = value;
    }

    function readDeathFiltersFromForm() {
        const form = document.getElementById('filters-form');
        const data = {};
        if (!form) return data;

        const formData = new FormData(form);
        for (const [key, value] of formData.entries()) {
            setDeathFilterValue(data, key, value);
        }

        return data;
    }

    function buildDeathFilterQuery(data) {
        const params = new URLSearchParams();

        Object.entries(data).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(item => {
                    if (item !== null && item !== undefined && item !== '') {
                        params.append(key + '[]', item);
                    }
                });
                return;
            }

            if (value !== null && value !== undefined && value !== '') {
                params.set(key, value);
            }
        });

        return params;
    }

    function applyDeathFiltersWithoutReload() {
        const nextFilters = readDeathFiltersFromForm();

        Object.keys(filterData).forEach(key => delete filterData[key]);
        Object.assign(filterData, nextFilters);

        const params = buildDeathFilterQuery(filterData);
        const nextUrl = params.toString()
            ? `${window.location.pathname}?${params.toString()}`
            : window.location.pathname;
        window.history.pushState({}, '', nextUrl);

        clearVisibleDeathSelection();
        window.deathsTable.ajax.reload();
    }

    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const bulkSelectionBarDeaths = document.getElementById('bulk-selection-bar-deaths');
    const tableToolbarDeaths = bulkSelectionBarDeaths?.closest('.app-table-toolbar');

    if (bulkSelectionBarDeaths && tableToolbarDeaths) {
        tableToolbarDeaths.insertAdjacentElement('afterend', bulkSelectionBarDeaths);
    }

    function deathsLoadingRow() {
        return `
            <tr class="app-table-loading-row">
                <td colspan="14">
                    <span class="app-table-loading-inline">
                        <span>Cargando datos</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="app-table-loading-dot"></span>
                            <span class="app-table-loading-dot"></span>
                            <span class="app-table-loading-dot"></span>
                        </span>
                    </span>
                </td>
            </tr>
        `;
    }

    $('#deaths-table').on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $('#deaths-table tbody').html(deathsLoadingRow());
        }
    });
    $('#deaths-table').on('preXhr.dt', function() {
        $('#deaths-table tbody').html(deathsLoadingRow());
    });
    $('#deaths-table tbody').html(deathsLoadingRow());

    // Initialize DataTables with server-side processing
    window.deathsTable = $('#deaths-table').DataTable({
        serverSide: true,
        processing: false,
        scrollX: true,
        deferredRender: true,
        searching: true,  // Enable DataTables search
        lengthChange: false, // Disable DataTables length (use custom)
        dom: 't', // Only show table
        ajax: {
            url: '{{ route('statistic.datatable') }}',
            type: 'POST',
            data: function(d) {
                // Include filter parameters from URL/form
                return $.extend({}, d, filterData);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                notifyDeaths('No se pudieron cargar las defunciones. Intenta nuevamente.', 'error');
            }
        },
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false, width: '2.25rem', render: function(data, type, row) { return '<input class="row-check" data-id="'+data+'" type="checkbox" />'; } },
            { data: 'gov_folio', name: 'gov_folio' },
            { data: 'name', name: 'name' },
            { data: 'first_last_name', name: 'first_last_name' },
            { data: 'second_last_name', name: 'second_last_name' },
            { data: 'age', name: 'age', render: function(data, type, row) { return type === 'sort' ? data : row.age_display; } },
            { data: 'sex', name: 'sex' },
            { data: 'death_date', name: 'death_date' },
            { data: 'residence_municipality', name: 'residence_municipality_id', orderable: false },
            { data: 'death_municipality', name: 'death_municipality_id', orderable: false },
            { data: 'district', name: 'district_id', orderable: false },
            { data: 'death_location', name: 'death_location_id', orderable: false },
            { data: 'death_cause', name: 'death_cause_id', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            { targets: [0], className: 'app-cell-check app-cell-nowrap' },
            { targets: [5, 6, 7, 13], className: 'app-cell-nowrap' },
            { targets: [1], className: 'app-cell-wrap app-cell-strong' },
            { targets: [2, 3, 4, 8, 9, 10, 11, 12], className: 'app-cell-wrap' }
        ],
        pageLength: 25,
        // Default: death_date desc — death_date is the 8th column (index 7) because
        // the table includes a leading checkbox column at index 0.
        order: [[7, 'desc']],
        language: {
            emptyTable: 'No hay datos disponibles',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            zeroRecords: 'No se encontraron registros coincidentes'
        },
        drawCallback: function(settings) {
            updateCustomInfoDeaths(this.api());
            updateCustomPaginationDeaths(this.api());
            // Ensure bulk-delete visibility is refreshed after each draw
            try { toggleBulkDeleteButton(); } catch (e) { console.error('toggleBulkDeleteButton error', e); }
            // Uncheck header select-all when page changes (to avoid stale state)
            try { $('#select-all-deaths').prop('checked', false).prop('indeterminate', false); } catch (e) {}
        }
    });

    // Ensure button state is correct after initialization
    try { toggleBulkDeleteButton(); } catch (e) {}

    // Custom search functionality
    $('#dt-search-deaths').on('keyup', function(e) {
        const val = $(this).val();
        $('#dt-clear-deaths-btn').toggleClass('hidden', !val);
        if (e.key === 'Enter') {
            window.deathsTable.search(val).draw();
        }
    });

    // Checkbox selection: select-all for visible page
    $('#select-all-deaths').on('change', function() {
        const checked = $(this).is(':checked');
        $('#deaths-table tbody .row-check').prop('checked', checked);
        toggleBulkDeleteButton();
    });

    // Delegate click for row checkboxes (as they are rendered by DataTables)
    $('#deaths-table tbody').on('change', '.row-check', function() {
        // If any checkbox unchecked, uncheck select-all header
        if (!$(this).is(':checked')) {
            $('#select-all-deaths').prop('checked', false).prop('indeterminate', false);
        }
        toggleBulkDeleteButton();
    });

    function toggleBulkDeleteButton() {
        const visibleChecks = $('#deaths-table tbody .row-check');
        const checkedCount = visibleChecks.filter(':checked').length;
        const any = checkedCount > 0;
        const allSelected = visibleChecks.length > 0 && checkedCount === visibleChecks.length;
        const selectAll = $('#select-all-deaths');

        selectAll.prop('checked', allSelected);
        selectAll.prop('indeterminate', any && !allSelected);

        if (any) {
            $('#bulk-selection-bar-deaths').removeClass('hidden').addClass('flex');
            $('#clear-selected-deaths').removeClass('hidden');
            $('#bulk-selected-count-deaths')
                .text(checkedCount + ' seleccionada' + (checkedCount === 1 ? '' : 's'));
            $('#bulk-delete-deaths').css('display', 'flex');
        } else {
            $('#bulk-selection-bar-deaths').addClass('hidden').removeClass('flex');
            $('#clear-selected-deaths').addClass('hidden');
            $('#bulk-selected-count-deaths').text('');
            $('#bulk-delete-deaths').css('display', 'none');
        }
    }

    function clearVisibleDeathSelection() {
        $('#deaths-table tbody .row-check').prop('checked', false);
        $('#select-all-deaths').prop('checked', false).prop('indeterminate', false);
        toggleBulkDeleteButton();
    }

    $('#clear-selected-deaths').on('click', function() {
        clearVisibleDeathSelection();
    });

    // Bulk delete action
    $('#bulk-delete-deaths').on('click', async function() {
        const ids = [];
        $('#deaths-table tbody .row-check:checked').each(function() {
            const id = $(this).data('id');
            if (id) ids.push(id);
        });
        if (!ids.length) {
            notifyDeaths('Selecciona al menos un registro.', 'warning');
            return;
        }

        const confirmed = await confirmDeathAction({
            title: 'Eliminar registros',
            message: 'Se eliminarán ' + ids.length + ' registros. Esta acción no se puede deshacer.',
            confirmText: 'Eliminar',
            cancelText: 'Cancelar',
            tone: 'danger'
        });

        if (!confirmed) return;

        $.ajax({
            url: '{{ route('statistic.massDelete') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            data: { ids: ids },
            success: function(res) {
                if (res && res.ok) {
                    notifyDeaths('Se eliminaron ' + (res.deleted || 0) + ' registros.', 'success');
                    // reload current page of table
                    window.deathsTable.ajax.reload(null, false);
                    // reset header checkbox
                    clearVisibleDeathSelection();
                } else {
                    notifyDeaths('No se pudieron eliminar los registros.', 'error');
                    console.error(res);
                }
            },
            error: function(xhr) {
                console.error(xhr);
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudieron eliminar los registros. Intenta nuevamente.';
                notifyDeaths(msg, 'error', 4500);
            }
        });
    });

    $('#dt-clear-deaths-btn').on('click', function() {
        $('#dt-search-deaths').val('');
        window.deathsTable.search('').draw();
        $(this).addClass('hidden');
    });

    // Custom per-page change
    $('#dt-per-page-deaths').on('change', function() {
        window.deathsTable.page.len(parseInt(this.value)).draw();
    });

    // Function to update custom info text
    function updateCustomInfoDeaths(api) {
        const info = api.page.info();
        const start = info.recordsDisplay === 0 ? 0 : info.start + 1;
        const end = info.end;
        const filteredTotal = info.recordsDisplay; // number of records after filtering
        const totalAll = info.recordsTotal; // total records without filtering
        let text = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${filteredTotal}</span> entradas`;
        if (filteredTotal !== totalAll) {
            text += ` <span class="text-sm text-gray-500">(de ${totalAll} totales)</span>`;
        }
        $('#dt-info-deaths').html(text);
    }

    // Function to build custom pagination
    function updateCustomPaginationDeaths(api) {
        const info = api.page.info();
        const current = info.page + 1;
        const pages = info.pages;
        let html = '<ul class="inline-flex items-stretch -space-x-px">';

        // Previous button
        if (current === 1) {
            html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-left text-xs"></i></span></li>';
        } else {
            html += `<li><a href="#" data-page="${current - 2}" class="dt-page-link-deaths flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-left text-xs"></i></a></li>`;
        }

        // Page numbers
        const maxButtons = 5;
        if (pages <= maxButtons) {
            for (let i = 1; i <= pages; i++) {
                if (i === current) {
                    html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                } else {
                    html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                }
            }
        } else {
            // Complex pagination with ellipsis
            if (current <= 3) {
                for (let i = 1; i <= 5; i++) {
                    if (i === current) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                html += `<li><a href="#" data-page="${pages - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${pages}</a></li>`;
            } else if (current >= pages - 2) {
                html += `<li><a href="#" data-page="0" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                for (let i = pages - 4; i <= pages; i++) {
                    if (i === current) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
            } else {
                html += `<li><a href="#" data-page="0" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                for (let i = current - 2; i <= current + 2; i++) {
                    if (i === current) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                html += `<li><a href="#" data-page="${pages - 1}" class="dt-page-link-deaths flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${pages}</a></li>`;
            }
        }

        // Next button
        if (current === pages || pages === 0) {
            html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-right text-xs"></i></span></li>';
        } else {
            html += `<li><a href="#" data-page="${current}" class="dt-page-link-deaths flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-right text-xs"></i></a></li>`;
        }

        html += '</ul>';
        $('#dt-pagination-deaths').html(html);

        // Attach click handlers to pagination links
        $('.dt-page-link-deaths').on('click', function(e) {
            e.preventDefault();
            window.deathsTable.page(parseInt($(this).data('page'))).draw('page');
        });
    }

    // Apply filters without refreshing the full page.
    $('#filters-form').on('submit', function(e) {
        e.preventDefault();

        const dateRange = document.getElementById('dateRange')?.value;
        const startDate = document.getElementById('startDate')?.value;
        const endDate = document.getElementById('endDate')?.value;

        if (dateRange === 'custom' && startDate && endDate && startDate > endDate) {
            notifyDeaths('La fecha inicial no puede ser mayor que la fecha final.', 'warning');
            return;
        }

        applyDeathFiltersWithoutReload();
    });
});
</script>
@endpush

 
