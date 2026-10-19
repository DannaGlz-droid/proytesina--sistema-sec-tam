@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de seguridad vial' : 'Registrar reporte de seguridad vial')
@section('content')
    @include('components.header-admin')
    @include('components.nav-reportes')

    <x-ui.form.page
        :title="isset($publication) ? 'Editar reporte de seguridad vial' : 'Registrar reporte de seguridad vial'"
        :description="isset($publication) ? 'Actualiza la información del reporte y conserva la trazabilidad de sus archivos.' : 'Captura la información general, la actividad y los archivos de respaldo del reporte.'"
        :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'seguridad_vial')])"
        back-label="Volver a reportes"
        :prefer-history-back="true"
        page-class="report-road-form-page report-road-safety-form-page"
        card-class="report-road-form-card"
    >
        @php
            $validationMessages = collect($errors->getMessages());
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
            $selectedActivity = old('activity_type_id', isset($report) ? $report->activity_type_id : '');
            $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : '');
            $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : '');
        @endphp

        <form id="seguridadVialForm" class="report-road-form ui-form-fields"
              action="{{ isset($publication) ? route('reportes.seguridad-vial.update', $publication) : route('reportes.seguridad-vial.store') }}"
              method="POST" enctype="multipart/form-data" novalidate data-ui-inline-validation>
            @csrf
            @if(isset($publication)) @method('PUT') @endif

            <x-ui.form.section title="Información general" icon="far fa-file-alt" variant="report-road"
                class="report-road-general" content-class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                <div>
                    <label for="tema">Tema <span class="text-red-600">*</span></label>
                    <input id="tema" type="text" name="tema" placeholder="Ej: Prevención de accidentes viales"
                           value="{{ old('tema', isset($publication) ? $publication->topic : '') }}" required minlength="3" maxlength="150"
                           data-validation-error-id="tema-error" data-validation-required-message="Capture el tema."
                           @error('tema') aria-invalid="true" aria-describedby="tema-error" @enderror>
                    <p id="tema-error" class="report-road-field-error ui-field-error {{ $errors->has('tema') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('tema')) data-server-error @endif role="alert">{{ $errors->first('tema') }}</p>
                </div>
                <div>
                    <label for="activity_type_id">Tipo de actividad <span class="text-red-600">*</span></label>
                    <select id="activity_type_id" name="activity_type_id" class="tomselect-select" required
                            data-select-variant="form" data-placeholder="Seleccione el tipo de actividad"
                            data-validation-required-message="Seleccione el tipo de actividad." data-validation-error-id="activity-type-error"
                            @error('activity_type_id') aria-invalid="true" aria-describedby="activity-type-error" @enderror>
                        <option value="">Seleccione el tipo de actividad</option>
                        @foreach($activityTypes as $activityType)
                            <option value="{{ $activityType->id }}" {{ (string) $selectedActivity === (string) $activityType->id ? 'selected' : '' }}>{{ $activityType->name }}</option>
                        @endforeach
                    </select>
                    <p id="activity-type-error" class="report-road-field-error ui-field-error {{ $errors->has('activity_type_id') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('activity_type_id')) data-server-error @endif role="alert">{{ $errors->first('activity_type_id') }}</p>
                </div>
                <div>
                    <label for="fecha">Fecha de la actividad <span class="text-red-600">*</span></label>
                    <input id="fecha" type="date" name="fecha"
                           value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}" required max="{{ date('Y-m-d') }}"
                           data-validation-error-id="fecha-error" data-validation-required-message="Seleccione la fecha de la actividad."
                           data-validation-number-message="La fecha no puede ser futura."
                           @error('fecha') aria-invalid="true" aria-describedby="fecha-error" @enderror>
                    <p id="fecha-error" class="report-road-field-error ui-field-error {{ $errors->has('fecha') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('fecha')) data-server-error @endif role="alert">{{ $errors->first('fecha') }}</p>
                </div>
                <div>
                    <label for="participantes">Participantes <span class="text-red-600">*</span></label>
                    <input id="participantes" type="number" name="participantes" placeholder="Ej: 25"
                           value="{{ old('participantes', isset($report) ? $report->participants : '') }}" required min="1" max="9999" inputmode="numeric"
                           data-validation-error-id="participantes-error" data-validation-required-message="Capture el número de participantes."
                           data-validation-number-message="Capture un número entero entre 1 y 9999."
                           @error('participantes') aria-invalid="true" aria-describedby="participantes-error" @enderror>
                    <p id="participantes-error" class="report-road-field-error ui-field-error {{ $errors->has('participantes') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('participantes')) data-server-error @endif role="alert">{{ $errors->first('participantes') }}</p>
                </div>
                <div>
                    <label for="lugar">Lugar <span class="text-red-600">*</span></label>
                    <input id="lugar" type="text" name="lugar" placeholder="Ej: Auditorio municipal"
                           value="{{ old('lugar', isset($report) ? $report->location : '') }}" required minlength="3" maxlength="180"
                           data-validation-error-id="lugar-error" data-validation-required-message="Capture el lugar de la actividad."
                           @error('lugar') aria-invalid="true" aria-describedby="lugar-error" @enderror>
                    <p id="lugar-error" class="report-road-field-error ui-field-error {{ $errors->has('lugar') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('lugar')) data-server-error @endif role="alert">{{ $errors->first('lugar') }}</p>
                </div>
                <div>
                    <label for="promotor">Promotor <span class="text-red-600">*</span></label>
                    <input id="promotor" type="text" name="promotor" placeholder="Ej: Secretaría de Salud"
                           value="{{ old('promotor', isset($report) ? $report->promoter : '') }}" required minlength="3" maxlength="180"
                           data-validation-error-id="promotor-error" data-validation-required-message="Capture el promotor de la actividad."
                           @error('promotor') aria-invalid="true" aria-describedby="promotor-error" @enderror>
                    <p id="promotor-error" class="report-road-field-error ui-field-error {{ $errors->has('promotor') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('promotor')) data-server-error @endif role="alert">{{ $errors->first('promotor') }}</p>
                </div>
                <div>
                    <div class="report-road-field-label-row report-road-municipality-label-row">
                        <label for="seguridad_municipality_select">Municipio <span class="text-red-600">*</span></label>
                        @if($canSelectAnyDistrict ?? false)
                            <button id="clear-district-filter-vial" type="button" class="report-road-clear-district-filter"
                                    aria-controls="seguridad_municipality_select jurisdiction_select_vial"
                                    aria-label="Quitar el filtro de distrito y mostrar todos los municipios" title="Quitar filtro de distrito" hidden>Mostrar todos</button>
                        @endif
                    </div>
                    <select id="seguridad_municipality_select" name="municipio" class="tomselect-select" required
                            data-select-variant="form" data-placeholder="Seleccione un municipio"
                            data-validation-required-message="Seleccione un municipio." data-validation-error-id="municipio-error"
                            @error('municipio') aria-invalid="true" aria-describedby="municipio-error" @enderror>
                        <option value="">Seleccione un municipio</option>
                        @if($selectedMunicipio)
                            @php $selectedMunicipalityModel = $municipalities->firstWhere('id', $selectedMunicipio); @endphp
                            @if($selectedMunicipalityModel)<option value="{{ $selectedMunicipalityModel->id }}" selected>{{ $selectedMunicipalityModel->name }}</option>@endif
                        @endif
                    </select>
                    <p id="municipio-error" class="report-road-field-error ui-field-error {{ $errors->has('municipio') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('municipio')) data-server-error @endif role="alert">{{ $errors->first('municipio') }}</p>
                </div>
                <div>
                    @if($canSelectAnyDistrict ?? false)
                        <div class="report-road-field-label-row"><label for="jurisdiction_select_vial">Distrito <span class="text-red-600">*</span></label></div>
                        <select id="jurisdiction_select_vial" name="jurisdiccion" class="tomselect-select" required
                                data-select-variant="form" data-placeholder="Seleccione un distrito"
                                data-validation-required-message="Seleccione un distrito." data-validation-error-id="jurisdiccion-error"
                                @error('jurisdiccion') aria-invalid="true" aria-describedby="jurisdiccion-error" @enderror>
                            <option value="">Seleccione un distrito</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ (string) $selectedDistrito === (string) $district->id ? 'selected' : '' }}>{{ $district->display_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="statistics-form-label-row">
                            <label for="jurisdiction_display_vial">Distrito asignado</label>
                            <x-ui.form.help-tooltip id="vial-assigned-district-help" label="Cómo se asigna el distrito">Se asigna automáticamente según el distrito de tu cuenta.</x-ui.form.help-tooltip>
                        </div>
                        <input type="hidden" id="jurisdiction_input_vial" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                        <input id="jurisdiction_display_vial" type="text" class="ui-field--disabled" value="{{ optional(auth()->user()->district)->display_name }}" disabled aria-disabled="true">
                    @endif
                    <p id="jurisdiccion-error" class="report-road-field-error ui-field-error {{ $errors->has('jurisdiccion') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('jurisdiccion')) data-server-error @endif role="alert">{{ $errors->first('jurisdiccion') }}</p>
                </div>
            </x-ui.form.section>

            <x-ui.form.section title="Descripción" icon="far fa-clipboard" variant="report-road" class="report-road-description" content-class="space-y-3">
                <div>
                    <label for="descripcion">Descripción de la actividad</label>
                    <textarea id="descripcion" name="descripcion" class="description-scroll" rows="4" maxlength="5000"
                              placeholder="Agregue contexto, resultados u observaciones relevantes" data-validation-error-id="descripcion-error"
                              @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                    <p id="descripcion-error" class="report-road-field-error ui-field-error {{ $errors->has('descripcion') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('descripcion')) data-server-error @endif role="alert">{{ $errors->first('descripcion') }}</p>
                </div>
            </x-ui.form.section>

            <x-ui.form.section title="Carga de archivos" icon="fas fa-cloud-upload-alt" variant="report-road" class="report-road-files" content-class="space-y-4">
                @if(isset($publication) && $publication->files->count() > 0)
                    <section class="report-road-file-collection report-road-file-collection--mosaic report-road-file-collection--existing report-road-file-collection--columns-3" aria-labelledby="existing-files-title">
                        <div class="report-road-file-collection-header">
                            <div><h3 id="existing-files-title">Archivos actuales</h3><p>{{ $publication->files->count() }} {{ $publication->files->count() === 1 ? 'archivo guardado' : 'archivos guardados' }}.</p></div>
                            <button id="select-all-existing-files" type="button" class="report-road-file-text-action" onclick="toggleAllExistingFiles()" aria-pressed="false">Marcar todos para eliminar</button>
                        </div>
                        <ul class="report-road-file-rows report-road-file-mosaic" id="existing-files-list">
                            @foreach($publication->files as $file)
                                @php
                                    $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                    $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);
                                    $fileIcon = match($fileType) { 'pdf' => 'far fa-file-pdf', 'excel' => 'far fa-file-excel', default => 'far fa-image' };
                                @endphp
                                <li class="report-road-file-row report-road-file-card file-item" data-file-id="{{ $file->id }}" data-file-type="{{ $fileType }}" data-file-name="{{ $file->original_name }}" data-file-size="{{ $file->file_size }}">
                                    <label class="report-road-file-checkbox-hitbox inline-flex" title="Marcar {{ $file->original_name }} para eliminar">
                                        <input type="checkbox" class="file-delete-checkbox report-road-file-checkbox" onchange="toggleFileStrikethrough(this)" aria-label="Marcar {{ $file->original_name }} para eliminar">
                                        <span class="report-road-file-checkbox-visual" aria-hidden="true"><i class="fas fa-check"></i></span>
                                    </label>
                                    <span class="report-road-file-card-preview {{ $fileType === 'photos' ? 'report-road-file-card-preview--photo' : 'report-road-file-card-preview--document' }}">
                                        @if($fileType === 'photos')<img src="{{ route('reportes.file.preview', $file) }}" alt="Vista previa de {{ $file->original_name }}" loading="lazy">@else<i class="{{ $fileIcon }}" aria-hidden="true"></i>@endif
                                    </span>
                                    <div class="report-road-file-info">
                                        <p class="report-road-file-name"><a href="{{ route('reportes.file.preview', $file) }}" class="report-road-file-preview-link" target="_blank" rel="noopener" title="Ver archivo" aria-label="Ver archivo {{ $file->original_name }}">{{ $file->original_name }}</a></p>
                                        <p class="report-road-file-meta"><span class="report-road-file-format">{{ strtoupper($extension) }}</span><span>{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</span></p>
                                        <p class="report-road-file-removal-state">Se eliminará al guardar</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <div class="report-road-file-requirements report-road-file-requirements--triple" aria-label="Requisitos de archivos">
                    @foreach([
                        ['pdf', 'far fa-file-pdf', 'Documento PDF', 'Mínimo 1 archivo · PDF'],
                        ['excel', 'far fa-file-excel', 'Hoja de cálculo', 'Mínimo 1 archivo · XLS o XLSX'],
                        ['photos', 'far fa-image', 'Fotografías', 'Mínimo 4 archivos · JPG, JPEG o PNG'],
                    ] as [$type, $icon, $title, $detail])
                        <div class="report-road-file-requirement">
                            <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                            <span class="report-road-file-requirement-copy"><strong>{{ $title }}</strong><small>{{ $detail }}</small></span>
                            <span id="{{ $type }}-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                        </div>
                    @endforeach
                </div>

                <div class="report-road-file-uploader">
                    <div class="report-road-file-uploader-heading"><h3 id="file-upload-title">@if(isset($publication)) Agregar nuevos archivos (opcional) @else Subir archivos <span class="text-red-600">*</span> @endif</h3></div>
                    <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                        <input type="file" id="file-input" name="archivos[]" class="hidden" accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png" multiple onchange="addFiles(this.files)" aria-describedby="file-error">
                        <span class="report-road-upload-zone-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                        <div class="report-road-upload-zone-copy">
                            <p><button id="choose-files-button" type="button" aria-describedby="file-error">Seleccionar archivos</button><span> o arrástralos aquí</span></p>
                            <div class="report-road-upload-zone-details"><small>Formatos: PDF, XLSX, XLS, JPG, JPEG o PNG</small><small>Máx. 10 MB por archivo · Puedes seleccionar varios</small></div>
                        </div>
                    </div>
                    <section id="file-list" class="report-road-file-collection report-road-file-collection--mosaic hidden" aria-labelledby="selected-files-title">
                        <div class="report-road-file-collection-header"><div><h3 id="selected-files-title">Archivos por agregar</h3><p id="selected-files-count">0 archivos seleccionados</p></div></div>
                        <div class="report-road-file-groups">
                            @foreach([
                                ['pdf', 'Documentos PDF', 'Aún no agregas documentos PDF.'],
                                ['excel', 'Hojas de cálculo', 'Aún no agregas hojas de cálculo.'],
                                ['photos', 'Fotografías', 'Aún no agregas fotografías.'],
                            ] as [$type, $title, $empty])
                                <section class="report-road-file-group" aria-labelledby="selected-{{ $type }}-title">
                                    <div class="report-road-file-group-heading"><h4 id="selected-{{ $type }}-title">{{ $title }}</h4><p id="selected-{{ $type }}-count">0 archivos</p></div>
                                    <ul id="selected-{{ $type === 'photos' ? 'photo' : $type }}-files" class="report-road-file-rows"></ul>
                                    <p id="selected-{{ $type }}-empty" class="report-road-file-group-empty">{{ $empty }}</p>
                                </section>
                            @endforeach
                        </div>
                    </section>
                    <div id="file-error" class="report-road-file-error {{ $fileErrors->isNotEmpty() ? '' : 'hidden' }}" role="alert">{{ $fileErrors->join(' ') }}</div>
                </div>
            </x-ui.form.section>

            @if(isset($publication))
                <x-form-buttons class="users-form-actions" primaryText="Actualizar registro" secondaryText="Restablecer cambios" primaryType="submit" secondaryType="button" secondaryOnclick="resetSeguridadVialEditForm(event)" />
            @else
                <x-form-buttons class="users-form-actions" primaryText="Guardar registro" secondaryText="Limpiar formulario" primaryType="submit" secondaryType="button" secondaryOnclick="clearSeguridadVialForm()" />
            @endif
            @if(isset($publication))<input type="hidden" id="files-to-delete" name="files_to_delete" value="">@endif
            <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'seguridad_vial') }}">
        </form>
    </x-ui.form.page>

    <script>
        let selectedFiles = [];
        const filesPendingEntryAnimation = new WeakSet();
        let selectedFilePreviewUrls = [];
        let roadSafetyValidationAttempted = false;

        function showFileError(message, focus = true) {
            const error = document.getElementById('file-error');
            if (!error) return;
            error.textContent = message;
            error.classList.remove('hidden');
            if (!focus) return;
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            error.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            document.getElementById('choose-files-button')?.focus();
        }

        function clearFileError() {
            const error = document.getElementById('file-error');
            if (error) { error.textContent = ''; error.classList.add('hidden'); }
        }

        function duplicateFileMessage(file) {
            const normalizedName = file.name.trim().toLowerCase();
            if (selectedFiles.some(item => item.name.trim().toLowerCase() === normalizedName && item.size === file.size)) {
                return `El archivo "${file.name}" ya está seleccionado.`;
            }
            const duplicateExisting = Array.from(document.querySelectorAll('#existing-files-list .file-item')).some(row => {
                const markedForRemoval = row.querySelector('.file-delete-checkbox')?.checked;
                return !markedForRemoval && (row.dataset.fileName || '').trim().toLowerCase() === normalizedName && Number(row.dataset.fileSize) === file.size;
            });
            return duplicateExisting
                ? `El archivo "${file.name}" ya está guardado. Márcalo para eliminar o reemplazar antes de volver a agregarlo.`
                : '';
        }

        function addFiles(files) {
            clearFileError();
            Array.from(files).forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['pdf', 'xlsx', 'xls', 'jpg', 'jpeg', 'png'].includes(extension)) {
                    showFileError('Formato no válido. Solo se permiten PDF, Excel (XLS o XLSX) y fotografías (JPG, JPEG o PNG).');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    showFileError(`El archivo ${file.name} excede el tamaño máximo permitido (10 MB).`);
                    return;
                }
                const duplicateMessage = duplicateFileMessage(file);
                if (duplicateMessage) { showFileError(duplicateMessage); return; }
                selectedFiles.push(file);
                filesPendingEntryAnimation.add(file);
            });
            updateFileDisplay();
            updateFileCounters();
            document.getElementById('file-input').value = '';
        }

        function removeFile(index, trigger) {
            clearFileError();
            const file = selectedFiles[index];
            const row = trigger?.closest('.report-road-file-card');
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            let finished = false;
            const finishRemoval = () => {
                if (finished) return;
                finished = true;
                const currentIndex = selectedFiles.indexOf(file);
                if (currentIndex !== -1) selectedFiles.splice(currentIndex, 1);
                updateFileDisplay();
                updateFileCounters();
            };
            if (!row || reduceMotion) { finishRemoval(); return; }
            row.classList.add('report-road-file-card--removing');
            row.addEventListener('animationend', finishRemoval, { once: true });
            window.setTimeout(finishRemoval, 220);
        }

        function updateFilesToDeleteInput() {
            const input = document.getElementById('files-to-delete');
            if (!input) return;
            input.value = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked'))
                .map(box => box.closest('.file-item')?.dataset.fileId).filter(Boolean).join(',');
        }

        function syncSelectAllExistingFiles() {
            const button = document.getElementById('select-all-existing-files');
            const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            if (!button || !boxes.length) return;
            const all = boxes.every(box => box.checked);
            button.textContent = all ? 'Desmarcar todos' : 'Marcar todos para eliminar';
            button.setAttribute('aria-pressed', all ? 'true' : 'false');
        }

        function toggleAllExistingFiles() {
            const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            const checked = !boxes.every(box => box.checked);
            boxes.forEach(box => { box.checked = checked; toggleFileStrikethrough(box); });
            syncSelectAllExistingFiles();
        }

        function toggleFileStrikethrough(box) {
            const row = box.closest('.file-item');
            if (box.checked) row.dataset.markedForRemoval = 'true'; else delete row.dataset.markedForRemoval;
            updateFilesToDeleteInput();
            updateFileCounters();
            syncSelectAllExistingFiles();
        }

        function setFileBadge(type, count, minimum) {
            const badge = document.getElementById(`${type}-status-badge`);
            if (!badge) return;
            const complete = count >= minimum;
            badge.textContent = complete ? 'Completado' : 'Pendiente';
            badge.dataset.status = complete ? 'complete' : 'pending';
        }

        function getRoadSafetyFileCounts() {
            let pdf = 0, excel = 0, photos = 0;
            document.querySelectorAll('#existing-files-list .file-item').forEach(row => {
                if (row.querySelector('.file-delete-checkbox')?.checked) return;
                if (row.dataset.fileType === 'pdf') pdf++;
                else if (row.dataset.fileType === 'excel') excel++;
                else if (row.dataset.fileType === 'photos') photos++;
            });
            selectedFiles.forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (extension === 'pdf') pdf++;
                else if (['xlsx', 'xls'].includes(extension)) excel++;
                else if (['jpg', 'jpeg', 'png'].includes(extension)) photos++;
            });
            return { pdf, excel, photos };
        }

        function updateFileCounters() {
            const { pdf, excel, photos } = getRoadSafetyFileCounts();
            setFileBadge('pdf', pdf, 1);
            setFileBadge('excel', excel, 1);
            setFileBadge('photos', photos, 4);
        }

        function validateRoadSafetyFiles(focus = false) {
            const { pdf, excel, photos } = getRoadSafetyFileCounts();
            if (pdf >= 1 && excel >= 1 && photos >= 4) { clearFileError(); return true; }
            showFileError(`El reporte requiere como mínimo: documento PDF (${pdf}/1), archivo Excel (${excel}/1), fotografías (${photos}/4).`, focus);
            return false;
        }

        function selectedFileType(extension) {
            if (extension === 'pdf') return 'pdf';
            if (['xlsx', 'xls'].includes(extension)) return 'excel';
            return 'photos';
        }

        function updateFileDisplay() {
            const collection = document.getElementById('file-list');
            const count = document.getElementById('selected-files-count');
            const lists = {
                pdf: document.getElementById('selected-pdf-files'),
                excel: document.getElementById('selected-excel-files'),
                photos: document.getElementById('selected-photo-files')
            };
            if (!collection || Object.values(lists).some(list => !list)) return;
            Object.values(lists).forEach(list => { list.innerHTML = ''; });
            selectedFilePreviewUrls.forEach(url => URL.revokeObjectURL(url));
            selectedFilePreviewUrls = [];
            const counts = { pdf: 0, excel: 0, photos: 0 };

            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                const type = selectedFileType(extension);
                const isPhoto = type === 'photos';
                const icon = type === 'pdf' ? 'far fa-file-pdf' : 'far fa-file-excel';
                const row = document.createElement('li');
                row.className = 'report-road-file-row report-road-file-card';
                if (filesPendingEntryAnimation.has(file)) {
                    row.classList.add('report-road-file-card--entering');
                    filesPendingEntryAnimation.delete(file);
                }
                row.innerHTML = `<span class="report-road-file-card-preview ${isPhoto ? 'report-road-file-card-preview--photo' : 'report-road-file-card-preview--document'}">${isPhoto ? '<img alt="">' : `<i class="${icon}" aria-hidden="true"></i>`}</span><div class="report-road-file-info"><p class="report-road-file-name"></p><p class="report-road-file-meta"><span class="report-road-file-format">${extension.toUpperCase()}</span><span>${(file.size / 1024 / 1024).toFixed(2)} MB</span></p></div><button type="button" class="report-road-file-remove" onclick="removeFile(${index}, this)" title="Quitar archivo"><i class="fas fa-times" aria-hidden="true"></i></button>`;
                const fileNameElement = row.querySelector('.report-road-file-name');
                fileNameElement.textContent = file.name;
                fileNameElement.title = file.name;
                row.querySelector('.report-road-file-remove').setAttribute('aria-label', `Quitar ${file.name}`);
                if (isPhoto) {
                    const previewUrl = URL.createObjectURL(file);
                    selectedFilePreviewUrls.push(previewUrl);
                    const image = row.querySelector('.report-road-file-card-preview img');
                    image.src = previewUrl;
                    image.alt = `Vista previa de ${file.name}`;
                }
                lists[type].appendChild(row);
                counts[type]++;
            });

            if (count) count.textContent = selectedFiles.length === 1 ? '1 archivo seleccionado' : `${selectedFiles.length} archivos seleccionados`;
            [['pdf', 'selected-pdf-count', 'selected-pdf-empty'], ['excel', 'selected-excel-count', 'selected-excel-empty'], ['photos', 'selected-photos-count', 'selected-photos-empty']]
                .forEach(([type, counterId, emptyId]) => {
                    const counter = document.getElementById(counterId);
                    if (counter) counter.textContent = counts[type] === 1 ? '1 archivo' : `${counts[type]} archivos`;
                    document.getElementById(emptyId)?.toggleAttribute('hidden', counts[type] > 0);
                });
            collection.classList.toggle('hidden', selectedFiles.length === 0);
            document.getElementById('file-drop-zone')?.toggleAttribute('data-has-files', selectedFiles.length > 0);
        }

        async function clearSeguridadVialForm() {
            const form = document.getElementById('seguridadVialForm');
            const canClear = window.confirmFormClear ? await window.confirmFormClear(form, selectedFiles.length) : false;
            if (!canClear) return;
            roadSafetyValidationAttempted = false;
            form?.reset();
            selectedFiles = [];
            updateFileDisplay(); updateFileCounters(); clearFileError();
            await window.resetSeguridadVialTomSelects?.();
            form?.querySelectorAll('[data-ui-error-slot]').forEach(error => {
                error.removeAttribute('data-server-error'); error.textContent = ''; error.classList.add('hidden');
            });
            window.showToast?.('Formulario limpiado.', 'info', 2400);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const drop = document.getElementById('file-drop-zone');
            const input = document.getElementById('file-input');
            document.getElementById('choose-files-button')?.addEventListener('click', () => input?.click());
            if (drop && input) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(name => drop.addEventListener(name, event => { event.preventDefault(); event.stopPropagation(); }));
                ['dragenter', 'dragover'].forEach(name => drop.addEventListener(name, () => drop.dataset.dragActive = 'true'));
                ['dragleave', 'drop'].forEach(name => drop.addEventListener(name, () => delete drop.dataset.dragActive));
                drop.addEventListener('drop', event => addFiles(event.dataTransfer.files));
            }
            updateFileCounters();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const municipalityToDistrict = @json($municipalities->mapWithKeys(fn($item) => [$item->id => $item->district_id]));
            const districtNames = @json($districts->mapWithKeys(fn($item) => [$item->id => $item->display_name]));
            const currentDistrict = @json(optional(auth()->user())->district_id);
            const canSelectAnyDistrict = @json($canSelectAnyDistrict ?? false);
            const activityType = document.getElementById('activity_type_id');
            const municipality = document.getElementById('seguridad_municipality_select');
            const district = document.getElementById('jurisdiction_select_vial');
            const districtDisplay = document.getElementById('jurisdiction_display_vial');
            const hiddenDistrict = document.getElementById('jurisdiction_input_vial');
            const clearFilter = document.getElementById('clear-district-filter-vial');
            const form = document.getElementById('seguridadVialForm');
            let loadId = 0;
            const isEdit = Boolean(form?.querySelector('input[name="_method"][value="PUT"]'));
            const navigationState = { submitting: false, navigating: false, confirming: false };

            if (!form || !window.AppFilterSelect) return;

            function selectError(select) { return document.getElementById(select?.dataset.validationErrorId || ''); }
            function clearSelectError(select) {
                if (!select) return;
                select.removeAttribute('aria-invalid');
                const error = selectError(select);
                if (!error) return;
                const describedBy = (select.getAttribute('aria-describedby') || '').split(/\s+/).filter(id => id && id !== error.id);
                if (describedBy.length) select.setAttribute('aria-describedby', describedBy.join(' '));
                else select.removeAttribute('aria-describedby');
                error.removeAttribute('data-server-error');
                error.textContent = '';
                error.classList.add('hidden');
            }
            function validateSelect(select, message) {
                if (!select || !select.required || select.value) { clearSelectError(select); return true; }
                const error = selectError(select);
                select.setAttribute('aria-invalid', 'true');
                if (error) {
                    error.textContent = message;
                    error.classList.remove('hidden');
                    select.setAttribute('aria-describedby', error.id);
                }
                return false;
            }
            function selectedDistrict() {
                if (!canSelectAnyDistrict && currentDistrict) return String(currentDistrict);
                return canSelectAnyDistrict && district ? String(district.value || '') : '';
            }
            function fetchMunicipalities(query) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(query) + '&limit=100';
                const selected = selectedDistrict();
                if (selected) url += '&district_id=' + encodeURIComponent(selected);
                return fetch(url).then(response => {
                    if (!response.ok) throw new Error('No se pudieron cargar los municipios.');
                    return response.json();
                }).then(items => items.map(item => ({ value: String(item.id), text: item.name })));
            }
            async function loadMunicipalities(open = false, valueToRestore = '') {
                if (!municipality?.tomselect) return;
                const requestId = ++loadId;
                const requested = selectedDistrict();
                try {
                    const items = await fetchMunicipalities('');
                    if (requestId !== loadId || requested !== selectedDistrict()) return;
                    const ts = municipality.tomselect;
                    ts.clearOptions();
                    items.forEach(item => ts.addOption(item));
                    if (valueToRestore) ts.setValue(String(valueToRestore), true);
                    ts.refreshOptions(open);
                    if (open) ts.open();
                } catch (error) {
                    if (requestId !== loadId) return;
                    const municipalityError = selectError(municipality);
                    municipality?.setAttribute('aria-invalid', 'true');
                    if (municipalityError) {
                        municipalityError.textContent = 'No se pudieron cargar los municipios. Inténtalo nuevamente.';
                        municipalityError.classList.remove('hidden');
                        municipality?.setAttribute('aria-describedby', municipalityError.id);
                    }
                }
            }
            function updateClearFilter() { if (clearFilter && district) clearFilter.hidden = !district.value; }
            function resetMunicipality(open = false) {
                const ts = municipality?.tomselect;
                if (!ts) { if (municipality) municipality.value = ''; return; }
                ts.clear(true); municipality.value = ''; ts.setTextboxValue(''); ts.clearOptions(); ts.clearCache?.(); loadMunicipalities(open);
            }

            window.AppFilterSelect.init(activityType, {
                searchable: activityType.options.length > 10,
                maxOptions: 100,
                onChange: () => roadSafetyValidationAttempted
                    ? validateSelect(activityType, 'Seleccione el tipo de actividad.')
                    : clearSelectError(activityType)
            });
            if (canSelectAnyDistrict && district) {
                window.AppFilterSelect.init(district, {
                    searchable: true, maxOptions: 100,
                    onChange: () => {
                        if (roadSafetyValidationAttempted) validateSelect(district, 'Seleccione un distrito.'); else clearSelectError(district);
                        clearSelectError(municipality); resetMunicipality(false); updateClearFilter();
                    }
                });
                updateClearFilter();
            }
            clearFilter?.addEventListener('click', () => {
                district.tomselect?.clear(true); district.value = '';
                clearSelectError(district); clearSelectError(municipality); updateClearFilter(); resetMunicipality(true);
            });
            if (municipality) {
                window.AppFilterSelect.init(municipality, {
                    searchable: true, preload: true, maxOptions: 100,
                    load: function(query, callback) {
                        const requested = selectedDistrict();
                        fetchMunicipalities(query).then(items => callback(requested === selectedDistrict() ? items : [])).catch(() => callback());
                    },
                    onChange: () => {
                        if (roadSafetyValidationAttempted) validateSelect(municipality, 'Seleccione un municipio.'); else clearSelectError(municipality);
                        const districtId = municipalityToDistrict[municipality.value];
                        if (districtId && canSelectAnyDistrict && district) {
                            district.tomselect?.setValue(String(districtId), true); clearSelectError(district); updateClearFilter();
                            loadMunicipalities(false, municipality.value);
                        }
                    }
                });
            }
            if (!canSelectAnyDistrict && currentDistrict) {
                if (hiddenDistrict) hiddenDistrict.value = currentDistrict;
                if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || districtDisplay.value;
            }

            const initialActivity = String(activityType?.value || '');
            const initialDistrict = String(district?.value || '');
            const initialMunicipality = String(municipality?.value || '');
            window.resetSeguridadVialTomSelects = async function(values = {}) {
                const activityValue = String(values.activity ?? '');
                const districtValue = String(values.district ?? '');
                const municipalityValue = String(values.municipality ?? '');
                activityType?.tomselect?.setValue(activityValue, true);
                if (activityType) activityType.value = activityValue;
                const municipalitySelect = municipality?.tomselect;
                if (municipalitySelect) { municipalitySelect.clear(true); municipalitySelect.setTextboxValue(''); }
                if (municipality) municipality.value = '';
                if (canSelectAnyDistrict && district) {
                    district.tomselect?.setValue(districtValue, true); district.value = districtValue; updateClearFilter();
                }
                await loadMunicipalities(false, municipalityValue);
                clearSelectError(activityType); clearSelectError(municipality); clearSelectError(district);
                if (!canSelectAnyDistrict && currentDistrict) {
                    if (hiddenDistrict) hiddenDistrict.value = currentDistrict;
                    if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || '';
                }
            };

            const getFormSnapshot = () => JSON.stringify(Array.from(new FormData(form).entries())
                .filter(([name]) => !['_token', '_method', 'archivos[]'].includes(name))
                .map(([name, value]) => [name, String(value)]));
            const initialSnapshot = getFormSnapshot();
            const hasUnsavedChanges = () => getFormSnapshot() !== initialSnapshot || selectedFiles.length > 0;

            async function confirmDiscardChanges(options) {
                if (!hasUnsavedChanges()) return true;
                if (navigationState.confirming) return false;
                navigationState.confirming = true;
                try {
                    if (typeof window.confirmDialog !== 'function') return window.confirm(`${options.question}\n\n${options.description}`);
                    return await window.confirmDialog({ title: 'Descartar cambios', question: options.question, description: options.description, confirmText: options.confirmText, cancelText: 'Continuar editando', variant: 'warning' });
                } finally { navigationState.confirming = false; }
            }

            window.resetSeguridadVialEditForm = async function(event) {
                event?.preventDefault();
                const hadChanges = hasUnsavedChanges();
                const shouldReset = await confirmDiscardChanges({
                    question: '¿Deseas restablecer los cambios?',
                    description: 'Los campos y archivos volverán al estado con el que abriste esta edición.',
                    confirmText: 'Restablecer'
                });
                if (!shouldReset) return;
                roadSafetyValidationAttempted = false;
                form.reset();
                selectedFiles = [];
                document.querySelectorAll('#existing-files-list .file-delete-checkbox').forEach(box => { box.checked = false; toggleFileStrikethrough(box); });
                updateFilesToDeleteInput(); updateFileDisplay(); clearFileError();
                form.querySelectorAll('[data-ui-error-slot]').forEach(error => {
                    error.removeAttribute('data-server-error'); error.textContent = ''; error.classList.add('hidden');
                });
                await window.resetSeguridadVialTomSelects({ activity: initialActivity, district: initialDistrict, municipality: initialMunicipality });
                updateFileCounters();
                if (hadChanges) window.AppToast?.info ? window.AppToast.info('Cambios restablecidos.') : window.showToast?.('Cambios restablecidos.', 'info', 4000);
            };

            form.addEventListener('submit', function(event) {
                roadSafetyValidationAttempted = true;
                const activityValid = validateSelect(activityType, 'Seleccione el tipo de actividad.');
                const municipalityValid = validateSelect(municipality, 'Seleccione un municipio.');
                const districtValid = validateSelect(canSelectAnyDistrict ? district : null, 'Seleccione un distrito.');
                updateFilesToDeleteInput();
                const filesValid = validateRoadSafetyFiles(false);

                if (selectedFiles.length) {
                    const transfer = new DataTransfer();
                    selectedFiles.forEach(file => transfer.items.add(file));
                    document.getElementById('file-input').files = transfer.files;
                }
                if (!activityValid || !municipalityValid || !districtValid || !filesValid) {
                    event.preventDefault();
                    const firstInvalid = Array.from(form.querySelectorAll('input, select, textarea')).find(field => {
                        if (field.disabled || field.type === 'hidden' || field.type === 'file') return false;
                        if ([activityType, municipality, district].includes(field)) return !field.value;
                        return field.willValidate && !field.validity.valid;
                    });
                    window.requestAnimationFrame(() => {
                        if (firstInvalid?.tomselect) firstInvalid.tomselect.focus();
                        else if (firstInvalid) firstInvalid.focus({ preventScroll: true });
                        else if (!filesValid) showFileError(document.getElementById('file-error')?.textContent || 'Complete los archivos requeridos.');
                    });
                    return;
                }
                queueMicrotask(() => { if (!event.defaultPrevented) navigationState.submitting = true; });
            }, true);

            document.addEventListener('click', async function(event) {
                const link = event.target.closest('a[href]');
                if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download') || !hasUnsavedChanges()) return;
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                const destination = new URL(link.href, window.location.href);
                if (destination.origin !== window.location.origin) return;
                event.preventDefault();
                const shouldLeave = await confirmDiscardChanges({
                    question: isEdit ? '¿Deseas salir de la edición?' : '¿Deseas salir del registro?',
                    description: isEdit ? 'Los cambios realizados no se guardarán.' : 'Los datos capturados no se guardarán.',
                    confirmText: 'Salir sin guardar'
                });
                if (!shouldLeave) return;
                navigationState.navigating = true;
                if (typeof window.navigateBackOrVisit === 'function') window.navigateBackOrVisit(destination.href);
                else window.location.assign(destination.href);
            }, true);

            window.addEventListener('beforeunload', function(event) {
                if (navigationState.submitting || navigationState.navigating || !hasUnsavedChanges()) return;
                event.preventDefault();
                event.returnValue = '';
            });
        });
    </script>
@endsection
