<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consolidate repeated catalog entries and prevent them from returning.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $canonicalByName = [];

            foreach (DB::table('activity_types')->orderBy('id')->get(['id', 'name']) as $activityType) {
                $normalizedName = preg_replace('/\s+/u', ' ', trim((string) $activityType->name));
                $key = mb_strtolower($normalizedName, 'UTF-8');

                if (! isset($canonicalByName[$key])) {
                    $canonicalByName[$key] = (int) $activityType->id;

                    if ($normalizedName !== $activityType->name) {
                        DB::table('activity_types')->where('id', $activityType->id)->update([
                            'name' => $normalizedName,
                            'updated_at' => now(),
                        ]);
                    }

                    continue;
                }

                $canonicalId = $canonicalByName[$key];

                foreach (['road_safety_reports', 'grupos_vulnerables_reports'] as $reportTable) {
                    if (Schema::hasTable($reportTable) && Schema::hasColumn($reportTable, 'activity_type_id')) {
                        DB::table($reportTable)
                            ->where('activity_type_id', $activityType->id)
                            ->update(['activity_type_id' => $canonicalId]);
                    }
                }

                DB::table('activity_types')->where('id', $activityType->id)->delete();
            }
        });

        Schema::table('activity_types', function (Blueprint $table): void {
            $table->unique('name', 'activity_types_name_unique');
        });
    }

    /**
     * Keep consolidated data while allowing the uniqueness constraint to be rolled back.
     */
    public function down(): void
    {
        Schema::table('activity_types', function (Blueprint $table): void {
            $table->dropUnique('activity_types_name_unique');
        });
    }
};
