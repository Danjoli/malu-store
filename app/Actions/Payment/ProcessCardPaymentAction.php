<?php

namespace App\Actions\Payment;

use App\Exceptions\Domain\PaymentGatewayException;
use App\Models\Order;
use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessCardPaymentAction
{
    public function __construct(
        private AsaasService $asaas,
        private OperationalAlertService $alerts,
    ) {}

    public function execute(Order $order): array
    {
        try {
            $payment = $this->asaas->createCardCheckout($order);
            $status = $payment['status'] ?? 'ACTIVE';

            $order->update([
                'gateway_payment_id' => $payment['id'] ?? null,
                'gateway_status' => $status,
                'status' => 'pending_payment',
                'payment_method' => 'card',
            ]);

            $order->refresh();

            Log::info('Checkout de cartão criado.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status]);

            return $payment;
        } catch (Throwable $exception) {
            // Falhas de validação do provedor são esperadas; indisponibilidade merece alerta operacional.
            if (! $exception instanceof PaymentGatewayException || $exception->isOperational) {
                $this->alerts->critical('Falha ao processar pagamento com cartão.', [
                    'Pedido' => $order->id,
                    'Tipo de erro' => $exception::class,
                ]);
            }

            throw $exception;
        }
    }
}
