<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          VERIFICACIÓN - ¿QUÉ PASÓ A LAS 5:20 PM?            ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$report = Publication::withoutTrashed()->find(188);

if ($report) {
    echo "❌ EL REPORTE 188 TODAVÍA EXISTE\n\n";
    echo "La tarea NO se ejecutó automáticamente.\n\n";
    echo "Posibles problemas:\n";
    echo "1. Las rutas podrían estar mal\n";
    echo "2. El desencadenador no está correctamente configurado\n";
    echo "3. La tarea se ejecutó pero falló (error silencioso)\n\n";
} else {
    echo "✅✅✅ ¡EL REPORTE 188 FUE ELIMINADO! ✅✅✅\n\n";
    echo "¡La tarea se ejecutó AUTOMÁTICAMENTE!\n";
    echo "¡TODO FUNCIONA PERFECTAMENTE!\n\n";
}

echo "═════════════════════════════════════════════════════════════\n\n";

// Verificar manualmente si la ruta está correcta ejecutando un comando
echo "Intentando ejecutar el comando manualmente para verificar...\n\n";

exec("C:\\xampp\\php\\php.exe \"C:\\Proyectos Laravel\\sistema-sec-tam\\artisan\" schedule:run 2>&1", $output, $exitCode);

echo "Resultado del comando manual:\n";
if ($exitCode === 0) {
    echo "✅ El comando se ejecutó correctamente\n";
    echo "Salida: " . implode("\n", $output) . "\n";
} else {
    echo "❌ El comando falló con código: $exitCode\n";
    echo "Error: " . implode("\n", $output) . "\n";
}

?>
