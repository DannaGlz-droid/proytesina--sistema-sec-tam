@extends('layouts.principal')
@section('title', isset($publication) ? 'Editar reporte de grupos vulnerables' : 'Registrar reporte de grupos vulnerables')
@section('content')
    @include('components.header-admin')
    @include('components.nav-reportes')

    <div class="report-road-form-page report-groups-form-page users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            :title="isset($publication) ? 'Editar reporte de grupos vulnerables' : 'Registrar reporte de grupos vulnerables'"
            :description="isset($publication) ? 'Actualiza la información de la actividad y conserva la trazabilidad de sus archivos.' : 'Captura la información general, la actividad y los archivos de respaldo del reporte.'"
            :back-href="route('reportes.index', ['tipo' => request('redirect_tipo', 'grupos-vulnerables')])"
            back-label="Volver a publicaciones"
            :prefer-history-back="true"
        />

        @php
            $validationMessages = collect($errors->getMessages());
            $fileErrors = $validationMessages->filter(fn($messages, $field) => str_starts_with($field, 'archivos'))->flatten();
        @endphp

        <div class="report-road-form-card users-form-card">
            <form id="gruposVulnerablesForm" class="report-road-form" action="{{ isset($publication) ? route('reportes.grupos-vulnerables.update', $publication) : route('reportes.grupos-vulnerables.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($publication)) @method('PUT') @endif

                <section class="report-road-section report-road-general">
                    <div class="report-road-section-heading"><i class="far fa-file-alt" aria-hidden="true"></i><h2>Información general</h2></div>
                    <div class="report-road-fields-grid grid grid-cols-1 gap-x-3 gap-y-3 lg:grid-cols-2 lg:gap-x-4">
                        <div>
                            <label for="tema">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" name="tema" placeholder="Ej: Atención a grupos vulnerables"
                                   value="{{ old('tema', isset($publication) ? $publication->topic : '') }}" required minlength="3" maxlength="150"
                                   aria-invalid="{{ $errors->has('tema') ? 'true' : 'false' }}" @error('tema') aria-describedby="tema-error" @enderror>
                            @error('tema') <p id="tema-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="activity_type_id">Tipo de actividad <span class="text-red-600">*</span></label>
                            @php $selectedActivity = old('activity_type_id', isset($report) ? $report->activity_type_id : ''); @endphp
                            <select id="activity_type_id" name="activity_type_id" class="tomselect-select" required
                                    aria-invalid="{{ $errors->has('activity_type_id') ? 'true' : 'false' }}" @error('activity_type_id') aria-describedby="activity-type-error" @enderror>
                                <option value="">Seleccione el tipo de actividad</option>
                                @foreach($activityTypes as $activityType)
                                    <option value="{{ $activityType->id }}" {{ (string) $selectedActivity === (string) $activityType->id ? 'selected' : '' }}>{{ $activityType->name }}</option>
                                @endforeach
                            </select>
                            @error('activity_type_id') <p id="activity-type-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="fecha">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" name="fecha" value="{{ old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '') }}"
                                   required max="{{ date('Y-m-d') }}" aria-invalid="{{ $errors->has('fecha') ? 'true' : 'false' }}" @error('fecha') aria-describedby="fecha-error" @enderror>
                            @error('fecha') <p id="fecha-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="participantes">Participantes <span class="text-red-600">*</span></label>
                            <input id="participantes" type="number" name="participantes" placeholder="Ej: 25"
                                   value="{{ old('participantes', isset($report) ? $report->participants : '') }}" required min="1" max="9999"
                                   aria-invalid="{{ $errors->has('participantes') ? 'true' : 'false' }}" @error('participantes') aria-describedby="participantes-error" @enderror>
                            @error('participantes') <p id="participantes-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="lugar">Lugar <span class="text-red-600">*</span></label>
                            <input id="lugar" type="text" name="lugar" placeholder="Ej: Auditorio municipal"
                                   value="{{ old('lugar', isset($report) ? $report->location : '') }}" required minlength="3" maxlength="180"
                                   aria-invalid="{{ $errors->has('lugar') ? 'true' : 'false' }}" @error('lugar') aria-describedby="lugar-error" @enderror>
                            @error('lugar') <p id="lugar-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="promotor">Promotor <span class="text-red-600">*</span></label>
                            <input id="promotor" type="text" name="promotor" placeholder="Ej: Secretaría de Salud"
                                   value="{{ old('promotor', isset($report) ? $report->promoter : '') }}" required minlength="3" maxlength="180"
                                   aria-invalid="{{ $errors->has('promotor') ? 'true' : 'false' }}" @error('promotor') aria-describedby="promotor-error" @enderror>
                            @error('promotor') <p id="promotor-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <div class="report-road-municipality-label-row">
                                <label for="grupos_municipality_select">Municipio <span class="text-red-600">*</span></label>
                                @if($canSelectAnyDistrict ?? false)
                                    <button id="clear-district-filter-groups" type="button" class="report-road-clear-district-filter"
                                            aria-controls="grupos_municipality_select jurisdiction_select_gv"
                                            aria-label="Quitar el filtro de distrito y mostrar todos los municipios" title="Quitar filtro de distrito" hidden>Mostrar todos</button>
                                @endif
                            </div>
                            @php $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : ''); @endphp
                            <select id="grupos_municipality_select" name="municipio" class="tomselect-select" required
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
                                <label for="jurisdiction_select_gv">Distrito <span class="text-red-600">*</span></label>
                                @php $selectedDistrito = old('jurisdiccion', isset($report) ? $report->district_id : ''); @endphp
                                <select id="jurisdiction_select_gv" name="jurisdiccion" class="tomselect-select" required
                                        aria-invalid="{{ $errors->has('jurisdiccion') ? 'true' : 'false' }}" @error('jurisdiccion') aria-describedby="jurisdiccion-error" @enderror>
                                    <option value="">Seleccione un distrito</option>
                                    @foreach($districts as $district)<option value="{{ $district->id }}" {{ (string) $selectedDistrito === (string) $district->id ? 'selected' : '' }}>{{ $district->name }}</option>@endforeach
                                </select>
                            @else
                                <label for="jurisdiction_display_gv">Distrito asignado</label>
                                <input type="hidden" id="jurisdiction_input_gv" name="jurisdiccion" value="{{ auth()->user()->district_id }}" required>
                                <input id="jurisdiction_display_gv" type="text" class="ui-field--disabled" value="{{ optional(auth()->user()->district)->name }}" disabled aria-disabled="true">
                            @endif
                            @error('jurisdiccion') <p id="jurisdiccion-error" class="report-road-field-error" role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

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
                                            $fileIcon = match($fileType) { 'pdf' => 'far fa-file-pdf', 'photos' => 'far fa-image', default => 'far fa-file-excel' };
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
                            <div class="report-road-file-requirement"><span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-pdf"></i></span><span class="report-road-file-requirement-copy"><strong>Documento PDF</strong><small>1 archivo · PDF</small></span><span id="pdf-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span></div>
                            <div class="report-road-file-requirement"><span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span><span class="report-road-file-requirement-copy"><strong>Hoja de cálculo</strong><small>1 archivo · XLSX</small></span><span id="excel-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">Pendiente</span></div>
                            <div class="report-road-file-requirement"><span class="report-road-file-requirement-icon" aria-hidden="true"><i class="far fa-image"></i></span><span class="report-road-file-requirement-copy"><strong>Fotografías</strong><small>4 archivos · JPG o PNG</small></span><span id="photos-status-badge" class="report-road-file-status" data-status="pending" aria-live="polite">0/4</span></div>
                        </div>

                        <div class="report-road-file-uploader">
                            <div class="report-road-file-uploader-heading"><h3 id="file-upload-title">@if(isset($publication)) Agregar nuevos archivos (opcional) @else Subir archivos (selección múltiple) <span class="text-red-600">*</span> @endif</h3><p>Máximo 10 MB por archivo.</p></div>
                            <div id="file-drop-zone" class="report-road-upload-zone" aria-labelledby="file-upload-title">
                                <input type="file" id="file-input" name="archivos[]" class="hidden" accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png" multiple onchange="addFiles(this.files)">
                                <span class="report-road-upload-zone-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                                <div class="report-road-upload-zone-copy"><p><button id="choose-files-button" type="button">Seleccionar archivos</button><span> o arrástralos aquí</span></p><small>PDF, XLSX, XLS, JPG, JPEG o PNG · selección múltiple</small></div>
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
                    @else<x-form-buttons primaryText="Guardar registro" secondaryText="Limpiar formulario" primaryType="submit" secondaryType="button" secondaryOnclick="clearGruposVulnerablesForm()" />@endif
                </div>
                @if(isset($publication))<input type="hidden" id="files-to-delete" name="files_to_delete" value="">@endif
                <input type="hidden" name="redirect_tipo" value="{{ request('redirect_tipo', 'grupos-vulnerables') }}">
            </form>
        </div>
    </div>

    <script>
        let selectedFiles = [];
        function showFileError(message) { const error = document.getElementById('file-error'); if (!error) return; error.textContent = message; error.classList.remove('hidden'); const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches; error.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' }); document.getElementById('choose-files-button')?.focus(); }
        function clearFileError() { const error = document.getElementById('file-error'); if (error) { error.textContent = ''; error.classList.add('hidden'); } }
        function addFiles(files) {
            clearFileError(); Array.from(files).forEach(file => { const extension = file.name.split('.').pop().toLowerCase(); if (!['pdf','xlsx','xls','jpg','jpeg','png'].includes(extension)) { showFileError('Formato no válido. Solo se permiten PDF, Excel y fotografías JPG o PNG.'); return; } if (file.size > 10 * 1024 * 1024) { showFileError(`El archivo ${file.name} excede el tamaño máximo permitido (10 MB).`); return; } if (!selectedFiles.some(item => item.name === file.name && item.size === file.size)) selectedFiles.push(file); });
            updateFileDisplay(); updateFileCounters(); document.getElementById('file-input').value = '';
        }
        function removeFile(index) { clearFileError(); selectedFiles.splice(index, 1); updateFileDisplay(); updateFileCounters(); }
        function updateFilesToDeleteInput() { const input = document.getElementById('files-to-delete'); if (input) input.value = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked')).map(box => box.closest('.file-item')?.dataset.fileId).filter(Boolean).join(','); }
        function syncSelectAllExistingFiles() { const button = document.getElementById('select-all-existing-files'); const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox')); if (!button || !boxes.length) return; const all = boxes.every(box => box.checked); button.textContent = all ? 'Deseleccionar todos' : 'Seleccionar todos'; button.setAttribute('aria-pressed', all ? 'true' : 'false'); }
        function toggleAllExistingFiles() { const boxes = Array.from(document.querySelectorAll('#existing-files-list .file-delete-checkbox')); const checked = !boxes.every(box => box.checked); boxes.forEach(box => { box.checked = checked; toggleFileStrikethrough(box); }); syncSelectAllExistingFiles(); }
        function toggleFileStrikethrough(box) { const row = box.closest('.file-item'); if (box.checked) row.dataset.markedForRemoval = 'true'; else delete row.dataset.markedForRemoval; updateFilesToDeleteInput(); updateFileCounters(); syncSelectAllExistingFiles(); }
        function setFileBadge(type, count, required) { const badge = document.getElementById(`${type}-status-badge`); if (!badge) return; const complete = count >= required; badge.textContent = complete ? 'Completado' : type === 'photos' ? `${count}/${required}` : 'Pendiente'; badge.dataset.status = complete ? 'complete' : count ? 'partial' : 'pending'; }
        function updateFileCounters() {
            const totals = { pdf: 0, excel: 0, photos: 0 }; document.querySelectorAll('#existing-files-list .file-item').forEach(row => { if (!row.querySelector('.file-delete-checkbox')?.checked && totals[row.dataset.fileType] !== undefined) totals[row.dataset.fileType]++; });
            selectedFiles.forEach(file => { const extension = file.name.split('.').pop().toLowerCase(); extension === 'pdf' ? totals.pdf++ : ['xlsx','xls'].includes(extension) ? totals.excel++ : totals.photos++; });
            setFileBadge('pdf', totals.pdf, 1); setFileBadge('excel', totals.excel, 1); setFileBadge('photos', totals.photos, 4);
        }
        function updateFileDisplay() {
            const list = document.getElementById('file-names'); const collection = document.getElementById('file-list'); const count = document.getElementById('selected-files-count'); if (!list || !collection) return; list.innerHTML = '';
            selectedFiles.forEach((file, index) => { const extension = file.name.split('.').pop().toLowerCase(); const icon = extension === 'pdf' ? 'fa-file-pdf' : ['xlsx','xls'].includes(extension) ? 'fa-file-excel' : 'fa-image'; const row = document.createElement('li'); row.className = 'report-road-file-row'; row.innerHTML = `<span class="report-road-file-icon" aria-hidden="true"><i class="far ${icon}"></i></span><div class="report-road-file-info"><p class="report-road-file-name"></p><p class="report-road-file-meta"><span class="report-road-file-format">${extension.toUpperCase()}</span><span>${(file.size / 1024 / 1024).toFixed(2)} MB</span></p></div><button type="button" class="report-road-file-remove" onclick="removeFile(${index})" title="Quitar archivo"><i class="fas fa-times" aria-hidden="true"></i></button>`; row.querySelector('.report-road-file-name').textContent = file.name; row.querySelector('.report-road-file-remove').setAttribute('aria-label', `Quitar ${file.name}`); list.appendChild(row); });
            if (count) count.textContent = selectedFiles.length === 1 ? '1 archivo seleccionado' : `${selectedFiles.length} archivos seleccionados`; collection.classList.toggle('hidden', selectedFiles.length === 0);
        }
        async function clearGruposVulnerablesForm() { const form = document.getElementById('gruposVulnerablesForm'); const canClear = window.confirmFormClear ? await window.confirmFormClear(form, selectedFiles.length) : false; if (!canClear) return; form?.reset(); selectedFiles = []; updateFileDisplay(); updateFileCounters(); window.resetGruposVulnerablesTomSelects?.(); window.showToast?.('Formulario limpiado.', 'info', 2400); }
        document.addEventListener('DOMContentLoaded', function() { const drop = document.getElementById('file-drop-zone'); const input = document.getElementById('file-input'); document.getElementById('choose-files-button')?.addEventListener('click', () => input?.click()); if (drop && input) { ['dragenter','dragover','dragleave','drop'].forEach(name => drop.addEventListener(name, event => { event.preventDefault(); event.stopPropagation(); })); ['dragenter','dragover'].forEach(name => drop.addEventListener(name, () => drop.dataset.dragActive = 'true')); ['dragleave','drop'].forEach(name => drop.addEventListener(name, () => delete drop.dataset.dragActive)); drop.addEventListener('drop', event => addFiles(event.dataTransfer.files)); } updateFileCounters(); });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const municipalityToDistrict = @json($municipalities->mapWithKeys(fn($item) => [$item->id => $item->district_id])); const districtNames = @json($districts->mapWithKeys(fn($item) => [$item->id => $item->name]));
            const currentDistrict = @json(optional(auth()->user())->district_id); const canSelectAnyDistrict = @json($canSelectAnyDistrict ?? false); const oldDistrict = @json(old('jurisdiccion', '')); const oldMunicipality = @json(old('municipio', isset($report) ? $report->municipality_id : ''));
            const municipality = document.getElementById('grupos_municipality_select'); const district = document.getElementById('jurisdiction_select_gv'); const activity = document.getElementById('activity_type_id'); const districtDisplay = document.getElementById('jurisdiction_display_gv'); const hiddenDistrict = document.getElementById('jurisdiction_input_gv'); const clearFilter = document.getElementById('clear-district-filter-groups'); const form = document.getElementById('gruposVulnerablesForm'); let loadId = 0;
            const validityInput = select => select?.tomselect?.control_input || select?.tomselect?.input || select; const clearSelectError = select => validityInput(select)?.setCustomValidity?.(''); const showSelectError = (select, message) => validityInput(select)?.setCustomValidity?.(message);
            function reportValidity(select) { select?.tomselect ? select.tomselect.focus() : select?.focus(); const input = validityInput(select); input?.reportValidity ? input.reportValidity() : select?.reportValidity?.(); }
            function validateSelect(select, message) { if (!select || !select.required || select.value) { clearSelectError(select); return true; } showSelectError(select, message); return false; }
            function selectedDistrict() { if (!canSelectAnyDistrict && currentDistrict) return String(currentDistrict); return canSelectAnyDistrict && district ? String(district.value || '') : ''; }
            function fetchMunicipalities(query) { let url = '/api/municipalities/search?q=' + encodeURIComponent(query) + '&limit=100'; const selected = selectedDistrict(); if (selected) url += '&district_id=' + encodeURIComponent(selected); return fetch(url).then(r => r.json()).then(items => items.map(item => ({ value: String(item.id), text: item.name }))); }
            function loadMunicipalities(open = false) { if (!municipality?.tomselect) return; const requestId = ++loadId; const requested = selectedDistrict(); fetchMunicipalities('').then(items => { if (requestId !== loadId || requested !== selectedDistrict()) return; const ts = municipality.tomselect; ts.clearOptions(); items.forEach(item => ts.addOption(item)); ts.refreshOptions(open); if (open) ts.open(); }).catch(() => {}); }
            function updateClearFilter() { if (clearFilter && district) clearFilter.hidden = !district.value; }
            function resetMunicipality(open = false) { const ts = municipality?.tomselect; if (!ts) { if (municipality) municipality.value = ''; return; } ts.clear(true); municipality.value = ''; ts.setTextboxValue(''); ts.clearOptions(); ts.clearCache?.(); loadMunicipalities(open); }
            if (activity) { new TomSelect(activity, { valueField: 'value', labelField: 'text', searchField: [], create: false, maxItems: 1, preload: false, maxOptions: 50 }); activity.addEventListener('change', () => clearSelectError(activity)); }
            if (canSelectAnyDistrict && district) { new TomSelect(district, { valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: false, maxOptions: 50 }); if (oldDistrict && districtNames[oldDistrict]) district.tomselect.setValue(String(oldDistrict), true); district.addEventListener('change', () => { clearSelectError(district); clearSelectError(municipality); resetMunicipality(false); updateClearFilter(); }); updateClearFilter(); }
            clearFilter?.addEventListener('click', () => { district.tomselect?.clear(true); district.value = ''; clearSelectError(district); clearSelectError(municipality); updateClearFilter(); resetMunicipality(true); });
            if (municipality) { new TomSelect(municipality, { valueField: 'value', labelField: 'text', searchField: ['text'], create: false, maxItems: 1, preload: true, maxOptions: 50, load: function(query, callback) { const requested = selectedDistrict(); fetchMunicipalities(query).then(items => callback(requested === selectedDistrict() ? items : [])).catch(() => callback()); } }); if (oldMunicipality) municipality.tomselect.setValue(String(oldMunicipality), true); municipality.addEventListener('change', () => { clearSelectError(municipality); const districtId = municipalityToDistrict[municipality.value]; if (districtId && canSelectAnyDistrict && district) { district.tomselect?.setValue(String(districtId), true); clearSelectError(district); loadMunicipalities(false); updateClearFilter(); } }); }
            if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || districtDisplay.value; }
            window.resetGruposVulnerablesTomSelects = function() { activity?.tomselect?.clear(true); if (activity) activity.value = ''; if (canSelectAnyDistrict && district) { district.tomselect?.clear(true); district.value = ''; updateClearFilter(); } resetMunicipality(false); clearSelectError(activity); clearSelectError(municipality); clearSelectError(district); if (!canSelectAnyDistrict && currentDistrict) { if (hiddenDistrict) hiddenDistrict.value = currentDistrict; if (districtDisplay) districtDisplay.value = districtNames[currentDistrict] || ''; } };
            form?.addEventListener('invalid', event => { const messages = new Map([[activity, 'Seleccione un tipo de actividad.'], [municipality, 'Seleccione un municipio.'], [district, 'Seleccione un distrito.']]); if (messages.has(event.target)) { event.preventDefault(); showSelectError(event.target, messages.get(event.target)); reportValidity(event.target); } }, true);
            form?.addEventListener('submit', function(event) {
                const fields = [[activity, 'Seleccione un tipo de actividad.'], [municipality, 'Seleccione un municipio.'], [canSelectAnyDistrict ? district : null, 'Seleccione un distrito.']]; const invalid = fields.find(([select, message]) => !validateSelect(select, message)); if (invalid) { event.preventDefault(); reportValidity(invalid[0]); return; }
                updateFilesToDeleteInput(); const edit = !!form.querySelector('input[name="_method"][value="PUT"]'); const counts = { pdf: 0, excel: 0, photos: 0 }; selectedFiles.forEach(file => { const extension = file.name.split('.').pop().toLowerCase(); extension === 'pdf' ? counts.pdf++ : ['xlsx','xls'].includes(extension) ? counts.excel++ : counts.photos++; });
                if (!edit && (counts.pdf < 1 || counts.excel < 1 || counts.photos < 4)) { event.preventDefault(); showFileError(`El reporte requiere: documento PDF (${counts.pdf}/1), archivo Excel (${counts.excel}/1), fotografías (${counts.photos}/4).`); return; }
                if (selectedFiles.length) { const transfer = new DataTransfer(); selectedFiles.forEach(file => transfer.items.add(file)); document.getElementById('file-input').files = transfer.files; }
            });
        });
    </script>
@endsection
