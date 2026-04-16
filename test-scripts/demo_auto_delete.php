<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREANDO REPORTE VISIBLE PARA PRUEBA ===\n\n";

// Obtener usuario válido
$validUserId = \Illuminate\Support\Facades\DB::table('users')->first()?->id ?? 1;
echo "Usuario: $validUserId\n";

// Crear DOS reportes: uno ANTIGUO que se eliminará y uno NUEVO que permanecerá
$twoYearsAgo = now()->subYears(2)->subMinutes(1);
$today = now();

echo "\n📝 Reporte 1 (SE ELIMINARÁ):\n";
$oldReport = Publication::create([
    'publication_type' => 'TEST-ELIMINACION',
    'topic' => '🔴🔴🔴 ELIMINAR ESTE - Hace 2 años 🔴🔴🔴',
    'description' => 'Este reporte tiene fecha de hace 2 años y será eliminado automáticamente',
    'publication_date' => $twoYearsAgo->format('Y-m-d'),
    'activity_date' => $twoYearsAgo->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'publicado',
]);

// Actualizar fechas directamente en BD
\Illuminate\Support\Facades\DB::table('publications')->where('id', $oldReport->id)->update([
    'created_at' => $twoYearsAgo,
    'updated_at' => $twoYearsAgo,
]);

echo "✓ ID: {$oldReport->id}\n";
echo "✓ Tema: {$oldReport->topic}\n";
echo "✓ Fecha: {$twoYearsAgo->format('Y-m-d H:i:s')}\n";

echo "\n📝 Reporte 2 (PERMANECERÁ):\n";
$newReport = Publication::create([
    'publication_type' => 'TEST-MANTENER',
    'topic' => '✅✅ MANTENER ESTE - Creado hoy ✅✅',
    'description' => 'Este reporte es reciente y NO será eliminado',
    'publication_date' => $today->format('Y-m-d'),
    'activity_date' => $today->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'publicado',
]);

echo "✓ ID: {$newReport->id}\n";
echo "✓ Tema: {$newReport->topic}\n";
echo "✓ Fecha: " . $today->format('Y-m-d H:i:s') . "\n";

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "INSTRUCCIONES:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "1. Abre: http://192.168.50.239:8000/reportes/publicaciones\n";
echo "2. Deberías ver AMBOS reportes en la lista\n";
echo "3. Ejecuta: php artisan publications:delete-old\n";
echo "4. Refresca la página\n";
echo "5. Verifica que EL ROJO fue eliminado (ID {$oldReport->id})\n";
echo "6. Verifica que EL VERDE seguidor siendo visible (ID {$newReport->id})\n";
echo "═══════════════════════════════════════════════════════════\n";

?>
