<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisputeStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dispute_statuses')->insert([
            [
                'dispute_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dispute_status' => 'proceeding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dispute_status' => 'resolved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dispute_status' => 'continue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dispute_status' => 'referred',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dispute_status' => 'discontinued',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
