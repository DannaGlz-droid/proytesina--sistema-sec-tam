<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Publication;
use App\Models\CommentRead;
use App\Models\User;

echo "=== DEBUG COMMENT READS ===\n\n";

// Obtener todas las publicaciones con comentarios
$publications = Publication::with(['comments.user', 'comments.reads.user'])->get();

foreach ($publications as $pub) {
    if ($pub->comments->count() > 0) {
        echo "Publication #{$pub->id}: {$pub->topic}\n";
        echo "  Total comments: {$pub->comments->count()}\n";
        
        foreach ($pub->comments as $comment) {
            echo "  - Comment #{$comment->id} by User #{$comment->user_id} ({$comment->user->name})\n";
            echo "    Total reads: {$comment->reads->count()}\n";
            
            if ($comment->reads->count() > 0) {
                foreach ($comment->reads as $read) {
                    echo "      Read by User #{$read->user_id} ({$read->user->name}) at {$read->seen_at}\n";
                }
            } else {
                echo "      No reads yet\n";
            }
        }
        echo "\n";
    }
}

// Verificar para cada usuario qué comentarios debería ver como "no leídos"
echo "\n=== PER USER UNREAD STATUS ===\n\n";

$users = User::all();
foreach ($users as $user) {
    echo "User #{$user->id} ({$user->name}) - Role: {$user->role->name}\n";
    
    foreach ($publications as $pub) {
        if ($pub->comments->count() > 0) {
            $unreadComments = [];
            foreach ($pub->comments as $comment) {
                // Skip own comments
                if ($comment->user_id == $user->id) {
                    continue;
                }
                
                // Check if read
                $isRead = $comment->reads()->where('user_id', $user->id)->exists();
                if (!$isRead) {
                    $unreadComments[] = "Comment #{$comment->id} by User #{$comment->user_id}";
                }
            }
            
            if (!empty($unreadComments)) {
                echo "  Publication #{$pub->id} has unread comments:\n";
                foreach ($unreadComments as $uc) {
                    echo "    - {$uc}\n";
                }
            }
        }
    }
    echo "\n";
}
