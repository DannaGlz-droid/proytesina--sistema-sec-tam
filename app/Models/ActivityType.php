<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'activity_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: ActivityType has many Road Safety Reports
     */
    public function roadSafetyReports()
    {
        return $this->hasMany(RoadSafetyReport::class);
    }
}
