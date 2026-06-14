<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyTables = [
            'municipalities',
            'users',
            'deaths',
            'road_safety_reports',
            'injury_observatory_reports',
            'grupos_vulnerables_reports',
        ];

        $hasLegacyColumns = collect($legacyTables)->contains(
            fn (string $table) => Schema::hasTable($table)
                && Schema::hasColumn($table, 'jurisdiction_id')
        );

        if (! Schema::hasTable('jurisdictions') && ! $hasLegacyColumns) {
            return;
        }

        // Step 1: Create districts table
        if (!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
            });

            // Copy existing data from jurisdictions if it exists
            if (Schema::hasTable('jurisdictions')) {
                DB::statement('INSERT INTO districts (id, name, created_at, updated_at) SELECT id, name, created_at, updated_at FROM jurisdictions');
            }
        }

        // Step 2: Add district_id columns to all tables if they don't exist
        Schema::table('municipalities', function (Blueprint $table) {
            if (!Schema::hasColumn('municipalities', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('id')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('position_id')->nullable();
            }
        });

        Schema::table('deaths', function (Blueprint $table) {
            if (!Schema::hasColumn('deaths', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('residence_municipality_id')->nullable();
            }
        });

        Schema::table('road_safety_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('road_safety_reports', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('municipality_id')->nullable();
            }
        });

        Schema::table('injury_observatory_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('injury_observatory_reports', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('municipality_id')->nullable();
            }
        });

        Schema::table('grupos_vulnerables_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('grupos_vulnerables_reports', 'district_id')) {
                $table->unsignedBigInteger('district_id')->after('municipality_id')->nullable();
            }
        });

        // Step 3: Copy data from jurisdiction_id to district_id
        DB::statement('UPDATE municipalities SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');
        DB::statement('UPDATE users SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');
        DB::statement('UPDATE deaths SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');
        DB::statement('UPDATE road_safety_reports SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');
        DB::statement('UPDATE injury_observatory_reports SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');
        DB::statement('UPDATE grupos_vulnerables_reports SET district_id = jurisdiction_id WHERE district_id IS NULL AND jurisdiction_id IS NOT NULL');

        // Step 4: Add foreign keys for district_id
        Schema::table('municipalities', function (Blueprint $table) {
            if (Schema::hasColumn('municipalities', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });

        Schema::table('deaths', function (Blueprint $table) {
            if (Schema::hasColumn('deaths', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });

        Schema::table('road_safety_reports', function (Blueprint $table) {
            if (Schema::hasColumn('road_safety_reports', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            }
        });

        Schema::table('injury_observatory_reports', function (Blueprint $table) {
            if (Schema::hasColumn('injury_observatory_reports', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });

        Schema::table('grupos_vulnerables_reports', function (Blueprint $table) {
            if (Schema::hasColumn('grupos_vulnerables_reports', 'district_id')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });

        // Step 5: Drop old jurisdiction_id columns and foreign keys
        Schema::table('municipalities', function (Blueprint $table) {
            if (Schema::hasColumn('municipalities', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        Schema::table('deaths', function (Blueprint $table) {
            if (Schema::hasColumn('deaths', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        Schema::table('road_safety_reports', function (Blueprint $table) {
            if (Schema::hasColumn('road_safety_reports', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        Schema::table('injury_observatory_reports', function (Blueprint $table) {
            if (Schema::hasColumn('injury_observatory_reports', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        Schema::table('grupos_vulnerables_reports', function (Blueprint $table) {
            if (Schema::hasColumn('grupos_vulnerables_reports', 'jurisdiction_id')) {
                try {
                    $table->dropForeign(['jurisdiction_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('jurisdiction_id');
            }
        });

        // Step 6: Drop jurisdictions table
        if (Schema::hasTable('jurisdictions')) {
            Schema::dropIfExists('jurisdictions');
        }
    }

    public function down(): void
    {
        // This is a destructive migration - rollback not fully supported
    }
};
