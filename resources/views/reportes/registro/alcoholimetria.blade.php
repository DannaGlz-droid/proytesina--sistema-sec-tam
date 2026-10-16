@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría')
@section('content')
    @include('components.header-admin')
    @include('components.nav-reportes')

    <x-ui.form.page
            :title="isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría'"
            :description="isset($publication) ? 'Actualiza los resultados del operativo y conserva la trazabilidad de sus archivos.' : 'Captura la información, los resultados del operativo y los archivos de respaldo.'"
            :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'alcoholimetria')])"
            back-label="Volver a publicaciones"
            :prefer-history-back="true"
            page-class="report-road-form-page report-alcohol-form-page"
            card-class="report-road-form-card"
        >

        @php
            $validationMessages = collect($errors->getMessages());
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
            $numericSections = [
                [
                    'title' => 'Operativos de alcoholimetría',
                    'icon' => 'fas fa-shield-alt',
                    'fields' => [
                        ['name' => 'puntos_revision', 'label' => 'Puntos de revisión instalados', 'placeholder' => 'Ej: 15', 'property' => 'checkpoints', 'max' => 9999],
                    ],
                ],
                [
                    'title' => 'Resultados por punto de revisión',
                    'icon' => 'fas fa-chart-bar',
                    'fields' => [
                        ['name' => 'pruebas_realizadas', 'label' => 'Pruebas realizadas', 'placeholder' => 'Ej: 250', 'property' => 'tests_performed', 'max' => 999999],
                        ['name' => 'conductores_no_aptos', 'label' => 'Conductores no aptos', 'placeholder' => 'Ej: 12', 'property' => 'drivers_not_fit', 'max' => 999999],
                    ],
                ],
                [
                    'title' => 'Conductores no aptos por género',
                    'icon' => 'fas fa-users',
                    'fields' => [
                        ['name' => 'mujeres_no_aptas', 'label' => 'Mujeres', 'placeholder' => 'Ej: 3', 'property' => 'women', 'max' => 999999],
                        ['name' => 'hombres_no_aptos', 'label' => 'Hombres', 'placeholder' => 'Ej: 9', 'property' => 'men', 'max' => 999999],
                    ],
                ],
                [
                    'title' => 'Conductores no aptos por tipo de vehículo',
                    'icon' => 'fas fa-car',
                    'fields' => [
                        ['name' => 'automoviles_camionetas', 'label' => 'Automóviles y camionetas', 'placeholder' => 'Ej: 8', 'property' => 'cars_trucks', 'max' => 999999],
                        ['name' => 'motocicletas', 'label' => 'Motocicletas', 'placeholder' => 'Ej: 2', 'property' => 'motorcycles', 'max' => 999999],
                        ['name' => 'transporte_colectivo', 'label' => 'Transporte público colectivo', 'placeholder' => 'Ej: 1', 'property' => 'public_transport_collective', 'max' => 999999],
                        ['name' => 'transporte_individual', 'label' => 'Transporte público individual', 'placeholder' => 'Ej: 0', 'property' => 'public_transport_individual', 'max' => 999999],
                        ['name' => 'transporte_carga', 'label' => 'Transporte de carga', 'placeholder' => 'Ej: 1', 'property' => 'cargo_transport', 'max' => 999999],
                        ['name' => 'vehiculos_emergencia', 'label' => 'Vehículos de emergencia', 'placeholder' => 'Ej: 0', 'property' => 'emergency_vehicles', 'max' => 999999],
                    ],
                ],
            ];
        @endphp

            <form id="alcoholimetriaForm" class="report-road-form ui-form-fields" action="{{ isset($publication) ? route('reportes.alcoholimetria.update', $publication) : route('reportes.alcoholimetria.store') }}" method="POST" enctype="multipart/form-data" novalidate data-ui-inline-validation>
                @csrf
                @if(isset($publication)) @method('PUT') @endif

                <x-ui.form.section title="Información general" icon="far fa-file-alt" variant="report-road"
                    class="report-road-general" content-class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                        <div>
                            <label for="tema">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" name="tema" placeholder="Ej: Operativo de alcoholimetría"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}" required minlength="3" maxlength="150"
                                   data-validation-error-id="tema-error" data-validation-required-message="Capture el tema."
                                   @error('tema') aria-invalid="true" aria-describedby="tema-error" @enderror>
                            <p id="tema-error" class="report-road-field-error ui-field-error {{ $errors->has('tema') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('tema')) data-server-error @endif role="alert">{{ $errors->first('tema') }}</p>
                        </div>
                        <div>
                            <label for="fecha">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" name="fecha"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}" required max="{{ date('Y-m-d') }}"
                                   data-validation-error-id="fecha-error" data-validation-required-message="Seleccione la fecha de la actividad." data-validation-number-message="La fecha no puede ser futura."
                                   @error('fecha') aria-invalid="true" aria-describedby="fecha-error" @enderror>
                            <p id="fecha-error" class="report-road-field-error ui-field-error {{ $errors->has('fecha') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('fecha')) data-server-error @endif role="alert">{{ $errors->first('fecha') }}</p>
                        </div>
                        <div>
                            <div class="report-road-municipality-label-row">
                                <label for="alcohol_municipality_select">Municipio <span class="text-red-600">*</span></label>
                                @if($canSelectAnyDistrict ?? false)
                                    <button id="clear-district-filter-alcohol" type="button" class="report-road-clear-district-filter"
                                            aria-controls="alcohol_municipality_select jurisdiction_select_alcohol"
                                            aria-label="Quitar el filtro de distrito y mostrar todos los municipios" title="Quitar filtro de distrito" hidden>Mostrar todos</button>
                                @endif
                            </div>
                            @php $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : ''); @endphp
                            <select id="alcohol_municipality_select" name="municipio" class="tomselect-select" required
                                    data-select-variant="form" data-placeholder="Seleccione un municipio" data-validation-required-message="Seleccione un municipio." data-validation-error-id="municipio-error"
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
                                <label for="jurisdiction_select_alcohol">Distrito <span class="text-red-600">*</span></label>
                                @php $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : ''); @endphp
                                <select id="jurisdiction_select_alcohol" name="jurisdiccion" class="tomselect-select" required
                                        data-select-variant="form" data-placeholder="Seleccione un distrito" data-validation-required-message="Seleccione un distrito." data-validation-error-id="jurisdiccion-error"
                                        @error('jurisdiccion') aria-invalid="true" aria-describedby="jurisdiccion-error" @enderror>
                                    <option value="">Seleccione un distrito</option>
                                    @foreach($districts as $district)<option value="{{ $district->id }}" {{ (string) $selectedDistrito === (string) $district->id ? 'selected' : '' }}>{{ $district->display_name }}</option>@endforeach
                                </select>
                            @else
                                <div class="statistics-form-label-row">
                                    <label for="jurisdiction_display_alcohol">Distrito asignado</label>
                                    <x-ui.form.help-tooltip id="alcohol-assigned-district-help" label="Cómo se asigna el distrito">
                                        Se asigna automáticamente según el distrito de tu cuenta.
                                    </x-ui.form.help-tooltip>
                                </div>
                                <input type="hidden" id="jurisdiction_input_alcohol" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                                <input id="jurisdiction_display_alcohol" type="text" class="ui-field--disabled" value="{{ optional(auth()->user()->district)->display_name }}" disabled aria-disabled="true">
                            @endif
                            <p id="jurisdiccion-error" class="report-road-field-error ui-field-error {{ $errors->has('jurisdiccion') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('jurisdiccion')) data-server-error @endif role="alert">{{ $errors->first('jurisdiccion') }}</p>
                        </div>
                </x-ui.form.section>

                @foreach($numericSections as $section)
                    <x-ui.form.section :title="$section['title']" :icon="$section['icon']" variant="report-road"
                        class="report-alcohol-data-section"
                        content-class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 {{ count($section['fields']) > 1 ? 'lg:grid-cols-2' : 'report-alcohol-single-field' }} lg:gap-x-4">
                            @foreach($section['fields'] as $field)
                                @php
                                    $fieldName = $field['name'];
                                    $fieldErrorId = str_replace('_', '-', $fieldName) . '-error';
                                    $fieldValue = old($fieldName, isset($report) ? $report->{$field['property']} : '');
                                @endphp
                                <div>
                                    <label for="{{ $fieldName }}">{{ $field['label'] }} <span class="text-red-600">*</span></label>
                                    <input id="{{ $fieldName }}" type="number" name="{{ $fieldName }}" min="0" max="{{ $field['max'] }}"
                                           placeholder="{{ $field['placeholder'] }}" value="{{ $fieldValue }}" required inputmode="numeric"
                                           data-validation-error-id="{{ $fieldName === 'conductores_no_aptos' ? 'alcohol-totals-error' : $fieldErrorId }}"
                                           data-validation-number-message="Capture un número entero entre 0 y {{ $field['max'] }}."
                                           @if($errors->has($fieldName)) aria-invalid="true" aria-describedby="{{ $fieldName === 'conductores_no_aptos' ? 'alcohol-totals-error' : $fieldErrorId }}" @endif>
                                    @if($fieldName !== 'conductores_no_aptos')
                                        <p id="{{ $fieldErrorId }}" class="report-road-field-error ui-field-error {{ $errors->has($fieldName) ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has($fieldName)) data-server-error @endif role="alert">{{ $errors->first($fieldName) }}</p>
                                    @endif
                                    @if($fieldName === 'conductores_no_aptos')
                                        <p id="alcohol-totals-error" class="report-road-field-error ui-field-error {{ $errors->has($fieldName) ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has($fieldName)) data-server-error @endif role="alert">{{ $errors->first($fieldName) }}</p>
                                    @endif
                                </div>
                            @endforeach
                    </x-ui.form.section>
                @endforeach

                <x-ui.form.section title="Descripción" icon="far fa-clipboard" variant="report-road"
                    class="report-road-description" content-class="space-y-3">
                    <div>
                        <label for="descripcion">Descripción de la actividad</label>
                        <textarea id="descripcion" name="descripcion" class="description-scroll" rows="4" maxlength="5000"
                                  placeholder="Agregue contexto, resultados u observaciones relevantes"
                                  data-validation-error-id="descripcion-error"
                                  @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                        <p id="descripcion-error" class="report-road-field-error ui-field-error {{ $errors->has('descripcion') ? '' : 'hidden' }}" data-ui-error-slot @if($errors->has('descripcion')) data-server-error @endif role="alert">{{ $errors->first('descripcion') }}</p>
                    </div>
                </x-ui.form.section>

                <x-ui.form.section title="Carga de archivos" icon="fas fa-cloud-upload-alt" variant="report-road"
                    class="report-road-files" content-class="space-y-4">
                        @if(isset($publication) && $publication->files->count() > 0)
                            <section class="report-road-file-collection" aria-labelledby="existing-files-title">
                                <div class="report-road-file-collection-header">
                                    <div><h3 id="existing-files-title">Archivos actuales</h3><p>{{ $publication->files->count() }} {{ $publication->files->count() === 1 ? 'archivo guardado' : 'archivos guardados' }}. Marca únicamente los que deseas eliminar o reemplazar.</p></div>
                                    <button id="select-all-existing-files" type="button" class="report-road-file-text-action" onclick="toggleAllExistingFiles()" aria-pressed="false">Seleccionar todos</button>
                                </div>
                                <ul class="report-road-file-rows" id="existing-files-list">
                                    @foreach($publication->files as $file)
                                        @php
                                            $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                            $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);
                                            $fileIcon = $fileType === 'photos' ? 'far fa-image' : 'far fa-file-excel';
                                        @endphp
                                        <li class="report-road-file-row file-item" data-file-id="{{ $file->id }}" data-file-type="{{ $fileType }}">
                                            <input type="checkbox" class="file-delete-checkbox report-road-file-checkbox" onchange="toggleFileStrikethrough(this)" aria-label="Marcar {{ $file->original_name }} para eliminar o reemplazar">
                                            <span class="report-road-file-icon" aria-hidden="true"><i class="{{ $fileIcon }}"></i></span>
                                            <div class="report-road-file-info"><p class="report-road-file-name">{{ $file->original_name }}</p><p class="report-road-file-meta"><span class="report-road-file-format">{{ strtoupper($extension) }}</span><span>{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</span></p></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        <div class="report-road-file-requirements" aria-label="Requisitos de archivos">
                            <div class="report-road-file-requirement">
                                <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                                <span class="report-road-file-requirement-copy"><strong>Hoja de cálculo</strong><small>Mínimo 1 archivo · XLS o XLSX</small></span>
                                <span id="excel-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                            </div>
                            <div class="report-road-file-requirement">
                                <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-image"></i></span>
                                <span class="report-road-file-requirement-copy"><strong>Fotografía</strong><small>Mínimo 1 archivo · JPG, JPEG o PNG</small></span>
                                <span id="photos-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                            </div>
                        </div>

                        <div class="report-road-file-uploader">
                            <div class="report-road-file-uploader-heading"><h3 id="file-upload-title">@if(isset($publication)) Agregar nuevos archivos (opcional) @else Subir archivos (selección múltiple) <span class="text-red-600">*</span> @endif</h3><p>Máximo 10 MB por archivo.</p></div>
                            <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                                <input type="file" id="file-input" name="archivos[]" class="hidden" accept=".xlsx,.xls,.jpg,.jpeg,.png" multiple onchange="addFiles(this.files)" aria-describedby="file-error">
                                <span class="report-road-upload-zone-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                                <div class="report-road-upload-zone-copy"><p><button id="choose-files-button" type="button" aria-describedby="file-error">Seleccionar archivos</button><span> o arrástralos aquí</span></p><small>XLSX, XLS, JPG, JPEG o PNG · selección múltiple</small></div>
                            </div>
                            <section id="file-list" class="report-road-file-collection hidden" aria-labelledby="selected-files-title">
                                <div class="report-road-file-collection-header"><div><h3 id="selected-files-title">Archivos por agregar</h3><p id="selected-files-count">0 archivos seleccionados</p></div></div>
                                <ul id="file-names" class="report-road-file-rows"></ul>
                            </section>
                            <div id="file-error" class="report-road-file-error {{ $fileErrors->isNotEmpty() ? '' : 'hidden' }}" role="alert">{{ $fileErrors->join(' ') }}</div>
                        </div>
                </x-ui.form.section>

                @if(isset($publication))<x-form-buttons class="users-form-actions" primaryText="Actualizar registro" secondaryText="Restablecer cambios" primaryType="submit" secondaryType="button" secondaryOnclick="resetAlcoholimetriaEditForm(event)" />
                @else<x-form-buttons class="users-form-actions" primaryText="Guardar registro" secondaryText="Limpiar formulario" primaryType="submit" secondaryType="button" secondaryOnclick="clearAlcoholimetriaForm()" />@endif
                @if(isset($publication))<input type="hidden" id="files-to-delete" name="files_to_delete" value="">@endif
                <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'alcoholimetria') }}">
            </form>
    </x-ui.form.page>

    <script>
        let selectedFiles = [];
        let alcoholValidationAttempted = false;
        const alcoholNumericFields = [
            'pruebas_realizadas', 'conductores_no_aptos', 'mujeres_no_aptas', 'hombres_no_aptos',
            'automoviles_camionetas', 'motocicletas', 'transporte_colectivo',
            'transporte_individual', 'transporte_carga', 'vehiculos_emergencia'
        ];
        const alcoholNumber = name => Number(document.querySelector(`[name="${name}"]`)?.value || 0);

        function clearAlcoholTotalsError() {
            const input = document.getElementById('conductores_no_aptos');
            const error = document.getElementById('alcohol-totals-error');
            input?.setCustomValidity('');
            input?.removeAttribute('aria-invalid');
            if (input && error) {
                const describedBy = (input.getAttribute('aria-describedby') || '').split(/\s+/).filter(id => id && id !== error.id);
                if (describedBy.length) input.setAttribute('aria-describedby', describedBy.join(' '));
                else input.removeAttribute('aria-describedby');
            }
            if (error) {
                error.removeAttribute('data-server-error');
                error.textContent = '';
                error.classList.add('hidden');
            }
        }

        function validateAlcoholTotals() {
            const input = document.getElementById('conductores_no_aptos');
            const error = document.getElementById('alcohol-totals-error');
            if (!input || !error) return true;
            input.setCustomValidity('');

            const fields = alcoholNumericFields.map(name => document.querySelector(`[name="${name}"]`));
            if (fields.some(field => !field || field.value === '' || !field.validity.valid)) {
                if (!error.hasAttribute('data-server-error')) {
                    error.textContent = '';
                    error.classList.add('hidden');
                }
                return true;
            }

            const total = alcoholNumber('conductores_no_aptos');
            let message = '';
            if (total > alcoholNumber('pruebas_realizadas')) message = 'Los conductores no aptos no pueden ser mayores que las pruebas realizadas.';
            else if (alcoholNumber('mujeres_no_aptas') + alcoholNumber('hombres_no_aptos') !== total) message = 'La suma de mujeres y hombres no aptos debe coincidir con el total.';
            else if (['automoviles_camionetas','motocicletas','transporte_colectivo','transporte_individual','transporte_carga','vehiculos_emergencia'].reduce((sum, name) => sum + alcoholNumber(name), 0) !== total) message = 'La suma por tipo de vehículo debe coincidir con el total de conductores no aptos.';
            input.setCustomValidity(message);
            error.textContent = message;
            error.classList.toggle('hidden', !message);
            if (message) input.setAttribute('aria-invalid', 'true');
            else input.removeAttribute('aria-invalid');
            if (message) input.setAttribute('aria-describedby', 'alcohol-totals-error');
            return !message;
        }

        function showFileError(message, focus = true) {
            const error = document.getElementById('file-error');
            if (!error) return;
            error.textContent = message; error.classList.remove('hidden');
            if (!focus) return;
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            error.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            document.getElementById('choose-files-button')?.focus();
        }
        function clearFileError() { const error = document.getElementById('file-error'); if (error) { error.textContent = ''; error.classList.add('hidden'); } }

        function addFiles(files) {
            clearFileError();
            Array.from(files).forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['xlsx','xls','jpg','jpeg','png'].includes(extension)) { showFileError('Formato no válido. Solo se permiten archivos Excel (XLS o XLSX) y fotografías (JPG, JPEG o PNG).'); return; }
                if (file.size > 10 * 1024 * 1024) { showFileError(`El archivo ${file.name} excede el tamaño máximo permitido (10 MB).`); return; }
                if (!selectedFiles.some(item => item.name === file.name && item.size === file.size)) selectedFiles.push(file);
            });
            updateFileDisplay(); updateFileCounters(); document.getElementById('file-input').value = '';
        }
        function removeFile(index) { clearFileError(); selectedFiles.splice(index, 1); updateFileDisplay(); updateFileCounters(); }
        function updateFilesToDeleteInput() {
            const input = document.getElementById('files-to-delete'); if (!input) return;
            input.value = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked')).map(box => box.closest('.file-item')?.dataset.fileId).filter(Boolean).join(',');
        }
        function syncSelectAllExistingFiles() {
            const button = document.getElementById('select-all-existing-files'); const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            if (!button || !boxes.length) return; const all = boxes.every(box => box.checked);
            button.textContent = all ? 'Deseleccionar todos' : 'Seleccionar todos'; button.setAttribute('aria-pressed', all ? 'true' : 'false');
        }
        function toggleAllExistingFiles() {
            const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox')); const checked = !boxes.every(box => box.checked);
            boxes.forEach(box => { box.checked = checked; toggleFileStrikethrough(box); }); syncSelectAllExistingFiles();
        }
        function toggleFileStrikethrough(box) {
            const row = box.closest('.file-item'); if (box.checked) row.dataset.markedForRemoval = 'true'; else delete row.dataset.markedForRemoval;
            updateFilesToDeleteInput(); updateFileCounters(); syncSelectAllExistingFiles();
        }
        function setFileBadge(type, count) {
            const badge = document.getElementById(`${type}-status-badge`); if (!badge) return;
            badge.textContent = count >= 1 ? 'Completado' : 'Pendiente'; badge.dataset.status = count >= 1 ? 'complete' : 'pending';
        }
        function getAlcoholFileCounts() {
            let excel = 0, photos = 0;
            document.querySelectorAll('#existing-files-list .file-item').forEach(row => { if (!row.querySelector('.file-delete-checkbox')?.checked) row.dataset.fileType === 'excel' ? excel++ : row.dataset.fileType === 'photos' ? photos++ : null; });
            selectedFiles.forEach(file => ['xlsx','xls'].includes(file.name.split('.').pop().toLowerCase()) ? excel++ : photos++);
            return { excel, photos };
        }
        function updateFileCounters() {
            const { excel, photos } = getAlcoholFileCounts();
            setFileBadge('excel', excel); setFileBadge('photos', photos);
        }
        function validateAlcoholFiles(focus = false) {
            const { excel, photos } = getAlcoholFileCounts();
            if (excel >= 1 && photos >= 1) { clearFileError(); return true; }
            showFileError(`El reporte requiere como mínimo: archivo Excel (${excel}/1), fotografía (${photos}/1).`, focus);
            return false;
        }
        function updateFileDisplay() {
            const list = document.getElementById('file-names'); const collection = document.getElementById('file-list'); const count = document.getElementById('selected-files-count');
            if (!list || !collection) return; list.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase(); const isPhoto = ['jpg','jpeg','png'].includes(extension); const row = document.createElement('li');
                row.className = 'report-road-file-row';
                row.innerHTML = `<span class="report-road-file-icon" aria-hidden="true"><i class="far ${isPhoto ? 'fa-image' : 'fa-file-excel'}"></i></span><div class="report-road-file-info"><p class="report-road-file-name"></p><p class="report-road-file-meta"><span class="report-road-file-format">${extension.toUpperCase()}</span><span>${(file.size / 1024 / 1024).toFixed(2)} MB</span></p></div><button type="button" class="report-road-file-remove" onclick="removeFile(${index})" title="Quitar archivo"><i class="fas fa-times" aria-hidden="true"></i></button>`;
                row.querySelector('.report-road-file-name').textContent = file.name; row.querySelector('.report-road-file-remove').setAttribute('aria-label', `Quitar ${file.name}`); list.appendChild(row);
            });
            if (count) count.textContent = selectedFiles.length === 1 ? '1 archivo seleccionado' : `${selectedFiles.length} archivos seleccionados`;
            collection.classList.toggle('hidden', selectedFiles.length === 0);
        }
        async function clearAlcoholimetriaForm() {
            const form = document.getElementById('alcoholimetriaForm'); const canClear = window.confirmFormClear ? await window.confirmFormClear(form, selectedFiles.length) : false;
            if (!canClear) return;
            alcoholValidationAttempted = false;
            form?.reset();
            selectedFiles = [];
            updateFileDisplay();
            updateFileCounters();
            clearFileError();
            clearAlcoholTotalsError();
            await window.resetAlcoholimetriaTomSelects?.();
            form?.querySelectorAll('[data-ui-error-slot]').forEach(error => {
                error.removeAttribute('data-server-error'); error.textContent = ''; error.classList.add('hidden');
            });
            window.showToast?.('Formulario limpiado.', 'info', 2400);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('alcoholimetriaForm');
            form?.addEventListener('input', event => {
                if (!event.target.matches(alcoholNumericFields.map(name => `[name="${name}"]`).join(','))) return;
                clearAlcoholTotalsError();
                if (alcoholValidationAttempted) validateAlcoholTotals();
            }, true);
            const drop = document.getElementById('file-drop-zone'); const input = document.getElementById('file-input'); document.getElementById('choose-files-button')?.addEventListener('click', () => input?.click());
            if (drop && input) {
                ['dragenter','dragover','dragleave','drop'].forEach(name => drop.addEventListener(name, event => { event.preventDefault(); event.stopPropagation(); }));
                ['dragenter','dragover'].forEach(name => drop.addEventListener(name, () => drop.dataset.dragActive = 'true'));
                ['dragleave','drop'].forEach(name => drop.addEventListener(name, () => delete drop.dataset.dragActive)); drop.addEventListener('drop', event => addFiles(event.dataTransfer.files));
            }
            updateFileCounters();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const municipalityToDistrict = @json($municipalities->mapWithKeys(fn($item) => [$item->id => $item->district_id]));
            const districtNames = @json($districts->mapWithKeys(fn($item) => [$item->id => $item->display_name]));
            const currentDistrict = @json(optional(auth()->user())->district_id); const canSelectAnyDistrict = @json($canSelectAnyDistrict ?? false);
            const municipality = document.getElementById('alcohol_municipality_select'); const district = document.getElementById('jurisdiction_select_alcohol');
            const districtDisplay = document.getElementById('jurisdiction_display_alcohol'); const hiddenDistrict = document.getElementById('jurisdiction_input_alcohol');
            const clearFilter = document.getElementById('clear-district-filter-alcohol'); const form = document.getElementById('alcoholimetriaForm'); let loadId = 0;
            const isEdit = Boolean(form?.querySelector('input[name="_method"][value="PUT"]'));
            const navigationState = { submitting: false, navigating: false, confirming: false };

            if (!form || !window.AppFilterSelect) return;

            function selectError(select) {
                return document.getElementById(select?.dataset.validationErrorId || '');
            }
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
            function selectedDistrict() { if (!canSelectAnyDistrict && currentDistrict) return String(currentDistrict); return canSelectAnyDistrict && district ? String(district.value || '') : ''; }
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
                const requestId = ++loadId; const requested = selectedDistrict();
                try {
                    const items = await fetchMunicipalities('');
                    if (requestId !== loadId || requested !== selectedDistrict()) return;
                    const ts = municipality.tomselect;
                    ts.clearOptions(); items.forEach(item => ts.addOption(item));
                    if (valueToRestore) ts.setValue(String(valueToRestore), true);
                    ts.refreshOptions(open); if (open) ts.open();
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
            function resetMunicipality(open = false) { const ts = municipality?.tomselect; if (!ts) { if (municipality) municipality.value = ''; return; } ts.clear(true); municipality.value = ''; ts.setTextboxValue(''); ts.clearOptions(); ts.clearCache?.(); loadMunicipalities(open); }

            if (canSelectAnyDistrict && district) {
                window.AppFilterSelect.init(district, {
                    searchable: true, maxOptions: 100,
                    onChange: () => {
                        if (alcoholValidationAttempted) validateSelect(district, 'Seleccione un distrito.'); else clearSelectError(district);
                        clearSelectError(municipality); resetMunicipality(false); updateClearFilter();
                    }
                });
                updateClearFilter();
            }
            clearFilter?.addEventListener('click', () => { district.tomselect?.clear(true); district.value = ''; clearSelectError(district); clearSelectError(municipality); updateClearFilter(); resetMunicipality(true); });
            if (municipality) {
                window.AppFilterSelect.init(municipality, {
                    searchable: true, preload: true, maxOptions: 100,
                    load: function(query, callback) { const requested = selectedDistrict(); fetchMunicipalities(query).then(items => callback(requested === selectedDistrict() ? items : [])).catch(() => callback()); },
                    onChange: () => {
                        if (alcoholValidationAttempted) validateSelect(municipality, 'Seleccione un municipio.'); else clearSelectError(municipality);
                        const districtId = municipalityToDistrict[municipality.value];
                        if (districtId && canSelectAnyDistrict && district) {
                            district.tomselect?.setValue(String(districtId), true); clearSelectError(district); updateClearFilter();
                            loadMunicipalities(false, municipality.value);
                        }
                    }
                });
            }
            if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || districtDisplay.value; }

            const initialDistrict = String(district?.value || '');
            const initialMunicipality = String(municipality?.value || '');
            window.resetAlcoholimetriaTomSelects = async function(values = {}) {
                const districtValue = String(values.district ?? ''); const municipalityValue = String(values.municipality ?? '');
                if (canSelectAnyDistrict && district) { district.tomselect?.setValue(districtValue, true); district.value = districtValue; updateClearFilter(); }
                await loadMunicipalities(false, municipalityValue);
                clearSelectError(municipality); clearSelectError(district);
                if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || ''; }
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

            window.resetAlcoholimetriaEditForm = async function(event) {
                event?.preventDefault();
                const hadChanges = hasUnsavedChanges();
                const shouldReset = await confirmDiscardChanges({ question: '¿Deseas restablecer los cambios?', description: 'Los campos y archivos volverán al estado con el que abriste esta edición.', confirmText: 'Restablecer' });
                if (!shouldReset) return;
                alcoholValidationAttempted = false; form.reset(); selectedFiles = [];
                document.querySelectorAll('#existing-files-list .file-delete-checkbox').forEach(box => { box.checked = false; toggleFileStrikethrough(box); });
                updateFilesToDeleteInput(); updateFileDisplay(); clearFileError(); clearAlcoholTotalsError();
                form.querySelectorAll('[data-ui-error-slot]').forEach(error => { error.removeAttribute('data-server-error'); error.textContent = ''; error.classList.add('hidden'); });
                await window.resetAlcoholimetriaTomSelects({ district: initialDistrict, municipality: initialMunicipality });
                updateFileCounters();
                if (hadChanges) window.AppToast?.info ? window.AppToast.info('Cambios restablecidos.') : window.showToast?.('Cambios restablecidos.', 'info', 4000);
            };

            form.addEventListener('submit', function(event) {
                alcoholValidationAttempted = true;
                const municipalityValid = validateSelect(municipality, 'Seleccione un municipio.');
                const districtValid = validateSelect(canSelectAnyDistrict ? district : null, 'Seleccione un distrito.');
                const totalsValid = validateAlcoholTotals();
                updateFilesToDeleteInput();
                const filesValid = validateAlcoholFiles(false);

                if (selectedFiles.length) {
                    const transfer = new DataTransfer(); selectedFiles.forEach(file => transfer.items.add(file));
                    document.getElementById('file-input').files = transfer.files;
                }

                if (!municipalityValid || !districtValid || !totalsValid || !filesValid) {
                    event.preventDefault();
                    const firstInvalid = Array.from(form.querySelectorAll('input, select, textarea')).find(field => {
                        if (field.disabled || field.type === 'hidden' || field.type === 'file') return false;
                        if (field === municipality || field === district) return !field.value;
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

            if (isEdit) {
                document.addEventListener('click', async function(event) {
                    const link = event.target.closest('a[href]');
                    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download') || !hasUnsavedChanges()) return;
                    const href = link.getAttribute('href'); if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                    const destination = new URL(link.href, window.location.href); if (destination.origin !== window.location.origin) return;
                    event.preventDefault();
                    const shouldLeave = await confirmDiscardChanges({ question: '¿Deseas salir de la edición?', description: 'Los cambios realizados no se guardarán.', confirmText: 'Salir sin guardar' });
                    if (!shouldLeave) return;
                    navigationState.navigating = true;
                    if (typeof window.navigateBackOrVisit === 'function') window.navigateBackOrVisit(destination.href); else window.location.assign(destination.href);
                }, true);

                window.addEventListener('beforeunload', function(event) {
                    if (navigationState.submitting || navigationState.navigating || !hasUnsavedChanges()) return;
                    event.preventDefault(); event.returnValue = '';
                });
            }
        });
    </script>
@endsection
