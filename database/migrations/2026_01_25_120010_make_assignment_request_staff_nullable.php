<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeAssignmentRequestStaffNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('assignment_requests', 'staff_id')) {
            return;
        }

        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->dropForeign('assignment_requests_staff_id_foreign');
        });

        DB::statement('ALTER TABLE assignment_requests MODIFY staff_id BIGINT UNSIGNED NULL');

        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('assignment_requests', 'staff_id')) {
            return;
        }

        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->dropForeign('assignment_requests_staff_id_foreign');
        });

        DB::statement('ALTER TABLE assignment_requests MODIFY staff_id BIGINT UNSIGNED NOT NULL');

        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
}
