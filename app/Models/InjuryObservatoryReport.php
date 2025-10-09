<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryObservatoryReport extends Model
{
     /**
     * The table associated with the model.
     */
    protected $table = 'injury_observatory_reports';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'municipality_id',
        'jurisdiction_id',
    ];

    protected $hidden = [
        'publication_id',   // FK sensible
        'municipality_id',  // FK sensible  
        'jurisdiction_id'   // FK sensible
    ];

    /**
     * Relationship: InjuryObservatoryReport belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
    /**
     * Relationship: InjuryObservatoryReport belongs to Municipality
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Relationship: InjuryObservatoryReport belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }
}