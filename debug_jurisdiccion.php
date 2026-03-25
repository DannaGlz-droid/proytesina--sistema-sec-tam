<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

// Bootstrap Laravel
$app = app();

use App\Models\FailedImportRecord;

echo "=== Checking Failed Records for Import 90 ===\n\n";

$records = FailedImportRecord::where('import_id', 90)
    ->where('status', 'pending')
    ->limit(1)
    ->get();

if ($records->isNotEmpty()) {
    $record = $records->first();
    
    echo "Record ID: " . $record->id . "\n";
    echo "Error: " . $record->error_message . "\n\n";
    
    echo "=== Original Data Fields ===\n";
    $originalData = $record->original_row_data ?? [];
    var_dump(array_keys($originalData));
    
    echo "\n=== Original Data (Sample) ===\n";
    foreach ($originalData as $key => $value) {
        echo "$key => " . (is_array($value) ? json_encode($value) : $value) . "\n";
    }
    
    echo "\n=== Corrected Data ===\n";
    $correctedData = $record->corrected_data ?? [];
    if (!empty($correctedData)) {
        var_dump(array_keys($correctedData));
    } else {
        echo "Empty\n";
    }
} else {
    echo "No failed records found for import 90\n";
}
