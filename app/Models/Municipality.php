<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{

     /**
     * The table associated with the model.
     */
    protected $table = 'municipalities';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'jurisdiction_id'
    ];

    /**
     * Relationship: Municipality belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    /**
     * Relationship: Municipality has many Deaths (residencia y defunción)
     */
    public function residenceDeaths()
    {
        return $this->hasMany(Death::class, 'residence_municipality_id');
    }

    /**
     * Relationship: Municipality has many Deaths locations (lugar de defunción)
     */
    public function deathLocationDeaths()
    {
        return $this->hasMany(Death::class, 'death_municipality_id');
    }
}
