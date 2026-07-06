<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('concessionaire_payments', function (Blueprint $table) {
            // The billing month this payment settles (first day of that month).
            // Kept separate from payment_date, which is when the cash was received,
            // so back payments and advance payments can be recorded accurately.
            $table->date('period_month')->nullable()->after('payment_date');
        });

        // Backfill legacy rows: the covered month is the payment_date's month.
        DB::table('concessionaire_payments')
            ->whereNull('period_month')
            ->update(['period_month' => DB::raw("DATE_FORMAT(payment_date, '%Y-%m-01')")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concessionaire_payments', function (Blueprint $table) {
            $table->dropColumn('period_month');
        });
    }
};
