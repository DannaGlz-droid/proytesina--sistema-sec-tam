<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

echo "Creando reporte base...\n";

// Crear un reporte base simple
Publication::create([
  'topic' => 'Reporte Base Para Testing',
  'content' => 'Contenido temporal',
  'publication_type' => 'seguridad_vial',
  'status' => 'aprobado',
  'user_id' => 1,
  'publication_date' => now(),
]);

echo "✓ Reporte base creado\n";
