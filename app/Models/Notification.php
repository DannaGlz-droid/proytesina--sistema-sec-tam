<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'recipient_user_id',
        'sender_user_id',
        'publication_id',
        'publication_comment_id',
        'type',
        'title',
        'message',
        'read',
    ];

    protected $hidden = [
        'recipient_user_id',  // FK sensible
        'sender_user_id',     // FK sensible  
        'publication_id',     // FK sensible
        'publication_comment_id',
        'read'                // Estado interno del sistema
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'read' => 'boolean',  // Convierte 1/0 a true/false
    ];

    /**
     * Relationship: Notification belongs to Publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function comment()
    {
        return $this->belongsTo(\App\Models\PublicationComment::class, 'publication_comment_id');
    }

    /**
     * Relationship: Notification belongs to User (Sender)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Relationship: Notification belongs to User (Recipient)
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
