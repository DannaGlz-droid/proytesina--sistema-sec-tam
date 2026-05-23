<?php
// Script para listar municipios y conteos por jurisdicción
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $jurisdictions = DB::table('jurisdictions')->select('id','name')->orderBy('id')->get();
    if ($jurisdictions->isEmpty()) {
        echo "No se encontraron jurisdicciones.\n";
        exit(0);
    }

    foreach ($jurisdictions as $jur) {
        echo "---- Jurisdicción {$jur->id} - {$jur->name} ----\n";
        $muns = DB::table('municipalities')->where('jurisdiction_id', $jur->id)->orderBy('name')->get(['id','name']);
        if ($muns->isEmpty()) {
            echo "  (No hay municipios asociados)\n";
            continue;
        }
        foreach ($muns as $m) {
            $count = DB::table('deaths')->where('death_municipality_id', $m->id)->count();
            echo "  - {$m->name}: {$count}\n";
        }
        // Also show municipalities that appear in deaths with a different jurisdiction_id (possible data mismatch)
        $otherMuns = DB::table('deaths')
            ->leftJoin('municipalities','municipalities.id','=','deaths.death_municipality_id')
            ->where('deaths.jurisdiction_id','<>',$jur->id)
            ->whereNotNull('deaths.death_municipality_id')
            ->whereIn('deaths.death_municipality_id', function($q) use ($jur) {
                $q->select('id')->from('municipalities')->where('jurisdiction_id', $jur->id);
            })
            ->select('municipalities.id','municipalities.name', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('municipalities.id','municipalities.name')
            ->get();
        if (!$otherMuns->isEmpty()) {
            echo "  ---- Atención: hay registros en 'deaths' cuyo 'jurisdiction_id' NO coincide con la jurisdicción del municipio (posible inconsistencia):\n";
            foreach ($otherMuns as $om) {
                echo "    * {$om->name} (ID {$om->id}): {$om->total} registros con jurisdiction_id distinto\n";
            }
        }

        echo "\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
