<?php

namespace Database\Seeders;

use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\District;
use App\Models\ImportDifference;
use App\Models\Municipality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportDifferencesPreviewSeeder extends Seeder
{
    private const FILE_NAME = 'VISTA PREVIA - diferencias simples y múltiples.xlsx';

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->removePreviousPreview();

            $user = User::query()->firstOrFail();
            $districts = District::query()->limit(2)->get();
            $municipalities = Municipality::query()->limit(2)->get();
            $locations = DeathLocation::query()->limit(2)->get();
            $causes = DeathCause::query()->limit(2)->get();

            if ($districts->count() < 2 || $municipalities->count() < 2 || $locations->count() < 2 || $causes->count() < 2) {
                throw new \RuntimeException('La vista previa necesita al menos dos registros en cada catálogo.');
            }

            $importId = DB::table('imports')->insertGetId([
                'user_id' => $user->id,
                'original_name' => self::FILE_NAME,
                'status' => 'preview',
                'rows_total' => 6,
                'rows_imported' => 0,
                'rows_failed' => 0,
                'rows_skipped_duplicates' => 0,
                'rows_changed_existing' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $base = [
                'import_id' => $importId,
                'age' => 46,
                'age_years' => 46,
                'age_months' => null,
                'age_days' => null,
                'sex' => 'F',
                'death_date' => '2026-03-12',
                'residence_municipality_id' => $municipalities[0]->id,
                'death_municipality_id' => $municipalities[0]->id,
                'district_id' => $districts[0]->id,
                'death_district_id' => $districts[0]->id,
                'death_location_id' => $locations[0]->id,
                'death_cause_id' => $causes[0]->id,
                'cie_cause_code' => 'X478',
                'cie_cause_description' => 'ENVENENAMIENTO ACCIDENTAL POR OTROS GASES Y VAPORES ESPECIFICADOS',
            ];

            $scenarios = [
                [
                    'person' => ['name' => 'Caso', 'first_last_name' => 'Una Diferencia', 'second_last_name' => null, 'gov_folio' => '99D00001E00000001'],
                    'changes' => ['death_cause_id' => $causes[1]->id],
                    'fields' => ['death_cause_id'],
                    'status' => 'pending',
                ],
                [
                    'person' => ['name' => 'Caso', 'first_last_name' => 'Datos Relacionados', 'second_last_name' => null, 'gov_folio' => '99D00002E00000002'],
                    'changes' => [
                        'death_cause_id' => $causes[1]->id,
                        'cie_cause_code' => 'X080',
                        'cie_cause_description' => 'EXPOSICIÓN A OTROS HUMOS, FUEGOS O LLAMAS ESPECIFICADOS, VIVIENDA',
                    ],
                    'fields' => ['death_cause_id', 'cie_cause_code', 'cie_cause_description'],
                    'status' => 'pending',
                ],
                [
                    'person' => ['name' => 'Caso', 'first_last_name' => 'Datos Independientes', 'second_last_name' => null, 'gov_folio' => '99D00003E00000003'],
                    'changes' => [
                        'sex' => 'M',
                        'death_date' => '2026-03-18',
                        'residence_municipality_id' => $municipalities[1]->id,
                        'death_location_id' => $locations[1]->id,
                    ],
                    'fields' => ['sex', 'death_date', 'residence_municipality_id', 'death_location_id'],
                    'status' => 'pending',
                ],
                [
                    'person' => ['name' => 'María Fernanda del Carmen', 'first_last_name' => 'Apellido Extraordinariamente Extenso', 'second_last_name' => 'De la Demostración', 'gov_folio' => '99D00004E00000004'],
                    'changes' => [
                        'name' => 'María Fernanda Alejandra del Carmen',
                        'first_last_name' => 'Apellido Corregido Extraordinariamente Extenso',
                        'age' => 47,
                        'age_years' => 47,
                        'death_date' => '2026-04-01',
                        'death_cause_id' => $causes[1]->id,
                        'cie_cause_description' => 'DESCRIPCIÓN CIE EXTENSA PARA COMPROBAR EL AJUSTE DE LÍNEAS, LA ALTURA DE LA TARJETA Y LA LEGIBILIDAD DE LA COMPARACIÓN',
                    ],
                    'fields' => ['name', 'first_last_name', 'age', 'age_years', 'death_date', 'death_cause_id', 'cie_cause_description'],
                    'status' => 'pending',
                ],
                [
                    'person' => ['name' => 'Caso', 'first_last_name' => 'Resuelto Reciente', 'second_last_name' => null, 'gov_folio' => '99D00005E00000005'],
                    'changes' => ['death_cause_id' => $causes[1]->id, 'cie_cause_code' => 'X080'],
                    'fields' => ['death_cause_id', 'cie_cause_code'],
                    'status' => 'applied',
                ],
                [
                    'person' => ['name' => 'Caso', 'first_last_name' => 'Resuelto Conservado', 'second_last_name' => null, 'gov_folio' => '99D00006E00000006'],
                    'changes' => ['death_date' => '2026-03-20', 'death_location_id' => $locations[1]->id],
                    'fields' => ['death_date', 'death_location_id'],
                    'status' => 'kept',
                ],
            ];

            foreach ($scenarios as $scenario) {
                $previous = array_merge($base, $scenario['person']);
                unset($previous['import_id'], $previous['gov_folio']);
                $proposed = array_replace($previous, $scenario['changes']);
                $resolved = $scenario['status'] !== 'pending';

                ImportDifference::create([
                    'import_id' => $importId,
                    'death_id' => null,
                    'source_import_id' => null,
                    'gov_folio' => $scenario['person']['gov_folio'],
                    'previous_values' => $previous,
                    'proposed_values' => $proposed,
                    'changed_fields' => $scenario['fields'],
                    'status' => $scenario['status'],
                    'resolved_by_user_id' => $resolved ? $user->id : null,
                    'resolved_at' => $resolved ? now() : null,
                ]);
            }

            $differenceCount = ImportDifference::query()->where('import_id', $importId)->count();
            $deathCount = DB::table('deaths')->where('import_id', $importId)->count();
            if ($differenceCount !== 6 || $deathCount !== 0) {
                throw new \RuntimeException('La vista previa no quedó aislada correctamente.');
            }

            $this->command?->info("Vista previa creada. Importación: {$importId}; 6 casos; 0 defunciones añadidas.");
        });
    }

    private function removePreviousPreview(): void
    {
        $importIds = DB::table('imports')->where('original_name', self::FILE_NAME)->pluck('id');
        if ($importIds->isEmpty()) {
            return;
        }

        ImportDifference::query()->whereIn('import_id', $importIds)->delete();
        DB::table('deaths')->whereIn('import_id', $importIds)->delete();
        DB::table('imports')->whereIn('id', $importIds)->delete();
    }
}
