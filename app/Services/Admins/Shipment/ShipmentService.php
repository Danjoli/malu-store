<?php

namespace App\Services\Admins\Shipment;

use App\Models\Shipment;
use App\Services\Public\MelhorEnvio\MelhorEnvioService;

class ShipmentService
{
    public function __construct(
        protected MelhorEnvioService $melhorEnvio
    ) {}

    public function updateShipment(Shipment $shipment, array $data)
    {
        if (in_array($shipment->status, ['delivered', 'cancelled'])) {
            throw new \Exception('Este envio não pode mais ser alterado.');
        }

        $shipment->update([
            'tracking_code' => $data['tracking_code'] ?? null,
            'status' => $data['status'] ?? $shipment->status,
        ]);
    }

    public function generateLabel(int $id)
    {
        $shipment = Shipment::with(['order.items', 'order.address', 'order.user'])
            ->findOrFail($id);

        if ($shipment->shipment_id) {
            throw new \Exception('Etiqueta já foi gerada!');
        }

        if ($shipment->order->status !== 'paid') {
            throw new \Exception('Pedido ainda não foi pago.');
        }

        if (!$shipment->service_id) {
            throw new \Exception('Serviço de frete não encontrado.');
        }

        $data = $this->buildPayload($shipment);

        // 1. carrinho
        $cart = $this->melhorEnvio->adicionarAoCarrinho($data);

        if (!isset($cart['id'])) {
            throw new \Exception($cart['message'] ?? 'Erro ao criar carrinho.');
        }

        // 2. compra
        $this->melhorEnvio->comprarEtiqueta(['orders' => [$cart['id']]]);

        // 3. gerar etiqueta
        $this->melhorEnvio->gerarEtiqueta(['orders' => [$cart['id']]]);

        sleep(2);

        // 4. tracking
        $tracking = $this->melhorEnvio->consultarPedido($cart['id']);
        $trackingData = current($tracking);

        // 5. pdf
        $print = $this->melhorEnvio->imprimirEtiqueta([$cart['id']]);

        $shipment->update([
            'shipment_id' => $cart['id'],
            'tracking_code' => $trackingData['tracking'] ?? null,
            'label_url' => $print['url'] ?? null,
            'status' => 'waiting_post',
            'last_update' => json_encode($trackingData),
        ]);
    }

    public function syncStatus(int $id)
    {
        $shipment = Shipment::findOrFail($id);

        if (!$shipment->shipment_id) {
            throw new \Exception('Envio não existe na Melhor Envio.');
        }

        $response = $this->melhorEnvio->consultarPedido($shipment->shipment_id);
        $trackingData = current($response);

        $apiStatus = $trackingData['status'] ?? null;

        $shipment->update([
            'status' => $this->mapStatus($apiStatus) ?? $shipment->status,
            'tracking_code' => $trackingData['tracking'] ?? $shipment->tracking_code,
            'label_url' => $shipment->label_url,
            'shipped_at' => $apiStatus === 'posted' ? now() : $shipment->shipped_at,
            'delivered_at' => $apiStatus === 'delivered' ? now() : $shipment->delivered_at,
            'last_update' => json_encode($trackingData),
        ]);
    }

    private function buildPayload(Shipment $shipment): array
    {
        return [
            "service" => (int) $shipment->service_id,

            "from" => [
                "name" => "Malu Store",
                "phone" => "11954598885",
                "email" => "store@email.com",
                "document" => "00000000000",
                "address" => "Rua Exemplo",
                "number" => "100",
                "district" => "Centro",
                "city" => "São Paulo",
                "state_abbr" => "SP",
                "postal_code" => "00000000"
            ],

            "to" => [
                "name" => $shipment->order->address->recipient_name,
                "phone" => $shipment->order->address->phone,
                "email" => $shipment->order->user->email,
                "document" => preg_replace('/\D/', '', $shipment->order->address->cpf),
                "address" => $shipment->order->address->street,
                "number" => $shipment->order->address->number,
                "district" => $shipment->order->address->neighborhood,
                "city" => $shipment->order->address->city,
                "state_abbr" => strtoupper($shipment->order->address->state),
                "postal_code" => preg_replace('/\D/', '', $shipment->order->address->cep)
            ],

            "products" => $shipment->order->items->map(function ($item) {
                return [
                    "name" => $item->name_snapshot,
                    "quantity" => $item->quantity,
                    "unitary_value" => $item->price
                ];
            })->toArray(),

            "volumes" => [
                [
                    "weight" => 0.3,
                    "width" => 20,
                    "height" => 5,
                    "length" => 25
                ]
            ]
        ];
    }

    private function mapStatus($status)
    {
        return [
            'created' => 'pending',
            'released' => 'waiting_post',
            'generated' => 'waiting_post',
            'posted' => 'in_transit',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
        ][$status] ?? null;
    }
}
