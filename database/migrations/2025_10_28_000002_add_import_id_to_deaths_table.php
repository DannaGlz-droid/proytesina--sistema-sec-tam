<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deaths', function (Blueprint $table) {
            // Track which import batch this death came from
            $table->foreignId('import_id')->nullable()->constrained('imports')->nullOnDelete()->after('id');
        });
    }

    public function down()
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Death::class);
            $table->dropColumn('import_id');
        });
    }
};
