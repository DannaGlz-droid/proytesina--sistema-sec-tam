<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class PruneNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:prune {--days=90 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete notifications older than specified days (default: 90)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Deleting notifications older than {$days} days (before {$cutoffDate->toDateString()})...");

        // Eliminar en batches para no sobrecargar la base de datos
        $totalDeleted = 0;
        $batchSize = 1000;

        do {
            $deleted = Notification::where('created_at', '<', $cutoffDate)
                ->limit($batchSize)
                ->delete();
            
            $totalDeleted += $deleted;
            
            if ($deleted > 0) {
                $this->line("Deleted {$deleted} notifications (total: {$totalDeleted})...");
            }
            
            // Pequeña pausa para no saturar la DB
            usleep(100000); // 0.1 segundos
            
        } while ($deleted > 0);

        $this->info("✓ Completed! Total deleted: {$totalDeleted} notifications.");
        
        return 0;
    }
}
