<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParalegalUserIdToDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disputes', function (Blueprint $table) {
            if (!Schema::hasColumn('disputes', 'paralegal_user_id')) {
                $table->unsignedBigInteger('paralegal_user_id')->nullable()->after('staff_id');
                $table->foreign('paralegal_user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'paralegal_user_id')) {
                $table->dropForeign(['paralegal_user_id']);
                $table->dropColumn('paralegal_user_id');
            }
        });
    }
}
