<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            'Primary',
            'Secondary',
            'Diploma',
            'Bachelor',
            'Master',
            'PhD',
            'Uneducated'
        ];

        foreach ($levels as $level) {
            EducationLevel::create([
                'education_level' => $level
            ]);
        }
    }
}
