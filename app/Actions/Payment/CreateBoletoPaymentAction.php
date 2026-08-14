<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;

class CreateBoletoPaymentAction
{
    public function __construct(private AsaasService $asaas) {}

    public function execute(Order $order): array
    {
        $payment = $this->asaas->createBoletoPayment($order);
        $order->update(['gateway_payment_id' => $payment['id'] ?? null, 'gateway_status' => $payment['status'] ?? 'PENDING', 'status' => 'pending', 'payment_method' => 'boleto', 'expires_at' => isset($payment['dueDate']) ? $payment['dueDate'].' 23:59:59' : null]);

        Log::info('Cobrança de boleto criada.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status]);

        return $payment;
    }
}
