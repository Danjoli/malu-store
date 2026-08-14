<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Public\Checkout\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_address_order_items_and_shipment(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create(['price' => 99.90]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 5]);
        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'color_snapshot' => $variant->color, 'size_snapshot' => $variant->size, 'price' => 99.90, 'quantity' => 2]);

        $order = $this->actingAs($user)->app->make(CheckoutService::class)->process([
            'recipient_name' => 'Cliente Teste', 'phone' => '11999999999', 'cpf' => '123.456.789-09',
            'street' => 'Rua Teste', 'number' => '100', 'complement' => '', 'neighborhood' => 'Centro',
            'city' => 'São Paulo', 'state' => 'sp', 'cep' => '01001-000', 'shipping_cost' => 15.50,
            'carrier' => 'Correios', 'service' => 'PAC',
        ]);

        $this->assertDatabaseHas('addresses', ['user_id' => $user->id, 'cep' => '01001000']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total' => 215.30, 'status' => 'pending']);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'quantity' => 2]);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'carrier' => 'Correios']);
    }
}
