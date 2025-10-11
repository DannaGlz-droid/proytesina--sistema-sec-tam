@extends('layouts.principal')
@section('title', 'Registro de Actividades')
@section('content')

    @include('components.header')
    @include('components.nav')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Registro de actividades</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para registrar las actividades correspondientes.</p>

        <!-- Cuadro del formulario responsive -->
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Tipo de actividad *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="tipo_actividad">
                                <option value="">Seleccione el tipo de actividad</option>
                                <option value="capacitacion">Capacitación</option>
                                <option value="taller">Taller</option>
                                <option value="conferencia">Conferencia</option>
                                <option value="reunion">Reunión</option>
                                <option value="evento">Evento especial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Fecha de la actividad *</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200"
                                   value="{{ old('fecha_actividad') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Lugar *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: Salón de usos múltiples"
                                   value="{{ old('lugar') }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Tema *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: Prevención de enfermedades"
                                   value="{{ old('tema') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Participantes *</label>
                            <input type="number" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: 25"
                                   value="{{ old('participantes') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Promotor *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: Departamento de Salud"
                                   value="{{ old('promotor') }}">
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
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Descripción de la actividad *</label>
                        <textarea 
                            class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                            rows="4"
                            placeholder="Describa los detalles de la actividad, objetivos, desarrollo y resultados..."
                        >{{ old('descripcion') }}</textarea>
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
                    <!-- Tres cuadros en una fila horizontal -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- (1) Documento PDF -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="document-outline" class="text-blue-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041]">Documento PDF</span>
                                </div>
                                <span id="pdf-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pendiente</span>
                            </div>
                            <p class="text-xs text-gray-600">Formato: PDF (obligatorio)</p>
                        </div>

                        <!-- (2) Hoja de Cálculo -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041]">Hoja de Cálculo</span>
                                </div>
                                <span id="excel-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pendiente</span>
                            </div>
                            <p class="text-xs text-gray-600">Formato: XLSX (obligatorio)</p>
                        </div>

                        <!-- (3) Fotografías -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <ion-icon name="images-outline" class="text-purple-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041]">Fotografías</span>
                                </div>
                                <span id="photos-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">0/4</span>
                            </div>
                            <p class="text-xs text-gray-600">Formatos: JPG, JPEG, PNG (4 fotos obligatorias)</p>
                        </div>
                    </div>

                    <!-- Área de carga de archivos -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2">Subir archivos (selección múltiple) *</label>
                        
                        <!-- Cuadro punteado para arrastrar y soltar -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#404041] transition-colors duration-200 bg-gray-50">
                            <input type="file" 
                                   id="file-input"
                                   class="hidden"
                                   accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png"
                                   multiple
                                   onchange="updateFileStatus()">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1">
                                    Haga clic o arrastre archivos aquí para subirlos
                                </p>
                                <p class="text-xs text-gray-500">
                                    Formatos permitidos: PDF, XLSX, XLS, JPG, JPEG, PNG
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información de archivos seleccionados -->
                        <div id="file-list" class="mt-3 text-xs text-gray-600 hidden">
                            <p class="font-medium mb-1">Archivos seleccionados:</p>
                            <ul id="file-names" class="space-y-1"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Botones responsive -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                <button type="button" 
                        onclick="clearForm()"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm border border-[#404041] text-[#404041] font-medium rounded-lg hover:bg-[#404041] hover:text-white transition-all duration-200">
                    Limpiar formulario
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm bg-[#404041] text-white font-medium rounded-lg hover:bg-[#2a2a2a] transition-all duration-200">
                    Guardar registro
                </button>
            </div>
        </div>
    </div>

    <!-- Script para manejo de archivos -->
    <script>
        function updateFileStatus() {
            const fileInput = document.getElementById('file-input');
            const files = fileInput.files;
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            
            let pdfCount = 0;
            let excelCount = 0;
            let photoCount = 0;
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            
            // Contar archivos por tipo y mostrar nombres
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Agregar a la lista
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center';
                listItem.innerHTML = `
                    <ion-icon name="document-outline" class="text-gray-400 mr-2"></ion-icon>
                    ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                `;
                fileNames.appendChild(listItem);
                
                if (extension === 'pdf') {
                    pdfCount++;
                } else if (extension === 'xlsx' || extension === 'xls') {
                    excelCount++;
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    photoCount++;
                }
            }
            
            // Mostrar/ocultar lista de archivos
            if (files.length > 0) {
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
                element.className = 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded';
            } else if (currentCount > 0) {
                element.textContent = typeName === 'Fotografías' ? `${currentCount}/${requiredCount}` : 'Incompleto';
                element.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
            } else {
                element.textContent = typeName === 'Fotografías' ? `0/${requiredCount}` : 'Pendiente';
                element.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
            }
        }
        
        function clearForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                document.querySelector('form').reset();
                // Resetear estados de archivos
                document.getElementById('pdf-status').textContent = 'Pendiente';
                document.getElementById('pdf-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
                document.getElementById('photos-status').textContent = '0/4';
                document.getElementById('photos-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
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
                fileInput.files = files;
                updateFileStatus();
            }
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection