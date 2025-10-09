<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathCause extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'death_causes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: DeathCause has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class);
    }
}
