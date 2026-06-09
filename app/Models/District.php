<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{

     /**
     * The table associated with the model.
     */
    protected $table = 'districts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: District has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'district_id');
    }

    /**
     * Relationship: District has many Municipalities
     */
    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'district_id');
    }

    /**
     * Relationship: District has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class, 'district_id');
    }

    /**
     * Relationship: District has many RoadSafetyReports
     */
    public function roadSafetyReports()
    {
        return $this->hasMany(RoadSafetyReport::class, 'district_id');
    }

    /**
     * Relationship: District has many InjuryObservatoryReports
     */
    public function injuryObservatoryReports()
    {
        return $this->hasMany(InjuryObservatoryReport::class, 'district_id');
    }

    /**
     * Relationship: District has many GruposVulnerablesReports
     */
    public function gruposVulnerablesReports()
    {
        return $this->hasMany(GruposVulnerablesReport::class, 'district_id');
    }
}
