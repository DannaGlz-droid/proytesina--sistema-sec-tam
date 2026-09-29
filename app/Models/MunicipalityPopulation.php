<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunicipalityPopulation extends Model
{
    protected $fillable = [
        'population_source_id', 'municipality_id', 'geographic_code', 'year', 'population',
    ];

    protected $casts = [
        'year' => 'integer',
        'population' => 'integer',
    ];

    public function source()
    {
        return $this->belongsTo(PopulationSource::class, 'population_source_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}
