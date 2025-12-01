<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurisdiction;
use App\Models\Municipality;
use App\Models\DeathLocation;
use App\Models\DeathCause;
use App\Models\Death;

class InsertTestDeathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get minimal related records required by the deaths table
        $jur = Jurisdiction::firstOrCreate(['name' => 'TEST_JUR']);

        $mun = Municipality::firstOrCreate([
            'name' => 'TEST_MUN',
            'jurisdiction_id' => $jur->id,
        ]);

        $loc = DeathLocation::firstOrCreate(['name' => 'TEST_LOC']);
        $cause = DeathCause::firstOrCreate(['name' => 'TEST_CAUSE']);

        // Create the Death record with gov_folio = 222222222 if it doesn't exist
        $death = Death::firstOrCreate(
            ['gov_folio' => '222222222'],
            [
                'name' => 'AUTOGEN',
                'first_last_name' => 'INSERT',
                'second_last_name' => 'TINKER',
                'age' => 30,
                'sex' => 'M',
                'death_date' => '2024-01-01',
                'residence_municipality_id' => $mun->id,
                'jurisdiction_id' => $jur->id,
                'death_municipality_id' => $mun->id,
                'death_location_id' => $loc->id,
                'death_cause_id' => $cause->id,
            ]
        );

        $this->command->info('InsertTestDeathSeeder: death id=' . $death->id . ' gov_folio=' . $death->gov_folio);
    }
}
