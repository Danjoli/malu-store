<?php

namespace App\Services\Public\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class AsaasWebhookService
{
    public function handleAsaas(array $data): void
    {
        Log::info('Webhook Asaas recebido', [
            'event' => $data['event'] ?? null,
            'payment_id' => $data['payment']['id'] ?? null,
        ]);

        $event = $data['event'] ?? null;

        if (!$event) {
            Log::warning('Webhook Asaas sem evento.');

            return;
        }

        match ($event) {
            'PAYMENT_CREATED' => $this->paymentCreated($data),
            'PAYMENT_CONFIRMED' => $this->paymentConfirmed($data),
            'PAYMENT_RECEIVED' => $this->paymentReceived($data),
            'PAYMENT_OVERDUE' => $this->paymentOverdue($data),
            'PAYMENT_DELETED' => $this->paymentDeleted($data),
            'PAYMENT_REFUNDED' => $this->paymentRefunded($data),
            default => Log::info(
                'Evento Asaas não tratado.',
                ['event' => $event]
            ),
        };
    }

    /**
     * Pagamento criado.
     */
    protected function paymentCreated(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'pending',
            'PENDING'
        );
    }

    /**
     * Pagamento confirmado.
     */
    protected function paymentConfirmed(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'paid',
            'CONFIRMED'
        );
    }

    /**
     * Pagamento recebido.
     */
    protected function paymentReceived(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'paid',
            'RECEIVED'
        );
    }

    /**
     * Pagamento vencido.
     */
    protected function paymentOverdue(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'expired',
            'OVERDUE'
        );
    }

    /**
     * Pagamento excluído/cancelado.
     */
    protected function paymentDeleted(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'cancelled',
            'DELETED'
        );
    }

    /**
     * Pagamento estornado.
     */
    protected function paymentRefunded(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'refunded',
            'REFUNDED'
        );
    }

    /**
     * Atualiza o pedido relacionado ao pagamento.
     */
    protected function updateOrderStatus(
        array $data,
        string $orderStatus,
        string $gatewayStatus
    ): void {
        $payment = $data['payment'] ?? [];

        $paymentId = $payment['id'] ?? null;
        $externalReference = $payment['externalReference'] ?? null;

        if (!$paymentId && !$externalReference) {
            Log::warning(
                'Webhook Asaas sem payment ID ou externalReference.',
                $data
            );

            return;
        }

        $order = null;

        /*
        |--------------------------------------------------------------------------
        | Busca pelo ID do pagamento no Asaas
        |--------------------------------------------------------------------------
        */

        if ($paymentId) {
            $order = Order::where(
                'gateway_payment_id',
                $paymentId
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Caso não encontre, tenta pelo ID do pedido
        |--------------------------------------------------------------------------
        */

        if (!$order && $externalReference) {
            $order = Order::find($externalReference);
        }

        if (!$order) {
            Log::warning(
                'Pedido não encontrado para webhook Asaas.',
                [
                    'payment_id' => $paymentId,
                    'external_reference' => $externalReference,
                ]
            );

            return;
        }

        $order->update([
            'status' => $orderStatus,
            'gateway_status' => $gatewayStatus,
            'gateway_payment_id' => $paymentId
                ?? $order->gateway_payment_id,
        ]);

        Log::info('Pedido atualizado via webhook Asaas.', [
            'order_id' => $order->id,
            'payment_id' => $paymentId,
            'status' => $orderStatus,
            'gateway_status' => $gatewayStatus,
        ]);
    }
}
