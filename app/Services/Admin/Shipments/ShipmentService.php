<?php

namespace App\Services\Admin\Shipments;

use App\Enums\ShipmentStatus;
use App\Exceptions\Domain\ShipmentFinalizedException;
use App\Models\Shipment;

class ShipmentService
{
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
}
