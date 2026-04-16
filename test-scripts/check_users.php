<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = DB::table('users')->get();

echo "Usuarios en la base de datos:\n";
echo "===================================\n\n";

if ($users->isEmpty()) {
    echo "❌ La tabla users está VACÍA - NO HAY USUARIOS\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id} - Nombre: {$user->name} - Email: {$user->email}\n";
    }
    echo "\nTotal: " . $users->count() . " usuarios\n";
}
