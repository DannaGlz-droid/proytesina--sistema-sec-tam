<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Las notificaciones vencidas se eliminan diariamente en la hora de operación estatal.
Schedule::command('notifications:prune')
    ->dailyAt(config('notifications.prune_at'))
    ->timezone(config('notifications.timezone'))
    ->withoutOverlapping();

Schedule::command('publications:delete-old')->dailyAt('02:00')->timezone('America/Mexico_City');
