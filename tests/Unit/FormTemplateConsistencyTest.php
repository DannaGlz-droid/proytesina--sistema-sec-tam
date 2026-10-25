<?php

function projectView(string $relativePath): string
{
    return file_get_contents(dirname(__DIR__, 2).'/resources/views/'.$relativePath);
}

it('uses the shared administrative form page in the reference create and edit views', function (): void {
    $views = [
        'estadisticas/acciones/registro.blade.php',
        'estadisticas/acciones/actualizar-registro.blade.php',
        'usuarios/acciones/registro.blade.php',
        'usuarios/acciones/actualizar-registro.blade.php',
    ];

    foreach ($views as $view) {
        expect(projectView($view))->toContain('<x-ui.form.page');
    }
});

it('keeps death create and edit fields in one shared partial', function (): void {
    $create = projectView('estadisticas/acciones/registro.blade.php');
    $edit = projectView('estadisticas/acciones/actualizar-registro.blade.php');
    $sharedInclude = "@include('estadisticas.acciones.partials.death-form-fields')";

    expect($create)->toContain($sharedInclude)
        ->and($edit)->toContain($sharedInclude)
        ->and($create)->not->toContain('<x-ui.form.section title="Información de la persona"')
        ->and($edit)->not->toContain('<x-ui.form.section title="Información de la persona"');
});

it('does not validate death selects merely for losing focus', function (): void {
    $create = projectView('estadisticas/acciones/registro.blade.php');
    $edit = projectView('estadisticas/acciones/actualizar-registro.blade.php');

    expect($create)->not->toContain("instance.on('blur'")
        ->and($edit)->not->toContain("instance.on('blur'")
        ->and($create)->toContain('validationAttempted = true;')
        ->and($edit)->toContain('validationAttempted = true;')
        ->and($create)->toContain('Seleccione el lugar de defunción.')
        ->and($edit)->toContain('Seleccione el lugar de defunción.')
        ->and($create)->not->toContain('Seleccione el lugar específico.')
        ->and($edit)->not->toContain('Seleccione el lugar específico.');
});

it('uses one inline validation contract without native validity bubbles', function (): void {
    $views = [
        'estadisticas/acciones/registro.blade.php',
        'estadisticas/acciones/actualizar-registro.blade.php',
        'usuarios/acciones/registro.blade.php',
        'usuarios/acciones/actualizar-registro.blade.php',
    ];

    foreach ($views as $view) {
        expect(projectView($view))
            ->toContain('novalidate')
            ->toContain('data-ui-inline-validation')
            ->not->toContain('reportValidity(');
    }
});

it('keeps one password visibility control in Edge', function (): void {
    $createUser = projectView('usuarios/acciones/registro.blade.php');
    $updatePassword = projectView('usuarios/acciones/actualizar-contrasena.blade.php');
    $passwordDialog = projectView('components/user-password-dialog.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect(substr_count($createUser, 'app-password-input-wrap'))->toBe(2)
        ->and(substr_count($updatePassword, 'app-password-input-wrap'))->toBe(2)
        ->and(substr_count($passwordDialog, 'app-password-input-wrap'))->toBe(2)
        ->and($styles)->toContain('.app-password-input-wrap input[type="password"]::-ms-reveal')
        ->and($styles)->toContain('.app-password-input-wrap input[type="password"]::-ms-clear');
});

it('marks death form navigation to restore the originating table context', function (): void {
    $data = projectView('estadisticas/datos.blade.php');
    $actions = projectView('estadisticas/partials/table-actions.blade.php');
    $create = projectView('estadisticas/acciones/registro.blade.php');
    $edit = projectView('estadisticas/acciones/actualizar-registro.blade.php');

    expect($data)->toContain('data-deaths-table-return=')
        ->toContain('deaths-table-context.v1.')
        ->toContain('syncDeathsPageLengthControl(initialDeathsPageLength)')
        ->toContain("option.setAttribute('aria-selected', String(isSelected))")
        ->and($actions)->toContain('data-deaths-table-return=')
        ->and($create)->toContain('history-back-scope="deaths"')
        ->and($edit)->toContain('history-back-scope="deaths"');
});

it('keeps death form labels with help icons aligned with regular labels', function (): void {
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/design-system/form-help.css');

    expect($styles)->toContain('.users-form-page .statistics-form-label-row')
        ->toContain('height: .9rem;')
        ->toContain('min-height: .9rem;')
        ->toContain('margin-bottom: .32rem;')
        ->toContain('min-height: .875rem !important;');
});

it('keeps the alcohol municipality and district controls aligned when showing the filter action', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-road-safety-form.css');

    expect($view)->toContain('class="report-road-field-label-row report-road-municipality-label-row"')
        ->toContain("@if(\$canSelectAnyDistrict ?? false)\n                                <div class=\"report-road-field-label-row\">")
        ->toContain('class="report-road-clear-district-filter"')
        ->and($styles)->toContain('.report-road-field-label-row')
        ->toContain('align-items: center;')
        ->toContain('height: .9rem;')
        ->toContain('min-height: .9rem;')
        ->toContain('.report-road-clear-district-filter')
        ->toContain('min-height: 0 !important;');
});

it('applies the complete shared form contract to alcohol reports', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $section = projectView('components/ui/form/section.blade.php');
    $buttons = projectView('components/form-buttons.blade.php');
    $validation = file_get_contents(dirname(__DIR__, 2).'/resources/js/form-validation.js');

    expect($view)->toContain('<x-ui.form.page')
        ->toContain('<x-ui.form.section')
        ->toContain('novalidate data-ui-inline-validation')
        ->toContain('window.AppFilterSelect.init')
        ->not->toContain('new TomSelect(')
        ->not->toContain('reportValidity(')
        ->toContain('Restablecer cambios')
        ->toContain('resetAlcoholimetriaEditForm')
        ->toContain('municipalitySelect.clear(true);')
        ->toContain("if (municipality) municipality.value = '';")
        ->toContain("isEdit ? '¿Deseas salir de la edición?' : '¿Deseas salir del registro?'")
        ->toContain("isEdit ? 'Los cambios realizados no se guardarán.' : 'Los datos capturados no se guardarán.'")
        ->toContain("window.addEventListener('beforeunload'")
        ->not->toContain("if (isEdit) {\n                document.addEventListener('click'")
        ->toContain('getAlcoholFileCounts')
        ->toContain('data-ui-error-slot')
        ->not->toContain('<div class="report-road-form-actions">')
        ->and($section)->toContain("'variant' => null")
        ->toContain("'contentClass' => null")
        ->and($buttons)->not->toContain("!request()->is('reportes/*/*/edit')")
        ->and($validation)->toContain("error.hasAttribute('data-ui-error-slot')")
        ->toContain('syncErrorDescription(field, error, invalid)');
});

it('applies the complete shared form contract to road safety reports', function (): void {
    $view = projectView('reportes/registro/seguridad-vial.blade.php');

    expect($view)->toContain('<x-ui.form.page')
        ->toContain('<x-ui.form.section')
        ->toContain('novalidate data-ui-inline-validation')
        ->toContain('@foreach($activityTypes as $activityType)')
        ->toContain('window.AppFilterSelect.init')
        ->not->toContain('new TomSelect(')
        ->not->toContain('reportValidity(')
        ->toContain('Restablecer cambios')
        ->toContain('resetSeguridadVialEditForm')
        ->toContain('resetSeguridadVialTomSelects')
        ->toContain("old('files_to_delete', '')")
        ->toContain('@checked($markedForDeletion)')
        ->toContain('data-marked-for-removal="true"')
        ->toContain('municipalitySelect.clear(true);')
        ->toContain("if (municipality) municipality.value = '';")
        ->toContain("window.addEventListener('beforeunload'")
        ->toContain('getRoadSafetyFileCounts')
        ->toContain("setFileBadge('pdf', pdf, 1)")
        ->toContain("setFileBadge('excel', excel, 1)")
        ->toContain("setFileBadge('photos', photos, 4)")
        ->toContain('data-ui-error-slot')
        ->not->toContain('<div class="report-road-form-actions">');
});

it('applies the complete shared form contract to vulnerable groups reports', function (): void {
    $view = projectView('reportes/registro/grupos-vulnerables.blade.php');

    expect($view)->toContain('<x-ui.form.page')
        ->toContain('<x-ui.form.section')
        ->toContain('novalidate data-ui-inline-validation')
        ->toContain('@foreach($activityTypes as $activityType)')
        ->toContain('window.AppFilterSelect.init')
        ->not->toContain('new TomSelect(')
        ->not->toContain('reportValidity(')
        ->toContain('Restablecer cambios')
        ->toContain('resetGruposVulnerablesEditForm')
        ->toContain('resetGruposVulnerablesTomSelects')
        ->toContain("old('files_to_delete', '')")
        ->toContain('@checked($markedForDeletion)')
        ->toContain('data-marked-for-removal="true"')
        ->toContain('municipalitySelect.clear(true);')
        ->toContain("if (municipality) municipality.value = '';")
        ->toContain("window.addEventListener('beforeunload'")
        ->toContain('getVulnerableGroupsFileCounts')
        ->toContain("setFileBadge('pdf', pdf, 1)")
        ->toContain("setFileBadge('excel', excel, 1)")
        ->toContain("setFileBadge('photos', photos, 4)")
        ->toContain('data-ui-error-slot')
        ->not->toContain('<div class="report-road-form-actions">');
});

it('applies the complete shared form contract to injury observatory reports', function (): void {
    $view = projectView('reportes/registro/observatorio-de-lesiones.blade.php');

    expect($view)->toContain('<x-ui.form.page')
        ->toContain('<x-ui.form.section')
        ->toContain('novalidate data-ui-inline-validation')
        ->toContain('window.AppFilterSelect.init')
        ->not->toContain('new TomSelect(')
        ->not->toContain('reportValidity(')
        ->toContain('Restablecer cambios')
        ->toContain('resetObservatorioEditForm')
        ->toContain('resetObservatorioTomSelects')
        ->toContain("old('files_to_delete', '')")
        ->toContain('@checked($markedForDeletion)')
        ->toContain('data-marked-for-removal="true"')
        ->toContain('municipalitySelect.clear(true);')
        ->toContain("if (municipality) municipality.value = '';")
        ->toContain("window.addEventListener('beforeunload'")
        ->toContain('getObservatoryFileCounts')
        ->toContain("setFileBadge('excel', excel, 1)")
        ->toContain('accept=".xlsx,.xls"')
        ->toContain('data-ui-error-slot')
        ->not->toContain('name="activity_type_id"')
        ->not->toContain('name="participantes"')
        ->not->toContain('name="lugar"')
        ->not->toContain('name="promotor"')
        ->not->toContain('<div class="report-road-form-actions">');
});

it('prevents automatic iOS zoom without disabling manual viewport zoom', function (): void {
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($styles)->toContain('iOS Safari zooms the viewport')
        ->toContain('@media (max-width: 760px)')
        ->toContain('.users-form-page input:not([type="checkbox"]):not([type="radio"])')
        ->toContain('.users-form-page .users-form-card form .ts-wrapper.app-filter-select--form .ts-control > input')
        ->toContain('font-size: 1rem !important;')
        ->toContain('.users-form-page input:not([type="checkbox"]):not([type="radio"])::placeholder')
        ->toContain('font-size: 0.8rem !important;')
        ->and($guide)->toContain('tamaño computado de al menos 16 px')
        ->toContain('placeholder puede conservar la escala visual compacta de 0.80 rem')
        ->toContain('No bloquear el zoom manual');
});

it('refreshes shared select searches immediately before Enter can use a stale option', function (): void {
    $filterSelect = file_get_contents(dirname(__DIR__, 2).'/resources/js/filter-select.js');

    expect($filterSelect)
        ->toContain('refreshThrottle: 0,')
        ->toContain('puede quedar activa una opción de la consulta anterior');
});

it('keeps the invalid border visible on native fields and shared form selects', function (): void {
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');
    $validation = file_get_contents(dirname(__DIR__, 2).'/resources/js/form-validation.js');

    expect($styles)
        ->toContain('.users-form-page .users-form-card form [aria-invalid="true"]:not([type="checkbox"]):not([type="radio"])')
        ->toContain('.ts-wrapper.app-filter-select--form:has(select[aria-invalid="true"]) .ts-control')
        ->toContain('select[aria-invalid="true"] + .ts-wrapper.app-filter-select--form .ts-control')
        ->toContain('.ts-wrapper.app-filter-select--form:has(select[aria-invalid="true"]).focus .ts-control')
        ->and($validation)->toContain("field.setAttribute('aria-invalid', 'true')")
        ->not->toContain("field.toggleAttribute('aria-invalid'");
});

it('keeps field-specific validation messages above generic numeric fallbacks', function (): void {
    $validation = file_get_contents(dirname(__DIR__, 2).'/resources/js/form-validation.js');

    expect($validation)
        ->toContain('A field-specific rule knows the business constraint better')
        ->and(strpos($validation, 'if (validity.customError)'))
        ->toBeLessThan(strpos($validation, 'if (validity.rangeUnderflow'));
});

it('uses the specific death folio format message instead of a generic minimum length', function (): void {
    $fields = projectView('estadisticas/acciones/partials/death-form-fields.blade.php');
    $validation = file_get_contents(dirname(__DIR__, 2).'/resources/js/form-validation.js');
    $controller = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/DeathController.php');
    $message = 'Ingrese 9 dígitos o el folio alfanumérico oficial.';

    expect($fields)
        ->toContain('data-validation-minlength-message="'.$message.'"')
        ->toContain('data-validation-pattern-message="'.$message.'"')
        ->and($validation)
        ->toContain('field.dataset.validationMinlengthMessage')
        ->and(substr_count($controller, "'gov_folio.regex' => '".$message."'"))
        ->toBe(2);
});

it('renders the alcohol file error after the complete upload group', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');
    $requirements = file_get_contents(dirname(__DIR__, 2).'/app/Config/ReportFileRequirements.php');

    expect(strpos($view, 'id="file-error"'))
        ->toBeGreaterThan(strpos($view, 'id="file-list"'))
        ->and($view)->toContain('Mínimo 1 archivo · XLS o XLSX')
        ->toContain('Mínimo 1 archivo · JPG, JPEG o PNG')
        ->toContain('El reporte requiere como mínimo: archivo Excel (${excel}/1), fotografía (${photos}/1).')
        ->and($view)->toContain('archivos Excel (XLS o XLSX) y fotografías (JPG, JPEG o PNG)')
        ->and($requirements)->toContain('El reporte requiere como mínimo: ')
        ->and($guide)->toContain('zona de selección, lista de archivos seleccionados y, al final, mensaje de error del grupo')
        ->toContain('Mínimo N`, `Exactamente N` o `Máximo N')
        ->toContain('superar el mínimo no genera error')
        ->toContain('puede mostrar valores como `2/1`')
        ->toContain('si se admite JPEG, debe nombrarse junto con JPG y PNG')
        ->toContain('nunca se inserta entre la zona y una lista visible');
});

it('documents one shared checkbox contract for selection and descriptive form options', function (): void {
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($guide)
        ->toContain('### 6.3 Checkboxes')
        ->toContain('La variante oficial para **seleccionar elementos**')
        ->toContain('La variante **descriptiva de formulario**')
        ->toContain('La selección funcional nunca usa vino institucional ni rojo semántico')
        ->toContain('No crear casillas con estilos locales')
        ->toContain('Space` debe funcionar al navegar con teclado');
});

it('uses the file name as the discreet preview action in alcohol edit mode', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($view)
        ->toContain('route(\'reportes.file.preview\', $file)')
        ->toContain('class="report-road-file-preview-link"')
        ->toContain('target="_blank"')
        ->and($guide)
        ->toContain('su nombre funciona como enlace discreto')
        ->toContain('No repetir una acción textual `Ver` en cada fila');
});

it('uses a visual attachment mosaic without changing the alcohol upload contract', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-road-safety-form.css');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($view)
        ->toContain('id="file-input" name="archivos[]"')
        ->toContain('accept=".xlsx,.xls,.jpg,.jpeg,.png" multiple')
        ->toContain('class="file-delete-checkbox report-road-file-checkbox"')
        ->toContain('id="files-to-delete" name="files_to_delete"')
        ->toContain('report-road-file-card')
        ->toContain('URL.createObjectURL(file)')
        ->not->toContain('report-road-file-origin')
        ->and($styles)
        ->toContain('object-fit: contain')
        ->toContain('background: var(--ui-surface-section)')
        ->not->toContain('report-road-file-origin')
        ->and($guide)
        ->toContain('se permite un mosaico compacto')
        ->toContain('no el contrato de carga ni sus validaciones');
});

it('explains that selecting saved report files marks them for deletion', function (): void {
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-road-safety-form.css');
    $views = [
        'reportes/registro/alcoholimetria.blade.php',
        'reportes/registro/seguridad-vial.blade.php',
        'reportes/registro/observatorio-de-lesiones.blade.php',
        'reportes/registro/grupos-vulnerables.blade.php',
    ];

    foreach ($views as $view) {
        expect(projectView($view))
            ->toContain('Marcar todos para eliminar')
            ->toContain('Desmarcar todos')
            ->not->toContain('Marca los que deseas eliminar al actualizar.')
            ->not->toContain('report-road-file-bulk-control')
            ->not->toContain('Eliminar al actualizar:')
            ->toContain('para eliminar">');
    }

    expect($styles)
        ->toContain('.report-road-file-row[data-marked-for-removal="true"] .report-road-file-checkbox-visual')
        ->toContain('.report-road-file-row[data-marked-for-removal="true"] .report-road-file-checkbox-visual i');
});

it('exposes the full name of selected report files when the card truncates it', function (): void {
    $views = [
        'reportes/registro/alcoholimetria.blade.php',
        'reportes/registro/seguridad-vial.blade.php',
        'reportes/registro/observatorio-de-lesiones.blade.php',
        'reportes/registro/grupos-vulnerables.blade.php',
    ];

    foreach ($views as $view) {
        expect(projectView($view))
            ->toContain('fileNameElement.textContent = file.name')
            ->toContain('fileNameElement.title = file.name');
    }
});

it('adapts the shared attachment layout to every report requirement count', function (): void {
    $roadSafety = projectView('reportes/registro/seguridad-vial.blade.php');
    $vulnerableGroups = projectView('reportes/registro/grupos-vulnerables.blade.php');
    $injuryObservatory = projectView('reportes/registro/observatorio-de-lesiones.blade.php');
    $alcohol = projectView('reportes/registro/alcoholimetria.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-road-safety-form.css');

    expect($roadSafety)
        ->toContain('report-road-file-requirements report-road-file-requirements--triple')
        ->toContain('report-road-file-collection--columns-3')
        ->toContain('report-road-file-collection--mosaic')
        ->toContain('report-road-file-card')
        ->and($vulnerableGroups)
        ->toContain('report-road-file-requirements report-road-file-requirements--triple')
        ->toContain('report-road-file-collection--columns-3')
        ->toContain('report-road-file-collection--mosaic')
        ->toContain('report-road-file-card')
        ->and($injuryObservatory)
        ->toContain('report-road-file-requirements report-road-file-requirements--single')
        ->toContain('report-road-file-collection--columns-1')
        ->toContain('<section id="file-list" class="report-road-file-collection report-road-file-collection--mosaic hidden"')
        ->toContain('report-road-file-collection--mosaic')
        ->toContain('report-road-file-card')
        ->and($alcohol)
        ->toContain('class="report-road-file-requirements"')
        ->toContain('report-road-file-collection--columns-2')
        ->toContain('report-road-file-collection--mosaic')
        ->and($styles)
        ->toContain('.report-road-file-requirements--single')
        ->toContain('.report-road-file-requirements--triple')
        ->toContain('.report-road-file-collection--columns-1')
        ->toContain('.report-road-file-collection--columns-2')
        ->toContain('.report-road-file-collection--columns-3');
});

it('keeps the per-file size limit inside every report upload zone', function (): void {
    $forms = [
        'reportes/registro/alcoholimetria.blade.php',
        'reportes/registro/seguridad-vial.blade.php',
        'reportes/registro/observatorio-de-lesiones.blade.php',
        'reportes/registro/grupos-vulnerables.blade.php',
    ];

    foreach ($forms as $form) {
        expect(projectView($form))
            ->toContain('report-road-upload-zone-details')
            ->toContain('Máx. 10 MB por archivo · Puedes seleccionar varios')
            ->not->toContain('Subir archivos (selección múltiple)')
            ->not->toContain('<p>Máximo 10 MB por archivo.</p>');
    }
});

it('keeps the institutional navigation above open form dropdowns', function (): void {
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect($styles)
        ->toContain('--ui-shell-header-layer: 110;')
        ->toContain('--ui-shell-subnav-layer: 100;')
        ->toMatch('/\.app-top-header\s*\{[^}]*z-index:\s*var\(--ui-shell-header-layer\)/s')
        ->toMatch('/\.app-subnav\s*\{[^}]*z-index:\s*var\(--ui-shell-subnav-layer\)/s');
});

it('uses the shared correction form contract for failed imports', function (): void {
    $view = projectView('estadisticas/failed-imports.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/statistics-imports.css');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($view)
        ->toContain('class="correction-form ui-form-fields" novalidate data-ui-inline-validation')
        ->toContain('failed-correction-section failed-correction-section--identity')
        ->toContain('failed-correction-section failed-correction-section--location')
        ->toContain('failed-correction-section failed-correction-section--death')
        ->toContain('data-select-variant="form"')
        ->toContain('window.AppFilterSelect?.init')
        ->toContain("field.closest('.failed-age-fields')?.parentElement")
        ->toContain("title: 'Descartar cambios'")
        ->toContain('showServerFieldErrors(form, pendingFieldErrors, false)')
        ->toContain("form.dataset.validationAttempted = 'true'")
        ->toContain("form.dataset.validationAttempted !== 'true' && !field.hasAttribute('aria-invalid')")
        ->toContain("if (firstInvalid) form.dataset.validationAttempted = 'true'")
        ->toContain("form?.dataset.validationAttempted === 'true'")
        ->toContain('delete form.dataset.validationAttempted')
        ->toContain("field?.closest('.failed-correction-field')")
        ->toContain("?.querySelector('.failed-field-error')")
        ->toContain("field.classList.add('has-field-error')")
        ->toContain("field.classList.remove('has-field-error')")
        ->toContain("activeButton.setAttribute('aria-busy', 'true')")
        ->toContain("activeButton.removeAttribute('aria-busy')")
        ->toContain('activeButton.style.width = `${activeButton.getBoundingClientRect().width}px`')
        ->toContain('id="failed-imports-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"')
        ->not->toContain('failed-record-errors-toggle')
        ->not->toMatch('/class="failed-record-error"[^>]*aria-live=/')
        ->toContain('const toastMethod = window.AppToast?.[type]')
        ->toContain('function hasUnsavedFailedImportChanges()')
        ->toContain('card.dataset.editingFormBaseline')
        ->toContain('card.dataset.editingFormBaseline = JSON.stringify(correctionFormSnapshot(form))')
        ->toContain('delete card.dataset.editingFormBaseline')
        ->toContain('async function confirmDiscardFailedImportChanges(options = {})')
        ->toContain("window.addEventListener('beforeunload'")
        ->toContain("question: '¿Deseas cambiar de página?'")
        ->toContain("question: '¿Deseas salir de esta página?'")
        ->toContain("announceFailedImportStatus('Modo de edición activado.')")
        ->toContain('function validateCorrectionForm(form, shouldFocus = true)')
        ->toMatch('/validateCorrectionForm\(form, false\);\s*showServerFieldErrors\(form, pendingFieldErrors, false\);/')
        ->toContain("announceFailedImportStatus('Edición cancelada.')")
        ->toContain("shouldRetry ? 'Importando…' : 'Guardando…'")
        ->toContain("true, 'Descartando…'")
        ->toContain('function playRecordModeTransition(card, mode)')
        ->toContain("playRecordModeTransition(card, 'edit')")
        ->toContain("playRecordModeTransition(card, 'view')")
        ->toContain('<span data-button-label>Descartar registro</span>')
        ->toContain('<span data-button-label>Editar registro</span>')
        ->toContain('<span data-button-label>Importar registro</span>')
        ->toContain('<span data-button-label>Cancelar</span>')
        ->toContain('<span data-button-label>Guardar corrección</span>')
        ->toContain('<span data-button-label>Guardar e importar</span>')
        ->toContain('function getUnsavedFailedImportCards()')
        ->toContain('const unsavedCards = getUnsavedFailedImportCards()')
        ->toContain('Los cambios de las ${unsavedCount} correcciones que estás editando no se guardarán.')
        ->not->toContain('excludedCard: card')
        ->not->toContain("question: '¿Deseas importar este registro?'")
        ->not->toContain("question: '¿Deseas descartar este registro y continuar?'")
        ->toContain('function removeRecordCardAndUpdateList(card)')
        ->toContain('function fillFailedRecordPageGap(recordsList)')
        ->toContain('const append = options.append === true')
        ->toContain('renderRecords(missingRecords, { append: true })')
        ->toContain("card.querySelectorAll('select.tomselect-select')")
        ->toContain('failedImportsPaginationState.total - 1')
        ->toContain("recordsList?.querySelectorAll('.record-card').length")
        ->toContain('removeRecordCardAndUpdateList(card)')
        ->toMatch('/btn-discard[^>]*>\s*<i class="fas fa-spinner fa-spin" data-button-spinner[^>]*hidden><\/i>\s*<span data-button-label>Descartar registro<\/span>/s')
        ->toMatch('/btn-import-corrected[^>]*hidden[^>]*>\s*<i class="fas fa-spinner fa-spin" data-button-spinner[^>]*hidden><\/i>\s*<span data-button-label>Importar registro<\/span>/s')
        ->toMatch('/btn-save-correction[^>]*>\s*<i class="fas fa-spinner fa-spin" data-button-spinner[^>]*hidden><\/i>\s*<span data-button-label>Guardar corrección<\/span>/s')
        ->toMatch('/btn-retry[^>]*>\s*<i class="fas fa-spinner fa-spin" data-button-spinner[^>]*hidden><\/i>\s*<span data-button-label>Guardar e importar<\/span>/s')
        ->not->toMatch('/btn-toggle-edit[^>]*>\s*<i\b/s')
        ->not->toMatch('/btn-cancel-edit[^>]*>\s*<i\b/s')
        ->toContain('Información de la persona')
        ->toContain('Lugar de defunción')
        ->toContain("fieldMessage.charAt(0).toLocaleUpperCase('es-MX')")
        ->toContain('capitalizePrefixedErrorMessage(friendlyRecordError(error))')
        ->toContain("statusTitle.textContent = isCorrected ? 'Corrección guardada' : 'Requiere corrección'")
        ->toContain('const visibleStatusMessages = isCorrected')
        ->toContain("['Edita el registro para revisar los campos señalados.']")
        ->toContain('card.dataset.statusMessages = JSON.stringify(statusMessages)')
        ->toContain('renderRecordErrors(card, visibleStatusMessages)')
        ->toContain("sitiodefunciond: 'Seleccione el lugar de defunción.'")
        ->toContain('placeholder="Ej: 230787888 o 28M19673E00000007"')
        ->toContain('name="nombre" type="text" value="" disabled required minlength="2"')
        ->toContain('name="primerapellido" type="text" value="" disabled required minlength="2"')
        ->toContain('name="segundoapellido" type="text" value="" disabled minlength="2"')
        ->toContain('data-placeholder="Seleccione la unidad"')
        ->toContain('Use años para edades de 12 meses o más.')
        ->toContain("form.querySelectorAll('.statistics-form-help-popover')")
        ->toContain('trigger.setAttribute(\'aria-describedby\', tooltipId)')
        ->toContain('field.id = `failed-${recordId}-${safeName}`')
        ->toContain('label.htmlFor = field.id')
        ->toContain('searchable: selectIsSearchable(name)')
        ->toContain("return !['sexod', 'edad_unidad'].includes(name)")
        ->toContain("if (name === 'edad_unidad') syncCorrectionAgeLimit(el.closest('form'))")
        ->toContain('const limits = { anos: 150, meses: 11, dias: 30 };')
        ->toContain("'display_name' => \$item->display_name")
        ->toContain('const municipalitiesData = @json($municipalitySelectData);')
        ->not->toContain('const municipalitiesData = @json($municipalities->map')
        ->toContain("name: item.display_name || item.name")
        ->toContain("rawName: item.name")
        ->toContain("normalize(option.display_name) === normalizedValue")
        ->toContain("\$jur ? \$jur->display_name : ''")
        ->toContain('syncDistrictFields(form)')
        ->toContain("[helpTooltip?.id, errorId].filter(Boolean).join(' ')")
        ->toContain("find(element => element?.classList.contains('failed-field-error'))")
        ->not->toContain('onBlur() {')
        ->not->toContain("field.addEventListener('blur'")
        ->and(substr_count($view, 'class="statistics-form-help-trigger"'))->toBe(3)
        ->and($view)
        ->not->toContain('Información del fallecido')
        ->not->toContain('Lugar específico')
        ->not->toContain('new TomSelect(')
        ->not->toContain('failed-imports-legacy-header')
        ->toContain('id="failed-imports-pending-count"')
        ->toContain("pendingSummary.textContent = totalPending === 1")
        ->toContain('registros pendientes de revisión')
        ->and($styles)
        ->toContain('.statistics-failed-imports-page .failed-correction-section-heading')
        ->toMatch('/\.statistics-failed-imports-page \.failed-correction-section-heading\s*\{[^}]*padding:\s*\.58rem \.82rem;/s')
        ->toContain('.statistics-failed-imports-page .failed-correction-grid--three')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1024px) and (max-width: 1279px)')
        ->toContain('@media (max-width: 1023px)')
        ->toContain('.failed-correction-field > input')
        ->toContain('grid-template-columns: minmax(0, 1fr) minmax(6.5rem, .7fr);')
        ->toMatch('/\.statistics-failed-imports-page \.failed-location-place\s*\{\s*grid-column:\s*1;\s*\}/')
        ->toContain('.statistics-failed-imports-page .statistics-form-help-trigger:focus-visible')
        ->toContain('.statistics-failed-imports-page .statistics-form-help-popover:focus-within .statistics-form-tooltip')
        ->toContain('.failed-correction-section:has(.ts-wrapper.dropdown-active)')
        ->toContain('max-height: 11.5rem;')
        ->toMatch('/\.statistics-failed-imports-page \.correction-form \.ts-dropdown \.ts-dropdown-content\s*\{[^}]*padding:\s*0\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.correction-form \.ts-wrapper\.app-filter-select--form \.ts-dropdown \.option,[^{]+\{[^}]*padding:\s*\.42rem \.58rem\s*!important;[^}]*border-radius:\s*0\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.correction-form \.ts-wrapper\.app-filter-select--form \.ts-dropdown \.option,[^{]+\{[^}]*font-weight:\s*500\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.is-editing-fields \.correction-form input:not\(\[type="hidden"\]\),[^\{]+\{[^}]*padding:\s*0 \.72rem\s*!important;[^}]*box-shadow:\s*0 1px 2px rgba\(15, 23, 42, \.025\)\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.failed-correction-section-heading i\s*\{[^}]*font-size:\s*\.82rem;/s')
        ->toMatch('/\.statistics-failed-imports-page \.failed-correction-section-heading h2\s*\{[^}]*font-size:\s*\.82rem;[^}]*line-height:\s*1\.22;/s')
        ->toMatch('/\.statistics-failed-imports-page \.action-buttons-view \.ui-button,[^{]+\{[^}]*height:\s*34px\s*!important;[^}]*padding:\s*0 \.88rem\s*!important;[^}]*font-family:\s*"Open Sans", system-ui, sans-serif\s*!important;[^}]*font-weight:\s*600\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.action-buttons-view \.ui-button--primary,[^{]+\{[^}]*border:\s*1px solid var\(--brand-primary\)\s*!important;[^}]*background:\s*var\(--brand-primary\)\s*!important;/s')
        ->toContain('@media (max-width: 640px)')
        ->toContain('.record-card.is-editing-fields .correction-form input:not([type="hidden"]):not([readonly]):not([role="combobox"]):hover')
        ->toMatch('/\.record-card\.is-editing-fields \.correction-form input:not\(\[type="hidden"\]\):not\(\[readonly\]\):not\(\[role="combobox"\]\):focus\s*\{[^}]*border-color:\s*var\(--ui-focus, #475569\)\s*!important;[^}]*background:\s*var\(--ui-surface, #fff\)\s*!important;[^}]*box-shadow:\s*0 0 0 2px var\(--ui-focus-ring, rgba\(71, 85, 105, \.18\)\)\s*!important;/s')
        ->toMatch('/\.statistics-failed-imports-page \.is-editing-fields \.correction-form \.ts-control\s*\{[^}]*padding:\s*0 2rem 0 \.72rem\s*!important;[^}]*box-shadow:\s*0 1px 2px rgba\(15, 23, 42, \.04\)\s*!important;/s')
        ->toContain('.statistics-failed-imports-page .is-editing-fields .correction-form .ts-wrapper.app-filter-select--form .ts-control:hover')
        ->toContain('.statistics-failed-imports-page .is-editing-fields .correction-form .ts-wrapper.app-filter-select--form.focus .ts-control')
        ->toContain('.ts-wrapper.app-filter-select--form.single.ui-has-search-query .ts-control > .item')
        ->toContain('.record-card.is-editing-fields .correction-form [aria-invalid="true"]:not([type="hidden"]):focus')
        ->toContain('.ts-wrapper.app-filter-select--form:has(select[aria-invalid="true"]).dropdown-active .ts-control')
        ->toContain('select[aria-invalid="true"] + .ts-wrapper.app-filter-select--form .ts-control')
        ->toContain('select[aria-invalid="true"] + .ts-wrapper.app-filter-select--form.focus .ts-control')
        ->toContain('select[aria-invalid="true"] + .ts-wrapper.app-filter-select--form.dropdown-active .ts-control')
        ->toContain('.ts-wrapper.app-filter-select--form:has(.ts-control > input[aria-invalid="true"]) .ts-control')
        ->toContain('input.has-field-error:not([type="hidden"]):not([role="combobox"]):focus')
        ->toContain('select.has-field-error + .ts-wrapper.app-filter-select--form.focus .ts-control')
        ->toContain('.record-card[aria-busy="true"] .ui-button[aria-busy="true"]:disabled')
        ->not->toContain('.failed-record-errors-toggle')
        ->toContain('cursor: wait;')
        ->not->toContain('.failed-correction-field:has(.failed-field-error:not(.hidden))')
        ->toMatch('/\.record-card\.is-editing-fields \.correction-form \[aria-invalid="true"\]:not\(\[type="hidden"\]\):focus,[^{]+\{[^}]*border-color:\s*var\(--ui-danger, #dc2626\)\s*!important;[^}]*box-shadow:\s*0 0 0 2px rgba\(185, 28, 28, \.14\)\s*!important;/s')
        ->toContain('max-width: 100%')
        ->toContain('overflow-x: clip')
        ->not->toContain('.failed-imports-list > .record-card.is-editing-fields')
        ->not->toContain('.failed-imports-list > .record-card:only-child')
        ->toContain('@keyframes failed-import-edit-mode-enter')
        ->toContain('@keyframes failed-import-view-mode-enter')
        ->toContain('.record-card.is-entering-edit-mode .failed-correction-grid')
        ->toContain('.record-card.is-entering-view-mode .failed-correction-grid')
        ->toContain('transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease, box-shadow 160ms ease;')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('@media (max-width: 639px)')
        ->and($guide)
        ->toContain('`lucia ortiz`')
        ->toContain('**“Descartar registro”**, **“Editar registro”** e **“Importar registro”**')
        ->toContain('conservar los valores, las selecciones de catálogo y su formato visible después de recargar')
        ->toContain('retirar únicamente esa tarjeta del listado, actualizar el total y la paginación')
        ->toContain('una única tarjeta conserva el ancho de una columna del listado')
        ->toContain('no usan iconos decorativos permanentes')
        ->toContain('un desplazamiento vertical máximo de 3 px durante 140–160 ms')
        ->toContain('Los controles editables y sus acciones reutilizan la altura, tipografía, peso, radio, sombra, hover, foco, estado deshabilitado y carga del formulario canónico.')
        ->toContain('validar inmediatamente todos sus valores actuales y marcar cada campo inválido sin desplazar el foco')
        ->toContain('no se debe esperar al primer intento de guardado para revelar otros campos inválidos')
        ->toContain('conserva siempre el título **“Requiere corrección”**')
        ->toContain('**“Edita el registro para revisar los campos señalados.”**')
        ->toContain('No muestra cantidades, listas de errores ni acciones para desplegarlas')
        ->toContain('el detalle completo se conserva internamente')
        ->toContain('completa los errores históricos con la validación actual del registro, sin modificarlo')
        ->toContain('el estado `focus` siempre prevalece sobre `hover`')
        ->toContain('`hover` solo aplica a controles sin foco')
        ->toContain('el borde y el anillo rojos prevalecen sobre ambos estados')
        ->toContain('Varias tarjetas pueden permanecer en edición al mismo tiempo.')
        ->toContain('solicita una sola confirmación antes de descartarlos.')
        ->toContain('El mensaje usa singular o plural según la cantidad de correcciones modificadas.')
        ->toContain('Las acciones propias de una tarjeta no muestran advertencias por cambios pendientes en otras tarjetas')
        ->toContain('Solo una acción global que reemplace el listado puede solicitar confirmación');
});

it('animates adding and removing newly selected report files consistently', function (): void {
    $forms = [
        'reportes/registro/alcoholimetria.blade.php',
        'reportes/registro/seguridad-vial.blade.php',
        'reportes/registro/observatorio-de-lesiones.blade.php',
        'reportes/registro/grupos-vulnerables.blade.php',
    ];

    foreach ($forms as $form) {
        expect(projectView($form))
            ->toContain('const filesPendingEntryAnimation = new WeakSet();')
            ->toContain('filesPendingEntryAnimation.add(file);')
            ->toContain('report-road-file-card--entering')
            ->toContain('report-road-file-card--removing')
            ->toContain('onclick="removeFile(${index}, this)"')
            ->toContain('prefers-reduced-motion: reduce');
    }

    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-road-safety-form.css');

    expect($styles)
        ->toContain('@keyframes report-road-file-card-enter')
        ->toContain('@keyframes report-road-file-card-exit')
        ->toContain('.report-road-file-card--entering')
        ->toContain('.report-road-file-card--removing');
});

it('uses reporte as the user-facing term throughout the reports module', function (): void {
    $forms = [
        'reportes/registro/alcoholimetria.blade.php',
        'reportes/registro/seguridad-vial.blade.php',
        'reportes/registro/observatorio-de-lesiones.blade.php',
        'reportes/registro/grupos-vulnerables.blade.php',
    ];

    foreach ($forms as $form) {
        expect(projectView($form))
            ->toContain('back-label="Volver a reportes"')
            ->not->toContain('back-label="Volver a publicaciones"');
    }

    $index = projectView('reportes/publicaciones.blade.php');
    $email = projectView('emails/reportes/comment.blade.php');
    $controller = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/ReportController.php');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($index)
        ->toContain("title: 'Eliminar reporte'")
        ->toContain("showToast('Reporte eliminado.'")
        ->not->toContain("title: 'Eliminar publicación'")
        ->and($email)->toContain('Nuevo comentario en tu reporte')
        ->and($controller)->toContain('Solo los administradores pueden eliminar reportes aprobados.')
        ->toContain('Solo puedes comentar en tus propios reportes.')
        ->not->toContain("->with('error', 'No tienes permisos para editar esta publicación.')")
        ->and($guide)->toContain('el objeto visible se denomina `reporte`');
});

it('compacts alcohol quantities without changing their field contract', function (): void {
    $view = projectView('reportes/registro/alcoholimetria.blade.php');
    $styles = file_get_contents(dirname(__DIR__, 2).'/resources/css/pages/report-alcohol-form.css');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');
    $fields = [
        'puntos_revision',
        'pruebas_realizadas',
        'conductores_no_aptos',
        'mujeres_no_aptas',
        'hombres_no_aptos',
        'automoviles_camionetas',
        'motocicletas',
        'transporte_colectivo',
        'transporte_individual',
        'transporte_carga',
        'vehiculos_emergencia',
    ];

    foreach ($fields as $field) {
        expect($view)->toContain("['name' => '{$field}'");
    }

    expect($view)
        ->toContain('content-class="report-alcohol-metrics"')
        ->toContain('role="group"')
        ->toContain('aria-labelledby="{{ $groupTitleId }}"')
        ->toContain('type="number" name="{{ $fieldName }}" min="0" max="{{ $field[\'max\'] }}"')
        ->toContain('value="{{ $fieldValue }}" required inputmode="numeric"')
        ->toContain('data-validation-number-message="Capture un número entero entre 0 y {{ $field[\'max\'] }}."')
        ->toContain("['name' => 'puntos_revision', 'label' => 'Puntos de revisión instalados', 'placeholder' => 'Ej: 15', 'property' => 'checkpoints', 'max' => 9999]")
        ->toContain("['name' => 'pruebas_realizadas', 'label' => 'Pruebas realizadas', 'placeholder' => 'Ej: 250', 'property' => 'tests_performed', 'max' => 999999]")
        ->and($styles)->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('grid-template-columns: 13rem minmax(0, 1fr);')
        ->toContain('.report-alcohol-metric-group--primary')
        ->toContain('grid-column: 2;')
        ->not->toContain('padding-top: 1.32rem;')
        ->toContain('grid-template-columns: repeat(3, minmax(0, 1fr));')
        ->toContain('@media (max-width: 767px)')
        ->toContain('.report-alcohol-form-page .report-alcohol-metric-group:not(:first-child)')
        ->toContain('color: var(--ui-text-primary);')
        ->toContain('text-wrap: pretty;')
        ->and($guide)->toContain('Los grupos densos de cantidades breves pueden usar una variante compacta')
        ->toContain('debe reservar la misma columna de títulos y alinear el control con la primera columna de los subgrupos')
        ->toContain('No reducir los inputs a anchos fijos ni cambiar su contrato visual.');
});

it('prevents the mouse wheel from changing focused number inputs while preserving page scroll', function (): void {
    $app = file_get_contents(dirname(__DIR__, 2).'/resources/js/app.js');
    $behavior = file_get_contents(dirname(__DIR__, 2).'/resources/js/number-input-wheel.js');
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($app)->toContain("import './number-input-wheel';")
        ->and($behavior)
        ->toContain("input.type !== 'number'")
        ->toContain('document.activeElement !== input')
        ->toContain('input.blur();')
        ->toContain("document.addEventListener('wheel', releaseFocusedNumberInput")
        ->toContain('passive: true')
        ->not->toContain('preventDefault')
        ->and($guide)->toContain('debe desplazar la página sin alterar el valor');
});

it('documents restoration after a rejected report file update', function (): void {
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($guide)
        ->toContain('identificadores de `files_to_delete`')
        ->toContain('reconstruir `files_to_delete` como fuente de verdad')
        ->toContain('las mismas filas vuelven marcadas')
        ->toContain('nunca puede convivir con todas las filas desmarcadas')
        ->toContain('`input[type="file"]`')
        ->toContain('estado inicial en el DOM');
});

it('documents the complete create and edit form action lifecycle', function (): void {
    $guide = file_get_contents(dirname(__DIR__, 2).'/SISTEMA_DISENO_UI.md');

    expect($guide)
        ->toContain('### 6.8 Limpieza y cambios sin guardar')
        ->toContain('Ejecutar únicamente `form.reset()` no es suficiente')
        ->toContain('La protección no puede condicionarse únicamente al modo edición.')
        ->toContain('no depender únicamente del repintado de `input:checked`')
        ->toContain('se interpreta como reemplazo')
        ->toContain('especialmente los basados en WebKit en iOS')
        ->toContain('toda página que capture o envíe formularios debe servirse mediante HTTPS')
        ->toContain('persiste exactamente un alta o una actualización.');
});
