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
            if (! Schema::hasColumn('partnership_applications', 'address')) {
                $table->string('address')->nullable()->after('business_name');
            }

            if (! Schema::hasColumn('partnership_applications', 'type_of_business')) {
                $table->string('type_of_business')->nullable()->after('address');
            }

            if (! Schema::hasColumn('partnership_applications', 'proposed_location')) {
                $table->string('proposed_location')->nullable()->after('type_of_business');
            }

            if (! Schema::hasColumn('partnership_applications', 'proposed_duration')) {
                $table->string('proposed_duration')->nullable()->after('proposed_location');
            }

            if (! Schema::hasColumn('partnership_applications', 'is_previous_concessionaire')) {
                $table->boolean('is_previous_concessionaire')->nullable()->default(false)->after('proposed_duration');
            }

            if (! Schema::hasColumn('partnership_applications', 'previous_location_year')) {
                $table->string('previous_location_year')->nullable()->after('is_previous_concessionaire');
            }

            if (! Schema::hasColumn('partnership_applications', 'certification_agreed')) {
                $table->boolean('certification_agreed')->default(false)->after('previous_location_year');
            }

            if (! Schema::hasColumn('partnership_applications', 'valid_id_path')) {
                $table->string('valid_id_path')->nullable()->after('certification_agreed');
            }

            if (! Schema::hasColumn('partnership_applications', 'business_permit_path')) {
                $table->string('business_permit_path')->nullable()->after('valid_id_path');
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
                'address',
                'type_of_business',
                'proposed_location',
                'proposed_duration',
                'is_previous_concessionaire',
                'previous_location_year',
                'certification_agreed',
                'valid_id_path',
                'business_permit_path',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('partnership_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
