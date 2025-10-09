<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurisdiction extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'jurisdictions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: Jurisdiction has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: Jurisdiction has many Municipalities
     */
    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }

    /**
     * Relationship: Jurisdiction has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class);
    }
}
