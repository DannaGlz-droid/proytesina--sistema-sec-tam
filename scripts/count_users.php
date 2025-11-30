<?php
// scripts/count_users.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$allowed = [
    'admin_local_test',
    'coordinador_local_test',
    'operador_local_test',
    'invitado_local_test',
    'jojo20',
];

$total = DB::table('users')->count();
$remaining = DB::table('users')->whereIn('username', $allowed)->get();

echo "Total users: " . $total . "\n";
foreach ($remaining as $u) {
    echo "- " . $u->id . " : " . $u->username . " (" . ($u->email ?? 'no-email') . ")\n";
}

exit(0);
