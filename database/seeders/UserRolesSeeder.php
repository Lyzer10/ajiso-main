<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert([
            [
                'role_abbreviation' => 'superadmin',
                'role_name' => 'Super Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_abbreviation' => 'admin',
                'role_name' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_abbreviation' => 'staff',
                'role_name' => 'Staff / Paralegal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_abbreviation' => 'beneficiary',
                'role_name' => 'Beneficiary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_abbreviation' => 'paralegal',
                'role_name' => 'Paralegal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
