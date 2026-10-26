<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\MelhorEnvioService;
use App\Services\Shipping\ShipmentStatusMapper;
use App\UseCases\Shipping\ProcessMelhorEnvioWebhook;
use App\UseCases\Shipping\SyncShipmentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ShippingUseCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_synchronizes_a_registered_shipment(): void
    {
        $shipment = $this->shipment(['shipment_id' => 'provider-1']);
        $provider = Mockery::mock(MelhorEnvioService::class);
        $provider->shouldReceive('consultarPedido')->once()->with('provider-1')->andReturn([[
            'id' => 'provider-1',
            'status' => 'posted',
            'tracking' => 'BR123',
        ]]);

        (new SyncShipmentStatus($provider, new ShipmentStatusMapper))->execute($shipment->id);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'in_transit',
            'tracking_code' => 'BR123',
        ]);
        $this->assertNotNull($shipment->fresh()->shipped_at);
    }

    public function test_it_processes_a_melhor_envio_webhook(): void
    {
        $shipment = $this->shipment(['shipment_id' => 'provider-2']);

        (new ProcessMelhorEnvioWebhook(new ShipmentStatusMapper))->execute([
            'id' => 'provider-2',
            'status' => 'delivered',
            'tracking' => 'BR456',
            'label' => 'https://example.test/label.pdf',
        ]);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'delivered',
            'tracking_code' => 'BR456',
            'label_url' => 'https://example.test/label.pdf',
        ]);
    }

    private function shipment(array $attributes = []): Shipment
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'recipient_name' => 'Cliente',
            'street' => 'Rua A',
            'number' => '1',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 10,
            'total' => 110,
            'status' => 'paid',
        ]);

        return Shipment::create($attributes + [
            'order_id' => $order->id,
            'carrier' => 'Correios',
            'status' => 'pending',
        ]);
    }
}
