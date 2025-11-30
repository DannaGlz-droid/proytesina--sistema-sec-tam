<?php
// scripts/count_publication_related.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    'publications',
    'publication_files',
    'publication_comments',
    'notifications',
    'breathalyzer_reports',
    'road_safety_reports',
    'injury_observatory_reports'
];

echo "Current counts for publication-related tables:\n";
foreach ($tables as $t) {
    try {
        $count = DB::table($t)->count();
    } catch (\Exception $e) {
        $count = "ERROR: " . $e->getMessage();
    }
    echo str_pad($t, 30) . ": " . $count . "\n";
}

exit(0);
