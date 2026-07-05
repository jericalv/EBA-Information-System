<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessDetailFieldsToPartnershipApplications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('partnership_applications', 'products_services')) {
                $table->text('products_services')->nullable()->after('business_name');
            }

            if (! Schema::hasColumn('partnership_applications', 'student_benefit')) {
                $table->text('student_benefit')->nullable()->after('products_services');
            }

            if (! Schema::hasColumn('partnership_applications', 'unique_selling_point')) {
                $table->text('unique_selling_point')->nullable()->after('student_benefit');
            }

            if (! Schema::hasColumn('partnership_applications', 'expected_price_range')) {
                $table->string('expected_price_range')->nullable()->after('unique_selling_point');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (Schema::hasColumn('partnership_applications', 'expected_price_range')) {
                $table->dropColumn('expected_price_range');
            }

            if (Schema::hasColumn('partnership_applications', 'unique_selling_point')) {
                $table->dropColumn('unique_selling_point');
            }

            if (Schema::hasColumn('partnership_applications', 'student_benefit')) {
                $table->dropColumn('student_benefit');
            }

            if (Schema::hasColumn('partnership_applications', 'products_services')) {
                $table->dropColumn('products_services');
            }
        });
    }
}