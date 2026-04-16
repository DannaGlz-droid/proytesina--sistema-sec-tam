<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

echo "\n========== TESTING ELIMINACIÓN AUTOMÁTICA DE REPORTES ==========\n\n";

// Paso 1: Crear reporte antiguo (3 minutos atrás)
$pub = Publication::first();
if($pub) {
  $copy = $pub->replicate();
  $copy->topic = "🔴 REPORTE PARA TESTING - CREADO HACE 3 MINUTOS";
  $copy->created_at = now()->subMinutes(3)->subSeconds(5);
  $copy->updated_at = now()->subMinutes(3)->subSeconds(5);
  $copy->save();
  
  echo "✓ Paso 1: Reporte de testing creado\n";
  echo "  ID: {$copy->id}\n";
  echo "  Título: {$copy->topic}\n";
  echo "  Fecha: " . $copy->created_at->format('d-m-Y H:i:s') . "\n";
  echo "  (Esto simula que fue creado hace 3 minutos)\n\n";

  // Paso 2: Verificar que existe
  $exists = Publication::where('id', $copy->id)->first();
  if($exists) {
    echo "✓ Paso 2: Reporte verificado en base de datos ✓\n\n";
  }

  // Paso 3: Instrucciones
  echo "═══════════════════════════════════════════════════════════════\n";
  echo "Ahora ejecuta en otra terminal:\n\n";
  echo "  php artisan publications:delete-old-test\n\n";
  echo "═══════════════════════════════════════════════════════════════\n\n";
  
  // Esperar a que el usuario ejecute el comando
  echo "⏳ Esperando que ejecutes el comando...\n";
  echo "Presiona ENTER aquí cuando hayas corrido el comando arriba:\n";
  $input = trim(fgets(STDIN));

  // Paso 4: Verificar si se eliminó
  $stillExists = Publication::where('id', $copy->id)->first();
  if(!$stillExists) {
    echo "\n✅ ¡ÉXITO! El reporte fue eliminado automáticamente\n";
    echo "El sistema de auto-eliminación funciona correctamente.\n";
  } else {
    echo "\n❌ ERROR: El reporte sigue en la BD\n";
    echo "Algo no funcionó en el proceso de eliminación.\n";
  }

} else {
  echo "✗ No hay reportes para duplicar\n";
}

echo "\n";
