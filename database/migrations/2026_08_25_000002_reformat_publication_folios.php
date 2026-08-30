<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateFolios(fn (object $publication, string $year, string $typeCode): string => sprintf(
            'EXP-%s-%04d-%s',
            $year,
            $publication->id,
            $typeCode,
        ));
    }

    public function down(): void
    {
        $this->updateFolios(fn (object $publication, string $year, string $typeCode): string => sprintf(
            'EXP-%s-%s-%06d',
            $year,
            $typeCode,
            $publication->id,
        ));
    }

    private function updateFolios(callable $format): void
    {
        DB::table('publications')
            ->select(['id', 'publication_type', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($publications) use ($format): void {
                foreach ($publications as $publication) {
                    $year = substr((string) $publication->created_at, 0, 4) ?: date('Y');
                    $typeCode = match ($publication->publication_type) {
                        'seguridad_vial' => 'SV',
                        'observatorio' => 'OBS',
                        'alcoholimetria' => 'ALC',
                        'grupos-vulnerables', 'grupos_vulnerables' => 'GV',
                        default => 'GEN',
                    };

                    DB::table('publications')
                        ->where('id', $publication->id)
                        ->update(['folio' => $format($publication, $year, $typeCode)]);
                }
            });
    }
};
