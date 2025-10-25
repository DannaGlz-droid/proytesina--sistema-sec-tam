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

            <a href="{{ route('statistic.create') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-plus text-xs"></i>
                Reg. Def.
            </a>
           
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
                <x-filtros.defunciones :jurisdictions="$jurisdictions" :municipalities="$municipalities" :causes="$causes" />
            </div>

            <!-- Columna Derecha - Tabla -->
            <div class="flex-1">
                <x-table-controls
                    :items="$deaths"
                    :action="route('statistic.data')"
                    :perPageOptions="[25,50,100,250]"
                    :sortOptions="[
                        // Show newest-first id as default, and oldest-first id second for clarity
                        'id_desc' => 'ID: Recientes',
                        'id_asc' => 'ID: Antiguos',
                        'death_date_desc' => 'Fecha Def.: Recientes',
                        'death_date_asc' => 'Fecha Def.: Antiguos',
                        'age_asc' => 'Edad: Menor a mayor',
                        'age_desc' => 'Edad: Mayor a menor',
                        'name_asc' => 'Nombre: A–Z',
                        'name_desc' => 'Nombre: Z–A',
                    ]"
                    :default-sort="'id_desc'">
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
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-right w-24">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($deaths) && $deaths->isNotEmpty())
                                        @foreach($deaths as $death)
                                            <tr class="border-b hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $death->id }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->first_last_name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->second_last_name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->age ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->sex ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ $death->death_date ? $death->death_date->format('d/m/Y') : '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ optional($death->residenceMunicipality)->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ optional($death->deathMunicipality)->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ optional($death->jurisdiction)->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ optional($death->deathLocation)->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap">{{ optional($death->deathCause)->name ?? '—' }}</td>
                                                <td class="px-3 py-3 whitespace-nowrap w-24 text-right">
                                                    <div class="flex items-center justify-end space-x-1">
                                                        <a href="{{ route('statistic.edit', $death->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar defunción {{ $death->id }}">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                        <form method="POST" action="{{ route('statistic.destroy', $death->id) }}" onsubmit="return confirm('¿Eliminar registro? Esta acción no se puede deshacer.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar registro {{ $death->id }}">
                                                                <i class="fas fa-trash text-xs"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="13" class="px-3 py-4 text-center text-sm text-gray-500">No se encontraron registros.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </x-table-controls>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection