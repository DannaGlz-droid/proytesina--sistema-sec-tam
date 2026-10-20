<?php

use Database\Seeders\ActivityTypesSeeder;
use Illuminate\Support\Facades\DB;

it('keeps the activity type catalog unique when it is seeded repeatedly', function (): void {
    $this->seed(ActivityTypesSeeder::class);
    $this->seed(ActivityTypesSeeder::class);

    expect(DB::table('activity_types')->orderBy('name')->pluck('name')->all())
        ->toBe([
            'Capacitación',
            'Conferencia',
            'Evento especial',
            'Reunión',
            'Taller',
        ]);
});
