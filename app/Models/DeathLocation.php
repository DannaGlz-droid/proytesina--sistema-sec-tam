<?php

namespace App\Models;

use App\Support\CatalogLabel;
use Illuminate\Database\Eloquent\Model;

class DeathLocation extends Model
{
     /**
     * The table associated with the model.
     */
    protected $table = 'death_locations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'usage_count',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function getDisplayNameAttribute(): string
    {
        return CatalogLabel::location($this->name);
    }

    /**
     * Relationship: DeathLocation has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class);
    }

    /**
     * Scope: Only active death locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
