<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'positions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: Position has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
