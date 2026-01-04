<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignBeneficiariesTable extends Migration
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

        $dropDisabled = Schema::hasColumn('beneficiaries', 'disabled');
        $addTribeId = !Schema::hasColumn('beneficiaries', 'tribe_id');
        $addReligionId = !Schema::hasColumn('beneficiaries', 'religion_id');

        Schema::table('beneficiaries', function (Blueprint $table) use ($dropDisabled, $addTribeId, $addReligionId) {
            if ($dropDisabled) {
                $table->dropColumn('disabled');
            }
            if ($addTribeId) {
                $table->unsignedBigInteger('tribe_id');
            }
            if ($addReligionId) {
                $table->unsignedBigInteger('religion_id');
            }
        });

        if (Schema::hasColumn('beneficiaries', 'marriage_form_id')) {
            DB::statement('UPDATE `beneficiaries` SET `marriage_form_id` = 1 WHERE `marriage_form_id` IS NULL');
            DB::statement('ALTER TABLE `beneficiaries` MODIFY `marriage_form_id` bigint(20) UNSIGNED NOT NULL');
        }

        $needsTribeFk = Schema::hasColumn('beneficiaries', 'tribe_id')
            && !$this->hasForeignKeyOnColumn('beneficiaries', 'tribe_id');
        $needsReligionFk = Schema::hasColumn('beneficiaries', 'religion_id')
            && !$this->hasForeignKeyOnColumn('beneficiaries', 'religion_id');

        Schema::table('beneficiaries', function (Blueprint $table) use ($needsTribeFk, $needsReligionFk) {
            if ($needsTribeFk) {
                $table->foreign('tribe_id')
                    ->references('id')
                    ->on('tribes')
                    ->cascadeOnUpdate();
            }
            if ($needsReligionFk) {
                $table->foreign('religion_id')
                    ->references('id')
                    ->on('religions')
                    ->cascadeOnUpdate();
            }
        });
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

        $dropTribeFk = $this->hasForeignKeyOnColumn('beneficiaries', 'tribe_id');
        $dropReligionFk = $this->hasForeignKeyOnColumn('beneficiaries', 'religion_id');

        Schema::table('beneficiaries', function (Blueprint $table) use ($dropTribeFk, $dropReligionFk) {
            if ($dropTribeFk) {
                $table->dropForeign(['tribe_id']);
            }
            if ($dropReligionFk) {
                $table->dropForeign(['religion_id']);
            }
        });

        $dropTribeId = Schema::hasColumn('beneficiaries', 'tribe_id');
        $dropReligionId = Schema::hasColumn('beneficiaries', 'religion_id');
        $addDisabled = !Schema::hasColumn('beneficiaries', 'disabled');

        Schema::table('beneficiaries', function (Blueprint $table) use ($dropTribeId, $dropReligionId, $addDisabled) {
            if ($dropTribeId) {
                $table->dropColumn('tribe_id');
            }
            if ($dropReligionId) {
                $table->dropColumn('religion_id');
            }
            if ($addDisabled) {
                $table->enum('disabled', ['yes', 'no']);
            }
        });

        if (Schema::hasColumn('beneficiaries', 'marriage_form_id')) {
            DB::statement('ALTER TABLE `beneficiaries` MODIFY `marriage_form_id` bigint(20) UNSIGNED NULL');
        }
    }

    /**
     * Check if any foreign key exists on a given column.
     *
     * @param  string  $table
     * @param  string  $column
     * @return bool
     */
    private function hasForeignKeyOnColumn($table, $column)
    {
        $result = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        return $result !== null;
    }
}
