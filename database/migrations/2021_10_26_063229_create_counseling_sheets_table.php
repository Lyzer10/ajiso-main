<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounselingSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('counseling_sheets', function (Blueprint $table) {
            $table->id();
            $table->date('attended_at');
            $table->time('time_in');
            $table->time('time_out');
            $table->boolean('is_open')->default(true);
            $table->longText('advice_given');
            $table->bigInteger('dispute_activity_id')
                ->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('dispute_activity_id')
                ->references('id')
                ->on('dispute_activities')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counseling_sheets');
    }
}
