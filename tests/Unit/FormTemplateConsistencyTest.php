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
