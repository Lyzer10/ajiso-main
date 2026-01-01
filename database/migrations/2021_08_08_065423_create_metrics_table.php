<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric');
            $table->bigInteger('metric_measure_id')->unsigned();
            $table->integer('metric_limit');
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('metric_measure_id')->references('id')->on('metric_measures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metrics');
    }
}
