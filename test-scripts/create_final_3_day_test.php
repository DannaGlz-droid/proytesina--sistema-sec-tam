<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validUserId = DB::table('users')->first()->id;
$twoYearsAgo = now()->subYears(2)->subMinutes(1);

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║      CREANDO 3 REPORTES PARA PRUEBA FINAL (3 DÍAS)          ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$testDays = array(
    'Mañana (16/04)',
    'Pasado Mañana (17/04)',
    'Viernes (18/04)'
);

$reports = array();

for ($i = 0; $i < 3; $i++) {
    $day = $testDays[$i];
    
    $report = Publication::create(array(
        'publication_type' => 'TEST-FINAL-' . ($i + 1),
        'topic' => '🟥 PRUEBA FINAL ' . $day . ' - SE ELIMINARÁ A LAS 02:00 AM 🟥',
        'description' => 'Reporte final de prueba #' . ($i + 1) . '. Debe eliminarse automáticamente a las 02:00 AM del ' . $day . '.',
        'publication_date' => $twoYearsAgo->format('Y-m-d'),
        'activity_date' => $twoYearsAgo->format('Y-m-d'),
        'user_id' => $validUserId,
        'status' => 'publicado'
    ));

    DB::table('publications')->where('id', $report->id)->update(array(
        'created_at' => $twoYearsAgo,
        'updated_at' => $twoYearsAgo
    ));

    $reports[$i] = array(
        'id' => $report->id,
        'day' => $day,
        'topic' => $report->topic
    );

    echo "✅ Reporte " . ($i + 1) . " creado\n";
    echo "   ID: " . $report->id . "\n";
    echo "   Día: " . $day . "\n";
    echo "   Tema: " . $report->topic . "\n\n";
}

echo "═════════════════════════════════════════════════════════════════\n\n";
echo "RESUMEN DE PRUEBAS:\n\n";

for ($i = 0; $i < 3; $i++) {
    echo "📌 ID " . $reports[$i]['id'] . " → " . $reports[$i]['day'] . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "PRUEBA AUTOMÁTICA:\n\n";
echo "Cada día a las 02:00 AM, se ejecutará automáticamente:\n\n";
echo "  • " . $reports[0]['day'] . " → Eliminar ID " . $reports[0]['id'] . "\n";
echo "  • " . $reports[1]['day'] . " → Eliminar ID " . $reports[1]['id'] . "\n";
echo "  • " . $reports[2]['day'] . " → Eliminar ID " . $reports[2]['id'] . "\n\n";
echo "Si los 3 desaparecen en sus respectivos días → ✅ SISTEMA 100% FUNCIONAL\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

?>
