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
            if (! Schema::hasColumn('partnership_applications', 'faculty_recommendation')) {
                $table->enum('faculty_recommendation', ['recommend_approval', 'recommend_rejection'])
                    ->nullable()
                    ->after('admin_notes');
            }

            if (! Schema::hasColumn('partnership_applications', 'faculty_notes')) {
                $table->text('faculty_notes')->nullable()->after('faculty_recommendation');
            }

            if (! Schema::hasColumn('partnership_applications', 'reviewed_by')) {
                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->after('faculty_notes')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (Schema::hasColumn('partnership_applications', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            if (Schema::hasColumn('partnership_applications', 'faculty_notes')) {
                $table->dropColumn('faculty_notes');
            }

            if (Schema::hasColumn('partnership_applications', 'faculty_recommendation')) {
                $table->dropColumn('faculty_recommendation');
            }
        });
    }
};
