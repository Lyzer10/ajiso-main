<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeofCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_of_cases')->insert([
            [

                'type_of_case' => 'Labour',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Civil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'GBV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Marriage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Probate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'GBV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Child Maintenance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Land',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type_of_case' => 'Criminal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
