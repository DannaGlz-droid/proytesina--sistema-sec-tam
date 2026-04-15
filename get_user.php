<?php
require_once 'vendor/autoload.php';
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::first();
echo "Email: " . ($user?->email ?? 'No hay usuarios') . "\n";
echo "Usuario ID: " . ($user?->id ?? 'N/A') . "\n";

?>
