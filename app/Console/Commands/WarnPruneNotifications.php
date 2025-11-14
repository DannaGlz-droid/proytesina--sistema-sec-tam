<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class WarnPruneNotifications extends Command
{
    protected $signature = 'notifications:warn-prune {--role=all : "all" or "admin" - who receives the warning} {--days=89 : Set created_at to N days ago}';

    protected $description = 'Create a warning notification dated N days ago so you can review before pruning.';

    public function handle()
    {
        $role = $this->option('role');
        $days = (int) $this->option('days');

        $date = Carbon::now()->subDays($days);

        if ($role === 'admin') {
            // assuming User model has isAdmin method
            $users = User::all()->filter(function ($u) {
                return method_exists($u, 'isAdmin') ? $u->isAdmin() : ($u->role ?? '') === 'Administrador';
            });
        } else {
            $users = User::all();
        }

        $count = 0;
        foreach ($users as $user) {
            Notification::create([
                'recipient_user_id' => $user->id,
                'sender_user_id' => null,
                'publication_id' => null,
                'type' => 'system',
                'title' => 'Aviso: próximas eliminaciones de notificaciones',
                'message' => 'Este es un aviso de prueba: las notificaciones más antiguas que 90 días se eliminarán mañana. Revísalas si quieres conservar alguna.',
                'read' => false,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $count++;
        }

        $this->info("Created {$count} warning notifications dated {$date->toDateString()} for role={$role}.");
        return 0;
    }
}
