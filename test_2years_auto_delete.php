<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║     TESTING ELIMINACIÓN AUTOMÁTICA - ESPERA 2 AÑOS + 3 MIN     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Paso 1: Crear reporte que será eliminado en 3 minutos
$pub = Publication::first();
if($pub) {
  // Fecha: Hace 2 años menos 3 minutos
  $createdDate = now()->subYears(2)->addMinutes(3);
  
  $copy = $pub->replicate();
  $copy->topic = "🔴 TESTING AUTO-DELETE: Será eliminado en ~3 minutos";
  $copy->created_at = $createdDate;
  $copy->updated_at = $createdDate;
  $copy->save();
  
  $reportId = $copy->id;
  
  echo "✓ Reporte de testing creado:\n";
  echo "  ID: $reportId\n";
  echo "  Título: {$copy->topic}\n";
  echo "  Fecha creación: " . $copy->created_at->format('d-m-Y H:i:s') . "\n";
  echo "  Fecha límite para eliminación: " . now()->subYears(2)->format('d-m-Y H:i:s') . "\n\n";

  // Calcular tiempo restante
  $deleteTime = $copy->created_at->addYears(2);
  $waitSeconds = $deleteTime->diffInSeconds(now());
  
  echo "⏱  Tiempo hasta eliminación automática: ~" . ceil($waitSeconds/60) . " minutos\n\n";

  echo "╔════════════════════════════════════════════════════════════════╗\n";
  echo "║              VERIFICA CADA 30 SEGUNDOS ABAJO                   ║\n";
  echo "╚════════════════════════════════════════════════════════════════╝\n\n";

  // Verificar cada 30 segundos durante 4 minutos
  $maxIterations = 8; // 30 seg x 8 = 4 minutos
  $iteration = 0;

  while ($iteration < $maxIterations) {
    $exists = Publication::where('id', $reportId)->first();
    $time = now()->format('H:i:s');
    
    if ($exists) {
      echo "[$time] ⏳ Reporte aún existe (esperando...)\n";
    } else {
      echo "[$time] ✅ ¡ELIMINADO! El sistema automático funcionó correctamente\n";
      break;
    }

    // Esperar 30 segundos
    if ($iteration < $maxIterations - 1) {
      sleep(30);
    }
    $iteration++;
  }

  // Verificación final
  echo "\n╔════════════════════════════════════════════════════════════════╗\n";
  $finalExists = Publication::where('id', $reportId)->first();
  if(!$finalExists) {
    echo "║                      ✅ ÉXITO CONFIRMADO                       ║\n";
    echo "║  El reporte fue eliminado automáticamente después de 2 años    ║\n";
  } else {
    echo "║                     ❌ FALLO EN ELIMINACIÓN                    ║\n";
    echo "║      El reporte aún existe (revisar scheduler config)          ║\n";
  }
  echo "╚════════════════════════════════════════════════════════════════╝\n\n";

} else {
  echo "✗ No hay reportes para duplicar\n";
}
