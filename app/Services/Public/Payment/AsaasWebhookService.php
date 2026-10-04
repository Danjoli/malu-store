<?php

namespace App\Services\Public\Payment;

use App\Actions\Payment\UpdateOrderFromAsaasWebhookAction;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class AsaasWebhookService
{
    public function __construct(private UpdateOrderFromAsaasWebhookAction $updateOrder) {}

    public function handleAsaas(array $data): void
    {
        $event = $data['event'] ?? null;
        if (! $event) {
            Log::warning('Webhook Asaas sem evento.');

            return;
        }
        if (str_starts_with($event, 'CHECKOUT_')) {
            $this->handleCheckout($data, $event);

            return;
        }

        $mapping = ['PAYMENT_CREATED' => [OrderStatus::Pending, 'PENDING'], 'PAYMENT_CONFIRMED' => [OrderStatus::Paid, 'CONFIRMED'], 'PAYMENT_RECEIVED' => [OrderStatus::Paid, 'RECEIVED'], 'PAYMENT_OVERDUE' => [OrderStatus::Expired, 'OVERDUE'], 'PAYMENT_DELETED' => [OrderStatus::Cancelled, 'DELETED'], 'PAYMENT_REFUNDED' => [OrderStatus::Cancelled, 'REFUNDED']];
        if (! isset($mapping[$event])) {
            Log::info('Evento Asaas não tratado.', ['event' => $event]);

            return;
        }
        [$status, $gatewayStatus] = $mapping[$event];
        $this->updateOrder->execute($data, $status, $gatewayStatus);
    }

    private function handleCheckout(array $data, string $event): void
    {
        $mapping = [
            'CHECKOUT_CREATED' => [OrderStatus::PendingPayment, 'ACTIVE'],
            'CHECKOUT_PAID' => [OrderStatus::Paid, 'PAID'],
            'CHECKOUT_CANCELED' => [OrderStatus::Cancelled, 'CANCELED'],
            'CHECKOUT_EXPIRED' => [OrderStatus::Expired, 'EXPIRED'],
        ];

        if (! isset($mapping[$event]) || ! is_array($data['checkout'] ?? null)) {
            Log::warning('Webhook de Checkout Asaas inválido.', ['event' => $event]);

            return;
        }

        $checkout = $data['checkout'];
        [$status, $gatewayStatus] = $mapping[$event];
        $this->updateOrder->execute([
            'payment' => [
                'id' => $checkout['id'] ?? null,
                'externalReference' => $checkout['externalReference'] ?? null,
                'billingType' => 'CREDIT_CARD',
            ],
        ], $status, $gatewayStatus);
    }
}
