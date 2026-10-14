<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const UNUSED_LOCATION_NAMES = [
        '4',
        'SITIO_NUEVO',
        'TEST_LOC',
        'V�A P�BLICA',
    ];

    public function up(): void
    {
        DB::table('death_locations')
            ->whereIn('name', self::UNUSED_LOCATION_NAMES)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('deaths')
                    ->whereColumn('deaths.death_location_id', 'death_locations.id');
            })
            ->delete();
    }

    public function down(): void
    {
        foreach (self::UNUSED_LOCATION_NAMES as $name) {
            DB::table('death_locations')->updateOrInsert(
                ['name' => $name],
                [
                    'usage_count' => 0,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
};
