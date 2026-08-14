<?php

namespace App\Actions\Payment;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinalizePaidOrderAction
{
    public function execute(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('items.variant');
            foreach ($order->items as $item) {
                $variant = $item->variant;
                if (! $variant || $variant->stock < $item->quantity) {
                    Log::warning('Estoque insuficiente ou variante ausente ao finalizar pedido.', ['order_id' => $order->id, 'order_item_id' => $item->id]);

                    continue;
                }
                $variant->decrement('stock', $item->quantity);
            }
            Cart::where('user_id', $order->user_id)->where('status', 'active')->each(fn (Cart $cart) => $cart->items()->delete());
        });
    }
}
