<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;

class ProcessCardPaymentAction
{
    public function __construct(private AsaasService $asaas) {}

    public function execute(Order $order, array $cardData): array
    {
        $payment = $this->asaas->createCardPayment($order, $cardData);
        $status = $payment['status'] ?? 'PENDING';
        $order->update(['gateway_payment_id' => $payment['id'] ?? null, 'gateway_status' => $status, 'status' => $status === 'CONFIRMED' ? 'paid' : 'pending', 'payment_method' => 'card', 'paid_at' => $status === 'CONFIRMED' ? now() : null]);

        Log::info('Pagamento com cartão processado.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status, 'order_status' => $order->status]);

        return $payment;
    }
}
