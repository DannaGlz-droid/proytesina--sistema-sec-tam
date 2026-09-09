<?php

use App\Http\Controllers\DeathImportController;

function validateImportRow(array $row): array
{
    $controller = app(DeathImportController::class);
    $method = new ReflectionMethod($controller, 'validateAndNormalizeImportRow');

    return $method->invoke($controller, $row);
}

it('normalizes a valid import row with the same rules used by retries', function (): void {
    $result = validateImportRow([
        'folio' => '123-456-789',
        'nombre' => 'Lucía',
        'primerapellido' => 'Ortiz',
        'segundoapellido' => 'Navarro',
        'sexod' => 'Femenino',
        'edad' => 11,
        'claveedadd' => 'Meses',
        'fechadefuncion' => now()->subDay()->format('Y-m-d'),
    ]);

    expect($result['errors'])->toBeEmpty()
        ->and($result['folio'])->toBe('123456789')
        ->and($result['sex'])->toBe('F')
        ->and($result['age_years'])->toBe(0)
        ->and($result['age_months'])->toBe(11)
        ->and($result['age_days'])->toBeNull();
});

it('reports every detectable problem in a row instead of stopping at the first one', function (): void {
    $result = validateImportRow([
        'folio' => 'folio incorrecto',
        'nombre' => '',
        'primerapellido' => '',
        'sexod' => 'desconocido',
        'edad' => 'doce',
        'claveedadd' => 'quincenas',
        'fechadefuncion' => 'fecha imposible',
    ]);

    expect($result['errors'])->toHaveCount(7)
        ->and($result['errors'])->toContain('Nombre: este campo es obligatorio.')
        ->and($result['errors'])->toContain('Primer apellido: este campo es obligatorio.')
        ->and($result['errors'])->toContain('Folio: ingrese 9 dígitos o un folio alfanumérico de defunción válido.')
        ->and($result['errors'])->toContain('Sexo: seleccione Masculino o Femenino.')
        ->and($result['errors'])->toContain('Edad: ingrese un número entero.')
        ->and($result['errors'])->toContain('Unidad de edad: seleccione años, meses o días.')
        ->and($result['errors'])->toContain('Fecha de defunción: ingrese una fecha válida.');
});

it('validates the range that corresponds to each age unit', function (string $unit, int $age, string $expected): void {
    $result = validateImportRow([
        'folio' => '123456789',
        'nombre' => 'Marcos',
        'primerapellido' => 'Torres',
        'sexod' => 'M',
        'edad_valor' => $age,
        'edad_unidad' => $unit,
        'fechadefuncion' => now()->format('Y-m-d'),
    ]);

    expect($result['errors'])->toContain($expected);
})->with([
    ['anos', 151, 'Edad: para años, ingrese un valor entre 0 y 150.'],
    ['meses', 12, 'Edad: para meses, ingrese un valor entre 0 y 11.'],
    ['dias', 31, 'Edad: para días, ingrese un valor entre 0 y 30.'],
]);

it('recognizes the misspelled other accidents worksheet name explicitly', function (): void {
    $controller = app(DeathImportController::class);
    $aliasesMethod = new ReflectionMethod($controller, 'deathCauseAliases');
    $aliases = $aliasesMethod->invoke($controller);

    expect($aliases)->toHaveKey('OTROS ACCIDENTEES')
        ->and($aliases['OTROS ACCIDENTEES'])->toBe('OTROS ACCIDENTES');
});
