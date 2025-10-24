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
        'registration_date',
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
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'registration_date' => 'datetime',
        'last_session' => 'datetime',
    ];

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

    /**
     * Relationship: User has many Road Safety Reports
     */
    public function roadSafetyReports()
    {
        return $this->hasMany(RoadSafetyReport::class);
    }

    /**
     * Relationship: User has many Injury Observatory Reports
     */
    public function injuryObservatoryReports()
    {
        return $this->hasMany(InjuryObservatoryReport::class);
    }

    /**
     * Relationship: User has many Breathalyzer Reports
     */
    public function breathalyzerReports()
    {
        return $this->hasMany(BreathalyzerReport::class);
    }

    /**
     * Accessor: formatted registration date (d/m/Y) or null
     * Keeps formatting logic out of the view.
     */
    public function getFormattedRegistrationDateAttribute()
    {
        if (! $this->registration_date) {
            return null;
        }

        try {
            if ($this->registration_date instanceof \DateTimeInterface) {
                return $this->registration_date->format('d/m/Y');
            }

            return \Carbon\Carbon::parse($this->registration_date)->format('d/m/Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Accessor: human readable last session (diffForHumans) or null
     */
    public function getLastSessionDiffAttribute()
    {
        if (! $this->last_session) {
            return null;
        }

        try {
            if ($this->last_session instanceof \DateTimeInterface) {
                return \Carbon\Carbon::instance($this->last_session)->diffForHumans();
            }

            return \Carbon\Carbon::parse($this->last_session)->diffForHumans();
        } catch (\Throwable $e) {
            return null;
        }
    }
}