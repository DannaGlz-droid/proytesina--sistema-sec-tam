<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // PUBLICACIONES
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('user_id')->constrained('users'); // FK to users - id_usuario
            $table->string('publication_type', 255); // varchar(255) NOT NULL - tipo_publicacion
            $table->string('topic', 255); // varchar(255) NOT NULL - tema
            $table->text('description')->nullable(); // text - descripcion
            $table->date('publication_date'); // date NOT NULL - fecha_publicacion
            $table->date('activity_date'); // date NOT NULL - fecha_actividad
            $table->string('status', 255)->default('borrador'); // varchar(255) DEFAULT 'borrador' - estado
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
