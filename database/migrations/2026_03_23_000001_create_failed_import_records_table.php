<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('failed_import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->cascadeOnDelete();
            
            // Original row data (from Excel)
            $table->text('original_row_data'); // JSON con los datos originales
            
            // Error message
            $table->text('error_message');
            
            // Corrected data (filled in by user)
            $table->text('corrected_data')->nullable(); // JSON con datos corregidos
            
            // Status: pending, corrected, imported, discarded
            $table->enum('status', ['pending', 'corrected', 'imported', 'discarded'])->default('pending');
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_import_records');
    }
};
