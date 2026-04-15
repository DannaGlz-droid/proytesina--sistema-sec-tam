<?php

namespace App\Console\Commands;

use App\Models\Publication;
use Illuminate\Console\Command;

class DeleteOldPublicationsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publications:delete-old-test';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'TESTING: Elimina publicaciones que tengan más de 3 minutos (en lugar de 2 años)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fecha límite: hace 3 minutos (SOLO PARA TESTING)
        $threeMinutesAgo = now()->subMinutes(3);

        // Buscar publicaciones creadas hace más de 3 minutos
        $oldPublications = Publication::where('created_at', '<', $threeMinutesAgo)->get();

        if ($oldPublications->isEmpty()) {
            $this->info('✓ No hay publicaciones antiguas para eliminar.');
            return;
        }

        $count = $oldPublications->count();
        $this->warn("⚠ Se van a eliminar $count publicación(es) de prueba...");

        // Eliminar archivos asociados y la publicación
        foreach ($oldPublications as $publication) {
            $this->line("  • Eliminando: {$publication->topic}");
            
            // Eliminar archivos si existen
            if ($publication->files()->exists()) {
                foreach ($publication->files as $file) {
                    $filePath = storage_path('app/public/' . $file->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $file->delete();
                }
            }

            // Eliminar la publicación
            $publication->delete();
        }

        $this->info("✓ Se eliminaron $count publicación(es) de testing exitosamente.");
    }
}
