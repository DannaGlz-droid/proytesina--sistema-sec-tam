<?php
// Script para actualizar reportes con distritos reales

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Publication;
use App\Models\District;
use App\Models\RoadSafetyReport;
use App\Models\InjuryObservatoryReport;
use App\Models\GruposVulnerablesReport;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Get all districts
$districts = District::where('id', '!=', 999)->get();
echo "Distritos disponibles:\n";
foreach ($districts as $district) {
    echo "- ID: {$district->id}, Nombre: {$district->name}\n";
}

if ($districts->isEmpty()) {
    echo "\nNo hay distritos disponibles.\n";
    exit(1);
}

// Update Road Safety Reports
echo "\n=== Actualizando Road Safety Reports ===\n";
$roadSafetyReports = RoadSafetyReport::whereNull('district_id')->get();
$districtIndex = 0;
foreach ($roadSafetyReports as $report) {
    $district = $districts[$districtIndex % $districts->count()];
    $report->update(['district_id' => $district->id]);
    echo "Actualizado Road Safety Report ID {$report->id} con Distrito: {$district->name}\n";
    $districtIndex++;
}

// Update Injury Observatory Reports
echo "\n=== Actualizando Injury Observatory Reports ===\n";
$observatoryReports = InjuryObservatoryReport::whereNull('district_id')->get();
$districtIndex = 0;
foreach ($observatoryReports as $report) {
    $district = $districts[$districtIndex % $districts->count()];
    $report->update(['district_id' => $district->id]);
    echo "Actualizado Injury Observatory Report ID {$report->id} con Distrito: {$district->name}\n";
    $districtIndex++;
}

// Update Grupos Vulnerables Reports
echo "\n=== Actualizando Grupos Vulnerables Reports ===\n";
$gruposReports = GruposVulnerablesReport::whereNull('district_id')->get();
$districtIndex = 0;
foreach ($gruposReports as $report) {
    $district = $districts[$districtIndex % $districts->count()];
    $report->update(['district_id' => $district->id]);
    echo "Actualizado Grupos Vulnerables Report ID {$report->id} con Distrito: {$district->name}\n";
    $districtIndex++;
}

echo "\n✅ Actualización completada\n";
?>
