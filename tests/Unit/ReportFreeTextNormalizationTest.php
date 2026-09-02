<?php

use App\Http\Requests\GruposVulnerablesReportRequest;
use App\Http\Requests\RoadSafetyReportRequest;

function expectConservativeReportNormalization(string $requestClass): void
{
    $request = $requestClass::create('/', 'POST', [
        'lugar' => '  CBTis   105  ',
        'promotor' => '  DIF   Tamaulipas  ',
    ]);

    $prepareForValidation = new ReflectionMethod($requestClass, 'prepareForValidation');
    $prepareForValidation->invoke($request);

    expect($request->input('lugar'))->toBe('CBTis 105')
        ->and($request->input('promotor'))->toBe('DIF Tamaulipas');
}

test('road safety report free text preserves capitalization', function () {
    expectConservativeReportNormalization(RoadSafetyReportRequest::class);
});

test('vulnerable groups report free text preserves capitalization', function () {
    expectConservativeReportNormalization(GruposVulnerablesReportRequest::class);
});
