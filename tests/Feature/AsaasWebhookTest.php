<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Public\Payment\AsaasWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsaasWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_received_webhook_decrements_stock_only_once(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 10]);
        $order = Order::create(['user_id' => $user->id, 'recipient_name' => 'Teste', 'street' => 'Rua A', 'number' => '1', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000', 'subtotal' => 100, 'shipping' => 0, 'total' => 100, 'status' => 'pending', 'gateway_payment_id' => 'pay_test']);
        OrderItem::create(['order_id' => $order->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'price' => 100, 'quantity' => 2]);
        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'price' => 100, 'quantity' => 1]);
        $payload = ['event' => 'PAYMENT_RECEIVED', 'payment' => ['id' => 'pay_test', 'billingType' => 'PIX']];
        app(AsaasWebhookService::class)->handleAsaas($payload);
        app(AsaasWebhookService::class)->handleAsaas($payload);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid', 'payment_method' => 'pix']);
        $this->assertSame(8, $variant->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);
    }
}
