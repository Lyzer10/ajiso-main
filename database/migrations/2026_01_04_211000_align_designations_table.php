<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('designations')) {
            return;
        }

        $hasName = Schema::hasColumn('designations', 'name');
        $hasDesignation = Schema::hasColumn('designations', 'designation');

        if ($hasDesignation && !$hasName) {
            DB::statement('ALTER TABLE `designations` CHANGE `designation` `name` VARCHAR(191) NOT NULL');
        }

        if (!Schema::hasColumn('designations', 'abbr')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->string('abbr', 20)->nullable()->after('name');
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
        if (!Schema::hasTable('designations')) {
            return;
        }

        if (Schema::hasColumn('designations', 'abbr')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->dropColumn('abbr');
            });
        }

        $hasName = Schema::hasColumn('designations', 'name');
        $hasDesignation = Schema::hasColumn('designations', 'designation');

        if ($hasName && !$hasDesignation) {
            DB::statement('ALTER TABLE `designations` CHANGE `name` `designation` VARCHAR(191) NOT NULL');
        }
    }
}
