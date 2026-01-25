<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUsersSalutationForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $existingForeignKey = $this->getForeignKeyName('users', 'salutation_id');
        if ($existingForeignKey) {
            Schema::table('users', function (Blueprint $table) use ($existingForeignKey) {
                $table->dropForeign($existingForeignKey);
            });
        }

        $designationIds = DB::table('designations')->pluck('id')->filter()->values();
        if ($designationIds->isNotEmpty()) {
            $defaultDesignationId = DB::table('designations')->where('name', 'Other')->value('id')
                ?? DB::table('designations')->where('abbr', 'OTHER')->value('id')
                ?? $designationIds->first();

            if ($defaultDesignationId) {
                DB::table('users')
                    ->whereNotNull('salutation_id')
                    ->whereNotIn('salutation_id', $designationIds->all())
                    ->update(['salutation_id' => $defaultDesignationId]);
            }
        }

        if (!$this->hasForeignKeyOnColumn('users', 'salutation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('salutation_id')
                    ->references('id')
                    ->on('designations')
                    ->cascadeOnUpdate();
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
        if (!Schema::hasTable('users')) {
            return;
        }

        $existingForeignKey = $this->getForeignKeyName('users', 'salutation_id');
        if ($existingForeignKey) {
            Schema::table('users', function (Blueprint $table) use ($existingForeignKey) {
                $table->dropForeign($existingForeignKey);
            });
        }

        if (!$this->hasForeignKeyOnColumn('users', 'salutation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('salutation_id')
                    ->references('id')
                    ->on('salutations')
                    ->cascadeOnUpdate();
            });
        }
    }

    /**
     * Get the foreign key name for a table/column pair.
     *
     * @param  string  $table
     * @param  string  $column
     * @return string|null
     */
    private function getForeignKeyName($table, $column)
    {
        $result = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        return $result ? $result->CONSTRAINT_NAME : null;
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
