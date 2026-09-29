<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopulationSource extends Model
{
    protected $fillable = [
        'name', 'version', 'source_url', 'published_at', 'imported_at', 'active',
    ];

    protected $casts = [
        'published_at' => 'date',
        'imported_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function populations()
    {
        return $this->hasMany(MunicipalityPopulation::class);
    }
}
