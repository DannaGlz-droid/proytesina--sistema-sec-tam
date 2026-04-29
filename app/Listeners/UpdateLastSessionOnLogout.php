<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Events\Dispatchable;

#[\Illuminate\Events\AsListener]
class UpdateLastSessionOnLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event - actualizar last_session SOLO en logout
     */
    public function handle(Logout $event): void
    {
        // Actualizar last_session solo cuando el usuario hace logout
        if ($event->user) {
            $event->user->update(['last_session' => now()]);
        }
    }
}
