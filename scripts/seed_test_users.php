<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\District;
use Illuminate\Support\Facades\Hash;

$op    = Role::where('name', 'Operador')->first();
$adm   = Role::where('name', 'Administrador')->first();
$coord = Role::where('name', 'Coordinador')->first();
$pos   = Position::first();
$dist  = District::first();

$roles = [$op, $adm, $coord];
$count = 0;

for ($i = 1; $i <= 30; $i++) {
    $r = $roles[$i % 3];
    if (!User::where('username', 'testuser' . $i)->exists()) {
        User::create([
            'username'          => 'testuser' . $i,
            'email'             => 'test' . $i . '@test.com',
            'name'              => 'Usuario Test',
            'first_last_name'   => 'Apellido' . $i,
            'second_last_name'  => 'Dos',
            'password'          => Hash::make('password'),
            'is_active'         => ($i % 3 !== 0),
            'role_id'           => $r->id,
            'position_id'       => $pos->id,
            'district_id'       => $dist->id,
            'registration_date' => now()->subDays($i)->toDateString(),
        ]);
        $count++;
    }
}

echo "Created: $count users. Total: " . User::count() . PHP_EOL;
