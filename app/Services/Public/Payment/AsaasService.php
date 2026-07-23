<?php

namespace App\Services\Public\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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
    protected function http()
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'access_token' => $this->apiKey,
        ]);
    }


    /**
     * Cria um cliente no Asaas.
     */
    protected function createCustomer(Order $order): array
    {
        $user = $order->user;

        if (!$user) {
            throw new RuntimeException('Usuário não encontrado.');
        }

        if (!$order->cpf) {
            throw new RuntimeException('CPF não encontrado no pedido.');
        }

        $response = $this->http()->post(
            $this->baseUrl . '/customers',
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
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Erro ao criar cliente no Asaas: ' .
                $response->body()
            );
        }

        return $response->json();
    }


    /**
     * Retorna um cliente existente ou cria um novo no Asaas.
     */
    protected function getOrCreateCustomer(Order $order): array
    {
        $user = $order->user;

        if (!$user) {
            throw new RuntimeException('Usuário não encontrado.');
        }


        if (!empty($user->asaas_customer_id)) {
            return [
                'id' => $user->asaas_customer_id,
            ];
        }


        $customer = $this->createCustomer($order);


        $user->update([
            'asaas_customer_id' => $customer['id'],
        ]);


        return $customer;
    }


    /**
     * Cria pagamento via Pix.
     */
    public function createPixPayment(Order $order): array
    {
        $customer = $this->getOrCreateCustomer($order);


        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [
                'customer' => $customer['id'],
                'billingType' => 'PIX',
                'value' => $order->total,
                'dueDate' => now()->format('Y-m-d'),
                'description' => 'Pedido #' . $order->id,
                'externalReference' => (string) $order->id,
            ]
        );


        if ($response->failed()) {
            throw new RuntimeException(
                'Erro ao criar cobrança Pix no Asaas: ' .
                $response->body()
            );
        }


        return $response->json();
    }


    /**
     * QR Code Pix.
     */
    public function getPixQrCode(string $paymentId): array
    {
        $response = $this->http()->get(
            $this->baseUrl . "/payments/{$paymentId}/pixQrCode"
        );


        if ($response->failed()) {
            throw new RuntimeException(
                'Erro ao obter QR Code do Pix: ' .
                $response->body()
            );
        }


        return $response->json();
    }


    /**
     * Cria pagamento via boleto.
     */
    public function createBoletoPayment(Order $order): array
    {
        $customer = $this->getOrCreateCustomer($order);


        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [
                'customer' => $customer['id'],
                'billingType' => 'BOLETO',
                'value' => $order->total,
                'dueDate' => now()->addDays(3)->format('Y-m-d'),
                'description' => 'Pedido #' . $order->id,
                'externalReference' => (string) $order->id,
            ]
        );


        if ($response->failed()) {
            throw new RuntimeException(
                'Erro ao criar boleto no Asaas: ' .
                $response->body()
            );
        }


        return $response->json();
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


        if (!$user) {
            throw new RuntimeException(
                'Usuário não encontrado para o pedido.'
            );
        }


        if (!$order->cep) {
            throw new RuntimeException(
                'Endereço não encontrado no pedido.'
            );
        }


        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [

                'customer' => $customer['id'],

                'billingType' => 'CREDIT_CARD',

                'value' => $order->total,

                'dueDate' => now()->format('Y-m-d'),

                'description' => 'Pedido #' . $order->id,

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
        );


        if ($response->failed()) {

            throw new RuntimeException(
                'Erro ao criar pagamento com cartão no Asaas: ' .
                $response->body()
            );

        }


        return $response->json();
    }


    public function getPayment(string $paymentId): array
    {
        $response = $this->http()->get(
            $this->baseUrl . '/payments/' . $paymentId
        );


        if ($response->failed()) {

            throw new RuntimeException(
                'Erro ao consultar pagamento no Asaas: ' .
                $response->body()
            );

        }


        return $response->json();
    }



    public function cancelPayment(string $paymentId): array
    {
        $response = $this->http()->delete(
            $this->baseUrl . '/payments/' . $paymentId
        );


        if ($response->failed()) {

            throw new RuntimeException(
                'Erro ao cancelar pagamento no Asaas: ' .
                $response->body()
            );

        }


        return $response->json();
    }
}
