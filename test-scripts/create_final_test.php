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
    'publication_type' => 'TEST-520PM',
    'topic' => '⏰⏰⏰ PRUEBA FINAL 5:20 PM - SE DEBE ELIMINAR AUTOMÁTICAMENTE ⏰⏰⏰',
    'description' => 'Este es el reporte final de prueba. Debe desaparecer automáticamente a las 5:20 PM sin intervención manual.',
    'publication_date' => $twoYearsAgo->format('Y-m-d'),
    'activity_date' => $twoYearsAgo->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'publicado',
]);

DB::table('publications')->where('id', $report->id)->update([
    'created_at' => $twoYearsAgo,
    'updated_at' => $twoYearsAgo,
]);

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║         REPORTE FINAL CREADO PARA PRUEBA DE 5:20 PM         ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
echo "✅ Reporte creado correctamente\n\n";
echo "ID: {$report->id}\n";
echo "Tema: ⏰⏰⏰ PRUEBA FINAL 5:20 PM - SE DEBE ELIMINAR AUTOMÁTICAMENTE ⏰⏰⏰\n";
echo "Fecha creación: {$twoYearsAgo->format('Y-m-d H:i:s')}\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "❗ A LAS 5:20 PM ESTE REPORTE DEBE DESAPARECER AUTOMÁTICAMENTE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nSi desaparece → ✅ El sistema funciona perfectamente\n";
echo "Si permanece → ❌ Hay un error\n\n";

?>
