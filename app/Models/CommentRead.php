<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentRead extends Model
{
    protected $table = 'comment_reads';

    protected $fillable = [
        'publication_comment_id',
        'user_id',
        'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];

    public function comment()
    {
        return $this->belongsTo(PublicationComment::class, 'publication_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
