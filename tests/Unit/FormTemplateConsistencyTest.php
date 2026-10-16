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
        ->toContain('nunca se inserta entre la zona y una lista visible');
});
