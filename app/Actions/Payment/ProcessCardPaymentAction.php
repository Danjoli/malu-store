<?php

namespace App\Actions\Payment;

use App\Enums\OrderStatus;
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

    public function execute(Order $order, array $cardData): array
    {
        try {
            $payment = $this->asaas->createCardPayment($order, $cardData);
            $status = $payment['status'] ?? 'PENDING';
            $order->update(['gateway_payment_id' => $payment['id'] ?? null, 'gateway_status' => $status, 'status' => $status === 'CONFIRMED' ? OrderStatus::Paid->value : OrderStatus::Pending->value, 'payment_method' => 'card', 'paid_at' => $status === 'CONFIRMED' ? now() : null]);

            Log::info('Pagamento com cartão processado.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status, 'order_status' => $order->status]);

            return $payment;
        } catch (Throwable $exception) {
            // Recusa de cartão é esperada; indisponibilidade do gateway merece alerta operacional.
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
