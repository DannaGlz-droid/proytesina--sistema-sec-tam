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
        ->and($data['coverage_percentage'])->toBe(83.3)
        ->and($data['period']['start_date'])->toBe('2026-01-01')
        ->and($data['period']['end_date'])->toBe('2026-01-31');
});

it('uses consistent presentation labels without changing catalog values', function (): void {
    $this->municipalityA->update(['name' => 'OTRO']);
    $this->causeA->update(['name' => 'OTROS ACCIDENTES']);

    $municipalities = statisticsChartResponse('municipios');
    $causes = statisticsChartResponse('causas');
    $districts = statisticsChartResponse('jurisdicciones');

    expect($municipalities['labels'])->toContain('Otro')
        ->and($causes['labels'])->toContain('Otros accidentes')
        ->and($districts['labels'])->toContain('I · Prueba')
        ->and($this->municipalityA->fresh()->name)->toBe('OTRO')
        ->and($this->causeA->fresh()->name)->toBe('OTROS ACCIDENTES');
});

it('compares the analyzed total with an immediately preceding period of equal length', function (): void {
    foreach (range(1, 3) as $index) {
        $previousDeath = Death::query()->where('gov_folio', 'STAT-'.$index)->firstOrFail()->replicate();
        $previousDeath->gov_folio = 'PREVIOUS-'.$index;
        $previousDeath->death_date = '2025-12-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
        $previousDeath->save();
    }

    $data = statisticsChartResponse('causas');
    $comparison = $data['previous_period_comparison'];

    expect($comparison['available'])->toBeTrue()
        ->and($comparison['current_total'])->toBe(6)
        ->and($comparison['previous_total'])->toBe(3)
        ->and($comparison['difference'])->toBe(3)
        ->and($comparison['percentage_change'])->toBe(100)
        ->and($comparison['direction'])->toBe('increase')
        ->and($comparison['period']['start_date'])->toBe('2025-12-01')
        ->and($comparison['period']['end_date'])->toBe('2025-12-31');
});

it('does not invent a percentage when the previous period has no records', function (): void {
    $comparison = statisticsChartResponse('causas')['previous_period_comparison'];

    expect($comparison['available'])->toBeTrue()
        ->and($comparison['previous_total'])->toBe(0)
        ->and($comparison['percentage_change'])->toBeNull()
        ->and($comparison['direction'])->toBe('no_baseline');
});

it('reports decreases and unchanged totals against the previous period', function (
    int $previousRecords,
    int $expectedDifference,
    int $expectedPercentage,
    string $expectedDirection,
): void {
    foreach (range(1, $previousRecords) as $index) {
        $source = (($index - 1) % 6) + 1;
        $previousDeath = Death::query()->where('gov_folio', 'STAT-'.$source)->firstOrFail()->replicate();
        $previousDeath->gov_folio = 'DIRECTION-PREVIOUS-'.$index;
        $previousDeath->death_date = '2025-12-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
        $previousDeath->save();
    }

    $comparison = statisticsChartResponse('causas')['previous_period_comparison'];

    expect($comparison['previous_total'])->toBe($previousRecords)
        ->and($comparison['difference'])->toBe($expectedDifference)
        ->and($comparison['percentage_change'])->toBe($expectedPercentage)
        ->and($comparison['direction'])->toBe($expectedDirection);
})->with([
    'decrease' => [12, -6, -50, 'decrease'],
    'unchanged' => [6, 0, 0, 'unchanged'],
]);

it('shifts year and month selections while retaining the other filters', function (): void {
    foreach (range(1, 2) as $index) {
        $previousDeath = Death::query()->where('gov_folio', 'STAT-'.$index)->firstOrFail()->replicate();
        $previousDeath->gov_folio = 'YEAR-PREVIOUS-'.$index;
        $previousDeath->death_date = '2025-01-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
        $previousDeath->save();
    }

    $request = Request::create('/', 'GET', [
        'years' => ['2026'],
        'months' => ['1'],
        'causas' => [$this->causeA->id],
    ]);
    $data = app(StatisticsController::class)->getChartData($request, 'causas')->getData(true);
    $comparison = $data['previous_period_comparison'];

    expect($data['filtered_total'])->toBe(3)
        ->and($comparison['previous_total'])->toBe(2)
        ->and($comparison['difference'])->toBe(1)
        ->and($comparison['percentage_change'])->toBe(50)
        ->and($comparison['period']['years'])->toBe([2025])
        ->and($comparison['period']['months'])->toBe([1]);
});

it('does not double the analyzed total in residence and death comparison', function (): void {
    $data = statisticsChartResponse('comparativa', [
        'comparativa_type' => 'residencia-defuncion',
        'limit' => 5,
    ]);

    expect($data['total'])->toBe(6)
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
        ->and($ageData)->not->toHaveKey('residence_counts')
        ->and($locationData['series'])->not->toBeEmpty()
        ->and($locationData['stacked'])->toBeTrue()
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

it('filters chart data by an exact age range or age list', function (): void {
    expect(statisticsChartResponse('municipios', ['age' => '22'])['filtered_total'])->toBe(1)
        ->and(statisticsChartResponse('municipios', ['age' => '20-35'])['filtered_total'])->toBe(2)
        ->and(statisticsChartResponse('municipios', ['age' => '10,46'])['filtered_total'])->toBe(2);
});

it('accepts the supported filter combinations in every chart metric', function (): void {
    $cases = [
        ['municipios', ['distritoes' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id], 'sex' => 'M']],
        ['tendencias', ['municipios' => [$this->municipalityA->id], 'causas' => [$this->causeA->id], 'sex' => 'M', 'group_by' => 'month']],
        ['edades', ['municipios' => [$this->municipalityA->id], 'distritoes' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id]]],
        ['genero', ['municipios' => [$this->municipalityA->id], 'distritoes' => [$this->municipalityA->district_id], 'causas' => [$this->causeA->id]]],
        ['causas', ['municipios' => [$this->municipalityA->id], 'distritoes' => [$this->municipalityA->district_id], 'sex' => 'M']],
        ['distritoes', ['causas' => [$this->causeA->id], 'sex' => 'M']],
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

it('returns valid data for every comparison mode', function (): void {
    foreach (['residencia-defuncion', 'genero-causa', 'edad-causa', 'lugar-causa'] as $comparisonType) {
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

    foreach ([1, 2] as $index) {
        $previousDeath = Death::query()->where('gov_folio', 'STAT-'.$index)->firstOrFail()->replicate();
        $previousDeath->gov_folio = 'QUALITY-PREVIOUS-'.$index;
        $previousDeath->death_date = '2025-12-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
        $previousDeath->save();
    }

    $data = statisticsChartResponse('municipios');

    expect($data['matched_total'])->toBe(6)
        ->and($data['filtered_total'])->toBe(5)
        ->and($data['total'])->toBe(5)
        ->and($data['excluded_total'])->toBe(1)
        ->and($data['quality']['required_fields'])->toBe(['Municipio de defunción'])
        ->and($data['previous_period_comparison']['previous_total'])->toBe(1)
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
        ->and($service->context(['analysis_type' => 'distritoes'])['required_fields'])->toBe(['Distrito de residencia'])
        ->and($service->context([
            'analysis_type' => 'comparativa',
            'comparativa_type' => 'lugar-causa',
        ])['required_fields'])->toBe(['Lugar de defunción', 'Causa de defunción']);
});
