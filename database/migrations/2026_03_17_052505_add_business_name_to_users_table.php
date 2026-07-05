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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'business_name')) {
                $table->string('business_name')->nullable()->after('name');
            }
        });

        if (! Schema::hasTable('partnership_applications')) {
            return;
        }

        $concessionaireUserIds = DB::table('users')
            ->where('role', 'concessionaire')
            ->whereNull('business_name')
            ->pluck('id');

        foreach ($concessionaireUserIds as $userId) {
            $latestBusinessName = DB::table('partnership_applications')
                ->where('user_id', $userId)
                ->whereNotNull('business_name')
                ->orderByDesc('created_at')
                ->value('business_name');

            if ($latestBusinessName) {
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['business_name' => $latestBusinessName]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'business_name')) {
                $table->dropColumn('business_name');
            }
        });
    }
};
