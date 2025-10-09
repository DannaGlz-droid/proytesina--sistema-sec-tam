<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // CAUSAS DE MUERTE
    {
        Schema::create('death_causes', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 255); // varchar(255) NOT NULL
            $table->timestamps(); // created_at y updated_at automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_causes');
    }
};
