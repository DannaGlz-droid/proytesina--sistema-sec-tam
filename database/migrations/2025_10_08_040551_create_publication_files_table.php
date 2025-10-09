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
        Schema::create('publication_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained('publications'); // FK to publications
            $table->string('original_name', 255); // varchar(255) NOT NULL - nombre_original
            $table->string('file_name', 255); // varchar(255) NOT NULL - nombre_archivo
            $table->string('file_path', 255); // varchar(255) NOT NULL - ruta_archivo
            $table->string('file_type', 255); // varchar(255) NOT NULL - tipo_archivo
            $table->bigInteger('file_size'); // bigint NOT NULL - tamano_archivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_files');
    }
};
