<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CorrectStudentRoleReassignment extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->leftJoin('partnership_applications as pa', 'pa.user_id', '=', 'users.id')
            ->where('users.role', 'concessionaire')
            ->whereNull('pa.id')
            ->update([
                'users.role' => 'student',
                'users.updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we cannot reliably distinguish restored students from existing students.
    }
}
