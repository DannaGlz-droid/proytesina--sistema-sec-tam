<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Jurisdiction;

$jur = Jurisdiction::where('name', 'TEST_JUR')->first();
if (! $jur) {
    echo "TEST_JUR not found\n";
    exit(0);
}
$jurId = $jur->id;
echo "Found TEST_JUR id=$jurId\n";

$tables = DB::select("SELECT TABLE_NAME FROM information_schema.columns WHERE COLUMN_NAME='jurisdiction_id' AND TABLE_SCHEMA=DATABASE()");
if (!$tables) {
    echo "No tables with column jurisdiction_id found\n";
    exit(0);
}

foreach ($tables as $t) {
    $tbl = $t->TABLE_NAME;
    try {
        $cnt = DB::selectOne("SELECT COUNT(*) AS c FROM `$tbl` WHERE jurisdiction_id = ?", [$jurId]);
        $c = $cnt->c ?? 0;
        echo "Table: $tbl - count: $c\n";
        if ($c > 0) {
            $rows = DB::select("SELECT id FROM `$tbl` WHERE jurisdiction_id = ? LIMIT 100", [$jurId]);
            $ids = array_map(fn($r) => $r->id, $rows);
            echo "  ids: ".implode(',', $ids)."\n";
        }
    } catch (\Throwable $e) {
        echo "  Error querying $tbl: " . $e->getMessage() . "\n";
    }
}

echo "Done\n";
