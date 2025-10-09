<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
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
    ];

    /**
     * Relationship: Publication belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
