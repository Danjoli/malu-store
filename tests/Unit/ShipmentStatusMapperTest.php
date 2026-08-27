<?php

namespace Tests\Unit;

use App\Enums\ShipmentStatus;
use App\Services\Shipping\ShipmentStatusMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ShipmentStatusMapperTest extends TestCase
{
    #[DataProvider('statuses')]
    public function test_it_maps_provider_statuses(?string $providerStatus, ?ShipmentStatus $expectedStatus): void
    {
        $mapper = new ShipmentStatusMapper;

        $this->assertSame($expectedStatus, $mapper->fromProvider($providerStatus));
    }

    public static function statuses(): array
    {
        return [
            'created shipment' => ['created', ShipmentStatus::Pending],
            'posted shipment' => ['posted', ShipmentStatus::InTransit],
            'delivered shipment' => ['delivered', ShipmentStatus::Delivered],
            'cancelled shipment' => ['cancelled', ShipmentStatus::Cancelled],
            'unknown shipment' => ['unknown', null],
        ];
    }
}
