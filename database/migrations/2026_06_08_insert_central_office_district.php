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
        // Insertar "Oficina Central" en la tabla districts
        DB::table('districts')->insertOrIgnore([
            'id' => 999,
            'name' => 'Oficina Central',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar "Oficina Central" al hacer rollback
        DB::table('districts')->where('id', 999)->delete();
    }
};
