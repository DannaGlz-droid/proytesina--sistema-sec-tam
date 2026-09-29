<?php

namespace App\Console\Commands;

use App\Services\MunicipalityPopulationImportService;
use Illuminate\Console\Command;

class ImportMunicipalityPopulations extends Command
{
    protected $signature = 'populations:import
        {file : Ruta del XLSX oficial de CONAPO}
        {--source-version= : Versión o edición de la fuente}
        {--published-at= : Fecha de publicación YYYY-MM-DD}';

    protected $description = 'Importa población municipal de Tamaulipas para calcular tasas estadísticas';

    public function handle(MunicipalityPopulationImportService $importer): int
    {
        try {
            $result = $importer->import((string) $this->argument('file'), array_filter([
                'version' => $this->option('source-version'),
                'published_at' => $this->option('published-at'),
            ]));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->info("Importación completa: {$result['municipalities']} municipios, {$result['records']} denominadores ({$result['year_min']}-{$result['year_max']}).");
        return self::SUCCESS;
    }
}
