@extends('layouts.principal')
@section('title', 'Gestión de Usuarios')
@section('content')

    @include('components.header')
    @include('components.nav')

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
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-plus text-xs"></i>
                Crear Usuario
            </button>
        </div>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <div class="p-4 lg:p-6">
                <!-- Layout principal: Filtros + Tabla -->
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- Columna Izquierda - Filtros -->
                    <div class="lg:w-80">
                        <x-filtros.usuarios />
                    </div>

                    <!-- Columna Derecha - Tabla -->
                    <div class="lg:flex-1">
                        <!-- BARRA SUPERIOR DE TABLA: Mantenemos tu diseño personalizado -->
                        <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 items-start lg:items-center mb-4">
                            <!-- Búsqueda y Mostrar entradas - Tu diseño personalizado -->
                            <div class="flex flex-col gap-2 min-w-0 lg:max-w-[350px]">
                                <!-- Búsqueda personalizada -->
                                <div>
                                    <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Buscar:</label>
                                    <div class="relative">
                                        <input type="text" id="customSearch" placeholder="Titulo, usuario..." 
                                               class="w-full border border-[#404041] rounded-lg px-3 py-1.5 pl-9 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                                    </div>
                                </div>

                                <!-- Mostrar entradas - Tu diseño personalizado -->
                                <div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-[#404041] font-lora whitespace-nowrap">Mostrar</span>
                                        <select id="customPageLength" class="w-10 border border-[#404041] rounded-lg px-1 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent bg-white">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <span class="text-xs text-gray-600 font-lora whitespace-nowrap">entradas</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contador de resultados - Tu diseño personalizado -->
                            <div class="text-xs text-gray-600 font-lora ml-auto mt-8">
                                <span id="customCounter" class="font-semibold text-[#404041]">0</span> usuarios encontrados
                            </div>
                        </div>

                        <!-- TABLA CON DATATABLES - Mantenemos tus estilos -->
                        <div class="border border-[#404041] rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table id="usersTable" class="w-full display">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-[#404041]">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Usuario</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Nombre</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Apellidos</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Correo Electrónico</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Teléfono</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Cargo</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Jurisdicción</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Fecha Alta</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Rol</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Última Sesión</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#404041] font-lora whitespace-nowrap">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables llenará esto automáticamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- DataTables proveerá su propia paginación, pero la ocultamos -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME PARA LOS ÍCONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DATATABLES CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- JQUERY Y DATATABLES JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Inicializar DataTables
        var table = $('#usersTable').DataTable({
            "processing": true,
            "serverSide": false, // Cambiar a true si usas backend
            "ajax": {
                "url": "/api/usuarios", // Tu endpoint aquí
                "type": "GET",
                "dataSrc": "" // Ajustar según la estructura de tu API
            },
            "columns": [
                { "data": "id" },
                { "data": "usuario" },
                { "data": "nombre" },
                { "data": "apellidos" },
                { "data": "email" },
                { "data": "telefono" },
                { "data": "cargo" },
                { "data": "jurisdiccion" },
                { "data": "fecha_alta" },
                { 
                    "data": "rol",
                    "render": function(data, type, row) {
                        var badgeClass = '';
                        switch(data) {
                            case 'Administrador':
                                badgeClass = 'bg-green-100 text-green-800';
                                break;
                            case 'Editor':
                                badgeClass = 'bg-blue-100 text-blue-800';
                                break;
                            case 'Supervisor':
                                badgeClass = 'bg-purple-100 text-purple-800';
                                break;
                            default:
                                badgeClass = 'bg-gray-100 text-gray-800';
                        }
                        return '<span class="inline-block '+badgeClass+' px-2 py-1 rounded-full text-xs font-semibold">'+data+'</span>';
                    }
                },
                { "data": "ultima_sesion" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex gap-1">
                                <button class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="w-7 h-7 flex items-center justify-center rounded border border-[#C08400] text-[#C08400] hover:bg-[#C08400] hover:text-white transition-all duration-200" title="Cambiar Contraseña">
                                    <i class="fas fa-key text-xs"></i>
                                </button>
                                <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false,
                    "searchable": false
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "dom": '<"top"f>rt<"bottom"lip><"clear">',
            "pageLength": 10,
            "drawCallback": function(settings) {
                // Actualizar contador personalizado
                var api = this.api();
                var total = api.rows({search:'applied'}).count();
                $('#customCounter').text(total);
            },
            "initComplete": function(settings, json) {
                // Inicializar contador
                var api = this.api();
                var total = api.rows({search:'applied'}).count();
                $('#customCounter').text(total);
            }
        });

        // Conectar búsqueda personalizada con DataTables
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Conectar selector de entradas personalizado con DataTables
        $('#customPageLength').on('change', function() {
            table.page.len(parseInt(this.value)).draw();
        });

        // Datos de ejemplo (remover cuando tengas tu API)
        // Solo para demostración - remover en producción
        setTimeout(function() {
            var sampleData = [
                {
                    "id": "001",
                    "usuario": "mgonzalez",
                    "nombre": "María",
                    "apellidos": "González López",
                    "email": "maria.gonzalez@ejemplo.com",
                    "telefono": "812-345-6789",
                    "cargo": "Coordinador",
                    "jurisdiccion": "Jurisdicción I",
                    "fecha_alta": "15/03/2023",
                    "rol": "Administrador",
                    "ultima_sesion": "Hace 2 horas"
                },
                {
                    "id": "002",
                    "usuario": "crodriguez",
                    "nombre": "Carlos",
                    "apellidos": "Rodríguez Martínez",
                    "email": "carlos.rodriguez@ejemplo.com",
                    "telefono": "812-456-7890",
                    "cargo": "Analista",
                    "jurisdiccion": "Jurisdicción II",
                    "fecha_alta": "22/04/2023",
                    "rol": "Editor",
                    "ultima_sesion": "Hace 1 día"
                },
                {
                    "id": "003",
                    "usuario": "amartinez",
                    "nombre": "Ana",
                    "apellidos": "Martínez Sánchez",
                    "email": "ana.martinez@ejemplo.com",
                    "telefono": "812-567-8901",
                    "cargo": "Supervisor",
                    "jurisdiccion": "Jurisdicción III",
                    "fecha_alta": "10/05/2023",
                    "rol": "Supervisor",
                    "ultima_sesion": "Hace 3 días"
                },
                {
                    "id": "004",
                    "usuario": "rsanchez",
                    "nombre": "Roberto",
                    "apellidos": "Sánchez Jiménez",
                    "email": "roberto.sanchez@ejemplo.com",
                    "telefono": "812-678-9012",
                    "cargo": "Técnico",
                    "jurisdiccion": "Jurisdicción I",
                    "fecha_alta": "18/06/2023",
                    "rol": "Usuario",
                    "ultima_sesion": "Hace 1 semana"
                }
            ];
            
            table.clear().rows.add(sampleData).draw();
        }, 500);
    });
    </script>

    <style>
    /* Personalizar DataTables para que coincida con tu diseño */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #404041 !important;
        background: white !important;
        color: #404041 !important;
        margin-left: 2px !important;
        border-radius: 0.5rem !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #404041 !important;
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #404041 !important;
        color: white !important;
    }
    
    /* Ocultar elementos nativos de DataTables que no necesitamos */
    .dataTables_filter, .dataTables_length, .dataTables_info {
        display: none !important;
    }
    </style>
@endsection