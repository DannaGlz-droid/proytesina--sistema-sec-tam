<?php

use App\Models\Death;
use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\District;
use App\Models\ImportDifference;
use App\Models\Municipality;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function importDifferenceScenario(): array
{
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $district = District::create(['name' => 'VI - MANTE']);
    $municipality = Municipality::create(['name' => 'Ocampo', 'district_id' => $district->id]);
    $location = DeathLocation::create(['name' => 'Hogar']);
    $previousCause = DeathCause::create(['name' => 'ENVENENAMIENTO RES']);
    $proposedCause = DeathCause::create(['name' => 'EXPO FUEGO Y HUMO RESIDENCIA']);

    $sourceImportId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'corte_octubre.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 1,
        'rows_failed' => 0,
        'rows_skipped_duplicates' => 0,
        'rows_changed_existing' => 0,
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'corte_mayo.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 0,
        'rows_failed' => 0,
        'rows_skipped_duplicates' => 0,
        'rows_changed_existing' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $death = Death::create([
        'import_id' => $sourceImportId,
        'gov_folio' => '240395896',
        'name' => 'Geronimo',
        'first_last_name' => 'Rodriguez',
        'second_last_name' => 'Gonzalez',
        'age' => 84,
        'age_years' => 84,
        'sex' => 'F',
        'death_date' => '2025-03-01',
        'residence_municipality_id' => $municipality->id,
        'death_municipality_id' => $municipality->id,
        'district_id' => $district->id,
        'death_district_id' => $district->id,
        'death_location_id' => $location->id,
        'death_cause_id' => $previousCause->id,
        'cie_cause_code' => 'X478',
        'cie_cause_description' => 'ENVENENAMIENTO ACCIDENTAL DE OTROS GASES Y VAPORES ESPECIFICADOS',
    ]);

    $previous = $death->only([
        'name', 'first_last_name', 'second_last_name', 'age', 'age_years', 'age_months', 'age_days',
        'sex', 'residence_municipality_id', 'death_municipality_id', 'district_id', 'death_district_id',
        'death_location_id', 'death_cause_id', 'cie_cause_code', 'cie_cause_description',
    ]);
    $previous['death_date'] = '2025-03-01';
    $proposed = array_replace($previous, [
        'death_cause_id' => $proposedCause->id,
        'cie_cause_code' => 'X080',
        'cie_cause_description' => 'EXPOSICIÓN A OTROS HUMOS, FUEGOS O LLAMAS ESPECIFICADOS, VIVIENDA',
    ]);

    $difference = ImportDifference::create([
        'import_id' => $importId,
        'death_id' => $death->id,
        'source_import_id' => $sourceImportId,
        'gov_folio' => $death->gov_folio,
        'previous_values' => $previous,
        'proposed_values' => $proposed,
        'changed_fields' => ['death_cause_id', 'cie_cause_code', 'cie_cause_description'],
        'status' => 'pending',
    ]);

    return compact('administrator', 'importId', 'death', 'difference', 'previousCause', 'proposedCause');
}

it('lists pending differences as a readable current-versus-proposed comparison', function (): void {
    $scenario = importDifferenceScenario();

    $response = $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-differences', ['importId' => $scenario['importId']]))
        ->assertOk()
        ->assertJsonPath('counts.pending', 1)
        ->assertJsonPath('counts.resolved', 0)
        ->assertJsonPath('counts.total', 1)
        ->assertJsonPath('data.data.0.gov_folio', '240395896')
        ->assertJsonPath('data.data.0.person_name', 'Geronimo Rodriguez Gonzalez');

    $fields = collect($response->json('data.data.0.changed_fields'))->keyBy('field');
    expect($fields['death_cause_id'])->toMatchArray([
        'label' => 'Causa de defunción',
        'previous' => 'ENVENENAMIENTO RES',
        'proposed' => 'EXPO FUEGO Y HUMO RESIDENCIA',
    ]);
    expect($fields['cie_cause_code']['previous'])->toBe('X478');
    expect($fields['cie_cause_code']['proposed'])->toBe('X080');
});

it('offers one record-level comparison without alternate review modes', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->get(route('statistic.import-differences-view', ['importId' => $scenario['importId']]))
        ->assertOk()
        ->assertSee('Pendientes')
        ->assertSee('Resueltas')
        ->assertSee('Conservar actual')
        ->assertSee('Usar archivo')
        ->assertDontSee('Vista enfocada')
        ->assertDontSee('Vista general')
        ->assertDontSee('Aplicar a todos');
});

it('can combine decisions for independent fields and records the mixed resolution', function (): void {
    $scenario = importDifferenceScenario();
    $difference = $scenario['difference'];
    $previous = $difference->previous_values;
    $proposed = array_replace($difference->proposed_values, ['sex' => 'M']);
    $difference->update([
        'previous_values' => $previous,
        'proposed_values' => $proposed,
        'changed_fields' => ['sex', 'death_cause_id', 'cie_cause_code', 'cie_cause_description'],
    ]);

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $difference->id]), [
            'decisions' => [
                'sex' => 'keep',
                'death_cause_id' => 'apply',
                'cie_cause_code' => 'apply',
                'cie_cause_description' => 'apply',
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.status', 'mixed');

    $death = $scenario['death']->fresh();
    expect($death->sex)->toBe('F')
        ->and($death->death_cause_id)->toBe($scenario['proposedCause']->id)
        ->and($difference->fresh()->field_decisions)->toMatchArray([
            'sex' => 'keep',
            'death_cause_id' => 'apply',
            'cie_cause_code' => 'apply',
            'cie_cause_description' => 'apply',
        ]);

    $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-differences', [
            'importId' => $scenario['importId'],
            'status' => 'resolved',
        ]))
        ->assertOk()
        ->assertJsonPath('counts.mixed', 1)
        ->assertJsonPath('counts.resolved', 1)
        ->assertJsonPath('data.data.0.status', 'mixed');
});

it('requires every changed field and keeps related fields under one decision', function (): void {
    $scenario = importDifferenceScenario();
    $route = route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]);

    $this->actingAs($scenario['administrator'])
        ->postJson($route, [
            'decisions' => ['death_cause_id' => 'apply'],
        ])
        ->assertUnprocessable()
        ->assertJsonPath('ok', false);

    $this->actingAs($scenario['administrator'])
        ->postJson($route, [
            'decisions' => [
                'death_cause_id' => 'apply',
                'cie_cause_code' => 'keep',
                'cie_cause_description' => 'apply',
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Los campos relacionados deben conservarse o reemplazarse juntos.');

    expect($scenario['difference']->fresh()->status)->toBe('pending');
});

it('shows the original identity and collapses duplicate age representations', function (): void {
    $scenario = importDifferenceScenario();
    $difference = $scenario['difference'];
    $difference->update([
        'changed_fields' => ['name', 'age', 'age_years'],
        'proposed_values' => array_replace($difference->proposed_values, [
            'name' => 'Nombre corregido',
            'age' => 85,
            'age_years' => 85,
        ]),
    ]);

    $response = $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-differences', ['importId' => $scenario['importId']]))
        ->assertOk()
        ->assertJsonPath('data.data.0.person_name', 'Geronimo Rodriguez Gonzalez')
        ->assertJsonPath('data.data.0.field_count', 2);

    $fields = collect($response->json('data.data.0.changed_fields'));
    expect($fields->pluck('field')->all())->toBe(['name', 'age'])
        ->and($fields->firstWhere('field', 'age')['raw_fields'])->toBe(['age', 'age_years']);
});

it('applies only the proposed changed fields and records who resolved them', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'apply',
        ])
        ->assertOk()
        ->assertJsonPath('ok', true);

    $scenario['death']->refresh();
    expect($scenario['death']->death_cause_id)->toBe($scenario['proposedCause']->id)
        ->and($scenario['death']->cie_cause_code)->toBe('X080')
        ->and($scenario['death']->name)->toBe('Geronimo');

    $this->assertDatabaseHas('import_differences', [
        'id' => $scenario['difference']->id,
        'status' => 'applied',
        'resolved_by_user_id' => $scenario['administrator']->id,
    ]);
});

it('applies a pending difference when some proposed fields are already current', function (): void {
    $scenario = importDifferenceScenario();
    $scenario['death']->update([
        'cie_cause_code' => $scenario['difference']->proposed_values['cie_cause_code'],
        'cie_cause_description' => $scenario['difference']->proposed_values['cie_cause_description'],
    ]);

    expect($scenario['death']->fresh()->cie_cause_code)
        ->toBe($scenario['difference']->proposed_values['cie_cause_code'])
        ->and($scenario['death']->fresh()->cie_cause_description)
        ->toBe($scenario['difference']->proposed_values['cie_cause_description']);

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'apply',
        ])
        ->assertOk()
        ->assertJsonPath('ok', true);

    $death = $scenario['death']->fresh();
    expect($death->death_cause_id)->toBe($scenario['proposedCause']->id)
        ->and($death->cie_cause_code)->toBe('X080');
    $this->assertDatabaseHas('import_differences', ['id' => $scenario['difference']->id, 'status' => 'applied']);
});

it('can keep the current record without modifying it', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'keep',
        ])
        ->assertOk();

    expect($scenario['death']->fresh()->death_cause_id)->toBe($scenario['previousCause']->id);
    $this->assertDatabaseHas('import_differences', ['id' => $scenario['difference']->id, 'status' => 'kept']);
});

it('can undo an applied record-level decision without overwriting unrelated data', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'apply',
        ])
        ->assertOk();

    expect($scenario['death']->fresh()->death_cause_id)->toBe($scenario['proposedCause']->id);

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.undo-import-difference', ['differenceId' => $scenario['difference']->id]))
        ->assertOk()
        ->assertJsonPath('data.status', 'pending');

    $death = $scenario['death']->fresh();
    $difference = $scenario['difference']->fresh();
    expect($death->death_cause_id)->toBe($scenario['previousCause']->id)
        ->and($death->cie_cause_code)->toBe('X478')
        ->and($death->name)->toBe('Geronimo')
        ->and($difference->status)->toBe('pending')
        ->and($difference->field_decisions)->toBeNull()
        ->and($difference->resolved_at)->toBeNull();
});

it('reopens a kept decision when it is undone', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'keep',
        ])
        ->assertOk();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.undo-import-difference', ['differenceId' => $scenario['difference']->id]))
        ->assertOk();

    expect($scenario['death']->fresh()->death_cause_id)->toBe($scenario['previousCause']->id)
        ->and($scenario['difference']->fresh()->status)->toBe('pending');
});

it('refuses to undo when the resolved record changed afterward', function (): void {
    $scenario = importDifferenceScenario();

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'apply',
        ])
        ->assertOk();

    $scenario['death']->update(['cie_cause_code' => 'W999']);

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.undo-import-difference', ['differenceId' => $scenario['difference']->id]))
        ->assertConflict();

    expect($scenario['death']->fresh()->cie_cause_code)->toBe('W999')
        ->and($scenario['difference']->fresh()->status)->toBe('applied');
});

it('separates pending and resolved differences for the selected import', function (): void {
    $scenario = importDifferenceScenario();
    $scenario['difference']->update([
        'status' => 'kept',
        'resolved_by_user_id' => $scenario['administrator']->id,
        'resolved_at' => now(),
    ]);

    $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-differences', [
            'importId' => $scenario['importId'],
            'status' => 'pending',
        ]))
        ->assertOk()
        ->assertJsonCount(0, 'data.data')
        ->assertJsonPath('counts.pending', 0)
        ->assertJsonPath('counts.resolved', 1);

    $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-differences', [
            'importId' => $scenario['importId'],
            'status' => 'resolved',
        ]))
        ->assertOk()
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.status', 'kept');
});

it('refuses to overwrite a record that changed after the import', function (): void {
    $scenario = importDifferenceScenario();
    $scenario['death']->update(['cie_cause_code' => 'W999']);

    $this->actingAs($scenario['administrator'])
        ->postJson(route('statistic.resolve-import-difference', ['differenceId' => $scenario['difference']->id]), [
            'decision' => 'apply',
        ])
        ->assertConflict()
        ->assertJsonPath('ok', false);

    expect($scenario['death']->fresh()->cie_cause_code)->toBe('W999');
    $this->assertDatabaseHas('import_differences', ['id' => $scenario['difference']->id, 'status' => 'pending']);
});

it('exposes pending and resolved difference counts in import history', function (): void {
    $scenario = importDifferenceScenario();

    $response = $this->actingAs($scenario['administrator'])
        ->getJson(route('statistic.import-history'))
        ->assertOk();

    $import = collect($response->json('data.data'))->firstWhere('id', $scenario['importId']);
    expect((int) $import['pending_differences'])->toBe(1)
        ->and((int) $import['resolved_differences'])->toBe(0);
});
