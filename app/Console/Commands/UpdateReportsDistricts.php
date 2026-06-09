<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\District;
use App\Models\RoadSafetyReport;
use App\Models\InjuryObservatoryReport;
use App\Models\GruposVulnerablesReport;

class UpdateReportsDistricts extends Command
{
    protected $signature = 'reports:update-districts';
    protected $description = 'Actualiza los distritos de los reportes existentes';

    public function handle()
    {
        // Get all districts
        $districts = District::where('id', '!=', 999)->get();
        
        if ($districts->isEmpty()) {
            $this->error('No hay distritos disponibles.');
            return 1;
        }

        $this->info('Distritos disponibles:');
        foreach ($districts as $district) {
            $this->line("  - ID: {$district->id}, Nombre: {$district->name}");
        }

        // Update Road Safety Reports
        $this->info("\n=== Actualizando Road Safety Reports ===");
        $roadSafetyReports = RoadSafetyReport::all();
        $districtIndex = 0;
        $updated = 0;
        foreach ($roadSafetyReports as $report) {
            $district = $districts[$districtIndex % $districts->count()];
            if ($report->district_id !== $district->id) {
                $report->update(['district_id' => $district->id]);
                $updated++;
                $this->line("✓ Road Safety Report ID {$report->id} → Distrito: {$district->name}");
            }
            $districtIndex++;
        }
        $this->info("Actualizados: {$updated} reportes");

        // Update Injury Observatory Reports
        $this->info("\n=== Actualizando Injury Observatory Reports ===");
        $observatoryReports = InjuryObservatoryReport::all();
        $districtIndex = 0;
        $updated = 0;
        foreach ($observatoryReports as $report) {
            $district = $districts[$districtIndex % $districts->count()];
            if ($report->district_id !== $district->id) {
                $report->update(['district_id' => $district->id]);
                $updated++;
                $this->line("✓ Injury Observatory Report ID {$report->id} → Distrito: {$district->name}");
            }
            $districtIndex++;
        }
        $this->info("Actualizados: {$updated} reportes");

        // Update Grupos Vulnerables Reports
        $this->info("\n=== Actualizando Grupos Vulnerables Reports ===");
        $gruposReports = GruposVulnerablesReport::all();
        $districtIndex = 0;
        $updated = 0;
        foreach ($gruposReports as $report) {
            $district = $districts[$districtIndex % $districts->count()];
            if ($report->district_id !== $district->id) {
                $report->update(['district_id' => $district->id]);
                $updated++;
                $this->line("✓ Grupos Vulnerables Report ID {$report->id} → Distrito: {$district->name}");
            }
            $districtIndex++;
        }
        $this->info("Actualizados: {$updated} reportes");

        $this->info("\n✅ Actualización completada");
        return 0;
    }
}
