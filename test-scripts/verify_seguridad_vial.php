<?php
// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Query the database
$reports = \App\Models\RoadSafetyReport::with(['municipality', 'jurisdiction'])->get();

echo "========================================\n";
echo "VERIFICACIÓN: Reportes de Seguridad Vial\n";
echo "========================================\n";
echo "Total reportes: " . $reports->count() . "\n\n";

foreach($reports as $r) {
  $mun = $r->municipality?->name ?? 'NULL';
  $jur = $r->jurisdiction?->name ?? 'NULL';
  echo "ID: {$r->id}, Municipio: {$mun}, Jurisdicción: {$jur}\n";
}

echo "\n========================================\n";
?>
