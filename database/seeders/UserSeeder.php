<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'user_no'       => 'USR001',
            'name'          => 'Super Admin',
            'email'         => 'superadmin@gmail.com',
            'designation_id' => 1,
            'password'      => Hash::make('12345678'),
            'first_name'    => 'Super',
            'last_name'     => 'Admin',
            'tel_no'        => '022-123456',
            'mobile_no'     => '0712345678',
            'user_role_id'  => 1,
        ]);
    }
}
