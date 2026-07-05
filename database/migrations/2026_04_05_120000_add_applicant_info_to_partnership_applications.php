<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicantInfoToPartnershipApplications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('partnership_applications', 'first_name')) {
                $table->string('first_name')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('partnership_applications', 'last_name')) {
                $table->string('last_name')->nullable()->after('middle_name');
            }

            if (! Schema::hasColumn('partnership_applications', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('business_name');
            }

            if (! Schema::hasColumn('partnership_applications', 'business_proposal')) {
                $table->text('business_proposal')->nullable()->after('phone_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (Schema::hasColumn('partnership_applications', 'phone_number')) {
                $table->dropColumn('phone_number');
            }

            if (Schema::hasColumn('partnership_applications', 'business_proposal')) {
                $table->dropColumn('business_proposal');
            }
        });
    }
}
