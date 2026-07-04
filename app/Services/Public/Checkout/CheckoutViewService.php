<?php

namespace App\Services\Public\Checkout;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutViewService
{
    public function getData()
    {
        $user = Auth::user();

        // Usuário não logado
        if (!$user) {
            return [
                'cart' => null,
                'items' => [],
                'subtotal' => 0,
                'total' => 0,
                'shipping' => 0,
            ];
        }

        // Busca carrinho ativo
        $cart = Cart::with('items.variant.product')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Se não tiver carrinho
        if (!$cart) {
            return [
                'cart' => null,
                'items' => [],
                'subtotal' => 0,
                'total' => 0,
                'shipping' => 0,
            ];
        }

        // Itens do carrinho (evita null no Blade)
        $items = $cart->items ?? collect();

        // Subtotal seguro
        $subtotal = $items->sum(function ($item) {
            return (float) $item->price * (int) $item->quantity;
        });

        // Frete (ainda fixo, pode vir do service depois)
        $shipping = 0;

        // Total final
        $total = $subtotal + $shipping;

        return [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ];
    }
}
