<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          VERIFICACIÓN DEL REPORTE DE PRUEBA                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Buscar el reporte ID 184
$report = Publication::withoutTrashed()->find(184);

if ($report) {
    echo "❌ EL REPORTE NO FUE ELIMINADO\n\n";
    echo "Estado actual:\n";
    echo "ID: {$report->id}\n";
    echo "Tema: {$report->topic}\n";
    echo "Fecha creación: {$report->created_at}\n";
    echo "Soft Deleted: NO\n\n";
} else {
    echo "✅ EL REPORTE FUE ELIMINADO CORRECTAMENTE\n\n";
    echo "El reporte ID 184 ya no existe en la BD.\n";
}

// Verificar cuántos reportes de más de 2 años hay ahora
$oldCount = Publication::where('created_at', '<', now()->subYears(2))->count();
echo "\nReportes con más de 2 años en BD: $oldCount\n\n";

if ($report) {
    echo "═════════════════════════════════════════════════════════════\n";
    echo "POSIBLES RAZONES:\n\n";
    echo "1. El Programador de Tareas NO se ejecutó\n";
    echo "   → Verifica que esté activo en Tareas Programadas\n\n";
    echo "2. Se ejecutó pero el comando no funcionó\n";
    echo "   → Ejecuta manualmente: php artisan publications:delete-old\n\n";
    echo "3. Hay un error en la configuración\n";
    echo "   → Revisa los logs\n";
    echo "═════════════════════════════════════════════════════════════\n\n";
}

?>
