<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateBoletoPaymentAction
{
    public function __construct(
        private AsaasService $asaas,
        private OperationalAlertService $alerts,
    ) {}

    public function execute(Order $order): array
    {
        try {
            $payment = $this->asaas->createBoletoPayment($order);
            $order->update(['gateway_payment_id' => $payment['id'] ?? null, 'gateway_status' => $payment['status'] ?? 'PENDING', 'status' => 'pending', 'payment_method' => 'boleto', 'expires_at' => isset($payment['dueDate']) ? $payment['dueDate'].' 23:59:59' : null]);

            Log::info('Cobrança de boleto criada.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status]);

            return $payment;
        } catch (Throwable $exception) {
            $this->alerts->critical('Falha ao criar cobrança por boleto.', [
                'Pedido' => $order->id,
                'Tipo de erro' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
