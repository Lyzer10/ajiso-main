<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationSourceToBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('beneficiaries')) {
            return;
        }

        if (!Schema::hasColumn('beneficiaries', 'registration_source')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->enum('registration_source', ['office', 'paralegal'])
                    ->default('office')
                    ->after('income_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('beneficiaries')) {
            return;
        }

        if (Schema::hasColumn('beneficiaries', 'registration_source')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropColumn('registration_source');
            });
        }
    }
}
