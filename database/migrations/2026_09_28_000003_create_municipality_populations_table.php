<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipality_populations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('population_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('municipality_id')->constrained()->cascadeOnDelete();
            $table->string('geographic_code', 5);
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('population');
            $table->timestamps();

            $table->unique(['population_source_id', 'municipality_id', 'year'], 'municipality_population_source_year_unique');
            $table->index(['municipality_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipality_populations');
    }
};
