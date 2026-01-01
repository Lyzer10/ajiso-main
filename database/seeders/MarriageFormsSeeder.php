<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarriageFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marriage_forms')->insert([
            [
                'marriage_form' => 'N/A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marriage_form' => 'Statutory',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marriage_form' => 'Christian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marriage_form' => 'Islamic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marriage_form' => 'Traditional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marriage_form' => 'Other',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
