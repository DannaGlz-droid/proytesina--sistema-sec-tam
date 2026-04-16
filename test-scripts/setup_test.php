<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Crear reporte base con SQL directo
DB::insert('INSERT INTO publications (topic, content, publication_type, status, user_id, publication_date, activity_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
    'Base Reporte Para Testing',
    'Contenido de prueba',
    'seguridad_vial',
    'aprobado',
    1,
    now(),
    now(),
    now(),
    now()
]);

echo "✓ Reporte base creado\n";
