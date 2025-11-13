<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'roles';

     /** Timestamps (created_at, updated_at)
      * Laravel asume que TODAS las tablas tienen created_at y updated_at
      * public $timestamps = true;
    */

    /** Clave primaria
    * Laravel asume que la clave primaria siempre se llama id
    * Solo si tu clave primaria NO se llama id
    * protected $primaryKey = 'id'; 
    */

    /**
     * The attributes that are mass assignable.
     * Asignación masiva
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /* Campos protegidos
    protected $guarded = [
        'id'
    ];
    */

    /** Conversión de tipos
    *  Solo si la tabla NO tiene created_at y updated_at
    *protected $casts = [
     *   'created_at' => 'datetime',
     *  'updated_at' => 'datetime'
    *]; 
    */

    /**
     * Relationship: Role has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
