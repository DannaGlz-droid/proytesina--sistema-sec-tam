@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de observatorio de lesiones' : 'Registrar reporte de observatorio de lesiones')
@section('content')
    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="report-road-form-page report-observatory-form-page users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            :title="isset($publication) ? 'Editar reporte de observatorio de lesiones' : 'Registrar reporte de observatorio de lesiones'"
            :description="isset($publication) ? 'Actualiza la información del reporte y conserva la trazabilidad de sus archivos.' : 'Captura la información general y adjunta la hoja de cálculo requerida.'"
            :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'observatorio')])"
            back-label="Volver a publicaciones"
            :prefer-history-back="true"
        />

        @php
            $validationMessages = collect($errors->getMessages());
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
        @endphp

        <div class="report-road-form-card users-form-card">
            <form id="observatorioForm" class="report-road-form" action="{{ isset($publication) ? route('reportes.observatorio.update', $publication) : route('reportes.observatorio.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($publication)) @method('PUT') @endif

                <section class="report-road-section report-road-general">
                    <div class="report-road-section-heading">
                        <i class="far fa-file-alt" aria-hidden="true"></i>
                        <h2>Información general</h2>
                    </div>
                    <div class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                        <div>
                            <label for="tema">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" name="tema" placeholder="Ej: Análisis de lesiones por accidentes"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}"
                                   required minlength="3" maxlength="150"
                                   aria-invalid="{{ $errors->has('tema') ? 'true' : 'false' }}"
                                   @error('tema') aria-describedby="tema-error" @enderror>
                            @error('tema') <p id="tema-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="fecha">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" name="fecha"
                                   value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required max="{{ date('Y-m-d') }}"
                                   aria-invalid="{{ $errors->has('fecha') ? 'true' : 'false' }}"
                                   @error('fecha') aria-describedby="fecha-error" @enderror>
                            @error('fecha') <p id="fecha-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <div class="report-road-municipality-label-row">
                                <label for="observatorio_municipality_select">Municipio <span class="text-red-600">*</span></label>
                                @if($canSelectAnyDistrict ?? false)
                                    <button id="clear-district-filter-observatorio" type="button" class="report-road-clear-district-filter"
                                            aria-controls="observatorio_municipality_select jurisdiction_select_observatorio"
                                            aria-label="Quitar el filtro de distrito y mostrar todos los municipios"
                                            title="Quitar filtro de distrito" hidden>Mostrar todos</button>
                                @endif
                            </div>
                            @php $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : ''); @endphp
                            <select id="observatorio_municipality_select" name="municipio" class="tomselect-select" required
                                    aria-invalid="{{ $errors->has('municipio') ? 'true' : 'false' }}"
                                    @error('municipio') aria-describedby="municipio-error" @enderror>
                                <option value="">Seleccione un municipio</option>
                                @if($selectedMunicipio)
                                    @php $selectedMunicipalityModel = $municipalities->firstWhere('id', $selectedMunicipio); @endphp
                                    @if($selectedMunicipalityModel)
                                        <option value="{{ $selectedMunicipalityModel->id }}" selected>{{ $selectedMunicipalityModel->name }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('municipio') <p id="municipio-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            @if($canSelectAnyDistrict ?? false)
                                <label for="jurisdiction_select_observatorio">Distrito <span class="text-red-600">*</span></label>
                                @php $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : ''); @endphp
                                <select id="jurisdiction_select_observatorio" name="jurisdiccion" class="tomselect-select" required
                                        aria-invalid="{{ $errors->has('jurisdiccion') ? 'true' : 'false' }}"
                                        @error('jurisdiccion') aria-describedby="jurisdiccion-error" @enderror>
                                    <option value="">Seleccione un distrito</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ (string) $selectedDistrito === (string) $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <label for="jurisdiction_display_observatorio">Distrito asignado</label>
                                <input type="hidden" id="jurisdiction_input_observatorio" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                                <input id="jurisdiction_display_observatorio" type="text" class="ui-field--disabled"
                                       value="{{ optional(auth()->user()->district)->name }}" disabled aria-disabled="true">
                            @endif
                            @error('jurisdiccion') <p id="jurisdiccion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="report-road-section report-road-description">
                    <div class="report-road-section-heading">
                        <i class="far fa-clipboard" aria-hidden="true"></i>
                        <h2>Descripción</h2>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label for="descripcion">Descripción de la actividad</label>
                            <textarea id="descripcion" name="descripcion" class="description-scroll" rows="4"
                                      placeholder="Agregue contexto, resultados u observaciones relevantes" maxlength="5000"
                                      aria-invalid="{{ $errors->has('descripcion') ? 'true' : 'false' }}"
                                      @error('descripcion') aria-describedby="descripcion-error" @enderror>{{ old('descripcion', isset($publication) && $publication->description !== 'Sin descripción adicional.' ? $publication->description : '') }}</textarea>
                            @error('descripcion') <p id="descripcion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="report-road-section report-road-files">
                    <div class="report-road-section-heading">
                        <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
                        <h2>Carga de archivos</h2>
                    </div>
                    <div class="space-y-4">
                        @if(isset($publication) && $publication->files->count() > 0)
                            <section class="report-road-file-collection" aria-labelledby="existing-files-title">
                                <div class="report-road-file-collection-header">
                                    <div>
                                        <h3 id="existing-files-title">Archivos actuales</h3>
                                        <p>{{ $publication->files->count() }} {{ $publication->files->count() === 1 ? 'archivo guardado' : 'archivos guardados' }}. Marca únicamente los que deseas eliminar o reemplazar.</p>
                                    </div>
                                    <button id="select-all-existing-files" type="button" class="report-road-file-text-action"
                                            onclick="toggleAllExistingFiles()" aria-pressed="false">Seleccionar todos</button>
                                </div>
                                <ul class="report-road-file-rows" id="existing-files-list">
                                    @foreach($publication->files as $file)
                                        @php
                                            $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                            $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);
                                        @endphp
                                        <li class="report-road-file-row file-item" data-file-id="{{ $file->id }}" data-file-type="{{ $fileType }}">
                                            <input type="checkbox" class="file-delete-checkbox report-road-file-checkbox"
                                                   onchange="toggleFileStrikethrough(this)"
                                                   aria-label="Marcar {{ $file->original_name }} para eliminar o reemplazar">
                                            <span class="report-road-file-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
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
                                <span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                                <span class="report-road-file-requirement-copy">
                                    <strong>Hoja de cálculo</strong>
                                    <small>1 archivo · XLSX</small>
                                </span>
                                <span id="excel-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span>
                            </div>
                        </div>

                        <div class="report-road-file-uploader">
                            <div class="report-road-file-uploader-heading">
                                <h3 id="file-upload-title">
                                    @if(isset($publication)) Agregar nuevos archivos (opcional)
                                    @else Subir archivos (selección múltiple) <span class="text-red-600">*</span>
                                    @endif
                                </h3>
                                <p>Máximo 10 MB por archivo.</p>
                            </div>
                            <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                                <input type="file" id="file-input" name="archivos[]" class="hidden" accept=".xlsx,.xls" multiple onchange="addFiles(this.files)">
                                <span class="report-road-upload-zone-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                                <div class="report-road-upload-zone-copy">
                                    <p><button id="choose-files-button" type="button">Seleccionar archivos</button><span> o arrástralos aquí</span></p>
                                    <small>XLSX o XLS · selección múltiple</small>
                                </div>
                            </div>
                            <div id="file-error" class="report-road-file-error hidden" role="alert"></div>
                            @if($fileErrors->isNotEmpty())
                                <div class="report-road-file-error" role="alert">
                                    @foreach($fileErrors as $error)<div>{{ $error }}</div>@endforeach
                                </div>
                            @endif
                            <section id="file-list" class="report-road-file-collection hidden" aria-labelledby="selected-files-title">
                                <div class="report-road-file-collection-header">
                                    <div><h3 id="selected-files-title">Archivos por agregar</h3><p id="selected-files-count">0 archivos seleccionados</p></div>
                                </div>
                                <ul id="file-names" class="report-road-file-rows"></ul>
                            </section>
                        </div>
                    </div>
                </section>

                <div class="report-road-form-actions">
                    @if(isset($publication))
                        <x-form-buttons primaryText="Actualizar registro" secondaryText="" primaryType="submit" />
                    @else
                        <x-form-buttons primaryText="Guardar registro" secondaryText="Limpiar formulario" primaryType="submit"
                                        secondaryType="button" secondaryOnclick="clearObservatorioLesionesForm()" />
                    @endif
                </div>
                @if(isset($publication)) <input type="hidden" id="files-to-delete" name="files_to_delete" value=""> @endif
                <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'observatorio') }}">
            </form>
        </div>
    </div>

    <script>
        let selectedFiles = [];

        function showFileError(message) {
            const error = document.getElementById('file-error');
            if (!error) return;
            error.textContent = message;
            error.classList.remove('hidden');
            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            error.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            document.getElementById('choose-files-button')?.focus();
        }

        function clearFileError() {
            const error = document.getElementById('file-error');
            if (!error) return;
            error.textContent = '';
            error.classList.add('hidden');
        }

        function addFiles(files) {
            clearFileError();
            Array.from(files).forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls'].includes(extension)) {
                    showFileError('Formato no válido. Solo se permiten archivos Excel (XLSX, XLS).');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    showFileError(`El archivo ${file.name} excede el tamaño máximo permitido (10 MB).`);
                    return;
                }
                if (!selectedFiles.some(item => item.name === file.name && item.size === file.size)) selectedFiles.push(file);
            });
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
            const input = document.getElementById('files-to-delete');
            if (!input) return;
            input.value = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked'))
                .map(checkbox => checkbox.closest('.file-item')?.dataset.fileId).filter(Boolean).join(',');
        }

        function syncSelectAllExistingFiles() {
            const button = document.getElementById('select-all-existing-files');
            const checkboxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            if (!button || !checkboxes.length) return;
            const allSelected = checkboxes.every(checkbox => checkbox.checked);
            button.textContent = allSelected ? 'Deseleccionar todos' : 'Seleccionar todos';
            button.setAttribute('aria-pressed', allSelected ? 'true' : 'false');
        }

        function toggleAllExistingFiles() {
            const checkboxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox'));
            const shouldCheck = !checkboxes.every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => { checkbox.checked = shouldCheck; toggleFileStrikethrough(checkbox); });
            syncSelectAllExistingFiles();
        }

        function toggleFileStrikethrough(checkbox) {
            const row = checkbox.closest('.file-item');
            if (checkbox.checked) row.dataset.markedForRemoval = 'true'; else delete row.dataset.markedForRemoval;
            updateFilesToDeleteInput();
            updateFileCounters();
            syncSelectAllExistingFiles();
        }

        function updateFileCounters() {
            const existingCount = Array.from(document.querySelectorAll('#existing-files-list .file-item'))
                .filter(row => !row.querySelector('.file-delete-checkbox')?.checked && row.dataset.fileType === 'excel').length;
            const badge = document.getElementById('excel-status-badge');
            if (!badge) return;
            const complete = existingCount + selectedFiles.length >= 1;
            badge.textContent = complete ? 'Completado' : 'Pendiente';
            badge.dataset.status = complete ? 'complete' : 'pending';
        }

        function updateFileStatus() {
            const list = document.getElementById('file-names');
            const collection = document.getElementById('file-list');
            const count = document.getElementById('selected-files-count');
            if (!list || !collection) return;
            list.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toUpperCase();
                const row = document.createElement('li');
                row.className = 'report-road-file-row';
                row.innerHTML = `<span class="report-road-file-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                    <div class="report-road-file-info"><p class="report-road-file-name"></p><p class="report-road-file-meta"><span class="report-road-file-format">${extension}</span><span>${(file.size / 1024 / 1024).toFixed(2)} MB</span></p></div>
                    <button type="button" class="report-road-file-remove" onclick="removeFile(${index})" title="Quitar archivo"><i class="fas fa-times" aria-hidden="true"></i></button>`;
                row.querySelector('.report-road-file-name').textContent = file.name;
                row.querySelector('.report-road-file-remove').setAttribute('aria-label', `Quitar ${file.name}`);
                list.appendChild(row);
            });
            if (count) count.textContent = selectedFiles.length === 1 ? '1 archivo seleccionado' : `${selectedFiles.length} archivos seleccionados`;
            collection.classList.toggle('hidden', selectedFiles.length === 0);
        }

        async function clearObservatorioLesionesForm() {
            const form = document.getElementById('observatorioForm');
            const canClear = window.confirmFormClear ? await window.confirmFormClear(form, selectedFiles.length) : false;
            if (!canClear) return;
            form?.reset();
            selectedFiles = [];
            updateFileStatus();
            document.querySelectorAll('#existing-files-list .file-item').forEach(row => delete row.dataset.markedForRemoval);
            updateFilesToDeleteInput();
            syncSelectAllExistingFiles();
            updateFileCounters();
            window.resetObservatorioTomSelects?.();
            window.showToast?.('Formulario limpiado.', 'info', 2400);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('file-drop-zone');
            const fileInput = document.getElementById('file-input');
            document.getElementById('choose-files-button')?.addEventListener('click', () => fileInput?.click());
            if (dropArea && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(name => dropArea.addEventListener(name, event => { event.preventDefault(); event.stopPropagation(); }));
                ['dragenter', 'dragover'].forEach(name => dropArea.addEventListener(name, () => dropArea.dataset.dragActive = 'true'));
                ['dragleave', 'drop'].forEach(name => dropArea.addEventListener(name, () => delete dropArea.dataset.dragActive));
                dropArea.addEventListener('drop', event => addFiles(event.dataTransfer.files));
            }
            updateFileCounters();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const municipalityToDistrict = @json($municipalities->mapWithKeys(fn($municipality) => [$municipality->id => $municipality->district_id]));
            const districtNames = @json($districts->mapWithKeys(fn($district) => [$district->id => $district->name]));
            const currentDistrict = @json(optional(auth()->user())->district_id);
            const canSelectAnyDistrict = @json($canSelectAnyDistrict ?? false);
            const oldDistrict = @json(old('jurisdiccion', ''));
            const oldMunicipality = @json(old('municipio', isset($report) ? $report->municipality_id : ''));
            const municipalitySelect = document.getElementById('observatorio_municipality_select');
            const districtSelect = document.getElementById('jurisdiction_select_observatorio');
            const districtDisplay = document.getElementById('jurisdiction_display_observatorio');
            const hiddenDistrict = document.getElementById('jurisdiction_input_observatorio');
            const clearFilterButton = document.getElementById('clear-district-filter-observatorio');
            const form = document.getElementById('observatorioForm');
            let municipalityLoadId = 0;

            const validityInput = select => select?.tomselect?.control_input || select?.tomselect?.input || select;
            const clearSelectError = select => validityInput(select)?.setCustomValidity?.('');
            const showSelectError = (select, message) => validityInput(select)?.setCustomValidity?.(message);
            const focusSelect = select => select?.tomselect ? select.tomselect.focus() : select?.focus();

            function reportSelectValidity(select) {
                focusSelect(select);
                const input = validityInput(select);
                if (input?.reportValidity) input.reportValidity(); else select?.reportValidity?.();
            }

            function validateSelect(select, message) {
                if (!select || !select.required || select.value) { clearSelectError(select); return true; }
                showSelectError(select, message);
                return false;
            }

            function selectedDistrictFilter() {
                if (!canSelectAnyDistrict && currentDistrict) return String(currentDistrict);
                return canSelectAnyDistrict && districtSelect ? String(districtSelect.value || '') : '';
            }

            function fetchMunicipalities(query) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(query) + '&limit=100';
                const district = selectedDistrictFilter();
                if (district) url += '&district_id=' + encodeURIComponent(district);
                return fetch(url).then(response => response.json()).then(items => items.map(item => ({ value: String(item.id), text: item.name })));
            }

            function loadMunicipalities(openDropdown = false) {
                if (!municipalitySelect?.tomselect) return;
                const loadId = ++municipalityLoadId;
                const requestedDistrict = selectedDistrictFilter();
                fetchMunicipalities('').then(items => {
                    if (loadId !== municipalityLoadId || requestedDistrict !== selectedDistrictFilter()) return;
                    const tomSelect = municipalitySelect.tomselect;
                    tomSelect.clearOptions();
                    items.forEach(item => tomSelect.addOption(item));
                    tomSelect.refreshOptions(openDropdown);
                    if (openDropdown) tomSelect.open();
                }).catch(() => {});
            }

            function updateClearFilter() {
                if (clearFilterButton && districtSelect) clearFilterButton.hidden = !districtSelect.value;
            }

            function resetMunicipality(openDropdown = false) {
                const tomSelect = municipalitySelect?.tomselect;
                if (!tomSelect) { if (municipalitySelect) municipalitySelect.value = ''; return; }
                tomSelect.clear(true);
                municipalitySelect.value = '';
                tomSelect.setTextboxValue('');
                tomSelect.clearOptions();
                tomSelect.clearCache?.();
                loadMunicipalities(openDropdown);
            }

            if (canSelectAnyDistrict && districtSelect) {
                new TomSelect(districtSelect, { valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: false, maxOptions: 50 });
                if (oldDistrict && districtNames[oldDistrict]) districtSelect.tomselect.setValue(String(oldDistrict), true);
                districtSelect.addEventListener('change', function() {
                    clearSelectError(districtSelect);
                    clearSelectError(municipalitySelect);
                    resetMunicipality(false);
                    updateClearFilter();
                });
                updateClearFilter();
            }

            clearFilterButton?.addEventListener('click', function() {
                districtSelect.tomselect?.clear(true);
                districtSelect.value = '';
                clearSelectError(districtSelect);
                clearSelectError(municipalitySelect);
                updateClearFilter();
                resetMunicipality(true);
            });

            if (municipalitySelect) {
                new TomSelect(municipalitySelect, {
                    valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: true, maxOptions: 50,
                    load: function(query, callback) {
                        const requestedDistrict = selectedDistrictFilter();
                        fetchMunicipalities(query).then(items => callback(requestedDistrict === selectedDistrictFilter() ? items : [])).catch(() => callback());
                    }
                });
                if (oldMunicipality) municipalitySelect.tomselect.setValue(String(oldMunicipality), true);
                municipalitySelect.addEventListener('change', function() {
                    clearSelectError(municipalitySelect);
                    const districtId = municipalityToDistrict[municipalitySelect.value];
                    if (districtId && canSelectAnyDistrict && districtSelect) {
                        districtSelect.tomselect?.setValue(String(districtId), true);
                        clearSelectError(districtSelect);
                        loadMunicipalities(false);
                        updateClearFilter();
                    }
                });
            }

            if (!canSelectAnyDistrict && currentDistrict) {
                if (hiddenDistrict) hiddenDistrict.value = currentDistrict;
                if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || districtDisplay.value;
            }

            window.resetObservatorioTomSelects = function() {
                if (canSelectAnyDistrict && districtSelect) {
                    districtSelect.tomselect?.clear(true);
                    districtSelect.value = '';
                    updateClearFilter();
                }
                resetMunicipality(false);
                clearSelectError(municipalitySelect);
                clearSelectError(districtSelect);
                if (!canSelectAnyDistrict && currentDistrict) {
                    if (hiddenDistrict) hiddenDistrict.value = currentDistrict;
                    if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || '';
                }
            };

            form?.addEventListener('invalid', function(event) {
                if (event.target === municipalitySelect) {
                    event.preventDefault();
                    showSelectError(municipalitySelect, 'Seleccione un municipio.');
                    reportSelectValidity(municipalitySelect);
                }
                if (event.target === districtSelect) {
                    event.preventDefault();
                    showSelectError(districtSelect, 'Seleccione un distrito.');
                    reportSelectValidity(districtSelect);
                }
            }, true);

            form?.addEventListener('submit', function(event) {
                const municipalityValid = validateSelect(municipalitySelect, 'Seleccione un municipio.');
                const districtValid = validateSelect(canSelectAnyDistrict ? districtSelect : null, 'Seleccione un distrito.');
                if (!municipalityValid || !districtValid) {
                    event.preventDefault();
                    reportSelectValidity(!municipalityValid ? municipalitySelect : districtSelect);
                    return;
                }
                updateFilesToDeleteInput();
                const isEditMode = !!form.querySelector('input[name="_method"][value="PUT"]');
                if (!isEditMode && selectedFiles.length === 0) {
                    event.preventDefault();
                    showFileError('Debe incluir al menos 1 archivo Excel (XLSX).');
                    return;
                }
                if (selectedFiles.length) {
                    const transfer = new DataTransfer();
                    selectedFiles.forEach(file => transfer.items.add(file));
                    document.getElementById('file-input').files = transfer.files;
                }
            });
        });
    </script>
@endsection
