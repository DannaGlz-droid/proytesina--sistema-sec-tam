<?php

use App\Config\ReportFileRequirements;

it('keeps vulnerable groups attachment requirements aligned with road safety', function (): void {
    expect(ReportFileRequirements::getRequirements('grupos-vulnerables')['required_files'])
        ->toBe(ReportFileRequirements::getRequirements('seguridad-vial')['required_files']);
});

it('protects vulnerable groups with the shared territory validation contract', function (): void {
    $request = file_get_contents(dirname(__DIR__, 2).'/app/Http/Requests/GruposVulnerablesReportRequest.php');

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
