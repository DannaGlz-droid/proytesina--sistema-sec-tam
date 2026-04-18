@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar Actividad' : 'Registro de Actividades')
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
    </style>

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">
            {{ isset($publication) ? 'Editar actividad' : 'Registro de actividades' }}
        </h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para {{ isset($publication) ? 'actualizar' : 'registrar' }} las actividades correspondientes.</p>

        <!-- Mensajes de error -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm text-red-800 font-lora font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-red-800 font-lora font-semibold mb-2">Errores de validación:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 font-lora space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Cuadro del formulario responsive -->
        <form id="seguridadVialForm" action="{{ isset($publication) ? route('reportes.seguridad-vial.update', $publication) : route('reportes.seguridad-vial.store') }}" method="POST" enctype="multipart/form-data">
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
                                   placeholder="Ej: Prevención de accidentes viales"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required minlength="3" maxlength="146">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" 
                                   name="fecha"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required max="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar <span class="text-red-600">*</span></label>
                            <input id="lugar" type="text" 
                                   name="lugar"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Salón de usos múltiples"
                                   value="{{ old('lugar', isset($report) ? $report->location : '') }}"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio <span class="text-red-600">*</span></label>
                            <select id="seguridad_municipality_select" name="municipio" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" required>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tipo de actividad <span class="text-red-600">*</span></label>
                            <select id="activity_type_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="activity_type_id" required>
                                <option value="">Seleccione el tipo de actividad</option>
                                @php
                                    $selectedActivity = old('activity_type_id', isset($report) ? $report->activity_type_id : '');
                                @endphp
                                <option value="1" {{ $selectedActivity == '1' ? 'selected' : '' }}>Capacitación</option>
                                <option value="2" {{ $selectedActivity == '2' ? 'selected' : '' }}>Taller</option>
                                <option value="3" {{ $selectedActivity == '3' ? 'selected' : '' }}>Platica de sensibilizacion</option>
                                <option value="4" {{ $selectedActivity == '4' ? 'selected' : '' }}>Reunión</option>
                                <option value="5" {{ $selectedActivity == '5' ? 'selected' : '' }}>Evento especial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Participantes <span class="text-red-600">*</span></label>
                            <input id="participantes" type="number" 
                                   name="participantes"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 25"
                                   value="{{ old('participantes', isset($report) ? $report->participants : '') }}"
                                   required min="1" max="9999">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Promotor <span class="text-red-600">*</span></label>
                            <input id="promotor" type="text" 
                                   name="promotor"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Departamento de Salud"
                                   value="{{ old('promotor', isset($report) ? $report->promoter : '') }}"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-500 mb-1 font-lora">Jurisdicción</label>
                            <input type="hidden" id="jurisdiction_input_vial" name="jurisdiccion" value="{{ old('jurisdiccion', isset($report) ? $report->jurisdiction_id : '') }}" required>
                            <input id="jurisdiction_display_vial" type="text" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-600 focus:ring-2 focus:ring-gray-300 focus:border-transparent transition-all duration-200 font-lora" value="{{ isset($report) && $report->jurisdiction ? $report->jurisdiction->name : 'Pendiente (seleccione municipio)' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Descripción -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="clipboard-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Descripción</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción de la actividad</label>
                        <textarea 
                            id="descripcion"
                            name="descripcion"
                            class="w-full px-3 py-2 text-xs lg:text-sm border border-[#404041] rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora break-words whitespace-normal description-scroll" 
                            rows="4"
                            placeholder="Describa los detalles, contexto, objetivos, resultados, etc. (opcional)"
                            maxlength="5000"
                        >{{ old('descripcion', isset($publication) ? $publication->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Carga de archivos -->
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
                                <p class="font-medium mb-3 font-lora text-sm text-[#404041] flex items-center">
                                    <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                    Archivos actuales ({{ $publication->files->count() }})
                                </p>
                                @php
                                    // Get file requirements and count files by type
                                    $requirements = \App\Config\ReportFileRequirements::getRequirements('seguridad-vial');
                                    $filesByType = [
                                        'pdf' => $publication->files->filter(fn($f) => strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)) === 'pdf'),
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
                                                'pdf' => ['icon' => 'document-text-outline', 'color' => 'text-white', 'bg' => 'bg-red-500'],
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

                    <!-- Tres cuadros en una fila horizontal - CON ESTADO ACTUALIZADO -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- (1) Documento PDF -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="document-outline" class="text-blue-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Documento PDF</span>
                                </div>
                                <span id="pdf-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: PDF (obligatorio)</p>
                        </div>

                        <!-- (2) Hoja de Cálculo -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Hoja de Cálculo</span>
                                </div>
                                <span id="excel-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: XLSX (obligatorio)</p>
                        </div>

                        <!-- (3) Fotografías -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="images-outline" class="text-purple-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Fotografías</span>
                                </div>
                                <span id="photos-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    0/4
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formatos: JPG, JPEG, PNG (4 fotos obligatorias)</p>
                        </div>
                    </div>

                    <!-- Área de carga de archivos -->
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
                                   class="hidden"
                                   accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png"
                                   multiple
                                   onchange="addFiles(this.files)">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1 font-lora">
                                    Haga clic o arrastre archivos aquí para subirlos
                                </p>
                                <p class="text-xs text-gray-500 font-lora">
                                    Formatos permitidos: PDF, XLSX, XLS, JPG, JPEG, PNG<br>
                                    <span class="text-xs text-gray-500">Tamaño máximo por archivo: 10 MB</span>
                                </p>
                                <p class="text-xs text-blue-600 font-lora mt-2">
                                    Puede seleccionar múltiples archivos a la vez
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información de archivos seleccionados -->
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
                    tertiaryHref="{{ route('reportes.index', ['tipo' => request('redirect_tipo', 'seguridad_vial')]) }}"
                    primaryType="submit"
                />
            @else
                <x-form-buttons 
                    primaryText="Guardar registro"
                    secondaryText="Limpiar formulario"
                    primaryType="submit"
                    secondaryType="button"
                    secondaryOnclick="clearSeguridadVialForm()"
                    tertiaryText="Volver al listado"
                    tertiaryHref="{{ route('reportes.index', ['tipo' => request('redirect_tipo', 'seguridad_vial')]) }}"
                />
            @endif
        </div>

        <!-- Input oculto para archivos a eliminar -->
        @if(isset($publication))
            <input type="hidden" id="files-to-delete" name="files_to_delete" value="">
        @endif
        <!-- Input oculto para redirect_tipo -->
        <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'seguridad_vial') }}">
        </form>

        @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fieldMap = {
                    'tema': 'tema',
                    'fecha': 'fecha',
                    'lugar': 'lugar',
                    'activity_type_id': 'activity_type_id',
                    'participantes': 'participantes',
                    'promotor': 'promotor',
                    'descripcion': 'descripcion'
                };

                let firstErrorField = null;

                @foreach($errors->keys() as $field)
                    @php $errorMessage = $errors->first($field); @endphp
                    if (fieldMap['{{ $field }}']) {
                        const element = document.getElementById(fieldMap['{{ $field }}']);
                        if (element) {
                            element.setCustomValidity('{{ addslashes($errorMessage) }}');
                            if (!firstErrorField) {
                                firstErrorField = element;
                            }
                            element.addEventListener('input', function() {
                                this.setCustomValidity('');
                            });
                        }
                    }
                @endforeach

                if (firstErrorField) {
                    firstErrorField.reportValidity();
                }
            });
        </script>
        @endif
    </div>

    <!-- Script para manejo de archivos -->
    <script>
        // Array para almacenar todos los archivos seleccionados
        let selectedFiles = [];
        
        // Track de archivos a eliminar
        let filesToDelete = [];
        
        function addFiles(newFiles) {
            // Agregar nuevos archivos al array
            for (let i = 0; i < newFiles.length; i++) {
                const file = newFiles[i];
                // Verificar que el archivo no esté ya en la lista
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedFiles.push(file);
                }
            }
            
            // Actualizar la visualización
            updateFileStatus();
            updateFileCounters();
            
            // Limpiar el input para permitir seleccionar el mismo archivo de nuevo si se elimina
            document.getElementById('file-input').value = '';
        }
        
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileStatus();
            updateFileCounters();
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
            
            // Actualizar contador y lista de archivos a eliminar
            updateFileCounters();
            
            // Actualizar el input hidden con archivos a eliminar
            const filesToDeleteIds = [];
            document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                const fileId = checkbox.closest('.file-item').dataset.fileId;
                if (fileId) filesToDeleteIds.push(fileId);
            });
            document.getElementById('files-to-delete').value = filesToDeleteIds.join(',');
        }
        
        function updateFileCounters() {
            // Contar archivos existentes (no marcados para eliminar)
            let existingPdfCount = 0;
            let existingExcelCount = 0;
            let existingPhotoCount = 0;
            
            // Contar archivos nuevos seleccionados
            let newPdfCount = 0;
            let newExcelCount = 0;
            let newPhotoCount = 0;
            
            // Contar archivos existentes no marcados para eliminar
            document.querySelectorAll('#existing-files-list .file-item').forEach(item => {
                const checkbox = item.querySelector('.file-delete-checkbox');
                const fileType = item.dataset.fileType;
                
                if (!checkbox.checked) { // Solo contar si NO está marcado para eliminar
                    if (fileType === 'pdf') existingPdfCount++;
                    else if (fileType === 'excel') existingExcelCount++;
                    else if (fileType === 'photos') existingPhotoCount++;
                }
            });
            
            // Contar archivos nuevos por tipo
            selectedFiles.forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (extension === 'pdf') newPdfCount++;
                else if (extension === 'xlsx' || extension === 'xls') newExcelCount++;
                else if (['jpg', 'jpeg', 'png'].includes(extension)) newPhotoCount++;
            });
            
            // Total = existentes + nuevos
            const totalPdf = existingPdfCount + newPdfCount;
            const totalExcel = existingExcelCount + newExcelCount;
            const totalPhotos = existingPhotoCount + newPhotoCount;
            
            // Actualizar los contadores de los tres cuadros
            updateCounterDisplay('pdf', totalPdf, 1);
            updateCounterDisplay('excel', totalExcel, 1);
            updateCounterDisplay('photos', totalPhotos, 4);
        }
        
        function updateCounterDisplay(type, currentCount, requiredCount) {
            const badgeId = type + '-status-badge';
            const badge = document.getElementById(badgeId);
            
            let statusText = '';
            let badgeClass = '';
            
            if (currentCount >= requiredCount) {
                statusText = currentCount === requiredCount ? 'Completado' : `${currentCount}/${requiredCount}`;
                badgeClass = 'bg-green-100 text-green-800';
            } else if (currentCount > 0) {
                statusText = type === 'photos' ? `${currentCount}/${requiredCount}` : 'Incompleto';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            } else {
                statusText = type === 'photos' ? `${currentCount}/${requiredCount}` : 'Pendiente';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            }
            
            badge.textContent = statusText;
            badge.className = `text-xs px-2 py-1 rounded font-lora ${badgeClass}`;
        }
        
        function updateFileStatus() {
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            
            let pdfCount = 0;
            let excelCount = 0;
            let photoCount = 0;
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            
            // Contar archivos por tipo y mostrar nombres
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Determinar icono y color según el tipo
                let iconName = 'document-outline';
                let bgColor = 'bg-gray-500';
                let iconColor = 'text-white';
                
                if (extension === 'pdf') {
                    pdfCount++;
                    iconName = 'document-text-outline';
                    bgColor = 'bg-red-500';
                } else if (extension === 'xlsx' || extension === 'xls') {
                    excelCount++;
                    iconName = 'document-outline';
                    bgColor = 'bg-green-500';
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    photoCount++;
                    iconName = 'image-outline';
                    bgColor = 'bg-purple-500';
                }
                
                // Agregar a la lista con botón de eliminar
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center justify-between py-2 px-3 hover:bg-white rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm';
                listItem.innerHTML = `
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg ${bgColor}">
                            <ion-icon name="${iconName}" class="${iconColor} text-xl"></ion-icon>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#404041] truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="ml-3 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-all duration-200" title="Eliminar archivo">
                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                    </button>
                `;
                fileNames.appendChild(listItem);
            });
            
            // Mostrar/ocultar lista de archivos
            if (selectedFiles.length > 0) {
                fileList.classList.remove('hidden');
            } else {
                fileList.classList.add('hidden');
            }
            
            // Actualizar estados
            updateCounterDisplay('pdf', pdfCount, 1);
            updateCounterDisplay('excel', excelCount, 1);
            updateCounterDisplay('photos', photoCount, 4);
        }
        
        function clearSeguridadVialForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                const form = document.getElementById('seguridadVialForm');
                if (form) {
                    form.reset();
                }
                // Limpiar archivos seleccionados
                selectedFiles = [];
                updateFileStatus();
                // Resetear estados de archivos
                document.getElementById('pdf-status-badge').textContent = 'Pendiente';
                document.getElementById('pdf-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('excel-status-badge').textContent = 'Pendiente';
                document.getElementById('excel-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('photos-status-badge').textContent = '0/4';
                document.getElementById('photos-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('file-list').classList.add('hidden');
                
                // Reinicializar Tom Select para que cargue todos los municipios de nuevo
                const seguridadMuni = document.getElementById('seguridad_municipality_select');
                if (seguridadMuni && seguridadMuni.tomselect) {
                    seguridadMuni.tomselect.destroy();
                    seguridadMuni.tomselect = null;
                    // Limpiar el valor
                    seguridadMuni.value = '';
                    // Reinicializar Tom Select
                    const currentJurisdiction = @json(optional(auth()->user())->jurisdiction_id);
                    const ts = new TomSelect(seguridadMuni, {
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        maxOptions: 20,
                        maxItems: 1,
                        create: false,
                        preload: true,
                        load: function(query, callback) {
                            let url = '/api/municipalities/search?q=' + encodeURIComponent(query);
                            if (currentJurisdiction) {
                                url += '&jurisdiction_id=' + encodeURIComponent(currentJurisdiction);
                            }
                            fetch(url).then(r => r.json()).then(items => callback(items)).catch(() => callback());
                        },
                        onChange: function(value) {
                            const evt = new Event('change');
                            seguridadMuni.dispatchEvent(evt);
                        }
                    });
                    try { seguridadMuni.style.display = 'none'; } catch (e) {}
                }
                
                // Restaurar la jurisdicción del usuario (es fija y no debe cambiar)
                const jurisdictionDisplay = document.getElementById('jurisdiction_display_vial');
                const hiddenJur = document.getElementById('jurisdiction_input_vial');
                const currentJurisdiction = @json(optional(auth()->user())->jurisdiction_id);
                const jurisNames = @json($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; }));
                if (currentJurisdiction) {
                    if (hiddenJur) hiddenJur.value = currentJurisdiction;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[currentJurisdiction] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
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
                addFiles(files);
            }
            
            // Interceptar el envío del formulario para agregar los archivos
            const mainForm = document.querySelector('form[action*="seguridad-vial"][method="POST"]:not([id^="delete-file"])');
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    console.log('Form submit interceptado, action:', this.action);
                    console.log('Form method:', this.method);
                    
                    // Solo validar archivos en modo CREACIÓN (no en modo EDICIÓN)
                    const isEditMode = this.action.includes('/edit') || this.querySelector('input[name="_method"][value="PUT"]');
                    console.log('Is edit mode:', isEditMode);
                    
                    if (!isEditMode && selectedFiles.length === 0) {
                        e.preventDefault();
                        alert('Debe seleccionar al menos un archivo antes de enviar el formulario.');
                        return false;
                    }
                    
                    // Contar archivos por tipo solo si hay archivos seleccionados
                    if (selectedFiles.length > 0) {
                        let pdfCount = 0;
                        let excelCount = 0;
                        let photoCount = 0;
                        
                        selectedFiles.forEach(file => {
                            const extension = file.name.split('.').pop().toLowerCase();
                            if (extension === 'pdf') pdfCount++;
                            else if (extension === 'xlsx' || extension === 'xls') excelCount++;
                            else if (['jpg', 'jpeg', 'png'].includes(extension)) photoCount++;
                        });
                        
                        // Validar solo en modo creación
                        if (!isEditMode) {
                            if (pdfCount < 1) {
                                e.preventDefault();
                                alert('Debe incluir al menos 1 archivo PDF.');
                                return false;
                            }
                            
                            if (excelCount < 1) {
                                e.preventDefault();
                                alert('Debe incluir al menos 1 archivo Excel (XLSX).');
                                return false;
                            }
                            
                            if (photoCount < 4) {
                                e.preventDefault();
                                alert(`Debe incluir 4 fotografías. Actualmente tiene ${photoCount} foto(s).`);
                                return false;
                            }
                        }
                        
                        // Crear un DataTransfer para poder asignar múltiples archivos al input
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        
                        // Crear un input hidden con todos los archivos
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'file';
                        hiddenInput.name = 'archivos[]';
                        hiddenInput.multiple = true;
                        hiddenInput.files = dataTransfer.files;
                        hiddenInput.style.display = 'none';
                        
                        this.appendChild(hiddenInput);
                    }
                    
                    console.log('Form va a ser enviado normalmente');
                });
            }
        });
    </script>

    {{-- Formularios ocultos para eliminar archivos (renderizados fuera del form principal para evitar MethodOverride en el PUT) --}}
    @if(isset($publication) && $publication->files->count() > 0)
        @foreach($publication->files as $file)
            <form id="delete-file-{{ $file->id }}" method="POST" action="{{ route('reportes.file.delete', $file) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
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
            border-radius: 0.5rem !important; font-size: 0.875rem;
            min-height: 36px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map municipality_id -> jurisdiction_id
            const muniToJur = @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id]; }));
            const jurisNames = @json($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; }));
            // Jurisdicción del usuario (puede ser null)
            const currentJurisdiction = @json(optional(auth()->user())->jurisdiction_id);

            const seguridadMuni = document.getElementById('seguridad_municipality_select');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display_vial');
            const hiddenJur = document.getElementById('jurisdiction_input_vial');

            function setJurisdictionBasedOnMunicipality() {
                const mid = seguridadMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[jid] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            }

            if (seguridadMuni) {
                seguridadMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }

            // Initialize Tom Select for municipality
            function fetchMunicipalities(q) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(q);
                if (currentJurisdiction) {
                    url += '&jurisdiction_id=' + encodeURIComponent(currentJurisdiction);
                }
                return fetch(url).then(r => r.json());
            }

            if (seguridadMuni) {
                const ts = new TomSelect(seguridadMuni, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 20,
                    maxItems: 1,
                    create: false,
                    preload: true,
                    load: function(query, callback) {
                        fetchMunicipalities(query).then(items => callback(items)).catch(() => callback());
                    },
                    onChange: function(value) {
                        const evt = new Event('change');
                        seguridadMuni.dispatchEvent(evt);
                    }
                });
                try { seguridadMuni.style.display = 'none'; } catch (e) {}
            }

            // Si el usuario tiene una jurisdicción asignada, establecerla en el campo oculto y en el display
            if (currentJurisdiction) {
                if (hiddenJur && !hiddenJur.value) hiddenJur.value = currentJurisdiction;
                if (jurisdictionDisplay && (!jurisdictionDisplay.value || jurisdictionDisplay.value.includes('Pendiente'))) {
                    jurisdictionDisplay.value = jurisNames[currentJurisdiction] || jurisdictionDisplay.value;
                }
            }

            // Interceptar el envío del formulario para actualizar files_to_delete
            const mainForm = document.getElementById('seguridadVialForm');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};
            
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
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

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection