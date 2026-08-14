<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndFavoritesTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        $product = Product::factory()->for(Category::factory())->create();
        ProductVariant::factory()->for($product)->create(['stock' => 10]);

        return $product;
    }

    public function test_user_can_add_update_and_remove_cart_item(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $variant = $product->variants()->first();
        $this->actingAs($user)->post(route('cart.add'), ['variant_id' => $variant->id, 'quantity' => 1])->assertSessionHas('success');
        $item = CartItem::firstOrFail();
        $this->put(route('cart.update', $item), ['quantity' => 3])->assertRedirect(route('public.cart.index'));
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);
        $this->delete(route('cart.remove', $item))->assertRedirect(route('public.cart.index'));
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_user_can_toggle_favorite(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $this->actingAs($user)->post(route('favorites.toggle', $product))->assertSessionHas('success');
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);
        $this->post(route('favorites.toggle', $product))->assertSessionHas('success');
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);
    }
}
