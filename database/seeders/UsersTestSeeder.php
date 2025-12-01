<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;
use Illuminate\Support\Facades\DB;

class UsersTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Ensure roles
            $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
            $operatorRole = Role::firstOrCreate(['name' => 'Operador']);
            $coordRole = Role::firstOrCreate(['name' => 'Coordinador']);
            $guestRole = Role::firstOrCreate(['name' => 'Invitado']);
            $userRole = Role::firstOrCreate(['name' => 'Usuario']);

            // Ensure positions
            $posMed = Position::firstOrCreate(['name' => 'Médico']);
            $posEnf = Position::firstOrCreate(['name' => 'Enfermero']);
            $posTec = Position::firstOrCreate(['name' => 'Técnico']);
            $posAdm = Position::firstOrCreate(['name' => 'Administrativo']);

            // Ensure jurisdictions
            $jur1 = Jurisdiction::firstOrCreate(['name' => 'I']);
            $jur2 = Jurisdiction::firstOrCreate(['name' => 'II']);
            $jurOtro = Jurisdiction::firstOrCreate(['name' => 'OTRO']);

            // Create 5 users
            $users = [
                [
                    'username' => 'testadmin',
                    'email' => 'testadmin@example.com',
                    'name' => 'Admin Prueba',
                    'first_last_name' => 'Gestion',
                    'second_last_name' => 'Sistema',
                    'phone' => '5550000001',
                    'is_active' => true,
                    'registration_date' => now()->subDays(30)->toDateString(),
                    'last_session' => now()->subHours(2),
                    'position_id' => $posAdm->id,
                    'jurisdiction_id' => $jur1->id,
                    'role_id' => $adminRole->id,
                    'password' => Hash::make('Password123!'),
                ],
                [
                    'username' => 'operator1',
                    'email' => 'operator1@example.com',
                    'name' => 'Operador Uno',
                    'first_last_name' => 'Unidad',
                    'second_last_name' => 'Centro',
                    'phone' => '5550000002',
                    'is_active' => false,
                    'registration_date' => now()->subDays(60)->toDateString(),
                    'last_session' => now()->subDays(10),
                    'position_id' => $posTec->id,
                    'jurisdiction_id' => $jur2->id,
                    'role_id' => $operatorRole->id,
                    'password' => Hash::make('Password123!'),
                ],
                [
                    'username' => 'coord1',
                    'email' => 'coord1@example.com',
                    'name' => 'Coordinador Uno',
                    'first_last_name' => 'Coord',
                    'second_last_name' => 'Area',
                    'phone' => '5550000003',
                    'is_active' => true,
                    'registration_date' => now()->subDays(15)->toDateString(),
                    'last_session' => now()->subDays(1),
                    'position_id' => $posMed->id,
                    'jurisdiction_id' => $jur1->id,
                    'role_id' => $coordRole->id,
                    'password' => Hash::make('Password123!'),
                ],
                [
                    'username' => 'guest1',
                    'email' => 'guest1@example.com',
                    'name' => 'Invitado Uno',
                    'first_last_name' => 'Visitante',
                    'second_last_name' => null,
                    'phone' => null,
                    'is_active' => true,
                    'registration_date' => now()->toDateString(),
                    'last_session' => null,
                    'position_id' => $posEnf->id,
                    'jurisdiction_id' => $jurOtro->id,
                    'role_id' => $guestRole->id,
                    'password' => Hash::make('Password123!'),
                ],
                [
                    'username' => 'usuario1',
                    'email' => 'usuario1@example.com',
                    'name' => 'Usuario Prueba',
                    'first_last_name' => 'Campo',
                    'second_last_name' => 'General',
                    'phone' => '5550000004',
                    'is_active' => true,
                    'registration_date' => now()->subDays(5)->toDateString(),
                    'last_session' => now()->subMinutes(90),
                    'position_id' => $posTec->id,
                    'jurisdiction_id' => $jur2->id,
                    'role_id' => $userRole->id,
                    'password' => Hash::make('Password123!'),
                ],
            ];

            foreach ($users as $u) {
                // Avoid duplicate unique key errors
                $existing = User::where('username', $u['username'])->orWhere('email', $u['email'])->first();
                if ($existing) {
                    // update some attributes to ensure filters can match
                    $existing->update([
                        'is_active' => $u['is_active'],
                        'role_id' => $u['role_id'],
                        'position_id' => $u['position_id'],
                        'jurisdiction_id' => $u['jurisdiction_id'],
                    ]);
                    continue;
                }

                User::create($u);
            }
        });

        $this->command->info('UsersTestSeeder: 5 users created/updated.');
    }
}
