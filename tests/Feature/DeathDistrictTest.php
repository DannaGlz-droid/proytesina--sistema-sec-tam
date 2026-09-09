<?php

use App\Models\Death;
use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\DeathImportController;
use App\Exports\DeathsExport;
use Illuminate\Support\Facades\Schema;

it('stores residence and death districts independently', function (): void {
    $residenceDistrict = District::create(['name' => 'I - Victoria']);
    $deathDistrict = District::create(['name' => 'VI - Mante']);
    $residenceMunicipality = Municipality::create([
        'name' => 'Victoria',
        'district_id' => $residenceDistrict->id,
    ]);
    $deathMunicipality = Municipality::create([
        'name' => 'Mante',
        'district_id' => $deathDistrict->id,
    ]);
    $location = DeathLocation::create(['name' => 'Hospital']);
    $cause = DeathCause::create(['name' => 'Accidente de tránsito']);

    $death = Death::create([
        'gov_folio' => '123456789',
        'name' => 'María',
        'first_last_name' => 'López',
        'second_last_name' => 'García',
        'age' => 42,
        'age_years' => 42,
        'sex' => 'F',
        'death_date' => '2026-08-20',
        'residence_municipality_id' => $residenceMunicipality->id,
        'district_id' => $residenceDistrict->id,
        'death_municipality_id' => $deathMunicipality->id,
        'death_district_id' => $deathDistrict->id,
        'death_location_id' => $location->id,
        'death_cause_id' => $cause->id,
    ]);

    expect(Schema::hasColumn('deaths', 'death_district_id'))->toBeTrue()
        ->and($death->district->id)->toBe($residenceDistrict->id)
        ->and($death->deathDistrict->id)->toBe($deathDistrict->id)
        ->and($death->district->id)->not->toBe($death->deathDistrict->id)
        ->and((new DeathsExport())->map($death->loadMissing(['district', 'deathDistrict']))[7])->toBe($residenceDistrict->display_name)
        ->and((new DeathsExport())->map($death)[9])->toBe($deathDistrict->display_name);
});

it('recognizes common jurisdiction labels from the death spreadsheet', function (): void {
    $district = District::create(['name' => 'VI - Mante']);
    $controller = app(DeathImportController::class);

    $buildLookup = new ReflectionMethod($controller, 'buildImportDistrictLookup');
    $resolve = new ReflectionMethod($controller, 'resolveImportDistrict');
    $lookup = $buildLookup->invoke($controller);

    expect($resolve->invoke($controller, 'Jurisdicción sanitaria VI Mante', $lookup)?->id)->toBe($district->id)
        ->and($resolve->invoke($controller, '06 Mante', $lookup)?->id)->toBe($district->id)
        ->and($resolve->invoke($controller, 'VI - Mante', $lookup)?->id)->toBe($district->id);
});

it('preserves zero when updating a composite death age', function (): void {
    $district = District::create(['name' => 'I - VICTORIA']);
    $municipality = Municipality::create([
        'name' => 'Victoria',
        'district_id' => $district->id,
    ]);
    $location = DeathLocation::create(['name' => 'HOGAR']);
    $cause = DeathCause::create(['name' => DeathCause::ALLOWED_NAMES[0]]);
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
        'district_id' => $district->id,
    ]);

    $death = Death::create([
        'gov_folio' => '123456789',
        'name' => 'María',
        'first_last_name' => 'López',
        'second_last_name' => null,
        'age' => 42,
        'age_years' => 42,
        'sex' => 'F',
        'death_date' => now()->subDay()->toDateString(),
        'residence_municipality_id' => $municipality->id,
        'district_id' => $district->id,
        'death_municipality_id' => $municipality->id,
        'death_district_id' => $district->id,
        'death_location_id' => $location->id,
        'death_cause_id' => $cause->id,
    ]);

    $response = $this->actingAs($administrator)->put(route('statistic.update', $death), [
        'gov_folio' => '123456789',
        'name' => 'María',
        'first_last_name' => 'López',
        'second_last_name' => '',
        'edad_valor' => 0,
        'edad_unidad' => 'meses',
        'sex' => 'F',
        'residence_municipality_id' => $municipality->id,
        'death_municipality_id' => $municipality->id,
        'district_id' => $district->id,
        'death_location_id' => $location->id,
        'death_cause_id' => $cause->id,
        'death_date' => now()->subDay()->toDateString(),
    ]);

    $response->assertRedirect(route('statistic.data'));

    $death->refresh();
    expect($death->age)->toBe(0)
        ->and($death->age_years)->toBe(0)
        ->and($death->age_months)->toBe(0)
        ->and($death->age_days)->toBeNull();
});
