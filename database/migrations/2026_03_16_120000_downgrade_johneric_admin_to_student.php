<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'tmc.johneric.alvarado@cvsu.edu.ph')
            ->where('role', 'admin')
            ->update(['role' => 'cashier']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('email', 'tmc.johneric.alvarado@cvsu.edu.ph')
            ->where('role', 'cashier')
            ->update(['role' => 'admin']);
    }
};
