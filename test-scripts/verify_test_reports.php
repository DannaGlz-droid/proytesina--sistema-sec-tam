<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Publication;

$reports = Publication::whereIn('id', [195, 196, 197])->get();
echo "\nVerifying test reports:\n";
echo "Total reports found: " . count($reports) . "\n";

foreach($reports as $r) {
    echo "✓ ID: " . $r->id . " | Status: " . $r->status . " | Created: " . $r->created_at . "\n";
}
echo "\n";
?>
