<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$report = Publication::withoutTrashed()->find(192);

if ($report) {
    echo "❌ Reporte 192 TODAVÍA EXISTE\n";
} else {
    echo "✅ Reporte 192 FUE ELIMINADO\n";
}

?>
