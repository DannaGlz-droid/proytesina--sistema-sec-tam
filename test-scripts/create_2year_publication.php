<?php
require 'bootstrap/app.php';

$app = app();
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$publication = new App\Models\Publication();
$publication->title = 'TEST 2 AÑOS - SERÁ ELIMINADO';
$publication->description = 'Reporte de prueba creado hace 2 años para testear auto-delete';
$publication->created_at = now()->subYears(2);
$publication->updated_at = now()->subYears(2);
$publication->save();

echo "✓ Creada publicación de 2 años atrás (ID: {$publication->id}, fecha: {$publication->created_at})\n";
