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

    public const ALLOWED_NAMES = [
        'PEATON RESIDENCIA',
        'VEHICULO DE MOTOR RESIDENCIA',
        'EXPO FUEGO Y HUMO RESIDENCIA',
        'CAIDAS ACCIDENTALES',
        'AHOGAMIENTO RESIDENCIA',
        'ENVENENAMIENTO RES',
        'OTROS ACCIDENTES',
    ];

    public static function allowedNames(): array
    {
        return self::ALLOWED_NAMES;
    }

    public static function allowedCatalog()
    {
        foreach (self::ALLOWED_NAMES as $name) {
            self::firstOrCreate(['name' => $name]);
        }

        return self::query()
            ->whereIn('name', self::ALLOWED_NAMES)
            ->orderBy('name')
            ->get();
    }

    /**
     * Relationship: DeathCause has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class);
    }
}
