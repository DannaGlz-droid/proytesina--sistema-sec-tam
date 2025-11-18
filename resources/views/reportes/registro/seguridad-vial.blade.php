@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar Actividad' : 'Registro de Actividades')
@section('content')

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
        <form action="{{ isset($publication) ? route('reportes.seguridad-vial.update', $publication) : route('reportes.seguridad-vial.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tema *</label>
                            <input type="text" 
                                   name="tema"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Prevención de enfermedades"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de la actividad *</label>
                            <input type="date" 
                                   name="fecha"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar *</label>
                            <input type="text" 
                                   name="lugar"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Salón de usos múltiples"
                                   value="{{ old('lugar', isset($report) ? $report->location : '') }}"
                                   required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                         <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tipo de actividad *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="activity_type_id" required>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Participantes *</label>
                            <input type="number" 
                                   name="participantes"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 25"
                                   value="{{ old('participantes', isset($report) ? $report->participants : '') }}"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Promotor *</label>
                            <input type="text" 
                                   name="promotor"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Departamento de Salud"
                                   value="{{ old('promotor', isset($report) ? $report->promoter : '') }}"
                                   required>
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
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción de la actividad *</label>
                        <textarea 
                            name="descripcion"
                            class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                            rows="4"
                            placeholder="Describa los detalles de la actividad, objetivos, desarrollo y resultados..."
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

                    <!-- Tres cuadros en una fila horizontal -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- (1) Documento PDF -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="document-outline" class="text-blue-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Documento PDF</span>
                                </div>
                                <span id="pdf-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    {{ isset($publication) ? 'Opcional' : 'Pendiente' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: PDF {{ isset($publication) ? '(opcional)' : '(obligatorio)' }}</p>
                        </div>

                        <!-- (2) Hoja de Cálculo -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Hoja de Cálculo</span>
                                </div>
                                <span id="excel-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    {{ isset($publication) ? 'Opcional' : 'Pendiente' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: XLSX {{ isset($publication) ? '(opcional)' : '(obligatorio)' }}</p>
                        </div>

                        <!-- (3) Fotografías -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="images-outline" class="text-purple-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Fotografías</span>
                                </div>
                                <span id="photos-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    {{ isset($publication) ? 'Opcional' : '0/4' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formatos: JPG, JPEG, PNG {{ isset($publication) ? '(opcional)' : '(4 fotos obligatorias)' }}</p>
                        </div>
                    </div>

                    <!-- Área de carga de archivos -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2 font-lora">Subir archivos (selección múltiple) *</label>
                        
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
                                    Formatos permitidos: PDF, XLSX, XLS, JPG, JPEG, PNG
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
            <x-form-buttons 
                primaryText="Guardar registro"
                secondaryText="Limpiar formulario"
                primaryType="submit"
                secondaryType="button"
                secondaryOnclick="clearSeguridadVialForm()"
            />
        </div>
        </form>
    </div>

    <!-- Script para manejo de archivos -->
    <script>
        // Array para almacenar todos los archivos seleccionados
        let selectedFiles = [];
        
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
            
            // Limpiar el input para permitir seleccionar el mismo archivo de nuevo si se elimina
            document.getElementById('file-input').value = '';
        }
        
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileStatus();
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
                let iconColor = 'text-gray-400';
                
                if (extension === 'pdf') {
                    pdfCount++;
                    iconName = 'document-text-outline';
                    iconColor = 'text-blue-500';
                } else if (extension === 'xlsx' || extension === 'xls') {
                    excelCount++;
                    iconName = 'stats-chart-outline';
                    iconColor = 'text-green-500';
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    photoCount++;
                    iconName = 'image-outline';
                    iconColor = 'text-purple-500';
                }
                
                // Agregar a la lista con botón de eliminar
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center justify-between py-2 px-3 hover:bg-white rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm';
                listItem.innerHTML = `
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg ${iconColor === 'text-blue-500' ? 'bg-blue-50' : iconColor === 'text-green-500' ? 'bg-green-50' : 'bg-purple-50'}">
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
            updateStatus('pdf-status', pdfCount, 1, 'Documento PDF');
            updateStatus('excel-status', excelCount, 1, 'Hoja de Cálculo');
            updateStatus('photos-status', photoCount, 4, 'Fotografías');
        }
        
        function updateStatus(elementId, currentCount, requiredCount, typeName) {
            const element = document.getElementById(elementId);
            
            if (currentCount >= requiredCount) {
                element.textContent = currentCount === 1 ? 'Completado' : `${currentCount}/${requiredCount}`;
                element.className = 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded font-lora';
            } else if (currentCount > 0) {
                element.textContent = typeName === 'Fotografías' ? `${currentCount}/${requiredCount}` : 'Incompleto';
                element.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
            } else {
                element.textContent = typeName === 'Fotografías' ? `0/${requiredCount}` : 'Pendiente';
                element.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
            }
        }
        
        function clearSeguridadVialForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                document.querySelector('form').reset();
                // Limpiar archivos seleccionados
                selectedFiles = [];
                updateFileStatus();
                // Resetear estados de archivos
                document.getElementById('pdf-status').textContent = 'Pendiente';
                document.getElementById('pdf-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('photos-status').textContent = '0/4';
                document.getElementById('photos-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('file-list').classList.add('hidden');
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

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection