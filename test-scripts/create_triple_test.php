<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validUserId = DB::table('users')->first()?->id ?? 1;
$twoYearsAgo = now()->subYears(2)->subMinutes(1);

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║         CREANDO 3 REPORTES PARA PRUEBA MÚLTIPLE             ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$times = ['5:32 PM', '5:35 PM', '5:38 PM'];
$reports = [];

foreach ($times as $index => $time) {
    $report = Publication::create([
        'publication_type' => 'TEST-MULTIPLE',
        'topic' => "🟥 PRUEBA $time - SE ELIMINARÁ AUTOMÁTICAMENTE 🟥",
        'description' => "Reporte de prueba que debe eliminarse a las $time automáticamente.",
        'publication_date' => $twoYearsAgo->format('Y-m-d'),
        'activity_date' => $twoYearsAgo->format('Y-m-d'),
        'user_id' => $validUserId,
        'status' => 'publicado',
    ]);

    DB::table('publications')->where('id', $report->id)->update([
        'created_at' => $twoYearsAgo,
        'updated_at' => $twoYearsAgo,
    ]);

    $reports[] = [
        'id' => $report->id,
        'time' => $time,
        'topic' => $report->topic,
    ];

    echo "✅ Reporte " . ($index + 1) . " creado\n";
    echo "   ID: {$report->id}\n";
    echo "   Hora: $time\n";
    echo "   Tema: {$report->topic}\n\n";
}

echo "═════════════════════════════════════════════════════════════════\n\n";
echo "RESUMEN:\n\n";
echo "IDs a eliminar automáticamente:\n";
foreach ($reports as $r) {
    echo "   • ID {$r['id']} a las {$r['time']}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "INSTRUCCIONES:\n\n";
echo "LA TAREA DEBE EJECUTARSE AUTOMÁTICAMENTE A:\n";
echo "  • 5:32 PM → Eliminar ID " . $reports[0]['id'] . "\n";
echo "  • 5:35 PM → Eliminar ID " . $reports[1]['id'] . "\n";
echo "  • 5:38 PM → Eliminar ID " . $reports[2]['id'] . "\n\n";
echo "Si los 3 desaparecen → ✅ SISTEMA FUNCIONA PERFECTAMENTE\n";
echo "Si solo algunos desaparecen → ❌ Hay un problema\n";
echo "Si ninguno desaparece → ❌ La tarea no se ejecuta\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

?>
