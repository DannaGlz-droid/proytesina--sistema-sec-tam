
<?php $__env->startSection('title', 'Datos de Defunciones'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-estadisticas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Datos de Defunciones</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    En esta sección puede cargar archivos (Excel o CSV), aplicar filtros y consultar los registros en la tabla.
                </p>
            </div>

            <a href="<?php echo e(route('statistic.create')); ?>" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-plus text-xs"></i>
                Reg. Def.
            </a>
           
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80 flex-shrink-0">
                <!-- SECCIÓN CARGAR ARCHIVO -->
                <div class="border border-[#404041] rounded-lg p-4 bg-white mb-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
                        <h3 class="font-semibold text-[#404041] text-lg font-lora">Cargar Archivo</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Compact Drag & Drop area (unified style with reportes, simpler) -->
                        <div id="deaths-drop-area" class="border-2 border-dashed border-gray-300 rounded-lg px-4 py-3 text-center cursor-pointer bg-white">
                            <input id="fileInput" type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" />
                            <div class="flex items-center justify-center gap-3">
                                <i class="fas fa-cloud-upload-alt text-lg text-gray-400"></i>
                                <div class="text-left">
                                    <p class="text-sm text-gray-700 font-lora mb-0">Arrastre el archivo aquí o haga clic para seleccionar</p>
                                    <p class="text-xs text-gray-500">Excel / CSV · Máx. 10MB</p>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>

                <!-- COMPONENTE DE FILTROS -->
                <?php if (isset($component)) { $__componentOriginal23105fab6c390d1966189f69b007b578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23105fab6c390d1966189f69b007b578 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.defunciones','data' => ['jurisdictions' => $jurisdictions,'municipalities' => $municipalities,'causes' => $causes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.defunciones'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jurisdictions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jurisdictions),'municipalities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($municipalities),'causes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($causes)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23105fab6c390d1966189f69b007b578)): ?>
<?php $attributes = $__attributesOriginal23105fab6c390d1966189f69b007b578; ?>
<?php unset($__attributesOriginal23105fab6c390d1966189f69b007b578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23105fab6c390d1966189f69b007b578)): ?>
<?php $component = $__componentOriginal23105fab6c390d1966189f69b007b578; ?>
<?php unset($__componentOriginal23105fab6c390d1966189f69b007b578); ?>
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
                                <input type="text" id="dt-search-deaths" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 pr-24 p-2.5" placeholder="Buscar en defunciones...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                                    <button type="button" id="dt-search-deaths-btn" class="h-8 px-3 bg-[#611132] text-white rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-150" title="Buscar">
                                        <i class="fas fa-search text-xs"></i>
                                    </button>
                                    <button type="button" id="dt-clear-deaths-btn" class="h-8 px-2 bg-white border border-[#404041] text-gray-700 rounded-lg text-xs hover:bg-gray-100 hidden" title="Limpiar búsqueda">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="ml-0 sm:ml-auto flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                <select id="dt-per-page-deaths" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-24 p-2">
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table wrapper -->
                    <div class="overflow-x-auto min-w-0">
                    <table id="deaths-table" class="min-w-full w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                            <tr>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Folio</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre(s)</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido P.</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido M.</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Edad</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Sexo</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha def.</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio (res.)</th>
                                <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio (def.)</th>
                                <th scope="col" title="Jurisdicción de residencia" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisdicción (res.)</th>
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

    <style>
        /* Hide ALL DataTables native controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }
        
        /* DataTables + Tailwind table styling */
        #deaths-table.dataTable tbody tr { transition: background-color .15s ease; }
        #deaths-table.dataTable tbody tr:hover { background-color: #f9fafb; }
        #deaths-table.dataTable tbody tr:nth-child(even) { background-color: #f9fafb; }
        #deaths-table.dataTable tbody tr:nth-child(odd) { background-color: white; }
        #deaths-table.dataTable thead th { background: #f8fafc; border-bottom: 1px solid #d1d5db; cursor: pointer; }
        #deaths-table.dataTable thead th.sorting:after,
        #deaths-table.dataTable thead th.sorting_asc:after,
        #deaths-table.dataTable thead th.sorting_desc:after {
            opacity: 0.5;
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // File upload functionality
    var selectBtn = document.getElementById('selectFileBtn');
    var fileInput = document.getElementById('fileInput');
    selectBtn && selectBtn.addEventListener('click', function () { fileInput.click(); });

    // Compact drag & drop handlers for the deaths upload area
    var dropArea = document.getElementById('deaths-drop-area');
    if (dropArea && fileInput) {
        // Click on drop area opens file picker
        dropArea.addEventListener('click', function (e) { fileInput.click(); });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function(e) { e.preventDefault(); e.stopPropagation(); }, false);
        });

        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function() { dropArea.classList.add('bg-gray-50', 'border-[#404041]'); }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, function() { dropArea.classList.remove('bg-gray-50', 'border-[#404041]'); }, false);
        });

        dropArea.addEventListener('drop', function(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            if (!files || files.length === 0) return;
            // Only accept single file
            if (files.length > 1) {
                alert('Solo se permite subir un archivo a la vez.');
                return;
            }
            // Assign files to the hidden input and trigger change
            try {
                fileInput.files = files;
                var evt = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(evt);
            } catch (err) {
                // Some browsers may not allow setting fileInput.files; fallback to alert
                alert('Arrastre detectado. Use el botón para seleccionar el archivo.');
            }
        }, false);
    }

    fileInput && fileInput.addEventListener('change', function (e) {
        var file = e.target.files[0];
        if (!file) return;
        if (file.size > 10 * 1024 * 1024) { alert('El archivo supera el límite de 10MB.'); return; }

        if (!confirm('¿Deseas importar este archivo ahora? Esto procesará y guardará los registros en la base de datos.')) return;

        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '<?php echo e(csrf_token()); ?>');

        var url = '<?php echo e(route("statistic.import")); ?>';
        fetch(url, { method: 'POST', body: fd, headers: {} })
            .then(function (res) {
                return res.text().then(function (text) {
                    try { return JSON.parse(text); } catch (e) { return { ok: false, message: text || 'Respuesta no JSON del servidor' }; }
                });
            })
            .then(function (json) {
                if (!json) { alert('Error inesperado'); return; }
                if (json.ok === false) {
                    var serverMsg = json.message || (json.error_message ? json.error_message : 'Error en el servidor');
                    alert('Importación fallida:\n' + serverMsg + '\n\nRevisa la consola o el log en el servidor para más detalles.');
                    if (json.errors_file) console.info('Archivo de errores:', json.errors_file);
                    return;
                }

                var total = typeof json.total !== 'undefined' ? json.total : 0;
                var imported = typeof json.imported !== 'undefined' ? json.imported : 0;
                var failed = typeof json.failed !== 'undefined' ? json.failed : 0;

                var msg = 'Importación finalizada:\nTotal filas: ' + total + '\nImportadas: ' + imported + '\nFallidas: ' + failed;
                if (json.errors_file) msg += '\nArchivo de errores: ' + json.errors_file;
                alert(msg);
                // Reload DataTables instead of full page reload
                if (window.deathsTable) {
                    window.deathsTable.ajax.reload();
                } else {
                    window.location.reload();
                }
            })
            .catch(function (err) { console.error(err); alert('Error al subir o procesar el archivo. Revisa la consola.'); });
    });

    // Initialize DataTables
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    // Get current URL parameters for filters
    const urlParams = new URLSearchParams(window.location.search);
    const filterData = {};
    for (const [key, value] of urlParams.entries()) {
        filterData[key] = value;
    }

    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTables with server-side processing
    window.deathsTable = $('#deaths-table').DataTable({
        serverSide: true,
        processing: true,
        scrollX: true,
        deferredRender: true,
        searching: true,  // Enable DataTables search
        lengthChange: false, // Disable DataTables length (use custom)
        dom: 't', // Only show table
        ajax: {
            url: '<?php echo e(route('statistic.datatable')); ?>',
            type: 'POST',
            data: function(d) {
                // Include filter parameters from URL/form
                return $.extend({}, d, filterData);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                alert('Error al cargar datos. Por favor, recarga la página.');
            }
        },
        columns: [
            { data: 'gov_folio', name: 'gov_folio' },
            { data: 'name', name: 'name' },
            { data: 'first_last_name', name: 'first_last_name' },
            { data: 'second_last_name', name: 'second_last_name' },
            { data: 'age', name: 'age' },
            { data: 'sex', name: 'sex' },
            { data: 'death_date', name: 'death_date' },
            { data: 'residence_municipality', name: 'residence_municipality_id', orderable: false },
            { data: 'death_municipality', name: 'death_municipality_id', orderable: false },
            { data: 'jurisdiction', name: 'jurisdiction_id', orderable: false },
            { data: 'death_location', name: 'death_location_id', orderable: false },
            { data: 'death_cause', name: 'death_cause_id', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        pageLength: 25,
        order: [[6, 'desc']], // Default: death_date desc
        language: {
            emptyTable: 'No hay datos disponibles',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            zeroRecords: 'No se encontraron registros coincidentes'
        },
        drawCallback: function(settings) {
            updateCustomInfoDeaths(this.api());
            updateCustomPaginationDeaths(this.api());
        }
    });

    // Custom search functionality
    $('#dt-search-deaths').on('keyup', function(e) {
        const val = $(this).val();
        if (e.key === 'Enter') {
            window.deathsTable.search(val).draw();
            $('#dt-clear-deaths-btn').toggleClass('hidden', !val);
        }
    });

    $('#dt-search-deaths-btn').on('click', function() {
        const val = $('#dt-search-deaths').val();
        window.deathsTable.search(val).draw();
        $('#dt-clear-deaths-btn').toggleClass('hidden', !val);
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
        const total = info.recordsTotal;
        $('#dt-info-deaths').html(`Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${total}</span> entradas`);
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

    // When filters change, reload table with new parameters
    $('form').on('submit', function(e) {
        // Let form submit naturally to refresh filters
    });
});
</script>
<?php $__env->stopPush(); ?>

 
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/estadisticas/datos.blade.php ENDPATH**/ ?>