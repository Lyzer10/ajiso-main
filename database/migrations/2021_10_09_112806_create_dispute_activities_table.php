<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispute_activities', function (Blueprint $table) {
            $table->id();
            $table->string('dispute_activity')->nullable();
            $table->longText('description');
            $table->enum('activity_type', ['status', 'notification', 'clinic', 'remarks']);
            $table->bigInteger('dispute_id')
                    ->unsigned();
            $table->bigInteger('staff_id')
                    ->unsigned()
                    ->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('dispute_id')
                    ->references('id')
                    ->on('disputes')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
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
        Schema::dropIfExists('dispute_activities');
    }
}
