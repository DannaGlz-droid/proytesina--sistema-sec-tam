@extends('layouts.principal')
@section('title', 'Historial de Importaciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="app-page-title">Historial de Importaciones</h1>
                <p class="app-page-subtitle">
                    Visualiza todas las importaciones realizadas, filtra por fecha y estado, y revierte las que desees deshacer.
                </p>
            </div>
            <a href="{{ route('statistic.data') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-arrow-left text-xs"></i>
                Volver
            </a>
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80 flex-shrink-0">
                <x-filtros.importaciones />
            </div>

            <!-- Columna Derecha - Tabla -->
            <div class="flex-1 min-w-0">
                <div class="app-table-card">
                    <!-- Custom search, per-page controls -->
                    <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                        <div class="flex-1 min-w-0 sm:w-1/3 lg:w-1/2">
                            <div class="app-table-search">
                                <div class="app-table-search-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <input type="text" id="search-imports" class="app-table-search-input" placeholder="Buscar en importaciones...">
                                <div class="app-table-search-actions">
                                    <button type="button" id="clear-imports-btn" class="app-table-clear-button hidden" title="Limpiar búsqueda">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="app-table-controls ml-0 sm:ml-auto flex flex-wrap items-center justify-end gap-3">
                            <div class="app-table-page-size">
                                <span>Mostrar</span>
                                <select id="per-page-imports">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div id="bulk-selection-bar-imports" class="app-table-bulk-inline hidden items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="app-table-selection-marker"></span>
                                    <span id="bulk-selected-count-imports" class="app-table-selection-count text-xs font-lora whitespace-nowrap"></span>
                                </div>
                                <span class="hidden xl:inline text-xs font-lora text-gray-500">En esta página</span>
                                <button id="clear-selected-imports" type="button" class="hidden text-xs font-semibold font-lora text-[#611132] hover:underline whitespace-nowrap" title="Quitar selección">
                                    Quitar selección
                                </button>
                                <button id="bulk-delete-imports" type="button" class="app-table-bulk-danger items-center gap-2" title="Eliminar seleccionados" style="display: none;">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Eliminar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="app-table-shell overflow-x-auto min-w-0">
                        <table id="imports-table" class="app-data-table min-w-full w-full text-sm text-left text-gray-500">
                            <colgroup>
                                <col style="width: 2.75rem;">
                                <col style="width: 32%;">
                                <col style="width: 15%;">
                                <col style="width: 8%;">
                                <col style="width: 5%;">
                                <col style="width: 10%;">
                                <col style="width: 6%;">
                                <col style="width: 6%;">
                                <col style="width: 8%;">
                                <col style="width: 6.75rem;">
                            </colgroup>
                            <thead class="text-xs border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-2 py-2 font-lora whitespace-nowrap text-xs text-center"><input id="select-all-imports" type="checkbox" /></th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting sortable" data-sort-key="original_name">Archivo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting sortable" data-sort-key="created_by">Cargado por</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting_desc sortable" data-sort-key="created_at">Fecha</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Total</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Importados</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs" title="Folios existentes omitidos o detectados con datos distintos">Observaciones</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fallidos</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-right w-24" data-orderable="false">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="imports-tbody">
                                <!-- Se llena con JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav class="flex flex-row flex-wrap items-center justify-between gap-3 p-4 border-t border-[#404041]">
                        <span class="text-sm font-normal text-gray-500 font-lora flex-1 min-w-0" id="info-imports">
                            Mostrando <span class="font-semibold text-gray-900">0-0</span> de <span class="font-semibold text-gray-900">0</span> entradas
                        </span>
                        <div id="pagination-imports" class="flex-none"></div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-processing {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-reversed {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .action-btn-disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .action-btn-disabled:hover {
            background-color: white !important;
            color: inherit !important;
        }

        #imports-table thead th:nth-child(1),
        #imports-table tbody td:nth-child(1) {
            width: 2.75rem;
            text-align: center;
            padding-left: 0.75rem;
        }

        #imports-table thead th:nth-child(2),
        #imports-table tbody td:nth-child(2) {
            width: 32%;
        }

        #imports-table thead th:nth-child(3),
        #imports-table tbody td:nth-child(3) {
            width: 15%;
        }

        #imports-table thead th:nth-child(4),
        #imports-table tbody td:nth-child(4) {
            width: 8%;
            white-space: nowrap;
        }

        #imports-table thead th:nth-child(5),
        #imports-table tbody td:nth-child(5) {
            width: 5%;
            text-align: center;
        }

        #imports-table thead th:nth-child(6),
        #imports-table tbody td:nth-child(6) {
            width: 6%;
            text-align: center;
        }

        #imports-table thead th:nth-child(7),
        #imports-table tbody td:nth-child(7),
        #imports-table thead th:nth-child(8),
        #imports-table tbody td:nth-child(8),
        #imports-table thead th:nth-child(9),
        #imports-table tbody td:nth-child(9) {
            width: 6%;
            text-align: center;
        }

        #imports-table thead th:nth-child(10),
        #imports-table tbody td:nth-child(10) {
            width: 8%;
            white-space: nowrap;
        }

        #imports-table thead th:nth-child(11),
        #imports-table tbody td:nth-child(11) {
            width: 6.75rem;
            white-space: nowrap;
        }

        /* Sorting style aligned with existing DataTables tables */
        #imports-table thead th.sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 1.5rem;
        }

        #imports-table thead th.sorting::before,
        #imports-table thead th.sorting::after,
        #imports-table thead th.sorting_asc::before,
        #imports-table thead th.sorting_asc::after,
        #imports-table thead th.sorting_desc::before,
        #imports-table thead th.sorting_desc::after {
            position: absolute;
            right: 0.5rem;
            line-height: 1;
            font-size: 0.55rem;
            color: #d1d5db;
            opacity: 1;
            pointer-events: none;
        }

        #imports-table thead th.sorting::before,
        #imports-table thead th.sorting_asc::before,
        #imports-table thead th.sorting_desc::before {
            content: '▲';
            top: 40%;
            transform: translateY(-50%);
        }

        #imports-table thead th.sorting::after,
        #imports-table thead th.sorting_asc::after,
        #imports-table thead th.sorting_desc::after {
            content: '▼';
            top: 62%;
            transform: translateY(-50%);
        }

        #imports-table thead th.sorting_asc::before {
            color: #9ca3af;
        }

        #imports-table thead th.sorting_asc::after {
            color: #e5e7eb;
        }

        #imports-table thead th.sorting_desc::before {
            color: #e5e7eb;
        }

        #imports-table thead th.sorting_desc::after {
            color: #9ca3af;
        }
    </style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let perPage = 10;
    let allImports = [];
    let filteredImports = [];
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
        return `
            <tr class="app-table-loading-row">
                <td colspan="10">
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

    function importsMessageRow(message, tone = 'muted') {
        const toneClass = tone === 'error' ? 'text-red-700' : 'text-gray-500';

        return `
            <tr>
                <td colspan="10" class="text-center py-4 ${toneClass}">
                    ${escapeHtml(message)}
                </td>
            </tr>
        `;
    }

    function setImportsTableLoading(isLoading) {
        if (!isLoading) return;

        const tbody = document.getElementById('imports-tbody');
        if (!tbody) return;

        tbody.innerHTML = importsLoadingRow();
    }

    function showImportsLoadError() {
        allImports = [];
        filteredImports = [];
        currentPage = 1;

        const tbody = document.getElementById('imports-tbody');
        if (tbody) tbody.innerHTML = importsMessageRow('No se pudieron cargar las importaciones.', 'error');

        updatePaginationInfo();
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

    // Load imports on page load
    loadImports();

    // Event listeners for filters
    const searchInput = document.getElementById('search-imports');
    const clearBtn = document.getElementById('clear-imports-btn');
    const dateRangeSelect = document.getElementById('dateRangeImports');
    const startDateInput = document.getElementById('startDateImports');
    const endDateInput = document.getElementById('endDateImports');
    const statusCheckboxes = document.querySelectorAll('.status-checkbox');
    const conFallidosCheckbox = document.getElementById('conFallidos');
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
            } else {
                if (!header.classList.contains('sorting')) {
                    header.classList.add('sorting');
                }
            }
        });
    }

    // Search input - applies only on Enter key
    if (searchInput) {
        searchInput.addEventListener('input', updateSearchButton);

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Clear search button
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            updateSearchButton();
            applyFilters();
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
        header.addEventListener('click', () => {
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
        });
    });

    // Checkbox selection: select-all toggles the visible page
    $('#select-all-imports').on('change', function() {
        const visibleChecks = $('#imports-table tbody .row-check-import:not(:disabled)');
        const checkedCount = visibleChecks.filter(':checked').length;
        const shouldCheck = checkedCount !== visibleChecks.length;
        visibleChecks.prop('checked', shouldCheck);
        updateImportSelectionState();
    });

    // Delegate click for row checkboxes
    $('#imports-table tbody').on('change', '.row-check-import', function() {
        updateImportSelectionState();
    });

    $('#imports-table tbody').on('click', 'input.row-check-import, button, a', function(e) {
        e.stopPropagation();
    });

    $('#clear-selected-imports').on('click', function() {
        clearVisibleImportSelection();
    });

    // Bulk delete action
    $('#bulk-delete-imports').on('click', async function() {
        const ids = [];
        $('#imports-table tbody .row-check-import:checked').each(function() {
            const id = $(this).data('id');
            if (id) ids.push(id);
        });

        if (!ids.length) {
            notifyImport('Selecciona al menos una importación eliminable.', 'warning');
            return;
        }

        const confirmed = await window.confirmDeleteDialog({
            title: 'Eliminar historial',
            subject: ids.length + ' importación' + (ids.length === 1 ? '' : 'es') + ' del historial',
            description: 'Solo pueden eliminarse importaciones revertidas o que no dejaron registros importados.',
            confirmText: 'Eliminar historial',
        });

        if (!confirmed) return;

        $.ajax({
            url: '{{ route('statistic.import-history.massDelete') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            data: { ids: ids },
            success: function(res) {
                if (res && res.ok) {
                    const deletedImports = Number(res.deleted || 0);
                    notifyImport(
                        deletedImports === 1
                            ? 'Se eliminó 1 importación del historial.'
                            : `Se eliminaron ${deletedImports} importaciones del historial.`,
                        'success'
                    );
                    clearVisibleImportSelection();
                    loadImports();
                } else {
                    notifyImport(res.message || 'No se pudieron eliminar las importaciones. Intenta nuevamente.', 'error');
                    console.error(res);
                }
            },
            error: function(xhr) {
                console.error(xhr);
                notifyImport(xhr.responseJSON?.message || 'No se pudieron eliminar las importaciones. Intenta nuevamente.', 'error');
            }
        });
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
            if (dateRangeSelect) dateRangeSelect.value = 'all';
            
            // Hide custom date range if visible
            const customRangeSelector = document.getElementById('customRangeSelectorImports');
            if (customRangeSelector) {
                customRangeSelector.style.display = 'none';
            }
            
            // Reset status checkboxes
            statusCheckboxes.forEach(cb => cb.checked = false);
            
            // Reset con fallidos checkbox
            if (conFallidosCheckbox) conFallidosCheckbox.checked = false;
            
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
        const url = '{{ route("statistic.import-history") }}';
        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(json => {
            if (json.ok && json.data && json.data.data) {
                allImports = json.data.data;
                filteredImports = [...allImports];
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
        });
    }

    window.filterImports = function() {
        const searchTerm = document.getElementById('search-imports').value.toLowerCase();
        const dateRange = document.getElementById('dateRangeImports').value;
        const selectedStatuses = Array.from(document.querySelectorAll('.status-checkbox:checked')).map(cb => cb.value);
        const usuarioId = document.getElementById('usuarioImports').value; // Now this is an ID, not a name
        const conFallidos = document.getElementById('conFallidos').checked;
        const startDate = document.getElementById('startDateImports').value;
        const endDate = document.getElementById('endDateImports').value;

        filteredImports = allImports.filter(imp => {
            // Búsqueda en nombre de archivo
            if (searchTerm && !(imp.original_name && imp.original_name.toLowerCase().includes(searchTerm))) {
                return false;
            }

            // Filtro por usuario (now comparing by ID)
            if (usuarioId && parseInt(usuarioId) !== parseInt(imp.user_id)) {
                return false;
            }

            // Filtro por estado
            if (selectedStatuses.length > 0) {
                const isReversed = imp.is_reversed;
                const status = isReversed ? 'reversed' : imp.status;
                if (!selectedStatuses.includes(status)) {
                    return false;
                }
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

            // Filtro por registros fallidos
            if (conFallidos && imp.rows_failed === 0) {
                return false;
            }

            return true;
        });
    };

    function renderTable() {
        const tbody = document.getElementById('imports-tbody');
        tbody.innerHTML = '';

        if (filteredImports.length === 0) {
            tbody.innerHTML = importsMessageRow('No se encontraron registros coincidentes');
            updatePaginationInfo();
            clearVisibleImportSelection();
            return;
        }

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageImports = filteredImports.slice(start, end);

        pageImports.forEach(imp => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50 transition-colors';

                const createdDate = new Date(imp.created_at);
                const createdByName = [imp.created_by, imp.created_by_first_last_name, imp.created_by_second_last_name]
                    .filter(Boolean)
                    .join(' ') || 'Sistema';
                const createdByUsername = imp.created_by_username ? `@${imp.created_by_username}` : '';
            
            const skippedDuplicates = Number(imp.rows_skipped_duplicates || imp.skipped_duplicates || 0);
            const changedExisting = Number(imp.rows_changed_existing || imp.changed_existing || 0);
            const observationParts = [];
            if (skippedDuplicates > 0) {
                observationParts.push(`<span class="text-gray-700 font-semibold">${skippedDuplicates} omitidos</span>`);
            }
            if (changedExisting > 0) {
                observationParts.push(`<span class="text-amber-700 font-semibold" title="Folios existentes con datos distintos. No se sobrescribieron automaticamente.">${changedExisting} cambios</span>`);
            }
            const observationsHtml = observationParts.length
                ? observationParts.join('<span class="text-gray-300 px-1">/</span>')
                : '<span class="text-gray-500">-</span>';
            const statusClass = imp.is_reversed ? 'status-reversed' : `status-${imp.status}`;
            const statusText = imp.is_reversed ? 'Revertido' : (imp.status === 'completed' ? 'Completado' : imp.status === 'processing' ? 'Procesando' : 'Fallido');
            
            const canReverse = !imp.is_reversed && imp.status === 'completed';
            const canDeleteHistory = canDeleteImportHistory(imp);
            const deleteHistoryTitle = canDeleteHistory
                ? 'Eliminar historial'
                : 'Primero revierte esta importación para conservar la trazabilidad de los datos';

            row.innerHTML = `
                <td class="text-center"><input class="row-check-import" data-id="${imp.id}" type="checkbox" ${canDeleteHistory ? '' : 'disabled'} title="${deleteHistoryTitle}" /></td>
                <td class="app-cell-wrap app-cell-strong">${escapeHtml(imp.original_name)}</td>
                <td class="app-cell-wrap">
                    <div class="flex flex-col leading-tight">
                        <span class="font-medium text-gray-900">${escapeHtml(createdByName)}</span>
                        ${createdByUsername ? `<span class="text-[11px] text-gray-500">${escapeHtml(createdByUsername)}</span>` : ''}
                    </div>
                </td>
                <td class="app-cell-nowrap text-xs">${createdDate.toLocaleDateString('es-ES')} ${createdDate.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</td>
                <td class="text-center font-semibold">${imp.rows_total}</td>
                <td class="text-center"><span class="text-green-600 font-semibold">${imp.rows_imported}</span></td>
                <td class="text-center" title="Folios que ya existían y se omitieron para evitar duplicados">
                    ${observationsHtml}
                </td>
                <td class="text-center" style="display:none">
                    ${changedExisting > 0 ? `<span class="text-amber-700 font-semibold" title="Folios existentes con datos distintos. No se sobrescribieron automáticamente.">${changedExisting}</span>` : '<span class="text-gray-500">0</span>'}
                </td>
                <td class="text-center">
                    ${imp.rows_failed > 0 ? `
                        <a href="/estadisticas/importaciones/${imp.id}/registros-fallidos"
                           class="inline-flex items-center justify-center text-red-600 font-semibold underline-offset-4 hover:underline hover:text-red-700 transition"
                           title="Ver registros fallidos" aria-label="Ver ${imp.rows_failed} registros fallidos de la importación ${imp.id}">
                            ${imp.rows_failed}
                        </a>
                    ` : '<span class="text-gray-500">0</span>'}
                </td>
                <td class="app-cell-nowrap"><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td class="app-cell-nowrap text-right">
                    <div class="flex gap-2 justify-end">
                    ${false ? `
                        <a href="/estadisticas/importaciones/${imp.id}/registros-fallidos" 
                           class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200 ${canReverse ? '' : 'action-btn-disabled'}" 
                           ${canReverse ? '' : 'onclick="return false;" style="pointer-events: none; cursor: not-allowed;"'}
                           title="${canReverse ? 'Ver registros fallidos' : 'No disponible (importación revertida)'}" aria-label="Registros fallidos ${imp.id}">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                        </a>
                    ` : ``}
                    <button class="w-7 h-7 flex items-center justify-center rounded border border-[#D99A00] text-[#B7791F] hover:bg-[#D99A00] hover:text-white transition-all duration-200 ${canReverse ? '' : 'action-btn-disabled'}"
                            onclick="reverseImport(${imp.id})" ${canReverse ? '' : 'disabled'} title="${canReverse ? 'Revertir esta importación' : 'No se puede revertir'}" aria-label="Revertir ${imp.id}">
                        <i class="fas fa-undo text-xs"></i>
                    </button>
                    <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200 ${canDeleteHistory ? '' : 'action-btn-disabled'}" onclick="deleteImport(${imp.id})" ${canDeleteHistory ? '' : 'disabled'} title="${deleteHistoryTitle}" aria-label="Eliminar historial ${imp.id}">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        updatePaginationInfo();
        renderPagination();
        updateSearchButton();
        clearVisibleImportSelection();
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
        
        document.getElementById('info-imports').innerHTML = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${total}</span> entradas`;
    }

    function updateImportSelectionState() {
        const visibleChecks = $('#imports-table tbody .row-check-import:not(:disabled)');
        const checkedCount = visibleChecks.filter(':checked').length;
        const totalVisible = visibleChecks.length;
        const hasSelection = checkedCount > 0;
        const allSelected = totalVisible > 0 && checkedCount === totalVisible;
        const selectAll = $('#select-all-imports');
        const button = document.getElementById('bulk-delete-imports');
        const hasSelectableRows = totalVisible > 0;

        selectAll.prop('checked', allSelected);
        selectAll.prop('indeterminate', hasSelection && !allSelected);
        selectAll.prop('disabled', !hasSelectableRows);
        selectAll.attr(
            'title',
            hasSelectableRows
                ? 'Seleccionar importaciones eliminables en esta página'
                : 'No hay importaciones eliminables en esta página'
        );

        if (hasSelection) {
            $('#bulk-selection-bar-imports').removeClass('hidden').addClass('flex');
            $('#clear-selected-imports').removeClass('hidden');
            $('#bulk-selected-count-imports')
                .text(checkedCount + ' seleccionada' + (checkedCount === 1 ? '' : 's'));
            if (button) button.style.display = 'flex';
        } else {
            $('#bulk-selection-bar-imports').addClass('hidden').removeClass('flex');
            $('#clear-selected-imports').addClass('hidden');
            $('#bulk-selected-count-imports').text('');
            if (button) button.style.display = 'none';
        }
    }

    function clearVisibleImportSelection() {
        $('#imports-table tbody .row-check-import:not(:disabled)').prop('checked', false);
        updateImportSelectionState();
    }

    function renderPagination() {
        const paginationContainer = document.getElementById('pagination-imports');
        paginationContainer.innerHTML = '';

        const totalPages = Math.ceil(filteredImports.length / perPage);
        if (totalPages <= 1) return;

        let html = '<ul class="inline-flex items-stretch -space-x-px">';

        // Previous button
        if (currentPage === 1) {
            html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-left text-xs"></i></span></li>';
        } else {
            html += `<li><a href="#" data-page="${currentPage - 1}" class="pagination-link-imports flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-left text-xs"></i></a></li>`;
        }

        // Page numbers
        const maxButtons = 5;
        if (totalPages <= maxButtons) {
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                } else {
                    html += `<li><a href="#" data-page="${i}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                }
            }
        } else {
            // Complex pagination with ellipsis
            if (currentPage <= 3) {
                for (let i = 1; i <= 5; i++) {
                    if (i === currentPage) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                html += `<li><a href="#" data-page="${totalPages}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${totalPages}</a></li>`;
            } else if (currentPage >= totalPages - 2) {
                html += `<li><a href="#" data-page="1" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                for (let i = totalPages - 4; i <= totalPages; i++) {
                    if (i === currentPage) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
            } else {
                html += `<li><a href="#" data-page="1" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                for (let i = currentPage - 2; i <= currentPage + 2; i++) {
                    if (i === currentPage) {
                        html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                    } else {
                        html += `<li><a href="#" data-page="${i}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                    }
                }
                html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                html += `<li><a href="#" data-page="${totalPages}" class="pagination-link-imports flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${totalPages}</a></li>`;
            }
        }

        // Next button
        if (currentPage === totalPages || totalPages === 0) {
            html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-right text-xs"></i></span></li>';
        } else {
            html += `<li><a href="#" data-page="${currentPage + 1}" class="pagination-link-imports flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-right text-xs"></i></a></li>`;
        }

        html += '</ul>';
        paginationContainer.innerHTML = html;

        // Attach click handlers to pagination links
        document.querySelectorAll('.pagination-link-imports').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.dataset.page);
                currentPage = page;
                renderTable();
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

    window.reverseImport = async function(importId) {
        const confirmed = await window.confirmDialog({
            title: 'Revertir importación',
            question: '¿Deseas revertir esta importación?',
            description: 'Los registros creados por la importación se eliminarán de forma permanente.',
            confirmText: 'Revertir',
            cancelText: 'Cancelar',
            variant: 'warning'
        });

        if (!confirmed) return;

        const url = `/api/estadisticas/revertir-importacion/${importId}`;
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
                notifyImport(json.message || 'Importación revertida.', 'success');
                loadImports();
            } else {
                notifyImport(json.message || 'No se pudo revertir la importación.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            notifyImport('No se pudo completar la acción. Intenta nuevamente.', 'error');
        });
    };

    window.deleteImport = async function(importId) {
        const confirmed = await window.confirmDeleteDialog({
            title: 'Eliminar historial',
            subject: 'este registro del historial',
            description: 'Solo puede eliminarse si la importación fue revertida o no dejó registros importados.',
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

