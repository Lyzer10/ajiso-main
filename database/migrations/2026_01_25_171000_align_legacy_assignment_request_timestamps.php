<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignLegacyAssignmentRequestTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('assignment_requests') || !Schema::hasTable('disputes')) {
            return;
        }

        // Legacy rows may not have requester_user_id. Align their timestamps
        // with the dispute dates so ordering and diffForHumans are meaningful.
        DB::statement(
            "UPDATE assignment_requests ar
             LEFT JOIN disputes d ON d.id = ar.dispute_id
             SET ar.created_at = COALESCE(d.created_at, d.updated_at, ar.created_at, CURRENT_TIMESTAMP),
                 ar.updated_at = COALESCE(d.updated_at, ar.updated_at, ar.created_at, CURRENT_TIMESTAMP)
             WHERE ar.requester_user_id IS NULL"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No-op: this is a data alignment migration.
    }
}

