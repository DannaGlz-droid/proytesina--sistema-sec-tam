<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            'Administrador' => [
                'name' => 'Admin Usuario',
                'email' => 'admin@local.test',
            ],
            'Coordinador' => [
                'name' => 'Coordinador Usuario',
                'email' => 'coordinador@local.test',
            ],
            'Operador' => [
                'name' => 'Operador Usuario',
                'email' => 'operador@local.test',
            ],
            'Invitado' => [
                'name' => 'Invitado Usuario',
                'email' => 'invitado@local.test',
            ],
        ];

        // Ensure there is at least one Position and one Jurisdiction to satisfy FK constraints
        $defaultPosition = Position::firstOrCreate(['name' => 'Sin cargo']);
        $defaultJurisdiction = Jurisdiction::firstOrCreate(['name' => 'Sin jurisdicción']);

        foreach ($users as $roleName => $data) {
            $role = Role::where('name', $roleName)->first();

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'first_last_name' => 'Apellido',
                    'second_last_name' => null,
                    'email' => $data['email'],
                    'username' => str_replace(['@', '.'], ['_', '_'], $data['email']),
                    'password' => Hash::make('password'), // contraseña: password
                    'is_active' => true,
                    'registration_date' => $now,
                    'position_id' => $defaultPosition->id,
                    'jurisdiction_id' => $defaultJurisdiction->id,
                    'role_id' => $role ? $role->id : null,
                ]
            );
        }
    }
}
