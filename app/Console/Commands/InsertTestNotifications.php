<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class InsertTestNotifications extends Command
{
    protected $signature = 'notifications:insert-test {--days=29}';
    protected $description = 'Inserta notificaciones de prueba con created_at ajustado';

    public function handle()
    {
        $days = (int) $this->option('days');
        $createdAt = Carbon::now()->subDays($days)->subMinute(1);

        $users = User::all();
        $count = 0;
        foreach ($users as $user) {
            Notification::create([
                'recipient_user_id' => $user->id,
                'sender_user_id' => null,
                'publication_id' => null,
                'type' => 'test_prune',
                'title' => 'TEST: Notificación de poda',
                'message' => 'Esta es una notificación de prueba para la poda automática.',
                'read' => false,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $count++;
        }

        $this->info("Insertadas {$count} notificaciones de prueba con created_at={$createdAt}.");
        return 0;
    }
}
