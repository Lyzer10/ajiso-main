<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetricMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metric_measures')->insert([
            [
                'metric_measure' => 'Disputes Registered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'metric_measure' => 'Beneficiaries Registered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'metric_measure' => 'Disputes Resolved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
