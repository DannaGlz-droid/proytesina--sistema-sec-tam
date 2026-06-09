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
        Schema::table('road_safety_reports', function (Blueprint $table) {
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities');
            $table->foreignId('district_id')->nullable()->constrained('districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('road_safety_reports', function (Blueprint $table) {
            $table->dropForeignIdFor('municipalities');
            $table->dropForeignIdFor('districts');
            $table->dropColumn(['municipality_id', 'district_id']);
        });
    }
};
