<?php

namespace Tests\Unit;

use App\Services\Shipping\ShipmentStatusMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ShipmentStatusMapperTest extends TestCase
{
    #[DataProvider('statuses')]
    public function test_it_maps_provider_statuses(?string $providerStatus, ?string $expectedStatus): void
    {
        $mapper = new ShipmentStatusMapper;

        $this->assertSame($expectedStatus, $mapper->fromProvider($providerStatus));
    }

    public static function statuses(): array
    {
        return [
            'created shipment' => ['created', 'pending'],
            'posted shipment' => ['posted', 'in_transit'],
            'delivered shipment' => ['delivered', 'delivered'],
            'cancelled shipment' => ['cancelled', 'cancelled'],
            'unknown shipment' => ['unknown', null],
        ];
    }
}
