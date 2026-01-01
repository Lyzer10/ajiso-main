<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveyChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $choices = [
            ['choice_abbr' => 'TV/Radio', 'survey_choice' => 'TV/Radio'],
            ['choice_abbr' => 'Magazine', 'survey_choice' => 'Magazine'],
            ['choice_abbr' => 'Socials', 'survey_choice' => 'Social Media'],
            ['choice_abbr' => 'Referral', 'survey_choice' => 'Peer Referral'],
            ['choice_abbr' => 'Pubs', 'survey_choice' => 'Blog or Publication'],
            ['choice_abbr' => 'Search', 'survey_choice' => 'Search engine (Google, Yahoo, etc.)'],
            ['choice_abbr' => 'Events', 'survey_choice' => 'Events'],
            ['choice_abbr' => 'Paralegals', 'survey_choice' => 'Referral from Legal aid providers'],
            ['choice_abbr' => 'Billboards', 'survey_choice' => 'Billboard Advertisements'],
            ['choice_abbr' => 'Other', 'survey_choice' => 'Others'],
        ];

        foreach ($choices as $choice) {
            DB::table('survey_choices')->insert([
                'choice_abbr' => $choice['choice_abbr'],
                'survey_choice' => $choice['survey_choice'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
