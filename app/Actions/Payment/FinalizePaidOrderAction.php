<?php

namespace App\Actions\Payment;

use App\Exceptions\Domain\InsufficientStockException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class FinalizePaidOrderAction
{
    public function execute(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('items');

            foreach ($order->items as $item) {
                $variant = ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $variant || $variant->stock < $item->quantity) {
                    throw new InsufficientStockException(
                        "Estoque insuficiente para finalizar o pedido {$order->id}."
                    );
                }

                $variant->decrement('stock', $item->quantity);
            }

            Cart::where('user_id', $order->user_id)->where('status', 'active')->each(fn (Cart $cart) => $cart->items()->delete());
        });
    }
}
