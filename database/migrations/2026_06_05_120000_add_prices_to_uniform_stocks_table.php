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
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->json('prices')->nullable()->after('item_type');
        });

        DB::table('uniform_stocks')
            ->whereNotNull('sizes')
            ->update(['prices' => DB::raw('sizes')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->dropColumn('prices');
        });
    }
};
