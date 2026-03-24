<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            // Track if this import has been reversed
            $table->boolean('is_reversed')->default(false)->after('error_message');
            
            // Track when it was reversed
            $table->timestamp('reversed_at')->nullable()->after('is_reversed');
            
            // Track who reversed it (admin user)
            $table->foreignId('reversed_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('reversed_at');
        });
    }

    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn(['is_reversed', 'reversed_at', 'reversed_by_user_id']);
        });
    }
};
