<?php

namespace App\Services\Shipping;

use App\Exceptions\Domain\ShippingProviderException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MelhorEnvioService
{
    private string $baseUrl;

    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.melhor_envio.url');
        $this->token = config('services.melhor_envio.token');
    }

    private function http(): PendingRequest
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->withUserAgent(config('services.melhor_envio.user_agent'))
            ->connectTimeout(5)
            ->timeout(20);
    }

    private function request(string $endpoint, array $data = [], string $method = 'POST'): array
    {
        $http = $this->http();

        $url = $this->baseUrl.$endpoint;

        $response = $method === 'GET'
            ? $http->get($url, $data)
            : $http->post($url, $data);

        $this->ensureSuccessful($response, $endpoint);

        return $response->json();
    }

    private function ensureSuccessful(Response $response, string $endpoint): void
    {
        if ($response->successful()) {
            return;
        }

        Log::warning('Falha na comunicação com Melhor Envio.', [
            'endpoint' => $endpoint,
            'http_status' => $response->status(),
        ]);

        throw new ShippingProviderException('Não foi possível comunicar com a Melhor Envio.');
    }

    public function calcularFrete(array $dados): array
    {
        return $this->request('shipment/calculate', $dados);
    }

    public function adicionarAoCarrinho(array $data): array
    {
        return $this->request('cart', $data);
    }

    public function comprarEtiqueta(array $data): array
    {
        return $this->request('shipment/checkout', $data);
    }

    public function gerarEtiqueta(array $data): array
    {
        return $this->request('shipment/generate', $data);
    }

    public function consultarPedido(string $shipmentId): array
    {
        $response = $this->http()
            ->post($this->baseUrl.'shipment/tracking', [
                'orders' => [$shipmentId],
            ]);

        $this->ensureSuccessful($response, 'shipment/tracking');

        return $response->json();
    }

    /** @param array<int, string> $ids */
    public function imprimirEtiqueta(array $ids): array
    {
        return $this->request(
            'shipment/print',
            [
                'mode' => 'public',
                'orders' => $ids,
            ]
        );
    }
}
