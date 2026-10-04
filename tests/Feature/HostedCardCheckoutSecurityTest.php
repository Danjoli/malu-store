<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HostedCardCheckoutSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_checkout_sends_only_order_data_and_never_card_secrets(): void
    {
        Http::fake(['*' => Http::response([
            'id' => 'checkout_secure_1',
            'link' => 'https://sandbox.asaas.com/checkoutSession/show/checkout_secure_1',
            'status' => 'ACTIVE',
        ])]);

        $user = User::factory()->create(['asaas_customer_id' => 'cus_test_1']);
        $product = Product::factory()->for(Category::factory())->create();
        $variant = ProductVariant::factory()->for($product)->create();
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Cliente Teste',
            'phone' => '11999999999',
            'cpf' => '12345678909',
            'street' => 'Rua A',
            'number' => '10',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 20,
            'total' => 120,
            'status' => 'pending',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'price' => 100,
            'quantity' => 1,
        ]);

        app(AsaasService::class)->createCardCheckout($order);

        Http::assertSent(function (Request $request) use ($order): bool {
            $payload = $request->data();
            $serialized = json_encode($payload, JSON_THROW_ON_ERROR);

            return str_ends_with($request->url(), '/checkouts')
                && $payload['billingTypes'] === ['CREDIT_CARD']
                && $payload['externalReference'] === (string) $order->id
                && ! str_contains($serialized, 'creditCard')
                && ! str_contains($serialized, 'card_number')
                && ! str_contains($serialized, 'ccv');
        });
    }

    public function test_checkout_source_contains_no_card_number_or_security_code_fields(): void
    {
        $template = file_get_contents(resource_path('views/components/public/checkout/payment-section.blade.php'));

        $this->assertIsString($template);
        $this->assertStringNotContainsString('card_number', $template);
        $this->assertStringNotContainsString('ccv', strtolower($template));
    }
}
