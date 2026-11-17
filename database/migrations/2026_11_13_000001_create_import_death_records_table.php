<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->boolean('record_trace_complete')
                ->default(false)
                ->after('rows_changed_existing');
        });

        Schema::create('import_death_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->cascadeOnDelete();
            $table->foreignId('death_id')->nullable()->constrained('deaths')->nullOnDelete();
            $table->foreignId('source_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->string('gov_folio')->nullable();
            $table->string('result', 24);
            $table->timestamps();

            $table->unique(['import_id', 'death_id'], 'import_death_records_import_death_unique');
            $table->index(['source_import_id', 'result'], 'import_death_records_source_result_index');
            $table->index(['import_id', 'result'], 'import_death_records_import_result_index');
            $table->index('gov_folio');
        });

        // Preserve the ownership information that already exists. Historical
        // duplicate encounters cannot be reconstructed because only their totals
        // were stored, so those imports intentionally remain untraced.
        $now = now();
        DB::table('deaths')
            ->whereNotNull('import_id')
            ->select(['id', 'import_id', 'gov_folio'])
            ->orderBy('id')
            ->chunkById(500, function ($deaths) use ($now) {
                DB::table('import_death_records')->insert(
                    $deaths->map(fn ($death) => [
                        'import_id' => $death->import_id,
                        'death_id' => $death->id,
                        'source_import_id' => $death->import_id,
                        'gov_folio' => $death->gov_folio,
                        'result' => 'added',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_death_records');

        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('record_trace_complete');
        });
    }
};
