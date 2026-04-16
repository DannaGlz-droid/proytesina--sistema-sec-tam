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
    'publication_type' => 'TEST-558PM-FINAL',
    'topic' => '🟥🟥🟥 PRUEBA FINAL 5:58 PM - ÚLTIMO TEST 🟥🟥🟥',
    'description' => 'Este es el último reporte de prueba con .bat. Debe eliminarse automáticamente a las 5:58 PM.',
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
echo "║         ÚLTIMO REPORTE DE PRUEBA CREADO - 5:58 PM           ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
echo "✅ Reporte creado correctamente\n\n";
echo "ID: {$report->id}\n";
echo "Tema: 🟥🟥🟥 PRUEBA FINAL 5:58 PM - ÚLTIMO TEST 🟥🟥🟥\n";
echo "Fecha creación: {$twoYearsAgo->format('Y-m-d H:i:s')}\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "❗ A LAS 5:58 PM ESTE REPORTE DEBE DESAPARECER AUTOMÁTICAMENTE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Si desaparece → ✅ ¡EL SISTEMA FUNCIONA PERFECTAMENTE!\n";
echo "Si no desaparece → ❌ Hay un problema\n\n";

?>
