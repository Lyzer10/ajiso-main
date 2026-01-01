<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Designation::factory(15)->create();
        //User::factory(30)->create();
        $this->call(AgeGroupsSeeder::class);
        $this->call(DesignationsSeeder::class);
        $this->call(DisputeStatusesSeeder::class);
        $this->call(EducationLevelsSeeder::class);
        $this->call(EmploymentStatusesSeeder::class);
        $this->call(IncomesSeeder::class);
        $this->call(MaritalStatusesSeeder::class);
        $this->call(MarriageFormsSeeder::class);
        $this->call(MetricMeasureSeeder::class);
        $this->call(MetricSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(ReligionsSeeder::class);
        $this->call(SurveyChoiceSeeder::class);
        $this->call(TribeSeeder::class);
        $this->call(TypeofCaseSeeder::class);
        $this->call(TypeofServiceSeeder::class);
        $this->call(UserRolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StaffSeeder::class);
        $this->call(BeneficiariesSeeder::class);
        $this->call(DisputesSeeder::class);
        $this->call(DisputeActivitiesSeeder::class);
    }
}
