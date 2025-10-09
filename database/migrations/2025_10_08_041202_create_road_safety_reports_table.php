<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // REPORTES DE SEGURIDAD VIAL (1)
    {
        Schema::create('road_safety_reports', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('publication_id')->constrained('publications'); // FK to publications - id_publicacion
            $table->foreignId('activity_type_id')->constrained('activity_types'); // FK to activity_types - id_tipo_actividad
            $table->integer('participants'); // int NOT NULL - participantes
            $table->string('location', 255); // varchar(255) NOT NULL - lugar
            $table->string('promoter', 255); // varchar(255) NOT NULL - promotor
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_safety_reports');
    }
};
