@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría')
@section('content')
    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="report-road-form-page report-alcohol-form-page users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            :title="isset($publication) ? 'Editar reporte de alcoholimetría' : 'Registrar reporte de alcoholimetría'"
            :description="isset($publication) ? 'Actualiza los resultados del operativo y conserva la trazabilidad de sus archivos.' : 'Captura la información, los resultados del operativo y los archivos de respaldo.'"
            :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'alcoholimetria')])"
            back-label="Volver a publicaciones"
            :prefer-history-back="true"
        />

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

        <div class="report-road-form-card users-form-card">
            <form id="alcoholimetriaForm" class="report-road-form" action="{{ isset($publication) ? route('reportes.alcoholimetria.update', $publication) : route('reportes.alcoholimetria.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($publication)) @method('PUT') @endif

                <section class="report-road-section report-road-general">
                    <div class="report-road-section-heading"><i class="far fa-file-alt" aria-hidden="true"></i><h2>Información general</h2></div>
                    <div class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                        <div>
                            <label for="tema">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" name="tema" placeholder="Ej: Operativo de alcoholimetría"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}" required minlength="3" maxlength="150"
                                   aria-invalid="{{ $errors->has('tema') ? 'true' : 'false' }}" @error('tema') aria-describedby="tema-error" @enderror>
                            @error('tema') <p id="tema-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="fecha">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" name="fecha"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}" required max="{{ date('Y-m-d') }}"
                                   aria-invalid="{{ $errors->has('fecha') ? 'true' : 'false' }}" @error('fecha') aria-describedby="fecha-error" @enderror>
                            @error('fecha') <p id="fecha-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
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
                                    aria-invalid="{{ $errors->has('municipio') ? 'true' : 'false' }}" @error('municipio') aria-describedby="municipio-error" @enderror>
                                <option value="">Seleccione un municipio</option>
                                @if($selectedMunicipio)
                                    @php $selectedMunicipalityModel = $municipalities->firstWhere('id', $selectedMunicipio); @endphp
                                    @if($selectedMunicipalityModel)<option value="{{ $selectedMunicipalityModel->id }}" selected>{{ $selectedMunicipalityModel->name }}</option>@endif
                                @endif
                            </select>
                            @error('municipio') <p id="municipio-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            @if($canSelectAnyDistrict ?? false)
                                <label for="jurisdiction_select_alcohol">Distrito <span class="text-red-600">*</span></label>
                                @php $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : ''); @endphp
                                <select id="jurisdiction_select_alcohol" name="jurisdiccion" class="tomselect-select" required
                                        aria-invalid="{{ $errors->has('jurisdiccion') ? 'true' : 'false' }}" @error('jurisdiccion') aria-describedby="jurisdiccion-error" @enderror>
                                    <option value="">Seleccione un distrito</option>
                                    @foreach($districts as $district)<option value="{{ $district->id }}" {{ (string) $selectedDistrito === (string) $district->id ? 'selected' : '' }}>{{ $district->display_name }}</option>@endforeach
                                </select>
                            @else
                                <label for="jurisdiction_display_alcohol">Distrito asignado</label>
                                <input type="hidden" id="jurisdiction_input_alcohol" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                                <input id="jurisdiction_display_alcohol" type="text" class="ui-field--disabled" value="{{ optional(auth()->user()->district)->display_name }}" disabled aria-disabled="true">
                            @endif
                            @error('jurisdiccion') <p id="jurisdiccion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                @foreach($numericSections as $section)
                    <section class="report-road-section report-alcohol-data-section">
                        <div class="report-road-section-heading"><i class="{{ $section['icon'] }}" aria-hidden="true"></i><h2>{{ $section['title'] }}</h2></div>
                        <div class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 {{ count($section['fields']) > 1 ? 'lg:grid-cols-2' : 'report-alcohol-single-field' }} lg:gap-x-4">
                            @foreach($section['fields'] as $field)
                                @php
                                    $fieldName = $field['name'];
                                    $fieldErrorId = str_replace('_', '-', $fieldName) . '-error';
                                    $fieldValue = old($fieldName, isset($report) ? $report->{$field['property']} : '');
                                @endphp
                                <div>
                                    <label for="{{ $fieldName }}">{{ $field['label'] }} <span class="text-red-600">*</span></label>
                                    <input id="{{ $fieldName }}" type="number" name="{{ $fieldName }}" min="0" max="{{ $field['max'] }}"
                                           placeholder="{{ $field['placeholder'] }}" value="{{ $fieldValue }}" required
                                           aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}"
                                           @if($errors->has($fieldName)) aria-describedby="{{ $fieldErrorId }}" @endif>
                                    @error($fieldName) <p id="{{ $fieldErrorId }}" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                                    @if($fieldName === 'conductores_no_aptos')
                                        <p id="alcohol-totals-error" class="report-road-field-error hidden" role="alert"></p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <section class="report-road-section report-road-description">
                    <div class="report-road-section-heading"><i class="far fa-clipboard" aria-hidden="true"></i><h2>Descripción</h2></div>
                    <div class="space-y-3"><div>
                        <label for="descripcion">Descripción de la actividad</label>
                        <textarea id="descripcion" name="descripcion" class="description-scroll" rows="4" maxlength="5000"
                                  placeholder="Agregue contexto, resultados u observaciones relevantes"
                                  aria-invalid="{{ $errors->has('descripcion') ? 'true' : 'false' }}" @error('descripcion') aria-describedby="descripcion-error" @enderror>{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                        @error('descripcion') <p id="descripcion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                    </div></div>
                </section>

                <section class="report-road-section report-road-files">
                    <div class="report-road-section-heading"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i><h2>Carga de archivos</h2></div>
                    <div class="space-y-4">
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
                                <span class="report-road-file-requirement-copy"><strong>Hoja de cálculo</strong><small>1 archivo · XLSX</small></span>
                                <span id="excel-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                            </div>
                            <div class="report-road-file-requirement">
                                <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-image"></i></span>
                                <span class="report-road-file-requirement-copy"><strong>Fotografía</strong><small>1 archivo · JPG o PNG</small></span>
                                <span id="photos-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                            </div>
                        </div>

                        <div class="report-road-file-uploader">
                            <div class="report-road-file-uploader-heading"><h3 id="file-upload-title">@if(isset($publication)) Agregar nuevos archivos (opcional) @else Subir archivos (selección múltiple) <span class="text-red-600">*</span> @endif</h3><p>Máximo 10 MB por archivo.</p></div>
                            <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                                <input type="file" id="file-input" name="archivos[]" class="hidden" accept=".xlsx,.xls,.jpg,.jpeg,.png" multiple onchange="addFiles(this.files)">
                                <span class="report-road-upload-zone-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                                <div class="report-road-upload-zone-copy"><p><button id="choose-files-button" type="button">Seleccionar archivos</button><span> o arrástralos aquí</span></p><small>XLSX, XLS, JPG, JPEG o PNG · selección múltiple</small></div>
                            </div>
                            <div id="file-error" class="report-road-file-error hidden" role="alert"></div>
                            @if($fileErrors->isNotEmpty())<div class="report-road-file-error" role="alert">@foreach($fileErrors as $error)<div>{{ $error }}</div>@endforeach</div>@endif
                            <section id="file-list" class="report-road-file-collection hidden" aria-labelledby="selected-files-title">
                                <div class="report-road-file-collection-header"><div><h3 id="selected-files-title">Archivos por agregar</h3><p id="selected-files-count">0 archivos seleccionados</p></div></div>
                                <ul id="file-names" class="report-road-file-rows"></ul>
                            </section>
                        </div>
                    </div>
                </section>

                <div class="report-road-form-actions">
                    @if(isset($publication))<x-form-buttons primaryText="Actualizar registro" secondaryText="" primaryType="submit" />
                    @else<x-form-buttons primaryText="Guardar registro" secondaryText="Limpiar formulario" primaryType="submit" secondaryType="button" secondaryOnclick="clearAlcoholimetriaForm()" />@endif
                </div>
                @if(isset($publication))<input type="hidden" id="files-to-delete" name="files_to_delete" value="">@endif
                <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'alcoholimetria') }}">
            </form>
        </div>
    </div>

    <script>
        let selectedFiles = [];
        const alcoholNumber = name => Number(document.querySelector(`[name="${name}"]`)?.value || 0);

        function clearAlcoholTotalsError() {
            const input = document.getElementById('conductores_no_aptos');
            const error = document.getElementById('alcohol-totals-error');
            if (input && !input.dataset.serverInvalid) input.setAttribute('aria-invalid', 'false');
            if (error) { error.textContent = ''; error.classList.add('hidden'); }
        }

        function validateAlcoholTotals(focusFirst = false) {
            const input = document.getElementById('conductores_no_aptos');
            const error = document.getElementById('alcohol-totals-error');
            if (!input || !error) return true;
            const total = alcoholNumber('conductores_no_aptos');
            let message = '';
            if (total > alcoholNumber('pruebas_realizadas')) message = 'Los conductores no aptos no pueden ser mayores que las pruebas realizadas.';
            else if (alcoholNumber('mujeres_no_aptas') + alcoholNumber('hombres_no_aptos') !== total) message = 'La suma de mujeres y hombres no aptos debe coincidir con el total.';
            else if (['automoviles_camionetas','motocicletas','transporte_colectivo','transporte_individual','transporte_carga','vehiculos_emergencia'].reduce((sum, name) => sum + alcoholNumber(name), 0) !== total) message = 'La suma por tipo de vehículo debe coincidir con el total de conductores no aptos.';
            error.textContent = message;
            error.classList.toggle('hidden', !message);
            input.setAttribute('aria-invalid', message ? 'true' : 'false');
            if (message && focusFirst) input.focus();
            return !message;
        }

        function showFileError(message) {
            const error = document.getElementById('file-error');
            if (!error) return;
            error.textContent = message; error.classList.remove('hidden');
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            error.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            document.getElementById('choose-files-button')?.focus();
        }
        function clearFileError() { const error = document.getElementById('file-error'); if (error) { error.textContent = ''; error.classList.add('hidden'); } }

        function addFiles(files) {
            clearFileError();
            Array.from(files).forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['xlsx','xls','jpg','jpeg','png'].includes(extension)) { showFileError('Formato no válido. Solo se permiten Excel y fotografías JPG o PNG.'); return; }
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
        function updateFileCounters() {
            let excel = 0, photos = 0;
            document.querySelectorAll('#existing-files-list .file-item').forEach(row => { if (!row.querySelector('.file-delete-checkbox')?.checked) row.dataset.fileType === 'excel' ? excel++ : row.dataset.fileType === 'photos' ? photos++ : null; });
            selectedFiles.forEach(file => ['xlsx','xls'].includes(file.name.split('.').pop().toLowerCase()) ? excel++ : photos++);
            setFileBadge('excel', excel); setFileBadge('photos', photos);
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
            if (!canClear) return; form?.reset(); selectedFiles = []; updateFileDisplay(); updateFileCounters(); clearAlcoholTotalsError(); window.resetAlcoholimetriaTomSelects?.(); window.showToast?.('Formulario limpiado.', 'info', 2400);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[name="pruebas_realizadas"],[name="conductores_no_aptos"],[name="mujeres_no_aptas"],[name="hombres_no_aptos"],[name="automoviles_camionetas"],[name="motocicletas"],[name="transporte_colectivo"],[name="transporte_individual"],[name="transporte_carga"],[name="vehiculos_emergencia"]').forEach(input => input.addEventListener('input', clearAlcoholTotalsError));
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
            const oldDistrict = @json(old('jurisdiccion', '')); const oldMunicipality = @json(old('municipio', isset($report) ? $report->municipality_id : ''));
            const municipality = document.getElementById('alcohol_municipality_select'); const district = document.getElementById('jurisdiction_select_alcohol');
            const districtDisplay = document.getElementById('jurisdiction_display_alcohol'); const hiddenDistrict = document.getElementById('jurisdiction_input_alcohol');
            const clearFilter = document.getElementById('clear-district-filter-alcohol'); const form = document.getElementById('alcoholimetriaForm'); let loadId = 0;
            const validityInput = select => select?.tomselect?.control_input || select?.tomselect?.input || select;
            const clearSelectError = select => validityInput(select)?.setCustomValidity?.(''); const showSelectError = (select, message) => validityInput(select)?.setCustomValidity?.(message);
            function reportValidity(select) { select?.tomselect ? select.tomselect.focus() : select?.focus(); const input = validityInput(select); input?.reportValidity ? input.reportValidity() : select?.reportValidity?.(); }
            function validateSelect(select, message) { if (!select || !select.required || select.value) { clearSelectError(select); return true; } showSelectError(select, message); return false; }
            function selectedDistrict() { if (!canSelectAnyDistrict && currentDistrict) return String(currentDistrict); return canSelectAnyDistrict && district ? String(district.value || '') : ''; }
            function fetchMunicipalities(query) { let url = '/api/municipalities/search?q=' + encodeURIComponent(query) + '&limit=100'; const selected = selectedDistrict(); if (selected) url += '&district_id=' + encodeURIComponent(selected); return fetch(url).then(r => r.json()).then(items => items.map(item => ({ value: String(item.id), text: item.name }))); }
            function loadMunicipalities(open = false) { if (!municipality?.tomselect) return; const requestId = ++loadId; const requested = selectedDistrict(); fetchMunicipalities('').then(items => { if (requestId !== loadId || requested !== selectedDistrict()) return; const ts = municipality.tomselect; ts.clearOptions(); items.forEach(item => ts.addOption(item)); ts.refreshOptions(open); if (open) ts.open(); }).catch(() => {}); }
            function updateClearFilter() { if (clearFilter && district) clearFilter.hidden = !district.value; }
            function resetMunicipality(open = false) { const ts = municipality?.tomselect; if (!ts) { if (municipality) municipality.value = ''; return; } ts.clear(true); municipality.value = ''; ts.setTextboxValue(''); ts.clearOptions(); ts.clearCache?.(); loadMunicipalities(open); }

            if (canSelectAnyDistrict && district) {
                new TomSelect(district, { valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: false, maxOptions: 50 });
                if (oldDistrict && districtNames[oldDistrict]) district.tomselect.setValue(String(oldDistrict), true);
                district.addEventListener('change', () => { clearSelectError(district); clearSelectError(municipality); resetMunicipality(false); updateClearFilter(); }); updateClearFilter();
            }
            clearFilter?.addEventListener('click', () => { district.tomselect?.clear(true); district.value = ''; clearSelectError(district); clearSelectError(municipality); updateClearFilter(); resetMunicipality(true); });
            if (municipality) {
                new TomSelect(municipality, { valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: true, maxOptions: 50, load: function(query, callback) { const requested = selectedDistrict(); fetchMunicipalities(query).then(items => callback(requested === selectedDistrict() ? items : [])).catch(() => callback()); } });
                if (oldMunicipality) municipality.tomselect.setValue(String(oldMunicipality), true);
                municipality.addEventListener('change', () => { clearSelectError(municipality); const districtId = municipalityToDistrict[municipality.value]; if (districtId && canSelectAnyDistrict && district) { district.tomselect?.setValue(String(districtId), true); clearSelectError(district); loadMunicipalities(false); updateClearFilter(); } });
            }
            if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || districtDisplay.value; }
            window.resetAlcoholimetriaTomSelects = function() { if (canSelectAnyDistrict && district) { district.tomselect?.clear(true); district.value = ''; updateClearFilter(); } resetMunicipality(false); clearSelectError(municipality); clearSelectError(district); if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || ''; } };
            form?.addEventListener('invalid', event => { if (event.target === municipality) { event.preventDefault(); showSelectError(municipality, 'Seleccione un municipio.'); reportValidity(municipality); } if (event.target === district) { event.preventDefault(); showSelectError(district, 'Seleccione un distrito.'); reportValidity(district); } }, true);
            form?.addEventListener('submit', function(event) {
                const municipalityValid = validateSelect(municipality, 'Seleccione un municipio.'); const districtValid = validateSelect(canSelectAnyDistrict ? district : null, 'Seleccione un distrito.');
                if (!municipalityValid || !districtValid) { event.preventDefault(); reportValidity(!municipalityValid ? municipality : district); return; }
                if (!validateAlcoholTotals(true)) { event.preventDefault(); return; }
                updateFilesToDeleteInput(); const edit = !!form.querySelector('input[name="_method"][value="PUT"]');
                const excel = selectedFiles.filter(file => ['xlsx','xls'].includes(file.name.split('.').pop().toLowerCase())).length; const photos = selectedFiles.filter(file => ['jpg','jpeg','png'].includes(file.name.split('.').pop().toLowerCase())).length;
                if (!edit && (excel < 1 || photos < 1)) { event.preventDefault(); showFileError(`El reporte requiere: archivo Excel (${excel}/1), fotografía (${photos}/1).`); return; }
                if (selectedFiles.length) { const transfer = new DataTransfer(); selectedFiles.forEach(file => transfer.items.add(file)); document.getElementById('file-input').files = transfer.files; }
            });
        });
    </script>
@endsection
