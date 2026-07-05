<?php

namespace Database\Seeders;

use App\Models\UniformStock;
use Illuminate\Database\Seeder;

class UniformStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['item_name' => 'Polo', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'Blouse', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'Slacks', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'PE Shirt', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'PE Pants', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'NSTP Shirt', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
            ['item_name' => 'Books', 'icon' => null, 'quantity' => 0, 'is_visible' => true],
        ];

        foreach ($items as $item) {
            UniformStock::updateOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }
    }
}
