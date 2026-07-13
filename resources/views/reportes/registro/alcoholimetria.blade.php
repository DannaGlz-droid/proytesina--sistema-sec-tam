@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría')
@section('content')

    <style>
        .description-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .description-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .description-scroll::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        .description-scroll::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
        .description-scroll {
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
        }

        /* Tom Select styling to match form inputs */
        .ts-wrapper { border: none !important; padding: 0 !important; background: transparent !important; }
        select.tomselect-select {
            position: absolute !important; left: -9999px !important; width: 1px !important;
            height: 1px !important; overflow: hidden !important; opacity: 0 !important;
            pointer-events: none !important; border: 0 !important; margin: 0 !important;
            padding: 0 !important; background: transparent !important;
        }
        .ts-wrapper { display: block; width: 100%; }
        .ts-control {
            border: 1px solid #d1d5db !important; border-radius: 0.5rem !important;
            padding: 8px 12px !important; background: #ffffff !important;
            font-size: 0.875rem; line-height: 1.25rem !important;
            box-shadow: none !important; height: auto !important; min-height: 36px !important;
        }
        .ts-control .item, .ts-control input { padding: 0 !important; margin: 0 !important; }
        .ts-dropdown { border: 1px solid #d1d5db; border-radius: 0.5rem; max-height: 240px; }
        .ts-dropdown .ts-option { padding: 0.5rem 0.75rem; }
        .ts-control::after {
            content: ""; position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%); width: 18px; height: 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6'><polyline points='6 9 12 15 18 9'/></svg>");
            background-size: 12px 12px; pointer-events: none;
        }
        input[type="date"] {
            padding: 8px 12px !important; border: 1px solid #d1d5db !important;
        }
    </style>

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">
            {{ isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría' }}
        </h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">{{ isset($publication) ? 'Actualiza la información y administra los archivos adjuntos de este reporte.' : 'Captura la información y adjunta los archivos requeridos para enviar este reporte.' }}</p>

        <!-- Mensajes de error -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm text-red-800 font-lora font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @php
            $validationMessages = collect($errors->getMessages());
            $generalErrors = $validationMessages->reject(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
        @endphp
        @if($generalErrors->isNotEmpty())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-red-800 font-lora font-semibold mb-2">Errores de validación:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 font-lora space-y-1">
                            @foreach($generalErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Cuadro del formulario responsive -->
        <form id="alcoholimetriaForm" action="{{ isset($publication) ? route('reportes.alcoholimetria.update', $publication) : route('reportes.alcoholimetria.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($publication))
                @method('PUT')
            @endif
            
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <!-- Sección 1: Información general -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="document-text-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información general</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" 
                                   name="tema"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Operativo de alcoholimetría"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required minlength="3" maxlength="150">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio <span class="text-red-600">*</span></label>
                            <select id="alcohol_municipality_select" name="municipio" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" required>
                                <option value="">Seleccione un municipio</option>
                                @php
                                    $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : '');
                                @endphp
                                @if($selectedMunicipio)
                                    @php $m = $municipalities->firstWhere('id', $selectedMunicipio) @endphp
                                    @if($m)
                                        <option value="{{ $m->id }}" selected>{{ $m->name }}</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" 
                                   name="fecha"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required max="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-500 mb-1 font-lora">Distrito</label>
                            @php
                                $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : '');
                            @endphp
                            @if($isAdminOrCoordinator ?? false)
                                <!-- Para Admin/Coordinador: Tom Select editable de distritos -->
                                <select id="jurisdiction_select_alcohol" name="jurisdiccion" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" placeholder="Seleccione un distrito" required>
                                    <option value="">Seleccione un distrito</option>
                                    @if($selectedDistrito)
                                        @php $selectedDistrictModel = $districts->firstWhere('id', $selectedDistrito) @endphp
                                        @if($selectedDistrictModel)
                                            <option value="{{ $selectedDistrictModel->id }}" selected>{{ $selectedDistrictModel->name }}</option>
                                        @endif
                                    @endif
                                </select>
                            @else
                                <!-- Para Operadores: campo readonly con distrito pre-asignado -->
                                <input type="hidden" id="jurisdiction_input_alcohol" name="jurisdiccion" value="{{ $selectedDistrito }}" required>
                                <input id="jurisdiction_display_alcohol" type="text" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-600 focus:ring-2 focus:ring-gray-300 focus:border-transparent transition-all duration-200 font-lora" value="{{ isset($report) && $report->district ? $report->district->name : 'Pendiente (seleccione municipio)' }}" readonly>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Operativos de alcoholimetría -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="shield-checkmark-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Operativos de alcoholimetría</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Puntos de revisión instalados <span class="text-red-600">*</span></label>
                            <input type="number"
                                   name="puntos_revision"
                                   min="0" max="9999"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: 15"
                                   value="{{ old('puntos_revision', isset($report) ? $report->checkpoints : '') }}"
                                   required>
                        </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Resultados por punto de revisión -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="stats-chart-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Resultados por punto de revisión</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Pruebas realizadas <span class="text-red-600">*</span></label>
                            <input type="number"
                                   name="pruebas_realizadas"
                                   min="0" max="999999"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: 250"
                                   value="{{ old('pruebas_realizadas', isset($report) ? $report->tests_performed : '') }}"
                                   required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Conductores no aptos <span class="text-red-600">*</span></label>
                            <input type="number"
                                   name="conductores_no_aptos"
                                   min="0" max="999999"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: 12"
                                   value="{{ old('conductores_no_aptos', isset($report) ? $report->drivers_not_fit : '') }}"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 4: Conductores no aptos por género -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="people-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Conductores no aptos por género</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Mujeres <span class="text-red-600">*</span></label>
                            <input type="number"
                                   name="mujeres_no_aptas"
                                   min="0" max="999999"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: 3"
                                   value="{{ old('mujeres_no_aptas', isset($report) ? $report->women : '') }}"
                                   required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Hombres <span class="text-red-600">*</span></label>
                            <input type="number"
                                   name="hombres_no_aptos"
                                   min="0" max="999999"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: 9"
                                   value="{{ old('hombres_no_aptos', isset($report) ? $report->men : '') }}"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 5: Conductores no aptos por tipo de vehículo -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="car-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Conductores no aptos por tipo de vehículo</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Automóviles y camionetas <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="automoviles_camionetas"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 8"
                                value="{{ old('automoviles_camionetas', isset($report) ? $report->cars_trucks : '') }}"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Motocicletas <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="motocicletas"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 2"
                                value="{{ old('motocicletas', isset($report) ? $report->motorcycles : '') }}"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Transporte público colectivo <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="transporte_colectivo"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 1"
                                value="{{ old('transporte_colectivo', isset($report) ? $report->public_transport_collective : '') }}"
                                required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Transporte público individual <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="transporte_individual"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 0"
                                value="{{ old('transporte_individual', isset($report) ? $report->public_transport_individual : '') }}"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Transporte de carga <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="transporte_carga"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 1"
                                value="{{ old('transporte_carga', isset($report) ? $report->cargo_transport : '') }}"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Vehículos de emergencia <span class="text-red-600">*</span></label>
                            <input type="number" 
                                name="vehiculos_emergencia"
                                min="0" max="999999"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 0"
                                value="{{ old('vehiculos_emergencia', isset($report) ? $report->emergency_vehicles : '') }}"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 6: Descripción -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="clipboard-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Descripción</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción</label>
                    <textarea id="descripcion" name="descripcion" maxlength="5000" class="w-full px-3 py-2 text-xs lg:text-sm border border-[#404041] rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora break-words whitespace-normal description-scroll" rows="4" placeholder="Agregue contexto, resultados u observaciones relevantes">{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 6: Carga de archivos -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="cloud-upload-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Carga de archivos</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-4">
                    <!-- Archivos existentes (solo en modo edición) -->
                    @if(isset($publication) && $publication->files->count() > 0)
                        <div class="mb-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                    <p class="font-medium font-lora text-sm text-[#404041] flex items-center">
                                        <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                        Archivos actuales ({{ $publication->files->count() }})
                                    </p>
                                    <button id="select-all-existing-files" type="button" class="text-sm text-[#611132] font-lora font-semibold underline-offset-4 hover:underline focus:outline-none focus:underline" onclick="toggleAllExistingFiles()">
                                        Seleccionar todos
                                    </button>
                                </div>
                                @php
                                    // Get file requirements and count files by type
                                    $requirements = \App\Config\ReportFileRequirements::getRequirements('alcoholimetria');
                                    $filesByType = [
                                        'excel' => $publication->files->filter(fn($f) => in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['xlsx', 'xls'])),
                                        'photos' => $publication->files->filter(fn($f) => in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])),
                                    ];
                                @endphp
                                <ul class="space-y-2" id="existing-files-list">
                                    @foreach($publication->files as $file)
                                        @php
                                            $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                            $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);

                                            $iconConfig = match($extension) {
                                                'xlsx', 'xls' => ['icon' => 'document-outline', 'color' => 'text-white', 'bg' => 'bg-green-500'],
                                                'jpg', 'jpeg', 'png' => ['icon' => 'image-outline', 'color' => 'text-white', 'bg' => 'bg-purple-500'],
                                                default => ['icon' => 'document-outline', 'color' => 'text-white', 'bg' => 'bg-gray-500']
                                            };
                                        @endphp
                                        <li class="flex items-center justify-between py-2 px-3 rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm file-item" 
                                            data-file-id="{{ $file->id }}" 
                                            data-file-type="{{ $fileType }}">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <input type="checkbox" class="file-delete-checkbox mr-2 w-4 h-4 cursor-pointer border border-gray-300 rounded accent-[#611132] focus:ring-2 focus:ring-[#611132] focus:ring-offset-1" onchange="toggleFileStrikethrough(this)">
                                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg {{ $iconConfig['bg'] }}">
                                                    <ion-icon name="{{ $iconConfig['icon'] }}" class="{{ $iconConfig['color'] }} text-xl"></ion-icon>
                                                </div>
                                                <div class="ml-3 flex-1 min-w-0 file-info">
                                                    <p class="text-sm font-medium text-[#404041] truncate">{{ $file->original_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-sm text-gray-600 font-lora mt-6 flex items-center">
                                    <ion-icon name="checkbox-outline" class="mr-2 text-sm"></ion-icon>
                                    Marca los archivos que deseas eliminar/reemplazar
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dos cuadros en una fila horizontal -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- (1) Documento Excel -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Hoja de Cálculo</span>
                                </div>
                                <span id="excel-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: XLSX (obligatorio)</p>
                        </div>

                        <!-- (2) Fotografía -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="image-outline" class="text-purple-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Fotografía</span>
                                </div>
                                <span id="photo-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formatos: JPG, JPEG, PNG (1 foto obligatoria)</p>
                        </div>
                    </div>

                    <!-- Área de carga de archivos (múltiples) -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2 font-lora">
                            @if(isset($publication))
                                Agregar nuevos archivos (opcional)
                            @else
                                Subir archivos (selección múltiple) <span class="text-red-600">*</span>
                            @endif
                        </label>
                        
                        <!-- Cuadro punteado para arrastrar y soltar -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#404041] transition-colors duration-200 bg-gray-50">
                            <input type="file" 
                                   id="file-input"
                                   name="archivos[]"
                                   class="hidden"
                                   accept=".xlsx,.xls,.jpg,.jpeg,.png"
                                   multiple
                                   onchange="addFiles(this.files)">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1 font-lora">
                                    Haga clic o arrastre archivos aquí para subirlos
                                </p>
                                <p class="text-xs text-gray-500 font-lora">
                                    Formatos permitidos: XLSX, XLS, JPG, JPEG, PNG<br>
                                    <span class="text-xs text-gray-500">Tamaño máximo por archivo: 10 MB</span>
                                </p>
                                <p class="text-xs text-blue-600 font-lora mt-2">
                                    Puede seleccionar múltiples archivos a la vez
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información de archivos seleccionados -->
                        <div id="file-error" class="mt-3 hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 font-lora"></div>

                        @if($fileErrors->isNotEmpty())
                            <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 font-lora space-y-1">
                                @foreach($fileErrors as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div id="file-list" class="mt-4 hidden">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium mb-3 font-lora text-sm text-[#404041] flex items-center">
                                    <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                    Archivos agregados
                                </p>
                                <ul id="file-names" class="space-y-2"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES -->
            @if(isset($publication) || request()->is('reportes/*/*/edit'))
                <x-form-buttons
                    primaryText="Actualizar registro"
                    secondaryText=""
                    tertiaryText="Volver al listado"
                    tertiaryHref="{{ route('reportes.index', ['tipo' => request('redirect_tipo', 'alcoholimetria')]) }}"
                    primaryType="submit"
                />
            @else
                <x-form-buttons 
                    primaryText="Guardar registro"
                    secondaryText="Limpiar formulario"
                    primaryType="submit"
                    secondaryType="button"
                    secondaryOnclick="clearAlcoholimetriaForm()"
                    tertiaryText="Volver al listado"
                    tertiaryHref="{{ route('reportes.index', ['tipo' => request('redirect_tipo', 'alcoholimetria')]) }}"
                />
            @endif

            <!-- Input oculto para archivos a eliminar -->
            @if(isset($publication))
                <input type="hidden" id="files-to-delete" name="files_to_delete" value="">
            @endif
            <!-- Input oculto para redirect_tipo -->
            <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'alcoholimetria') }}">
        </div>
        </form>

    {{-- Formularios ocultos para eliminar archivos (renderizados fuera del form principal para evitar MethodOverride en el PUT) --}}
    @if(isset($publication) && $publication->files->count() > 0)
        @foreach($publication->files as $file)
            <form id="delete-file-{{ $file->id }}" method="POST" action="{{ route('reportes.file.delete', $file) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

    </div>

    <!-- Script para manejo de municipios y distritos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map municipality_id -> district_id
            const muniToJur = @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->district_id]; }));
            const jurisNames = @json($districts->mapWithKeys(function($j){ return [$j->id => $j->name]; }));
            const districtOptions = @json($districts->map(function($j){ return ['id' => (string) $j->id, 'name' => $j->name]; })->values());
            // Jurisdicción del usuario (puede ser null)
            const currentJurisdiction = @json(optional(auth()->user())->district_id);
            const isAdminOrCoordinator = @json($isAdminOrCoordinator ?? false);
            const initialDistrict = @json(old('jurisdiccion', isset($report) ? $report->district_id : ''));

            const alcoholMuni = document.getElementById('alcohol_municipality_select');
            const jurisdictionSelect = document.getElementById('jurisdiction_select_alcohol');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display_alcohol');
            const hiddenJur = document.getElementById('jurisdiction_input_alcohol');
            let syncingDistrictFromMunicipality = false;

            function getTomSelectWrapper(select) {
                return select?.tomselect?.wrapper || select?.nextElementSibling || null;
            }

            function getTomSelectValidityInput(select) {
                return select?.tomselect?.control_input || select?.tomselect?.input || select || null;
            }

            function showTomSelectError(select, message) {
                if (!select) return;
                const validityInput = getTomSelectValidityInput(select);
                if (validityInput?.setCustomValidity) {
                    validityInput.setCustomValidity(message);
                }
            }

            function clearTomSelectError(select) {
                if (!select) return;
                const validityInput = getTomSelectValidityInput(select);
                if (validityInput?.setCustomValidity) {
                    validityInput.setCustomValidity('');
                }
            }

            function focusTomSelect(select) {
                if (select?.tomselect) {
                    select.tomselect.focus();
                } else {
                    select?.focus();
                }
            }

            function reportTomSelectValidity(select) {
                const validityInput = getTomSelectValidityInput(select);
                focusTomSelect(select);
                if (validityInput?.reportValidity) {
                    validityInput.reportValidity();
                } else if (select?.reportValidity) {
                    select.reportValidity();
                }
            }

            function validateRequiredTomSelect(select, message) {
                if (!select || !select.hasAttribute('required') || select.value) {
                    if (select) clearTomSelectError(select);
                    return true;
                }
                showTomSelectError(select, message);
                return false;
            }

            function validateAlcoholTomSelects(focusFirst = false) {
                const fields = [
                    { select: alcoholMuni, message: 'Seleccione un municipio.' },
                    { select: isAdminOrCoordinator ? jurisdictionSelect : null, message: 'Seleccione un distrito.' },
                ];
                let firstInvalid = null;

                fields.forEach(({ select, message }) => {
                    if (!validateRequiredTomSelect(select, message) && !firstInvalid) {
                        firstInvalid = select;
                    }
                });

                if (focusFirst && firstInvalid) {
                    reportTomSelectValidity(firstInvalid);
                }

                return !firstInvalid;
            }

            window.resetAlcoholimetriaTomSelects = function() {
                clearTomSelectError(alcoholMuni);
                clearTomSelectError(jurisdictionSelect);

                if (isAdminOrCoordinator && jurisdictionSelect?.tomselect) {
                    syncingDistrictFromMunicipality = true;
                    jurisdictionSelect.tomselect.clear(true);
                    syncingDistrictFromMunicipality = false;
                } else if (jurisdictionSelect) {
                    jurisdictionSelect.value = '';
                }

                if (alcoholMuni?.tomselect) {
                    alcoholMuni.tomselect.clear(true);
                    alcoholMuni.tomselect.clearOptions();
                    if (typeof alcoholMuni.tomselect.clearCache === 'function') {
                        alcoholMuni.tomselect.clearCache();
                    }
                    alcoholMuni.tomselect.refreshOptions(false);
                } else if (alcoholMuni) {
                    alcoholMuni.value = '';
                }

                if (!isAdminOrCoordinator && currentJurisdiction) {
                    if (hiddenJur) hiddenJur.value = currentJurisdiction;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[currentJurisdiction] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            };

            function setJurisdictionBasedOnMunicipality() {
                const mid = alcoholMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (isAdminOrCoordinator && jurisdictionSelect) {
                        // Para admin/coordinador: actualizar el select del distrito en modo silencioso
                        // para no disparar onChange del distrito que limpia el municipio (bucle circular)
                        syncingDistrictFromMunicipality = true;
                        if (jurisdictionSelect.tomselect) {
                            jurisdictionSelect.tomselect.setValue(String(jid), true);
                        } else {
                            jurisdictionSelect.value = jid;
                        }
                        clearTomSelectError(jurisdictionSelect);
                        syncingDistrictFromMunicipality = false;
                        loadMunicipalityOptions(false);
                    } else {
                        // Para operadores: actualizar el campo hidden y display
                        if (hiddenJur) hiddenJur.value = jid;
                        if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[jid] || '';
                    }
                } else {
                    if (isAdminOrCoordinator && jurisdictionSelect) {
                        return;
                    } else {
                        if (hiddenJur) hiddenJur.value = '';
                        if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                    }
                }
            }

            // Para Admin/Coordinador: inicializar Tom Select para Distrito
            if (isAdminOrCoordinator && jurisdictionSelect) {
                const districtTs = new TomSelect(jurisdictionSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 20,
                    maxItems: 1,
                    create: false,
                    options: districtOptions,
                    onChange: function(value) {
                        if (syncingDistrictFromMunicipality) {
                            return;
                        }
                        clearTomSelectError(jurisdictionSelect);
                        // Limpiar el municipio cuando cambia el distrito
                        if (alcoholMuni && alcoholMuni.tomselect) {
                            alcoholMuni.tomselect.setValue('');
                            loadMunicipalityOptions(false);
                        }
                    }
                });
                try { jurisdictionSelect.style.display = 'none'; } catch (e) {}
                
                // Si hay un valor pre-seleccionado, cargar esa opción
                if (initialDistrict) {
                    const initialDistrictName = jurisNames[initialDistrict];
                    if (initialDistrictName) {
                        districtTs.addOption({ id: String(initialDistrict), name: initialDistrictName });
                    }
                    districtTs.setValue(String(initialDistrict), true);
                }
            }

            // Initialize Tom Select for municipality
            function fetchMunicipalities(q) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(q) + '&limit=100';
                if (!isAdminOrCoordinator && currentJurisdiction) {
                    // Para operadores: filtrar por su distrito
                    url += '&district_id=' + encodeURIComponent(currentJurisdiction);
                } else if (isAdminOrCoordinator && jurisdictionSelect) {
                    // Para admin/coordinador: filtrar por el distrito seleccionado
                    const selectedDistrict = jurisdictionSelect.value;
                    if (selectedDistrict) {
                        url += '&district_id=' + encodeURIComponent(selectedDistrict);
                    }
                }
                return fetch(url).then(r => r.json());
            }

            function loadMunicipalityOptions(openDropdown = false) {
                if (!alcoholMuni?.tomselect) return;

                const tomSelect = alcoholMuni.tomselect;
                fetchMunicipalities('').then(items => {
                    tomSelect.clearOptions();
                    items.forEach(item => tomSelect.addOption(item));
                    tomSelect.refreshOptions(openDropdown);
                    if (openDropdown) tomSelect.open();
                }).catch(() => {});
            }

            if (alcoholMuni) {
                const ts = new TomSelect(alcoholMuni, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 100,
                    maxItems: 1,
                    create: false,
                    preload: true,
                    load: function(query, callback) {
                        fetchMunicipalities(query).then(items => callback(items)).catch(() => callback());
                    },
                    onDropdownOpen: function() {
                        loadMunicipalityOptions(true);
                    },
                    onChange: function(value) {
                        alcoholMuni.value = value;
                        clearTomSelectError(alcoholMuni);
                        const evt = new Event('change', { bubbles: true });
                        alcoholMuni.dispatchEvent(evt);
                    }
                });
                alcoholMuni.classList.remove('tomselect-select');
                try { alcoholMuni.style.display = 'none'; } catch (e) {}
            }

            if (alcoholMuni) {
                alcoholMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }

            // Para Admin/Coordinador: cuando cambia el distrito, refrescar la lista de municipios
            if (isAdminOrCoordinator && jurisdictionSelect) {
                jurisdictionSelect.addEventListener('change', function() {
                    // Limpiar la selección de municipio cuando cambia el distrito
                    if (alcoholMuni && alcoholMuni.tomselect) {
                        alcoholMuni.tomselect.clear(true);
                        alcoholMuni.value = '';
                        alcoholMuni.tomselect.setTextboxValue('');
                        alcoholMuni.tomselect.clearOptions();
                        if (typeof alcoholMuni.tomselect.clearCache === 'function') {
                            alcoholMuni.tomselect.clearCache();
                        }
                        loadMunicipalityOptions(false);
                    }
                });
            }

            // Si el usuario tiene una jurisdicción asignada y NO es admin/coordinador, establecerla en el campo oculto y en el display
            if (!isAdminOrCoordinator && currentJurisdiction) {
                if (hiddenJur && !hiddenJur.value) hiddenJur.value = currentJurisdiction;
                if (jurisdictionDisplay && (!jurisdictionDisplay.value || jurisdictionDisplay.value.includes('Pendiente'))) {
                    jurisdictionDisplay.value = jurisNames[currentJurisdiction] || jurisdictionDisplay.value;
                }
            }

            // Interceptar el envío del formulario para actualizar files_to_delete
            const mainForm = document.getElementById('alcoholimetriaForm');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};
            
            if (mainForm) {
                mainForm.addEventListener('invalid', function(e) {
                    if (e.target === alcoholMuni) {
                        e.preventDefault();
                        showTomSelectError(alcoholMuni, 'Seleccione un municipio.');
                        reportTomSelectValidity(alcoholMuni);
                    }
                    if (e.target === jurisdictionSelect) {
                        e.preventDefault();
                        showTomSelectError(jurisdictionSelect, 'Seleccione un distrito.');
                        reportTomSelectValidity(jurisdictionSelect);
                    }
                }, true);

                mainForm.addEventListener('submit', function(e) {
                    if (!validateAlcoholTotals(true)) {
                        e.preventDefault();
                        return false;
                    }

                    if (!validateAlcoholTomSelects(true)) {
                        e.preventDefault();
                        return false;
                    }

                    // Actualizar files_to_delete SIEMPRE (tanto en creación como en edición)
                    if (isEditMode && document.getElementById('files-to-delete')) {
                        const filesToDeleteIds = [];
                        document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                            const fileId = checkbox.closest('.file-item').dataset.fileId;
                            if (fileId) filesToDeleteIds.push(fileId);
                        });
                        document.getElementById('files-to-delete').value = filesToDeleteIds.join(',');
                    }
                });
            }
        });
    </script>

    <!-- Script para manejo de archivos -->
    <script>
        let selectedFiles = [];

        function getAlcoholNumber(name) {
            const input = document.querySelector(`[name="${name}"]`);
            return input ? Number(input.value || 0) : 0;
        }

        function clearAlcoholTotalsValidity() {
            const totalInput = document.querySelector('[name="conductores_no_aptos"]');
            if (totalInput) totalInput.setCustomValidity('');
        }

        function validateAlcoholTotals(focusFirst = false) {
            const totalInput = document.querySelector('[name="conductores_no_aptos"]');
            if (!totalInput) return true;

            const tests = getAlcoholNumber('pruebas_realizadas');
            const total = getAlcoholNumber('conductores_no_aptos');
            const genderTotal = getAlcoholNumber('mujeres_no_aptas') + getAlcoholNumber('hombres_no_aptos');
            const vehicleTotal = getAlcoholNumber('automoviles_camionetas')
                + getAlcoholNumber('motocicletas')
                + getAlcoholNumber('transporte_colectivo')
                + getAlcoholNumber('transporte_individual')
                + getAlcoholNumber('transporte_carga')
                + getAlcoholNumber('vehiculos_emergencia');

            let message = '';
            if (total > tests) {
                message = 'Los conductores no aptos no pueden ser mayores que las pruebas realizadas.';
            } else if (genderTotal !== total) {
                message = 'Mujeres y hombres no aptos deben sumar el total de conductores no aptos.';
            } else if (vehicleTotal !== total) {
                message = 'Los tipos de vehículo deben sumar el total de conductores no aptos.';
            }

            totalInput.setCustomValidity(message);
            if (message && focusFirst) {
                totalInput.reportValidity();
            }

            return message === '';
        }

        document.querySelectorAll([
            '[name="pruebas_realizadas"]',
            '[name="conductores_no_aptos"]',
            '[name="mujeres_no_aptas"]',
            '[name="hombres_no_aptos"]',
            '[name="automoviles_camionetas"]',
            '[name="motocicletas"]',
            '[name="transporte_colectivo"]',
            '[name="transporte_individual"]',
            '[name="transporte_carga"]',
            '[name="vehiculos_emergencia"]'
        ].join(',')).forEach(input => {
            input.addEventListener('input', clearAlcoholTotalsValidity);
        });

        function showFileError(message) {
            const fileError = document.getElementById('file-error');
            const fileInput = document.getElementById('file-input');
            if (!fileError) return;

            fileError.textContent = message;
            fileError.classList.remove('hidden');
            fileError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            try { fileInput?.focus(); } catch (err) {}
        }

        function clearFileError() {
            const fileError = document.getElementById('file-error');
            if (!fileError) return;

            fileError.textContent = '';
            fileError.classList.add('hidden');
        }

        function addFiles(files) {
            clearFileError();
            // Agregar nuevos archivos al array
            for (let file of files) {
                const extension = file.name.split('.').pop().toLowerCase();
                const allowedExtensions = ['xlsx', 'xls', 'jpg', 'jpeg', 'png'];
                const maxBytes = 10 * 1024 * 1024;

                if (!allowedExtensions.includes(extension)) {
                    showFileError('Formato no válido. Solo se permiten Excel (XLSX, XLS) y fotos (JPG, JPEG, PNG).');
                    continue;
                }

                if (file.size > maxBytes) {
                    showFileError('El archivo ' + file.name + ' excede el tamaño máximo permitido (10 MB).');
                    continue;
                }

                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedFiles.push(file);
                }
            }
            updateFileDisplay();
            updateFileCounters();
            document.getElementById('file-input').value = '';
        }

        function updateFileCounters() {
            // Contar archivos existentes (no marcados para eliminar)
            let existingExcelCount = 0;
            let existingPhotoCount = 0;
            
            // Contar archivos nuevos seleccionados
            let newExcelCount = 0;
            let newPhotoCount = 0;
            
            // Contar archivos existentes no marcados para eliminar
            const existingFilesList = document.getElementById('existing-files-list');
            if (existingFilesList) {
                existingFilesList.querySelectorAll('.file-item').forEach(item => {
                    const checkbox = item.querySelector('.file-delete-checkbox');
                    const fileType = item.dataset.fileType;
                    
                    if (!checkbox.checked) { // Solo contar si NO está marcado para eliminar
                        if (fileType === 'excel') existingExcelCount++;
                        else if (fileType === 'photos') existingPhotoCount++;
                    }
                });
            }
            
            // Contar archivos nuevos por tipo
            selectedFiles.forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (extension === 'xlsx' || extension === 'xls') newExcelCount++;
                else if (['jpg', 'jpeg', 'png'].includes(extension)) newPhotoCount++;
            });
            
            // Total = existentes + nuevos
            const totalExcel = existingExcelCount + newExcelCount;
            const totalPhotos = existingPhotoCount + newPhotoCount;
            
            // Actualizar los contadores
            updateCounterDisplay('excel', totalExcel, 1);
            updateCounterDisplay('photo', totalPhotos, 1);
        }
        
        function updateCounterDisplay(type, current, required) {
            const statusBadge = document.getElementById(`${type}-status`);
            
            let statusText = '';
            let badgeClass = '';
            
            if (current >= required) {
                statusText = current === required ? 'Completado' : `${current}/${required}`;
                badgeClass = 'bg-green-100 text-green-800';
            } else if (current > 0) {
                statusText = required > 1 ? `${current}/${required}` : 'Incompleto';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            } else {
                statusText = required > 1 ? `${current}/${required}` : 'Pendiente';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            }
            
            statusBadge.textContent = statusText;
            statusBadge.className = `text-xs px-2 py-1 rounded font-lora ${badgeClass}`;
        }

        function updateFileDisplay() {
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            
            // Limpiar lista
            fileNames.innerHTML = '';
            
            if (selectedFiles.length === 0) {
                fileList.classList.add('hidden');
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('photo-status').textContent = 'Pendiente';
                document.getElementById('photo-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                return;
            }

            let excelCount = 0;
            let photoCount = 0;

            // Crear elemento para cada archivo
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                let iconConfig = {};

                if (extension === 'xlsx' || extension === 'xls') {
                    iconConfig = { icon: 'document-outline', color: 'text-white', bg: 'bg-green-500' };
                    excelCount++;
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    iconConfig = { icon: 'image-outline', color: 'text-white', bg: 'bg-purple-500' };
                    photoCount++;
                } else {
                    iconConfig = { icon: 'document-outline', color: 'text-white', bg: 'bg-gray-500' };
                }

                const fileCard = document.createElement('li');
                fileCard.className = 'bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between';
                fileCard.innerHTML = `
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${iconConfig.bg}">
                            <ion-icon name="${iconConfig.icon}" class="text-xl ${iconConfig.color}"></ion-icon>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="ml-3 text-red-600 hover:text-red-800 transition-colors flex-shrink-0">
                        <ion-icon name="trash-outline" class="text-xl"></ion-icon>
                    </button>
                `;
                fileNames.appendChild(fileCard);
            });

            fileList.classList.remove('hidden');
            
            // Actualizar estados
            document.getElementById('excel-status').textContent = excelCount >= 1 ? (excelCount === 1 ? 'Completado' : `${excelCount}/1`) : 'Pendiente';
            document.getElementById('excel-status').className = excelCount >= 1 ? 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded font-lora' : 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
            
            document.getElementById('photo-status').textContent = photoCount >= 1 ? (photoCount === 1 ? 'Completado' : `${photoCount}/1`) : 'Pendiente';
            document.getElementById('photo-status').className = photoCount >= 1 ? 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded font-lora' : 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
        }

        function updateFilesToDeleteInput() {
            const filesToDeleteInput = document.getElementById('files-to-delete');
            if (!filesToDeleteInput) return;

            const filesToDeleteIds = [];
            document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                const fileId = checkbox.closest('.file-item').dataset.fileId;
                if (fileId) filesToDeleteIds.push(fileId);
            });
            filesToDeleteInput.value = filesToDeleteIds.join(',');
        }

        function syncSelectAllExistingFiles() {
            const selectAll = document.getElementById('select-all-existing-files');
            const checkboxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            if (!selectAll || checkboxes.length === 0) return;

            const checkedCount = checkboxes.filter(checkbox => checkbox.checked).length;
            const allSelected = checkedCount === checkboxes.length;
            selectAll.textContent = allSelected ? 'Deseleccionar todos' : 'Seleccionar todos';
            selectAll.setAttribute('aria-pressed', allSelected ? 'true' : 'false');
        }

        function toggleAllExistingFiles() {
            const checkboxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            const shouldCheck = !checkboxes.every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => checkbox.checked = shouldCheck);
            checkboxes.forEach(checkbox => toggleFileStrikethrough(checkbox));
            syncSelectAllExistingFiles();
        }

        function toggleFileStrikethrough(checkbox) {
            const fileItem = checkbox.closest('.file-item');
            const fileInfo = fileItem.querySelector('.file-info');
            const fileId = fileItem.dataset.fileId;
            
            if (checkbox.checked) {
                fileInfo.classList.add('line-through', 'opacity-50');
                fileItem.classList.add('opacity-75');
            } else {
                fileInfo.classList.remove('line-through', 'opacity-50');
                fileItem.classList.remove('opacity-75');
            }
            
            updateFilesToDeleteInput();
            syncSelectAllExistingFiles();
            
            // Actualizar contadores
            updateFileCounters();
        }

        function removeFile(index) {
            clearFileError();
            selectedFiles.splice(index, 1);
            updateFileDisplay();
            updateFileCounters();
        }

        async function clearAlcoholimetriaForm() {
            const form = document.getElementById('alcoholimetriaForm');
            const canClear = window.confirmFormClear
                ? await window.confirmFormClear(form, selectedFiles.length)
                : false;
            if (canClear) {
                if (form) {
                    form.reset();
                }
                if (window.resetAlcoholimetriaTomSelects) {
                    window.resetAlcoholimetriaTomSelects();
                }
                selectedFiles = [];
                updateFileDisplay();
                updateFileCounters();

                if (typeof window.showToast === 'function') {
                    window.showToast('Formulario limpiado.', 'info', 2400);
                }
            }
        }
        
        // Funcionalidad de arrastrar y soltar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar contadores en modo edición
            updateFileCounters();
            
            const dropArea = document.querySelector('.border-dashed');
            const fileInput = document.getElementById('file-input');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropArea.classList.add('border-[#404041]', 'bg-blue-50');
            }
            
            function unhighlight() {
                dropArea.classList.remove('border-[#404041]', 'bg-blue-50');
            }
            
            dropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                addFiles(files);
            }

            // Interceptar el envío del formulario para validar archivos en cliente (solo en creación)
            const mainForm = document.getElementById('alcoholimetriaForm');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};
            
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    if (e.defaultPrevented) {
                        return false;
                    }

                    // Actualizar files_to_delete SIEMPRE (tanto en creación como en edición)
                    if (isEditMode && document.getElementById('files-to-delete')) {
                        const filesToDeleteIds = [];
                        document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                            const fileId = checkbox.closest('.file-item').dataset.fileId;
                            if (fileId) filesToDeleteIds.push(fileId);
                        });
                        document.getElementById('files-to-delete').value = filesToDeleteIds.join(',');
                    }
                    
                    // Solo validar en modo CREACIÓN
                    if (!isEditMode) {
                        if (selectedFiles.length === 0) {
                            e.preventDefault();
                            showFileError('El reporte requiere: archivo Excel (0/1), fotografías (0/1).');
                            return false;
                        }
                        
                        let excelCount = 0;
                        let photoCount = 0;
                        
                        selectedFiles.forEach(file => {
                            const extension = file.name.split('.').pop().toLowerCase();
                            if (extension === 'xlsx' || extension === 'xls') excelCount++;
                            else if (['jpg', 'jpeg', 'png'].includes(extension)) photoCount++;
                        });
                        
                        if (excelCount < 1) {
                            e.preventDefault();
                            showFileError('Debe incluir al menos 1 archivo Excel (XLSX).');
                            return false;
                        }
                        
                        if (photoCount < 1) {
                            e.preventDefault();
                            showFileError('Debe incluir 1 fotografía (JPG, JPEG o PNG).');
                            return false;
                        }
                        
                        // Asignar los archivos seleccionados al input file existente
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        if (fileInput) {
                            fileInput.files = dataTransfer.files;
                        }
                    } else if (isEditMode && selectedFiles.length > 0) {
                        // En modo edición, también agregar los nuevos archivos
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        if (fileInput) {
                            fileInput.files = dataTransfer.files;
                        }
                    }
                });
            }
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection
