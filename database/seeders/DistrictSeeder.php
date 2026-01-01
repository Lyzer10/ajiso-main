<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districtsByRegion = [
            'Mjini Magharibi' => ['Magharibi A', 'Magharibi B'],
            'Kaskazini Pemba' => ['Wete', 'Micheweni'],
            'Kusini Pemba' => ['Chake Chake', 'Mkoani'],
            'Kaskazini Unguja' => ['Kaskazini A', 'Kaskazini B'],
            'Kusini Unguja' => ['Kati', 'Kusini'],
        ];

        foreach ($districtsByRegion as $regionName => $districts) {
            $regionId = DB::table('regions')->where('region', $regionName)->value('id');

            foreach ($districts as $district) {
                DB::table('districts')->insert([
                    'district' => $district,
                    'region_id' => $regionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
