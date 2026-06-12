<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

User::select('id', 'name', 'email')->limit(10)->get()->each(function($u) {
    echo $u->id . ' | ' . $u->name . ' | ' . $u->email . PHP_EOL;
});