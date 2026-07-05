<?php

use App\Models\Product;
use App\Models\User;

test('deleted concessionaire products are removed from the public products index', function () {
    $concessionaire = User::factory()->create([
        'role' => 'concessionaire',
        'is_active_concessionaire' => true,
        'business_name' => 'Test Canteen',
    ]);

    $otherConcessionaire = User::factory()->create([
        'role' => 'concessionaire',
        'is_active_concessionaire' => true,
        'business_name' => 'Other Canteen',
    ]);

    $deletedProduct = Product::create([
        'concessionaire_id' => $concessionaire->id,
        'name' => 'Delete Me Product',
        'description' => 'Temporary product for deletion test',
        'price' => 99.00,
        'category' => 'food',
        'is_available' => true,
    ]);

    $keptProduct = Product::create([
        'concessionaire_id' => $otherConcessionaire->id,
        'name' => 'Keep Me Product',
        'description' => 'Product that should remain visible',
        'price' => 49.00,
        'category' => 'food',
        'is_available' => true,
    ]);

    $this->actingAs($concessionaire)
        ->delete(route('concessionaire.products.destroy', $deletedProduct))
        ->assertRedirect(route('concessionaire.products'));

    $this->assertDatabaseMissing('products', [
        'id' => $deletedProduct->id,
    ]);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertDontSee($deletedProduct->name)
        ->assertSee($keptProduct->name);
});
