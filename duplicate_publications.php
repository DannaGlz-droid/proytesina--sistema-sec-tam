<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

$pub = Publication::first();
if($pub) {
  for($i = 0; $i < 120; $i++) {
    $copy = $pub->replicate();
    $copy->topic = $pub->topic . ' (Copia ' . ($i+1) . ')';
    $copy->save();
  }
  echo "✓ 120 reportes creados exitosamente\n";
} else {
  echo "✗ No hay reportes para duplicar\n";
}
