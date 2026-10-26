<?php

namespace App\UseCases\Payment;

use App\Exceptions\Domain\PaymentException;
use App\Exceptions\Domain\PaymentGatewayException;
use App\Models\Order;
use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCardCheckout
{
    public function __construct(
        private AsaasService $asaas,
        private OperationalAlertService $alerts,
    ) {}

    public function execute(Order $order): string
    {
        try {
            $checkout = $this->asaas->createCardCheckout($order);
            $link = $checkout['link'] ?? null;

            if (! is_string($link) || ! $this->isTrustedAsaasUrl($link)) {
                throw new PaymentException('O provedor não retornou um link seguro para pagamento.');
            }

            $order->update([
                'gateway_payment_id' => $checkout['id'] ?? null,
                'gateway_status' => $checkout['status'] ?? 'ACTIVE',
                'status' => 'pending_payment',
                'payment_method' => 'card',
            ]);

            Log::info('Checkout de cartão criado.', [
                'order_id' => $order->id,
                'gateway_payment_id' => $order->gateway_payment_id,
                'gateway_status' => $order->gateway_status,
            ]);

            return $link;
        } catch (Throwable $exception) {
            if (! $exception instanceof PaymentGatewayException || $exception->isOperational) {
                $this->alerts->critical('Falha ao processar pagamento com cartão.', [
                    'Pedido' => $order->id,
                    'Tipo de erro' => $exception::class,
                ]);
            }

            throw $exception;
        }
    }

    private function isTrustedAsaasUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');

        return ($parts['scheme'] ?? null) === 'https'
            && ($host === 'asaas.com' || str_ends_with($host, '.asaas.com'));
    }
}
