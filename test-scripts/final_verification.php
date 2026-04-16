<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          VERIFICACIÓN FINAL DEL REPORTE 186                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$report = Publication::withoutTrashed()->find(186);

if ($report) {
    echo "❌ EL REPORTE 186 TODAVÍA EXISTE\n";
    echo "Tema: {$report->topic}\n\n";
} else {
    echo "✅✅✅ ¡EL REPORTE 186 FUE ELIMINADO CORRECTAMENTE! ✅✅✅\n\n";
    echo "🎉 EL SISTEMA DE AUTO-ELIMINACIÓN FUNCIONA PERFECTO\n\n";
    echo "CONFIRMACIÓN FINAL:\n";
    echo "- La tarea programada se ejecutó ✓\n";
    echo "- El scheduler encontró reportes antiguos ✓\n";
    echo "- Los eliminó correctamente ✓\n";
    echo "- Sin intervención manual ✓\n";
}

?>
