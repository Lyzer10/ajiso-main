<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preferences')->insert([
            'sys_abbr' => 'AJISO',
            'sys_name' => 'AJISO Legal Aid System',
            'org_abbr' => 'AJISO',
            'org_name' => 'Action for Justice in Society',
            'org_site' => 'https://ajiso.org',
            'org_email' => 'info@ajiso.org',
            'org_tel' => '+255 000 000 000',
            'currency_format' => 'Tshs',
            'date_format' => 'd-m-Y',
            'language' => 'en',
            'notification_mode' => 'sms_email_sys',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
