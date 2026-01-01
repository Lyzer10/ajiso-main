<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employment_statuses')->insert([
            [
                'employment_status' => 'None',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Employed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Unemployed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Self-employed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Retired',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Wage labor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employment_status' => 'Minor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
