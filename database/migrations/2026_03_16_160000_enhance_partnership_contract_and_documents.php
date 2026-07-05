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
            if (!Schema::hasColumn('partnership_applications', 'contract_period_start')) {
                $table->date('contract_period_start')->nullable()->after('admin_notes');
            }

            if (!Schema::hasColumn('partnership_applications', 'contract_period_end')) {
                $table->date('contract_period_end')->nullable()->after('contract_period_start');
            }

            if (!Schema::hasColumn('partnership_applications', 'warning_30_sent')) {
                $table->boolean('warning_30_sent')->default(false)->after('contract_period_end');
            }

            if (!Schema::hasColumn('partnership_applications', 'warning_7_sent')) {
                $table->boolean('warning_7_sent')->default(false)->after('warning_30_sent');
            }

            if (!Schema::hasColumn('partnership_applications', 'warning_1_sent')) {
                $table->boolean('warning_1_sent')->default(false)->after('warning_7_sent');
            }

            if (!Schema::hasColumn('partnership_applications', 'moa_path')) {
                $table->string('moa_path')->nullable()->after('warning_1_sent');
            }

            if (!Schema::hasColumn('partnership_applications', 'contract_path')) {
                $table->string('contract_path')->nullable()->after('moa_path');
            }

            if (!Schema::hasColumn('partnership_applications', 'letter_of_intent_path')) {
                $table->string('letter_of_intent_path')->nullable()->after('contract_path');
            }
        });

        if (Schema::hasColumn('partnership_applications', 'letter_of_intent')
            && Schema::hasColumn('partnership_applications', 'letter_of_intent_path')) {
            DB::table('partnership_applications')
                ->whereNull('letter_of_intent_path')
                ->whereNotNull('letter_of_intent')
                ->update(['letter_of_intent_path' => DB::raw('letter_of_intent')]);
        }

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE partnership_applications MODIFY status ENUM('pending','approved','rejected','registered','expired') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE partnership_applications MODIFY status ENUM('pending','approved','rejected','registered') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('partnership_applications', function (Blueprint $table) {
            $columns = [
                'contract_period_start',
                'contract_period_end',
                'warning_30_sent',
                'warning_7_sent',
                'warning_1_sent',
                'moa_path',
                'contract_path',
                'letter_of_intent_path',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('partnership_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
