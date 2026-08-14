<?php

namespace App\Actions\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class CreateOrderItemsAction
{
    public function execute(Order $order, Cart $cart): void
    {
        foreach ($cart->items as $item) {
            OrderItem::create(['order_id' => $order->id, 'product_variant_id' => $item->product_variant_id, 'name_snapshot' => $item->name_snapshot, 'image_snapshot' => $item->image_snapshot, 'color_snapshot' => $item->color_snapshot, 'size_snapshot' => $item->size_snapshot, 'price' => $item->price, 'quantity' => $item->quantity]);
        }
    }
}
