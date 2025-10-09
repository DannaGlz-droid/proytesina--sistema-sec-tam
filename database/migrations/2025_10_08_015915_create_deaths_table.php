<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // DEFUNCIONES
    {
        Schema::create('deaths', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 255); // varchar(255) NOT NULL - nombre
            $table->string('first_last_name', 255); // varchar(255) NOT NULL - apellido_paterno
            $table->string('second_last_name', 255)->nullable(); // varchar(255) - apellido_materno
            $table->integer('age'); // int NOT NULL - edad
            $table->enum('sex', ['M', 'F', 'Otro']); // enum('M', 'F', 'Otro') NOT NULL - sexo
            $table->date('death_date'); // date NOT NULL - fecha_defuncion
            $table->foreignId('residence_municipality_id')->constrained('municipalities'); // FK to municipalities - id_municipio_residencia
            $table->foreignId('jurisdiction_id')->constrained('jurisdictions'); // FK to jurisdictions - id_jurisdiccion
            $table->foreignId('death_municipality_id')->constrained('municipalities'); // FK to municipalities - id_municipio_defuncion
            $table->foreignId('death_location_id')->constrained('death_locations'); // FK to death_locations - id_lugar_defuncion
            $table->foreignId('death_cause_id')->constrained('death_causes'); // FK to death_causes - id_causa_defuncion
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deaths');
    }
};
