<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREANDO PUBLICACIÓN DE PRUEBA ===\n\n";

// Obtener usuario válido
$validUserId = DB::table('users')->first()?->id ?? 1;
echo "Usuario ID seleccionado: $validUserId\n";

// Crear fecha hace 2 años y 1 minuto
$twoYearsAgo = now()->subYears(2)->subMinutes(1);
echo "Fecha a usar: " . $twoYearsAgo->format('Y-m-d H:i:s') . "\n\n";

try {
    // Crear publicación usando modelo - pero bypaseando timestamps automáticos
    $publication = new Publication([
        'publication_type' => 'AUTO-DELETE-TEST',
        'topic' => '🔴 PRUEBA AUTOMÁTICA ' . date('Y-m-d H:i:s'),
        'description' => 'Reporte de prueba para verificar auto-eliminación',
        'publication_date' => $twoYearsAgo->format('Y-m-d'),
        'activity_date' => $twoYearsAgo->format('Y-m-d'),
        'user_id' => $validUserId,
        'status' => 'borrador',
    ]);
    
    // Asignar fechas manualmente después
    $publication->created_at = $twoYearsAgo;
    $publication->updated_at = $twoYearsAgo;
    
    // Guardar sin actualizar timestamps
    $publication->saveQuietly();
    
    echo "✅ PUBLICACIÓN CREADA EXITOSAMENTE\n";
    echo "ID: {$publication->id}\n";
    echo "Fecha guardada (intended): " . $twoYearsAgo->format('Y-m-d H:i:s') . "\n\n";
    
    // Verificar que existe
    $verify = Publication::find($publication->id);
    if ($verify) {
        echo "✅ VERIFICACIÓN: El reporte existe en la BD\n";
        echo "ID: {$verify->id}, Created: {$verify->created_at}\n\n";
    } else {
        echo "❌ ERROR: El reporte NO existe en la BD después de crearlo\n\n";
    }
    
    // Mostrar próximos pasos
    echo "PRÓXIMOS PASOS:\n";
    echo "1. Ejecuta: php artisan publications:delete-old\n";
    echo "2. Si funciona, el reporte debe ser eliminado\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR AL CREAR PUBLICACIÓN:\n";
    echo $e->getMessage() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
