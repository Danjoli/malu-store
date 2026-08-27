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
        $mapping = ['PAYMENT_CREATED' => [OrderStatus::Pending, 'PENDING'], 'PAYMENT_CONFIRMED' => [OrderStatus::Paid, 'CONFIRMED'], 'PAYMENT_RECEIVED' => [OrderStatus::Paid, 'RECEIVED'], 'PAYMENT_OVERDUE' => [OrderStatus::Expired, 'OVERDUE'], 'PAYMENT_DELETED' => [OrderStatus::Cancelled, 'DELETED'], 'PAYMENT_REFUNDED' => [OrderStatus::Cancelled, 'REFUNDED']];
        if (! isset($mapping[$event])) {
            Log::info('Evento Asaas não tratado.', ['event' => $event]);

            return;
        }
        [$status, $gatewayStatus] = $mapping[$event];
        $this->updateOrder->execute($data, $status, $gatewayStatus);
    }
}
