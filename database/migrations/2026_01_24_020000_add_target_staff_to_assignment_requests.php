<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTargetStaffToAssignmentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('assignment_requests')) {
            DB::table('assignment_requests')
                ->where('created_at', '0000-00-00 00:00:00')
                ->update(['created_at' => null]);
            DB::table('assignment_requests')
                ->where('updated_at', '0000-00-00 00:00:00')
                ->update(['updated_at' => null]);
        }

        Schema::table('assignment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_requests', 'target_staff_id')) {
                $table->unsignedBigInteger('target_staff_id')->nullable()->after('staff_id');
                $table->foreign('target_staff_id')
                    ->references('id')
                    ->on('staff')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
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
            if (Schema::hasColumn('assignment_requests', 'target_staff_id')) {
                $table->dropForeign(['target_staff_id']);
                $table->dropColumn('target_staff_id');
            }
        });
    }
}
