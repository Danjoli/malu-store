<?php

namespace App\Services\Public\Payment;

use App\Exceptions\Domain\PaymentGatewayException;
use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.base_url');
        $this->apiKey = config('services.asaas.api_key');
    }

    /**
     * Cliente HTTP padrão do Asaas.
     */
    protected function http(): PendingRequest
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'access_token' => $this->apiKey,
            'User-Agent' => config('services.asaas.user_agent'),
        ])->connectTimeout(5)->timeout(20);
    }

    /**
     * Cria um cliente no Asaas.
     */
    protected function createCustomer(Order $order): array
    {
        $user = $order->user;

        if (! $user) {
            throw new PaymentGatewayException('Usuário não encontrado.');
        }

        if (! $order->cpf) {
            throw new PaymentGatewayException('CPF não encontrado no pedido.');
        }

        return $this->request(fn (): Response => $this->http()->post(
            $this->baseUrl.'/customers',
            [
                'name' => $user->name,
                'email' => $user->email,
                'cpfCnpj' => preg_replace(
                    '/\D/',
                    '',
                    $order->cpf
                ),
                'externalReference' => (string) $order->id,
            ]
        ), 'customers');
    }

    /**
     * Retorna um cliente existente ou cria um novo no Asaas.
     */
    protected function getOrCreateCustomer(Order $order): array
    {
        $user = $order->user;

        if (! $user) {
            throw new PaymentGatewayException('Usuário não encontrado.');
        }

        // Usuário já possui um cliente cadastrado no Asaas
        if (! empty($user->asaas_customer_id)) {
            return [
                'id' => $user->asaas_customer_id,
            ];
        }

        // Cria cliente no Asaas
        $customer = $this->createCustomer($order);

        if (empty($customer['id'])) {
            throw new PaymentGatewayException(
                'O Asaas não retornou o ID do cliente.'
            );
        }

        // Salva o ID do cliente do Asaas no usuário
        $user->asaas_customer_id = $customer['id'];
        $user->save();

        return [
            'id' => $customer['id'],
        ];
    }

    /**
     * Cria pagamento via Pix.
     */
    public function createPixPayment(Order $order): array
    {
        $customer = $this->getOrCreateCustomer($order);

        return $this->request(fn (): Response => $this->http()->post(
            $this->baseUrl.'/payments',
            [
                'customer' => $customer['id'],
                'billingType' => 'PIX',
                'value' => $order->total,
                'dueDate' => now()->format('Y-m-d'),
                'description' => 'Pedido #'.$order->id,
                'externalReference' => (string) $order->id,
            ]
        ), 'payments/pix');
    }

    /**
     * QR Code Pix.
     */
    public function getPixQrCode(string $paymentId): array
    {
        return $this->request(fn (): Response => $this->http()->get(
            $this->baseUrl."/payments/{$paymentId}/pixQrCode"
        ), 'payments/pix-qr-code');
    }

    /**
     * Cria pagamento via boleto.
     */
    public function createBoletoPayment(Order $order): array
    {
        $customer = $this->getOrCreateCustomer($order);

        return $this->request(fn (): Response => $this->http()->post(
            $this->baseUrl.'/payments',
            [
                'customer' => $customer['id'],
                'billingType' => 'BOLETO',
                'value' => $order->total,
                'dueDate' => now()->addDays(3)->format('Y-m-d'),
                'description' => 'Pedido #'.$order->id,
                'externalReference' => (string) $order->id,
            ]
        ), 'payments/boleto');
    }

    /** Cria uma página de cartão hospedada pelo Asaas. */
    public function createCardCheckout(Order $order): array
    {
        $customer = $this->getOrCreateCustomer($order);
        $order->loadMissing('items');
        $items = $order->items->map(fn ($item): array => [
            'externalReference' => (string) $item->id,
            'name' => $item->name_snapshot,
            'description' => collect([$item->color_snapshot, $item->size_snapshot])->filter()->implode(' / '),
            'quantity' => $item->quantity,
            'value' => (float) $item->price,
        ])->values()->all();

        if ((float) $order->shipping > 0) {
            $items[] = [
                'externalReference' => 'shipping-'.$order->id,
                'name' => 'Frete',
                'description' => 'Entrega do pedido #'.$order->id,
                'quantity' => 1,
                'value' => (float) $order->shipping,
            ];
        }

        return $this->request(fn (): Response => $this->http()->post(
            $this->baseUrl.'/checkouts',
            [
                'customer' => $customer['id'],
                'billingTypes' => ['CREDIT_CARD'],
                'chargeTypes' => ['DETACHED'],
                'minutesToExpire' => 60,
                'externalReference' => (string) $order->id,
                'callback' => [
                    'successUrl' => route('payment.success', $order),
                    'cancelUrl' => route('payment.error', ['order' => $order, 'reason' => 'cancelled']),
                    'expiredUrl' => route('payment.error', ['order' => $order, 'reason' => 'expired']),
                ],
                'items' => $items,
            ]
        ), 'checkouts/card');
    }

    public function getPayment(string $paymentId): array
    {
        return $this->request(fn (): Response => $this->http()->get(
            $this->baseUrl.'/payments/'.$paymentId
        ), 'payments/get');
    }

    public function cancelPayment(string $paymentId): array
    {
        return $this->request(fn (): Response => $this->http()->delete(
            $this->baseUrl.'/payments/'.$paymentId
        ), 'payments/cancel');
    }

    /** @param callable(): Response $send */
    private function request(callable $send, string $operation): array
    {
        try {
            $response = $send();
        } catch (ConnectionException) {
            Log::warning('Timeout ou falha de conexão com Asaas.', ['operation' => $operation]);

            throw new PaymentGatewayException('Não foi possível comunicar com o provedor de pagamentos.', true);
        }

        $this->ensureSuccessful($response, $operation);
        $payload = $response->json();

        if (! is_array($payload)) {
            Log::warning('Resposta inválida recebida do Asaas.', ['operation' => $operation]);

            throw new PaymentGatewayException('O provedor de pagamentos retornou uma resposta inválida.', true);
        }

        return $payload;
    }

    private function ensureSuccessful(Response $response, string $operation): void
    {
        if ($response->successful()) {
            return;
        }

        Log::warning('Falha na comunicação com Asaas.', [
            'operation' => $operation,
            'http_status' => $response->status(),
        ]);

        throw new PaymentGatewayException(
            'Não foi possível comunicar com o provedor de pagamentos.',
            ! in_array($response->status(), [400, 402, 422], true),
        );
    }
}
