<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgeGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('age_groups')->insert([
            [
                'age_group' => 'Below 18',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'age_group' => '18 - 45',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'age_group' => '46 - 59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'age_group' => 'Over 60',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
