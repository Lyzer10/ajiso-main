<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dispute_id')
                    ->unsigned();
            $table->longText('reason_description');
            $table->bigInteger('staff_id')
                    ->unsigned();
            $table->enum('request_status', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('staff_id')
                    ->references('id')
                    ->on('staff')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            $table->foreign('dispute_id')
                    ->references('id')
                    ->on('disputes')
                    ->cascadeOnUpdate()
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
        Schema::dropIfExists('assignment_requests');
    }
}
