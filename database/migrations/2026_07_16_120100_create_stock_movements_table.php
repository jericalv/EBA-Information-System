<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_stock_id')->constrained('uniform_stocks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20); // initial | restock | correction | edit | sale
            $table->string('size', 5)->nullable();
            $table->integer('quantity_change');
            // Remaining stock after the movement: per-size count when `size` is set, total otherwise.
            $table->unsignedInteger('quantity_after');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['uniform_stock_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
