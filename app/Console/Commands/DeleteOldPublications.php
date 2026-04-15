<?php

namespace App\Console\Commands;

use App\Models\Publication;
use Illuminate\Console\Command;

class DeleteOldPublications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publications:delete-old';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Elimina publicaciones que tengan más de 2 años sin ser eliminadas manualmente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fecha límite: hace 2 años
        $twoYearsAgo = now()->subYears(2);

        // Buscar publicaciones creadas hace más de 2 años
        $oldPublications = Publication::where('created_at', '<', $twoYearsAgo)->get();

        if ($oldPublications->isEmpty()) {
            $this->info('✓ No hay publicaciones antiguas para eliminar.');
            return;
        }

        $count = $oldPublications->count();

        // Eliminar archivos asociados y la publicación
        foreach ($oldPublications as $publication) {
            // Eliminar archivos si existen
            if ($publication->files()->exists()) {
                foreach ($publication->files as $file) {
                    // Eliminar del sistema de archivos
                    $filePath = storage_path('app/public/' . $file->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Eliminar del registro en BD
                    $file->delete();
                }
            }

            // Eliminar la publicación
            $publication->delete();
        }

        $this->info("✓ Se eliminaron $count publicación(es) con más de 2 años.");
    }
}
