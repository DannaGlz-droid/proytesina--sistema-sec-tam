<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DE ELIMINACIÓN ===\n\n";

// Verificar si la publicación 179 existe
$exists = Publication::withoutTrashed()->find(179);

if ($exists) {
    echo "❌ ERROR: La publicación 179 TODAVÍA EXISTE en la BD\n";
    echo "ID: {$exists->id}, Created: {$exists->created_at}\n";
} else {
    echo "✅ ÉXITO: La publicación 179 fue ELIMINADA CORRECTAMENTE\n";
    echo "El sistema de auto-eliminación funciona perfectamente!\n";
}

?>
