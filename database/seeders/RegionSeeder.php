<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            'Mjini Magharibi',
            'Kaskazini Pemba',
            'Kusini Pemba',
            'Kaskazini Unguja',
            'Kusini Unguja',
        ];

        foreach ($regions as $region) {
            DB::table('regions')->insert([
                'region' => $region,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
