<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metrics')->insert([
            [

                'metric' => 'performance',
                'metric_measure_id' => 1,
                'metric_limit' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
