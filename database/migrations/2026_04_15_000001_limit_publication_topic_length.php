<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, truncar los datos que sean más largos que 146 caracteres
        DB::table('publications')
            ->whereRaw('CHAR_LENGTH(topic) > 146')
            ->update([
                'topic' => DB::raw('SUBSTRING(topic, 1, 146)')
            ]);
        
        // Luego cambiar la longitud del campo
        Schema::table('publications', function (Blueprint $table) {
            $table->string('topic', 146)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            // Revertir a 255 caracteres
            $table->string('topic', 255)->change();
        });
    }
};
