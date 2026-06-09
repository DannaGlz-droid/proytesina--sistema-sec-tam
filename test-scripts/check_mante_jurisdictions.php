<?php
/**
 * Script para verificar jurisdicciones y municipios relacionados con "mante"
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Jurisdiction;
use App\Models\Municipality;

echo "========================================\n";
echo "BÚSQUEDA DE JURISDICCIONES Y MUNICIPIOS CON 'MANTE'\n";
echo "========================================\n\n";

// Buscar jurisdicciones que contengan "mante"
echo "JURISDICCIONES:\n";
$jurisdictions = Jurisdiction::where('name', 'LIKE', '%mante%')
    ->orWhere('name', 'LIKE', '%MANTE%')
    ->get();

if ($jurisdictions->count() > 0) {
    foreach ($jurisdictions as $jur) {
        echo "ID: {$jur->id}, Nombre: '{$jur->name}'\n";
    }
} else {
    echo "No se encontraron jurisdicciones con 'mante'\n";
}

echo "\n";

// Buscar municipios que contengan "mante"
echo "MUNICIPIOS:\n";
$municipalities = Municipality::where('name', 'LIKE', '%mante%')
    ->orWhere('name', 'LIKE', '%MANTE%')
    ->with('jurisdiction')
    ->get();

if ($municipalities->count() > 0) {
    foreach ($municipalities as $mun) {
        $jurName = $mun->jurisdiction ? $mun->jurisdiction->name : 'N/A';
        echo "ID: {$mun->id}, Nombre: '{$mun->name}', Jurisdicción: '{$jurName}' (ID: {$mun->jurisdiction_id})\n";
    }
} else {
    echo "No se encontraron municipios con 'mante'\n";
}

echo "\n";

// Lista completa de jurisdicciones
echo "TODAS LAS JURISDICCIONES:\n";
$allJurs = Jurisdiction::all();
foreach ($allJurs as $jur) {
    echo "  - ID: {$jur->id}, Nombre: '{$jur->name}'\n";
}

echo "\n";

// Lista de municipios por jurisdicción
echo "MUNICIPIOS POR JURISDICCIÓN:\n";
foreach ($allJurs as $jur) {
    $count = $jur->municipalities()->count();
    echo "Jurisdicción '{$jur->name}' (ID: {$jur->id}): {$count} municipios\n";
    $muns = $jur->municipalities()->limit(5)->get();
    foreach ($muns as $mun) {
        echo "  - {$mun->name}\n";
    }
    if ($count > 5) {
        echo "  ... y " . ($count - 5) . " más\n";
    }
}
