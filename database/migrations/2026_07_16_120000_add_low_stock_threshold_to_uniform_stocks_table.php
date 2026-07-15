<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->unsignedInteger('low_stock_threshold')->default(10)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });
    }
};
