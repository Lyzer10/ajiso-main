<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('office');
            $table->boolean('is_assigned')
                    ->default(true);
            $table->bigInteger('user_id')
                    ->unsigned();
            $table->timestamps();
            $table->softDeletes();

            //Foreign Keys
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('staff');
    }
}
