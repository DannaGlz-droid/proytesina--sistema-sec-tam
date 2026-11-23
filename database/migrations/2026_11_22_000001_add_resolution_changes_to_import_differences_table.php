<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_differences', function (Blueprint $table) {
            $table->json('resolution_changes')->nullable()->after('field_decisions');
        });
    }

    public function down(): void
    {
        Schema::table('import_differences', function (Blueprint $table) {
            $table->dropColumn('resolution_changes');
        });
    }
};
