@extends('layouts.principal')
@section('title', 'Datos de Defunciones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Datos de Defunciones</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    En esta sección puede cargar archivos (Excel o CSV), aplicar filtros y consultar los registros en la tabla.
                </p>
            </div>
            
            <!-- BOTÓN CARGAR ARCHIVO -->
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-upload text-xs"></i>
                Cargar Archivo
            </button>
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80">
                <!-- SECCIÓN CARGAR ARCHIVO -->
                <div class="border border-[#404041] rounded-lg p-4 bg-white mb-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
                        <h3 class="font-semibold text-[#404041] text-lg font-lora">Cargar Archivo</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Área de Drag & Drop -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-600 font-lora mb-1">Arrastre un archivo aquí</p>
                            <p class="text-xs text-gray-500 font-lora">(Excel/CSV)</p>
                        </div>
                        
                        <!-- Información de tamaño -->
                        <p class="text-xs text-gray-500 text-center font-lora">Tamaño máximo: 10MB</p>
                        
                        <!-- Botón Seleccionar Archivo -->
                        <button id="selectFileBtn" type="button" class="w-full bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2 shadow-sm" aria-controls="fileInput" aria-label="Seleccionar archivo">
                            <i class="fas fa-folder-open text-xs"></i>
                            Seleccionar archivo
                        </button>
                    </div>
                </div>

                <!-- COMPONENTE DE FILTROS -->
                <x-filtros.defunciones />
            </div>

            <!-- Columna Derecha - Tabla -->
            <div class="flex-1">
                <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                    
                    <!-- BARRA SUPERIOR: Búsqueda y Controles -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                        
                        <!-- Búsqueda -->
                        <div class="w-full md:w-1/2">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="table-search-deaths" 
                                       class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 p-2.5" 
                                       placeholder="Buscar por ID, nombre, municipio, causa...">
                            </div>
                        </div>
                        
                        <!-- Controles Derecha -->
                        <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                            
                            <!-- Selector de entradas -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                <select id="entries-per-page" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-16 p-2">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-700 font-lora">entradas</span>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Paterno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Materno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Edad</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Sexo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha Def.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio Res.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio Def.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisd.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Lugar</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Causa</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Fila 1 -->
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">D-001</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Juan</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Pérez</td>
                                    <td class="px-3 py-3 whitespace-nowrap">García</td>
                                    <td class="px-3 py-3 whitespace-nowrap">72</td>
                                    <td class="px-3 py-3 whitespace-nowrap">M</td>
                                    <td class="px-3 py-3 whitespace-nowrap">15/03/2024</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio A</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio X</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Jurisd. I</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Hospital Central</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Cardiopatía</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-end space-x-1">
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Fila 2 -->
                                <tr class="border-b hover:bg-gray-50 bg-gray-50">
                                    <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">D-002</td>
                                    <td class="px-3 py-3 whitespace-nowrap">María</td>
                                    <td class="px-3 py-3 whitespace-nowrap">López</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Hernández</td>
                                    <td class="px-3 py-3 whitespace-nowrap">65</td>
                                    <td class="px-3 py-3 whitespace-nowrap">F</td>
                                    <td class="px-3 py-3 whitespace-nowrap">22/04/2024</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio B</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio Y</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Jurisd. II</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Domicilio</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">Cáncer</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-end space-x-1">
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Fila 3 -->
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">D-003</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Carlos</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Martínez</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rodríguez</td>
                                    <td class="px-3 py-3 whitespace-nowrap">48</td>
                                    <td class="px-3 py-3 whitespace-nowrap">M</td>
                                    <td class="px-3 py-3 whitespace-nowrap">10/05/2024</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio C</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio Z</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Jurisd. III</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Accidente</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full">Accidente</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-end space-x-1">
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Fila 4 -->
                                <tr class="border-b hover:bg-gray-50 bg-gray-50">
                                    <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">D-004</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Ana</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Sánchez</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Gómez</td>
                                    <td class="px-3 py-3 whitespace-nowrap">81</td>
                                    <td class="px-3 py-3 whitespace-nowrap">F</td>
                                    <td class="px-3 py-3 whitespace-nowrap">18/06/2024</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio A</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Municipio W</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Jurisd. I</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Hospital Regional</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">COVID-19</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-end space-x-1">
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINACIÓN -->
                    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-t border-[#404041]">
                        <span class="text-sm font-normal text-gray-500 font-lora">
                            Mostrando 
                            <span class="font-semibold text-gray-900">1-4</span>
                            de
                            <span class="font-semibold text-gray-900">24</span>
                            entradas
                        </span>
                        <ul class="inline-flex items-stretch -space-x-px">
                            <li>
                                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">2</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">3</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">4</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">5</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection