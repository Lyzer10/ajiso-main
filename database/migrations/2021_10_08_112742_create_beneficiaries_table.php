<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beneficiaries', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('user_id')
				->unsigned();
			$table->enum('gender', ['male', 'female', 'other']);
			$table->enum('disabled', ['yes', 'no']);
			$table->integer('age')->unsigned()->default(0);
			$table->integer('age_group')->unsigned()->default(0);
			$table->bigInteger('education_level_id')->unsigned();
			$table->string('address')->nullable();
			$table->bigInteger('district_id')->unsigned();
			$table->string('ward')->nullable();
			$table->string('street')->nullable();
			$table->bigInteger('survey_choice_id')->unsigned();
			$table->bigInteger('marital_status_id')->unsigned();
			$table->bigInteger('marriage_form_id')->unsigned()->nullable();
			$table->string('marriage_date')->nullable()->default(NULL);
			$table->integer('no_of_children')->default(0);
			$table->enum('financial_capability', ['Capable', 'Incapable']);
			$table->bigInteger('employment_status_id')->unsigned();
			$table->string('occupation_business')->default('N/A');
			$table->bigInteger('income_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();

			//Foreign Keys
			$table->foreign('district_id')
				->references('id')
				->on('districts')
				->cascadeOnUpdate();
			$table->foreign('education_level_id')
				->references('id')
				->on('education_levels')
				->cascadeOnUpdate();
			$table->foreign('survey_choice_id')
				->references('id')
				->on('survey_choices')
				->cascadeOnUpdate();
			$table->foreign('marital_status_id')
				->references('id')
				->on('marital_statuses')
				->cascadeOnUpdate();
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->cascadeOnUpdate()
				->cascadeOnDelete();
			$table->foreign('income_id')
				->references('id')
				->on('incomes')
				->cascadeOnUpdate();
			$table->foreign('employment_status_id')
				->references('id')
				->on('employment_statuses')
				->cascadeOnUpdate();
			$table->foreign('marriage_form_id')
				->references('id')
				->on('marriage_forms')
				->cascadeOnUpdate();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('beneficiaries');
	}
}
