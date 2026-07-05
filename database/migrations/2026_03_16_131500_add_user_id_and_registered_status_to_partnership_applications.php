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
        Schema::table('partnership_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('partnership_applications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE partnership_applications MODIFY status ENUM('pending','approved','rejected','registered') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE partnership_applications MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('partnership_applications', function (Blueprint $table) {
            if (Schema::hasColumn('partnership_applications', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
