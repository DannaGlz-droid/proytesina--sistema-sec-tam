<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$activityTypes = DB::table('activity_types')->get();

echo "Activity Types en la base de datos:\n";
echo "===================================\n\n";

if ($activityTypes->isEmpty()) {
    echo "❌ La tabla está VACÍA\n";
} else {
    foreach ($activityTypes as $type) {
        echo "ID: {$type->id} - Nombre: {$type->name}\n";
    }
    echo "\nTotal: " . $activityTypes->count() . " tipos de actividad\n";
}
