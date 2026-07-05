<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each applicant may now upload up to 5 pictures per document. The
     * ordered list of stored paths lives in these JSON columns while the
     * existing single-path columns keep pointing at the first file for
     * backward compatibility with older views and admin flows.
     */
    public function up(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('partnership_applications', 'letter_of_intent_paths')) {
                $table->json('letter_of_intent_paths')->nullable()->after('letter_of_intent_path');
            }

            if (! Schema::hasColumn('partnership_applications', 'valid_id_paths')) {
                $table->json('valid_id_paths')->nullable()->after('valid_id_path');
            }

            if (! Schema::hasColumn('partnership_applications', 'business_permit_paths')) {
                $table->json('business_permit_paths')->nullable()->after('business_permit_path');
            }

            if (! Schema::hasColumn('partnership_applications', 'receipt_paths')) {
                $table->json('receipt_paths')->nullable()->after('receipt_path');
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
                'letter_of_intent_paths',
                'valid_id_paths',
                'business_permit_paths',
                'receipt_paths',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('partnership_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
