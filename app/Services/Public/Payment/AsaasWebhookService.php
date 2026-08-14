<?php

namespace App\Services\Public\Payment;

use App\Actions\Payment\UpdateOrderFromAsaasWebhookAction;
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
        $mapping = ['PAYMENT_CREATED' => ['pending', 'PENDING'], 'PAYMENT_CONFIRMED' => ['paid', 'CONFIRMED'], 'PAYMENT_RECEIVED' => ['paid', 'RECEIVED'], 'PAYMENT_OVERDUE' => ['expired', 'OVERDUE'], 'PAYMENT_DELETED' => ['cancelled', 'DELETED'], 'PAYMENT_REFUNDED' => ['cancelled', 'REFUNDED']];
        if (! isset($mapping[$event])) {
            Log::info('Evento Asaas não tratado.', ['event' => $event]);

            return;
        }
        [$status, $gatewayStatus] = $mapping[$event];
        $this->updateOrder->execute($data, $status, $gatewayStatus);
    }
}
