<?php

use App\Exports\DeathsExport;
use App\Http\Controllers\DeathController;
use App\Http\Controllers\StatisticsController;
use App\Models\Death;
use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\District;
use App\Models\Municipality;
use App\Services\DeathFilterService;
use App\Services\StatisticsAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $district = District::create(['name' => 'I - PRUEBA']);
    $this->municipalityA = Municipality::create(['name' => 'Municipio A', 'district_id' => $district->id]);
    $this->municipalityB = Municipality::create(['name' => 'Municipio B', 'district_id' => $district->id]);
    $this->locationA = DeathLocation::create(['name' => 'Hospital']);
    $this->locationB = DeathLocation::create(['name' => 'Hogar']);
    $this->causeA = DeathCause::create(['name' => 'Causa A']);
    $this->causeB = DeathCause::create(['name' => 'Causa B']);
    $this->causeC = DeathCause::create(['name' => 'Causa C']);

    $causes = [$this->causeA, $this->causeA, $this->causeA, $this->causeB, $this->causeB, $this->causeC];
    foreach ($causes as $index => $cause) {
        Death::create([
            'gov_folio' => 'STAT-'.($index + 1),
            'name' => 'Persona '.($index + 1),
            'first_last_name' => 'Prueba',
            'age' => 10 + ($index * 12),
            'age_years' => 10 + ($index * 12),
            'sex' => $index % 2 === 0 ? 'M' : 'F',
            'death_date' => '2026-01-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'residence_municipality_id' => $index < 4 ? $this->municipalityA->id : $this->municipalityB->id,
            'district_id' => $district->id,
            'death_municipality_id' => $index < 2 ? $this->municipalityA->id : $this->municipalityB->id,
            'death_district_id' => $district->id,
            'death_location_id' => $index < 4 ? $this->locationA->id : $this->locationB->id,
            'death_cause_id' => $cause->id,
        ]);
    }
});

function statisticsChartResponse(string $type, array $filters = []): array
{
    $request = Request::create('/', 'GET', array_merge([
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
    ], $filters));

    return app(StatisticsController::class)->getChartData($request, $type)->getData(true);
}

it('keeps the filtered total while reporting Top N coverage', function (): void {
    $data = statisticsChartResponse('causas', ['limit' => 2]);

    expect($data['total'])->toBe(6)
        ->and($data['filtered_total'])->toBe(6)
        ->and($data['displayed_total'])->toBe(5)
        ->and($data['available_categories'])->toBe(3)
        ->and($data['displayed_categories'])->toBe(2)
        ->and($data['ranking_label'])->toBe('causas')
        ->and($data['coverage_percentage'])->toBe(83.3)
        ->and($data['period']['start_date'])->toBe('2026-01-01')
        ->and($data['period']['end_date'])->toBe('2026-01-31');
});

it('groups death locations by catalog entry and supports an optional Top N', function (): void {
    $all = statisticsChartResponse('lugares');
    $top = statisticsChartResponse('lugares', ['limit' => 1]);

    expect($all['labels'])->toBe(['Hospital', 'Hogar'])
        ->and($all['counts'])->toBe([4, 2])
        ->and($all['available_categories'])->toBe(2)
        ->and($all['ranking_label'])->toBe('lugares')
        ->and($all['quality']['required_fields'])->toBe(['Lugar de defunción'])
        ->and($top['labels'])->toBe(['Hospital'])
        ->and($top['displayed_total'])->toBe(4)
        ->and($top['filtered_total'])->toBe(6);
});

it('uses consistent presentation labels without changing catalog values', function (): void {
    $this->municipalityA->update(['name' => 'OTRO']);
    $this->causeA->update(['name' => 'OTROS ACCIDENTES']);

    $municipalities = statisticsChartResponse('municipios');
    $causes = statisticsChartResponse('causas');
    $districts = statisticsChartResponse('jurisdicciones');

    expect($municipalities['labels'])->toContain('Otro')
        ->and($municipalities['ranking_label'])->toBe('municipios')
        ->and($causes['labels'])->toContain('Otros accidentes')
        ->and($causes['ranking_label'])->toBe('causas')
        ->and($districts['labels'])->toContain('I · Prueba')
        ->and($districts['ranking_label'])->toBe('distritos')
        ->and($this->municipalityA->fresh()->name)->toBe('OTRO')
        ->and($this->causeA->fresh()->name)->toBe('OTROS ACCIDENTES');
});

it('orders municipalities by total and alphabetically when totals are tied', function (): void {
    $this->municipalityA->update(['name' => 'Zeta']);
    $this->municipalityB->update(['name' => 'Alfa']);

    $ranked = statisticsChartResponse('municipios');

    expect($ranked['labels'])->toBe(['Alfa', 'Zeta'])
        ->and($ranked['counts'])->toBe([4, 2]);

    Death::query()->where('gov_folio', 'STAT-3')->update([
        'death_municipality_id' => $this->municipalityA->id,
    ]);

    $tied = statisticsChartResponse('municipios');

    expect($tied['counts'])->toBe([3, 3])
        ->and($tied['labels'])->toBe(['Alfa', 'Zeta']);
});

it('does not double the analyzed total in residence and death comparison', function (): void {
    $data = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'residencia-defuncion',
        'limit' => 5,
    ]);

    expect($data['total'])->toBe(6)
        ->and($data['ranking_label'])->toBe('municipios')
        ->and(array_column($data['series'], 'name'))->toBe([
            'Municipio de residencia',
            'Municipio de defunción',
        ]);
});

it('returns real cause series for age and location comparisons', function (): void {
    $ageData = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'edad-causa',
        'limit' => 2,
    ]);
    $locationData = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'lugar-causa',
        'limit' => 2,
    ]);

    expect($ageData['series'])->not->toBeEmpty()
        ->and($ageData['stacked'])->toBeTrue()
        ->and($ageData['ranking_label'])->toBe('causas')
        ->and($ageData)->not->toHaveKey('residence_counts')
        ->and($locationData['series'])->not->toBeEmpty()
        ->and($locationData['stacked'])->toBeTrue()
        ->and($locationData['ranking_label'])->toBe('lugares')
        ->and($locationData)->not->toHaveKey('residence_counts');
});

it('applies equivalent filters to tables charts and exports', function (): void {
    $service = app(DeathFilterService::class);
    $tableInput = [
        'dateRange' => 'months',
        'year' => '2026',
        'selectedMonths' => ['1'],
        'municipio' => 'Municipio A',
        'sexo' => 'hombre',
        'edad' => '10',
        'causa' => $this->causeA->id,
    ];
    $chartInput = [
        'years' => ['2026'],
        'months' => ['1'],
        'municipios' => [$this->municipalityA->id],
        'municipio_kind' => 'residence',
        'sex' => 'M',
        'age' => '10',
        'causas' => [$this->causeA->id],
    ];

    $tableQuery = Death::query();
    $service->apply($tableQuery, $service->normalize($tableInput));

    $chartQuery = DB::table('deaths');
    $service->apply($chartQuery, $service->normalize($chartInput, [
        'municipality_column' => 'residence_municipality_id',
    ]));

    $exportQuery = (new DeathsExport($tableInput))->query();
    $tableData = app(DeathController::class)
        ->dataTable(Request::create('/', 'POST', $tableInput))
        ->getData(true);
    $chartData = app(StatisticsController::class)
        ->getChartData(Request::create('/', 'GET', $chartInput), 'causas')
        ->getData(true);

    expect($tableQuery->count())->toBe(1)
        ->and($chartQuery->count())->toBe(1)
        ->and($exportQuery->count())->toBe(1)
        ->and($tableData['recordsFiltered'])->toBe(1)
        ->and($chartData['filtered_total'])->toBe(1);
});

it('offers and applies origin filters to chart records', function (): void {
    $importId = DB::table('imports')->insertGetId([
        'original_name' => 'reporte estatal.xlsx',
        'status' => 'completed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Death::query()->where('gov_folio', 'STAT-1')->update(['import_id' => $importId]);

    $imported = app(DeathController::class)
        ->dataTable(Request::create('/', 'POST', ['length' => 25, 'origin' => "import:{$importId}"]))
        ->getData(true);
    $manual = app(DeathController::class)
        ->dataTable(Request::create('/', 'POST', ['length' => 25, 'origin' => 'manual']))
        ->getData(true);
    $controller = app(DeathController::class);
    $originOptions = new ReflectionMethod($controller, 'getAnalysisOriginOptions');
    $origins = collect($originOptions->invoke($controller, Death::query()))->keyBy('value');

    expect($imported['recordsFiltered'])->toBe(1)
        ->and($imported['data'][0]['gov_folio'])->toBe('STAT-1')
        ->and($manual['recordsFiltered'])->toBe(5)
        ->and((new DeathsExport(['origin' => "import:{$importId}"]))->query()->count())->toBe(1)
        ->and($origins->sum('records'))->toBe(6)
        ->and($origins["import:{$importId}"]['label'])->toBe('reporte estatal.xlsx')
        ->and($origins["import:{$importId}"]['records'])->toBe(1)
        ->and($origins['manual']['records'])->toBe(5);
});

it('filters chart data by an exact age range or age list', function (): void {
    expect(statisticsChartResponse('municipios', ['age' => '22'])['filtered_total'])->toBe(1)
        ->and(statisticsChartResponse('municipios', ['age' => '20-35'])['filtered_total'])->toBe(2)
        ->and(statisticsChartResponse('municipios', ['age' => '10,46'])['filtered_total'])->toBe(2);
});

it('accepts the supported filter combinations in every chart metric', function (): void {
    $cases = [
        ['municipios', ['death_district_ids' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id], 'sex' => 'M']],
        ['tendencias', ['municipios' => [$this->municipalityA->id], 'municipio_kind' => 'residence', 'district_ids' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id], 'sex' => 'M', 'group_by' => 'month']],
        ['edades', ['municipios' => [$this->municipalityA->id], 'death_district_ids' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id], 'sex' => 'M']],
        ['genero', ['municipios' => [$this->municipalityA->id], 'death_district_ids' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id]]],
        ['causas', ['municipios' => [$this->municipalityA->id], 'death_district_ids' => [$this->municipalityA->district_id], 'sex' => 'M']],
        ['distritoes', ['causas' => [$this->causeA->id], 'sex' => 'M']],
        ['lugares', ['municipios' => [$this->municipalityA->id], 'death_district_ids' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id], 'sex' => 'M']],
    ];

    foreach ($cases as [$type, $filters]) {
        $data = statisticsChartResponse($type, $filters);
        if (isset($data['error'])) {
            throw new RuntimeException($type.': '.json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        expect($data)->not->toHaveKey('error')
            ->and($data['filtered_total'])->toBeGreaterThan(0)
            ->and($data['period']['start_date'])->toBe('2026-01-01')
            ->and($data['period']['end_date'])->toBe('2026-01-31');
    }
});

it('uses sexo consistently in chart and comparison labels', function (): void {
    $sex = statisticsChartResponse('genero');
    $comparison = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'genero-causa',
    ]);

    expect($sex['labels'])->toBe(['Masculino', 'Femenino'])
        ->and(array_column($comparison['series'], 'name'))->toBe(['Masculino', 'Femenino']);
});

it('groups and validates districts using the selected geographic scope', function (): void {
    $deathDistrict = District::create(['name' => 'II - DEFUNCIÓN']);
    Death::query()->where('gov_folio', 'STAT-1')->update(['death_district_id' => $deathDistrict->id]);

    $deathScope = statisticsChartResponse('distritoes', ['municipio_type' => 'defuncion']);
    $residenceScope = statisticsChartResponse('distritoes', ['municipio_type' => 'residencia']);

    expect($deathScope['labels'])->toHaveCount(2)
        ->and($deathScope['labels'])->toContain('II · Defunción')
        ->and($deathScope['quality']['required_fields'])->toBe(['Distrito de defunción'])
        ->and($residenceScope['labels'])->toHaveCount(1)
        ->and($residenceScope['quality']['required_fields'])->toBe(['Distrito de residencia']);
});

it('supports an explicit all-time period without applying the default range', function (): void {
    $oldDeath = Death::query()->where('gov_folio', 'STAT-1')->firstOrFail()->replicate();
    $oldDeath->gov_folio = 'STAT-HISTORICAL';
    $oldDeath->death_date = '2020-01-01';
    $oldDeath->save();

    $data = app(StatisticsController::class)
        ->getChartData(Request::create('/', 'GET', ['all_time' => '1']), 'causas')
        ->getData(true);

    expect($data['filtered_total'])->toBe(7)
        ->and($data['period']['start_date'])->toBeNull()
        ->and($data['period']['end_date'])->toBeNull()
        ->and($data['period']['is_default'])->toBeFalse()
        ->and($data['period']['is_all_time'])->toBeTrue();
});

it('returns valid data for every comparison mode', function (): void {
    foreach (['residencia-defuncion', 'distrito-residencia-defuncion', 'genero-causa', 'edad-causa', 'lugar-causa', 'lugar-municipio'] as $comparisonType) {
        $data = statisticsChartResponse('comparativa', [
            'comparativa_type' => $comparisonType,
            'limit' => 5,
        ]);

        expect($data)->not->toHaveKey('error')
            ->and($data['filtered_total'])->toBe(6)
            ->and($data['labels'])->not->toBeEmpty()
            ->and($data['series'])->not->toBeEmpty();
    }
});

it('returns a district residence and death matrix for the comparison heatmap', function (): void {
    $deathDistrict = District::create(['name' => 'II - DEFUNCIÓN']);
    Death::query()->whereIn('gov_folio', ['STAT-1', 'STAT-2'])->update([
        'death_district_id' => $deathDistrict->id,
    ]);

    $data = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'distrito-residencia-defuncion',
    ]);

    expect($data['matrix'])->toBeTrue()
        ->and($data['x_axis_label'])->toBe('Distrito de defunción')
        ->and($data['y_axis_label'])->toBe('Distrito de residencia')
        ->and($data['filtered_total'])->toBe(6)
        ->and($data['labels'])->toContain('II · Defunción')
        ->and($data['series'])->not->toBeEmpty()
        ->and(array_sum(array_merge(...array_column($data['series'], 'data'))))->toBe(6)
        ->and($data['quality']['required_fields'])->toBe([
            'Distrito de residencia',
            'Distrito de defunción',
        ]);
});

it('returns a death location and municipality matrix for the comparison heatmap', function (): void {
    $data = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'lugar-municipio',
        'limit' => 5,
    ]);

    expect($data['matrix'])->toBeTrue()
        ->and($data['stacked'])->toBeTrue()
        ->and($data['x_axis_label'])->toBe('Municipio de defunción')
        ->and($data['y_axis_label'])->toBe('Lugar de defunción')
        ->and($data['labels'])->toBe(['Municipio B', 'Municipio a'])
        ->and(array_column($data['series'], 'name'))->toBe(['Hospital', 'Hogar'])
        ->and(array_sum(array_merge(...array_column($data['series'], 'data'))))->toBe(6)
        ->and($data['quality']['required_fields'])->toBe([
            'Lugar de defunción',
            'Municipio de defunción',
        ]);
});

it('ships the complete Tamaulipas municipality geometry for the choropleth map', function (): void {
    $geoJson = json_decode(
        file_get_contents(public_path('data/tamaulipas-municipios.geojson')),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($geoJson['type'])->toBe('FeatureCollection')
        ->and($geoJson['features'])->toHaveCount(43)
        ->and(array_column(array_column($geoJson['features'], 'properties'), 'name'))
        ->toContain('Reynosa', 'Victoria', 'Nuevo Laredo', 'Río Bravo', 'Güémez', 'Gómez Farías');
});

it('groups trends by day month and year without changing the filtered total', function (): void {
    foreach (['day', 'month', 'year'] as $groupBy) {
        $data = statisticsChartResponse('tendencias', ['group_by' => $groupBy]);

        expect($data['group_by'])->toBe($groupBy)
            ->and($data['filtered_total'])->toBe(6)
            ->and(array_sum($data['counts']))->toBe(6)
            ->and($data['labels'])->not->toBeEmpty();
    }
});

it('does not broaden results when a supplied catalog filter is invalid', function (): void {
    $data = statisticsChartResponse('causas', ['causas' => [999999]]);

    expect($data['filtered_total'])->toBe(0)
        ->and($data['total'])->toBe(0)
        ->and($data['counts'])->toBeEmpty();
});

it('excludes incomplete records from the analyzed total without creating a missing category', function (): void {
    Death::query()->where('gov_folio', 'STAT-1')->update(['death_municipality_id' => null]);

    $data = statisticsChartResponse('municipios');

    expect($data)->not->toHaveKey('previous_period_comparison');
    expect($data['matched_total'])->toBe(6)
        ->and($data['filtered_total'])->toBe(5)
        ->and($data['total'])->toBe(5)
        ->and($data['excluded_total'])->toBe(1)
        ->and($data['quality']['required_fields'])->toBe(['Municipio de defunción'])
        ->and($data['labels'])->not->toContain('Sin dato');
});

it('uses the same analyzed and excluded scopes in the table and export', function (): void {
    Death::query()->where('gov_folio', 'STAT-1')->update(['death_municipality_id' => null]);
    $filters = [
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'analysis_type' => 'municipios',
        'municipio_type' => 'defuncion',
    ];

    $analyzedTable = app(DeathController::class)
        ->dataTable(Request::create('/', 'POST', $filters))
        ->getData(true);
    $analyzedExport = (new DeathsExport($filters))->query()->count();

    $excludedFilters = array_merge($filters, ['analysis_excluded' => true]);
    $excludedTable = app(DeathController::class)
        ->dataTable(Request::create('/', 'POST', $excludedFilters))
        ->getData(true);
    $excludedExport = (new DeathsExport($excludedFilters))->query()->count();

    expect($analyzedTable['recordsFiltered'])->toBe(5)
        ->and($analyzedExport)->toBe(5)
        ->and($excludedTable['recordsFiltered'])->toBe(1)
        ->and($excludedExport)->toBe(1);
});

it('defines the required fields for every statistical analysis', function (): void {
    $service = app(StatisticsAnalysisService::class);

    expect($service->context(['analysis_type' => 'tendencias'])['required_fields'])->toBe(['Fecha de defunción'])
        ->and($service->context(['analysis_type' => 'edades'])['required_fields'])->toBe(['Edad'])
        ->and($service->context(['analysis_type' => 'genero'])['required_fields'])->toBe(['Sexo'])
        ->and($service->context(['analysis_type' => 'causas'])['required_fields'])->toBe(['Causa de defunción'])
        ->and($service->context(['analysis_type' => 'distritoes'])['required_fields'])->toBe(['Distrito de defunción'])
        ->and($service->context(['analysis_type' => 'lugares'])['required_fields'])->toBe(['Lugar de defunción'])
        ->and($service->context([
            'analysis_type' => 'distritoes',
            'municipio_type' => 'residencia',
        ])['required_fields'])->toBe(['Distrito de residencia'])
        ->and($service->context([
            'analysis_type' => 'comparativa',
            'comparativa_type' => 'lugar-causa',
        ])['required_fields'])->toBe(['Lugar de defunción', 'Causa de defunción'])
        ->and($service->context([
            'analysis_type' => 'comparativa',
            'comparativa_type' => 'lugar-municipio',
        ])['required_fields'])->toBe(['Lugar de defunción', 'Municipio de defunción'])
        ->and($service->context([
            'analysis_type' => 'comparativa',
            'comparativa_type' => 'distrito-residencia-defuncion',
        ])['required_fields'])->toBe(['Distrito de residencia', 'Distrito de defunción']);
});
