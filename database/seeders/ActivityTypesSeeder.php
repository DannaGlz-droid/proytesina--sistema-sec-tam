<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $activityTypes = [
            ['name' => 'Capacitación', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Taller', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Conferencia', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Reunión', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Evento especial', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('activity_types')->upsert($activityTypes, ['name'], ['updated_at']);
    }
}
