<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_variant_combination_is_unique_for_a_product(): void
    {
        $product = Product::factory()->create();

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => 'Off-white',
            'size' => 'M',
        ]);

        $this->expectException(QueryException::class);

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => 'Off-white',
            'size' => 'M',
        ]);
    }

    public function test_gateway_payment_id_is_unique_for_orders(): void
    {
        $gatewayPaymentId = 'payment-'.fake()->uuid();
        $this->createOrder($gatewayPaymentId);

        $this->expectException(QueryException::class);

        $this->createOrder($gatewayPaymentId);
    }

    public function test_an_order_can_have_only_one_shipment(): void
    {
        $order = $this->createOrder();

        Shipment::create([
            'order_id' => $order->id,
            'shipment_id' => 'shipment-1',
            'status' => 'pending',
        ]);

        $this->expectException(QueryException::class);

        Shipment::create([
            'order_id' => $order->id,
            'shipment_id' => 'shipment-2',
            'status' => 'pending',
        ]);
    }

    public function test_deleting_a_variant_preserves_the_order_item_snapshot(): void
    {
        $variant = ProductVariant::factory()->create();
        $order = $this->createOrder();

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => 'Produto histórico',
            'image_snapshot' => 'produto.png',
            'color_snapshot' => $variant->color,
            'size_snapshot' => $variant->size,
            'price' => 99.90,
            'quantity' => 1,
        ]);

        $variant->delete();

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'product_variant_id' => null,
            'name_snapshot' => 'Produto histórico',
        ]);
    }

    private function createOrder(?string $gatewayPaymentId = null): Order
    {
        return Order::create([
            'user_id' => User::factory()->create()->id,
            'recipient_name' => 'Cliente de teste',
            'street' => 'Rua das Flores',
            'number' => '100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 99.90,
            'shipping' => 10,
            'total' => 109.90,
            'status' => 'pending',
            'gateway_payment_id' => $gatewayPaymentId ?? 'payment-'.fake()->uuid(),
        ]);
    }
}
