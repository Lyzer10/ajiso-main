<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('religions')->insert([
            [
                'religion' => 'Christian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Islam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Folk Religion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Unafilliated',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Bahá\'í Faith',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Judaism',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Buddhism',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Hinduism',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'religion' => 'Sikhism',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
