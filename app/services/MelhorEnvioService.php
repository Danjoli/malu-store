<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MelhorEnvioService {

    private $baseUrl;
    private $token;

    public function __construct()
    {
        $this->baseUrl = config('services.melhor_envio.url');
        $this->token = config('services.melhor_envio.token');
    }

    private function request($endpoint, $data = [], $method = 'POST')
    {
        $http = Http::withToken($this->token)
            ->acceptJson();

        $url = $this->baseUrl . $endpoint;

        if ($method === 'GET') {
            $response = $http->get($url, $data);
        } else {
            $response = $http->post($url, $data);
        }

        return $response->json();
    }

    public function calcularFrete($dados)
    {
        return $this->request('shipment/calculate', $dados);
    }

    public function adicionarAoCarrinho($data)
    {
        return $this->request('cart', $data);
    }

    public function comprarEtiqueta($data)
    {
        return $this->request('shipment/checkout', $data);
    }

    public function gerarEtiqueta($data)
    {
        return $this->request('shipment/generate', $data);
    }

    public function consultarPedido($shipmentId)
    {
        return $this->request("shipment/{$shipmentId}", [], 'GET');
    }
}
