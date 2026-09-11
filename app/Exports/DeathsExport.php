<?php

namespace App\Exports;

use App\Models\Death;
use App\Services\DeathFilterService;
use App\Services\StatisticsAnalysisService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeathsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = []) {}

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

        $deathFilters = app(DeathFilterService::class);
        $deathFilters->apply($query, $deathFilters->normalize($this->filters));
        app(StatisticsAnalysisService::class)->applyRequestedScope($query, $this->filters);

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
}
