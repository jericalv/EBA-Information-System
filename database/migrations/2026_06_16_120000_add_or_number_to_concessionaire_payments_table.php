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
            if (! Schema::hasColumn('concessionaire_payments', 'or_number')) {
                $table->string('or_number')->nullable()->after('reference_number');
            }
        });

        if (Schema::hasColumn('concessionaire_payments', 'reference_number')) {
            DB::statement("UPDATE concessionaire_payments SET or_number = reference_number WHERE or_number IS NULL AND reference_number IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concessionaire_payments', function (Blueprint $table) {
            if (Schema::hasColumn('concessionaire_payments', 'or_number')) {
                $table->dropColumn('or_number');
            }
        });
    }
};
