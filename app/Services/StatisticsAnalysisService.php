<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class StatisticsAnalysisService
{
    private const CHART_TYPES = [
        'municipios', 'tendencias', 'edades', 'genero', 'causas', 'distritoes', 'comparativa',
    ];

    private const COMPARISON_TYPES = [
        'residencia-defuncion', 'genero-causa', 'edad-causa', 'lugar-causa',
    ];

    public function context(array $input): ?array
    {
        $chartType = (string) ($input['analysis_type'] ?? $input['chart_type'] ?? '');
        if (! in_array($chartType, self::CHART_TYPES, true)) {
            return null;
        }

        $comparisonType = (string) ($input['comparativa_type'] ?? $input['comparison_type'] ?? 'residencia-defuncion');
        if (! in_array($comparisonType, self::COMPARISON_TYPES, true)) {
            $comparisonType = 'residencia-defuncion';
        }

        return [
            'chart_type' => $chartType,
            'comparison_type' => $comparisonType,
            'municipality_type' => ($input['municipio_type'] ?? 'defuncion') === 'residencia' ? 'residencia' : 'defuncion',
            'excluded' => filter_var($input['analysis_excluded'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'label' => $this->label($chartType, $comparisonType),
            'required_fields' => array_values(array_unique(array_column(
                $this->requirements($chartType, $comparisonType, (string) ($input['municipio_type'] ?? 'defuncion')),
                'label'
            ))),
        ];
    }

    public function applyEligible(EloquentBuilder|QueryBuilder $query, array $context): EloquentBuilder|QueryBuilder
    {
        foreach ($this->requirementsFromContext($context) as $requirement) {
            $this->applyValidRequirement($query, $requirement);
        }

        return $query;
    }

    public function applyExcluded(EloquentBuilder|QueryBuilder $query, array $context): EloquentBuilder|QueryBuilder
    {
        $requirements = $this->requirementsFromContext($context);
        if ($requirements === []) {
            return $query->whereRaw('1 = 0');
        }

        $query->where(function ($invalidQuery) use ($requirements) {
            foreach ($requirements as $requirement) {
                $invalidQuery->orWhere(function ($fieldQuery) use ($requirement) {
                    $this->applyInvalidRequirement($fieldQuery, $requirement);
                });
            }
        });

        return $query;
    }

    public function applyRequestedScope(EloquentBuilder|QueryBuilder $query, array $input): EloquentBuilder|QueryBuilder
    {
        $context = $this->context($input);
        if (! $context) {
            return $query;
        }

        return $context['excluded']
            ? $this->applyExcluded($query, $context)
            : $this->applyEligible($query, $context);
    }

    private function requirementsFromContext(array $context): array
    {
        return $this->requirements(
            $context['chart_type'],
            $context['comparison_type'] ?? 'residencia-defuncion',
            $context['municipality_type'] ?? 'defuncion'
        );
    }

    private function requirements(string $chartType, string $comparisonType, string $municipalityType): array
    {
        $municipality = $municipalityType === 'residencia'
            ? $this->relation('deaths.residence_municipality_id', 'municipalities', 'Municipio de residencia')
            : $this->relation('deaths.death_municipality_id', 'municipalities', 'Municipio de defunción');

        return match ($chartType) {
            'municipios' => [$municipality],
            'tendencias' => [$this->value('deaths.death_date', 'Fecha de defunción')],
            'edades' => [$this->age()],
            'genero' => [$this->value('deaths.sex', 'Sexo', true)],
            'causas' => [$this->relation('deaths.death_cause_id', 'death_causes', 'Causa de defunción')],
            'distritoes' => [$this->relation('deaths.district_id', 'districts', 'Distrito de residencia')],
            'comparativa' => match ($comparisonType) {
                'genero-causa' => [
                    $this->value('deaths.sex', 'Sexo', true),
                    $this->relation('deaths.death_cause_id', 'death_causes', 'Causa de defunción'),
                ],
                'edad-causa' => [
                    $this->age(),
                    $this->relation('deaths.death_cause_id', 'death_causes', 'Causa de defunción'),
                ],
                'lugar-causa' => [
                    $this->relation('deaths.death_location_id', 'death_locations', 'Lugar de defunción'),
                    $this->relation('deaths.death_cause_id', 'death_causes', 'Causa de defunción'),
                ],
                default => [
                    $this->relation('deaths.residence_municipality_id', 'municipalities', 'Municipio de residencia'),
                    $this->relation('deaths.death_municipality_id', 'municipalities', 'Municipio de defunción'),
                ],
            },
            default => [],
        };
    }

    private function applyValidRequirement(EloquentBuilder|QueryBuilder $query, array $requirement): void
    {
        $query->whereNotNull($requirement['column']);

        if (($requirement['kind'] ?? null) === 'non_empty') {
            $query->where($requirement['column'], '<>', '');
        }
        if (($requirement['kind'] ?? null) === 'age') {
            $query->where($requirement['column'], '>=', 0);
        }
        if (! empty($requirement['table'])) {
            $query->whereExists(function ($related) use ($requirement) {
                $related->selectRaw('1')
                    ->from($requirement['table'])
                    ->whereColumn($requirement['table'].'.id', $requirement['column']);
            });
        }
    }

    private function applyInvalidRequirement(EloquentBuilder|QueryBuilder $query, array $requirement): void
    {
        $query->whereNull($requirement['column']);

        if (($requirement['kind'] ?? null) === 'non_empty') {
            $query->orWhere($requirement['column'], '');
        }
        if (($requirement['kind'] ?? null) === 'age') {
            $query->orWhere($requirement['column'], '<', 0);
        }
        if (! empty($requirement['table'])) {
            $query->orWhereNotExists(function ($related) use ($requirement) {
                $related->selectRaw('1')
                    ->from($requirement['table'])
                    ->whereColumn($requirement['table'].'.id', $requirement['column']);
            });
        }
    }

    private function relation(string $column, string $table, string $label): array
    {
        return compact('column', 'table', 'label') + ['kind' => 'relation'];
    }

    private function value(string $column, string $label, bool $nonEmpty = false): array
    {
        return compact('column', 'label') + ['kind' => $nonEmpty ? 'non_empty' : 'value'];
    }

    private function age(): array
    {
        return ['column' => 'deaths.age', 'label' => 'Edad', 'kind' => 'age'];
    }

    private function label(string $chartType, string $comparisonType): string
    {
        return match ($chartType) {
            'municipios' => 'Distribución por municipios',
            'tendencias' => 'Tendencia temporal',
            'edades' => 'Distribución por edades',
            'genero' => 'Distribución por género',
            'causas' => 'Causas de defunción',
            'distritoes' => 'Distribución por distritos',
            'comparativa' => match ($comparisonType) {
                'genero-causa' => 'Género por causa de defunción',
                'edad-causa' => 'Rango etario por causa de defunción',
                'lugar-causa' => 'Lugar de defunción por causa',
                default => 'Residencia frente a lugar de defunción',
            },
        };
    }
}
