<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PublicationComment extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'publication_comments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'user_id',
        'comment',
        'seen',
    ];

    protected $hidden = [
        'publication_id',  // FK sensible
        'user_id',         // FK sensible
        'seen'             // Estado interno del sistema
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'seen' => 'boolean',
    ]; 

    /**
     * Relationship: PublicationComment belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relationship: PublicationComment belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Per-user read relations
     */
    public function reads()
    {
        return $this->hasMany(CommentRead::class, 'publication_comment_id');
    }

    /**
     * Helper to check if a given user has read this comment
     */
    public function isReadByUser($userId)
    {
        // If the comment_reads table doesn't exist yet (migration not run),
        // avoid throwing an exception and treat as unread.
        if (!Schema::hasTable('comment_reads')) {
            return false;
        }

        return $this->reads()->where('user_id', $userId)->exists();
    }
}
