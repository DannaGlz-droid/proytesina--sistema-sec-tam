<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table): void {
            $table->string('folio', 32)->nullable()->unique()->after('id');
        });

        DB::table('publications')
            ->select(['id', 'publication_type', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($publications): void {
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
                        ->update([
                            'folio' => sprintf('EXP-%s-%s-%06d', $year, $typeCode, $publication->id),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table): void {
            $table->dropUnique(['folio']);
            $table->dropColumn('folio');
        });
    }
};
