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
        Schema::create('grupos_vulnerables_reports', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('publication_id')->constrained('publications'); // FK to publications
            $table->foreignId('activity_type_id')->constrained('activity_types'); // FK to activity_types
            $table->integer('participants'); // int NOT NULL - participantes
            $table->string('location', 255); // varchar(255) NOT NULL - lugar
            $table->string('promoter', 255); // varchar(255) NOT NULL - promotor
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities'); // FK to municipalities
            $table->foreignId('jurisdiction_id')->nullable()->constrained('jurisdictions'); // FK to jurisdictions
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos_vulnerables_reports');
    }
};
