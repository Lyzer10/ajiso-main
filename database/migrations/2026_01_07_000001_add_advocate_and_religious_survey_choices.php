<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAdvocateAndReligiousSurveyChoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('survey_choices')) {
            return;
        }

        $choices = [
            ['choice_abbr' => 'Advocate', 'survey_choice' => 'Referral from Advocate'],
            ['choice_abbr' => 'Religious', 'survey_choice' => 'Religious organizations'],
        ];

        foreach ($choices as $choice) {
            $exists = DB::table('survey_choices')
                ->where('survey_choice', $choice['survey_choice'])
                ->exists();

            if (!$exists) {
                DB::table('survey_choices')->insert([
                    'choice_abbr' => $choice['choice_abbr'],
                    'survey_choice' => $choice['survey_choice'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('survey_choices')) {
            return;
        }

        DB::table('survey_choices')
            ->whereIn('survey_choice', ['Referral from Advocate', 'Religious organizations'])
            ->delete();
    }
}
