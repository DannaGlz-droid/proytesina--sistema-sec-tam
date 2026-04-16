<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

$pub = Publication::first();
if($pub) {
  $copy = $pub->replicate();
  $copy->topic = "REPORTE ANTIGUO PARA ELIMINAR (Creado hace 2+ años)";
  $copy->created_at = now()->subYears(2)->subDays(1);
  $copy->updated_at = now()->subYears(2)->subDays(1);
  $copy->save();
  
  echo "✓ Reporte antiguo creado con fecha: " . $copy->created_at->format('d-m-Y H:i:s') . "\n";
  echo "Ahora ejecuta: php artisan publications:delete-old\n";
} else {
  echo "✗ No hay reportes para duplicar\n";
}
