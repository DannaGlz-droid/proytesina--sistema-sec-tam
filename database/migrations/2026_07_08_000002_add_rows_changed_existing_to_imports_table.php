<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            if (!Schema::hasColumn('imports', 'rows_changed_existing')) {
                $table->integer('rows_changed_existing')->default(0)->after('rows_skipped_duplicates');
            }
        });
    }

    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            if (Schema::hasColumn('imports', 'rows_changed_existing')) {
                $table->dropColumn('rows_changed_existing');
            }
        });
    }
};
