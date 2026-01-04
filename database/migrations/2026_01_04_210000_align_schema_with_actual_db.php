<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignSchemaWithActualDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('centers')) {
            Schema::create('centers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 50);
                $table->string('location', 100);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('salutations')) {
            Schema::create('salutations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('staff')) {
            $dropOffice = Schema::hasColumn('staff', 'office');
            $addDesignationId = !Schema::hasColumn('staff', 'designation_id');
            $addCenterId = !Schema::hasColumn('staff', 'center_id');
            $addType = !Schema::hasColumn('staff', 'type');

            Schema::table('staff', function (Blueprint $table) use ($dropOffice, $addDesignationId, $addCenterId, $addType) {
                if ($dropOffice) {
                    $table->dropColumn('office');
                }
                if ($addDesignationId) {
                    $table->unsignedBigInteger('designation_id');
                }
                if ($addCenterId) {
                    $table->unsignedBigInteger('center_id');
                }
                if ($addType) {
                    $table->enum('type', ['staff', 'paralegal', 'other'])->default('staff');
                }
            });

            $needsDesignationFk = Schema::hasColumn('staff', 'designation_id')
                && !$this->hasForeignKeyOnColumn('staff', 'designation_id');
            $needsCenterFk = Schema::hasColumn('staff', 'center_id')
                && !$this->hasForeignKeyOnColumn('staff', 'center_id');

            Schema::table('staff', function (Blueprint $table) use ($needsDesignationFk, $needsCenterFk) {
                if ($needsDesignationFk) {
                    $table->foreign('designation_id')
                        ->references('id')
                        ->on('designations')
                        ->cascadeOnUpdate()
                        ->cascadeOnDelete();
                }
                if ($needsCenterFk) {
                    $table->foreign('center_id')
                        ->references('id')
                        ->on('centers')
                        ->cascadeOnUpdate()
                        ->cascadeOnDelete();
                }
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
        if (Schema::hasTable('staff')) {
            $dropDesignationFk = $this->hasForeignKey('staff', 'staff_designation_id_foreign');
            $dropCenterFk = $this->hasForeignKey('staff', 'staff_center_id_foreign');

            Schema::table('staff', function (Blueprint $table) use ($dropDesignationFk, $dropCenterFk) {
                if ($dropDesignationFk) {
                    $table->dropForeign('staff_designation_id_foreign');
                }
                if ($dropCenterFk) {
                    $table->dropForeign('staff_center_id_foreign');
                }
            });

            $dropType = Schema::hasColumn('staff', 'type');
            $dropDesignationId = Schema::hasColumn('staff', 'designation_id');
            $dropCenterId = Schema::hasColumn('staff', 'center_id');
            $addOffice = !Schema::hasColumn('staff', 'office');

            Schema::table('staff', function (Blueprint $table) use ($dropType, $dropDesignationId, $dropCenterId, $addOffice) {
                if ($dropType) {
                    $table->dropColumn('type');
                }
                if ($dropDesignationId) {
                    $table->dropColumn('designation_id');
                }
                if ($dropCenterId) {
                    $table->dropColumn('center_id');
                }
                if ($addOffice) {
                    $table->string('office');
                }
            });
        }

        if (Schema::hasTable('salutations')) {
            Schema::dropIfExists('salutations');
        }

        if (Schema::hasTable('centers')) {
            Schema::dropIfExists('centers');
        }
    }

    /**
     * Check if a foreign key exists in the current schema.
     *
     * @param  string  $table
     * @param  string  $foreignKey
     * @return bool
     */
    private function hasForeignKey($table, $foreignKey)
    {
        $result = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $foreignKey]
        );

        return $result !== null;
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
