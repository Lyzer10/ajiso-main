<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAttachmentActivityTypeToDisputeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE dispute_activities MODIFY activity_type ENUM('status','notification','clinic','remarks','attachment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE dispute_activities MODIFY activity_type ENUM('status','notification','clinic','remarks') NOT NULL");
    }
}
