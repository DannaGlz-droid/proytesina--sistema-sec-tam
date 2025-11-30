@extends('layouts.principal')
@section('title', 'Gestión de Usuarios')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Gestión de Usuarios</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Administre y gestione todos los usuarios del sistema con permisos y roles específicos.
                </p>
            </div>
            
            <!-- BOTÓN CREAR USUARIO -->
                <a href="{{ route('user.create') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" title="Crear Usuario">
                <i class="fas fa-plus text-xs"></i>
                Crear Usuario
                </a>
            </div>
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-80 flex-shrink-0">
                        <x-filtros.usuarios :positions="$positions" :jurisdictions="$jurisdictions" :roles="$roles" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <!-- Custom controls wrapper styled like the site -->
                        <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                            <!-- Custom search, per-page and sort controls -->
                            <div class="flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                                <div class="flex-1 min-w-0 sm:w-1/3 lg:w-1/2">
                                    <div class="relative w-full max-w-xl">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400 text-sm"></i>
                                        </div>
                                        <input type="text" id="dt-search-users" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 pr-24 p-2.5" placeholder="Buscar usuarios...">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                                            <button type="button" id="dt-search-btn" class="h-8 px-3 bg-[#611132] text-white rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-150" title="Buscar">
                                                <i class="fas fa-search text-xs"></i>
                                            </button>
                                            <button type="button" id="dt-clear-btn" class="h-8 px-2 bg-white border border-[#404041] text-gray-700 rounded-lg text-xs hover:bg-gray-100 hidden" title="Limpiar búsqueda">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-0 sm:ml-auto flex items-center space-x-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                        <select id="dt-per-page" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-24 p-2">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Table wrapper -->
                            <div class="overflow-x-auto min-w-0">
                        <table id="users-table" class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Usuario</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre(s)</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido P.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido M.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Correo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Teléfono</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Cargo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisdicción</th>
                                    <th scope="col" class="px-3 py-3 font-lora whitespace-nowrap text-xs">Fecha alta</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Rol</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Últ. sesión</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-center" data-orderable="false">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this via AJAX -->
                            </tbody>
                        </table>
                            </div>

                            <!-- Custom pagination styled like the site -->
                            <nav class="flex flex-row flex-wrap items-center justify-between gap-3 p-4 border-t border-[#404041]">
                                <span class="text-sm font-normal text-gray-500 font-lora flex-1 min-w-0" id="dt-info">
                                    Mostrando <span class="font-semibold text-gray-900">0-0</span> de <span class="font-semibold text-gray-900">0</span> entradas
                                </span>
                                <div id="dt-pagination" class="flex-none"></div>
                            </nav>
                        </div>
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
        #users-table.dataTable tbody tr { transition: background-color .15s ease; }
        #users-table.dataTable tbody tr:hover { background-color: #f9fafb; }
        #users-table.dataTable tbody tr:nth-child(even) { background-color: #f9fafb; }
        #users-table.dataTable tbody tr:nth-child(odd) { background-color: white; }
        #users-table.dataTable thead th { background: #f8fafc; border-bottom: 1px solid #d1d5db; cursor: pointer; }
        #users-table.dataTable thead th.sorting:after,
        #users-table.dataTable thead th.sorting_asc:after,
        #users-table.dataTable thead th.sorting_desc:after {
            opacity: 0.5;
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
    </style>

    <script>
        function clearSearch(btn) {
            try {
                const form = btn.closest('form');
                if (!form) return;
                const q = form.querySelector('input[name="q"]');
                if (q) q.value = '';
                form.submit();
            } catch (e) {
                console.error('clearSearch error', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
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
            const table = $('#users-table').DataTable({
                serverSide: true,
                processing: true,
                scrollX: true,
                deferredRender: true,
                searching: true,  // Enable DataTables search
                lengthChange: false, // Disable DataTables length (use custom)
                dom: 't', // Only show table
                ajax: {
                    url: '{{ route('user.datatable') }}',
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
                    { data: 'id', name: 'id' },
                    { data: 'username', name: 'username' },
                    { data: 'name', name: 'name' },
                    { data: 'first_last_name', name: 'first_last_name' },
                    { data: 'second_last_name', name: 'second_last_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'position', name: 'position_id', orderable: false },
                    { data: 'jurisdiction', name: 'jurisdiction_id', orderable: false },
                    { data: 'registration_date', name: 'registration_date' },
                    { data: 'role', name: 'role_id', orderable: false },
                    { data: 'status', name: 'is_active', orderable: false },
                    { data: 'last_session', name: 'last_session' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: 25,
                order: [[0, 'desc']], // Default: ID desc
                language: {
                    emptyTable: 'No hay datos disponibles',
                    loadingRecords: 'Cargando...',
                    processing: 'Procesando...',
                    zeroRecords: 'No se encontraron registros coincidentes'
                },
                drawCallback: function(settings) {
                    updateCustomInfo(this.api());
                    updateCustomPagination(this.api());
                }
            });

            // Custom search functionality
            $('#dt-search-users').on('keyup', function(e) {
                const val = $(this).val();
                if (e.key === 'Enter') {
                    table.search(val).draw();
                    $('#dt-clear-btn').toggleClass('hidden', !val);
                }
            });

            $('#dt-search-btn').on('click', function() {
                const val = $('#dt-search-users').val();
                table.search(val).draw();
                $('#dt-clear-btn').toggleClass('hidden', !val);
            });

            $('#dt-clear-btn').on('click', function() {
                $('#dt-search-users').val('');
                table.search('').draw();
                $(this).addClass('hidden');
            });

            // Custom per-page change
            $('#dt-per-page').on('change', function() {
                table.page.len(parseInt(this.value)).draw();
            });

            // Function to update custom info text
            function updateCustomInfo(api) {
                const info = api.page.info();
                const start = info.recordsDisplay === 0 ? 0 : info.start + 1;
                const end = info.end;
                const total = info.recordsTotal;
                $('#dt-info').html(`Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${total}</span> entradas`);
            }

            // Function to build custom pagination
            function updateCustomPagination(api) {
                const info = api.page.info();
                const current = info.page + 1;
                const pages = info.pages;
                let html = '<ul class="inline-flex items-stretch -space-x-px">';

                // Previous button
                if (current === 1) {
                    html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-left text-xs"></i></span></li>';
                } else {
                    html += `<li><a href="#" data-page="${current - 2}" class="dt-page-link flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-left text-xs"></i></a></li>`;
                }

                // Page numbers
                const maxButtons = 5;
                if (pages <= maxButtons) {
                    for (let i = 1; i <= pages; i++) {
                        if (i === current) {
                            html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                        } else {
                            html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                        }
                    }
                } else {
                    // Complex pagination with ellipsis
                    if (current <= 3) {
                        for (let i = 1; i <= 5; i++) {
                            if (i === current) {
                                html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                            } else {
                                html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                            }
                        }
                        html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                        html += `<li><a href="#" data-page="${pages - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${pages}</a></li>`;
                    } else if (current >= pages - 2) {
                        html += `<li><a href="#" data-page="0" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                        html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                        for (let i = pages - 4; i <= pages; i++) {
                            if (i === current) {
                                html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                            } else {
                                html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                            }
                        }
                    } else {
                        html += `<li><a href="#" data-page="0" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>`;
                        html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                        for (let i = current - 2; i <= current + 2; i++) {
                            if (i === current) {
                                html += `<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">${i}</span></li>`;
                            } else {
                                html += `<li><a href="#" data-page="${i - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${i}</a></li>`;
                            }
                        }
                        html += '<li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>';
                        html += `<li><a href="#" data-page="${pages - 1}" class="dt-page-link flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">${pages}</a></li>`;
                    }
                }

                // Next button
                if (current === pages || pages === 0) {
                    html += '<li><span class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 cursor-default"><i class="fas fa-chevron-right text-xs"></i></span></li>';
                } else {
                    html += `<li><a href="#" data-page="${current}" class="dt-page-link flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><i class="fas fa-chevron-right text-xs"></i></a></li>`;
                }

                html += '</ul>';
                $('#dt-pagination').html(html);

                // Attach click handlers to pagination links
                $('.dt-page-link').on('click', function(e) {
                    e.preventDefault();
                    table.page(parseInt($(this).data('page'))).draw('page');
                });
            }

            // When filters change, reload table with new parameters
            $('form').on('submit', function(e) {
                // Let form submit naturally to refresh filters
            });
        });
    </script>
@endsection