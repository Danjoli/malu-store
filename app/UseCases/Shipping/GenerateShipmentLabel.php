<?php

namespace App\UseCases\Shipping;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Exceptions\Domain\OrderNotPaidException;
use App\Exceptions\Domain\ShippingProviderException;
use App\Exceptions\Domain\ShippingSenderConfigurationException;
use App\Exceptions\Domain\ShippingServiceNotFoundException;
use App\Models\Shipment;
use App\Services\Shipping\MelhorEnvioService;
use Illuminate\Support\Arr;

class GenerateShipmentLabel
{
    public function __construct(private MelhorEnvioService $melhorEnvio) {}

    public function execute(int $shipmentId): void
    {
        $shipment = Shipment::with(['order.items', 'order.user'])->findOrFail($shipmentId);

        if ($shipment->order->status !== OrderStatus::Paid->value) {
            throw new OrderNotPaidException;
        }

        if (! $shipment->service_id) {
            throw new ShippingServiceNotFoundException;
        }

        $providerId = $shipment->shipment_id;

        if (! $providerId) {
            $cart = $this->melhorEnvio->adicionarAoCarrinho($this->payload($shipment));

            if (! isset($cart['id'])) {
                throw new ShippingProviderException($cart['message'] ?? 'Erro ao criar carrinho.');
            }

            $providerId = $cart['id'];
            $this->melhorEnvio->comprarEtiqueta(['orders' => [$providerId]]);
            $this->melhorEnvio->gerarEtiqueta(['orders' => [$providerId]]);

            $shipment->update([
                'shipment_id' => $providerId,
                'status' => ShipmentStatus::WaitingPost->value,
            ]);
        }

        $tracking = $this->melhorEnvio->consultarPedido($providerId);
        $trackingData = current($tracking) ?: [];
        $print = $this->melhorEnvio->imprimirEtiqueta([$providerId]);

        if (blank($print['url'] ?? null)) {
            throw new ShippingProviderException('A etiqueta ainda está sendo processada pelo provedor.');
        }

        $shipment->update([
            'tracking_code' => $trackingData['tracking'] ?? null,
            'label_url' => $print['url'],
            'status' => ShipmentStatus::WaitingPost->value,
            'last_update' => $this->snapshot($trackingData),
        ]);
    }

    private function payload(Shipment $shipment): array
    {
        $order = $shipment->order;

        return [
            'service' => (int) $shipment->service_id,
            'from' => $this->sender(),
            'to' => [
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
            'products' => $order->items->map(fn ($item): array => [
                'name' => $item->name_snapshot,
                'quantity' => $item->quantity,
                'unitary_value' => $item->price,
            ])->toArray(),
            'volumes' => [[
                'weight' => 0.3,
                'width' => 20,
                'height' => 5,
                'length' => 25,
            ]],
        ];
    }

    /** @return array<string, string> */
    private function sender(): array
    {
        $sender = config('services.melhor_envio.sender', []);
        $required = ['name', 'phone', 'email', 'document', 'address', 'number', 'district', 'city', 'state_abbr'];

        foreach ($required as $field) {
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

    /** @param array<string, mixed> $data */
    private function snapshot(array $data): ?string
    {
        return json_encode(Arr::only($data, ['id', 'status', 'tracking', 'label', 'updated_at'])) ?: null;
    }
}
