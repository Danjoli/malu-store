<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Jobs\GenerateShipmentLabel;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Admin\Shipments\ShipmentService;
use App\Services\Shipping\MelhorEnvioService;
use App\Services\Shipping\ShipmentStatusMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ShipmentLabelPayloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_request_queues_label_generation_without_waiting_for_provider(): void
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('Senha@2026'),
            'is_active' => true,
            'role' => AdminRole::SuperAdmin,
        ]);

        Queue::fake();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.shipments.gerar', 999))
            ->assertRedirect()
            ->assertSessionHas('success');

        Queue::assertPushed(
            GenerateShipmentLabel::class,
            fn (GenerateShipmentLabel $job): bool => $job->shipmentId === 999,
        );
    }

    public function test_label_generation_uses_the_order_address_snapshot(): void
    {
        config()->set('services.melhor_envio.origin_zip', '01001000');
        config()->set('services.melhor_envio.sender', [
            'name' => 'Malu Store',
            'phone' => '11999999999',
            'email' => 'remetente@example.test',
            'document' => '12345678000190',
            'address' => 'Rua da Loja',
            'number' => '10',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state_abbr' => 'SP',
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Cliente do Pedido',
            'phone' => '11999999999',
            'cpf' => '12345678909',
            'street' => 'Rua Preservada',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 15,
            'total' => 115,
            'status' => 'paid',
        ]);
        $shipment = Shipment::create([
            'order_id' => $order->id,
            'carrier' => 'Correios',
            'service_id' => '1',
            'status' => 'pending',
        ]);

        $melhorEnvio = Mockery::mock(MelhorEnvioService::class);
        $melhorEnvio->shouldReceive('adicionarAoCarrinho')
            ->once()
            ->with(Mockery::on(function (array $payload): bool {
                return $payload['to']['name'] === 'Cliente do Pedido'
                    && $payload['to']['address'] === 'Rua Preservada'
                    && $payload['to']['postal_code'] === '01001000';
            }))
            ->andReturn(['id' => 'shipment-provider-id']);
        $melhorEnvio->shouldReceive('comprarEtiqueta')->once()->andReturn([]);
        $melhorEnvio->shouldReceive('gerarEtiqueta')->once()->andReturn([]);
        $melhorEnvio->shouldReceive('consultarPedido')->once()->andReturn([
            ['tracking' => 'BR000000001', 'status' => 'created'],
        ]);
        $melhorEnvio->shouldReceive('imprimirEtiqueta')->once()->andReturn([
            'url' => 'https://example.test/label.pdf',
        ]);

        (new ShipmentService($melhorEnvio, new ShipmentStatusMapper))
            ->generateLabel($shipment->id);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'shipment_id' => 'shipment-provider-id',
            'tracking_code' => 'BR000000001',
            'status' => 'waiting_post',
        ]);
    }
}
