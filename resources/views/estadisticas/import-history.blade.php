@extends('layouts.principal')
@section('title', 'Historial de Importaciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-import-history-page px-4 lg:pl-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header class="users-management-header" title="Historial de importaciones" description="Consulte las cargas realizadas, revise sus resultados y gestione las importaciones reversibles.">
            <x-slot:actions><a href="{{ route('statistic.data') }}" class="users-page-create-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver a datos</a></x-slot:actions>
        </x-ui.page-header>
        <section id="imports-overview" class="imports-overview" aria-label="Resumen del historial de importaciones" aria-busy="true">
            <button type="button" class="imports-overview-item imports-overview-item--attention imports-overview-filter" data-import-overview-filter="corrections" aria-pressed="false">
                <span class="imports-overview-label">Registros por corregir</span>
                <strong id="imports-overview-corrections" class="imports-overview-value">--</strong>
                <small id="imports-overview-corrections-detail">Calculando el resumen</small>
            </button>
            <button type="button" class="imports-overview-item imports-overview-item--differences imports-overview-filter" data-import-overview-filter="differences" aria-pressed="false">
                <span class="imports-overview-label">Folios con diferencias</span>
                <strong id="imports-overview-differences" class="imports-overview-value">--</strong>
                <small id="imports-overview-differences-detail">Calculando el resumen</small>
            </button>
            <article class="imports-overview-item imports-overview-item--latest">
                <span class="imports-overview-label">Última importación</span>
                <strong id="imports-overview-latest" class="imports-overview-file">Cargando historial</strong>
                <small id="imports-overview-latest-detail">Espere un momento</small>
            </article>
        </section>
        <div class="app-table-card users-table-card imports-table-card">
            <div class="imports-view-tabs" role="tablist" aria-label="Estado de las importaciones">
                <button type="button" class="imports-view-tab is-active" role="tab" aria-selected="true" data-import-view="active">
                    <span>Activas</span><span id="imports-view-count-active" class="imports-view-count">0</span>
                </button>
                <button type="button" class="imports-view-tab" role="tab" aria-selected="false" data-import-view="reversed">
                    <span>Revertidas</span><span id="imports-view-count-reversed" class="imports-view-count">0</span>
                </button>
                <button type="button" class="imports-view-tab" role="tab" aria-selected="false" data-import-view="failed">
                    <span>Fallidas</span><span id="imports-view-count-failed" class="imports-view-count">0</span>
                </button>
            </div>
            <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                <x-filtros.importaciones />
                <div id="bulk-selection-bar-imports" class="app-table-bulk-inline hidden items-center gap-3">
                    <div class="flex items-center gap-2"><span class="app-table-selection-marker"></span><span id="bulk-selected-count-imports" class="app-table-selection-count text-xs whitespace-nowrap"></span></div>
                    <span class="hidden xl:inline text-xs text-gray-500">En esta página</span>
                    <button id="clear-selected-imports" type="button" class="hidden text-xs font-semibold text-slate-600 hover:underline whitespace-nowrap">Quitar selección</button>
                    <button id="bulk-delete-imports" type="button" class="app-table-bulk-danger items-center gap-2" style="display:none"><i class="fas fa-trash" aria-hidden="true"></i><span>Eliminar del historial</span></button>
                </div>
            </div>
            <div class="app-table-shell imports-table-scroll min-w-0">
                <div class="users-table-refresh-progress" aria-hidden="true"></div><span id="imports-table-status" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></span>
                <table id="imports-table" class="app-data-table imports-table min-w-full w-full text-sm text-left text-gray-500">
                    <thead class="text-xs"><tr>
                        <th scope="col" class="dt-checkbox-cell"><label class="users-checkbox-hitbox" for="select-all-imports" title="Seleccionar registros visibles"><input id="select-all-imports" type="checkbox" aria-label="Seleccionar registros visibles"></label></th>
                        <th scope="col" class="sorting sortable" data-sort-key="original_name" tabindex="0" aria-sort="none">Archivo</th>
                        <th scope="col" class="sorting_desc sortable" data-sort-key="created_at" tabindex="0" aria-sort="descending">Fecha de carga / responsable</th>
                        <th id="imports-result-heading" scope="col">Resultado</th><th scope="col">Estado</th><th scope="col" class="dt-actions-cell"><span class="sr-only">Acciones</span></th>
                    </tr></thead>
                    <tbody id="imports-tbody"></tbody>
                </table>
            </div>
            <nav class="users-table-footer imports-table-footer flex flex-row flex-wrap items-center justify-between gap-3 p-4"><span id="dt-info" class="text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading">Preparando tabla</span><div id="dt-pagination" class="flex-none"></div></nav>
        </div>
    </main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let perPage = 10;
    let allImports = [];
    let filteredImports = [];
    let currentImportView = 'active';
    let currentOverviewFilter = null;
    let currentSort = {
        key: 'created_at',
        direction: 'desc'
    };

    function notifyImport(message, type = 'success', duration = 3000) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type, duration);
            return;
        }

        console[type === 'error' ? 'error' : 'log'](message);
    }

    function importsLoadingRow() {
        return `<tr class="users-table-state-row"><td colspan="6"><div class="users-table-skeleton" role="status" aria-label="Cargando importaciones"><span class="sr-only">Cargando importaciones</span><div></div><div></div><div></div><div></div><div></div></div></td></tr>`;
    }

    function importsMessageRow(message, tone = 'muted') {
        const error = tone === 'error';
        const kind = error ? 'error' : 'no-results';
        return `<tr class="users-table-state-row users-table-state-row--${kind}"><td colspan="6"><div class="users-table-state users-table-state--${kind}" role="${error ? 'alert' : 'status'}">
            <i class="fas ${error ? 'fa-triangle-exclamation' : 'fa-magnifying-glass'}" aria-hidden="true"></i>
            <strong>${error ? 'No pudimos cargar el historial' : 'No encontramos resultados'}</strong>
            <span>${escapeHtml(message)}</span>
            <button type="button" class="users-table-state-action" data-imports-${error ? 'retry' : 'reset'}>${error ? 'Reintentar' : 'Limpiar búsqueda y filtros'}</button>
        </div></td></tr>`;
    }

    function setImportsTableLoading(isLoading) {
        const card = document.querySelector('.imports-table-card');
        card?.classList.toggle('is-refreshing', isLoading);
        const status = document.getElementById('imports-table-status');
        if (status) status.textContent = isLoading ? 'Actualizando historial de importaciones' : '';
        if (!isLoading) return;
        const tbody = document.getElementById('imports-tbody');
        if (!tbody) return;

        tbody.innerHTML = importsLoadingRow();
    }

    function showImportsLoadError() {
        allImports = [];
        filteredImports = [];
        currentPage = 1;
        updateImportOverview([], true);
        updateImportViewTabs();

        const tbody = document.getElementById('imports-tbody');
        if (tbody) tbody.innerHTML = importsMessageRow('Revise la conexión e intente nuevamente.', 'error');

        updatePaginationInfo();
        renderPagination();
        clearVisibleImportSelection();
    }

    function canDeleteImportHistory(imp) {
        if (imp.is_reversed) {
            return true;
        }

        if (imp.status === 'processing') {
            return false;
        }

        return Number(imp.rows_imported || 0) === 0;
    }

    function getImportView(imp) {
        if (imp?.is_reversed) return 'reversed';
        if (imp?.status === 'failed') return 'failed';
        return 'active';
    }

    function updateImportViewTabs() {
        const counts = { active: 0, reversed: 0, failed: 0 };
        allImports.forEach(item => { counts[getImportView(item)] += 1; });

        Object.entries(counts).forEach(([view, count]) => {
            const counter = document.getElementById(`imports-view-count-${view}`);
            if (counter) counter.textContent = count.toLocaleString('es-MX');
        });

        document.querySelectorAll('[data-import-view]').forEach(tab => {
            const selected = tab.dataset.importView === currentImportView;
            tab.classList.toggle('is-active', selected);
            tab.setAttribute('aria-selected', String(selected));
            tab.tabIndex = selected ? 0 : -1;
        });

        const card = document.querySelector('.imports-table-card');
        card?.classList.toggle('is-active-view', currentImportView === 'active');

        const resultHeading = document.getElementById('imports-result-heading');
        if (resultHeading) {
            const headings = {
                active: 'Resultado',
                reversed: 'Resultado original',
                failed: 'Motivo de falla',
            };
            resultHeading.textContent = headings[currentImportView] || headings.active;
        }

        window.setImportFilterView?.(currentImportView);

    }

    function updateOverviewFilterState() {
        document.querySelectorAll('[data-import-overview-filter]').forEach(button => {
            const filter = button.dataset.importOverviewFilter;
            const active = currentImportView === 'active' && currentOverviewFilter === filter;
            const matchCount = Number(button.dataset.matchCount || 0);
            const label = filter === 'corrections' ? 'registros por corregir' : 'folios con diferencias';

            button.classList.toggle('is-filter-active', active);
            button.setAttribute('aria-pressed', String(active));
            button.setAttribute('aria-label', active
                ? `Mostrando importaciones con ${label}. Pulse para mostrar todas las activas.`
                : `Mostrar importaciones con ${label}.`);
            button.disabled = matchCount === 0;
        });
    }

    function pluralizeImport(value, singular, plural) {
        return `${value} ${value === 1 ? singular : plural}`;
    }

    function formatImportDate(date) {
        return new Intl.DateTimeFormat('es-MX', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).format(date);
    }

    function updateImportOverview(imports, failed = false) {
        const overview = document.getElementById('imports-overview');
        if (!overview) return;

        const correctionsValue = document.getElementById('imports-overview-corrections');
        const correctionsDetail = document.getElementById('imports-overview-corrections-detail');
        const differencesValue = document.getElementById('imports-overview-differences');
        const differencesDetail = document.getElementById('imports-overview-differences-detail');
        const latestValue = document.getElementById('imports-overview-latest');
        const latestDetail = document.getElementById('imports-overview-latest-detail');

        overview.setAttribute('aria-busy', 'false');
        overview.classList.toggle('has-load-error', failed);

        if (failed) {
            currentOverviewFilter = null;
            correctionsValue.textContent = '—';
            differencesValue.textContent = '—';
            correctionsDetail.textContent = 'Resumen no disponible';
            differencesDetail.textContent = 'Resumen no disponible';
            latestValue.textContent = 'No se pudo consultar';
            latestDetail.textContent = 'Reintente la carga del historial';
            document.querySelectorAll('[data-import-overview-filter]').forEach(button => {
                button.dataset.matchCount = '0';
            });
            updateOverviewFilterState();
            return;
        }

        const activeImports = imports.filter(item => getImportView(item) === 'active');
        const corrections = activeImports.reduce((total, item) => total + Number(item.rows_failed || 0), 0);
        const correctionsImports = activeImports.filter(item => Number(item.rows_failed || 0) > 0).length;
        const differences = activeImports.reduce((total, item) => total + Number(item.rows_changed_existing || item.changed_existing || 0), 0);
        const differencesImports = activeImports.filter(item => Number(item.rows_changed_existing || item.changed_existing || 0) > 0).length;
        const latest = [...imports].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

        correctionsValue.textContent = corrections.toLocaleString('es-MX');
        correctionsDetail.textContent = correctionsImports
            ? `En ${pluralizeImport(correctionsImports, 'importación', 'importaciones')}`
            : 'No hay registros pendientes';
        differencesValue.textContent = differences.toLocaleString('es-MX');
        differencesDetail.textContent = differencesImports
            ? `Detectados en ${pluralizeImport(differencesImports, 'importación', 'importaciones')}`
            : 'No se detectaron diferencias';

        const correctionsButton = document.querySelector('[data-import-overview-filter="corrections"]');
        const differencesButton = document.querySelector('[data-import-overview-filter="differences"]');
        if (correctionsButton) correctionsButton.dataset.matchCount = String(correctionsImports);
        if (differencesButton) differencesButton.dataset.matchCount = String(differencesImports);
        if ((currentOverviewFilter === 'corrections' && correctionsImports === 0)
            || (currentOverviewFilter === 'differences' && differencesImports === 0)) {
            currentOverviewFilter = null;
        }
        updateOverviewFilterState();

        if (!latest) {
            latestValue.textContent = 'Sin importaciones';
            latestDetail.textContent = 'El historial todavía está vacío';
            return;
        }

        const latestDate = new Date(latest.created_at);
        const latestAdded = Number(latest.rows_imported || 0);
        const latestCorrections = Number(latest.rows_failed || 0);
        const latestDifferences = Number(latest.rows_changed_existing || latest.changed_existing || 0);
        const latestUnchanged = Number(latest.rows_skipped_duplicates || latest.skipped_duplicates || 0);
        let latestResult = 'Sin cambios registrados';

        if (latest.is_reversed) {
            latestResult = latestAdded > 0
                ? 'Revertida · Datos retirados'
                : 'Revertida · No agregó datos';
        } else if (latest.status === 'failed') {
            latestResult = `Fallida · ${latest.failure_reason || 'Motivo no disponible'}`;
        } else if (latest.status === 'processing') {
            latestResult = 'Procesamiento en curso';
        } else if (latestCorrections > 0) {
            latestResult = pluralizeImport(latestCorrections, 'registro por corregir', 'registros por corregir');
        } else if (latestDifferences > 0) {
            latestResult = pluralizeImport(latestDifferences, 'folio con diferencias', 'folios con diferencias');
            if (latestAdded > 0) latestResult += ` · ${pluralizeImport(latestAdded, 'registro agregado', 'registros agregados')}`;
        } else if (latestAdded > 0) {
            latestResult = pluralizeImport(latestAdded, 'registro agregado', 'registros agregados');
        } else if (latestUnchanged > 0) {
            latestResult = pluralizeImport(latestUnchanged, 'registro ya existente', 'registros ya existentes');
        }

        latestValue.textContent = latest.original_name || 'Archivo sin nombre';
        latestValue.title = latest.original_name || 'Archivo sin nombre';
        latestDetail.textContent = `${latestDate.toLocaleDateString('es-MX')} · ${latestResult}`;
        latestDetail.title = latestResult;
    }

    // Load imports on page load
    loadImports();

    // Event listeners for filters
    const searchInput = document.getElementById('search-imports');
    const clearBtn = document.getElementById('clear-imports-btn');
    const searchControl = searchInput?.closest('.users-filter-search');
    let importSearchTimer = null;
    const dateRangeSelect = document.getElementById('dateRangeImports');
    const startDateInput = document.getElementById('startDateImports');
    const endDateInput = document.getElementById('endDateImports');
    const contextFilterInputs = Array.from(document.querySelectorAll('[data-import-context-filter]'));
    const contextFilterSelects = Array.from(document.querySelectorAll('[data-import-context-select]'));
    const selectedImportContextFilters = () => [
        ...contextFilterInputs.filter(input => input.checked).map(input => input.value),
        ...contextFilterSelects.flatMap(select => Array.from(select.selectedOptions).map(option => option.value)),
    ];
    const usuarioSelect = document.getElementById('usuarioImports');
    const perPageSelect = document.getElementById('per-page-imports');
    const aplicarBtn = document.getElementById('aplicarFiltrosImportaciones');
    const limpiarBtn = document.getElementById('limpiarFiltrosImportaciones');
    const sortableHeaders = document.querySelectorAll('#imports-table thead th.sortable');
    const bulkSelectionBar = document.getElementById('bulk-selection-bar-imports');
    const tableToolbar = bulkSelectionBar?.closest('.app-table-toolbar');

    if (bulkSelectionBar && tableToolbar) {
        tableToolbar.insertAdjacentElement('afterend', bulkSelectionBar);
    }

    const importViewTabs = Array.from(document.querySelectorAll('[data-import-view]'));
    const overviewFilterButtons = Array.from(document.querySelectorAll('[data-import-overview-filter]'));

    overviewFilterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.dataset.importOverviewFilter;
            if (!filter || button.disabled) return;

            const shouldClear = currentImportView === 'active' && currentOverviewFilter === filter;
            clearVisibleImportSelection();
            currentImportView = 'active';
            currentOverviewFilter = shouldClear ? null : filter;
            window.clearImportContextFilters?.();
            currentPage = 1;
            filterImports();
            sortImports();
            updateImportViewTabs();
            updateOverviewFilterState();
            renderTable();
            window.syncImportFilterUI?.();
        });
    });

    importViewTabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            const nextView = tab.dataset.importView;
            if (!nextView || nextView === currentImportView) return;

            clearVisibleImportSelection();
            currentImportView = nextView;
            currentOverviewFilter = null;
            window.setImportFilterView?.(currentImportView, true);
            currentPage = 1;
            filterImports();
            sortImports();
            updateImportViewTabs();
            updateOverviewFilterState();
            renderTable();
            window.syncImportFilterUI?.();
        });

        tab.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            let nextIndex = index;
            if (event.key === 'ArrowLeft') nextIndex = (index - 1 + importViewTabs.length) % importViewTabs.length;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % importViewTabs.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = importViewTabs.length - 1;
            importViewTabs[nextIndex]?.focus();
            importViewTabs[nextIndex]?.click();
        });
    });
    updateImportViewTabs();

    // Helper function to apply filters
    function applyFilters() {
        if (dateRangeSelect?.value === 'custom' && startDateInput?.value && endDateInput?.value) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);

            if (start > end) {
                notifyImport('La fecha inicial no puede ser mayor que la fecha final.', 'warning');
                return;
            }
        }

        filterImports();
        sortImports();
        currentPage = 1;
        renderTable();
        window.syncImportFilterUI?.();
    }

    function sortImports() {
        const key = currentSort.key;
        const direction = currentSort.direction === 'asc' ? 1 : -1;

        filteredImports.sort((a, b) => {
            let valueA;
            let valueB;

            if (key === 'created_at') {
                valueA = new Date(a.created_at).getTime() || 0;
                valueB = new Date(b.created_at).getTime() || 0;
            } else if (key === 'created_by') {
                valueA = (a.created_by || 'Sistema').toString().toLowerCase();
                valueB = (b.created_by || 'Sistema').toString().toLowerCase();
            } else {
                valueA = (a[key] || '').toString().toLowerCase();
                valueB = (b[key] || '').toString().toLowerCase();
            }

            if (valueA < valueB) return -1 * direction;
            if (valueA > valueB) return 1 * direction;
            return 0;
        });
    }

    function updateSortUI() {
        sortableHeaders.forEach(header => {
            header.classList.remove('sorting_asc', 'sorting_desc');
            if (header.dataset.sortKey === currentSort.key) {
                header.classList.add(currentSort.direction === 'asc' ? 'sorting_asc' : 'sorting_desc');
                header.setAttribute('aria-sort', currentSort.direction === 'asc' ? 'ascending' : 'descending');
            } else {
                header.setAttribute('aria-sort', 'none');
                if (!header.classList.contains('sorting')) {
                    header.classList.add('sorting');
                }
            }
        });
    }

    function runImportSearch() {
        window.clearTimeout(importSearchTimer);
        searchControl?.classList.add('is-searching');
        searchInput?.setAttribute('aria-busy', 'true');

        // The history is filtered locally. Keep the same visible feedback as
        // server-side tables long enough for the browser to paint one frame.
        importSearchTimer = window.setTimeout(() => {
            applyFilters();
            searchControl?.classList.remove('is-searching');
            searchInput?.setAttribute('aria-busy', 'false');
            updateSearchButton();
        }, 180);
    }

    // Match the pilot: typing prepares the query; Enter executes it.
    if (searchInput) {
        searchInput.addEventListener('input', updateSearchButton);

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                runImportSearch();
            }
        });
    }

    // Clear search button
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            updateSearchButton();
            runImportSearch();
        });
    }

    // Per page dropdown - applies immediately
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function (e) {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            renderTable();
        });
    }

    // Sorting headers
    sortableHeaders.forEach(header => {
        const applyHeaderSort = () => {
            const sortKey = header.dataset.sortKey;
            if (!sortKey) return;

            if (currentSort.key === sortKey) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.key = sortKey;
                currentSort.direction = 'asc';
            }

            updateSortUI();
            sortImports();
            currentPage = 1;
            renderTable();
        };

        header.addEventListener('click', applyHeaderSort);
        header.addEventListener('keydown', event => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            applyHeaderSort();
        });
    });

    // Checkbox selection: select-all toggles the visible page
    document.getElementById('select-all-imports')?.addEventListener('change', function() {
        const visibleChecks = Array.from(document.querySelectorAll('#imports-table tbody .row-check-import:not(:disabled)'));
        const checkedCount = visibleChecks.filter(input => input.checked).length;
        const shouldCheck = checkedCount !== visibleChecks.length;
        visibleChecks.forEach(input => { input.checked = shouldCheck; });
        updateImportSelectionState();
    });

    // Delegate click for row checkboxes
    document.getElementById('imports-tbody')?.addEventListener('change', function(event) {
        if (event.target.matches('.row-check-import')) updateImportSelectionState();
    });

    document.getElementById('imports-tbody')?.addEventListener('click', function(event) {
        if (event.target.closest('input.row-check-import, button, a')) event.stopPropagation();
    });

    function restoreImportActionMenu(menu) {
        if (!menu?._importsMenuHost) return;
        menu._importsMenuHost.appendChild(menu);
        menu.classList.remove('is-viewport-positioned');
        menu.style.removeProperty('top');
        menu.style.removeProperty('left');
        menu.style.removeProperty('right');
        menu.style.removeProperty('visibility');
        menu._importsMenuHost = null;
    }

    function closeImportActionMenus(exceptMenu = null) {
        document.querySelectorAll('.users-row-menu[data-import-action-menu]').forEach(menu => {
            if (menu === exceptMenu) return;
            menu.classList.add('hidden');
            menu._importsMenuTrigger?.setAttribute('aria-expanded', 'false');
            restoreImportActionMenu(menu);
        });
    }

    function positionImportActionMenu(menu, trigger) {
        menu._importsMenuHost = menu.parentElement;
        menu._importsMenuTrigger = trigger;
        const rect = trigger.getBoundingClientRect();
        document.body.appendChild(menu);
        menu.classList.add('is-viewport-positioned');
        menu.classList.remove('hidden');
        menu.style.visibility = 'hidden';

        const width = menu.offsetWidth;
        const height = menu.offsetHeight;
        const margin = 8;
        const left = Math.min(window.innerWidth - width - margin, Math.max(margin, rect.right - width));
        const top = rect.bottom + height + margin <= window.innerHeight
            ? rect.bottom + 5
            : Math.max(margin, rect.top - height - 5);

        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.visibility = '';
    }

    document.getElementById('imports-tbody')?.addEventListener('click', function (event) {
        const menuToggle = event.target.closest('[data-import-menu-toggle]');
        if (menuToggle) {
            const menu = menuToggle.nextElementSibling;
            const willOpen = menu?.classList.contains('hidden');
            closeImportActionMenus(willOpen ? menu : null);
            if (willOpen && menu) {
                positionImportActionMenu(menu, menuToggle);
                menuToggle.setAttribute('aria-expanded', 'true');
            } else if (menu) {
                menu.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
                restoreImportActionMenu(menu);
            }
            return;
        }
        const reverse = event.target.closest('[data-reverse-import]');
        if (reverse && !reverse.disabled) {
            closeImportActionMenus();
            window.reverseImport(Number(reverse.dataset.reverseImport));
        }
        const remove = event.target.closest('[data-delete-import]');
        if (remove && !remove.disabled) {
            closeImportActionMenus();
            window.deleteImport(Number(remove.dataset.deleteImport));
        }
    });

    document.addEventListener('click', function (event) {
        const reverse = event.target.closest('[data-reverse-import]');
        const remove = event.target.closest('[data-delete-import]');
        if (reverse && !reverse.disabled) {
            closeImportActionMenus();
            window.reverseImport(Number(reverse.dataset.reverseImport));
            return;
        }
        if (remove && !remove.disabled) {
            closeImportActionMenus();
            window.deleteImport(Number(remove.dataset.deleteImport));
            return;
        }
        if (!event.target.closest('[data-import-menu-toggle]') && !event.target.closest('[data-import-action-menu]')) closeImportActionMenus();
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        closeImportActionMenus();
    });
    window.addEventListener('resize', () => {
        closeImportActionMenus();
    });
    window.addEventListener('scroll', () => {
        closeImportActionMenus();
    }, true);

    document.getElementById('imports-tbody')?.addEventListener('click', function (event) {
        if (event.target.closest('[data-imports-retry]')) loadImports();
        if (event.target.closest('[data-imports-reset]')) {
            document.getElementById('limpiarFiltrosImportaciones')?.click();
            if (searchInput) searchInput.value = '';
            updateSearchButton();
            applyFilters();
        }
    });

    document.getElementById('clear-selected-imports')?.addEventListener('click', function() {
        clearVisibleImportSelection();
    });

    // Bulk delete action
    document.getElementById('bulk-delete-imports')?.addEventListener('click', async function() {
        const ids = Array.from(document.querySelectorAll('#imports-table tbody .row-check-import:checked'))
            .map(input => Number(input.dataset.id))
            .filter(Boolean);

        if (!ids.length) {
            notifyImport('Selecciona al menos una importación eliminable.', 'warning');
            return;
        }

        const selectedImports = allImports.filter(item => ids.includes(Number(item.id)));
        const discardedPending = selectedImports.reduce((total, item) => total + Number(item.rows_failed || 0), 0);
        const deleteDescription = discardedPending > 0
            ? `Se quitarán del historial. También se perderá el registro de ${pluralizeImport(discardedPending, 'pendiente descartado', 'pendientes descartados')}.`
            : 'Se quitarán del historial. Sus datos ya no están aplicados en el sistema.';

        const confirmed = await window.confirmDeleteDialog({
            title: 'Eliminar historial',
            subject: ids.length + ' importación' + (ids.length === 1 ? '' : 'es') + ' del historial',
            description: deleteDescription,
            confirmText: 'Eliminar historial',
        });

        if (!confirmed) return;

        try {
            const response = await fetch('{{ route('statistic.import-history.massDelete') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids }),
            });
            const res = await response.json();

            if (!response.ok || !res?.ok) {
                throw new Error(res?.message || 'No se pudieron eliminar las importaciones. Intenta nuevamente.');
            }

            const deletedImports = Number(res.deleted || 0);
            notifyImport(
                deletedImports === 1
                    ? 'Se eliminó 1 importación del historial.'
                    : `Se eliminaron ${deletedImports} importaciones del historial.`,
                'success'
            );
            clearVisibleImportSelection();
            loadImports();
        } catch (error) {
            console.error(error);
            notifyImport(error.message || 'No se pudieron eliminar las importaciones. Intenta nuevamente.', 'error');
        }
    });

    // Apply filters button - applies all filter changes
    if (aplicarBtn) {
        aplicarBtn.addEventListener('click', () => {
            applyFilters();
        });
    }

    // Clear filters button - resets and applies
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', () => {
            // Reset search
            if (searchInput) searchInput.value = '';
            
            // Reset date range
            if (dateRangeSelect?.tomselect) dateRangeSelect.tomselect.setValue('all', true);
            else if (dateRangeSelect) dateRangeSelect.value = 'all';
            
            // Hide custom date range if visible
            const customRangeSelector = document.getElementById('customRangeSelectorImports');
            if (customRangeSelector) {
                customRangeSelector.style.display = 'none';
            }
            
            contextFilterInputs.forEach(input => { input.checked = false; });
            contextFilterSelects.forEach(select => select.tomselect ? select.tomselect.clear(true) : Array.from(select.options).forEach(option => { option.selected = false; }));
            currentOverviewFilter = null;
            updateOverviewFilterState();
            
            // Reset usuario select
            if (usuarioSelect) {
                // Clear TomSelect if available
                if (usuarioSelect.tomselect) {
                    usuarioSelect.tomselect.clear();
                } else {
                    usuarioSelect.value = '';
                }
            }
            
            // Reset date inputs
            if (startDateInput) startDateInput.value = '';
            if (endDateInput) endDateInput.value = '';
            window.syncImportFilterUI?.();
            
            // Apply filters (show all records)
            applyFilters();
        });
    }

    // ===== TOGGLE FUNCTIONALITY FOR FILTER SECTIONS =====
    // Initialize all filter section toggles
    document.querySelectorAll('.filter-section').forEach(section => {
        const header = section.querySelector('.filter-section-header');
        const content = section.querySelector('.filter-section-content');
        const icon = header?.querySelector('.fa-chevron-down');
        
        if (!header || !content || !icon) return;
        
        // Set initial state based on current style
        const isCurrentlyOpen = content.style.maxHeight !== '0px' && 
                               content.style.maxHeight !== '' && 
                               parseFloat(content.style.opacity) !== 0;
        
        if (!isCurrentlyOpen) {
            icon.style.transform = 'rotate(-90deg)';
        }

        // Add click listener to header
        header.addEventListener('click', function(e) {
            const isOpen = parseFloat(content.style.maxHeight) > 0;
            
            if (isOpen) {
                // Close section
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                icon.style.transform = 'rotate(-90deg)';
            } else {
                // Open section
                let scrollHeight = content.scrollHeight;
                
                // Add extra space for TomSelect dropdowns if present
                if (content.querySelector('.tomselect-select')) {
                    scrollHeight += 350;
                }
                
                content.style.maxHeight = scrollHeight + 'px';
                content.style.opacity = '1';
                icon.style.transform = 'rotate(0deg)';
                
                // Recalculate height after animation in case content changed
                setTimeout(() => {
                    let newScrollHeight = content.scrollHeight;
                    if (content.querySelector('.tomselect-select')) {
                        newScrollHeight += 350;
                    }
                    if (newScrollHeight > scrollHeight) {
                        content.style.maxHeight = newScrollHeight + 'px';
                    }
                }, 50);
            }
        });
    });

    function loadImports() {
        setImportsTableLoading(true);
        const url = '{{ route("statistic.import-history") }}?per_page=5000';
        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(json => {
            if (json.ok && json.data && json.data.data) {
                allImports = json.data.data;
                window.syncImportUserFilterOptions?.(allImports);
                updateImportOverview(allImports);
                updateImportViewTabs();
                filterImports();
                sortImports();
                updateSortUI();
                currentPage = 1;
                renderTable();
            } else {
                showError('No se pudieron cargar las importaciones.');
                showImportsLoadError();
            }
        })
        .catch(err => {
            console.error(err);
            showError('No se pudieron cargar los datos.');
            showImportsLoadError();
        })
        .finally(() => setImportsTableLoading(false));
    }

    window.filterImports = function() {
        const searchTerm = document.getElementById('search-imports').value.toLowerCase();
        const dateRange = document.getElementById('dateRangeImports').value;
        const usuarioId = document.getElementById('usuarioImports').value; // Now this is an ID, not a name
        const selectedContextFilters = selectedImportContextFilters();
        const startDate = document.getElementById('startDateImports').value;
        const endDate = document.getElementById('endDateImports').value;

        filteredImports = allImports.filter(imp => {
            if (getImportView(imp) !== currentImportView) {
                return false;
            }

            // Búsqueda en nombre de archivo
            if (searchTerm && !(imp.original_name && imp.original_name.toLowerCase().includes(searchTerm))) {
                return false;
            }

            // Filtro por usuario (now comparing by ID)
            if (usuarioId && parseInt(usuarioId) !== parseInt(imp.user_id)) {
                return false;
            }

            // Filtro por fecha
            if (dateRange !== 'all') {
                const impDate = new Date(imp.created_at);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                switch (dateRange) {
                    case 'today':
                        const impDateStart = new Date(impDate);
                        impDateStart.setHours(0, 0, 0, 0);
                        if (impDateStart.getTime() !== today.getTime()) return false;
                        break;
                    case 'week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        if (impDate < weekAgo) return false;
                        break;
                    case 'month':
                        const monthAgo = new Date(today);
                        monthAgo.setDate(monthAgo.getDate() - 30);
                        if (impDate < monthAgo) return false;
                        break;
                    case 'year':
                        const yearAgo = new Date(today);
                        yearAgo.setFullYear(yearAgo.getFullYear() - 1);
                        if (impDate < yearAgo) return false;
                        break;
                    case 'custom':
                        if (startDate && new Date(startDate) > impDate) return false;
                        if (endDate && new Date(endDate) < impDate) return false;
                        break;
                }
            }

            if (selectedContextFilters.length) {
                const differences = Number(imp.rows_changed_existing || imp.changed_existing || 0);
                const corrections = Number(imp.rows_failed || 0);
                const added = Number(imp.rows_imported || 0);
                const failureReasons = {
                    missing_columns: 'Faltan columnas requeridas',
                    invalid_file: 'Archivo dañado o no compatible',
                    unavailable_file: 'No se pudo acceder al archivo',
                    internal_error: 'Error interno al procesar',
                    unknown: 'Motivo no disponible',
                };
                let matchesContext = false;

                if (currentImportView === 'active') {
                    matchesContext = selectedContextFilters.some(filter =>
                        (filter === 'corrections' && corrections > 0)
                        || (filter === 'differences' && differences > 0)
                    );
                } else if (currentImportView === 'reversed') {
                    matchesContext = selectedContextFilters.some(filter =>
                        (filter === 'removed' && added > 0)
                        || (filter === 'none' && added === 0)
                    );
                } else if (currentImportView === 'failed') {
                    const reason = String(imp.failure_reason || 'Motivo no disponible');
                    matchesContext = selectedContextFilters.some(filter => failureReasons[filter] === reason);
                }

                if (!matchesContext) return false;
            }

            if (currentOverviewFilter === 'corrections' && Number(imp.rows_failed || 0) === 0) {
                return false;
            }

            if (currentOverviewFilter === 'differences'
                && Number(imp.rows_changed_existing || imp.changed_existing || 0) === 0) {
                return false;
            }

            return true;
        });
    };

    function renderImportResult(imp) {
        const added = Number(imp.rows_imported || 0);
        const unchanged = Number(imp.rows_skipped_duplicates || imp.skipped_duplicates || 0);
        const different = Number(imp.rows_changed_existing || imp.changed_existing || 0);
        const toCorrect = Number(imp.rows_failed || 0);

        if (imp.status === 'failed') {
            const failureReason = String(imp.error_summary || imp.failure_reason || '').trim();
            const resultText = failureReason || 'Sin resultado';
            return `<div class="imports-result-empty imports-result-empty--failed"><span title="${escapeHtml(resultText)}">${escapeHtml(resultText)}</span></div>`;
        }

        if (added + unchanged + different + toCorrect === 0) {
            if (imp.status === 'processing') {
                return '<div class="imports-result-empty"><strong>En proceso</strong><small>Calculando el resultado</small></div>';
            }

            return '<div class="imports-result-empty"><strong>Sin cambios</strong><small>No se agregaron ni modificaron registros</small></div>';
        }

        const metrics = [
            {
                value: toCorrect,
                label: imp.is_reversed
                    ? (toCorrect === 1 ? 'pendiente descartado' : 'pendientes descartados')
                    : 'por corregir',
                modifier: 'imports-result-correction',
                title: imp.is_reversed
                    ? 'Registros pendientes descartados al revertir la carga'
                    : 'Registros que requieren corrección',
            },
            {
                value: different,
                label: imp.is_reversed
                    ? (different === 1 ? 'diferencia' : 'diferencias')
                    : 'con diferencias',
                modifier: 'imports-result-different',
                title: imp.is_reversed
                    ? 'Diferencias descartadas al revertir la carga'
                    : 'Folios existentes con información distinta; no fueron sobrescritos',
            },
            { value: added, label: added === 1 ? 'agregado' : 'agregados', modifier: 'imports-result-added', title: imp.is_reversed ? 'Registros que esta carga agregó antes de revertirse' : 'Registros nuevos guardados' },
            { value: unchanged, label: unchanged === 1 ? 'ya existente' : 'ya existentes', modifier: 'imports-result-unchanged', title: 'Folios que ya existían con la misma información' },
        ].filter(metric => metric.value > 0);

        const description = metrics.map(metric => `${metric.value} ${metric.label}`).join(', ');
        const metricMarkup = metrics.map(metric => {
            const content = `<strong>${metric.value.toLocaleString('es-MX')}</strong><span>${metric.label}</span>`;

            if (imp.is_reversed || metric.modifier === 'imports-result-unchanged') {
                return `<span class="imports-result-text ${metric.modifier}" title="${metric.title}">${content}</span>`;
            }

            return metric.modifier === 'imports-result-correction'
                ? `<a class="imports-result-badge ${metric.modifier}" href="/estadisticas/importaciones/${imp.id}/registros-fallidos" title="Abrir los registros que requieren corrección">${content}<i class="fas fa-arrow-right" aria-hidden="true"></i></a>`
                : `<span class="imports-result-badge ${metric.modifier}" title="${metric.title}">${content}</span>`;
        }).join('');

        return `<div class="imports-result-list${imp.is_reversed ? ' is-reverted' : ''}" aria-label="${escapeHtml(description)}">${metricMarkup}</div>`;
    }

    function renderTable() {
        const tbody = document.getElementById('imports-tbody');
        tbody.innerHTML = '';
        updateImportViewTabs();

        if (filteredImports.length === 0) {
            const emptyMessages = {
                active: 'No hay importaciones activas que coincidan con la búsqueda y los filtros.',
                reversed: 'No hay importaciones revertidas que coincidan con la búsqueda y los filtros.',
                failed: 'No hay importaciones fallidas que coincidan con la búsqueda y los filtros.',
            };
            tbody.innerHTML = importsMessageRow(emptyMessages[currentImportView]);
            updatePaginationInfo();
            renderPagination();
            clearVisibleImportSelection();
            return;
        }

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageImports = filteredImports.slice(start, end);

        pageImports.forEach(imp => {
            const row = document.createElement('tr');
            row.className = 'imports-table-row';

            if (imp.is_reversed) {
                row.classList.add('is-reversed');
            }

            if (imp.status === 'failed') {
                row.classList.add('is-failed');
            }

                const createdDate = new Date(imp.created_at);
                const createdByFullName = [imp.created_by, imp.created_by_first_last_name, imp.created_by_second_last_name]
                    .filter(Boolean)
                    .join(' ');
                const createdByUsername = imp.created_by_username ? `@${imp.created_by_username}` : '';
                const createdByName = createdByFullName || createdByUsername || 'Sistema';
                const createdByIdentity = createdByUsername && createdByUsername !== createdByName
                    ? `${createdByName} · ${createdByUsername}`
                    : createdByName;
            
            const addedRows = Number(imp.rows_imported || 0);
            const differentRows = Number(imp.rows_changed_existing || imp.changed_existing || 0);
            const failedRows = Number(imp.rows_failed || 0);
            const statusClass = imp.is_reversed ? 'status-reversed' : `status-${imp.status}`;
            const statusText = imp.is_reversed ? 'Revertido' : (imp.status === 'completed' ? 'Completado' : imp.status === 'processing' ? 'Procesando' : 'Fallido');
            const statusHelp = imp.is_reversed
                ? (addedRows > 0
                    ? 'Los datos agregados fueron retirados. El resultado muestra lo ocurrido antes de revertir la carga.'
                    : 'La importación fue revertida, pero no había registros agregados que retirar.')
                : (imp.status === 'completed'
                    ? 'La carga terminó y sus datos continúan aplicados.'
                    : (imp.status === 'processing'
                        ? 'La carga todavía se está procesando.'
                        : 'El archivo no pudo procesarse y no agregó datos.'));
            if (!imp.is_reversed && imp.status === 'completed') {
                if (failedRows > 0) {
                    row.classList.add('has-corrections');
                } else if (differentRows > 0) {
                    row.classList.add('has-differences');
                }
            }
            const statusNote = imp.is_reversed
                ? (addedRows > 0 ? 'Datos retirados' : 'No agregó datos')
                : (imp.status === 'processing'
                    ? 'Resultado en curso'
                    : (imp.status === 'failed'
                        ? ''
                        : (failedRows > 0 ? 'Requiere corrección' : (differentRows > 0 ? 'Con diferencias' : ''))));
            
            const canReverse = !imp.is_reversed && imp.status === 'completed';
            const newerActiveImport = canReverse ? findNewerActiveImport(imp) : null;
            const isReversalBlocked = Boolean(newerActiveImport);
            const reverseActionTitle = isReversalBlocked
                ? `Primero revierta ${newerActiveImport.original_name || 'la importación activa más reciente'}`
                : 'Revertir esta importación';
            const historySelectionEnabled = currentImportView !== 'active';
            const canDeleteHistory = historySelectionEnabled && canDeleteImportHistory(imp);
            const deleteHistoryTitle = canDeleteHistory
                ? 'Eliminar historial'
                : 'Primero revierte esta importación para conservar la trazabilidad de los datos';
            const selectionTitle = canDeleteHistory
                ? 'Seleccionar para eliminar este registro del historial'
                : 'Este registro no se puede eliminar del historial';
            const selectionCell = historySelectionEnabled
                ? `<td class="dt-checkbox-cell"><label class="users-checkbox-hitbox${canDeleteHistory ? '' : ' is-disabled'}" title="${escapeHtml(selectionTitle)}"><input class="row-check-import" data-id="${imp.id}" type="checkbox" ${canDeleteHistory ? '' : 'disabled'} aria-label="${escapeHtml(selectionTitle)}: ${escapeHtml(imp.original_name)}"></label></td>`
                : '<td class="dt-checkbox-cell" aria-hidden="true"></td>';

            const fileName = String(imp.original_name || 'Archivo sin nombre');
            const extensionIndex = fileName.lastIndexOf('.');
            const fileBaseName = extensionIndex > 0 ? fileName.slice(0, extensionIndex) : fileName;
            const fileExtension = extensionIndex > 0 ? fileName.slice(extensionIndex) : '';
            const fileRowsMarkup = imp.status === 'failed'
                ? ''
                : `<small>${Number(imp.rows_total || 0).toLocaleString('es-MX')} filas</small>`;
            row.innerHTML = `
                ${selectionCell}
                <td class="imports-file-column"><div class="imports-file-cell"><div><strong class="imports-file-name" tabindex="0" title="${escapeHtml(fileName)}" aria-label="Nombre completo del archivo: ${escapeHtml(fileName)}"><span class="imports-file-name-base">${escapeHtml(fileBaseName)}</span><span class="imports-file-name-extension">${escapeHtml(fileExtension)}</span></strong>${fileRowsMarkup}</div></div></td>
                <td class="imports-origin-column"><div class="imports-origin-cell"><strong class="imports-origin-date"><time datetime="${escapeHtml(String(imp.created_at || ''))}">${formatImportDate(createdDate)} · ${createdDate.toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'})}</time></strong><span class="imports-origin-user" title="${escapeHtml(createdByIdentity)}" aria-label="Responsable: ${escapeHtml(createdByIdentity)}">${escapeHtml(createdByName)}</span></div></td>
                <td class="imports-result-column"><div class="imports-result-cell">${renderImportResult(imp)}</div></td>
                <td class="imports-status-cell"><div class="imports-status-wrap"><div class="imports-status ${statusClass}" title="${escapeHtml(statusHelp)}" aria-label="${statusText}. ${escapeHtml(statusHelp)}"><span class="imports-status-dot" aria-hidden="true"></span><span>${statusText}</span></div>${statusNote ? `<small class="imports-status-note">${statusNote}</small>` : ''}</div></td>
                <td class="dt-actions-cell imports-actions-cell"><div class="users-row-menu-wrap"><button type="button" class="users-row-menu-button" data-import-menu-toggle aria-haspopup="menu" aria-expanded="false" aria-label="Acciones de ${escapeHtml(imp.original_name)}"><i class="fas fa-ellipsis-v" aria-hidden="true"></i></button><div class="users-row-menu hidden" data-import-action-menu role="menu">
                    ${currentImportView === 'active' && failedRows > 0 ? `<a role="menuitem" class="users-row-menu-item" href="/estadisticas/importaciones/${imp.id}/registros-fallidos"><i class="fas fa-exclamation-circle users-row-menu-icon" aria-hidden="true"></i><span>Revisar y corregir</span></a>` : ''}
                    ${currentImportView === 'active' ? `<button type="button" role="menuitem" class="users-row-menu-item${isReversalBlocked ? ' is-reversal-blocked' : ''}" data-reverse-import="${imp.id}" title="${escapeHtml(reverseActionTitle)}" aria-label="${escapeHtml(isReversalBlocked ? `Revertir, bloqueado. ${reverseActionTitle}` : 'Revertir importación')}" ${canReverse ? '' : 'disabled'}><i class="fas ${isReversalBlocked ? 'fa-lock' : 'fa-undo'} users-row-menu-icon" aria-hidden="true"></i><span>${isReversalBlocked ? 'Revertir (bloqueado)' : 'Revertir'}</span></button>` : ''}
                    ${canDeleteHistory ? `<button type="button" role="menuitem" class="users-row-menu-item users-row-menu-item-danger" data-delete-import="${imp.id}" title="${escapeHtml(deleteHistoryTitle)}"><i class="fas fa-trash users-row-menu-icon" aria-hidden="true"></i><span>Eliminar historial</span></button>` : ''}
                </div></div></td>
            `;
            tbody.appendChild(row);
        });

        updatePaginationInfo();
        renderPagination();
        updateSearchButton();
        clearVisibleImportSelection();
        document.getElementById('imports-table-status').textContent = `${pageImports.length} importaciones mostradas`;
    }

    function updateSearchButton() {
        const searchInput = document.getElementById('search-imports');
        const clearBtn = document.getElementById('clear-imports-btn');

        if (!searchInput || !clearBtn) return;

        clearBtn.classList.toggle('hidden', !searchInput.value);
    }

    function updatePaginationInfo() {
        const total = filteredImports.length;
        const start = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, total);
        
        const totalInView = allImports.filter(item => getImportView(item) === currentImportView).length;
        let text = `<span class="users-table-info-main">Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${total}</span></span>`;
        if (total !== totalInView) {
            text += `<span class="users-table-info-context text-sm text-gray-400">(${totalInView} en esta vista)</span>`;
        }
        document.getElementById('dt-info').classList.remove('is-loading');
        document.getElementById('dt-info').innerHTML = text;
    }

    function updateImportSelectionState() {
        const visibleChecks = Array.from(document.querySelectorAll('#imports-table tbody .row-check-import:not(:disabled)'));
        const checkedCount = visibleChecks.filter(input => input.checked).length;
        const totalVisible = visibleChecks.length;
        const hasSelection = checkedCount > 0;
        const allSelected = totalVisible > 0 && checkedCount === totalVisible;
        const selectAll = document.getElementById('select-all-imports');
        const button = document.getElementById('bulk-delete-imports');
        const hasSelectableRows = totalVisible > 0;

        if (selectAll) {
            selectAll.checked = allSelected;
            selectAll.indeterminate = hasSelection && !allSelected;
            selectAll.disabled = !hasSelectableRows;
            selectAll.title = hasSelectableRows
                ? 'Seleccionar registros visibles para eliminarlos del historial'
                : (currentImportView === 'active'
                    ? 'La selección está disponible en Revertidas y Fallidas'
                    : 'No hay registros eliminables en esta página');
            selectAll.setAttribute('aria-label', selectAll.title);
        }

        document.querySelectorAll('#imports-table tbody .imports-table-row').forEach(row => {
            row.classList.toggle('is-selected', Boolean(row.querySelector('.row-check-import')?.checked));
        });

        const bulkBar = document.getElementById('bulk-selection-bar-imports');
        const clearButton = document.getElementById('clear-selected-imports');
        const selectedCount = document.getElementById('bulk-selected-count-imports');
        if (hasSelection) {
            bulkBar?.classList.remove('hidden');
            bulkBar?.classList.add('flex');
            clearButton?.classList.remove('hidden');
            if (selectedCount) selectedCount.textContent = checkedCount + ' seleccionada' + (checkedCount === 1 ? '' : 's');
            if (button) button.style.display = 'flex';
        } else {
            bulkBar?.classList.add('hidden');
            bulkBar?.classList.remove('flex');
            clearButton?.classList.add('hidden');
            if (selectedCount) selectedCount.textContent = '';
            if (button) button.style.display = 'none';
        }
    }

    function clearVisibleImportSelection() {
        document.querySelectorAll('#imports-table tbody .row-check-import:not(:disabled)').forEach(input => {
            input.checked = false;
        });
        updateImportSelectionState();
    }

    function renderPagination() {
        const paginationContainer = document.getElementById('dt-pagination');
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(filteredImports.length / perPage);
        paginationContainer.classList.remove('hidden');

        const pageButton = page => page === currentPage
            ? `<span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">${page}</span>`
            : `<a href="#" data-page="${page}" class="pagination-link-imports fb-page-btn fb-page-num">${page}</a>`;
        const ellipsis = () => '<span class="fb-page-btn fb-page-num fb-page-ellipsis">...</span>';
        let html = '<div class="fb-pagination" role="navigation" aria-label="Paginación de importaciones">';

        html += currentPage === 1 || totalPages === 0
            ? '<span class="fb-page-btn fb-page-first fb-page-disabled">Anterior</span>'
            : `<a href="#" data-page="${currentPage - 1}" class="pagination-link-imports fb-page-btn fb-page-first">Anterior</a>`;

        if (totalPages <= 5) {
            for (let page = 1; page <= totalPages; page++) html += pageButton(page);
        } else if (currentPage <= 3) {
            for (let page = 1; page <= 5; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        } else if (currentPage >= totalPages - 2) {
            html += pageButton(1) + ellipsis();
            for (let page = totalPages - 4; page <= totalPages; page++) html += pageButton(page);
        } else {
            html += pageButton(1) + ellipsis();
            for (let page = currentPage - 1; page <= currentPage + 1; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        }

        html += currentPage === totalPages || totalPages === 0
            ? '<span class="fb-page-btn fb-page-last fb-page-disabled">Siguiente</span>'
            : `<a href="#" data-page="${currentPage + 1}" class="pagination-link-imports fb-page-btn fb-page-last">Siguiente</a>`;
        html += '</div>';
        paginationContainer.innerHTML = html;

        // Attach click handlers to pagination links
        document.querySelectorAll('.pagination-link-imports').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.dataset.page);
                currentPage = page;
                renderTable();
                document.querySelector('.imports-table-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showError(msg) {
        notifyImport(msg, 'error');
    }

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }

    function findNewerActiveImport(importItem) {
        if (!importItem) return null;

        // Prefer the exact dependency supplied by the traceability-aware API.
        if (importItem.reversal_blocked_by) {
            return importItem.reversal_blocked_by;
        }

        // If this field exists, a missing blocker is an authoritative result.
        if (Object.prototype.hasOwnProperty.call(importItem, 'record_trace_complete')) {
            return null;
        }

        const importTime = new Date(importItem.created_at).getTime();
        return allImports
            .filter(item => ['processing', 'completed'].includes(item.status) && !item.is_reversed)
            .filter(item => {
                const itemTime = new Date(item.created_at).getTime();
                return itemTime > importTime || (itemTime === importTime && Number(item.id) > Number(importItem.id));
            })
            .sort((a, b) => {
                const dateDifference = new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
                return dateDifference || Number(b.id) - Number(a.id);
            })[0] || null;
    }

    function reverseImportDescription(importItem) {
        const added = Number(importItem?.rows_imported || 0);
        const unchanged = Number(importItem?.rows_skipped_duplicates || importItem?.skipped_duplicates || 0);
        const different = Number(importItem?.rows_changed_existing || importItem?.changed_existing || 0);
        const toCorrect = Number(importItem?.rows_failed || 0);
        const unaffected = [];

        if (unchanged > 0) unaffected.push(`${unchanged.toLocaleString('es-MX')} ya existentes`);
        if (different > 0) unaffected.push(`${different.toLocaleString('es-MX')} con diferencias`);

        const parts = added > 0
            ? [`Se eliminarán permanentemente ${pluralizeImport(added, 'registro agregado', 'registros agregados')} por esta importación.`]
            : ['No se eliminarán registros porque esta importación no agregó ninguno; quedará marcada como revertida.'];

        if (unaffected.length) {
            parts.push(`${unaffected.join(' y ')} no se modificarán.`);
        }

        if (toCorrect > 0) {
            parts.push(`${pluralizeImport(toCorrect, 'registro por corregir', 'registros por corregir')} dejarán de estar activos.`);
        }

        if (added > 0) {
            parts.push('Las modificaciones posteriores realizadas sobre esos registros también se perderán.');
        }

        return parts.join(' ');
    }

    window.reverseImport = async function(importId) {
        const importItem = allImports.find(item => Number(item.id) === Number(importId));
        const newerActiveImport = findNewerActiveImport(importItem);

        if (newerActiveImport) {
            const blockingDate = new Date(newerActiveImport.created_at).toLocaleDateString('es-MX');
            const dependencyIsVerified = importItem?.reversal_block_reason === 'included_by_active_import';
            await window.confirmDialog({
                title: 'No se puede revertir todavía',
                question: dependencyIsVerified
                    ? 'Otra importación activa incluye registros de esta carga.'
                    : 'Hay una importación activa posterior.',
                description: `Primero revierta “${newerActiveImport.original_name || 'la carga posterior'}” del ${blockingDate}. Este orden conserva la consistencia del historial y evita dejar incompletos los cortes acumulativos.`,
                confirmText: 'Entendido',
                hideCancel: true,
                variant: 'warning'
            });
            return;
        }

        const confirmed = await window.confirmDialog({
            title: 'Revertir importación',
            question: `¿Deseas revertir ${importItem?.original_name ? `“${importItem.original_name}”` : 'esta importación'}?`,
            description: reverseImportDescription(importItem),
            confirmText: 'Revertir importación',
            cancelText: 'Cancelar',
            variant: 'warning'
        });

        if (!confirmed) return;

        const url = `/api/estadisticas/revertir-importacion/${importId}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });
            const json = await response.json();

            if (json.ok) {
                notifyImport(json.message || 'Importación revertida.', 'success');
                loadImports();
            } else if (['newer_active_import_exists', 'dependent_active_import_exists', 'newer_untraced_import_exists'].includes(json.code)) {
                const blocker = json.blocking_import || {};
                const blockingDate = blocker.created_at ? new Date(blocker.created_at).toLocaleDateString('es-MX') : 'una fecha posterior';
                await window.confirmDialog({
                    title: 'No se puede revertir todavía',
                    question: json.code === 'dependent_active_import_exists'
                        ? 'Otra importación activa incluye registros de esta carga.'
                        : 'El historial cambió y ahora existe una importación activa posterior.',
                    description: `Primero revierta “${blocker.original_name || 'la carga posterior'}” del ${blockingDate}.`,
                    confirmText: 'Entendido',
                    hideCancel: true,
                    variant: 'warning'
                });
                loadImports();
            } else {
                notifyImport(json.message || 'No se pudo revertir la importación.', 'error');
            }
        } catch (err) {
            console.error(err);
            notifyImport('No se pudo completar la acción. Intenta nuevamente.', 'error');
        }
    };

    window.deleteImport = async function(importId) {
        const importRecord = allImports.find(item => Number(item.id) === Number(importId));
        const discardedPending = Number(importRecord?.rows_failed || 0);
        const deleteDescription = discardedPending > 0
            ? `Se quitará del historial. También se perderá el registro de ${pluralizeImport(discardedPending, 'pendiente descartado', 'pendientes descartados')}.`
            : 'Se quitará del historial. Sus datos ya no están aplicados en el sistema.';
        const confirmed = await window.confirmDeleteDialog({
            title: 'Eliminar historial',
            subject: 'este registro del historial',
            description: deleteDescription,
            confirmText: 'Eliminar historial',
        });

        if (!confirmed) return;

        const url = `/api/estadisticas/importaciones/${importId}/delete`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(json => {
            if (json.ok) {
                notifyImport('El registro se eliminó del historial.', 'success');
                loadImports();
            } else {
                notifyImport(json.message || 'No se pudo eliminar el registro.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            notifyImport('No se pudo completar la acción. Intenta nuevamente.', 'error');
        });
    };
});

</script>
@endpush

