<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_differences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->cascadeOnDelete();
            $table->foreignId('death_id')->nullable()->constrained('deaths')->nullOnDelete();
            $table->foreignId('source_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->string('gov_folio')->nullable()->index();
            $table->json('previous_values');
            $table->json('proposed_values');
            $table->json('changed_fields');
            $table->string('status', 24)->default('pending');
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['import_id', 'death_id'], 'import_differences_import_death_unique');
            $table->index(['import_id', 'status'], 'import_differences_import_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_differences');
    }
};
