<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            ['id' => 1, 'name' => 'Capacitación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Taller', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Platica de sensibilizacion', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Reunión', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Evento especial', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('activity_types')->insert($activityTypes);
    }
}
