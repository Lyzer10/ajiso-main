<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incomes')->insert([
            [
                'income' => 'None',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'income' => 'Below - 300,000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'income' => '300,000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'income' => 'Above - 300,000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
