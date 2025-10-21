@extends('layouts.principal')
@section('title', 'Registro de Observatorio de Lesiones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Registro de observatorio de lesiones</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para registrar los datos del observatorio de lesiones.</p>

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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Tema *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: Análisis de lesiones por accidentes"
                                   value="{{ old('tema') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Municipio *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="municipio">
                                <option value="">Seleccione un municipio</option>
                                <option value="monterrey">Monterrey</option>
                                <option value="sanpedro">San Pedro Garza García</option>
                                <option value="guadalupe">Guadalupe</option>
                                <option value="apodaca">Apodaca</option>
                                <option value="sannicolas">San Nicolás de los Garza</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Fecha *</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200"
                                   value="{{ old('fecha') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Jurisdicción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="jurisdiccion">
                                <option value="">Seleccione una jurisdicción</option>
                                <option value="centro">Jurisdicción Centro</option>
                                <option value="norte">Jurisdicción Norte</option>
                                <option value="sur">Jurisdicción Sur</option>
                                <option value="este">Jurisdicción Este</option>
                                <option value="oeste">Jurisdicción Oeste</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Carga de archivo -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="cloud-upload-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Carga de archivo</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-4">
                    <!-- Cuadro para archivo Excel que abarca todo el ancho -->
                    <div class="p-4 border border-gray-300 rounded-lg bg-white">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                <span class="text-sm font-medium text-[#404041]">Hoja de Cálculo</span>
                            </div>
                            <span id="excel-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pendiente</span>
                        </div>
                        <p class="text-xs text-gray-600">Formato: XLSX (obligatorio)</p>
                    </div>

                    <!-- Área de carga de archivo -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2">Subir archivo *</label>
                        
                        <!-- Cuadro punteado para arrastrar y soltar -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#404041] transition-colors duration-200 bg-gray-50">
                            <input type="file" 
                                   id="file-input"
                                   class="hidden"
                                   accept=".xlsx,.xls"
                                   onchange="updateFileStatus()">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1">
                                    Haga clic o arrastre el archivo aquí para subirlo
                                </p>
                                <p class="text-xs text-gray-500">
                                    Formatos permitidos: XLSX, XLS
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
                        <div id="file-list" class="mt-3 text-xs text-gray-600 hidden">
                            <p class="font-medium mb-1">Archivo seleccionado:</p>
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
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            
            // Verificar archivo seleccionado
            if (files.length > 0) {
                const file = files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Validar que sea archivo Excel
                if (extension === 'xlsx' || extension === 'xls') {
                    // Agregar a la lista
                    const listItem = document.createElement('li');
                    listItem.className = 'flex items-center';
                    listItem.innerHTML = `
                        <ion-icon name="document-outline" class="text-gray-400 mr-2"></ion-icon>
                        ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                    `;
                    fileNames.appendChild(listItem);
                    
                    // Actualizar estado
                    document.getElementById('excel-status').textContent = 'Completado';
                    document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded';
                    
                    fileList.classList.remove('hidden');
                } else {
                    // Archivo no válido
                    alert('Por favor seleccione un archivo Excel (XLSX o XLS)');
                    fileInput.value = '';
                    document.getElementById('excel-status').textContent = 'Pendiente';
                    document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
                    fileList.classList.add('hidden');
                }
            } else {
                // No hay archivo seleccionado
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
                fileList.classList.add('hidden');
            }
        }
        
        function clearForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                document.querySelector('form').reset();
                // Resetear estado del archivo
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded';
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
                
                // Solo permitir un archivo
                if (files.length === 1) {
                    fileInput.files = files;
                    updateFileStatus();
                } else if (files.length > 1) {
                    alert('Solo se permite subir un archivo a la vez');
                }
            }
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection