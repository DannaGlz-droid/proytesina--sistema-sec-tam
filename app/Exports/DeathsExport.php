<?php

namespace App\Exports;

use App\Models\Death;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeathsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Death::query()->select([
            'id', 'nombre', 'apellido', 'edad', 'sexo', 'fecha_defuncion', 'municipio_id', 'causa_id', 'observaciones'
        ]);

        if (!empty($this->filters['jurisdiccion'])) {
            $q->where('jurisdiccion_id', $this->filters['jurisdiccion']);
        }
        if (!empty($this->filters['municipio'])) {
            $q->where('municipio_id', $this->filters['municipio']);
        }
        if (!empty($this->filters['sexo'])) {
            $q->where('sexo', $this->filters['sexo']);
        }
        if (!empty($this->filters['causa'])) {
            $q->where('causa_id', $this->filters['causa']);
        }
        if (!empty($this->filters['year'])) {
            $year = (int) $this->filters['year'];
            $q->whereYear('fecha_defuncion', $year);
        }
        if (!empty($this->filters['month'])) {
            $month = (int) $this->filters['month'];
            $q->whereMonth('fecha_defuncion', $month);
        }
        if (!empty($this->filters['q'])) {
            $term = $this->filters['q'];
            $q->where(function ($s) use ($term) {
                $s->where('nombre', 'like', "%{$term}%")
                    ->orWhere('apellido', 'like', "%{$term}%");
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'ID', 'Nombre', 'Apellido', 'Edad', 'Sexo', 'Fecha de defunción', 'Municipio ID', 'Causa ID', 'Observaciones'
        ];
    }

    public function map($death): array
    {
        return [
            $death->id,
            $death->nombre,
            $death->apellido,
            $death->edad,
            $death->sexo,
            $death->fecha_defuncion ? $death->fecha_defuncion->format('Y-m-d') : null,
            $death->municipio_id,
            $death->causa_id,
            $death->observaciones,
        ];
    }
}
