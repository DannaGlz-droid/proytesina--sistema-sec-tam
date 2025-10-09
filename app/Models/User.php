<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'email',
        'phone',
        'username',
        'is_active',
        'position_id',
        'jurisdiction_id',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [ // Protege datos sensibles en respuestas API
        'password',
        'remember_token',   // Token de sesión
        'phone',            // Teléfono personal
        'email',           // Email institucional
        'username',        // Nombre de usuario
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationship: User belongs to Role
     */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship: User belongs to Position
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relationship: User belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    /**
     * Relationship: User has many Publications
     */
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Relationship: User has many Comments
     */
    public function comments()
    {
        return $this->hasMany(PublicationComment::class);
    }

    /**
     * Relationship: User has many Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}