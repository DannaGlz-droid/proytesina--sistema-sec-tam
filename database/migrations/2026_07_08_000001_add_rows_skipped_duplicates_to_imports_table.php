<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->integer('rows_skipped_duplicates')->default(0)->after('rows_failed');
        });
    }

    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('rows_skipped_duplicates');
        });
    }
};
