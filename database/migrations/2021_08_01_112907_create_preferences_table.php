<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->string('sys_abbr');
            $table->string('sys_name');
            $table->string('org_abbr');
            $table->string('org_name');
            $table->string('org_site');
            $table->string('org_email');
            $table->string('org_tel');
            $table->string('currency_format');
            $table->string('date_format');
            $table->string('language');
            $table->string('notification_mode');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferences');
    }
}
