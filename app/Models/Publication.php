<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use SoftDeletes;

     /**
     * The table associated with the model.
     */
    protected $table = 'publications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'publication_type',
        'topic',
        'description',
        'publication_date',
        'activity_date',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $hidden = [ // Protege datos sensibles en respuestas API
        'user_id',          // ID del usuario que creó la publicación
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'publication_date' => 'date',
        'activity_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relationship: Publication belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: User who approved the publication
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship: User who rejected the publication
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Relationship: Publication has many Files
     */
    public function files()
    {
        return $this->hasMany(PublicationFile::class);
    }

    /**
     * Relationship: Publication has many Comments
     */
    public function comments()
    {
        return $this->hasMany(PublicationComment::class);
    }

    /**
     * Relationship: Publication has many Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relationship: Publication has many Road Safety Reports
     */
    public function roadSafetyReports()
    {
        return $this->hasMany(RoadSafetyReport::class);
    }

    /**
     * Relationship: Publication has many Injury Observatory Reports
     */
    public function injuryObservatoryReports()
    {
        return $this->hasMany(InjuryObservatoryReport::class);
    }

    /**
     * Relationship: Publication has many Breathalyzer Reports
     */
    public function breathalyzerReports()
    {
        return $this->hasMany(BreathalyzerReport::class);
    }

    /**
     * Check if the publication can be edited by the given user
     */
    public function canBeEditedBy($userId)
    {
        // El autor puede editar la publicación mientras NO esté aprobada.
        // Normalizar estado y usar comparación laxa para evitar fallos por tipo.
        $status = strtolower(trim((string) $this->status));
        return $this->user_id == $userId && $status !== 'aprobado';
    }

    /**
     * Check if the publication can be resubmitted
     */
    public function canBeResubmitted()
    {
        return $this->status === 'rechazado';
    }

    /**
     * Check if the publication is pending review
     */
    public function isPending()
    {
        return $this->status === 'pendiente';
    }

    /**
     * Check if the publication is approved
     */
    public function isApproved()
    {
        return $this->status === 'aprobado';
    }

    /**
     * Check if the publication is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rechazado';
    }
}
