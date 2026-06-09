<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreathalyzerReport extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'breathalyzer_reports';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'checkpoints',
        'tests_performed',
        'drivers_not_fit',
        'women',
        'men',
        'cars_trucks',
        'motorcycles',
        'public_transport_collective',
        'public_transport_individual',
        'cargo_transport',
        'emergency_vehicles',
        'municipality_id',
        'district_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'publication_id'  // FK sensible
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'checkpoints' => 'integer',
        'tests_performed' => 'integer',
        'drivers_not_fit' => 'integer',
        'women' => 'integer',
        'men' => 'integer',
        'cars_trucks' => 'integer',
        'motorcycles' => 'integer',
        'public_transport_collective' => 'integer',
        'public_transport_individual' => 'integer',
        'cargo_transport' => 'integer',
        'emergency_vehicles' => 'integer'
    ];

    /**
     * Relationship: BreathalyzerReport belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relationship: BreathalyzerReport belongs to Municipality
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Relationship: BreathalyzerReport belongs to District
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
