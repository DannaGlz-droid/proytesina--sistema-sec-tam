<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$twoYearsAgo = now()->subYears(2);

echo "=== DIAGNÓSTICO ===\n\n";
echo "Fecha actual: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Fecha límite (2 años atrás): " . $twoYearsAgo->format('Y-m-d H:i:s') . "\n\n";

// Query con detalles
$query = Publication::where('created_at', '<', $twoYearsAgo);
echo "SQL Query: " . $query->toSql() . "\n";
echo "SQL Bindings: " . json_encode($query->getBindings()) . "\n\n";

$publications = $query->get();
echo "Total publicaciones antiguas encontradas: " . $publications->count() . "\n\n";

// Verificar si existe la publicación 177
echo "Verificando publicación específica ID 177:\n";
$pub177 = Publication::withoutTrashed()->find(177);
if ($pub177) {
    echo "✓ Encontrada: ID {$pub177->id}, Created: {$pub177->created_at}, Deleted at: {$pub177->deleted_at}\n";
} else {
    echo "✗ No encontrada\n";
}
echo "\n";

if ($publications->count() > 0) {
    echo "Publicaciones encontradas:\n";
    foreach ($publications as $p) {
        echo "- ID: {$p->id}, Topic: {$p->topic}, Created: {$p->created_at}, Soft Deleted: " . ($p->deleted_at ? "Sí" : "No") . "\n";
    }
} else {
    echo "No se encontraron publicaciones antiguas.\n\n";
    echo "Verificando primeras publicaciones sin filtro:\n";
    $allPublications = Publication::limit(5)->get();
    foreach ($allPublications as $p) {
        echo "- ID: {$p->id}, Created: {$p->created_at}\n";
    }
}

?>
