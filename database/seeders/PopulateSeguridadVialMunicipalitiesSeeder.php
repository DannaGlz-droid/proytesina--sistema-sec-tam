<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoadSafetyReport;
use App\Models\Municipality;
use App\Models\Publication;

class PopulateSeguridadVialMunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer municipio disponible como valor por defecto
        $defaultMunicipality = Municipality::first();
        
        if (!$defaultMunicipality) {
            $this->command->warn('No hay municipios disponibles. Por favor, crea municipios primero.');
            return;
        }

        $defaultJurisdictionId = $defaultMunicipality->jurisdiction_id;
        
        // Buscar todos los RoadSafetyReports que tengan municipio_id NULL
        $reportsToUpdate = RoadSafetyReport::whereNull('municipality_id')
            ->orWhereNull('jurisdiction_id')
            ->get();

        if ($reportsToUpdate->isEmpty()) {
            $this->command->info('No hay reportes de Seguridad Vial sin municipio/jurisdicción.');
            return;
        }

        // Actualizar cada reporte con los valores por defecto
        foreach ($reportsToUpdate as $report) {
            // Si el reporte no tiene municipio, asignar el por defecto
            if (is_null($report->municipality_id)) {
                $report->municipality_id = $defaultMunicipality->id;
            }
            
            // Si el reporte no tiene jurisdicción, asignar la del municipio (o la por defecto)
            if (is_null($report->jurisdiction_id)) {
                if ($report->municipality_id) {
                    $municipality = Municipality::find($report->municipality_id);
                    $report->jurisdiction_id = $municipality?->jurisdiction_id ?? $defaultJurisdictionId;
                } else {
                    $report->jurisdiction_id = $defaultJurisdictionId;
                }
            }
            
            $report->save();
        }

        $this->command->info("Se han actualizado {$reportsToUpdate->count()} reportes de Seguridad Vial con municipio y jurisdicción.");
    }
}
