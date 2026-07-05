<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            $table->unsignedInteger('contract_period_edit_count')->default(0)->after('contract_period_end');
            $table->unsignedInteger('contract_period_last_edited_year')->nullable()->after('contract_period_edit_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            $table->dropColumn(['contract_period_edit_count', 'contract_period_last_edited_year']);
        });
    }
};
