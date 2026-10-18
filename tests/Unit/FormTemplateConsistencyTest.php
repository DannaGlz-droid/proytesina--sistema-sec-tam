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
        ->and($edit)->toContain('validationAttempted = true;');
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
        ->toContain("window.addEventListener('beforeunload'")
        ->toContain('getAlcoholFileCounts')
        ->toContain('data-ui-error-slot')
        ->not->toContain('<div class="report-road-form-actions">')
        ->and($section)->toContain("'variant' => null")
        ->toContain("'contentClass' => null")
        ->and($buttons)->not->toContain("!request()->is('reportes/*/*/edit')")
        ->and($validation)->toContain("error.hasAttribute('data-ui-error-slot')")
        ->toContain('syncErrorDescription(field, error, invalid)');
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
        ->and($requirements)->toContain("El reporte requiere como mínimo: ")
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
        ->toContain('const showNewFileOrigin = @json(isset($publication));')
        ->toContain("showNewFileOrigin ? '<span class=\"report-road-file-origin\"")
        ->and($styles)
        ->toContain('object-fit: contain')
        ->toContain('background: var(--ui-surface-section)')
        ->and($guide)
        ->toContain('se permite un mosaico compacto')
        ->toContain('no el contrato de carga ni sus validaciones');
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
