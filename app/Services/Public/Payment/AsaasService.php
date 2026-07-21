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
     * Cria pagamento via Pix.
     */
    public function createPixPayment(Order $order): array
    {
        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [
                'customer' => $order->asaas_customer_id,
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
     * Cria pagamento via boleto.
     */
    public function createBoletoPayment(Order $order): array
    {
        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [
                'customer' => $order->asaas_customer_id,
                'billingType' => 'BOLETO',
                'value' => $order->total,
                'dueDate' => now()->format('Y-m-d'),
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
        $response = $this->http()->post(
            $this->baseUrl . '/payments',
            [
                'customer' => $order->asaas_customer_id,
                'billingType' => 'CREDIT_CARD',
                'value' => $order->total,
                'dueDate' => now()->format('Y-m-d'),
                'description' => 'Pedido #' . $order->id,
                'externalReference' => (string) $order->id,

                'creditCard' => [
                    'holderName' => $cardData['holder_name'],
                    'number' => $cardData['card_number'],
                    'expiryMonth' => $cardData['expiration_month'],
                    'expiryYear' => $cardData['expiration_year'],
                    'ccv' => $cardData['ccv'],
                ],
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

    /**
     * Consulta uma cobrança.
     */
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

    /**
     * Cancela uma cobrança.
     */
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
