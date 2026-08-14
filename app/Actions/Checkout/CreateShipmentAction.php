<?php

namespace App\Actions\Checkout;

use App\Data\CheckoutData;
use App\Models\Order;
use App\Models\Shipment;

class CreateShipmentAction
{
    public function execute(Order $order, CheckoutData $data): Shipment
    {
        return Shipment::create(['order_id' => $order->id, 'carrier' => $data->carrier, 'shipping_cost' => $data->shippingCost, 'service_id' => $data->service, 'status' => 'pending']);
    }
}
