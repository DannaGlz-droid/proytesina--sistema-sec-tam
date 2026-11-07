<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->string('residence_municipality_external')->nullable()->after('residence_municipality_id');
            $table->string('residence_district_external')->nullable()->after('district_id');
            $table->string('death_municipality_external')->nullable()->after('death_municipality_id');
            $table->string('death_district_external')->nullable()->after('death_district_id');
        });
    }

    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->dropColumn([
                'residence_municipality_external',
                'residence_district_external',
                'death_municipality_external',
                'death_district_external',
            ]);
        });
    }
};
