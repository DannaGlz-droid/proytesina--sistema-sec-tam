<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Retención de notificaciones
    |--------------------------------------------------------------------------
    |
    | La operación central del sistema usa la hora de Ciudad Victoria.
    | Las notificaciones son información transitoria y se eliminan después
    | de este número de meses calendario.
    |
    */
    'timezone' => env('NOTIFICATIONS_TIMEZONE', 'America/Monterrey'),
    'retention_months' => (int) env('NOTIFICATIONS_RETENTION_MONTHS', 3),
    'prune_at' => env('NOTIFICATIONS_PRUNE_AT', '02:00'),
];
