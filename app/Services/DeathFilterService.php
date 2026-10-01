<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DeathFilterService
{
    public function describe(array $input): array
    {
        $filters = $this->normalize($input);
        $labels = [];

        if ($filters['start_date'] || $filters['end_date']) {
            $start = $filters['start_date'] ? Carbon::parse($filters['start_date'])->locale('es')->translatedFormat('d M Y') : 'inicio';
            $end = $filters['end_date'] ? Carbon::parse($filters['end_date'])->locale('es')->translatedFormat('d M Y') : 'actualidad';
            $labels[] = "Periodo: {$start} – {$end}";
        } elseif ($filters['years'] !== []) {
            $labels[] = 'Año: '.implode(', ', $filters['years']);
        }

        $this->appendCatalogLabel($labels, 'Municipio de residencia', 'municipalities', $filters['residence_municipality_ids']);
        $this->appendCatalogLabel($labels, 'Municipio de defunción', 'municipalities', $filters['death_municipality_ids']);
        $this->appendCatalogLabel($labels, 'Distrito', 'districts', $filters['district_ids']);
        $this->appendCatalogLabel($labels, 'Distrito de defunción', 'districts', $filters['death_district_ids']);
        $this->appendCatalogLabel($labels, 'Causa', 'death_causes', $filters['cause_ids']);
        $this->appendCatalogLabel($labels, 'Lugar de defunción', 'death_locations', $filters['death_location_ids']);

        if ($filters['sex']) {
            $labels[] = 'Sexo: '.match (mb_strtolower($filters['sex'])) {
                'm' => 'Hombre',
                'f' => 'Mujer',
                default => $filters['sex'],
            };
        }

        $age = $filters['age'];
        $ageUnit = match ($age['unit'] ?? 'years') {
            'days' => ' días',
            'months' => ' meses',
            default => ' años',
        };
        if (($age['type'] ?? null) === 'exact') {
            $labels[] = 'Edad: '.$age['value'].$ageUnit;
        } elseif (($age['type'] ?? null) === 'range') {
            $labels[] = 'Edad: '.$age['min'].'-'.$age['max'].$ageUnit;
        } elseif (($age['type'] ?? null) === 'list') {
            $labels[] = 'Edad: '.implode(', ', $age['values']).$ageUnit;
        } elseif (($age['type'] ?? null) === 'minimum') {
            $labels[] = 'Edad: '.$age['min'].$ageUnit.' o más';
        }

        return $labels;
    }

    /**
     * Convert the filter names used by tables, exports and charts into one contract.
     */
    public function normalize(array $input, array $context = []): array
    {
        $filters = [
            'start_date' => null,
            'end_date' => null,
            'years' => [],
            'months' => [],
            'residence_municipality_ids' => [],
            'death_municipality_ids' => [],
            'district_ids' => [],
            'death_district_ids' => [],
            'cause_ids' => [],
            'death_location_ids' => [],
            'sex' => null,
            'age' => null,
            'origin' => null,
        ];

        $this->normalizeDates($filters, $input);

        $filters['residence_municipality_ids'] = $this->resolveIds(
            'municipalities',
            $input['residence_municipality_ids'] ?? $input['municipio'] ?? []
        );
        $filters['death_municipality_ids'] = $this->resolveIds(
            'municipalities',
            $input['death_municipality_ids'] ?? $input['municipioDefuncion'] ?? []
        );

        $chartMunicipalities = $input['municipios'] ?? $input['municipality_id'] ?? [];
        if ($this->values($chartMunicipalities) !== []) {
            $column = $context['municipality_column'] ?? 'death_municipality_id';
            $target = $column === 'residence_municipality_id'
                ? 'residence_municipality_ids'
                : 'death_municipality_ids';
            $filters[$target] = array_values(array_unique(array_merge(
                $filters[$target],
                $this->resolveIds('municipalities', $chartMunicipalities)
            )));
        }

        $filters['district_ids'] = $this->resolveIds(
            'districts',
            $input['district_ids']
                ?? $input['district_id']
                ?? $input['jurisdicciones']
                ?? $input['distritoes']
                ?? $input['distrito']
                ?? $input['jurisdiccion']
                ?? []
        );
        $filters['death_district_ids'] = $this->resolveIds(
            'districts',
            $input['death_district_ids'] ?? $input['distritoDefuncion'] ?? []
        );
        $filters['cause_ids'] = $this->resolveIds(
            'death_causes',
            $input['cause_ids'] ?? $input['cause_id'] ?? $input['causas'] ?? $input['causa'] ?? []
        );
        $filters['death_location_ids'] = $this->resolveIds(
            'death_locations',
            $input['death_location_ids'] ?? $input['death_location_id'] ?? $input['lugares'] ?? $input['lugar'] ?? []
        );

        $sex = trim((string) ($input['sex'] ?? $input['sexo'] ?? ''));
        $filters['sex'] = $sex !== '' ? $this->normalizeSex($sex) : null;
        $filters['age'] = $this->normalizeAge(
            $input['age'] ?? $input['edad'] ?? null,
            $input['age_unit'] ?? $input['edad_unidad'] ?? 'years'
        );
        $filters['origin'] = $this->normalizeOrigin($input['origin'] ?? null);

        return $filters;
    }

    public function apply(EloquentBuilder|QueryBuilder $query, array $filters): EloquentBuilder|QueryBuilder
    {
        if ($filters['start_date']) {
            $query->whereDate('deaths.death_date', '>=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->whereDate('deaths.death_date', '<=', $filters['end_date']);
        }

        if (! $filters['start_date'] && ! $filters['end_date']) {
            if ($filters['years'] !== []) {
                $query->where(function ($dateQuery) use ($filters) {
                    foreach ($filters['years'] as $index => $year) {
                        $method = $index === 0 ? 'whereYear' : 'orWhereYear';
                        $dateQuery->{$method}('deaths.death_date', $year);
                    }
                });
            }
            if ($filters['months'] !== []) {
                $query->where(function ($dateQuery) use ($filters) {
                    foreach ($filters['months'] as $index => $month) {
                        $method = $index === 0 ? 'whereMonth' : 'orWhereMonth';
                        $dateQuery->{$method}('deaths.death_date', $month);
                    }
                });
            }
        }

        $this->whereIds($query, 'deaths.residence_municipality_id', $filters['residence_municipality_ids']);
        $this->whereIds($query, 'deaths.death_municipality_id', $filters['death_municipality_ids']);
        $this->whereIds($query, 'deaths.district_id', $filters['district_ids']);
        $this->whereIds($query, 'deaths.death_district_id', $filters['death_district_ids']);
        $this->whereIds($query, 'deaths.death_cause_id', $filters['cause_ids']);
        $this->whereIds($query, 'deaths.death_location_id', $filters['death_location_ids']);

        if ($filters['sex']) {
            $query->whereRaw('LOWER(deaths.sex) = ?', [mb_strtolower($filters['sex'])]);
        }

        $this->applyAge($query, $filters['age']);

        $this->applyOrigin($query, $filters['origin']);

        return $query;
    }

    public function applyOrigin(EloquentBuilder|QueryBuilder $query, ?array $origin): EloquentBuilder|QueryBuilder
    {
        if (($origin['type'] ?? null) === 'manual') {
            $query->whereNull('deaths.import_id');
        } elseif (($origin['type'] ?? null) === 'import') {
            $query->where('deaths.import_id', $origin['id']);
        } elseif (($origin['type'] ?? null) === 'invalid') {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function normalizeDates(array &$filters, array $input): void
    {
        $mode = $input['dateRange'] ?? null;

        if (is_numeric($mode)) {
            $filters['start_date'] = now()->subDays((int) $mode)->toDateString();

            return;
        }

        $startDate = $input['start_date'] ?? $input['startDate'] ?? null;
        $endDate = $input['end_date'] ?? $input['endDate'] ?? null;
        $years = $this->parseYears($input['years'] ?? $input['year'] ?? []);
        $months = $this->parseMonths($input['months'] ?? $input['selectedMonths'] ?? $input['month'] ?? []);

        if ($mode === 'all') {
            return;
        }

        if ($mode === 'custom' || ($mode === null && ($startDate || $endDate))) {
            $filters['start_date'] = $this->validDate($startDate);
            $filters['end_date'] = $this->validDate($endDate);

            return;
        }

        if ($mode === 'quarter') {
            $quarter = (int) ($input['quarter'] ?? 0);
            if ($quarter >= 1 && $quarter <= 4) {
                $months = range((($quarter - 1) * 3) + 1, (($quarter - 1) * 3) + 3);
            }
        }

        if ($mode === null || in_array($mode, ['year', 'years', 'month', 'months', 'multiple-months', 'quarter'], true)) {
            $filters['years'] = $years;
            $filters['months'] = in_array($mode, ['month', 'months', 'multiple-months', 'quarter'], true) || $mode === null
                ? $months
                : [];
        }
    }

    private function resolveIds(string $table, mixed $rawValues): array
    {
        $values = $this->values($rawValues);
        $ids = collect($values)->filter(fn ($value) => is_numeric($value))->map(fn ($value) => (int) $value);
        $names = collect($values)->reject(fn ($value) => is_numeric($value))->map(fn ($value) => trim((string) $value))->filter();

        foreach ($names as $name) {
            $matches = DB::table($table)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->pluck('id');
            if ($matches->isEmpty()) {
                $matches = DB::table($table)->where('name', 'like', '%'.$name.'%')->pluck('id');
            }
            $ids = $ids->merge($matches);
        }

        $resolved = $ids->map(fn ($id) => (int) $id)->unique()->values()->all();

        // A supplied value that cannot be resolved must match nothing, never all rows.
        return $values !== [] && $resolved === [] ? [-1] : $resolved;
    }

    private function values(mixed $value): array
    {
        return collect(Arr::wrap($value))
            ->flatMap(fn ($item) => is_string($item) && str_contains($item, ',') ? explode(',', $item) : [$item])
            ->map(fn ($item) => is_string($item) ? trim($item) : $item)
            ->filter(fn ($item) => $item !== null && $item !== '')
            ->values()
            ->all();
    }

    private function parseYears(mixed $value): array
    {
        $currentYear = now()->year;
        $years = [];
        foreach ($this->values($value) as $part) {
            if (is_string($part) && preg_match('/^(\d{4})-(\d{4})$/', $part, $matches)) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];
                $years = array_merge($years, range(min($start, $end), max($start, $end)));
            } elseif (preg_match('/^\d{4}$/', (string) $part)) {
                $years[] = (int) $part;
            }
        }

        return collect($years)->filter(fn ($year) => $year >= 1950 && $year <= $currentYear)->unique()->values()->all();
    }

    private function parseMonths(mixed $value): array
    {
        return collect($this->values($value))
            ->map(fn ($month) => (int) $month)
            ->filter(fn ($month) => $month >= 1 && $month <= 12)
            ->unique()
            ->values()
            ->all();
    }

    private function validDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeSex(string $value): string
    {
        return match (mb_strtolower($value)) {
            'masculino', 'hombre', 'male', 'm' => 'M',
            'femenino', 'mujer', 'female', 'f' => 'F',
            default => $value,
        };
    }

    private function normalizeAge(mixed $value, mixed $unit = 'years'): ?array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $unit = match (mb_strtolower(trim((string) $unit))) {
            'days', 'day', 'dias', 'días' => 'days',
            'months', 'month', 'meses', 'mes' => 'months',
            default => 'years',
        };
        $maximum = match ($unit) {
            'days' => 30,
            'months' => 11,
            default => 150,
        };

        $valid = static fn (int $age): bool => $age >= 0 && $age <= $maximum;

        if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $value, $matches)) {
            $first = (int) $matches[1];
            $second = (int) $matches[2];

            if (! $valid($first) || ! $valid($second)) {
                return null;
            }

            return ['type' => 'range', 'unit' => $unit, 'min' => min($first, $second), 'max' => max($first, $second)];
        }

        if (preg_match('/^(\d+)\s*\+$/', $value, $matches)) {
            $minimum = (int) $matches[1];

            return $valid($minimum) ? ['type' => 'minimum', 'unit' => $unit, 'min' => $minimum] : null;
        }

        if (str_contains($value, ',')) {
            $values = collect(explode(',', $value))->map(fn ($age) => trim($age))->filter(fn ($age) => is_numeric($age))->map(fn ($age) => (int) $age)->unique()->values()->all();

            return $values === [] || collect($values)->contains(fn (int $age) => ! $valid($age))
                ? null
                : ['type' => 'list', 'unit' => $unit, 'values' => $values];
        }

        if (! is_numeric($value) || ! $valid((int) $value)) {
            return null;
        }

        return ['type' => 'exact', 'unit' => $unit, 'value' => (int) $value];
    }

    private function applyAge(EloquentBuilder|QueryBuilder $query, ?array $age): void
    {
        if (! $age) {
            return;
        }

        $unit = $age['unit'] ?? 'years';
        $column = match ($unit) {
            'days' => 'deaths.age_days',
            'months' => 'deaths.age_months',
            default => DB::raw('COALESCE(deaths.age_years, deaths.age)'),
        };

        if ($unit === 'months') {
            $query->whereRaw('COALESCE(deaths.age_years, 0) = 0');
        } elseif ($unit === 'days') {
            $query->whereRaw('COALESCE(deaths.age_years, 0) = 0')
                ->whereRaw('COALESCE(deaths.age_months, 0) = 0');
        }

        if (($age['type'] ?? null) === 'range') {
            $query->whereBetween($column, [$age['min'], $age['max']]);
        } elseif (($age['type'] ?? null) === 'list') {
            $query->whereIn($column, $age['values']);
        } elseif (($age['type'] ?? null) === 'exact') {
            $query->where($column, $age['value']);
        } elseif (($age['type'] ?? null) === 'minimum') {
            $query->where($column, '>=', $age['min']);
        }
    }

    private function normalizeOrigin(mixed $value): ?array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if ($value === 'manual') {
            return ['type' => 'manual'];
        }
        if (preg_match('/^import:(\d+)$/', $value, $matches)) {
            return ['type' => 'import', 'id' => (int) $matches[1]];
        }

        return ['type' => 'invalid'];
    }

    private function whereIds(EloquentBuilder|QueryBuilder $query, string $column, array $ids): void
    {
        if ($ids !== []) {
            $query->whereIn($column, $ids);
        }
    }

    private function appendCatalogLabel(array &$labels, string $prefix, string $table, array $ids): void
    {
        $validIds = array_values(array_filter($ids, fn ($id) => $id > 0));
        if ($validIds === []) {
            return;
        }

        $names = DB::table($table)->whereIn('id', $validIds)->pluck('name')->filter()->values()->all();
        if ($names !== []) {
            $labels[] = $prefix.': '.implode(', ', $names);
        }
    }
}
