@extends('layouts.principal')
@section('title', 'Datos de Defunciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="users-management-page statistics-data-page px-4 lg:pl-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header
            class="users-management-header statistics-data-header"
            title="Datos de defunciones"
            description="Consulte, filtre e importe los registros que integran la operación estadística."
        >
            <x-slot:actions>
                <a href="{{ route('statistic.import-history-view') }}" class="users-page-create-btn">
                    <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                    Historial de cargas
                </a>
                <button id="statistics-import-open" type="button" class="users-page-create-btn" aria-haspopup="dialog" aria-controls="statistics-import-dialog">
                    <i class="fas fa-file-arrow-up" aria-hidden="true"></i>
                    <span>Importar Excel</span>
                </button>
                <a href="{{ route('statistic.create') }}" class="users-page-create-btn">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    Registrar defunción
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if($analysisContext)
            @php
                $analysisCount = $deaths->total();
                $analysisCsvUrl = route('statistic.export').'?'.http_build_query(array_merge(request()->query(), ['format' => 'csv']));
                $isExcludedReview = $analysisContext['excluded'];
            @endphp
            <section id="statisticsAnalysisContext" class="statistics-analysis-context {{ $isExcludedReview ? 'is-warning' : '' }}" aria-labelledby="statistics-analysis-title">
                <div class="statistics-analysis-context__icon" aria-hidden="true">
                    <i class="fas {{ $isExcludedReview ? 'fa-triangle-exclamation' : 'fa-chart-column' }}"></i>
                </div>
                <div class="statistics-analysis-context__body">
                    <p id="statistics-analysis-title" class="statistics-analysis-context__title">
                        @if($isExcludedReview)
                            {{ number_format($analysisCount) }} {{ $analysisCount === 1 ? 'registro excluido' : 'registros excluidos' }} de “{{ $analysisContext['label'] }}”
                        @else
                            {{ number_format($analysisCount) }} {{ $analysisCount === 1 ? 'registro utilizado' : 'registros utilizados' }} en “{{ $analysisContext['label'] }}”
                        @endif
                    </p>
                    <p class="statistics-analysis-context__description">
                        {{ $isExcludedReview
                            ? 'Estos registros no cuentan con todos los campos necesarios para formar parte de la gráfica.'
                            : 'Esta tabla conserva el periodo y los filtros con los que se calculó la gráfica.' }}
                    </p>
                    @if($analysisFilterLabels)
                        <div class="statistics-analysis-context__filters" aria-label="Filtros provenientes de la gráfica">
                            @foreach($analysisFilterLabels as $filterLabel)
                                <span>{{ $filterLabel }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="statistics-analysis-context__actions">
                    <a href="{{ route('estadisticas.graficas') }}" class="statistics-analysis-context__secondary">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver a gráficas
                    </a>
                    <a href="{{ $analysisCsvUrl }}" class="statistics-analysis-context__primary">
                        <i class="fas fa-file-csv" aria-hidden="true"></i> Descargar CSV
                    </a>
                </div>
            </section>
        @endif

        <div class="space-y-4">
            <div class="min-w-0">
                <div class="app-table-card users-table-card statistics-table-card">
                    <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                        <x-filtros.defunciones :districts="$districts" :municipalities="$municipalities" :causes="$causes" />
                        <div id="bulk-selection-bar-deaths" class="app-table-bulk-inline hidden items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="app-table-selection-marker"></span>
                                <span id="bulk-selected-count-deaths" class="app-table-selection-count text-xs whitespace-nowrap"></span>
                            </div>
                            <span class="hidden xl:inline text-xs text-gray-500">En esta página</span>
                            <button id="clear-selected-deaths" type="button" class="hidden text-xs font-semibold text-slate-600 hover:underline whitespace-nowrap">Quitar selección</button>
                            <button id="bulk-delete-deaths" type="button" class="app-table-bulk-danger items-center gap-2" style="display:none;">
                                <i class="fas fa-trash" aria-hidden="true"></i><span>Eliminar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="app-table-shell overflow-x-auto min-w-0">
                    <div class="users-table-refresh-progress" aria-hidden="true"></div>
                    <span id="statistics-table-status" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></span>
                    <table id="deaths-table" class="app-data-table min-w-full w-full text-sm text-left text-gray-500">
                        <thead class="text-xs">
                            <tr>
                                <th scope="col" class="app-cell-check dt-checkbox-cell whitespace-nowrap text-xs">
                                    <label class="users-checkbox-hitbox" for="select-all-deaths">
                                        <input id="select-all-deaths" type="checkbox" aria-label="Seleccionar todas las defunciones visibles" />
                                    </label>
                                </th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Folio</th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Persona</th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">
                                    <span class="statistics-column-label--full">Fecha de defunción</span>
                                    <span class="statistics-column-label--short" aria-hidden="true">Fecha def.</span>
                                </th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Residencia</th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">
                                    <span class="statistics-column-label--full">Municipio de defunción</span>
                                    <span class="statistics-column-label--short" aria-hidden="true">Municipio (def.)</span>
                                </th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Lugar</th>
                                <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Causa</th>
                                <th scope="col" class="dt-actions-cell px-3 py-2 whitespace-nowrap text-xs text-center" data-orderable="false"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this via AJAX -->
                        </tbody>
                    </table>
                    </div>
                        
                    <!-- Custom pagination -->
                    <nav class="users-table-footer statistics-table-footer flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                        <span class="text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading" id="dt-info">
                            Mostrando <span class="font-semibold text-gray-900">0-0</span> de <span class="font-semibold text-gray-900">0</span>
                        </span>
                        <div id="dt-pagination" class="flex-none"></div>
                    </nav>
                </div>
                </div>
            </div>

        <x-ui.dialog
            id="statistics-import-dialog"
            size="task"
            labelledby="statistics-import-title"
            describedby="statistics-import-description"
            class="statistics-import-dialog"
            panel-class="statistics-import-dialog-card"
            :overlay-attributes="['class' => 'statistics-import-dialog-overlay', 'data-import-close' => '']"
        >
                <header class="ui-dialog__header statistics-import-dialog-header">
                    <div class="ui-dialog__heading">
                        <div class="ui-dialog__title-row statistics-import-dialog-title-row">
                            <i class="fas fa-file-arrow-up" aria-hidden="true"></i>
                            <h2 id="statistics-import-title" class="ui-dialog__title">Importar datos</h2>
                        </div>
                        <p id="statistics-import-description" class="ui-dialog__description">Seleccione el archivo oficial de defunciones que desea cargar.</p>
                    </div>
                    <button type="button" class="ui-dialog__close statistics-import-dialog-close" data-import-close aria-label="Cerrar">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </header>

                <div class="ui-dialog__body statistics-import-dialog-body">
                    <div class="statistics-import-field">
                        <p id="statistics-import-field-label" class="statistics-import-field-label">
                            Archivo de defunciones <span aria-hidden="true">*</span>
                        </p>
                        <button
                            id="statistics-import-dropzone"
                            type="button"
                            class="statistics-import-dropzone"
                            aria-labelledby="statistics-import-field-label statistics-import-dropzone-action"
                            aria-describedby="statistics-import-help"
                        >
                            <i class="fas fa-cloud-arrow-up" aria-hidden="true"></i>
                            <span class="statistics-import-dropzone-copy">
                                <span><strong id="statistics-import-dropzone-action">Seleccionar archivo</strong> o arrástrelo aquí</span>
                                <small id="statistics-import-help">Un archivo XLSX o XLS · máximo 10 MB</small>
                            </span>
                        </button>
                        <input id="fileInput" type="file" accept=".xlsx,.xls" class="hidden">

                        <div id="statistics-import-file" class="statistics-import-file hidden" aria-live="polite">
                            <span class="statistics-import-file-icon"><i class="far fa-file-excel" aria-hidden="true"></i></span>
                            <span class="statistics-import-file-copy">
                                <strong id="statistics-import-file-name"></strong>
                                <small id="statistics-import-file-meta"></small>
                            </span>
                            <button id="statistics-import-remove" type="button" aria-label="Quitar archivo" title="Quitar archivo">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        </div>

                        <p id="statistics-import-error" class="statistics-inline-error hidden" role="alert">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                            <span id="statistics-import-error-text"></span>
                        </p>
                    </div>
                </div>

                <footer class="ui-dialog__actions statistics-import-dialog-actions">
                    <button id="statistics-import-cancel" type="button" class="ui-button ui-button--secondary" data-import-close>Cancelar</button>
                    <button id="statistics-import-submit" type="button" class="ui-button ui-button--primary" disabled>
                        <span data-import-default>Importar datos</span>
                        <span data-import-loading class="hidden items-center gap-2"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i><span>Procesando…</span></span>
                    </button>
                </footer>
        </x-ui.dialog>
    </div>

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

    function escapeDeathCell(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(character) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character];
        });
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

    // Importación de Excel en un diálogo breve, sin abandonar el listado.
    var importOpen = document.getElementById('statistics-import-open');
    var importDialog = document.getElementById('statistics-import-dialog');
    var importCard = importDialog ? importDialog.querySelector('.statistics-import-dialog-card') : null;
    var fileInput = document.getElementById('fileInput');
    var dropArea = document.getElementById('statistics-import-dropzone');
    var importSubmit = document.getElementById('statistics-import-submit');
    var importCancel = document.getElementById('statistics-import-cancel');
    var importRemove = document.getElementById('statistics-import-remove');
    var importFile = document.getElementById('statistics-import-file');
    var importFileName = document.getElementById('statistics-import-file-name');
    var importFileMeta = document.getElementById('statistics-import-file-meta');
    var importError = document.getElementById('statistics-import-error');
    var importErrorText = document.getElementById('statistics-import-error-text');
    var importDefault = importSubmit ? importSubmit.querySelector('[data-import-default]') : null;
    var importLoading = importSubmit ? importSubmit.querySelector('[data-import-loading]') : null;
    var importCloseControls = importDialog ? Array.from(importDialog.querySelectorAll('[data-import-close]')) : [];
    var selectedImportFile = null;
    var importPreviousFocus = null;
    var deathImporting = false;
    var deathImportAwaitingDiscard = false;

    function setDeathImportError(message) {
        if (!importError) return;
        if (importErrorText) importErrorText.textContent = message || '';
        importError.classList.toggle('hidden', !message);
    }

    function formatDeathImportSize(bytes) {
        if (!Number.isFinite(bytes) || bytes <= 0) return '0 KB';
        if (bytes < 1024 * 1024) return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2).replace(/\.00$/, '') + ' MB';
    }

    function resetDeathImportSelection() {
        selectedImportFile = null;
        if (fileInput) fileInput.value = '';
        if (importFile) importFile.classList.add('hidden');
        if (importFileName) importFileName.textContent = '';
        if (importFileMeta) importFileMeta.textContent = '';
        if (importSubmit) importSubmit.disabled = true;
        setDeathImportError('');
    }

    function selectDeathImportFile(file) {
        setDeathImportError('');
        if (!file) return;

        if (!/\.(xlsx|xls)$/i.test(file.name)) {
            resetDeathImportSelection();
            setDeathImportError('Seleccione un archivo Excel en formato XLSX o XLS.');
            dropArea?.focus();
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            resetDeathImportSelection();
            setDeathImportError('El archivo supera el límite de 10 MB.');
            dropArea?.focus();
            return;
        }

        selectedImportFile = file;
        if (importFileName) importFileName.textContent = file.name;
        if (importFileMeta) {
            var extension = file.name.split('.').pop().toUpperCase();
            importFileMeta.textContent = extension + ' · ' + formatDeathImportSize(file.size);
        }
        if (importFile) importFile.classList.remove('hidden');
        if (importSubmit) importSubmit.disabled = false;
        importSubmit?.focus();
    }

    function openDeathImportDialog() {
        if (!importDialog) return;
        importPreviousFocus = document.activeElement;
        importDialog.classList.remove('hidden');
        importDialog.classList.add('flex');
        importDialog.setAttribute('aria-hidden', 'false');
        document.body.classList.add('statistics-import-dialog-open');
        window.requestAnimationFrame(function () {
            importDialog.classList.add('is-open');
            dropArea?.focus();
        });
    }

    function closeDeathImportDialog() {
        if (!importDialog || deathImporting) return;
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        importDialog.classList.remove('is-open');
        importDialog.classList.add('is-closing');
        window.setTimeout(function () {
            importDialog.classList.add('hidden');
            importDialog.classList.remove('flex', 'is-closing');
            importDialog.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('statistics-import-dialog-open');
            resetDeathImportSelection();
            if (importPreviousFocus && document.contains(importPreviousFocus)) importPreviousFocus.focus();
        }, reduceMotion ? 0 : 120);
    }

    async function requestCloseDeathImportDialog() {
        if (deathImporting || deathImportAwaitingDiscard) return;
        if (!selectedImportFile) {
            closeDeathImportDialog();
            return;
        }

        deathImportAwaitingDiscard = true;
        var confirmed = await window.confirmDialog({
            title: 'Descartar archivo',
            question: '¿Deseas cerrar la importación?',
            description: 'El archivo seleccionado no se importará.',
            confirmText: 'Descartar',
            cancelText: 'Continuar importando',
            variant: 'warning'
        });
        deathImportAwaitingDiscard = false;
        if (confirmed) closeDeathImportDialog();
        else importSubmit?.focus();
    }

    function setDeathImporting(isImporting) {
        deathImporting = isImporting;
        if (importDefault) importDefault.classList.toggle('hidden', isImporting);
        if (importLoading) {
            importLoading.classList.toggle('hidden', !isImporting);
            importLoading.classList.toggle('flex', isImporting);
        }
        if (fileInput) fileInput.disabled = isImporting;
        if (dropArea) {
            dropArea.disabled = isImporting;
            dropArea.setAttribute('aria-busy', isImporting ? 'true' : 'false');
        }
        if (importSubmit) importSubmit.disabled = isImporting || !selectedImportFile;
        if (importRemove) importRemove.disabled = isImporting;
        if (importCancel) importCancel.disabled = isImporting;
        importCloseControls.forEach(function (control) { control.disabled = isImporting; });
        if (importCard) importCard.setAttribute('aria-busy', isImporting ? 'true' : 'false');
    }

    importOpen?.addEventListener('click', openDeathImportDialog);
    importCloseControls.forEach(function (control) { control.addEventListener('click', requestCloseDeathImportDialog); });
    dropArea?.addEventListener('click', function () { fileInput?.click(); });
    importRemove?.addEventListener('click', function () {
        resetDeathImportSelection();
        dropArea?.focus();
    });
    fileInput?.addEventListener('change', function (event) { selectDeathImportFile(event.target.files[0]); });

    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
            dropArea.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
            });
        });
        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropArea.addEventListener(eventName, function () { dropArea.classList.add('is-dragging'); });
        });
        ['dragleave', 'drop'].forEach(function (eventName) {
            dropArea.addEventListener(eventName, function () { dropArea.classList.remove('is-dragging'); });
        });
        dropArea.addEventListener('drop', function (event) {
            var files = event.dataTransfer ? event.dataTransfer.files : null;
            if (!files || !files.length) return;
            if (files.length > 1) {
                setDeathImportError('Seleccione un solo archivo para cada importación.');
                return;
            }
            selectDeathImportFile(files[0]);
        });
    }

    importDialog?.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            requestCloseDeathImportDialog();
            return;
        }
        if (event.key !== 'Tab') return;

        var focusable = Array.from(importDialog.querySelectorAll('button:not([disabled]), input:not([disabled]), [href], [tabindex]:not([tabindex="-1"])'))
            .filter(function (element) { return !element.classList.contains('hidden') && element.offsetParent !== null; });
        if (!focusable.length) return;
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    importSubmit?.addEventListener('click', function () {
        var file = selectedImportFile;
        if (!file || deathImporting) return;

        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '{{ csrf_token() }}');

        var url = '{{ route("statistic.import") }}';
        var importSucceeded = false;
        setDeathImportError('');
        setDeathImporting(true);
        fetch(url, { method: 'POST', body: fd, headers: {} })
            .then(function (res) {
                return res.text().then(function (text) {
                    try { return JSON.parse(text); } catch (e) { return { ok: false, message: text || 'Respuesta no JSON del servidor' }; }
                });
            })
            .then(function (json) {
                if (!json) {
                    setDeathImportError('No se pudo confirmar la importación. Intente nuevamente.');
                    notifyDeaths('No se pudo confirmar la importación. Intenta nuevamente.', 'error');
                    return;
                }
                if (json.ok === false) {
                    var serverMsg = json.message || (json.error_message ? json.error_message : 'Error en el servidor');
                    console.warn('Detalle de importación:', serverMsg);
                    if (importError) {
                        importError.textContent = buildDeathImportErrorMessage(serverMsg);
                        importError.classList.remove('hidden');
                    }
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
                importSucceeded = true;
            })
            .catch(function (err) {
                console.error(err);
                if (importError) {
                    importError.textContent = 'No se pudo subir el archivo. Intente nuevamente.';
                    importError.classList.remove('hidden');
                }
                notifyDeaths('No se pudo subir el archivo. Intenta nuevamente.', 'error');
            })
            .finally(function () {
                setDeathImporting(false);
                if (importSucceeded) closeDeathImportDialog();
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

        if (data.dateRange === 'all') {
            delete data.dateRange;
        }

        const dateMode = data.dateRange || 'all';
        const selectedMonths = Array.isArray(data.selectedMonths)
            ? data.selectedMonths.filter(Boolean)
            : (data.selectedMonths ? [data.selectedMonths] : []);
        const dateCriterionIsComplete = dateMode === 'years'
            ? Boolean(data.year)
            : dateMode === 'months'
                ? Boolean(data.year) && selectedMonths.length > 0
                : dateMode === 'quarter'
                    ? Boolean(data.year) && Boolean(data.quarter)
                    : dateMode === 'custom'
                        ? Boolean(data.startDate || data.endDate)
                        : false;

        if (dateMode !== 'all' && !dateCriterionIsComplete) {
            ['dateRange', 'year', 'month', 'selectedMonths', 'quarter', 'startDate', 'endDate']
                .forEach(key => delete data[key]);
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
        document.getElementById('statisticsAnalysisContext')?.remove();

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

    function deathsStateRow(kind, title, message, actionLabel = '') {
        const stateKind = kind === 'search' ? 'no-results' : kind;
        const icon = kind === 'error' ? 'fa-triangle-exclamation' : (kind === 'search' ? 'fa-magnifying-glass' : 'fa-table-list');
        const action = actionLabel
            ? `<button class="users-table-state-action" type="button" data-deaths-state-action="${kind}">${actionLabel}</button>`
            : '';
        return `<tr class="users-table-state-row users-table-state-row--${stateKind}"><td colspan="9"><div class="users-table-state users-table-state--${stateKind}" role="${kind === 'error' ? 'alert' : 'status'}"><i class="fas ${icon}" aria-hidden="true"></i><strong>${title}</strong><span>${message}</span>${action}</div></td></tr>`;
    }

    const deathsCard = document.querySelector('.statistics-table-card');
    const deathsTableBody = document.querySelector('#deaths-table tbody');
    const deathsStatus = document.getElementById('statistics-table-status');
    const deathsSearchControl = document.getElementById('dt-search-deaths')?.closest('.users-filter-search');
    let deathsHasDrawn = false;
    let deathsRefreshTimer = null;

    function setDeathsRefreshing(refreshing, message = '') {
        window.clearTimeout(deathsRefreshTimer);
        if (!deathsCard) return;
        if (!refreshing) {
            deathsCard.classList.remove('is-refreshing', 'is-search-refresh');
            deathsCard.setAttribute('aria-busy', 'false');
            deathsTableBody?.removeAttribute('inert');
            if (deathsStatus && message) deathsStatus.textContent = message;
            return;
        }
        if (!deathsHasDrawn) return;
        const isSearchRefresh = deathsSearchControl?.classList.contains('is-searching') || false;
        deathsCard.setAttribute('aria-busy', 'true');
        deathsTableBody?.setAttribute('inert', '');
        deathsRefreshTimer = window.setTimeout(function () {
            deathsCard.classList.toggle('is-search-refresh', isSearchRefresh);
            deathsCard.classList.add('is-refreshing');
            if (deathsStatus) deathsStatus.textContent = isSearchRefresh ? 'Buscando defunciones' : 'Actualizando defunciones';
        }, 150);
    }

    $('#deaths-table').on('preXhr.dt', function() {
        setDeathsRefreshing(true);
    });
    $('#deaths-table').on('draw.dt error.dt', function() {
        setDeathsRefreshing(false, 'Datos actualizados');
        deathsSearchControl?.classList.remove('is-searching');
        document.getElementById('dt-search-deaths')?.setAttribute('aria-busy', 'false');
    });

    // Initialize DataTables with server-side processing
    window.deathsTable = $('#deaths-table').DataTable({
        serverSide: true,
        processing: false,
        scrollX: false,
        autoWidth: false,
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
                setDeathsRefreshing(false, 'No se pudieron cargar los datos');
                deathsSearchControl?.classList.remove('is-searching');
                document.getElementById('dt-search-deaths')?.setAttribute('aria-busy', 'false');
                $('#deaths-table tbody').html(deathsStateRow('error', 'No se pudieron cargar los datos', 'Verifique su conexión e intente nuevamente.', 'Reintentar'));
                notifyDeaths('No se pudieron cargar las defunciones. Intenta nuevamente.', 'error');
            }
        },
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false, width: '2.4rem', className: 'dt-checkbox-cell', render: function(data, type, row) { return '<label class="users-checkbox-hitbox"><input class="row-check" data-id="'+data+'" type="checkbox" aria-label="Seleccionar defunción '+data+'" /></label>'; } },
            { data: 'gov_folio', name: 'gov_folio', type: 'string', className: 'statistics-folio-cell', render: function(data) { return `<span class="statistics-folio">${escapeDeathCell(data || '—')}</span>`; } },
            { data: null, name: 'name', orderable: false, render: function(data, type, row) {
                const fullName = [row.name, row.first_last_name, row.second_last_name].filter(Boolean).join(' ') || 'Sin nombre';
                const details = [row.age_display, row.sex].filter(Boolean).join(' · ') || 'Sin datos demográficos';
                return `<div class="statistics-person"><strong title="${escapeDeathCell(fullName)}">${escapeDeathCell(fullName)}</strong><span>${escapeDeathCell(details)}</span></div>`;
            } },
            { data: 'death_date', name: 'death_date' },
            { data: null, name: 'residence_municipality_id', orderable: false, render: function(data, type, row) {
                return `<div class="statistics-location"><strong>${escapeDeathCell(row.residence_municipality || '—')}</strong><span>${escapeDeathCell(row.district || 'Sin distrito')}</span></div>`;
            } },
            { data: null, name: 'death_municipality_id', orderable: false, render: function(data, type, row) {
                return `<div class="statistics-location"><strong>${escapeDeathCell(row.death_municipality || '—')}</strong><span>${escapeDeathCell(row.death_district || 'Sin distrito')}</span></div>`;
            } },
            { data: 'death_location', name: 'death_location_id', orderable: false },
            { data: 'death_cause', name: 'death_cause_id', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '2.75rem', className: 'dt-actions-cell' }
        ],
        columnDefs: [
            { targets: [0], className: 'app-cell-check app-cell-nowrap' },
            { targets: [1, 3, 8], className: 'app-cell-nowrap' },
            { targets: [1], className: 'app-cell-wrap app-cell-strong' },
            { targets: [2, 4, 5, 6, 7], className: 'app-cell-wrap' }
        ],
        pageLength: 10,
        order: [[3, 'desc']],
        language: {
            emptyTable: '<div class="users-table-state users-table-state--empty" role="status"><i class="fas fa-table-list" aria-hidden="true"></i><strong>Aún no hay defunciones registradas</strong><span>Importe un archivo o registre una defunción para comenzar.</span></div>',
            loadingRecords: '<div class="users-table-skeleton" role="status" aria-label="Cargando defunciones"><span class="sr-only">Cargando defunciones</span><div></div><div></div><div></div><div></div><div></div></div>',
            processing: 'Procesando...',
            zeroRecords: '<div class="users-table-state users-table-state--no-results" role="status"><i class="fas fa-search" aria-hidden="true"></i><strong>No encontramos resultados</strong><span>Prueba con otra búsqueda o elimina los filtros aplicados.</span><button class="users-table-state-action" type="button" data-deaths-state-action="search">Limpiar búsqueda y filtros</button></div>'
        },
        drawCallback: function(settings) {
            if (typeof closeDeathActionMenus === 'function') closeDeathActionMenus();
            const tableInfo = this.api().page.info();
            if (tableInfo.recordsDisplay === 0) {
                const stateRow = document.querySelector('#deaths-table tbody tr');
                const stateKind = tableInfo.recordsTotal === 0 ? 'empty' : 'no-results';
                stateRow?.classList.add('users-table-state-row', `users-table-state-row--${stateKind}`);
            }
            deathsHasDrawn = true;
            setDeathsRefreshing(false, 'Tabla actualizada');
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

    // Match the pilot: typing prepares the query; Enter executes it.
    $('#dt-search-deaths').on('input', function() {
        $('#dt-clear-deaths-btn').toggleClass('hidden', !this.value.trim());
    }).on('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const query = this.value.trim();
        if (window.deathsTable.search() === query) return;
        deathsSearchControl?.classList.add('is-searching');
        this.setAttribute('aria-busy', 'true');
        window.deathsTable.search(query).draw();
    });

    $('#deaths-table tbody').on('click', '[data-deaths-state-action]', function() {
        if (this.dataset.deathsStateAction === 'error') {
            deathsHasDrawn = false;
            window.deathsTable.ajax.reload();
            return;
        }
        $('#dt-search-deaths').val('');
        $('#dt-clear-deaths-btn').addClass('hidden');
        window.deathsTable.search('');
        document.getElementById('limpiarFiltros')?.click();
    });

    function restoreDeathActionMenu(menu) {
        const host = menu._deathMenuHost;
        menu.classList.remove('is-viewport-positioned', 'opens-upward');
        ['top','right','bottom','left'].forEach(property => menu.style.removeProperty(property));
        if (host?.isConnected && menu.parentElement !== host) host.appendChild(menu);
        else if (!host?.isConnected && menu.parentElement === document.body) menu.remove();
        delete menu._deathMenuHost;
        delete menu._deathMenuTrigger;
    }

    function closeDeathActionMenus(exceptMenu) {
        document.querySelectorAll('.statistics-data-page .users-row-menu, body > .users-row-menu').forEach(menu => {
            if (menu === exceptMenu) return;
            const trigger = menu._deathMenuTrigger || menu.closest('.users-row-actions')?.querySelector('.users-row-menu-button');
            menu.classList.add('hidden');
            trigger?.setAttribute('aria-expanded', 'false');
            restoreDeathActionMenu(menu);
        });
    }

    function positionDeathActionMenu(menu, trigger) {
        const margin = 8;
        const spacing = 6;
        const triggerRect = trigger.getBoundingClientRect();
        menu._deathMenuHost = menu.parentElement;
        menu._deathMenuTrigger = trigger;
        document.body.appendChild(menu);
        menu.classList.add('is-viewport-positioned');
        menu.classList.remove('opens-upward');
        const left = Math.min(window.innerWidth - menu.offsetWidth - margin, Math.max(margin, triggerRect.right - menu.offsetWidth));
        let top = triggerRect.bottom + spacing;
        if (top + menu.offsetHeight > window.innerHeight - margin) {
            top = Math.max(margin, triggerRect.top - menu.offsetHeight - spacing);
            menu.classList.add('opens-upward');
        }
        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
    }

    $('#deaths-table tbody').on('click', '.users-row-menu-button', function(event) {
        event.preventDefault();
        event.stopPropagation();
        const menu = this.closest('.users-row-actions')?.querySelector('.users-row-menu');
        if (!menu) return;
        const isOpen = !menu.classList.contains('hidden');
        closeDeathActionMenus(menu);
        menu.classList.toggle('hidden', isOpen);
        this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        if (!isOpen) positionDeathActionMenu(menu, this);
        else closeDeathActionMenus();
    });
    document.addEventListener('click', event => { if (!event.target.closest('.users-row-actions, .users-row-menu')) closeDeathActionMenus(); });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        const menu = document.querySelector('body > .users-row-menu:not(.hidden), .statistics-data-page .users-row-menu:not(.hidden)');
        const trigger = menu?._deathMenuTrigger;
        closeDeathActionMenus();
        trigger?.focus();
    });
    window.addEventListener('resize', () => closeDeathActionMenus());
    window.addEventListener('scroll', () => closeDeathActionMenus(), true);

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

        const confirmed = await window.confirmDeleteDialog({
            title: 'Eliminar registros',
            subject: ids.length + ' registro' + (ids.length === 1 ? '' : 's'),
            description: 'Los registros seleccionados dejarán de estar disponibles de forma permanente.'
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
                    const deletedRecords = Number(res.deleted || 0);
                    notifyDeaths(
                        deletedRecords === 1
                            ? 'Se eliminó 1 registro.'
                            : `Se eliminaron ${deletedRecords} registros.`,
                        'success'
                    );
                    // reload current page of table
                    window.deathsTable.ajax.reload(null, false);
                    // reset header checkbox
                    clearVisibleDeathSelection();
                } else {
                    notifyDeaths('No se pudieron eliminar los registros. Inténtalo nuevamente.', 'error');
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
        deathsSearchControl?.classList.add('is-searching');
        document.getElementById('dt-search-deaths')?.setAttribute('aria-busy', 'true');
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
        let text = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${filteredTotal}</span>`;
        if (filteredTotal !== totalAll) {
            text += ` <span class="text-sm text-gray-500">(de ${totalAll} totales)</span>`;
        }
        $('#dt-info').removeClass('is-loading').html(text);
    }

    // Function to build custom pagination
    function updateCustomPaginationDeaths(api) {
        const info = api.page.info();
        const current = info.page + 1;
        const pages = info.pages;
        const pageButton = function(page) {
            return page === current
                ? `<span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">${page}</span>`
                : `<a href="#" data-page="${page - 1}" class="dt-page-link-deaths fb-page-btn fb-page-num">${page}</a>`;
        };
        const ellipsis = () => '<span class="fb-page-btn fb-page-num fb-page-ellipsis">...</span>';
        let html = '<div class="fb-pagination" role="navigation" aria-label="Paginación de defunciones">';

        html += current === 1 || pages === 0
            ? '<span class="fb-page-btn fb-page-first fb-page-disabled">Anterior</span>'
            : `<a href="#" data-page="${current - 2}" class="dt-page-link-deaths fb-page-btn fb-page-first">Anterior</a>`;

        if (pages <= 5) {
            for (let page = 1; page <= pages; page++) html += pageButton(page);
        } else if (current <= 3) {
            for (let page = 1; page <= 5; page++) html += pageButton(page);
            html += ellipsis() + pageButton(pages);
        } else if (current >= pages - 2) {
            html += pageButton(1) + ellipsis();
            for (let page = pages - 4; page <= pages; page++) html += pageButton(page);
        } else {
            html += pageButton(1) + ellipsis();
            for (let page = current - 1; page <= current + 1; page++) html += pageButton(page);
            html += ellipsis() + pageButton(pages);
        }

        html += current === pages || pages === 0
            ? '<span class="fb-page-btn fb-page-last fb-page-disabled">Siguiente</span>'
            : `<a href="#" data-page="${current}" class="dt-page-link-deaths fb-page-btn fb-page-last">Siguiente</a>`;
        html += '</div>';
        $('#dt-pagination').html(html);

        // Attach click handlers to pagination links
        $('#dt-pagination').find('a.fb-page-btn').on('click', function(e) {
            e.preventDefault();
            window.deathsTable.page(parseInt($(this).data('page'))).draw('page');
        });
    }

    // Apply filters without refreshing the full page.
    $('#filters-form').on('submit', function(e) {
        e.preventDefault();

        const dateRange = document.getElementById('dateRange')?.value;
        const year = document.getElementById('year')?.value?.trim();
        const startDate = document.getElementById('startDate')?.value;
        const endDate = document.getElementById('endDate')?.value;
        const selectedMonths = document.querySelectorAll('input[name="selectedMonths[]"]:checked');
        const quarter = document.getElementById('quarter')?.value;
        const filterError = document.getElementById('statistics-filter-error');

        if (filterError) {
            filterError.textContent = '';
            filterError.classList.add('hidden');
        }

        const hasSelectedMonths = selectedMonths.length > 0;
        const hasSelectedQuarter = Boolean(quarter);

        if (dateRange === 'months' && hasSelectedMonths && !year) {
            if (filterError) {
                filterError.textContent = 'Indique el año para aplicar el periodo seleccionado.';
                filterError.classList.remove('hidden');
            }
            document.getElementById('year')?.focus();
            notifyDeaths('Indique el año para aplicar el periodo seleccionado.', 'warning');
            return;
        }

        if (dateRange === 'quarter' && hasSelectedQuarter && !year) {
            if (filterError) {
                filterError.textContent = 'Indique el año para aplicar el periodo seleccionado.';
                filterError.classList.remove('hidden');
            }
            document.getElementById('year')?.focus();
            notifyDeaths('Indique el año para aplicar el periodo seleccionado.', 'warning');
            return;
        }

        if (dateRange === 'custom' && startDate && endDate && startDate > endDate) {
            if (filterError) {
                filterError.textContent = 'La fecha inicial no puede ser mayor que la fecha final.';
                filterError.classList.remove('hidden');
            }
            document.getElementById('startDate')?.focus();
            notifyDeaths('La fecha inicial no puede ser mayor que la fecha final.', 'warning');
            return;
        }

        if (dateRange === 'months' && year && !hasSelectedMonths) {
            if (filterError) {
                filterError.textContent = 'Seleccione al menos un mes.';
                filterError.classList.remove('hidden');
            }
            document.querySelector('.statistics-month-options input')?.focus();
            notifyDeaths('Seleccione al menos un mes.', 'warning');
            return;
        }

        if (dateRange === 'quarter' && year && !hasSelectedQuarter) {
            if (filterError) {
                filterError.textContent = 'Seleccione un trimestre.';
                filterError.classList.remove('hidden');
            }
            document.getElementById('quarter')?.tomselect?.focus();
            notifyDeaths('Seleccione un trimestre.', 'warning');
            return;
        }

        applyDeathFiltersWithoutReload();
    });
});
</script>
@endpush

 
