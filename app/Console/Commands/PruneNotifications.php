<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PruneNotifications extends Command
{
    protected $signature = 'notifications:prune
                            {--months= : Retención para registros antiguos sin expires_at}';

    protected $description = 'Elimina permanentemente las notificaciones cuya retención terminó';

    public function handle(): int
    {
        $timezone = config('notifications.timezone');
        $months = $this->option('months') !== null
            ? max(1, (int) $this->option('months'))
            : config('notifications.retention_months');
        $now = CarbonImmutable::now($timezone);
        $legacyThreshold = $now->subMonthsNoOverflow($months);

        $count = Notification::query()
            ->withTrashed()
            ->where(function (Builder $query) use ($now, $legacyThreshold): void {
                $query->where('expires_at', '<=', $now)
                    ->orWhere(function (Builder $legacy) use ($legacyThreshold): void {
                        $legacy->whereNull('expires_at')
                            ->where('created_at', '<=', $legacyThreshold);
                    });
            })
            ->forceDelete();

        if ($count === 0) {
            $this->info("No hay notificaciones vencidas ({$timezone}).");

            return self::SUCCESS;
        }

        $this->info("Se eliminaron {$count} notificaciones vencidas.");

        return self::SUCCESS;
    }
}
