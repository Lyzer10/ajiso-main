<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequesterUserToAssignmentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_requests', 'requester_user_id')) {
                $table->bigInteger('requester_user_id')->unsigned()->nullable()->after('staff_id');
                $table->foreign('requester_user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
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
        Schema::table('assignment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_requests', 'requester_user_id')) {
                $table->dropForeign(['requester_user_id']);
                $table->dropColumn('requester_user_id');
            }
        });
    }
}
