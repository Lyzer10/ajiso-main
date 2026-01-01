<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('disputes', function (Blueprint $table) {
			$table->id();
			$table->string('dispute_no');
			$table->date('reported_on');
			$table->bigInteger('beneficiary_id')
				->unsigned();
			$table->enum('matter_to_court', ['yes', 'no']);
			$table->enum('type_of_court', ['supreme court', '
land court', 'regional court', 'district court', 'the court of first instance', 'court of kadhi', 'social welfare/police', 'arbitration commission']);
			$table->longText('problem_description');
			$table->string('where_reported')
				->nullable();
			$table->longText('service_experience')
				->nullable();
			$table->longText('how_did_they_help')
				->nullable();
			$table->longText('how_can_we_help');
			$table->longText('defendant_names_addr');
			$table->bigInteger('staff_id')
				->unsigned()
				->nullable();
			$table->bigInteger('type_of_service_id')
				->unsigned();
			$table->bigInteger('type_of_case_id')
				->unsigned();
			$table->bigInteger('dispute_status_id')
				->unsigned();
			$table->timestamps();
			$table->softDeletes();

			// Foreign Keys
			$table->foreign('beneficiary_id')
				->references('id')
				->on('beneficiaries')
				->cascadeOnDelete();
			$table->foreign('staff_id')
				->references('id')
				->on('staff')
				->cascadeOnUpdate()
				->nullOnDelete();
			$table->foreign('type_of_service_id')
				->references('id')
				->on('type_of_services');
			$table->foreign('type_of_case_id')
				->references('id')
				->on('type_of_cases');
			$table->foreign('dispute_status_id')
				->references('id')
				->on('dispute_statuses');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('disputes');
	}
}
