<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // LUGARES DE DEFUNCIÓN
    {
        Schema::create('death_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // varchar(255) NOT NULL
            $table->integer('usage_count')->default(0); // int DEFAULT 0
            $table->boolean('is_active')->default(true); // boolean DEFAULT true
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_locations');
    }
};
