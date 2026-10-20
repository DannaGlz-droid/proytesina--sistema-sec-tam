<?php

use App\Config\ReportFileRequirements;

it('keeps the injury observatory attachment requirement limited to Excel', function (): void {
    expect(ReportFileRequirements::getRequirements('observatorio')['required_files'])
        ->toBe([
            'excel' => ['min' => 1, 'extensions' => ['xlsx', 'xls']],
        ]);
});

it('protects injury observatory reports with the shared territory validation contract', function (): void {
    $request = file_get_contents(dirname(__DIR__, 2).'/app/Http/Requests/InjuryObservatoryReportRequest.php');

    expect($request)
        ->toContain('use App\\Models\\District;')
        ->toContain('use App\\Models\\Municipality;')
        ->toContain('use Illuminate\\Validation\\Rule;')
        ->toContain("District::numberedCatalog()->pluck('id')")
        ->toContain("where('district_id', \$userDistrictId)")
        ->toContain('Rule::in($allowedDistrictIds)')
        ->toContain("Municipality::find(\$this->input('municipio'))")
        ->toContain('El municipio no pertenece al distrito seleccionado.');
});
