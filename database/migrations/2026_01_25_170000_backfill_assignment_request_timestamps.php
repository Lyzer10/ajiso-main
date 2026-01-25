<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillAssignmentRequestTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('assignment_requests')) {
            return;
        }

        DB::table('assignment_requests')
            ->whereNull('created_at')
            ->update([
                'created_at' => DB::raw('COALESCE(updated_at, CURRENT_TIMESTAMP)'),
            ]);

        DB::table('assignment_requests')
            ->whereNull('updated_at')
            ->update([
                'updated_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)'),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No-op: this is a data backfill.
    }
}

