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
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->enum('item_type', ['books', 'uniforms'])->nullable()->default(null)->after('image');
            $table->json('sizes')->nullable()->after('item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uniform_stocks', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'sizes']);
        });
    }
};
