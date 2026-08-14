<?php

namespace App\Actions\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class UpdateOrderFromAsaasWebhookAction
{
    public function __construct(private FinalizePaidOrderAction $finalizePaidOrder) {}

    public function execute(array $data, string $orderStatus, string $gatewayStatus): void
    {
        $payment = $data['payment'] ?? [];
        $paymentId = $payment['id'] ?? null;
        $reference = $payment['externalReference'] ?? null;
        if (! $paymentId && ! $reference) {
            Log::warning('Webhook Asaas sem identificação de pagamento.');

            return;
        }
        $order = $paymentId ? Order::where('gateway_payment_id', $paymentId)->first() : null;
        $order ??= $reference ? Order::find($reference) : null;
        if (! $order) {
            Log::warning('Pedido não encontrado para webhook Asaas.', ['payment_id' => $paymentId, 'external_reference' => $reference]);

            return;
        }
        $wasAlreadyPaid = $order->status === 'paid';
        $method = match ($payment['billingType'] ?? null) {
            'PIX' => 'pix', 'BOLETO' => 'boleto', 'CREDIT_CARD' => 'card', default => isset($payment['billingType']) ? strtolower($payment['billingType']) : $order->payment_method
        };
        $updates = ['status' => $orderStatus, 'gateway_status' => $gatewayStatus, 'gateway_payment_id' => $paymentId ?? $order->gateway_payment_id, 'payment_method' => $method];
        if ($orderStatus === 'paid' && ! $order->paid_at) {
            $updates['paid_at'] = now();
        }
        $order->update($updates);
        if ($orderStatus === 'paid' && ! $wasAlreadyPaid) {
            $this->finalizePaidOrder->execute($order);
        }
        Log::info('Pedido atualizado via webhook Asaas.', ['order_id' => $order->id, 'status' => $orderStatus, 'gateway_status' => $gatewayStatus]);
    }
}
