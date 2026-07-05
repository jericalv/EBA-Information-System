<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeInvalidUserRoles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $validRoles = ['admin', 'cashier', 'concessionaire', 'student'];

        DB::table('users')
            ->whereNull('role')
            ->orWhereNotIn('role', $validRoles)
            ->update([
                'role' => 'concessionaire',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: previous invalid role values are intentionally not restored.
    }
}
