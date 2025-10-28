<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Death;
use Illuminate\Support\Facades\DB;

class NormalizePersonNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --dry-run  : show changes without saving
     * --chunk=N  : number of records per chunk
     */
    protected $signature = 'names:normalize {--dry-run} {--chunk=500}';

    /**
     * The console command description.
     */
    protected $description = 'Normalize person name fields in deaths table (Title Case). Use --dry-run to preview.';

    public function handle()
    {
        $dry = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');
        $this->info('Starting normalization of Death names. Dry-run: ' . ($dry ? 'yes' : 'no'));

        $updated = 0;
        Death::query()->orderBy('id')->chunkById($chunk, function ($rows) use (&$updated, $dry) {
            foreach ($rows as $d) {
                $before = [$d->name, $d->first_last_name, $d->second_last_name];
                $after = [Death::formatPersonName($d->name), Death::formatPersonName($d->first_last_name), Death::formatPersonName($d->second_last_name)];
                if ($before !== $after) {
                    if ($dry) {
                        $this->line("[DRY] id={$d->id} => before=" . implode(' | ', $before) . " => after=" . implode(' | ', $after));
                    } else {
                        $d->name = $after[0];
                        $d->first_last_name = $after[1];
                        $d->second_last_name = $after[2];
                        $d->save();
                        $updated++;
                        $this->line("Updated id={$d->id}");
                    }
                }
            }
        });

        $this->info('Done. Updated: ' . $updated . ' records.');
        return 0;
    }
}
