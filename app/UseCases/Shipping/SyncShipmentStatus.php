<?php

namespace App\UseCases\Shipping;

use App\Exceptions\Domain\ShipmentNotRegisteredException;
use App\Models\Shipment;
use App\Services\Shipping\MelhorEnvioService;
use App\Services\Shipping\ShipmentStatusMapper;
use Illuminate\Support\Arr;

class SyncShipmentStatus
{
    public function __construct(
        private MelhorEnvioService $melhorEnvio,
        private ShipmentStatusMapper $statusMapper,
    ) {}

    public function execute(int $shipmentId): void
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if (! $shipment->shipment_id) {
            throw new ShipmentNotRegisteredException;
        }

        $response = $this->melhorEnvio->consultarPedido($shipment->shipment_id);
        $tracking = current($response);
        $providerStatus = $tracking['status'] ?? null;

        $shipment->update([
            'status' => $this->statusMapper->fromProvider($providerStatus)?->value ?? $shipment->status,
            'tracking_code' => $tracking['tracking'] ?? $shipment->tracking_code,
            'shipped_at' => $providerStatus === 'posted' ? now() : $shipment->shipped_at,
            'delivered_at' => $providerStatus === 'delivered' ? now() : $shipment->delivered_at,
            'last_update' => json_encode(Arr::only($tracking, [
                'id', 'status', 'tracking', 'label', 'updated_at',
            ])) ?: null,
        ]);
    }
}
