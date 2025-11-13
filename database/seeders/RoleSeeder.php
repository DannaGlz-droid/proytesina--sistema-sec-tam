<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'description' => 'Control absoluto del sistema: gestión de usuarios, reportes y estadísticas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Coordinador',
                'description' => 'Acceso a reportes y estadísticas (sin gestión de usuarios)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operador',
                'description' => 'Solo puede crear y gestionar reportes propios (sin acceso a estadísticas)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Invitado',
                'description' => 'Solo lectura: puede visualizar reportes y estadísticas públicas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('✓ Roles creados exitosamente');
    }
}
