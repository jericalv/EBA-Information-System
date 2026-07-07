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
        Schema::create('concessionaire_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
        });

        // Backfill any existing single carousel banner into the new gallery table.
        $now = now();
        DB::table('users')
            ->whereNotNull('carousel_image')
            ->where('carousel_image', '!=', '')
            ->orderBy('id')
            ->select('id', 'carousel_image')
            ->each(function ($user) use ($now) {
                DB::table('concessionaire_media')->insert([
                    'user_id' => $user->id,
                    'path' => $user->carousel_image,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concessionaire_media');
    }
};
