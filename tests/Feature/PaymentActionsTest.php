<?php

namespace Tests\Feature;

use App\Actions\Payment\CreateBoletoPaymentAction;
use App\Actions\Payment\CreatePixPaymentAction;
use App\Actions\Payment\ProcessCardPaymentAction;
use App\Models\Order;
use App\Models\User;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentActionsTest extends TestCase
{
    use RefreshDatabase;

    private function order(): Order
    {
        return Order::create(['user_id' => User::factory()->create()->id, 'recipient_name' => 'Teste', 'street' => 'Rua A', 'number' => '1', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000', 'subtotal' => 100, 'shipping' => 0, 'total' => 100, 'status' => 'pending']);
    }

    public function test_pix_payment_updates_order_without_real_api_call(): void
    {
        $asaas = Mockery::mock(AsaasService::class);
        $asaas->shouldReceive('createPixPayment')->once()->andReturn(['id' => 'pix_1', 'status' => 'PENDING']);
        $asaas->shouldReceive('getPixQrCode')->once()->with('pix_1')->andReturn(['encodedImage' => 'base64', 'payload' => 'pix-payload']);
        $order = $this->order();
        $result = (new CreatePixPaymentAction($asaas))->execute($order);
        $this->assertSame('pix-payload', $result['qr_code']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'gateway_payment_id' => 'pix_1', 'payment_method' => 'pix', 'status' => 'pending']);
    }

    public function test_boleto_payment_updates_order_without_real_api_call(): void
    {
        $asaas = Mockery::mock(AsaasService::class);
        $asaas->shouldReceive('createBoletoPayment')->once()->andReturn(['id' => 'boleto_1', 'status' => 'PENDING', 'dueDate' => '2026-09-01']);
        $order = $this->order();
        (new CreateBoletoPaymentAction($asaas))->execute($order);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'gateway_payment_id' => 'boleto_1', 'payment_method' => 'boleto', 'status' => 'pending']);
    }

    public function test_confirmed_card_payment_marks_order_as_paid_without_real_api_call(): void
    {
        $asaas = Mockery::mock(AsaasService::class);
        $asaas->shouldReceive('createCardPayment')->once()->andReturn(['id' => 'card_1', 'status' => 'CONFIRMED']);
        $order = $this->order();
        (new ProcessCardPaymentAction($asaas))->execute($order, ['holderName' => 'Teste']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'gateway_payment_id' => 'card_1', 'payment_method' => 'card', 'status' => 'paid']);
        $this->assertNotNull($order->fresh()->paid_at);
    }
}
