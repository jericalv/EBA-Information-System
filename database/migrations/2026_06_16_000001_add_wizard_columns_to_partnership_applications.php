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
            if (! Schema::hasColumn('partnership_applications', 'wizard_status')) {
                $table->string('wizard_status')->default('loi_pending')->after('letter_of_intent_path');
            }

            if (! Schema::hasColumn('partnership_applications', 'loi_rejection_reason')) {
                $table->text('loi_rejection_reason')->nullable()->after('wizard_status');
            }

            if (! Schema::hasColumn('partnership_applications', 'form_rejection_reason')) {
                $table->text('form_rejection_reason')->nullable()->after('loi_rejection_reason');
            }

            if (! Schema::hasColumn('partnership_applications', 'docs_recommendation_checked')) {
                $table->boolean('docs_recommendation_checked')->default(false)->after('form_rejection_reason');
            }

            if (! Schema::hasColumn('partnership_applications', 'docs_notice_occupy_checked')) {
                $table->boolean('docs_notice_occupy_checked')->default(false)->after('docs_recommendation_checked');
            }

            if (! Schema::hasColumn('partnership_applications', 'docs_notice_termination_checked')) {
                $table->boolean('docs_notice_termination_checked')->default(false)->after('docs_notice_occupy_checked');
            }

            if (! Schema::hasColumn('partnership_applications', 'docs_moa_contract_checked')) {
                $table->boolean('docs_moa_contract_checked')->default(false)->after('docs_notice_termination_checked');
            }

            if (! Schema::hasColumn('partnership_applications', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('docs_moa_contract_checked');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            $columns = [
                'wizard_status',
                'loi_rejection_reason',
                'form_rejection_reason',
                'docs_recommendation_checked',
                'docs_notice_occupy_checked',
                'docs_notice_termination_checked',
                'docs_moa_contract_checked',
                'receipt_path',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('partnership_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
