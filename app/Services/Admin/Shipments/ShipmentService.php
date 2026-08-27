<?php

namespace App\Services\Admin\Shipments;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Exceptions\Domain\LabelAlreadyGeneratedException;
use App\Exceptions\Domain\OrderNotPaidException;
use App\Exceptions\Domain\ShipmentFinalizedException;
use App\Exceptions\Domain\ShipmentNotRegisteredException;
use App\Exceptions\Domain\ShippingProviderException;
use App\Exceptions\Domain\ShippingSenderConfigurationException;
use App\Exceptions\Domain\ShippingServiceNotFoundException;
use App\Models\Shipment;
use App\Services\Shipping\MelhorEnvioService;
use App\Services\Shipping\ShipmentStatusMapper;

class ShipmentService
{
    public function __construct(
        protected MelhorEnvioService $melhorEnvio,
        protected ShipmentStatusMapper $statusMapper,
    ) {}

    public function updateShipment(Shipment $shipment, array $data): void
    {
        if (ShipmentStatus::tryFrom($shipment->status)?->isFinal()) {
            throw new ShipmentFinalizedException;
        }

        $shipment->update([
            'tracking_code' => $data['tracking_code'] ?? null,
            'status' => $data['status'] ?? $shipment->status,
        ]);
    }

    public function generateLabel(int $id): void
    {
        $shipment = Shipment::with(['order.items', 'order.user'])
            ->findOrFail($id);

        if ($shipment->shipment_id) {
            throw new LabelAlreadyGeneratedException;
        }

        if ($shipment->order->status !== OrderStatus::Paid->value) {
            throw new OrderNotPaidException;
        }

        if (! $shipment->service_id) {
            throw new ShippingServiceNotFoundException;
        }

        $data = $this->buildPayload($shipment);

        // 1. carrinho
        $cart = $this->melhorEnvio->adicionarAoCarrinho($data);

        if (! isset($cart['id'])) {
            throw new ShippingProviderException($cart['message'] ?? 'Erro ao criar carrinho.');
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
            'status' => ShipmentStatus::WaitingPost->value,
            'last_update' => json_encode($trackingData),
        ]);
    }

    public function syncStatus(int $id): void
    {
        $shipment = Shipment::findOrFail($id);

        if (! $shipment->shipment_id) {
            throw new ShipmentNotRegisteredException;
        }

        $response = $this->melhorEnvio->consultarPedido($shipment->shipment_id);
        $trackingData = current($response);

        $apiStatus = $trackingData['status'] ?? null;

        $shipment->update([
            'status' => $this->statusMapper->fromProvider($apiStatus)?->value ?? $shipment->status,
            'tracking_code' => $trackingData['tracking'] ?? $shipment->tracking_code,
            'label_url' => $shipment->label_url,
            'shipped_at' => $apiStatus === 'posted' ? now() : $shipment->shipped_at,
            'delivered_at' => $apiStatus === 'delivered' ? now() : $shipment->delivered_at,
            'last_update' => json_encode($trackingData),
        ]);
    }

    private function buildPayload(Shipment $shipment): array
    {
        $order = $shipment->order;
        $sender = $this->sender();

        return [
            'service' => (int) $shipment->service_id,

            'from' => [
                ...$sender,
            ],

            'to' => [
                // O pedido guarda um snapshot do endereço para não depender de alterações posteriores.
                'name' => $order->recipient_name,
                'phone' => $order->phone,
                'email' => $order->user->email,
                'document' => preg_replace('/\D/', '', $order->cpf),
                'address' => $order->street,
                'number' => $order->number,
                'district' => $order->neighborhood,
                'city' => $order->city,
                'state_abbr' => strtoupper($order->state),
                'postal_code' => preg_replace('/\D/', '', $order->cep),
            ],

            'products' => $order->items->map(function ($item) {
                return [
                    'name' => $item->name_snapshot,
                    'quantity' => $item->quantity,
                    'unitary_value' => $item->price,
                ];
            })->toArray(),

            'volumes' => [
                [
                    'weight' => 0.3,
                    'width' => 20,
                    'height' => 5,
                    'length' => 25,
                ],
            ],
        ];
    }

    /** @return array<string, string> */
    private function sender(): array
    {
        $sender = config('services.melhor_envio.sender', []);
        $requiredFields = ['name', 'phone', 'email', 'document', 'address', 'number', 'district', 'city', 'state_abbr'];

        foreach ($requiredFields as $field) {
            if (blank($sender[$field] ?? null)) {
                throw new ShippingSenderConfigurationException;
            }
        }

        $postalCode = config('services.melhor_envio.origin_zip');

        if (blank($postalCode)) {
            throw new ShippingSenderConfigurationException;
        }

        return [
            'name' => $sender['name'],
            'phone' => preg_replace('/\D/', '', $sender['phone']),
            'email' => $sender['email'],
            'document' => preg_replace('/\D/', '', $sender['document']),
            'address' => $sender['address'],
            'number' => $sender['number'],
            'district' => $sender['district'],
            'city' => $sender['city'],
            'state_abbr' => strtoupper($sender['state_abbr']),
            'postal_code' => preg_replace('/\D/', '', $postalCode),
        ];
    }
}
