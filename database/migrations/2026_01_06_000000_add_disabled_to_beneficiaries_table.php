<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisabledToBeneficiariesTable extends Migration
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

        if (!Schema::hasColumn('beneficiaries', 'disabled')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->enum('disabled', ['yes', 'no'])->default('no');
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

        if (Schema::hasColumn('beneficiaries', 'disabled')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropColumn('disabled');
            });
        }
    }
}
