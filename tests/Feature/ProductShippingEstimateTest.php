<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductShippingEstimateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_calculate_a_shipping_estimate_from_product_page(): void
    {
        $product = Product::factory()->create(['price' => 129.90]);

        config([
            'services.melhor_envio.url' => 'https://shipping.example/',
            'services.melhor_envio.token' => 'test-token',
            'services.melhor_envio.origin_zip' => '01001000',
        ]);
        Http::fake([
            'https://shipping.example/shipment/calculate' => Http::response([
                [
                    'id' => 1,
                    'name' => 'PAC',
                    'price' => 19.90,
                    'delivery_time' => 5,
                ],
            ]),
        ]);

        $this->postJson(route('frete.calcular'), [
            'cep' => '03657060',
            'product_id' => $product->id,
        ])
            ->assertOk()
            ->assertJsonPath('0.name', 'PAC')
            ->assertJsonPath('0.price', 19.90);

        Http::assertSent(fn ($request) => $request['from']['postal_code'] === '01001000');
    }
}
