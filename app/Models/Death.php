<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Death extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'deaths';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'age',
        'sex',
        'death_date',
        'residence_municipality_id',
        'jurisdiction_id',
        'death_municipality_id',
        'death_location_id',
        'death_cause_id',
    ];

    protected $hidden = [
        'residence_municipality_id',  // FK sensible
        'jurisdiction_id',            // FK sensible
        'death_municipality_id',      // FK sensible (te faltó este)
        'death_location_id',          // FK sensible
        'death_cause_id',             // FK sensible
        'name',                       // Información personal sensible
        'first_last_name',            // Información personal sensible
        'second_last_name'            // Información personal sensible
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'death_date' => 'date',
        'age' => 'integer',
        // 'sex' no necesita cast - enum se maneja como string automáticamente
    ];

    /**
     * Relationship: Death belongs to Residence Municipality
     */
    public function residenceMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'residence_municipality_id');
    }

    /**
     * Relationship: Death belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    /**
     * Relationship: Death belongs to Death Location
     */
    public function deathLocation()
    {
        return $this->belongsTo(DeathLocation::class, 'death_location_id');
    }

    /**
     * Relationship: Death belongs to Death Cause
     */
    public function deathCause()
    {
        return $this->belongsTo(DeathCause::class, 'death_cause_id');
    }

    /**
     * Relationship: Death belongs to Death Municipality
     */
    public function deathMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'death_municipality_id');
    }
}
