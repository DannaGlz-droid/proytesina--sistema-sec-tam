<?php

use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\Death;
use App\Models\District;
use App\Models\FailedImportRecord;
use App\Models\Municipality;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

it('renders failed import catalogs with presentation labels', function (): void {
    $district = District::create(['name' => 'I - VICTORIA']);
    $municipality = Municipality::create([
        'name' => 'VILLAGRAN',
        'district_id' => $district->id,
    ]);
    $location = DeathLocation::create(['name' => 'HOGAR']);
    $cause = DeathCause::create(['name' => 'OTROS ACCIDENTES']);
    $user = User::factory()->create();

    $this->actingAs($user);

    $html = view('estadisticas.failed-imports', [
        'importId' => 189,
        'importFileName' => 'Prueba.xlsx',
        'municipalities' => new EloquentCollection([$municipality]),
        'districts' => new EloquentCollection([$district]),
        'jurisdictions' => new EloquentCollection([$district]),
        'locations' => new EloquentCollection([$location]),
        'causes' => new EloquentCollection([$cause]),
    ])->render();

    expect($html)
        ->toContain('const municipalitiesData =')
        ->toContain('const locationsData =')
        ->toContain('const causesData =')
        ->toContain('display_name')
        ->toContain('function removeRecordCardAndUpdateList(card)')
        ->toContain('function fillFailedRecordPageGap(recordsList)')
        ->toContain('renderRecords(missingRecords, { append: true })')
        ->toContain("recordsList?.querySelectorAll('.record-card').length")
        ->toContain('failedImportsPaginationState.total - 1')
        ->not->toContain('function removeRecordCardAndRefresh(card)');
});

it('presents failed import person names like the deaths table without changing the source payload', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'Nombres.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 0,
        'rows_failed' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $record = FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'nombre' => 'MARCOS DE LA CRUZ',
            'primerapellido' => 'TORRES',
            'segundoapellido' => 'DÍAZ',
        ],
        'error_message' => 'Causa no reconocida: "Causa1". Use una de las causas permitidas.',
        'error_details' => null,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($administrator)
        ->getJson(route('statistic.failed-records', ['importId' => $importId]));

    $response->assertOk()
        ->assertJsonPath('data.data.0.original_row_data.nombre', 'Marcos de la Cruz')
        ->assertJsonPath('data.data.0.original_row_data.primerapellido', 'Torres')
        ->assertJsonPath('data.data.0.original_row_data.segundoapellido', 'Díaz')
        ->assertJsonFragment(['Causa no reconocida: "Causa1". Use una de las causas permitidas.'])
        ->assertJsonFragment(['Edad: este campo es obligatorio.']);

    expect($record->fresh()->original_row_data)->toMatchArray([
        'nombre' => 'MARCOS DE LA CRUZ',
        'primerapellido' => 'TORRES',
        'segundoapellido' => 'DÍAZ',
    ]);
});

it('searches failed records using terms from separate person fields', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'Busqueda.xlsx',
        'status' => 'completed',
        'rows_total' => 2,
        'rows_imported' => 0,
        'rows_failed' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $matchingRecord = FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'folio' => '333333334',
            'nombre' => 'LUCIA',
            'primerapellido' => 'ORTIZ',
            'segundoapellido' => 'NAVARRO',
        ],
        'error_message' => 'Causa pendiente de correccion.',
        'status' => 'pending',
    ]);
    FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'folio' => '333333335',
            'nombre' => 'LUCIA',
            'primerapellido' => 'PEREZ',
            'segundoapellido' => 'NAVARRO',
        ],
        'error_message' => 'Causa pendiente de correccion.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($administrator)->getJson(
        route('statistic.failed-records', ['importId' => $importId])
        . '?' . http_build_query(['search' => 'lucia ortiz'])
    );

    $response->assertOk()
        ->assertJsonPath('data.total', 1)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $matchingRecord->id)
        ->assertJsonPath('data.data.0.original_row_data.nombre', 'Lucia')
        ->assertJsonPath('data.data.0.original_row_data.primerapellido', 'Ortiz');
});

it('reports an existing folio before a pending failed record is saved again', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $district = District::create(['name' => 'II - TAMPICO']);
    $municipality = Municipality::create([
        'name' => 'TAMPICO',
        'district_id' => $district->id,
    ]);
    $location = DeathLocation::create(['name' => 'HOGAR']);
    $cause = DeathCause::create(['name' => 'OTROS ACCIDENTES']);

    Death::create([
        'gov_folio' => '333333342',
        'name' => 'Lucio',
        'first_last_name' => 'Fuentes',
        'second_last_name' => 'Navarro',
        'age' => 0,
        'age_days' => 29,
        'sex' => 'F',
        'death_date' => '2020-05-02',
        'residence_municipality_id' => $municipality->id,
        'district_id' => $district->id,
        'death_municipality_id' => $municipality->id,
        'death_district_id' => $district->id,
        'death_location_id' => $location->id,
        'death_cause_id' => $cause->id,
    ]);

    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'Duplicados.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 0,
        'rows_failed' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $record = FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'folio' => '333333342',
            'nombre' => 'LUCIO',
            'primerapellido' => 'FUENTES',
            'segundoapellido' => 'NAVARRO',
            'sexod' => 'F',
            'edad_valor' => '29',
            'edad_unidad' => 'dias',
            'municipioresidenciad' => 'TAMPICO',
            'municipiodefunciond' => 'TAMPICO',
            'sitiodefunciond' => 'HOGAR',
            'sheet' => 'Causa1',
            'fechadefuncion' => '2020-05-02',
        ],
        'error_message' => 'Causa pendiente de correccion.',
        'error_details' => ['Causa pendiente de correccion.'],
        'status' => 'pending',
    ]);

    $this->actingAs($administrator)
        ->getJson(route('statistic.failed-records', ['importId' => $importId]))
        ->assertOk()
        ->assertJsonPath('data.data.0.id', $record->id)
        ->assertJsonFragment([
            'Folio: ya existe en el sistema. Ingrese uno diferente.',
        ])
        ->assertJsonFragment(['Causa pendiente de correccion.']);
});

it('rejects correction locations that are outside the registration catalogue', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $district = District::create(['name' => 'II - TAMPICO']);
    Municipality::create(['name' => 'TAMPICO', 'district_id' => $district->id]);
    DeathLocation::create(['name' => 'HOGAR']);
    DeathCause::create(['name' => 'OTROS ACCIDENTES']);

    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'Lugares.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 0,
        'rows_failed' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $record = FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'folio' => '123456789',
            'nombre' => 'Lucia',
            'primerapellido' => 'Ortiz',
            'segundoapellido' => 'Navarro',
            'sexod' => 'F',
            'edad_valor' => '34',
            'edad_unidad' => 'anos',
            'municipioresidenciad' => 'TAMPICO',
            'municipiodefunciond' => 'TAMPICO',
            'sitiodefunciond' => 'LUGAR FUERA DEL CATALOGO',
            'sheet' => 'OTROS ACCIDENTES',
            'fechadefuncion' => now()->format('Y-m-d'),
        ],
        'error_message' => 'Lugar pendiente de corrección.',
        'error_details' => ['Lugar pendiente de corrección.'],
        'status' => 'pending',
    ]);

    $response = $this->actingAs($administrator)->postJson(
        route('statistic.save-correction', ['recordId' => $record->id]),
        ['corrected_data' => ['sitiodefunciond' => 'LUGAR FUERA DEL CATALOGO']]
    );

    $response->assertUnprocessable()
        ->assertJsonPath('ok', false)
        ->assertJsonFragment([
            'Lugar de defunción: seleccione una opción permitida.',
        ]);

    expect(DeathLocation::where('name', 'LUGAR FUERA DEL CATALOGO')->exists())->toBeFalse();

    $this->actingAs($administrator)->postJson(
        route('statistic.save-correction', ['recordId' => $record->id]),
        ['corrected_data' => ['sitiodefunciond' => 'hogar']]
    )->assertOk()->assertJsonPath('data.status', 'corrected');

    expect(DeathLocation::count())->toBe(1);
});

it('preserves a saved correction and imports it through the failed-record workflow', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $residenceDistrict = District::create(['name' => 'XII - ALTAMIRA']);
    $deathDistrict = District::create(['name' => 'II - TAMPICO']);
    $residenceMunicipality = Municipality::create([
        'name' => 'ALTAMIRA',
        'district_id' => $residenceDistrict->id,
    ]);
    $deathMunicipality = Municipality::create([
        'name' => 'TAMPICO',
        'district_id' => $deathDistrict->id,
    ]);
    $location = DeathLocation::create(['name' => 'HOGAR']);
    $cause = DeathCause::create(['name' => 'OTROS ACCIDENTES']);

    $importId = DB::table('imports')->insertGetId([
        'user_id' => $administrator->id,
        'original_name' => 'Correcciones.xlsx',
        'status' => 'completed',
        'rows_total' => 1,
        'rows_imported' => 0,
        'rows_failed' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $record = FailedImportRecord::create([
        'import_id' => $importId,
        'original_row_data' => [
            'folio' => '333333337',
            'nombre' => 'LUCIA',
            'primerapellido' => 'ORTIZ',
            'segundoapellido' => 'NAVARRO',
            'sexod' => 'F',
            'edad_valor' => '32',
            'edad_unidad' => 'dias',
            'municipioresidenciad' => 'ALTAMIRA',
            'municipiodefunciond' => 'TAMPICO',
            'sitiodefunciond' => 'HOGAR',
            'sheet' => 'OTROS ACCIDENTES',
            'fechadefuncion' => '2020-05-02',
        ],
        'error_message' => 'Edad: para días, ingrese un valor entre 0 y 30.',
        'error_details' => ['Edad: para días, ingrese un valor entre 0 y 30.'],
        'status' => 'pending',
    ]);
    $correctedData = [
        'folio' => '333333337',
        'nombre' => 'Lucía María',
        'primerapellido' => 'Ortiz',
        'segundoapellido' => 'Navarro',
        'sexod' => 'F',
        'edad_valor' => '11',
        'edad_unidad' => 'meses',
        'municipioresidenciad' => 'ALTAMIRA',
        'municipiodefunciond' => 'TAMPICO',
        'sitiodefunciond' => 'HOGAR',
        'sheet' => 'OTROS ACCIDENTES',
        'fechadefuncion' => '2020-04-02',
    ];

    $this->actingAs($administrator)
        ->postJson(route('statistic.save-correction', ['recordId' => $record->id]), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('corrected_data');

    $this->actingAs($administrator)
        ->postJson(
            route('statistic.save-correction', ['recordId' => $record->id]),
            ['corrected_data' => $correctedData]
        )
        ->assertOk()
        ->assertJsonPath('data.status', 'corrected')
        ->assertJsonPath('data.corrected_data.nombre', 'Lucía María')
        ->assertJsonPath('data.corrected_data.edad_valor', '11')
        ->assertJsonPath('data.corrected_data.edad_unidad', 'meses')
        ->assertJsonPath('data.corrected_data.fechadefuncion', '2020-04-02');

    $record->refresh();
    expect($record->status)->toBe('corrected')
        ->and($record->corrected_data)->toMatchArray($correctedData);

    $this->actingAs($administrator)
        ->getJson(route('statistic.failed-records', ['importId' => $importId]))
        ->assertOk()
        ->assertJsonPath('data.data.0.corrected_data.nombre', 'Lucía María')
        ->assertJsonPath('data.data.0.corrected_data.edad_valor', '11')
        ->assertJsonPath('data.data.0.corrected_data.edad_unidad', 'meses')
        ->assertJsonPath('data.data.0.corrected_data.fechadefuncion', '2020-04-02');

    $this->actingAs($administrator)
        ->postJson(
            route('statistic.retry-failed', ['recordId' => $record->id]),
            ['corrected_data' => 'invalid']
        )
        ->assertUnprocessable()
        ->assertJsonValidationErrors('corrected_data');

    $this->actingAs($administrator)
        ->postJson(
            route('statistic.retry-failed', ['recordId' => $record->id]),
            ['corrected_data' => $correctedData]
        )
        ->assertOk()
        ->assertJsonPath('ok', true);

    expect($record->fresh()->status)->toBe('imported');
    $death = Death::where('gov_folio', '333333337')->firstOrFail();
    expect($death->name)->toBe('Lucía María')
        ->and($death->first_last_name)->toBe('Ortiz')
        ->and($death->second_last_name)->toBe('Navarro')
        ->and($death->age)->toBe(0)
        ->and($death->age_years)->toBe(0)
        ->and($death->age_months)->toBe(11)
        ->and($death->age_days)->toBeNull()
        ->and($death->sex)->toBe('F')
        ->and($death->death_date->format('Y-m-d'))->toBe('2020-04-02')
        ->and($death->residence_municipality_id)->toBe($residenceMunicipality->id)
        ->and($death->death_municipality_id)->toBe($deathMunicipality->id)
        ->and($death->death_location_id)->toBe($location->id)
        ->and($death->death_cause_id)->toBe($cause->id);

    $this->assertDatabaseHas('imports', [
        'id' => $importId,
        'rows_imported' => 1,
        'rows_failed' => 0,
    ]);
    $this->actingAs($administrator)
        ->getJson(route('statistic.failed-records', ['importId' => $importId]))
        ->assertOk()
        ->assertJsonPath('data.total', 0)
        ->assertJsonCount(0, 'data.data');
});
