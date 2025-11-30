@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar Observatorio de Lesiones' : 'Registro de Observatorio de Lesiones')
@section('content')

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">
            {{ isset($publication) ? 'Editar observatorio de lesiones' : 'Registro de observatorio de lesiones' }}
        </h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para {{ isset($publication) ? 'actualizar' : 'registrar' }} los datos del observatorio de lesiones.</p>

        <!-- Cuadro del formulario responsive -->
        <form id="observatorioForm" action="{{ isset($publication) ? route('reportes.observatorio.update', $publication) : route('reportes.observatorio.store') }}" method="POST" enctype="multipart/form-data">
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
                                   placeholder="Ej: Análisis de lesiones por accidentes"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio <span class="text-red-600">*</span></label>
                            <select id="death_municipality_select" name="municipio" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" required>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción <span class="text-red-600">*</span></label>
                            <input type="hidden" id="jurisdiction_input" name="jurisdiccion" value="{{ old('jurisdiccion', isset($report) ? $report->jurisdiction_id : '') }}" required>
                            <input id="jurisdiction_display" type="text" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" value="{{ isset($report) && $report->jurisdiction ? $report->jurisdiction->name : 'Pendiente (seleccione municipio)' }}" readonly>
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
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" rows="4" placeholder="Describa los detalles, contexto, objetivos, resultados, etc. (opcional)" maxlength="5000">{{ old('descripcion', isset($publication) ? $publication->description : '') }}</textarea>
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
                                                                                <!-- Form oculto para eliminar archivo (moverá los formularios fuera del form principal) -->
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
                    secondaryOnclick="clearObservatorioLesionesForm()"
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
        fileNames.innerHTML = '';
        if (files.length > 0) {
            const file = files[0];
            const extension = file.name.split('.').pop().toLowerCase();
            if (extension === 'xlsx' || extension === 'xls') {
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
                document.getElementById('excel-status').textContent = 'Completado';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-800 rounded font-lora';
                fileList.classList.remove('hidden');
            } else {
                alert('Por favor seleccione un archivo Excel (XLSX o XLS)');
                fileInput.value = '';
                document.getElementById('excel-status').textContent = 'Pendiente';
                document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                fileList.classList.add('hidden');
            }
        } else {
            document.getElementById('excel-status').textContent = 'Pendiente';
            document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
            fileList.classList.add('hidden');
        }
    }

    function clearSelectedFile() {
        document.getElementById('file-input').value = '';
        updateFileStatus();
    }

    function clearObservatorioLesionesForm() {
        if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
            document.querySelector('form').reset();
            const desc = document.getElementById('descripcion');
            if (desc) desc.value = '';
            const jurisdictionDisplay = document.getElementById('jurisdiction_display');
            const hiddenJur = document.getElementById('jurisdiction_input');
            if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
            if (hiddenJur) hiddenJur.value = '';
            document.getElementById('excel-status').textContent = 'Pendiente';
            document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
            document.getElementById('file-list').classList.add('hidden');
        }
    }

    // Drag & drop
    document.addEventListener('DOMContentLoaded', function() {
        const dropArea = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('file-input');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        function highlight() { dropArea.classList.add('border-[#404041]', 'bg-blue-50'); }
        function unhighlight() { dropArea.classList.remove('border-[#404041]', 'bg-blue-50'); }
        dropArea.addEventListener('drop', handleDrop, false);
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
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

    <!-- Tom Select CDN -->
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

            const deathMuni = document.getElementById('death_municipality_select');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display');
            const hiddenJur = document.getElementById('jurisdiction_input');

            function setJurisdictionBasedOnMunicipality() {
                const mid = deathMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[jid] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            }

            if (deathMuni) {
                deathMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }

            window.clearObservatorioLesionesForm = function() {
                if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                    document.querySelector('form').reset();
                    // Vaciar descripción (si existe) para asegurar que quede en blanco
                    const desc = document.getElementById('descripcion');
                    if (desc) desc.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                    if (hiddenJur) hiddenJur.value = '';
                    document.getElementById('excel-status').textContent = 'Pendiente';
                    document.getElementById('excel-status').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                    document.getElementById('file-list').classList.add('hidden');
                }
            }

            // Initialize Tom Select for municipality
            function fetchMunicipalities(q) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(q);
                if (currentJurisdiction) {
                    url += '&jurisdiction_id=' + encodeURIComponent(currentJurisdiction);
                }
                return fetch(url).then(r => r.json());
            }

            if (deathMuni) {
                const ts = new TomSelect(deathMuni, {
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
                        deathMuni.dispatchEvent(evt);
                    }
                });
                try { deathMuni.style.display = 'none'; } catch (e) {}
            }

            // Si el usuario tiene una jurisdicción asignada, establecerla en el campo oculto y en el display
            if (currentJurisdiction) {
                if (hiddenJur && !hiddenJur.value) hiddenJur.value = currentJurisdiction;
                if (jurisdictionDisplay && (!jurisdictionDisplay.value || jurisdictionDisplay.value.includes('Pendiente'))) {
                    jurisdictionDisplay.value = jurisNames[currentJurisdiction] || jurisdictionDisplay.value;
                }
            }

            // Validación en el cliente: si estamos en modo creación, exigir archivo Excel
            const mainForm = document.getElementById('observatorioForm') || document.querySelector('form[action*="observatorio"][method="POST"]');
            const fileInput = document.getElementById('file-input');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};

            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    // Solo validar en modo CREACIÓN
                    if (!isEditMode) {
                        // Si no hay archivo seleccionado, evitar envío y mostrar mensaje
                        if (!fileInput || fileInput.files.length === 0) {
                            e.preventDefault();
                            // Mensaje uniforme con Seguridad Vial
                            alert('Debe incluir al menos 1 archivo Excel (XLSX).');
                            try { document.getElementById('file-input').focus(); } catch (err) {}
                            return false;
                        }
                        // Validar extensión y tamaño de forma básica (cliente)
                        if (fileInput && fileInput.files.length > 0) {
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
                    }
                });
            }
        });
    </script>
@endsection