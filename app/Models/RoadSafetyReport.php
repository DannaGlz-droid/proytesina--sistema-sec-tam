<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadSafetyReport extends Model
{
     /**
     * The table associated with the model.
     */
    protected $table = 'road_safety_reports';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'activity_type_id',
        'participants',
        'location',
        'promoter',
    ];

    protected $hidden = [
        'publication_id',    // FK - información interna
        'activity_type_id',  // FK - información interna  
        'promoter'           // Nombre personal sensible
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'participants' => 'integer',
    ];

    /**
     * Relationship: RoadSafetyReport belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relationship: RoadSafetyReport belongs to ActivityType
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }
}
