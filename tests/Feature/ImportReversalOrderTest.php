<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createCompletedImportForReversal(User $user, string $name, string $createdAt): int
{
    return DB::table('imports')->insertGetId([
        'user_id' => $user->id,
        'original_name' => $name,
        'status' => 'completed',
        'rows_total' => 10,
        'rows_imported' => 0,
        'rows_failed' => 0,
        'rows_skipped_duplicates' => 10,
        'rows_changed_existing' => 0,
        'is_reversed' => false,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}

it('blocks reversing an older import while a later completed import remains active', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $olderImportId = createCompletedImportForReversal($administrator, 'corte_1_al_15.xlsx', '2026-11-01 10:00:00');
    $newerImportId = createCompletedImportForReversal($administrator, 'corte_1_al_30.xlsx', '2026-11-16 10:00:00');

    $response = $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $olderImportId]));

    $response->assertConflict()
        ->assertJsonPath('ok', false)
        ->assertJsonPath('code', 'newer_active_import_exists')
        ->assertJsonPath('blocking_import.id', $newerImportId)
        ->assertJsonPath('blocking_import.original_name', 'corte_1_al_30.xlsx');

    $this->assertDatabaseHas('imports', [
        'id' => $olderImportId,
        'is_reversed' => false,
    ]);
});

it('allows reversing imports from newest to oldest', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $olderImportId = createCompletedImportForReversal($administrator, 'corte_1_al_15.xlsx', '2026-11-01 10:00:00');
    $newerImportId = createCompletedImportForReversal($administrator, 'corte_1_al_30.xlsx', '2026-11-16 10:00:00');

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $newerImportId]))
        ->assertOk()
        ->assertJsonPath('ok', true);

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $olderImportId]))
        ->assertOk()
        ->assertJsonPath('ok', true);

    $this->assertDatabaseHas('imports', ['id' => $newerImportId, 'is_reversed' => true]);
    $this->assertDatabaseHas('imports', ['id' => $olderImportId, 'is_reversed' => true]);
});

it('uses the id as a stable tie breaker when imports share a timestamp', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $olderImportId = createCompletedImportForReversal($administrator, 'primera.xlsx', '2026-11-16 10:00:00');
    $newerImportId = createCompletedImportForReversal($administrator, 'segunda.xlsx', '2026-11-16 10:00:00');

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $olderImportId]))
        ->assertConflict()
        ->assertJsonPath('blocking_import.id', $newerImportId);
});

it('lists actual importers regardless of their role', function (): void {
    $administratorRole = Role::create(['name' => 'Administrador']);
    $leaderRole = Role::create(['name' => 'Líder estatal']);
    $administrator = User::factory()->create(['role_id' => $administratorRole->id]);
    $leader = User::factory()->create([
        'role_id' => $leaderRole->id,
        'name' => 'Carlos Enrique',
        'first_last_name' => 'Rodríguez',
        'second_last_name' => 'Nava',
        'username' => 'lider_importador',
    ]);
    User::factory()->create(['role_id' => $leaderRole->id]);
    createCompletedImportForReversal($leader, 'carga_lider.xlsx', '2026-11-16 10:00:00');

    $response = $this->actingAs($administrator)
        ->getJson(route('api.users.search', ['importers_only' => 1]));

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $leader->id)
        ->assertJsonPath('0.username', 'lider_importador')
        ->assertJsonPath('0.display_name', 'Carlos Enrique Rodríguez Nava (@lider_importador)');
});

it('also blocks an older reversal while a later import is still processing', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $olderImportId = createCompletedImportForReversal($administrator, 'corte_anterior.xlsx', '2026-11-01 10:00:00');
    $processingImportId = createCompletedImportForReversal($administrator, 'corte_en_proceso.xlsx', '2026-11-16 10:00:00');
    DB::table('imports')->where('id', $processingImportId)->update(['status' => 'processing']);

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $olderImportId]))
        ->assertConflict()
        ->assertJsonPath('blocking_import.id', $processingImportId);
});

it('allows reversing an older traced import when a newer traced import is unrelated', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $olderImportId = createCompletedImportForReversal($administrator, 'accidentes_enero.xlsx', '2026-11-01 10:00:00');
    $newerImportId = createCompletedImportForReversal($administrator, 'mortalidad_febrero.xlsx', '2026-11-16 10:00:00');

    DB::table('imports')->whereIn('id', [$olderImportId, $newerImportId])->update([
        'record_trace_complete' => true,
    ]);

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $olderImportId]))
        ->assertOk()
        ->assertJsonPath('ok', true);

    $this->assertDatabaseHas('imports', ['id' => $olderImportId, 'is_reversed' => true]);
});

it('blocks a traced source import when a later active import includes its records', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $sourceImportId = createCompletedImportForReversal($administrator, 'corte_1.xlsx', '2026-11-01 10:00:00');
    $dependentImportId = createCompletedImportForReversal($administrator, 'corte_2.xlsx', '2026-11-16 10:00:00');

    DB::table('imports')->whereIn('id', [$sourceImportId, $dependentImportId])->update([
        'record_trace_complete' => true,
    ]);
    DB::table('import_death_records')->insert([
        'import_id' => $dependentImportId,
        'death_id' => null,
        'source_import_id' => $sourceImportId,
        'gov_folio' => 'FOLIO-001',
        'result' => 'unchanged',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($administrator)
        ->postJson(route('statistic.reverse-import', ['importId' => $sourceImportId]))
        ->assertConflict()
        ->assertJsonPath('code', 'dependent_active_import_exists')
        ->assertJsonPath('blocking_import.id', $dependentImportId);
});

it('exposes verified cumulative relations in import history', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);
    $sourceImportId = createCompletedImportForReversal($administrator, 'corte_1.xlsx', '2026-11-01 10:00:00');
    $dependentImportId = createCompletedImportForReversal($administrator, 'corte_2.xlsx', '2026-11-16 10:00:00');

    DB::table('imports')->whereIn('id', [$sourceImportId, $dependentImportId])->update([
        'record_trace_complete' => true,
    ]);
    DB::table('import_death_records')->insert([
        'import_id' => $dependentImportId,
        'death_id' => null,
        'source_import_id' => $sourceImportId,
        'gov_folio' => 'FOLIO-001',
        'result' => 'different',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($administrator)
        ->getJson(route('statistic.import-history'))
        ->assertOk();

    $imports = collect($response->json('data.data'))->keyBy('id');

    expect($imports[$dependentImportId]['included_from'][0])
        ->toMatchArray([
            'id' => $sourceImportId,
            'original_name' => 'corte_1.xlsx',
            'records_count' => 1,
        ]);
    expect($imports[$sourceImportId]['included_by'][0])
        ->toMatchArray([
            'id' => $dependentImportId,
            'original_name' => 'corte_2.xlsx',
            'records_count' => 1,
        ]);
    expect($imports[$sourceImportId]['reversal_blocked_by']['id'])->toBe($dependentImportId);
});

it('exposes safe failure reasons without leaking internal import errors', function (): void {
    $administrator = User::factory()->create([
        'role_id' => Role::create(['name' => 'Administrador'])->id,
    ]);

    $cases = [
        'columnas.xlsx' => [
            'El archivo no contiene la estructura requerida para importar defunciones. Faltan columnas obligatorias: folio.',
            'Faltan columnas requeridas',
        ],
        'danado.xlsx' => [
            'No se pudo leer el archivo de importación: Zip archive is inconsistent.',
            'Archivo dañado o no compatible',
        ],
        'inaccesible.xlsx' => [
            'Uploaded file was not found on disk. Checked tmp: C:\\private\\upload.tmp.',
            'No se pudo acceder al archivo',
        ],
        'interno.xlsx' => [
            'SQLSTATE[HY000]: General error with private database details.',
            'Error interno al procesar',
        ],
        'sin_motivo.xlsx' => [
            null,
            'Motivo no disponible',
        ],
    ];

    foreach ($cases as $name => [$internalMessage]) {
        DB::table('imports')->insert([
            'user_id' => $administrator->id,
            'original_name' => $name,
            'status' => 'failed',
            'rows_total' => 0,
            'rows_imported' => 0,
            'rows_failed' => 0,
            'error_message' => $internalMessage,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $response = $this->actingAs($administrator)
        ->getJson(route('statistic.import-history'))
        ->assertOk();

    $imports = collect($response->json('data.data'));
    $reasons = $imports->pluck('failure_reason', 'original_name');

    foreach ($cases as $name => [, $expectedReason]) {
        expect($reasons[$name])->toBe($expectedReason);
    }

    expect($imports->every(fn (array $import): bool => !array_key_exists('error_message', $import)))->toBeTrue();
    expect(json_encode($response->json()))->not->toContain('C:\\private', 'SQLSTATE');
});
