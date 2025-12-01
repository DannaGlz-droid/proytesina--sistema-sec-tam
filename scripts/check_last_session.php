<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Carbon\Carbon;

$now = Carbon::now();
$since7 = $now->copy()->subDays(7);

$total = User::count();
$recent = User::whereNotNull('last_session')->where('last_session', '>=', $since7)->count();
$nulls = User::whereNull('last_session')->count();

echo "Now: " . $now->toDateTimeString() . PHP_EOL;
echo "Since7: " . $since7->toDateTimeString() . PHP_EOL;

echo "Total users: $total\n";
echo "Users with last_session >= since7: $recent\n";
echo "Users with last_session IS NULL: $nulls\n";

$examples = User::whereNotNull('last_session')->orderBy('last_session', 'desc')->take(10)->get();
if ($examples->isNotEmpty()) {
    echo "\nRecent users (id, username, last_session):\n";
    foreach ($examples as $u) {
        echo "{$u->id}, {$u->username}, {$u->last_session}\n";
    }
}

exit(0);
