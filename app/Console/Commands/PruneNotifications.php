<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class PruneNotifications extends Command
{
    protected $signature = 'notifications:prune {--days=30}';
    protected $description = 'Elimina permanentemente notificaciones antiguas';

    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = Carbon::now()->subDays($days);

        // Obtener notificaciones a eliminar
        $toDelete = Notification::where('created_at', '<=', $threshold)
            ->withoutGlobalScopes()
            ->get();

        $count = $toDelete->count();

        if ($count === 0) {
            $this->info('No hay notificaciones para eliminar.');
            return 0;
        }

        // Eliminar permanentemente (forzar, no soft delete)
        foreach ($toDelete as $notification) {
            $notification->forceDelete();
        }

        $this->info("✓ Eliminadas {$count} notificaciones con created_at <= {$threshold}.");
        return 0;
    }
}
