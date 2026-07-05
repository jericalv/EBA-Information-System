<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a concessionaire user
        $concessionaire = User::where('role', 'concessionaire')->first();
        
        if (!$concessionaire) {
            $concessionaire = User::create([
                'name' => 'Campus Canteen',
                'email' => 'canteen@example.com',
                'password' => bcrypt('password'),
                'role' => 'concessionaire',
                'business_name' => 'Campus Canteen',
            ]);
        }

        $products = [
            [
                'name' => 'Chicken Adobo',
                'description' => 'Classic Filipino chicken adobo braised in soy sauce and vinegar with garlic. Served with steamed rice.',
                'price' => 65.00,
                'category' => 'food',
            ],
            [
                'name' => 'Sinigang na Baboy',
                'description' => 'Traditional sour soup with pork and vegetables in tamarind broth. Perfect for rainy days.',
                'price' => 75.00,
                'category' => 'food',
            ],
            [
                'name' => 'Fried Chicken',
                'description' => 'Crispy golden fried chicken served with steamed rice and gravy.',
                'price' => 55.00,
                'category' => 'food',
            ],
            [
                'name' => 'Pancit Canton',
                'description' => 'Stir-fried egg noodles with vegetables, pork, and shrimp.',
                'price' => 45.00,
                'category' => 'food',
            ],
            [
                'name' => 'Burger Steak',
                'description' => 'Juicy beef patties smothered in mushroom gravy. Comes with rice.',
                'price' => 50.00,
                'category' => 'food',
            ],
            [
                'name' => 'Iced Coffee',
                'description' => 'Refreshing cold brewed coffee served over ice.',
                'price' => 35.00,
                'category' => 'beverage',
            ],
            [
                'name' => 'Fresh Lemonade',
                'description' => 'Freshly squeezed lemonade with a hint of honey.',
                'price' => 25.00,
                'category' => 'beverage',
            ],
            [
                'name' => 'Mango Shake',
                'description' => 'Creamy mango shake made with fresh Philippine mangoes.',
                'price' => 40.00,
                'category' => 'beverage',
            ],
            [
                'name' => 'Turon',
                'description' => 'Sweet fried banana spring rolls with caramelized sugar coating.',
                'price' => 15.00,
                'category' => 'snack',
            ],
            [
                'name' => 'Empanada',
                'description' => 'Savory pastry filled with seasoned ground meat and vegetables.',
                'price' => 20.00,
                'category' => 'snack',
            ],
            [
                'name' => 'French Fries',
                'description' => 'Crispy golden french fries served with ketchup.',
                'price' => 30.00,
                'category' => 'snack',
            ],
            [
                'name' => 'Fish and Chips',
                'description' => 'Battered fish fillet served with crispy fries and tartar sauce.',
                'price' => 85.00,
                'category' => 'food',
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'concessionaire_id' => $concessionaire->id,
                ...$product,
                'is_available' => true,
            ]);
        }
    }
}
