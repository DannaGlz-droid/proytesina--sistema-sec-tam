<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Publication;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Obtener un user_id válido
$validUserId = DB::table('users')->first()?->id ?? 1;

echo "=== PRUEBA DE AUTO-ELIMINACIÓN EN TIEMPO REAL ===\n\n";

// Paso 1: Modificar scheduler temporalmente
echo "📝 Paso 1: Modificando scheduler para ejecutarse cada minuto...\n";
$consolePath = 'routes/console.php';
$consoleContent = file_get_contents($consolePath);
$backupPath = 'routes/console.php.backup';
file_put_contents($backupPath, $consoleContent);

// Reemplazar la línea de scheduleo
$modifiedContent = str_replace(
    "Schedule::command('publications:delete-old')->dailyAt('02:00');",
    "Schedule::command('publications:delete-old')->everyMinute();",
    $consoleContent
);
file_put_contents($consolePath, $modifiedContent);
echo "✓ Scheduler modificado a cada minuto\n\n";

// Paso 2: Crear reporte de prueba con fecha antigua (2 años y 1 minuto atrás)
echo "📝 Paso 2: Creando reporte de prueba con más de 2 años de antigüedad...\n";
$twoYearsAgo = now()->subYears(2)->subMinutes(1);
$testReportId = DB::table('publications')->insertGetId([
    'publication_type' => 'AUTO-DELETE TEST',
    'topic' => '🔴 PRUEBA AUTO-DELETE EN TIEMPO REAL - ' . date('Y-m-d H:i:s'),
    'description' => 'Este reporte será eliminado automáticamente en menos de 1 minuto',
    'publication_date' => $twoYearsAgo->format('Y-m-d'),
    'activity_date' => $twoYearsAgo->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'borrador',
    'created_at' => $twoYearsAgo,
    'updated_at' => $twoYearsAgo,
]);
echo "✓ Reporte creado con ID: $testReportId\n";
echo "✓ Fecha de creación: " . $twoYearsAgo->format('Y-m-d H:i:s') . " (hace 2 años y 1 minuto)\n\n";

// Paso 3: Monitorear eliminación
echo "📝 Paso 3: Monitoreando eliminación cada 10 segundos...\n";
echo "⏱️ El reporte debe ser eliminado en menos de 1 minuto cuando se ejecute el scheduler\n\n";

$startTime = time();
$maxWaitSeconds = 120; // Esperar máximo 2 minutos
$checkInterval = 10; // Revisar cada 10 segundos
$lastOutput = 0;

while ((time() - $startTime) < $maxWaitSeconds) {
    $exists = DB::table('publications')->where('id', $testReportId)->exists();
    $elapsed = time() - $startTime;
    
    if ($elapsed - $lastOutput >= 10 || $elapsed === 0) {
        echo "[{$elapsed}s] Estado: " . ($exists ? "✓ EXISTE (sin eliminar)" : "❌ ELIMINADO!") . "\n";
        $lastOutput = $elapsed;
    }
    
    if (!$exists) {
        echo "\n🎉 ¡ÉXITO! El reporte fue eliminado automáticamente\n";
        echo "⏱️ Tiempo total hasta eliminación: {$elapsed} segundos\n";
        break;
    }
    
    sleep($checkInterval);
}

// Paso 4: Restaurar scheduler original
echo "\n📝 Paso 4: Restaurando scheduler a su configuración original...\n";
file_put_contents($consolePath, $consoleContent);
unlink($backupPath);
echo "✓ Scheduler restaurado\n\n";

// Resumen
echo "=== RESUMEN ===\n";
$finalExists = DB::table('publications')->where('id', $testReportId)->exists();
if (!$finalExists) {
    echo "✅ PRUEBA EXITOSA: El sistema de auto-eliminación funciona correctamente\n";
    echo "   El reporte se eliminó automáticamente sin intervención manual\n";
} else {
    echo "⚠️ El reporte aún existe. Verifica que el scheduler esté funcionando.\n";
    echo "   Ejecuta manualmente: php artisan publications:delete-old\n";
}

echo "\n";
?>
