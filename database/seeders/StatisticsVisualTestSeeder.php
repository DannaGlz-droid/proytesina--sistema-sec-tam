<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StatisticsVisualTestSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->firstOrCreate(['name' => 'Administrador']);
        $position = Position::query()->firstOrCreate(['name' => 'Pruebas visuales']);
        $district = District::query()->firstOrCreate(['name' => 'I - Victoria']);

        User::query()->updateOrCreate(
            ['username' => 'visual_admin'],
            [
                'name' => 'Pruebas',
                'first_last_name' => 'Visuales',
                'second_last_name' => 'SEC-TAM',
                'email' => 'visual-tests@example.test',
                'phone' => '8340000000',
                'email_verified_at' => now(),
                'password' => Hash::make('visual-tests'),
                'is_active' => true,
                'registration_date' => now()->toDateString(),
                'position_id' => $position->id,
                'district_id' => $district->id,
                'role_id' => $role->id,
            ]
        );
    }
}
