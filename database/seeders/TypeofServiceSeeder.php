<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeofServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_of_services')->insert([
            [
                'service_abbreviation' => 'MEDREC',
                'type_of_service' => 'Mediation & Reconciliation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_abbreviation' => 'EDUINFO',
                'type_of_service' => 'Education & Information',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_abbreviation' => 'ESCORT',
                'type_of_service' => 'Escort/Representation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
