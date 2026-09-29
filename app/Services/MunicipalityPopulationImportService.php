<?php

namespace App\Services;

use App\Models\Municipality;
use App\Models\MunicipalityPopulation;
use App\Models\PopulationSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MunicipalityPopulationImportService
{
    private const REQUIRED_COLUMNS = ['CLAVE', 'CLAVE_ENT', 'NOM_ENT', 'NOM_MUN', 'SEXO', 'ANO', 'POB_TOTAL'];

    public function import(string $path, array $metadata = []): array
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException("No se encontró el archivo: {$path}");
        }

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $headers = array_map(fn ($value) => strtoupper(trim((string) $value)), array_shift($rows) ?? []);
        $indexes = array_flip($headers);

        foreach (self::REQUIRED_COLUMNS as $column) {
            if (!array_key_exists($column, $indexes)) {
                throw new InvalidArgumentException("Falta la columna obligatoria {$column}.");
            }
        }

        $groups = [];
        foreach ($rows as $offset => $row) {
            $entityCode = (int) ($row[$indexes['CLAVE_ENT']] ?? 0);
            if ($entityCode !== 28) {
                continue;
            }

            $code = str_pad((string) ((int) ($row[$indexes['CLAVE']] ?? 0)), 5, '0', STR_PAD_LEFT);
            $year = (int) ($row[$indexes['ANO']] ?? 0);
            $sex = strtoupper(trim((string) ($row[$indexes['SEXO']] ?? '')));
            $population = filter_var($row[$indexes['POB_TOTAL']] ?? null, FILTER_VALIDATE_INT);
            $name = trim((string) ($row[$indexes['NOM_MUN']] ?? ''));

            if ($code === '00000' || $year < 1900 || $name === '' || !in_array($sex, ['HOMBRES', 'MUJERES'], true) || $population === false || $population < 0) {
                throw new InvalidArgumentException('Fila '.($offset + 2).' inválida en el archivo de población.');
            }

            $key = "{$code}:{$year}";
            $groups[$key] ??= ['code' => $code, 'year' => $year, 'name' => $name, 'sexes' => []];
            if (array_key_exists($sex, $groups[$key]['sexes'])) {
                throw new InvalidArgumentException("Población duplicada para {$name}, {$year}, {$sex}.");
            }
            $groups[$key]['sexes'][$sex] = (int) $population;
        }

        if (!$groups) {
            throw new InvalidArgumentException('El archivo no contiene población municipal de Tamaulipas.');
        }

        foreach ($groups as $group) {
            if (array_keys($group['sexes']) !== ['HOMBRES', 'MUJERES'] && array_keys($group['sexes']) !== ['MUJERES', 'HOMBRES']) {
                throw new InvalidArgumentException("Falta HOMBRES o MUJERES para {$group['name']}, {$group['year']}.");
            }
        }

        return DB::transaction(function () use ($groups, $metadata) {
            PopulationSource::query()->update(['active' => false]);
            $source = PopulationSource::create([
                'name' => $metadata['name'] ?? 'Consejo Nacional de Población (CONAPO)',
                'version' => $metadata['version'] ?? 'Reconstrucción y proyecciones municipales 1990-2040 (publicación 2024)',
                'source_url' => $metadata['source_url'] ?? 'https://www.gob.mx/conapo/documentos/reconstruccion-y-proyecciones-de-la-poblacion-de-los-municipios-de-mexico-1990-2040',
                'published_at' => $metadata['published_at'] ?? '2024-05-31',
                'imported_at' => now(),
                'active' => true,
            ]);

            $municipalities = Municipality::query()->get()->keyBy(fn ($municipality) => $this->normalizeName($municipality->name));
            $payload = [];
            $linked = [];

            foreach ($groups as $group) {
                $municipality = $municipalities->get($this->normalizeName($group['name']));
                if (!$municipality) {
                    throw new InvalidArgumentException("No se pudo relacionar el municipio '{$group['name']}' con el catálogo del sistema.");
                }

                if (($municipality->inegi_code ?? null) && $municipality->inegi_code !== $group['code']) {
                    throw new InvalidArgumentException("La clave de {$group['name']} no coincide con la ya registrada.");
                }

                $municipality->forceFill(['inegi_code' => $group['code']])->save();
                $linked[$municipality->id] = true;
                $payload[] = [
                    'population_source_id' => $source->id,
                    'municipality_id' => $municipality->id,
                    'geographic_code' => $group['code'],
                    'year' => $group['year'],
                    'population' => array_sum($group['sexes']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            MunicipalityPopulation::query()->insert($payload);

            return [
                'source_id' => $source->id,
                'municipalities' => count($linked),
                'records' => count($payload),
                'year_min' => min(array_column($payload, 'year')),
                'year_max' => max(array_column($payload, 'year')),
            ];
        });
    }

    private function normalizeName(string $name): string
    {
        if (preg_match('/[ÃÂ]/u', $name)) {
            $repaired = @mb_convert_encoding($name, 'Windows-1252', 'UTF-8');
            if (is_string($repaired) && mb_check_encoding($repaired, 'UTF-8')) {
                $name = $repaired;
            }
        }
        $normalized = preg_replace('/[^a-z0-9]+/', '', strtolower(Str::ascii(trim($name))));

        return [
            'ciudadmadero' => 'madero',
            'elmante' => 'mante',
        ][$normalized] ?? $normalized;
    }
}
