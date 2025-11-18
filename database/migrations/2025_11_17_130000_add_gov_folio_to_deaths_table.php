<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGovFolioToDeathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deaths', function (Blueprint $table) {
            if (!Schema::hasColumn('deaths', 'gov_folio')) {
                $table->string('gov_folio')->nullable()->unique()->after('age_months');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deaths', function (Blueprint $table) {
            if (Schema::hasColumn('deaths', 'gov_folio')) {
                $table->dropUnique(['gov_folio']);
                $table->dropColumn('gov_folio');
            }
        });
    }
}
