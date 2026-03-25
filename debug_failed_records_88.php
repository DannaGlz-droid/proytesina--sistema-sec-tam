<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\FailedImportRecord;

// Get the Laravel application instance
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Check failed records for import 88
$failedRecords = FailedImportRecord::where('import_id', 88)
    ->where('status', 'pending')
    ->get();

echo "Total failed records (pending) for import 88: " . count($failedRecords) . PHP_EOL;
echo "\nRecords:\n";
foreach ($failedRecords as $record) {
    echo "- ID: {$record->id}, Status: {$record->status}, Error: {$record->error_message}\n";
}

// Check all statuses for import 88
$allRecords = FailedImportRecord::where('import_id', 88)->get();
echo "\nTotal failed records (all statuses) for import 88: " . count($allRecords) . PHP_EOL;
echo "Statuses:\n";
$statusCounts = $allRecords->groupBy('status')->map->count();
foreach ($statusCounts as $status => $count) {
    echo "- $status: $count\n";
}
