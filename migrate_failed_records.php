<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\FailedImportRecord;

$importId = 84;

// Get the import record to find the error CSV path
$import = DB::table('imports')->where('id', $importId)->first();

if (!$import) {
    echo "Import ID $importId not found\n";
    exit(1);
}

echo "Import encontrada: {$import->original_name}\n";
echo "Ruta del error CSV: {$import->error_csv_path}\n";
echo "Registros fallidos según registro: {$import->rows_failed}\n\n";

if (!$import->error_csv_path) {
    echo "No hay archivo CSV de errores para esta importación\n";
    exit(0);
}

// Read the error CSV file
$csvPath = storage_path('app/' . $import->error_csv_path);

if (!file_exists($csvPath)) {
    echo "Archivo CSV no encontrado en: $csvPath\n";
    exit(1);
}

echo "Leyendo archivo: $csvPath\n\n";

// Read CSV
$file = fopen($csvPath, 'r');
$headers = fgetcsv($file);
$rowCount = 0;

while (($row = fgetcsv($file)) !== false) {
    if (empty($row) || (count($row) === 1 && empty($row[0]))) {
        continue;
    }

    // Map CSV row to associative array using headers
    $rowData = [];
    foreach ($headers as $i => $header) {
        $rowData[$header] = $row[$i] ?? null;
    }

    // Extract error message if it exists
    $errorMessage = $rowData['errors'] ?? 'Error desconocido durante la importación';
    
    // Remove the special fields (sheet, row, errors) from the original data
    unset($rowData['sheet']);
    unset($rowData['row']);
    unset($rowData['errors']);

    // Create the failed record
    try {
        FailedImportRecord::create([
            'import_id' => $importId,
            'original_row_data' => $rowData,
            'error_message' => $errorMessage,
            'status' => 'pending',
        ]);
        $rowCount++;
        echo "✓ Registro $rowCount guardado\n";
    } catch (\Exception $e) {
        echo "✗ Error al guardar registro: " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n=== RESUMEN ===\n";
echo "Total de registros procesados: $rowCount\n";
echo "Registros en BD para import_id $importId: " . FailedImportRecord::where('import_id', $importId)->count() . "\n";
