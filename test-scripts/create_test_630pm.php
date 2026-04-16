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
    'publication_type' => 'TEST-630PM',
    'topic' => '🟥🟥🟥 PRUEBA 6:30 PM - NUEVO TEST 🟥🟥🟥',
    'description' => 'Este reporte debe eliminarse automáticamente a las 6:30 PM con la nueva configuración del scheduler.',
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
echo "║         REPORTE CREADO PARA PRUEBA DE 6:30 PM              ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
echo "✅ Reporte creado correctamente\n\n";
echo "ID: {$report->id}\n";
echo "Tema: 🟥🟥🟥 PRUEBA 6:30 PM - NUEVO TEST 🟥🟥🟥\n";
echo "Fecha creación: {$twoYearsAgo->format('Y-m-d H:i:s')}\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "❗ A LAS 6:30 PM ESTE REPORTE DEBE DESAPARECER AUTOMÁTICAMENTE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Si desaparece → ✅ ¡EL SISTEMA FUNCIONA PERFECTAMENTE!\n";
echo "Si no desaparece → ❌ Hay un problema\n\n";

?>
