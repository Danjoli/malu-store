<?php

namespace Tests\Feature;

use App\Exceptions\Domain\InsufficientStockException;
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

        $product = Product::factory()
            ->for(Category::factory())
            ->create();

        $variant = ProductVariant::factory()
            ->for($product)
            ->create([
                'stock' => 10,
            ]);

        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Teste',
            'street' => 'Rua A',
            'number' => '1',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'status' => 'pending',
            'gateway_payment_id' => 'pay_test',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'price' => 100,
            'quantity' => 2,
        ]);

        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'price' => 100,
            'quantity' => 1,
        ]);

        $payload = [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => [
                'id' => 'pay_test',
                'billingType' => 'PIX',
            ],
        ];

        app(AsaasWebhookService::class)->handleAsaas($payload);
        app(AsaasWebhookService::class)->handleAsaas($payload);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
            'payment_method' => 'pix',
        ]);

        $this->assertSame(8, $variant->fresh()->stock);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_paid_webhook_does_not_mark_order_as_paid_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 1]);
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Teste',
            'street' => 'Rua A',
            'number' => '1',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'status' => 'pending',
            'gateway_payment_id' => 'pay_without_stock',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'price' => 100,
            'quantity' => 2,
        ]);

        try {
            app(AsaasWebhookService::class)->handleAsaas([
                'event' => 'PAYMENT_RECEIVED',
                'payment' => ['id' => 'pay_without_stock', 'billingType' => 'PIX'],
            ]);
            $this->fail('A confirmação deveria falhar sem estoque suficiente.');
        } catch (InsufficientStockException) {
            $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
            $this->assertSame(1, $variant->fresh()->stock);
        }
    }

    public function test_checkout_paid_webhook_marks_card_order_as_paid_only_once(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 5]);
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Teste',
            'street' => 'Rua A',
            'number' => '1',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'status' => 'pending_payment',
            'gateway_payment_id' => 'checkout_test',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'price' => 100,
            'quantity' => 2,
        ]);
        $payload = [
            'id' => 'evt_checkout_paid',
            'event' => 'CHECKOUT_PAID',
            'checkout' => [
                'id' => 'checkout_test',
                'externalReference' => (string) $order->id,
                'status' => 'PAID',
            ],
        ];

        app(AsaasWebhookService::class)->handleAsaas($payload);
        app(AsaasWebhookService::class)->handleAsaas($payload);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
            'payment_method' => 'card',
            'gateway_status' => 'PAID',
        ]);
        $this->assertSame(3, $variant->fresh()->stock);
    }

    public function test_checkout_cancelled_and_expired_events_update_the_order_journey(): void
    {
        $user = User::factory()->create();

        foreach ([
            ['event' => 'CHECKOUT_CANCELED', 'status' => 'cancelled', 'gateway' => 'CANCELED'],
            ['event' => 'CHECKOUT_EXPIRED', 'status' => 'expired', 'gateway' => 'EXPIRED'],
        ] as $scenario) {
            $order = Order::create([
                'user_id' => $user->id,
                'recipient_name' => 'Teste',
                'street' => 'Rua A',
                'number' => '1',
                'city' => 'São Paulo',
                'state' => 'SP',
                'cep' => '01001000',
                'subtotal' => 100,
                'shipping' => 0,
                'total' => 100,
                'status' => 'pending_payment',
                'gateway_payment_id' => 'checkout_'.strtolower($scenario['gateway']),
            ]);

            app(AsaasWebhookService::class)->handleAsaas([
                'id' => 'evt_'.strtolower($scenario['gateway']),
                'event' => $scenario['event'],
                'checkout' => [
                    'id' => $order->gateway_payment_id,
                    'externalReference' => (string) $order->id,
                ],
            ]);

            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'status' => $scenario['status'],
                'gateway_status' => $scenario['gateway'],
            ]);
        }
    }
}
