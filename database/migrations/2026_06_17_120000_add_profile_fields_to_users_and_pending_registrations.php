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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (! Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix', 32)->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 32)->nullable()->after('email');
            }
        });

        Schema::table('pending_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('pending_registrations', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (! Schema::hasColumn('pending_registrations', 'suffix')) {
                $table->string('suffix', 32)->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('pending_registrations', 'phone_number')) {
                $table->string('phone_number', 32)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'suffix', 'phone_number']);
        });

        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'suffix', 'phone_number']);
        });
    }
};
