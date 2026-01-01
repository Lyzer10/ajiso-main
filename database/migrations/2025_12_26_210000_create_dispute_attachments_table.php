<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputeAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispute_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id');
            $table->string('name');
            $table->string('path');
            $table->string('file_type', 20);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('dispute_attachments');
    }
}
