<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marital_statuses')->insert([
            [
                'marital_status' => 'Single',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marital_status' => 'Married',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marital_status' => 'Widow/Widower',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marital_status' => 'Single Parent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marital_status' => 'Divorced',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
