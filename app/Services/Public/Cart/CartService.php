<?php

namespace App\Services\Public\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getCart(): Cart
    {
        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->with('items')
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'status' => 'active',
            ]);
        }

        return $cart->load('items');
    }

    public function addItem(int $variantId, int $quantity): CartItem
    {
        $cart = $this->getCart();

        $variant = ProductVariant::with('product.images')
            ->findOrFail($variantId);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        $requestedQuantity = ($item?->quantity ?? 0) + $quantity;

        if ($requestedQuantity > $variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade solicitada não está disponível em estoque.',
            ]);
        }

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item;
        }

        $image = optional($variant->product->images->first())->image ?? '';

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $variant->product->name,
            'image_snapshot' => $image,
            'color_snapshot' => $variant->color,
            'size_snapshot' => $variant->size,
            'price' => $variant->product->price,
            'quantity' => $quantity,
        ]);
    }

    public function updateItem(int $itemId, int $quantity): CartItem
    {
        $cart = $this->getCart();

        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        $item->loadMissing('variant');

        if (! $item->variant || $quantity > $item->variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade solicitada não está disponível em estoque.',
            ]);
        }

        $item->update([
            'quantity' => $quantity,
        ]);

        return $item;
    }

    public function removeItem(int $itemId): bool
    {
        $cart = $this->getCart();

        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        return $item->delete();
    }
}
