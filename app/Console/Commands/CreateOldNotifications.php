<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateOldNotifications extends Command
{
    protected $signature = 'notifications:create-old {--days=31}';
    protected $description = 'Crea notificaciones viejas para probar la poda (usando DB::table para preservar created_at)';

    public function handle()
    {
        $days = (int) $this->option('days');
        $oldDate = Carbon::now()->subDays($days);
        
        $user = User::first();
        
        if (!$user) {
            $this->error('No hay usuarios en la BD.');
            return 1;
        }

        DB::table('notifications')->insert([
            'recipient_user_id' => $user->id,
            'sender_user_id' => null,
            'publication_id' => null,
            'type' => 'test_old',
            'title' => 'TEST: Notificación vieja',
            'message' => "Notificación creada hace {$days} días",
            'read' => false,
            'created_at' => $oldDate,
            'updated_at' => $oldDate,
        ]);

        $this->info("✓ Creada 1 notificación con created_at hace {$days} días (fecha: {$oldDate}).");
        return 0;
    }
}
