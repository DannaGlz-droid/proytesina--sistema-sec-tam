<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->foreignId('death_district_id')
                ->nullable()
                ->after('death_municipality_id')
                ->constrained('districts')
                ->nullOnDelete();
        });

        DB::table('deaths')
            ->whereNotNull('death_municipality_id')
            ->orderBy('id')
            ->chunkById(500, function ($deaths): void {
                $districtsByMunicipality = DB::table('municipalities')
                    ->whereIn('id', $deaths->pluck('death_municipality_id')->filter()->unique())
                    ->pluck('district_id', 'id');

                foreach ($deaths as $death) {
                    $districtId = $districtsByMunicipality[$death->death_municipality_id] ?? null;

                    if ($districtId) {
                        DB::table('deaths')
                            ->where('id', $death->id)
                            ->update(['death_district_id' => $districtId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->dropConstrainedForeignId('death_district_id');
        });
    }
};
