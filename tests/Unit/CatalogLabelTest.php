<?php

use App\Support\CatalogLabel;

it('formats catalog labels without changing their stored values', function () {
    expect(CatalogLabel::municipality('OTRO'))->toBe('Otro')
        ->and(CatalogLabel::municipality('NUEVO LAREDO'))->toBe('Nuevo Laredo')
        ->and(CatalogLabel::district('IV - REYNOSA'))->toBe('IV · Reynosa')
        ->and(CatalogLabel::cause('OTROS ACCIDENTES'))->toBe('Otros accidentes')
        ->and(CatalogLabel::cause('VEHICULO DE MOTOR RESIDENCIA'))->toBe('Vehículo de motor residencia')
        ->and(CatalogLabel::location('SERVICIOS DE SALUD IMSS BIENESTAR'))->toBe('Servicios de salud IMSS bienestar');
});

it('preserves approved acronyms in proper names and sentences', function () {
    expect(CatalogLabel::properName('CLINICA IMSS'))->toBe('Clinica IMSS')
        ->and(CatalogLabel::sentence('ATENCION ISSSTE'))->toBe('Atencion ISSSTE');
});
