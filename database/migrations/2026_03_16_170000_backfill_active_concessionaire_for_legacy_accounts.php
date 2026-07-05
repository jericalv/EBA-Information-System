<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'concessionaire')
            ->where(function ($query) {
                $query->whereNull('is_active_concessionaire')
                    ->orWhere('is_active_concessionaire', false);
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('partnership_applications')
                    ->whereColumn('partnership_applications.user_id', 'users.id')
                    ->whereNotNull('partnership_applications.contract_period_end');
            })
            ->update(['is_active_concessionaire' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfill is intentionally non-reversible to avoid disabling active accounts.
    }
};
