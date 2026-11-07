<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->string('cie_cause_code', 32)->nullable()->after('death_cause_id');
            $table->text('cie_cause_description')->nullable()->after('cie_cause_code');
        });
    }

    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->dropColumn(['cie_cause_code', 'cie_cause_description']);
        });
    }
};
