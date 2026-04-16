<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \Illuminate\Support\Facades\DB::table('failed_import_records')->count();
echo "Total registros fallidos en BD: " . $count . PHP_EOL;

// Check for import 84
$countForImport84 = \Illuminate\Support\Facades\DB::table('failed_import_records')
    ->where('import_id', 84)
    ->count();
echo "Registros fallidos para importación 84: " . $countForImport84 . PHP_EOL;

// Show first few records if any
$records = \Illuminate\Support\Facades\DB::table('failed_import_records')
    ->where('import_id', 84)
    ->limit(5)
    ->get();

if ($records->count() > 0) {
    echo "\nPrimeros registros:\n";
    foreach ($records as $r) {
        echo "ID: " . $r->id . ", Error: " . substr($r->error_message, 0, 50) . "...\n";
    }
} else {
    echo "\nNo hay registros fallidos para import_id 84\n";
}
