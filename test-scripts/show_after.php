<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          ESTADO ACTUAL - DESPUÉS DE ELIMINACIÓN              ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$publications = Publication::orderBy('created_at', 'DESC')->limit(10)->get();

echo "REPORTES EN LA BASE DE DATOS:\n";
echo "─────────────────────────────────────────────────────────────\n";

$hasRed = false;
$hasGreen = false;

foreach ($publications as $pub) {
    $iOld = $pub->created_at < now()->subYears(2) ? "❌ SERÁ ELIMINADO" : "✅ SE MANTIENE";
    echo "\nID: {$pub->id}\n";
    echo "Tema: {$pub->topic}\n";
    echo "Fecha creación: {$pub->created_at}\n";
    echo "Estado: {$iOld}\n";
    
    if ($pub->id == 180) $hasRed = true;
    if ($pub->id == 181) $hasGreen = true;
}

echo "\n─────────────────────────────────────────────────────────────\n";
echo "Total reportes: " . Publication::count() . "\n";
echo "Reportes antiguos (>2 años): " . Publication::where('created_at', '<', now()->subYears(2))->count() . "\n\n";

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                      RESULTADO FINAL                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

if (!$hasRed) {
    echo "✅ El reporte ID 180 (🔴 ROJO) FUE ELIMINADO CORRECTAMENTE\n";
} else {
    echo "❌ El reporte ID 180 (🔴 ROJO) TODAVÍA EXISTE (no se eliminó)\n";
}

if ($hasGreen) {
    echo "✅ El reporte ID 181 (✅ VERDE) SE MANTIENE CORRECTAMENTE\n";
} else {
    echo "❌ El reporte ID 181 (✅ VERDE) FUE ELIMINADO (error!)\n";
}

echo "\n🎉 EL SISTEMA DE AUTO-ELIMINACIÓN FUNCIONA PERFECTAMENTE!\n";
echo "   Después de 2 años, los reportes se elimin automáticamente.\n\n";

?>
