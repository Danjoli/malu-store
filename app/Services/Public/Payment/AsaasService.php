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

    /**
     * Cria pagamento via cartão.
     */
    public function createCardPayment(
        Order $order,
        array $cardData
    ): array {

        $customer = $this->getOrCreateCustomer($order);

        $user = $order->user;

        if (! $user) {
            throw new PaymentGatewayException(
                'Usuário não encontrado para o pedido.'
            );
        }

        if (! $order->cep) {
            throw new PaymentGatewayException(
                'Endereço não encontrado no pedido.'
            );
        }

        return $this->request(fn (): Response => $this->http()->post(
            $this->baseUrl.'/payments',
            [

                'customer' => $customer['id'],

                'billingType' => 'CREDIT_CARD',

                'value' => $order->total,

                'dueDate' => now()->format('Y-m-d'),

                'description' => 'Pedido #'.$order->id,

                'externalReference' => (string) $order->id,

                'creditCard' => [

                    'holderName' => $cardData['holder_name'],

                    'number' => preg_replace(
                        '/\D/',
                        '',
                        $cardData['card_number']
                    ),

                    'expiryMonth' => $cardData['expiration_month'],

                    'expiryYear' => $cardData['expiration_year'],

                    'ccv' => $cardData['ccv'],

                ],

                'creditCardHolderInfo' => [

                    'name' => $cardData['holder_name'],

                    'email' => $user->email,

                    'cpfCnpj' => preg_replace(
                        '/\D/',
                        '',
                        $order->cpf
                    ),

                    'postalCode' => preg_replace(
                        '/\D/',
                        '',
                        $order->cep
                    ),

                    'addressNumber' => $order->number,

                    'addressComplement' => $order->complement,

                    'phone' => $order->phone,

                    'mobilePhone' => $order->phone,

                ],

                'remoteIp' => request()->ip(),

            ]
        ), 'payments/card');
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
