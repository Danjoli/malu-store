<?php

namespace App\Actions\Checkout;

use App\Exceptions\Domain\InsufficientStockException;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class RefreshCartForCheckoutAction
{
    public function execute(Cart $cart): Cart
    {
        foreach ($cart->items as $item) {
            $variant = ProductVariant::query()
                ->with('product.images')
                ->whereKey($item->product_variant_id)
                ->lockForUpdate()
                ->first();

            if (! $variant || ! $variant->product || ! $variant->product->active) {
                throw ValidationException::withMessages([
                    'cart' => "O produto {$item->name_snapshot} não está mais disponível.",
                ]);
            }

            if ($variant->stock < $item->quantity) {
                throw new InsufficientStockException(
                    "Estoque insuficiente para {$variant->product->name}."
                );
            }

            $item->update([
                'name_snapshot' => $variant->product->name,
                'image_snapshot' => optional($variant->product->images->first())->image ?? '',
                'color_snapshot' => $variant->color,
                'size_snapshot' => $variant->size,
                'price' => $variant->product->price,
            ]);
        }

        return $cart->fresh('items.variant.product');
    }
}
