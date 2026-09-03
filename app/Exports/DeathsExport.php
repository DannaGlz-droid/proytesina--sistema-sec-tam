<?php

namespace App\Exports;

use App\Models\Death;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeathsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = Death::query()->with([
            'residenceMunicipality',
            'district',
            'deathMunicipality',
            'deathDistrict',
            'deathLocation',
            'deathCause',
        ]);

        $this->applyRelatedFilter($query, 'district', 'district_id', $this->filters['distrito'] ?? $this->filters['jurisdiccion'] ?? null);
        $this->applyRelatedFilter($query, 'residenceMunicipality', 'residence_municipality_id', $this->filters['municipio'] ?? null);
        $this->applyRelatedFilter($query, 'deathMunicipality', 'death_municipality_id', $this->filters['municipioDefuncion'] ?? null);

        if (!empty($this->filters['sexo'])) {
            $query->whereRaw('LOWER(sex) = ?', [mb_strtolower((string) $this->filters['sexo'])]);
        }

        if (!empty($this->filters['causa'])) {
            $cause = $this->filters['causa'];
            is_numeric($cause)
                ? $query->where('death_cause_id', (int) $cause)
                : $query->whereHas('deathCause', fn (Builder $relation) => $relation->where('name', 'like', '%'.$cause.'%'));
        }

        if (!empty($this->filters['year'])) {
            $query->whereYear('death_date', (int) $this->filters['year']);
        }

        if (!empty($this->filters['month'])) {
            $query->whereMonth('death_date', (int) $this->filters['month']);
        }

        if (!empty($this->filters['startDate'])) {
            $query->whereDate('death_date', '>=', $this->filters['startDate']);
        }

        if (!empty($this->filters['endDate'])) {
            $query->whereDate('death_date', '<=', $this->filters['endDate']);
        }

        return $query->orderByDesc('death_date')->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Nombre(s)',
            'Apellido paterno',
            'Apellido materno',
            'Sexo',
            'Edad',
            'Municipio de residencia',
            'Distrito de residencia',
            'Municipio de defunción',
            'Distrito de defunción',
            'Fecha de defunción',
            'Lugar de defunción',
            'Causa de defunción',
        ];
    }

    public function map($death): array
    {
        return [
            $death->gov_folio,
            $death->name_formatted,
            $death->first_last_name_formatted,
            $death->second_last_name_formatted,
            $death->sex,
            $death->pretty_age,
            $death->residenceMunicipality?->name,
            $death->district?->display_name,
            $death->deathMunicipality?->name,
            $death->deathDistrict?->display_name,
            $death->death_date?->format('d/m/Y'),
            $death->deathLocation?->name,
            $death->deathCause?->name,
        ];
    }

    private function applyRelatedFilter(Builder $query, string $relation, string $foreignKey, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (is_numeric($value)) {
            $query->where($foreignKey, (int) $value);
            return;
        }

        $query->whereHas($relation, fn (Builder $related) => $related->where('name', 'like', '%'.$value.'%'));
    }
}
