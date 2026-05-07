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
        'last_session',
        'position_id',
        'jurisdiction_id',
        'role_id',
        'password',
        'profile_photo_path',
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
     * Accessor: human readable last session status
     * - "En línea" if user has an ACTIVE session in the sessions table
     * - "Desconectado hace X" if user logged out (last_session has a timestamp)
     * - "Nunca" if user never logged in (last_session is NULL and no session record)
     */
    public function getLastSessionDiffAttribute()
    {
        // Check if user has an ACTIVE session in the sessions table
        $hasActiveSession = \DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->subHours(24)->timestamp)
            ->exists();
        
        if ($hasActiveSession) {
            return '<span class="inline-block px-2.5 py-1 rounded border border-green-400 bg-green-50 text-green-700 text-xs font-medium">● En línea</span>';
        }

        // If no active session but has last_session timestamp, show disconnection time
        if ($this->last_session) {
            try {
                $carbon = $this->last_session instanceof \DateTimeInterface 
                    ? \Carbon\Carbon::instance($this->last_session)
                    : \Carbon\Carbon::parse($this->last_session);
                
                // Calculate time difference manually for better Spanish translation
                $now = \Carbon\Carbon::now();
                $diff = $carbon->diff($now);
                
                // Build Spanish phrase based on the difference (abbreviated)
                $phrase = '';
                if ($diff->y > 0) {
                    $phrase = 'hace ' . $diff->y . 'a';
                } elseif ($diff->m > 0) {
                    $phrase = 'hace ' . $diff->m . ' mes';
                } elseif ($diff->d > 0) {
                    $phrase = 'hace ' . $diff->d . 'd';
                } elseif ($diff->h > 0) {
                    $phrase = 'hace ' . $diff->h . 'h';
                } elseif ($diff->i > 0) {
                    $phrase = 'hace ' . $diff->i . ' min';
                } else {
                    $phrase = 'hace unos segundos';
                }
                
                return '<span class="inline-block px-2.5 py-1 rounded border border-dashed border-gray-400 bg-gray-50 text-gray-700 text-xs">Desconectado ' . $phrase . '</span>';
            } catch (\Throwable $e) {
                return '<span class="inline-block px-2.5 py-1 rounded border border-gray-400 bg-gray-50 text-gray-700 text-xs">—</span>';
            }
        }

        // User never logged in
        return '<span class="inline-block px-2.5 py-1 rounded border border-gray-500 bg-gray-100 text-gray-600 text-xs">Nunca</span>';
    }

    /**
     * Helper: Check if user has a specific role
     * 
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Helper: Check if user has any of the given roles
     * 
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Helper: Check if user is Administrator
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }

    /**
     * Helper: Check if user is Coordinator
     * 
     * @return bool
     */
    public function isCoordinator(): bool
    {
        return $this->hasRole('Coordinador');
    }

    /**
     * Helper: Check if user is Operator
     * 
     * @return bool
     */
    public function isOperator(): bool
    {
        return $this->hasRole('Operador');
    }

    /**
     * Helper: Check if user is Guest
     * 
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this->hasRole('Invitado');
    }
}