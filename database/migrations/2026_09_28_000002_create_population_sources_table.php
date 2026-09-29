<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('population_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->text('source_url')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamp('imported_at');
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('population_sources');
    }
};
