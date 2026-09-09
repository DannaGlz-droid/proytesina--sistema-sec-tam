@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de seguridad vial' : 'Registrar reporte de seguridad vial')
@section('content')

    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="report-road-form-page users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            :title="isset($publication) ? 'Editar reporte de seguridad vial' : 'Registrar reporte de seguridad vial'"
            :description="isset($publication) ? 'Actualiza la información del reporte y conserva la trazabilidad de sus archivos.' : 'Captura la información general, la actividad y los archivos de respaldo del reporte.'"
            :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'seguridad_vial')])"
            back-label="Volver a publicaciones"
            :prefer-history-back="true"
        />

        <!-- Los mensajes transitorios de sesión se muestran desde el componente global de toast. -->

        @php
            $validationMessages = collect($errors->getMessages());
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
        @endphp

        <!-- Cuadro del formulario responsive -->
        <div class="report-road-form-card users-form-card">
        <form id="seguridadVialForm" class="report-road-form ui-form-fields" action="{{ isset($publication) ? route('reportes.seguridad-vial.update', $publication) : route('reportes.seguridad-vial.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($publication))
                @method('PUT')
            @endif
            
            <!-- Sección 1: Información general -->
            <div class="report-road-section report-road-general mb-6 lg:mb-8">
                <div class="report-road-section-heading flex items-center mb-4">
                    <i class="far fa-file-alt" aria-hidden="true"></i>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información general</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tema <span class="text-red-600">*</span></label>
                        <input id="tema" type="text"
                               name="tema"
                               aria-invalid="{{ $errors->has('tema') ? 'true' : 'false' }}"
                               @error('tema') aria-describedby="tema-error" @enderror
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                               placeholder="Ej: Prevención de accidentes viales"
                               value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                               required minlength="3" maxlength="150">
                        @error('tema') <p id="tema-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="activity_type_id" class="block">Tipo de actividad <span class="text-red-600">*</span></label>
                        <select id="activity_type_id" class="tomselect-select" name="activity_type_id" required aria-invalid="{{ $errors->has('activity_type_id') ? 'true' : 'false' }}" @error('activity_type_id') aria-describedby="activity-type-error" @enderror>
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
                        @error('activity_type_id') <p id="activity-type-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de la actividad <span class="text-red-600">*</span></label>
                        <input id="fecha" type="date"
                               name="fecha"
                               aria-invalid="{{ $errors->has('fecha') ? 'true' : 'false' }}"
                               @error('fecha') aria-describedby="fecha-error" @enderror
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                               value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                               required max="{{ date('Y-m-d') }}">
                        @error('fecha') <p id="fecha-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Participantes <span class="text-red-600">*</span></label>
                        <input id="participantes" type="number"
                               name="participantes"
                               aria-invalid="{{ $errors->has('participantes') ? 'true' : 'false' }}"
                               @error('participantes') aria-describedby="participantes-error" @enderror
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                               placeholder="Ej: 25"
                               value="{{ old('participantes', isset($report) ? $report->participants : '') }}"
                               required min="1" max="9999">
                        @error('participantes') <p id="participantes-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar <span class="text-red-600">*</span></label>
                        <input id="lugar" type="text"
                               name="lugar"
                               aria-invalid="{{ $errors->has('lugar') ? 'true' : 'false' }}"
                               @error('lugar') aria-describedby="lugar-error" @enderror
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                               placeholder="Ej: Auditorio municipal"
                               value="{{ old('lugar', isset($report) ? $report->location : '') }}"
                               required minlength="3" maxlength="180">
                        @error('lugar') <p id="lugar-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Promotor <span class="text-red-600">*</span></label>
                        <input id="promotor" type="text"
                               name="promotor"
                               aria-invalid="{{ $errors->has('promotor') ? 'true' : 'false' }}"
                               @error('promotor') aria-describedby="promotor-error" @enderror
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                               placeholder="Ej: Secretaría de Salud"
                               value="{{ old('promotor', isset($report) ? $report->promoter : '') }}"
                               required minlength="3" maxlength="180">
                        @error('promotor') <p id="promotor-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="report-road-municipality-label-row">
                            <label for="seguridad_municipality_select" class="block">Municipio <span class="text-red-600">*</span></label>
                            @if($canSelectAnyDistrict ?? false)
                                <button
                                    id="clear-district-filter-vial"
                                    type="button"
                                    class="report-road-clear-district-filter"
                                    aria-controls="seguridad_municipality_select jurisdiction_select_vial"
                                    aria-label="Quitar el filtro de distrito y mostrar todos los municipios"
                                    title="Quitar filtro de distrito"
                                    hidden
                                >Mostrar todos</button>
                            @endif
                        </div>
                        <select id="seguridad_municipality_select" name="municipio" class="tomselect-select" required aria-invalid="{{ $errors->has('municipio') ? 'true' : 'false' }}" @error('municipio') aria-describedby="municipio-error" @enderror>
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
                        @error('municipio') <p id="municipio-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        @if($canSelectAnyDistrict ?? false)
                            <label for="jurisdiction_select_vial" class="block">Distrito <span class="text-red-600">*</span></label>
                            <!-- Oficina Central: selector de distritos territoriales vigentes. -->
                            @php
                                $selectedDistritoVial = old('jurisdiccion', isset($report) ? $report->district_id : '');
                            @endphp
                            <select id="jurisdiction_select_vial" name="jurisdiccion" class="tomselect-select" required aria-invalid="{{ $errors->has('jurisdiccion') ? 'true' : 'false' }}" @error('jurisdiccion') aria-describedby="jurisdiccion-error" @enderror>
                                <option value="">Seleccione un distrito</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ (string) $selectedDistritoVial === (string) $district->id ? 'selected' : '' }}>{{ $district->display_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <label class="block text-xs lg:text-sm font-medium text-gray-500 mb-1 font-lora">Distrito asignado</label>
                            <!-- Adscripción territorial: valor fijo, visible y no editable. -->
                            <input type="hidden" id="jurisdiction_input_vial" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                            <input id="jurisdiction_display_vial" type="text" class="ui-field--disabled" value="{{ optional(auth()->user()->district)->display_name }}" disabled aria-disabled="true">
                        @endif
                        @error('jurisdiccion') <p id="jurisdiccion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="report-road-legacy-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Descripción -->
            <div class="report-road-section report-road-description mb-6 lg:mb-8">
                <div class="report-road-section-heading flex items-center mb-4">
                    <i class="far fa-clipboard" aria-hidden="true"></i>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Descripción</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción de la actividad</label>
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            aria-invalid="{{ $errors->has('descripcion') ? 'true' : 'false' }}"
                            @error('descripcion') aria-describedby="descripcion-error" @enderror
                            class="w-full px-3 py-2 text-xs lg:text-sm border border-[#404041] rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora break-words whitespace-normal description-scroll" 
                            rows="4"
                            placeholder="Agregue contexto, resultados u observaciones relevantes"
                            maxlength="5000"
                        >{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                        @error('descripcion') <p id="descripcion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="report-road-legacy-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Carga de archivos -->
            <div class="report-road-section report-road-files mb-6 lg:mb-8">
                <div class="report-road-section-heading flex items-center mb-4">
                    <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Carga de archivos</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-4">
                    <!-- Archivos existentes (solo en modo edición) -->
                    @if(isset($publication) && $publication->files->count() > 0)
                        <section class="report-road-file-collection" aria-labelledby="existing-files-title">
                            <div class="report-road-file-collection-header">
                                <div>
                                    <h3 id="existing-files-title">Archivos actuales</h3>
                                    <p>{{ $publication->files->count() }} {{ $publication->files->count() === 1 ? 'archivo guardado' : 'archivos guardados' }}. Marca únicamente los que deseas eliminar o reemplazar.</p>
                                </div>
                                <button id="select-all-existing-files" type="button" class="report-road-file-text-action" onclick="toggleAllExistingFiles()" aria-pressed="false">
                                    Seleccionar todos
                                </button>
                            </div>
                                <ul class="report-road-file-rows" id="existing-files-list">
                                    @foreach($publication->files as $file)
                                        @php
                                            $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                            $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);
                                            $fileIconClass = match($extension) {
                                                'pdf' => 'far fa-file-pdf',
                                                'xls', 'xlsx' => 'far fa-file-excel',
                                                'jpg', 'jpeg', 'png' => 'far fa-image',
                                                default => 'far fa-file'
                                            };
                                        @endphp
                                        <li class="report-road-file-row file-item"
                                            data-file-id="{{ $file->id }}" 
                                            data-file-type="{{ $fileType }}">
                                            <input
                                                type="checkbox"
                                                class="file-delete-checkbox report-road-file-checkbox"
                                                onchange="toggleFileStrikethrough(this)"
                                                aria-label="Marcar {{ $file->original_name }} para eliminar o reemplazar"
                                            >
                                            <span class="report-road-file-icon" aria-hidden="true">
                                                <i class="{{ $fileIconClass }}"></i>
                                            </span>
                                            <div class="report-road-file-info">
                                                <p class="report-road-file-name">{{ $file->original_name }}</p>
                                                <p class="report-road-file-meta">
                                                    <span class="report-road-file-format">{{ strtoupper($extension) }}</span>
                                                    <span>{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</span>
                                                </p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                        </section>
                    @endif

                    <div class="report-road-file-requirements" aria-label="Requisitos de archivos">
                        <div class="report-road-file-requirement">
                            <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-pdf"></i></span>
                            <span class="report-road-file-requirement-copy">
                                <strong>Documento PDF</strong>
                                <small>1 archivo · PDF</small>
                            </span>
                            <span id="pdf-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                        </div>

                        <div class="report-road-file-requirement">
                            <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                            <span class="report-road-file-requirement-copy">
                                <strong>Hoja de cálculo</strong>
                                <small>1 archivo · XLSX</small>
                            </span>
                            <span id="excel-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                        </div>

                        <div class="report-road-file-requirement">
                            <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-image"></i></span>
                            <span class="report-road-file-requirement-copy">
                                <strong>Fotografías</strong>
                                <small>4 archivos · JPG o PNG</small>
                            </span>
                            <span id="photos-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">0/4</span>
                        </div>
                    </div>

                    <!-- Área de carga de archivos -->
                    <div class="report-road-file-uploader">
                        <div class="report-road-file-uploader-heading">
                            <h3 id="file-upload-title">
                            @if(isset($publication))
                                Agregar nuevos archivos (opcional)
                            @else
                                Subir archivos (selección múltiple) <span class="text-red-600">*</span>
                            @endif
                            </h3>
                            <p>Máximo 10 MB por archivo.</p>
                        </div>

                        <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                            <input type="file" 
                                   id="file-input"
                                   name="archivos[]"
                                   class="hidden"
                                   accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png"
                                   multiple
                                   onchange="addFiles(this.files)">

                            <span class="report-road-upload-zone-icon" aria-hidden="true">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </span>
                            <div class="report-road-upload-zone-copy">
                                <p><button id="choose-files-button" type="button">Seleccionar archivos</button><span> o arrástralos aquí</span></p>
                                <small>PDF, XLSX, XLS, JPG, JPEG o PNG · selección múltiple</small>
                            </div>
                        </div>
                        
                        <!-- Información de archivos seleccionados -->
                        <div id="file-error" class="report-road-file-error hidden" role="alert"></div>

                        @if($fileErrors->isNotEmpty())
                            <div class="report-road-file-error" role="alert">
                                @foreach($fileErrors as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <section id="file-list" class="report-road-file-collection hidden" aria-labelledby="selected-files-title">
                            <div class="report-road-file-collection-header">
                                <div>
                                    <h3 id="selected-files-title">Archivos por agregar</h3>
                                    <p id="selected-files-count">0 archivos seleccionados</p>
                                </div>
                            </div>
                            <ul id="file-names" class="report-road-file-rows"></ul>
                        </section>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="report-road-legacy-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES -->
            <div class="report-road-form-actions">
                @if(isset($publication) || request()->is('reportes/*/*/edit'))
                    <x-form-buttons
                        primaryText="Actualizar registro"
                        secondaryText=""
                        primaryType="submit"
                    />
                @else
                    <x-form-buttons
                        primaryText="Guardar registro"
                        secondaryText="Limpiar formulario"
                        primaryType="submit"
                        secondaryType="button"
                        secondaryOnclick="clearSeguridadVialForm()"
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
        </div>

    </div>

    <!-- Script para manejo de archivos -->
    <script>
        // Array para almacenar todos los archivos seleccionados
        let selectedFiles = [];
        
        function showFileError(message) {
            const fileError = document.getElementById('file-error');
            const chooseFilesButton = document.getElementById('choose-files-button');
            if (!fileError) return;

            fileError.textContent = message;
            fileError.classList.remove('hidden');
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            fileError.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            try { chooseFilesButton?.focus(); } catch (err) {}
        }

        function clearFileError() {
            const fileError = document.getElementById('file-error');
            if (!fileError) return;

            fileError.textContent = '';
            fileError.classList.add('hidden');
        }
        
        // Función para inicializar contadores de archivos existentes
        function initializeFileCounters() {
            const existingFilesList = document.getElementById('existing-files-list');
            
            if (!existingFilesList) {
                return; // No hay archivos existentes en modo creación
            }
            
            let pdfCount = 0;
            let excelCount = 0;
            let photoCount = 0;
            
            // Contar archivos existentes por tipo
            existingFilesList.querySelectorAll('.file-item').forEach(item => {
                const fileType = item.dataset.fileType;
                
                if (fileType === 'pdf') {
                    pdfCount++;
                } else if (fileType === 'excel') {
                    excelCount++;
                } else if (fileType === 'photos') {
                    photoCount++;
                }
            });
            
            // Actualizar badges con los conteos de archivos existentes
            updateCounterDisplay('pdf', pdfCount, 1);
            updateCounterDisplay('excel', excelCount, 1);
            updateCounterDisplay('photos', photoCount, 4);
        }
        
        function addFiles(newFiles) {
            clearFileError();
            for (let i = 0; i < newFiles.length; i++) {
                const file = newFiles[i];
                const extension = file.name.split('.').pop().toLowerCase();
                const allowedExtensions = ['pdf', 'xlsx', 'xls', 'jpg', 'jpeg', 'png'];
                const maxBytes = 10 * 1024 * 1024;

                if (!allowedExtensions.includes(extension)) {
                    showFileError('Formato no válido. Solo se permiten PDF, Excel (XLSX, XLS) y fotos (JPG, JPEG, PNG).');
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

            updateFileStatus();
            updateFileCounters();
            document.getElementById('file-input').value = '';
        }
        
        function removeFile(index) {
            clearFileError();
            selectedFiles.splice(index, 1);
            updateFileStatus();
            updateFileCounters();
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
            
            if (checkbox.checked) {
                fileItem.dataset.markedForRemoval = 'true';
            } else {
                delete fileItem.dataset.markedForRemoval;
            }
            
            // Actualizar contador y lista de archivos a eliminar
            updateFileCounters();
            
            updateFilesToDeleteInput();
            syncSelectAllExistingFiles();
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
            let status = 'pending';
            
            if (currentCount >= requiredCount) {
                statusText = 'Completado';
                status = 'complete';
            } else if (currentCount > 0) {
                statusText = type === 'photos' ? `${currentCount}/${requiredCount}` : 'Incompleto';
                status = 'partial';
            } else {
                statusText = type === 'photos' ? `${currentCount}/${requiredCount}` : 'Pendiente';
            }
            
            badge.textContent = statusText;
            badge.dataset.status = status;
        }
        
        function updateFileStatus() {
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            const selectedFilesCount = document.getElementById('selected-files-count');
            
            let pdfCount = 0;
            let excelCount = 0;
            let photoCount = 0;
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            
            // Contar archivos por tipo y mostrar nombres
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Determinar icono según el tipo
                let iconClass = 'far fa-file';
                let fileTypeLabel = extension.toUpperCase();
                
                if (extension === 'pdf') {
                    pdfCount++;
                    iconClass = 'far fa-file-pdf';
                } else if (extension === 'xlsx' || extension === 'xls') {
                    excelCount++;
                    iconClass = 'far fa-file-excel';
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    photoCount++;
                    iconClass = 'far fa-image';
                }
                
                // Agregar a la lista con botón de eliminar
                const listItem = document.createElement('li');
                listItem.className = 'report-road-file-row';
                listItem.innerHTML = `
                    <span class="report-road-file-icon" aria-hidden="true">
                        <i class="${iconClass}"></i>
                    </span>
                    <div class="report-road-file-info">
                        <p class="report-road-file-name"></p>
                        <p class="report-road-file-meta">
                            <span class="report-road-file-format">${fileTypeLabel}</span>
                            <span>${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                        </p>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="report-road-file-remove" aria-label="Quitar archivo" title="Quitar archivo">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                `;
                listItem.querySelector('.report-road-file-name').textContent = file.name;
                listItem.querySelector('.report-road-file-remove').setAttribute('aria-label', `Quitar ${file.name}`);
                fileNames.appendChild(listItem);
            });

            if (selectedFilesCount) {
                selectedFilesCount.textContent = selectedFiles.length === 1
                    ? '1 archivo seleccionado'
                    : `${selectedFiles.length} archivos seleccionados`;
            }
            
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
        
        async function clearSeguridadVialForm() {
            const form = document.getElementById('seguridadVialForm');
            const canClear = window.confirmFormClear
                ? await window.confirmFormClear(form, selectedFiles.length)
                : false;
            if (canClear) {
                if (form) {
                    form.reset();
                }
                // Limpiar archivos seleccionados
                selectedFiles = [];
                updateFileStatus();

                document.querySelectorAll('#existing-files-list .file-item').forEach(item => {
                    delete item.dataset.markedForRemoval;
                });
                updateFilesToDeleteInput();
                syncSelectAllExistingFiles();
                updateFileCounters();
                
                if (typeof window.resetSeguridadVialTomSelects === 'function') {
                    window.resetSeguridadVialTomSelects();
                }

                if (typeof window.showToast === 'function') {
                    window.showToast('Formulario limpiado.', 'info', 2400);
                }
            }
        }
        
        // Funcionalidad de arrastrar y soltar
        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('file-drop-zone');
            const fileInput = document.getElementById('file-input');
            const chooseFilesButton = document.getElementById('choose-files-button');

            if (!dropArea || !fileInput) return;

            chooseFilesButton?.addEventListener('click', function() {
                fileInput.click();
            });
            
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
                dropArea.dataset.dragActive = 'true';
            }
            
            function unhighlight() {
                delete dropArea.dataset.dragActive;
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
                    // Detectar modo edición por la presencia del campo _method=PUT
                    const isEditMode = !!this.querySelector('input[name="_method"][value="PUT"]');
                    
                    if (!isEditMode && selectedFiles.length === 0) {
                        e.preventDefault();
                        showFileError('El reporte requiere: documento PDF (0/1), archivo Excel (0/1), fotografías (0/4).');
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
                                showFileError('Debe incluir al menos 1 archivo PDF.');
                                return false;
                            }
                            
                            if (excelCount < 1) {
                                e.preventDefault();
                                showFileError('Debe incluir al menos 1 archivo Excel (XLSX).');
                                return false;
                            }
                            
                            if (photoCount < 4) {
                                e.preventDefault();
                                showFileError(`Debe incluir 4 fotografías. Actualmente tiene ${photoCount} foto(s).`);
                                return false;
                            }
                        }
                        
                        // Asignar los archivos seleccionados al input file existente
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
            
            // Inicializar contadores de archivos existentes en modo edición
            initializeFileCounters();
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map municipality_id -> district_id
            const muniToJur = @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->district_id]; }));
            const jurisNames = @json($districts->mapWithKeys(function($j){ return [$j->id => $j->display_name]; }));
            // Distrito del usuario (puede ser null)
            const currentJurisdiction = @json(optional(auth()->user())->district_id);
            const canSelectAnyDistrict = @json($canSelectAnyDistrict ?? false);

            const seguridadMuni = document.getElementById('seguridad_municipality_select');
            const activityTypeSelect = document.getElementById('activity_type_id');
            const jurisdictionSelect = document.getElementById('jurisdiction_select_vial');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display_vial');
            const hiddenJur = document.getElementById('jurisdiction_input_vial');
            const clearDistrictFilterButton = document.getElementById('clear-district-filter-vial');
            let municipalityOptionsLoadId = 0;

            // Variables para restaurar old() después de error de validación
            const oldJurisdiccion = '{{ old('jurisdiccion', '') }}';
            const oldMunicipio = '{{ old('municipio', '') }}';

            if (activityTypeSelect) {
                new TomSelect(activityTypeSelect, {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: [],
                    create: false,
                    maxItems: 1,
                    preload: false,
                    maxOptions: 10
                });

                activityTypeSelect.addEventListener('change', function() {
                    clearTomSelectError(activityTypeSelect);
                });
            }

            function setJurisdictionBasedOnMunicipality() {
                const mid = seguridadMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (canSelectAnyDistrict && jurisdictionSelect) {
                        // Para Oficina Central: actualizar el select del distrito en modo silencioso
                        // para no disparar onChange del distrito que limpia el municipio (bucle circular)
                        if (jurisdictionSelect.tomselect) {
                            jurisdictionSelect.tomselect.setValue(String(jid), true);
                            clearTomSelectError(jurisdictionSelect);
                        } else {
                            jurisdictionSelect.value = jid;
                        }
                        loadMunicipalityOptions(false);
                        updateDistrictFilterAction();
                    }
                } else {
                    if (canSelectAnyDistrict && jurisdictionSelect) {
                        return;
                    }
                }
            }

            // Para Oficina Central: inicializar Tom Select para Distrito
            if (canSelectAnyDistrict && jurisdictionSelect) {
                const districtTs = new TomSelect(jurisdictionSelect, {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: ['text'],
                    create: false,
                    maxItems: 1,
                    preload: false,
                    maxOptions: 50
                });
                // Restaurar old('jurisdiccion') después de error de validación
                if (oldJurisdiccion) {
                    const districtName = jurisNames[oldJurisdiccion];
                    if (districtName) {
                        districtTs.addOption({ value: String(oldJurisdiccion), text: districtName });
                        districtTs.setValue(String(oldJurisdiccion), true);
                    }
                } else if (jurisdictionSelect.value) {
                    districtTs.setValue(String(jurisdictionSelect.value), true);
                }

                updateDistrictFilterAction();
            }

            function updateDistrictFilterAction() {
                if (!clearDistrictFilterButton || !jurisdictionSelect) return;

                clearDistrictFilterButton.hidden = !jurisdictionSelect.value;
            }

            function resetMunicipalityForDistrict(openDropdown = false) {
                if (!seguridadMuni?.tomselect) {
                    if (seguridadMuni) seguridadMuni.value = '';
                    return;
                }

                const tomSelect = seguridadMuni.tomselect;
                tomSelect.clear(true);
                seguridadMuni.value = '';
                tomSelect.setTextboxValue('');
                tomSelect.clearOptions();
                if (typeof tomSelect.clearCache === 'function') {
                    tomSelect.clearCache();
                }
                loadMunicipalityOptions(openDropdown);
            }

            // Para Oficina Central: cuando cambia el distrito, filtrar municipios
            if (canSelectAnyDistrict && jurisdictionSelect) {
                jurisdictionSelect.addEventListener('change', function() {
                    clearTomSelectError(jurisdictionSelect);
                    clearTomSelectError(seguridadMuni);
                    resetMunicipalityForDistrict(false);
                    updateDistrictFilterAction();
                });
            }

            if (clearDistrictFilterButton && jurisdictionSelect) {
                clearDistrictFilterButton.addEventListener('click', function() {
                    if (jurisdictionSelect.tomselect) {
                        jurisdictionSelect.tomselect.clear(true);
                        jurisdictionSelect.tomselect.setTextboxValue('');
                        jurisdictionSelect.tomselect.refreshItems();
                    }
                    jurisdictionSelect.value = '';

                    clearTomSelectError(jurisdictionSelect);
                    clearTomSelectError(seguridadMuni);
                    updateDistrictFilterAction();
                    resetMunicipalityForDistrict(true);
                });
            }

            if (seguridadMuni) {
                seguridadMuni.addEventListener('change', function() {
                    clearTomSelectError(seguridadMuni);
                    setJurisdictionBasedOnMunicipality();
                });
                // Solo llamar en carga inicial si NO hay old values (para no sobrescribir distrito)
                if (!oldJurisdiccion && !oldMunicipio) {
                    setJurisdictionBasedOnMunicipality();
                }
            }

            // Initialize Tom Select for municipality
            function currentMunicipalityDistrictFilter() {
                if (!canSelectAnyDistrict && currentJurisdiction) {
                    return String(currentJurisdiction);
                }

                return canSelectAnyDistrict && jurisdictionSelect
                    ? String(jurisdictionSelect.value || '')
                    : '';
            }

            function fetchMunicipalities(q) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(q) + '&limit=100';
                const selectedDistrict = currentMunicipalityDistrictFilter();
                if (selectedDistrict) {
                    url += '&district_id=' + encodeURIComponent(selectedDistrict);
                }
                return fetch(url)
                    .then(r => r.json())
                    .then(items => items.map(item => ({
                        value: String(item.id),
                        text: item.name,
                    })));
            }

            function loadMunicipalityOptions(openDropdown = false) {
                if (!seguridadMuni?.tomselect) return;

                const tomSelect = seguridadMuni.tomselect;
                const loadId = ++municipalityOptionsLoadId;
                const requestedDistrict = currentMunicipalityDistrictFilter();
                fetchMunicipalities('').then(items => {
                    if (loadId !== municipalityOptionsLoadId || requestedDistrict !== currentMunicipalityDistrictFilter()) {
                        return;
                    }

                    tomSelect.clearOptions();
                    items.forEach(item => tomSelect.addOption(item));
                    tomSelect.refreshOptions(openDropdown);
                    if (openDropdown) tomSelect.open();
                }).catch(() => {});
            }

            function reloadMunicipalityOptions() {
                if (!seguridadMuni) return;

                if (seguridadMuni.tomselect) {
                    const ts = seguridadMuni.tomselect;
                    ts.clear(true);
                    seguridadMuni.value = '';
                    ts.clearOptions();
                    if (typeof ts.clearCache === 'function') {
                        ts.clearCache();
                    }
                    ts.setTextboxValue('');
                    ts.refreshOptions(false);
                } else {
                    seguridadMuni.value = '';
                }
            }

            function getTomSelectWrapper(select) {
                return select?.tomselect?.wrapper || select?.nextElementSibling || null;
            }

            function getTomSelectValidityInput(select) {
                return select?.tomselect?.control_input || select?.tomselect?.input || select || null;
            }

            function showTomSelectError(select, message) {
                if (!select) return;

                const validityInput = getTomSelectValidityInput(select);

                if (validityInput) {
                    validityInput.setCustomValidity(message);
                }
            }

            function clearTomSelectError(select) {
                if (!select) return;

                const validityInput = getTomSelectValidityInput(select);

                if (validityInput) {
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

            function validateSeguridadVialTomSelects(focusFirst = false) {
                const fields = [
                    { select: activityTypeSelect, message: 'Seleccione un tipo de actividad.' },
                    { select: seguridadMuni, message: 'Seleccione un municipio.' },
                    { select: canSelectAnyDistrict ? jurisdictionSelect : null, message: 'Seleccione un distrito.' },
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

            window.resetSeguridadVialTomSelects = function() {
                if (activityTypeSelect?.tomselect) {
                    activityTypeSelect.tomselect.clear(true);
                    activityTypeSelect.value = '';
                    activityTypeSelect.tomselect.setTextboxValue('');
                    activityTypeSelect.tomselect.refreshItems();
                }

                if (canSelectAnyDistrict && jurisdictionSelect) {
                    if (jurisdictionSelect.tomselect) {
                        jurisdictionSelect.tomselect.clear(true);
                        jurisdictionSelect.value = '';
                        jurisdictionSelect.tomselect.setTextboxValue('');
                        jurisdictionSelect.tomselect.refreshItems();
                    } else {
                        jurisdictionSelect.value = '';
                    }
                    updateDistrictFilterAction();
                }

                reloadMunicipalityOptions();

                if (!canSelectAnyDistrict) {
                    if (currentJurisdiction) {
                        if (hiddenJur) hiddenJur.value = currentJurisdiction;
                        if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[currentJurisdiction] || '';
                    }
                }

                clearTomSelectError(seguridadMuni);
                clearTomSelectError(activityTypeSelect);
                clearTomSelectError(jurisdictionSelect);
            };

            if (seguridadMuni) {
                const ts = new TomSelect(seguridadMuni, {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: ['text'],
                    create: false,
                    maxItems: 1,
                    preload: true,
                    maxOptions: 50,
                    load: function(query, callback) {
                        const requestedDistrict = currentMunicipalityDistrictFilter();
                        fetchMunicipalities(query).then(items => {
                            callback(requestedDistrict === currentMunicipalityDistrictFilter() ? items : []);
                        }).catch(() => callback());
                    }
                });

                if (oldMunicipio) {
                    ts.setValue(oldMunicipio, true);
                }
            }

            // La adscripción territorial siempre prevalece sobre valores anteriores del reporte o del formulario.
            if (!canSelectAnyDistrict && currentJurisdiction) {
                if (hiddenJur) hiddenJur.value = currentJurisdiction;
                if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[currentJurisdiction] || jurisdictionDisplay.value;
            }

            // Interceptar el envío del formulario para actualizar files_to_delete
            const mainForm = document.getElementById('seguridadVialForm');
            const isEditMode = {{ isset($publication) ? 'true' : 'false' }};
            
            if (mainForm) {
                mainForm.addEventListener('invalid', function(e) {
                    if (e.target === activityTypeSelect) {
                        e.preventDefault();
                        showTomSelectError(activityTypeSelect, 'Seleccione un tipo de actividad.');
                        reportTomSelectValidity(activityTypeSelect);
                    }

                    if (e.target === seguridadMuni) {
                        e.preventDefault();
                        showTomSelectError(seguridadMuni, 'Seleccione un municipio.');
                        reportTomSelectValidity(seguridadMuni);
                    }

                    if (e.target === jurisdictionSelect) {
                        e.preventDefault();
                        showTomSelectError(jurisdictionSelect, 'Seleccione un distrito.');
                        reportTomSelectValidity(jurisdictionSelect);
                    }
                }, true);

                mainForm.addEventListener('submit', function(e) {
                    if (!validateSeguridadVialTomSelects(true)) {
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

@endsection
