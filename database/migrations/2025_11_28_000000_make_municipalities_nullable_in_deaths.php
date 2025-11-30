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
            // Make municipality columns nullable to allow "No encontrado" (NULL) values
            $table->unsignedBigInteger('residence_municipality_id')->nullable()->change();
            $table->unsignedBigInteger('death_municipality_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            // Revert to NOT NULL if needed
            $table->unsignedBigInteger('residence_municipality_id')->nullable(false)->change();
            $table->unsignedBigInteger('death_municipality_id')->nullable(false)->change();
        });
    }
};
