<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get a canonical admin role
        $role = Role::firstOrCreate(['name' => 'Administrador']);

        // Create or get a position for admin
        $position = Position::firstOrCreate(['name' => 'Administrador']);

        // Create or get a fallback jurisdiction
        $jurisdiction = Jurisdiction::firstOrCreate(['name' => 'Sin jurisdicción']);

        // Credentials to use for the test admin user
        $email = 'admin@example.test';
        $username = 'admin';
        $password = 'Admin1234!';

        // Create or update the admin user
        $user = User::where('email', $email)->first();
        if ($user) {
            // Update existing by email
            $user->update([
                'name' => 'Admin Test',
                'first_last_name' => 'Sistema',
                'second_last_name' => null,
                'username' => $username,
                'phone' => null,
                'password' => Hash::make($password),
                'is_active' => true,
                'position_id' => $position->id,
                'jurisdiction_id' => $jurisdiction->id,
                'role_id' => $role->id,
                'registration_date' => now()->toDateString(),
            ]);
        } else {
            // Ensure username is unique; if taken, append suffix
            $existingUsername = User::where('username', $username)->first();
            if ($existingUsername) {
                $username = $username . '_' . rand(100, 999);
            }

            $user = User::create([
                'email' => $email,
                'name' => 'Admin Test',
                'first_last_name' => 'Sistema',
                'second_last_name' => null,
                'username' => $username,
                'phone' => null,
                'password' => Hash::make($password),
                'is_active' => true,
                'position_id' => $position->id,
                'jurisdiction_id' => $jurisdiction->id,
                'role_id' => $role->id,
                'registration_date' => now()->toDateString(),
            ]);
        }

        $this->command->info("Admin user created/updated:");
        $this->command->info("  email: {$email}");
        $this->command->info("  username: {$username}");
        $this->command->info("  password: {$password}");
    }
}
