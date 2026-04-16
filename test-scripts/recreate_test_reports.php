<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validUserId = DB::table('users')->first()->id;

echo "\n✅ Creando 3 reportes con fechas DIFERENTES para eliminar en días diferentes\n\n";

// Crear reportes con fechas backedatadas pero en diferentes días
for ($i = 0; $i < 3; $i++) {
    // Cada reporte está 2 años atrás + diferente cantidad de días
    $createdDate = now()->subYears(2)->addDays($i);
    
    $report = Publication::create(array(
        'publication_type' => 'TEST-FINAL-' . ($i + 1),
        'topic' => 'PRUEBA FINAL ' . ($i + 1) . ' - Se eliminará automáticamente día ' . ($i + 1),
        'description' => 'Reporte de prueba #' . ($i + 1) . ' para eliminar en día ' . ($i + 1),
        'publication_date' => $createdDate->format('Y-m-d'),
        'activity_date' => $createdDate->format('Y-m-d'),
        'user_id' => $validUserId,
        'status' => 'publicado'
    ));

    DB::table('publications')->where('id', $report->id)->update(array(
        'created_at' => $createdDate,
        'updated_at' => $createdDate
    ));

    echo "ID " . $report->id . " - Creado con fecha: " . $createdDate->format('Y-m-d H:i:s') . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "PRUEBA: Se eliminarán en DÍAS DIFERENTES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ID 202 → Se elimina MAÑANA (16/04) a las 2 AM\n";
echo "ID 203 → Se elimina PASADO MAÑANA (17/04) a las 2 AM\n";
echo "ID 204 → Se elimina VIERNES (18/04) a las 2 AM\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
?>
