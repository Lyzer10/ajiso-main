<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('dispute_statuses')
            ->whereRaw('LOWER(dispute_status) = ?', ['non-compliance'])
            ->exists();

        if (!$exists) {
            DB::table('dispute_statuses')->insert([
                'dispute_status' => 'non-compliance',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('dispute_statuses')
            ->whereRaw('LOWER(dispute_status) = ?', ['non-compliance'])
            ->delete();
    }
};
