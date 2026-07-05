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
            $table->timestamp('loi_submitted_at')->nullable()->after('letter_of_intent_path');
            $table->timestamp('form_submitted_at')->nullable()->after('loi_submitted_at');
            $table->timestamp('receipt_submitted_at')->nullable()->after('receipt_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            $table->dropColumn([
                'loi_submitted_at',
                'form_submitted_at',
                'receipt_submitted_at',
            ]);
        });
    }
};
