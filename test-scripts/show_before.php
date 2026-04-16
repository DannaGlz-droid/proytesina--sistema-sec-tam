<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          ESTADO ACTUAL - ANTES DE ELIMINACIÓN                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$publications = Publication::orderBy('created_at', 'DESC')->limit(10)->get();

echo "REPORTES EN LA BASE DE DATOS:\n";
echo "─────────────────────────────────────────────────────────────\n";

foreach ($publications as $pub) {
    $iOld = $pub->created_at < now()->subYears(2) ? "❌ SERÁ ELIMINADO" : "✅ SE MANTIENE";
    echo "\nID: {$pub->id}\n";
    echo "Tema: {$pub->topic}\n";
    echo "Fecha creación: {$pub->created_at}\n";
    echo "Estado: {$iOld}\n";
}

echo "\n─────────────────────────────────────────────────────────────\n";
echo "Total reportes: " . Publication::count() . "\n";
echo "Reportes antiguos (>2 años): " . Publication::where('created_at', '<', now()->subYears(2))->count() . "\n\n";

?>
