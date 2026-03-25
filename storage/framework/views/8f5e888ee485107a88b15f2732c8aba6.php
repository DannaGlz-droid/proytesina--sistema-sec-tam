
<?php $__env->startSection('title', 'Historial de Importaciones'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-estadisticas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Historial de Importaciones</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Visualiza todas las importaciones realizadas, filtra por fecha y estado, y revierte las que desees deshacer.
                </p>
            </div>
            <a href="<?php echo e(route('statistic.data')); ?>" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-arrow-left text-xs"></i>
                Volver
            </a>
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80 flex-shrink-0">
                <?php if (isset($component)) { $__componentOriginal8f2a4b25389a21c6c450f585b532a2e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f2a4b25389a21c6c450f585b532a2e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.importaciones','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.importaciones'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f2a4b25389a21c6c450f585b532a2e2)): ?>
<?php $attributes = $__attributesOriginal8f2a4b25389a21c6c450f585b532a2e2; ?>
<?php unset($__attributesOriginal8f2a4b25389a21c6c450f585b532a2e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f2a4b25389a21c6c450f585b532a2e2)): ?>
<?php $component = $__componentOriginal8f2a4b25389a21c6c450f585b532a2e2; ?>
<?php unset($__componentOriginal8f2a4b25389a21c6c450f585b532a2e2); ?>
<?php endif; ?>
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
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="overflow-x-auto min-w-0">
                        <table id="imports-table" class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Archivo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Cargado por</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha</th>
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
    </style>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let perPage = 10;
    let allImports = [];
    let filteredImports = [];

    // Load imports on page load
    loadImports();

    // Event listeners for filters
    document.getElementById('search-imports').addEventListener('keyup', debounce(() => {
        filterImports();
        currentPage = 1;
        renderTable();
    }, 300));

    document.getElementById('search-imports-btn').addEventListener('click', () => {
        filterImports();
        currentPage = 1;
        renderTable();
    });

    document.getElementById('clear-imports-btn').addEventListener('click', () => {
        document.getElementById('search-imports').value = '';
        filterImports();
        currentPage = 1;
        renderTable();
    });

    document.getElementById('dateRangeImports').addEventListener('change', () => {
        filterImports();
        currentPage = 1;
        renderTable();
    });

    document.querySelectorAll('.status-checkbox, #conFallidos').forEach(el => {
        el.addEventListener('change', () => {
            filterImports();
            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('usuarioImports').addEventListener('keyup', debounce(() => {
        filterImports();
        currentPage = 1;
        renderTable();
    }, 300));

    document.getElementById('per-page-imports').addEventListener('change', function (e) {
        perPage = parseInt(e.target.value);
        currentPage = 1;
        renderTable();
    });

    // Date pickers for custom range
    document.getElementById('startDateImports').addEventListener('change', () => {
        filterImports();
        currentPage = 1;
        renderTable();
    });

    document.getElementById('endDateImports').addEventListener('change', () => {
        filterImports();
        currentPage = 1;
        renderTable();
    });

    function loadImports() {
        const url = '<?php echo e(route("statistic.import-history")); ?>';
        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(res => res.json())
        .then(json => {
            if (json.ok && json.data && json.data.data) {
                allImports = json.data.data;
                filteredImports = [...allImports];
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
        const usuario = document.getElementById('usuarioImports').value.toLowerCase();
        const conFallidos = document.getElementById('conFallidos').checked;
        const startDate = document.getElementById('startDateImports').value;
        const endDate = document.getElementById('endDateImports').value;

        filteredImports = allImports.filter(imp => {
            // Búsqueda en nombre de archivo
            if (searchTerm && !(imp.original_name && imp.original_name.toLowerCase().includes(searchTerm))) {
                return false;
            }

            // Filtro por usuario
            if (usuario && !(imp.created_by && imp.created_by.toLowerCase().includes(usuario))) {
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
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-500">No hay importaciones que cumplan los filtros</td></tr>';
            updatePaginationInfo();
            return;
        }

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageImports = filteredImports.slice(start, end);

        pageImports.forEach(imp => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50 transition-colors';

            const createdDate = new Date(imp.created_at);
            
            const statusClass = imp.is_reversed ? 'status-reversed' : `status-${imp.status}`;
            const statusText = imp.is_reversed ? 'Revertido' : (imp.status === 'completed' ? 'Completado' : imp.status === 'processing' ? 'Procesando' : 'Fallido');
            
            const canReverse = !imp.is_reversed && imp.status === 'completed';

            row.innerHTML = `
                <td class="px-3 py-2 font-semibold text-gray-900">${escapeHtml(imp.original_name)}</td>
                <td class="px-3 py-2">${imp.created_by || 'Sistema'}</td>
                <td class="px-3 py-2 whitespace-nowrap text-xs">${createdDate.toLocaleDateString('es-ES')} ${createdDate.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</td>
                <td class="px-3 py-2 text-center font-semibold">${imp.rows_total}</td>
                <td class="px-3 py-2 text-center"><span class="text-green-600 font-semibold">${imp.rows_imported}</span></td>
                <td class="px-3 py-2 text-center"><span class="${imp.rows_failed > 0 ? 'text-red-600 font-semibold' : 'text-gray-500'}">${imp.rows_failed > 0 ? imp.rows_failed : 'N/A'}</span></td>
                <td class="px-3 py-2"><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td class="px-3 py-2 text-center">${imp.is_reversed ? '<i class="fas fa-check-circle text-green-600" title="Revertido" aria-label="Revertido"></i>' : '<i class="fas fa-times-circle text-red-600" title="No revertido" aria-label="No revertido"></i>'}</td>
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

    function renderPagination() {
        const paginationContainer = document.getElementById('pagination-imports');
        paginationContainer.innerHTML = '';

        const totalPages = Math.ceil(filteredImports.length / perPage);
        if (totalPages <= 1) return;

        const createPageBtn = (page, label, active = false) => {
            const btn = document.createElement('button');
            btn.textContent = label;
            btn.className = `px-3 py-1 mx-1 text-sm rounded-lg font-semibold transition-all ${
                active 
                    ? 'bg-[#611132] text-white' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`;
            btn.onclick = () => {
                currentPage = page;
                renderTable();
            };
            btn.disabled = active;
            return btn;
        };

        // Previous button
        if (currentPage > 1) {
            paginationContainer.appendChild(createPageBtn(currentPage - 1, '← Anterior'));
        }

        // Page numbers
        for (let i = Math.max(1, currentPage - 1); i <= Math.min(totalPages, currentPage + 1); i++) {
            paginationContainer.appendChild(createPageBtn(i, i.toString(), i === currentPage));
        }

        // Next button
        if (currentPage < totalPages) {
            paginationContainer.appendChild(createPageBtn(currentPage + 1, 'Siguiente →'));
        }
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>';

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
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/estadisticas/import-history.blade.php ENDPATH**/ ?>