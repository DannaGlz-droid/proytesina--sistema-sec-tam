<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class CreateExpiringNotification extends Command
{
    protected $signature = 'notifications:create-expiring-test
                            {recipient : ID, usuario o correo de la persona destinataria}
                            {--expires-at= : Fecha local YYYY-MM-DD HH:MM; mañana a la hora de limpieza por defecto}';

    protected $description = 'Crea una notificación visible para comprobar la eliminación automática';

    public function handle(): int
    {
        $recipientValue = (string) $this->argument('recipient');
        $recipient = User::query()
            ->where('id', $recipientValue)
            ->orWhere('username', $recipientValue)
            ->orWhere('email', $recipientValue)
            ->first();

        if (! $recipient) {
            $this->error("No se encontró al usuario destinatario: {$recipientValue}.");

            return self::FAILURE;
        }

        $timezone = config('notifications.timezone');
        $expiresAt = $this->expirationDate($timezone);

        $notification = Notification::create([
            'recipient_user_id' => $recipient->id,
            'sender_user_id' => null,
            'publication_id' => null,
            'publication_comment_id' => null,
            'type' => 'system',
            'title' => 'Prueba de eliminación automática',
            'message' => sprintf(
                'Esta notificación de prueba se eliminará el %s a las %s.',
                $expiresAt->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y'),
                $expiresAt->format('H:i')
            ),
            'read' => false,
            'expires_at' => $expiresAt,
        ]);

        $this->info("Notificación {$notification->id} creada para {$recipient->username}.");
        $this->line("Vence: {$expiresAt->format('Y-m-d H:i:s')} ({$timezone}).");

        return self::SUCCESS;
    }

    private function expirationDate(string $timezone): CarbonImmutable
    {
        if ($value = $this->option('expires-at')) {
            return CarbonImmutable::createFromFormat('Y-m-d H:i', $value, $timezone);
        }

        [$hour, $minute] = array_map('intval', explode(':', config('notifications.prune_at')));

        return CarbonImmutable::now($timezone)
            ->addDay()
            ->startOfDay()
            ->setTime($hour, $minute);
    }
}
