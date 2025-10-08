<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // REPORTES DE ALCOHOLIMETRIA (3)
    {
        Schema::create('breathalyzer_tests', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('publication_id')->constrained('publications'); // FK to publications - id_publicacion
            
            // Estadísticas de puntos y pruebas
            $table->integer('checkpoints'); // int NOT NULL - puntos_revision
            $table->integer('tests_performed'); // int NOT NULL - pruebas_realizadas
            $table->integer('drivers_not_fit'); // int NOT NULL - conductores_no_aptos
            
            // Estadísticas por género
            $table->integer('women'); // int NOT NULL - mujeres
            $table->integer('men'); // int NOT NULL - hombres
            
            // Estadísticas por tipo de vehículo
            $table->integer('cars_trucks'); // int NOT NULL - automoviles_camionetas
            $table->integer('motorcycles'); // int NOT NULL - motocicletas
            $table->integer('public_transport_collective'); // int NOT NULL - transporte_publico_colectivo
            $table->integer('public_transport_individual'); // int NOT NULL - transporte_publico_individual
            $table->integer('cargo_transport'); // int NOT NULL - transporte_carga
            $table->integer('emergency_vehicles'); // int NOT NULL - vehiculos_emergencia
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breathalyzer_tests');
    }
};
