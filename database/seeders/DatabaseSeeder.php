<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@eba.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create a pending concessionaire test account
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'concessionaire',
            'is_approved' => false,
            'is_active_concessionaire' => false,
        ]);

        $this->call([
            UniformStockSeeder::class,
        ]);
    }
}
