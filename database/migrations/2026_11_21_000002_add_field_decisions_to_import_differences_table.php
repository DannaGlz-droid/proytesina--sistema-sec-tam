<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_differences', function (Blueprint $table) {
            $table->json('field_decisions')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('import_differences', function (Blueprint $table) {
            $table->dropColumn('field_decisions');
        });
    }
};
