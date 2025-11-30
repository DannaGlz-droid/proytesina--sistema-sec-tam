@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar Alcoholimetría' : 'Registro de Alcoholimetría')
@section('content')

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">
            {{ isset($publication) ? 'Editar alcoholimetría' : 'Registro de alcoholimetría' }}
        </h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para {{ isset($publication) ? 'actualizar' : 'registrar' }} los datos de los operativos de alcoholimetría.</p>

        <!-- Cuadro del formulario responsive -->
        <form action="{{ isset($publication) ? route('reportes.alcoholimetria.update', $publication) : route('reportes.alcoholimetria.store') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text"
                                   name="tema"
                                   minlength="3"
                                   maxlength="255"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   placeholder="Ej: Operativo alcoholimetría fin de semana"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha <span class="text-red-600">*</span></label>
                            <input type="date"
                                   name="fecha"
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Operativos de alcoholimetría -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="location-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
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
                        <textarea id="descripcion" name="descripcion" maxlength="5000" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" rows="4" placeholder="Describa los detalles, contexto, objetivos, resultados, etc. (opcional)">{{ old('descripcion', isset($publication) ? $publication->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 6: Carga de archivo -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="cloud-upload-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Carga de archivo</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-4">
                    <!-- Archivos existentes (solo en modo edición) -->
                    @if(isset($publication) && $publication->files->count() > 0)
                        <div class="mb-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium mb-3 font-lora text-sm text-[#404041] flex items-center">
                                    <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                    Archivos actuales ({{ $publication->files->count() }})
                                </p>
                                <ul class="space-y-2">
                                    @foreach($publication->files as $file)
                                        <li class="flex items-center justify-between py-2 px-3 hover:bg-white rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm">
                                            <div class="flex items-center flex-1 min-w-0">
                                                @php
                                                    $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                                    $iconConfig = match(strtolower($extension)) {
                                                        'pdf' => ['icon' => 'document-text-outline', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                                        'xlsx', 'xls' => ['icon' => 'stats-chart-outline', 'color' => 'text-green-500', 'bg' => 'bg-green-50'],
                                                        'jpg', 'jpeg', 'png' => ['icon' => 'image-outline', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50'],
                                                        default => ['icon' => 'document-outline', 'color' => 'text-gray-400', 'bg' => 'bg-gray-50']
                                                    };
                                                @endphp
                                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg {{ $iconConfig['bg'] }}">
                                                    <ion-icon name="{{ $iconConfig['icon'] }}" class="{{ $iconConfig['color'] }} text-xl"></ion-icon>
                                                </div>
                                                <div class="ml-3 flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-[#404041] truncate">{{ $file->original_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</p>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    onclick="if(confirm('¿Eliminar este archivo?')) { document.getElementById('delete-file-{{ $file->id }}').submit(); }"
                                                    class="ml-3 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-all duration-200" 
                                                    title="Eliminar archivo">
                                                <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Cuadro para archivo Excel que abarca todo el ancho -->
                    <div class="p-4 border border-gray-300 rounded-lg bg-white">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                <span class="text-sm font-medium text-[#404041] font-lora">Hoja de Cálculo</span>
                            </div>
                            <span id="excel-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                {{ isset($publication) ? 'Opcional (agregar nuevo)' : 'Pendiente' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 font-lora">Formato: XLSX {{ isset($publication) ? '(opcional - se agregará a los existentes)' : '(obligatorio)' }}</p>
                    </div>

                    <!-- Área de carga de archivo -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2 font-lora">
                            @if(isset($publication))
                                Agregar nuevo archivo (opcional)
                            @else
                                Subir archivo <span class="text-red-600">*</span>
                            @endif
                        </label>
                        
                        <!-- Cuadro punteado para arrastrar y soltar -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#404041] transition-colors duration-200 bg-gray-50">
                            <input type="file" 
                                   id="file-input"
                                   name="archivo"
                                   class="hidden"
                                   accept=".xlsx,.xls"
                                   onchange="updateFileStatus()">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1 font-lora">
                                    Haga clic o arrastre el archivo aquí para subirlo
                                </p>
                                <p class="text-xs text-gray-500 font-lora">
                                    Formatos permitidos: XLSX, XLS<br>
                                    <span class="text-xs text-gray-500">Tamaño máximo: 10 MB</span>
                                </p>
                            </div>
                            <div id="file-error" class="mt-2 text-xs text-red-600 font-lora hidden"></div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
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
                    tertiaryHref="{{ route('reportes.index') }}"
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
                    tertiaryHref="{{ route('reportes.index') }}"
                />
            @endif
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

    <!-- Script para manejo de archivos -->
    <script>
        function updateFileStatus() {
            const fileInput = document.getElementById('file-input');
            const files = fileInput.files;
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            // Limpiar mensaje de error
            const fileErrorEl = document.getElementById('file-error');
            if (fileErrorEl) { fileErrorEl.classList.add('hidden'); fileErrorEl.textContent = ''; }
            
            // Verificar archivo seleccionado
            if (files.length > 0) {
                const file = files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Validar que sea archivo Excel
                if (extension === 'xlsx' || extension === 'xls') {
                    // Validar tamaño (10 MB)
                    const maxBytes = 10 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        // Archivo demasiado grande
                        const fileError = document.getElementById('file-error');
                        if (fileError) {
                            fileError.textContent = 'El archivo excede el tamaño máximo permitido (10 MB).';
                            fileError.classList.remove('hidden');
                        } else {
                            alert('El archivo excede el tamaño máximo permitido (10 MB).');
                        }
                        fileInput.value = '';
                        document.getElementById('excel-status').textContent = 'Pendiente';
                        document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                        fileList.classList.add('hidden');
                        return;
                    }
                    // Crear card estilizada para el archivo
                    const fileCard = document.createElement('li');
                    fileCard.className = 'bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between';
                    fileCard.innerHTML = `
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <ion-icon name="stats-chart" class="text-xl text-green-600"></ion-icon>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                                <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            </div>
                        </div>
                        <button type="button" onclick="clearSelectedFile()" class="ml-3 text-red-600 hover:text-red-800 transition-colors flex-shrink-0">
                            <ion-icon name="trash-outline" class="text-xl"></ion-icon>
                        </button>
                    `;
                    fileNames.appendChild(fileCard);
                    
                    // Actualizar estado
                    document.getElementById('excel-status').textContent = 'Completado';
                    document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded font-lora';
                    
                    fileList.classList.remove('hidden');
                } else {
                    // Archivo no válido
                    const fileError = document.getElementById('file-error');
                    if (fileError) {
                        fileError.textContent = 'Formato no válido. Solo se permiten archivos XLSX o XLS.';
                        fileError.classList.remove('hidden');
                    } else {
                        alert('Por favor seleccione un archivo Excel (XLSX o XLS)');
                    }
                    fileInput.value = '';
                    document.getElementById('excel-status').textContent = 'Pendiente';
                    document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                    fileList.classList.add('hidden');
                }
            } else {
                // No hay archivo seleccionado
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                fileList.classList.add('hidden');
            }
        }

        function clearSelectedFile() {
            const fileInput = document.getElementById('file-input');
            fileInput.value = '';
            updateFileStatus();
        }
        
        function clearAlcoholimetriaForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                document.querySelector('form').reset();
                // Resetear estado del archivo
                const desc = document.getElementById('descripcion');
                if (desc) desc.value = '';
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('file-list').classList.add('hidden');
                const fileError = document.getElementById('file-error');
                if (fileError) { fileError.classList.add('hidden'); fileError.textContent = ''; }
            }
        }
        
        // Funcionalidad de arrastrar y soltar
        document.addEventListener('DOMContentLoaded', function() {
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
                
                // Solo permitir un archivo
                if (files.length === 1) {
                    fileInput.files = files;
                    updateFileStatus();
                } else if (files.length > 1) {
                    alert('Solo se permite subir un archivo a la vez');
                }
            }

            // Interceptar el envío del formulario para validar archivo en cliente (solo en creación)
            const mainForm = document.querySelector('form[action*="alcoholimetria"][method="POST"]');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    if (!isEditMode) {
                        if (!fileInput || fileInput.files.length === 0) {
                            e.preventDefault();
                            alert('Debe incluir al menos 1 archivo Excel (XLSX).');
                            try { fileInput.focus(); } catch (err) {}
                            return false;
                        }
                        // Validar extensión y tamaño
                        const f = fileInput.files[0];
                        const ext = f.name.split('.').pop().toLowerCase();
                        const allowed = ['xlsx','xls'];
                        if (!allowed.includes(ext)) {
                            e.preventDefault();
                            alert('Formato no válido. Solo se permiten archivos XLSX o XLS.');
                            return false;
                        }
                        const maxBytes = 10 * 1024 * 1024; // 10 MB
                        if (f.size > maxBytes) {
                            e.preventDefault();
                            alert('El archivo excede el tamaño máximo permitido (10 MB).');
                            return false;
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