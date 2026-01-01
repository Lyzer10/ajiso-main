<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('designations')->insert([
            [
                'designation' => 'Mr.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'designation' => 'Mrs.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'designation' => 'Ms.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
