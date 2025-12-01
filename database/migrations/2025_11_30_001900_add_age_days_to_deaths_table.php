<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            if (!Schema::hasColumn('deaths', 'age_days')) {
                $table->unsignedInteger('age_days')->nullable()->after('age_months');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            if (Schema::hasColumn('deaths', 'age_days')) {
                $table->dropColumn('age_days');
            }
        });
    }
};
