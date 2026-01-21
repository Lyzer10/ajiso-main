<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameClerkRoleToParalegal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_roles')
            ->where('role_abbreviation', 'clerk')
            ->update([
                'role_abbreviation' => 'paralegal',
                'role_name' => 'Paralegal',
            ]);

        DB::table('user_roles')
            ->where('role_abbreviation', 'paralegal')
            ->update([
                'role_name' => 'Paralegal',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('user_roles')
            ->where('role_abbreviation', 'paralegal')
            ->update([
                'role_abbreviation' => 'clerk',
                'role_name' => 'Clerk',
            ]);
    }
}
