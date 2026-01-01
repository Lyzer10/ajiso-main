<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispute_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('file_type', 20);
            $table->bigInteger('counseling_sheet_id')
                ->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('counseling_sheet_id')
                ->references('id')
                ->on('counseling_sheets')
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
        Schema::dropIfExists('dispute_files');
    }
}
