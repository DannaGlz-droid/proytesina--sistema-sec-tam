@extends('layouts.principal')
@section('title', 'Historial de Importaciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Historial de Importaciones</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
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
                <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                    <!-- Custom search, per-page controls -->
                    <div class="flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                        <div class="flex-1 min-w-0 sm:w-1/3 lg:w-1/2">
                            <div class="relative w-full max-w-xl min-w-0">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="search-imports" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 pr-24 p-2.5" placeholder="Buscar en importaciones...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                                    <button type="button" id="search-imports-btn" class="h-8 px-3 bg-[#611132] text-white rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-150" title="Buscar">
                                        <i class="fas fa-search text-xs"></i>
                                    </button>
                                    <button type="button" id="clear-imports-btn" class="h-8 px-2 bg-white border border-[#404041] text-gray-700 rounded-lg text-xs hover:bg-gray-100 hidden" title="Limpiar búsqueda">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="ml-0 sm:ml-auto flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                <select id="per-page-imports" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-24 p-2">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button id="bulk-delete-imports" type="button" class="bg-[#AB1A1A] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#8B1515] transition-all duration-300 font-lora items-center gap-2 whitespace-nowrap shadow-sm" title="Eliminar seleccionados" style="display: none;">
                                <i class="fas fa-trash text-xs"></i>
                                <span>Eliminar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="overflow-x-auto min-w-0">
                        <table id="imports-table" class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs"><input id="select-all-imports" type="checkbox" /></th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting sortable" data-sort-key="original_name">Archivo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting sortable" data-sort-key="created_by">Cargado por</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs sorting_desc sortable" data-sort-key="created_at">Fecha</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Total</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Importados</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fallidos</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Revertido</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-right w-32" data-orderable="false">Acciones</th>
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
            font-weight: 500;
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

        /* Table alternating rows */
        #imports-tbody tr { transition: background-color .15s ease; }
        #imports-tbody tr:hover { background-color: #f9fafb; }
        #imports-tbody tr:nth-child(even) { background-color: #f9fafb; }
        #imports-tbody tr:nth-child(odd) { background-color: white; }

        /* Hide ALL DataTables native controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        #imports-table.dataTable tbody tr { 
            transition: background-color .15s ease; 
        }
        #imports-table.dataTable tbody tr:hover { 
            background-color: #f9fafb; 
        }
        #imports-table.dataTable tbody tr:nth-child(even) { 
            background-color: #f9fafb; 
        }
        #imports-table.dataTable tbody tr:nth-child(odd) { 
            background-color: white; 
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

    // Load imports on page load
    loadImports();

    // Event listeners for filters
    const searchInput = document.getElementById('search-imports');
    const searchBtn = document.getElementById('search-imports-btn');
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

    // Helper function to apply filters
    function applyFilters() {
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
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Search button
    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            applyFilters();
        });
    }

    // Clear search button
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
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

    // Checkbox selection: select-all for visible page
    $('#select-all-imports').on('change', function() {
        const checked = $(this).is(':checked');
        $('#imports-table tbody .row-check-import').prop('checked', checked);
        toggleBulkDeleteImportsButton();
    });

    // Delegate click for row checkboxes
    $('#imports-table tbody').on('change', '.row-check-import', function() {
        if (!$(this).is(':checked')) {
            $('#select-all-imports').prop('checked', false);
        }
        toggleBulkDeleteImportsButton();
    });

    // Bulk delete action
    $('#bulk-delete-imports').on('click', function() {
        const ids = [];
        $('#imports-table tbody .row-check-import:checked').each(function() {
            const id = $(this).data('id');
            if (id) ids.push(id);
        });

        if (!ids.length) {
            alert('Selecciona al menos un registro.');
            return;
        }

        if (!confirm('¿Confirmas eliminar ' + ids.length + ' registro(s) del historial? Esta acción no se puede deshacer.')) {
            return;
        }

        $.ajax({
            url: '{{ route('statistic.import-history.massDelete') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            data: { ids: ids },
            success: function(res) {
                if (res && res.ok) {
                    alert('Eliminados: ' + (res.deleted || 0));
                    $('#select-all-imports').prop('checked', false);
                    loadImports();
                } else {
                    alert('Error al eliminar. Revisa la consola.');
                    console.error(res);
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Error del servidor: ' + (xhr.responseText || xhr.statusText));
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
                showError('Error al cargar importaciones');
            }
        })
        .catch(err => {
            console.error(err);
            showError('Error al cargar datos');
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
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">No se encontraron registros coincidentes</td></tr>';
            updatePaginationInfo();
            toggleBulkDeleteImportsButton();
            try { $('#select-all-imports').prop('checked', false); } catch (e) {}
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
            
            const statusClass = imp.is_reversed ? 'status-reversed' : `status-${imp.status}`;
            const statusText = imp.is_reversed ? 'Revertido' : (imp.status === 'completed' ? 'Completado' : imp.status === 'processing' ? 'Procesando' : 'Fallido');
            
            const canReverse = !imp.is_reversed && imp.status === 'completed';

            row.innerHTML = `
                <td class="px-3 py-2"><input class="row-check-import" data-id="${imp.id}" type="checkbox" /></td>
                <td class="px-3 py-2 font-semibold text-gray-900">${escapeHtml(imp.original_name)}</td>
                <td class="px-3 py-2">
                    <div class="flex flex-col leading-tight">
                        <span class="font-medium text-gray-900">${escapeHtml(createdByName)}</span>
                        ${createdByUsername ? `<span class="text-[11px] text-gray-500">${escapeHtml(createdByUsername)}</span>` : ''}
                    </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-xs">${createdDate.toLocaleDateString('es-ES')} ${createdDate.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</td>
                <td class="px-3 py-2 text-center font-semibold">${imp.rows_total}</td>
                <td class="px-3 py-2 text-center"><span class="text-green-600 font-semibold">${imp.rows_imported}</span></td>
                <td class="px-3 py-2 text-center"><span class="${imp.rows_failed > 0 ? 'text-red-600 font-semibold' : 'text-gray-500'}">${imp.rows_failed > 0 ? imp.rows_failed : 0}</span></td>
                <td class="px-3 py-2"><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td class="px-3 py-2 text-center">${imp.is_reversed ? '<i class="fas fa-check-circle text-green-600" title="Revertido" aria-label="Revertido"></i>' : '<i class="fas fa-circle text-gray-400" title="No revertido" aria-label="No revertido"></i>'}</td>
                <td class="px-3 py-2 text-right flex gap-2 justify-end">
                    ${imp.rows_failed > 0 ? `
                        <a href="/estadisticas/importaciones/${imp.id}/registros-fallidos" 
                           class="inline-flex items-center justify-center gap-1 w-20 px-2 py-1 rounded text-xs font-semibold border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200 ${canReverse ? '' : 'action-btn-disabled'}" 
                           ${canReverse ? '' : 'onclick="return false;" style="pointer-events: none; cursor: not-allowed;"'}
                           title="${canReverse ? 'Ver registros fallidos' : 'No disponible (importación revertida)'}">
                            <i class="fas fa-exclamation-circle text-xs"></i> Fallidos
                        </a>
                    ` : `<div class="w-20"></div>`}
                    <button class="inline-flex items-center justify-center gap-1 w-20 px-2 py-1 rounded text-xs font-semibold border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200 ${canReverse ? '' : 'action-btn-disabled'}" 
                            onclick="reverseImport(${imp.id})" 
                            ${canReverse ? '' : 'disabled'} 
                            title="${canReverse ? 'Revertir esta importación' : 'No se puede revertir'}">
                        <i class="fas fa-undo text-xs"></i> Revertir
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        updatePaginationInfo();
        renderPagination();
        updateSearchButton();
        toggleBulkDeleteImportsButton();
        try { $('#select-all-imports').prop('checked', false); } catch (e) {}
    }

    function updateSearchButton() {
        const searchInput = document.getElementById('search-imports');
        const clearBtn = document.getElementById('clear-imports-btn');
        
        if (searchInput.value) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
    }

    function updatePaginationInfo() {
        const total = filteredImports.length;
        const start = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, total);
        
        document.getElementById('info-imports').innerHTML = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${total}</span> entradas`;
    }

    function toggleBulkDeleteImportsButton() {
        const any = $('#imports-table tbody .row-check-import:checked').length > 0;
        const button = document.getElementById('bulk-delete-imports');
        if (!button) return;

        button.style.display = any ? 'flex' : 'none';
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
        alert(msg);
    }

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }
});

function reverseImport(importId) {
    if (!confirm('¿Estás seguro de que deseas revertir esta importación? Esto eliminará todos los registros importados.')) {
        return;
    }

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
            alert(json.message);
            location.reload();
        } else {
            alert('Error: ' + (json.message || 'Error desconocido'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar la solicitud');
    });
}
</script>
@endpush

