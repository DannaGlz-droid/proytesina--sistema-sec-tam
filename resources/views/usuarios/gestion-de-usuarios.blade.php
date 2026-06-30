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
                        <x-filtros.usuarios :positions="$positions" :districts="$districts" :roles="$roles" />
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

                                <div class="ml-0 sm:ml-auto flex flex-wrap items-center justify-end gap-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                        <select id="dt-per-page" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-24 p-2">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <!-- Botón eliminar seleccionados -->
                                    <div id="bulk-selection-bar" class="hidden items-center gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-[#611132] flex-shrink-0"></span>
                                            <span id="bulk-selected-count" class="text-xs font-lora text-gray-600 whitespace-nowrap"></span>
                                        </div>
                                        <span class="hidden xl:inline text-xs font-lora text-gray-500">En esta pagina</span>
                                    <button id="clear-selected-users" type="button" class="hidden text-xs font-semibold font-lora text-[#611132] hover:underline whitespace-nowrap" title="Quitar selección">
                                        Quitar selección
                                    </button>
                                    <button id="bulk-delete-users" type="button" class="bg-[#AB1A1A] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#8B1515] transition-all duration-300 font-lora items-center gap-2 whitespace-nowrap shadow-sm" title="Eliminar seleccionados" style="display: none;">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Eliminar</span>
                                    </button>
                                    </div>
                                </div>
                            </div>

                            <div id="unused-bulk-selection-bar-bottom" class="hidden mx-4 mb-3 rounded-lg border border-[#ead5dd] bg-[#fbf7f9] px-3 py-2 items-center justify-between gap-3 shadow-sm">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-[#611132] flex-shrink-0"></span>
                                    <span id="unused-bulk-selected-count-bottom" class="text-xs font-lora font-semibold text-[#404041] whitespace-nowrap"></span>
                                    <span class="hidden sm:inline text-xs font-lora text-gray-500">Seleccionados en esta página</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button id="unused-clear-selected-users-bottom" type="button" class="hidden text-xs font-semibold font-lora text-[#611132] hover:underline whitespace-nowrap" title="Quitar selección">
                                        Quitar selección
                                    </button>
                                    <button id="unused-bulk-delete-users-bottom" type="button" class="bg-[#AB1A1A] text-white px-3 py-1.5 rounded-md text-xs font-semibold hover:bg-[#8B1515] transition-all duration-300 font-lora items-center gap-2 whitespace-nowrap shadow-sm" title="Eliminar seleccionados" style="display: none;">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Table wrapper -->
                            <div class="overflow-x-hidden min-w-0">
                        <table id="users-table" class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-2 py-2 font-lora whitespace-nowrap text-xs" data-orderable="false"></th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs"><input id="select-all-users" type="checkbox" /></th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Usuario</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre(s)</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido paterno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Apellido materno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Correo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Teléfono</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Cargo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Distrito</th>
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
        #users-table.dataTable tbody tr:hover { background-color: #eef2f7; }
        #users-table.dataTable tbody tr:nth-child(even) { background-color: #f3f4f6; }
        #users-table.dataTable tbody tr:nth-child(odd) { background-color: white; }
        #users-table.dataTable tbody tr { border-bottom: 1px solid #e5e7eb; }
        #users-table.dataTable thead th { background: #f8fafc; border-bottom: 1px solid #d1d5db; cursor: pointer; }
        #users-table {
            table-layout: fixed;
            width: 100% !important;
        }
        #users-table.dataTable thead th,
        #users-table.dataTable tbody td {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            white-space: normal;
            overflow-wrap: break-word;
            word-break: normal;
            vertical-align: middle;
        }
        #users-table.dataTable thead th {
            padding-top: 0.72rem;
            padding-bottom: 0.72rem;
        }
        #users-table.dataTable tbody td {
            padding-top: 0.58rem;
            padding-bottom: 0.58rem;
            line-height: 1.2;
        }
        #users-table.dataTable tbody td.dt-username-cell {
            overflow-wrap: anywhere;
            word-break: break-word;
            padding-right: 0.9rem;
            color: #111827;
            font-weight: 600;
            line-height: 1.25;
        }
        #users-table.dataTable tbody td.dt-role-cell {
            white-space: nowrap;
            overflow: visible;
        }
        #users-table.dataTable tbody td.dt-phone-cell,
        #users-table.dataTable tbody td.dt-status-cell,
        #users-table.dataTable tbody td.dt-last-session-cell {
            white-space: nowrap;
            overflow-wrap: normal;
        }
        .dt-session-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 1.5rem;
            padding: 0.25rem 0.55rem;
            border-radius: 0.25rem;
            font-size: 0.6875rem;
            line-height: 1;
            white-space: nowrap;
        }
        .dt-session-online {
            border: 1px solid #bbf7d0;
            background: #ecfdf5;
            color: #047857;
            font-weight: 600;
        }
        .dt-session-away {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #475569;
        }
        .dt-session-empty {
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            color: #475569;
        }
        #users-table.dataTable thead th.sorting:after,
        #users-table.dataTable thead th.sorting_asc:after,
        #users-table.dataTable thead th.sorting_desc:after {
            opacity: 0.5;
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        .dt-details-toggle {
            width: 1.3rem;
            height: 1.3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            color: #6b7280;
            transition: background-color .15s ease, color .15s ease, transform .15s ease;
        }
        .dt-details-toggle:hover {
            background: #f3f4f6;
            color: #611132;
        }
        .dt-details-toggle i {
            font-size: 0.7rem;
            transition: transform .15s ease;
        }
        tr.dt-details-open .dt-details-toggle i {
            transform: rotate(90deg);
        }
        #users-table tbody tr.child-row td {
            background: #fbfcfd;
            padding: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .dt-user-details {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.55rem 1rem;
            padding: 0.7rem 1rem 0.85rem 4.35rem;
            font-family: 'Lora', serif;
        }
        .dt-user-details dt {
            font-size: 0.68rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 0.18rem;
        }
        .dt-user-details dd {
            font-size: 0.78rem;
            color: #374151;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }
        @media (max-width: 1280px) {
            .dt-user-details {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.jQuery || !$.fn.DataTable) {
                console.error('jQuery or DataTables not loaded');
                return;
            }

            function notifyUser(message, type = 'success', duration = 3000) {
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type, duration);
                } else {
                    console[type === 'error' ? 'error' : 'log'](message);
                }
            }

            async function confirmUserAction(options) {
                if (typeof window.confirmDialog === 'function') {
                    return window.confirmDialog(options);
                }

                return window.confirm(options.message || 'Confirma la accion para continuar.');
            }

            // We will read current filter form values on each AJAX request
            const filtersForm = document.getElementById('filtersForm');

            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function escapeHtml(value) {
                return $('<div>').text(value == null || value === '' ? '—' : value).html();
            }

            function userDetailItem(label, value, isHtml = false) {
                const content = isHtml ? (value || '—') : escapeHtml(value);
                return `<div><dt>${escapeHtml(label)}</dt><dd>${content}</dd></div>`;
            }

            function formatUserDetails(row) {
                return `
                    <dl class="dt-user-details">
                        ${userDetailItem('Cargo', row.position)}
                        ${userDetailItem('Fecha alta', row.registration_date)}
                        ${userDetailItem('Ultima sesion', row.last_session, true)}
                    </dl>
                `;
            }

            // Initialize DataTables with server-side processing
            const table = $('#users-table').DataTable({
                serverSide: true,
                processing: true,
                scrollX: false,
                autoWidth: false,
                deferredRender: true,
                searching: true,  // Enable DataTables search
                lengthChange: false, // Disable DataTables length (use custom)
                dom: 't', // Only show table
                responsive: false,
                ajax: {
                    url: '{{ route('user.datatable') }}',
                    type: 'POST',
                    data: function(d) {
                        // Build live filters from the form at request time so changes
                        // are always sent to the server (e.g. last_session)
                        const liveFilters = {};
                        if (filtersForm) {
                            const arr = $(filtersForm).serializeArray();
                            arr.forEach(function(item) {
                                // normalize multiple values into arrays
                                if (liveFilters.hasOwnProperty(item.name)) {
                                    if (Array.isArray(liveFilters[item.name])) {
                                        liveFilters[item.name].push(item.value);
                                    } else {
                                        liveFilters[item.name] = [liveFilters[item.name], item.value];
                                    }
                                } else {
                                    liveFilters[item.name] = item.value;
                                }
                            });
                        }

                        return $.extend({}, d, liveFilters);
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        notifyUser('No se pudieron cargar los usuarios. Intenta nuevamente.', 'error');
                    }
                },
                columns: [
                    { data: null, name: 'details', orderable: false, searchable: false, defaultContent: '<button type="button" class="dt-details-toggle" title="Ver detalles" aria-label="Ver detalles"><i class="fas fa-chevron-right"></i></button>' },
                    { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) { return '<input class="row-check-user" data-id="'+data+'" type="checkbox" />'; } },
                    { data: 'id', name: 'id' },
                    { data: 'username', name: 'username' },
                    { data: 'name', name: 'name' },
                    { data: 'first_last_name', name: 'first_last_name' },
                    { data: 'second_last_name', name: 'second_last_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone', orderable: false },
                    { data: 'position', name: 'position_id', orderable: false },
                    { data: 'district', name: 'district_name' },
                    { data: 'registration_date', name: 'registration_date' },
                    { data: 'role', name: 'role_id', orderable: false },
                    { data: 'status', name: 'is_active', orderable: false },
                    { data: 'last_session', name: 'last_session', render: function(data) { return data || '—'; } },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                // Hide the ID column visually (we still include it in data for checkbox rendering)
                columnDefs: [
                    { targets: 0, width: '2rem', className: 'text-center', orderable: false, searchable: false },
                    { targets: 1, width: '2.25rem', className: 'text-center' },
                    { targets: 2, visible: false, searchable: false },
                    { targets: 3, width: '12%', className: 'dt-username-cell' },
                    { targets: 4, width: '10%' },
                    { targets: 5, width: '8.5%' },
                    { targets: 6, width: '8.5%' },
                    { targets: 8, width: '7%', className: 'dt-phone-cell' },
                    { targets: [9, 11, 14], visible: false },
                    { targets: 7, width: '18%' },
                    { targets: 10, width: '13%' },
                    { targets: 12, width: '10%', className: 'dt-role-cell' },
                    { targets: 13, width: '7%', className: 'dt-status-cell' },
                    { targets: 15, width: '6.75rem', className: 'text-right' }
                ],
                pageLength: 25,
                // Default: registration_date desc so newest users appear first.
                // registration_date is column index 11 (details + checkbox + hidden id included).
                order: [[11, 'desc']],
                language: {
                    emptyTable: 'No se encontraron registros coincidentes',
                    loadingRecords: 'Cargando...',
                    processing: 'Procesando...',
                    zeroRecords: 'No se encontraron registros coincidentes'
                },
                drawCallback: function(settings) {
                    updateEmptyState(this.api());
                    updateCustomInfo(this.api());
                    updateCustomPagination(this.api());
                    try {
                        clearVisibleUserSelection();
                    } catch (e) {
                        console.error('clearVisibleUserSelection error', e);
                    }
                }
            });

            // Ensure button state after init
            try { toggleBulkDeleteUsersButton(); } catch (e) {}

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

            // Checkbox selection for users table
            $('#select-all-users').on('change', function() {
                const visibleChecks = $('#users-table tbody .row-check-user');
                const checkedCount = visibleChecks.filter(':checked').length;
                const shouldCheck = checkedCount !== visibleChecks.length;
                visibleChecks.prop('checked', shouldCheck);
                updateUserSelectionState();
            });

            // Prevent row-expansion clicks when interacting with checkboxes or action buttons
            $('#users-table tbody').on('click', 'input.row-check-user, button:not(.dt-details-toggle), a', function(e) {
                e.stopPropagation();
            });

            $('#users-table tbody').on('click', 'button.dt-details-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const tr = $(this).closest('tr');
                const row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('dt-details-open');
                } else {
                    row.child(formatUserDetails(row.data()), 'child-row').show();
                    tr.addClass('dt-details-open');
                }
            });

            $('#users-table tbody').on('change', '.row-check-user', function() {
                updateUserSelectionState();
            });

            $('#clear-selected-users').on('click', function() {
                clearVisibleUserSelection();
            });

            function toggleBulkDeleteUsersButton() {
                updateUserSelectionState();
            }

            function updateUserSelectionState() {
                const visibleChecks = $('#users-table tbody .row-check-user');
                const checkedCount = visibleChecks.filter(':checked').length;
                const totalVisible = visibleChecks.length;
                const hasSelection = checkedCount > 0;
                const allSelected = totalVisible > 0 && checkedCount === totalVisible;
                const selectAll = $('#select-all-users');

                selectAll.prop('checked', allSelected);
                selectAll.prop('indeterminate', hasSelection && !allSelected);

                if (hasSelection) {
                    $('#bulk-selection-bar').removeClass('hidden').addClass('flex');
                    $('#bulk-delete-users').css('display', 'flex');
                    $('#clear-selected-users').removeClass('hidden');
                    $('#bulk-selected-count')
                        .text(checkedCount + ' seleccionado' + (checkedCount === 1 ? '' : 's'));
                } else {
                    $('#bulk-selection-bar').addClass('hidden').removeClass('flex');
                    $('#bulk-delete-users').css('display', 'none');
                    $('#clear-selected-users').addClass('hidden');
                    $('#bulk-selected-count').text('');
                }
            }

            function clearVisibleUserSelection() {
                $('#users-table tbody .row-check-user').prop('checked', false);
                updateUserSelectionState();
            }

            function updateEmptyState(api) {
                const info = api.page.info();
                const tbody = $('#users-table tbody');
                if (info.recordsDisplay === 0) {
                    tbody.html('<tr class="users-empty-row"><td colspan="16" class="text-center py-4 text-gray-500">No se encontraron registros coincidentes</td></tr>');
                }
            }

            $('#bulk-delete-users').on('click', async function() {
                const ids = [];
                $('#users-table tbody .row-check-user:checked').each(function() {
                    const id = $(this).data('id');
                    if (id) ids.push(id);
                });
                if (!ids.length) {
                    notifyUser('Selecciona al menos un usuario.', 'warning');
                    return;
                }

                const confirmed = await confirmUserAction({
                    title: 'Eliminar usuarios',
                    message: 'Se eliminaran ' + ids.length + ' usuario' + (ids.length === 1 ? '' : 's') + '. Esta accion no se puede deshacer.',
                    confirmText: 'Eliminar',
                    cancelText: 'Cancelar',
                    variant: 'danger'
                });

                if (!confirmed) return;

                $.ajax({
                    url: '{{ route('user.massDelete') }}',
                    method: 'POST',
                    data: { ids: ids },
                    success: function(res) {
                        if (res && res.ok) {
                            notifyUser('Usuarios eliminados: ' + (res.deleted || 0), 'success');
                            table.ajax.reload(null, false);
                            clearVisibleUserSelection();
                        } else {
                            notifyUser('No se pudieron eliminar los usuarios.', 'error');
                            console.error(res);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        notifyUser('Error del servidor al eliminar usuarios.', 'error');
                    }
                });
            });

            $('#users-table tbody').on('submit', '.js-delete-user-form', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const form = this;
                const userName = form.dataset.userName || 'este usuario';
                const confirmed = await confirmUserAction({
                    title: 'Eliminar usuario',
                    message: 'Se eliminara ' + userName + '. Esta accion no se puede deshacer.',
                    confirmText: 'Eliminar',
                    cancelText: 'Cancelar',
                    variant: 'danger'
                });

                if (!confirmed) return;

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: $(form).serialize(),
                    success: function(res) {
                        notifyUser((res && res.message) ? res.message : 'Usuario eliminado.', 'success');
                        table.ajax.reload(null, false);
                        clearVisibleUserSelection();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        notifyUser('No se pudo eliminar el usuario.', 'error');
                    }
                });
            });

            // Function to update custom info text
            function updateCustomInfo(api) {
                const info = api.page.info();
                const start = info.recordsDisplay === 0 ? 0 : info.start + 1;
                const end = info.end;
                const filteredTotal = info.recordsDisplay; // number of records after filtering
                const totalAll = info.recordsTotal; // total records without filtering
                let text = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${filteredTotal}</span> entradas`;
                if (filteredTotal !== totalAll) {
                    text += ` <span class="text-sm text-gray-500">(de ${totalAll} totales)</span>`;
                }
                $('#dt-info').html(text);
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

            // When the filters form is submitted, prevent a full page reload and
            // reload the DataTable via AJAX so current filter values are sent.
            $('#filtersForm').on('submit', function(e) {
                e.preventDefault();
                try {
                    table.ajax.reload();
                } catch (err) {
                    // if table isn't available, fallback to full submit
                    this.submit();
                }
            });

            // Handle date range selector visibility
            $('#dateRange').on('change', function() {
                const val = $(this).val();
                const customSelector = $('#customRangeSelector');
                if (val === 'custom') {
                    customSelector.slideDown(200);
                } else {
                    customSelector.slideUp(200);
                }
            });
        });
    </script>
@endsection
