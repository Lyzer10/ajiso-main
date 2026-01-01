<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_no')->unique();
            $table->string('name')->unique();
            $table->string('email')->unique()->nullable();
            $table->BigInteger('designation_id')->unsigned();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('tel_no');
            $table->string('mobile_no')->nullable();
            $table->BigInteger('user_role_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->string('image')->default('avatar.png');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('designation_id')->references('id')->on('designations');
            $table->foreign('user_role_id')->references('id')->on('user_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
