<?php

namespace Tests\Feature;

use App\Actions\Shipping\CalculateShippingOptionsAction;
use App\Services\Shipping\MelhorEnvioService;
use Mockery;
use Tests\TestCase;

class ShippingActionTest extends TestCase
{
    public function test_shipping_action_returns_only_valid_paid_options(): void
    {
        $melhorEnvio = Mockery::mock(MelhorEnvioService::class);
        $melhorEnvio->shouldReceive('calcularFrete')->once()->andReturn([
            ['name' => 'PAC', 'price' => 19.90],
            ['name' => 'Indisponível', 'price' => 0],
            ['name' => 'Erro'],
        ]);

        $options = (new CalculateShippingOptionsAction($melhorEnvio))->execute('01001000');

        $this->assertCount(1, $options);
        $this->assertSame('PAC', $options->first()['name']);
    }
}
