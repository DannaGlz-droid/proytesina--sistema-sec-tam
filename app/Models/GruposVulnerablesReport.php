<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GruposVulnerablesReport extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'grupos_vulnerables_reports';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'activity_type_id',
        'participants',
        'location',
        'promoter',
        'municipality_id',
        'jurisdiction_id',
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
     * Relationship: GruposVulnerablesReport belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relationship: GruposVulnerablesReport belongs to ActivityType
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * Relationship: GruposVulnerablesReport belongs to Municipality
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Relationship: GruposVulnerablesReport belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }
}
