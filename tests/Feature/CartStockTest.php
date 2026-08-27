<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_add_more_items_than_available_stock(): void
    {
        $user = User::factory()->create();
        $variant = $this->variantWithStock(2);

        $this->actingAs($user)->post(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 3,
        ])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_cannot_update_another_customers_cart_item(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $variant = $this->variantWithStock(5);
        $cart = Cart::create(['user_id' => $owner->id, 'status' => 'active']);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $variant->product->name,
            'image_snapshot' => '',
            'color_snapshot' => $variant->color,
            'size_snapshot' => $variant->size,
            'price' => $variant->product->price,
            'quantity' => 1,
        ]);

        $this->actingAs($otherCustomer)->put(route('cart.update', $item), ['quantity' => 2])
            ->assertNotFound();

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 1]);
    }

    private function variantWithStock(int $stock): ProductVariant
    {
        $product = Product::factory()->for(Category::factory())->create();

        return ProductVariant::factory()->for($product)->create(['stock' => $stock]);
    }
}
