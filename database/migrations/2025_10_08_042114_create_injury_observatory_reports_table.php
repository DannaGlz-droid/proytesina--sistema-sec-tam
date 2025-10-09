<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // REPORTES DEL OBSERVATORIO DE LESIONES (2)
    {
        Schema::create('injury_observatory_reports', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('publication_id')->constrained('publications'); // FK to publications - id_publicacion
            $table->foreignId('municipality_id')->constrained('municipalities'); // FK to municipalities - id_municipio
            $table->foreignId('jurisdiction_id')->constrained('jurisdictions'); // FK to jurisdictions - id_jurisdiccion
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('injury_observatory_reports');
    }
};
