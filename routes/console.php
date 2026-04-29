<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks (ejecutar diariamente a las 2:00 AM)
Schedule::command('notifications:prune')->dailyAt('02:00')->timezone('America/Mexico_City');
Schedule::command('publications:delete-old')->dailyAt('02:00')->timezone('America/Mexico_City');
