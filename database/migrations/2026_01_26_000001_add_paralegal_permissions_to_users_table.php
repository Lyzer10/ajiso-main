<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParalegalPermissionsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't already exist
            if (!Schema::hasColumn('users', 'can_register_staff')) {
                $table->boolean('can_register_staff')->default(true)->after('is_active')->comment('Can this paralegal register other paralegals');
            }
            if (!Schema::hasColumn('users', 'has_system_access')) {
                $table->boolean('has_system_access')->default(true)->after('can_register_staff')->comment('Does this paralegal have system access');
            }
            if (!Schema::hasColumn('users', 'added_by_admin')) {
                $table->boolean('added_by_admin')->default(false)->after('has_system_access')->comment('Was this paralegal added by admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists(['can_register_staff', 'has_system_access', 'added_by_admin']);
        });
    }
}
