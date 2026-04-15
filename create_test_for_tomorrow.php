<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validUserId = DB::table('users')->first()?->id ?? 1;
$twoYearsAgo = now()->subYears(2)->subMinutes(1);

$report = Publication::create([
    'publication_type' => 'TEST-ELIMINATION',
    'topic' => '🟥🟥🟥 REPORTE PARA PRUEBA DE AUTO-ELIMINACIÓN 🟥🟥🟥',
    'description' => 'Este reporte fue creado con fecha de hace 2 años y debe ser eliminado automáticamente mañana a las 02:00 AM. Si lo ves desaparecer significa que todo funciona perfectamente.',
    'publication_date' => $twoYearsAgo->format('Y-m-d'),
    'activity_date' => $twoYearsAgo->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'publicado',
]);

// Actualizar fecha en BD para asegurar
DB::table('publications')->where('id', $report->id)->update([
    'created_at' => $twoYearsAgo,
    'updated_at' => $twoYearsAgo,
]);

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║         REPORTE DE PRUEBA CREADO EXITOSAMENTE              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
echo "🔴 INFORMACIÓN IMPORTANTE:\n\n";
echo "ID del Reporte: {$report->id}\n";
echo "Tema: 🟥🟥🟥 REPORTE PARA PRUEBA DE AUTO-ELIMINACIÓN 🟥🟥🟥\n";
echo "Fecha creación: {$twoYearsAgo->format('Y-m-d H:i:s')}\n";
echo "Estado: Cumple criterio de eliminación (>2 años)\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "INSTRUCCIONES PARA MAÑANA:\n\n";
echo "1. Mañana a las 02:00 AM o después, abre:\n";
echo "   http://192.168.50.239:8000/reportes/publicaciones\n\n";
echo "2. Busca este reporte con tema: 🟥🟥🟥 ROJO 🟥🟥🟥\n\n";
echo "3. Si DESAPARECIÓ → ✅ El sistema funciona perfectamente\n";
echo "   Si SIGUE AQUÍ → ❌ Hay un problema\n\n";
echo "4. Si hay problema, ejecuta en terminal:\n";
echo "   php artisan publications:delete-old\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

?>
