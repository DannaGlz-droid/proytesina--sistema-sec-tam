<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Eliminar los reportes que están sin usar (199, 200, 201)
DB::table('publications')->whereIn('id', [199, 200, 201])->delete();

echo "Reportes 199, 200, 201 eliminados\n";
?>
