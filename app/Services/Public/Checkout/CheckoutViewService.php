<?php

namespace App\Services\Public\Checkout;

use App\Models\Address;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutViewService
{
    public function getData()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | USUÁRIO NÃO LOGADO
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return [
                'cart' => null,
                'items' => collect(),
                'subtotal' => 0,
                'total' => 0,
                'shipping' => 0,
                'addresses' => [],
                'address' => null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | BUSCAR ENDEREÇOS DO USUÁRIO
        |--------------------------------------------------------------------------
        */

        $addresses = Address::where('user_id', $user->id)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SELECIONAR ENDEREÇO PADRÃO
        |--------------------------------------------------------------------------
        */

        $address = $addresses
            ->firstWhere('is_default', true)
            ?? $addresses->first();

        /*
        |--------------------------------------------------------------------------
        | PREPARAR ENDEREÇOS PARA O JAVASCRIPT
        |--------------------------------------------------------------------------
        */

        $addressesData = $addresses->map(function ($address) {
            return [
                'id' => $address->id,
                'label' => $address->label,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'cpf' => $address->cpf,
                'cep' => $address->cep,
                'street' => $address->street,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city' => $address->city,
                'state' => $address->state,
                'is_default' => (bool) $address->is_default,
            ];
        })->values()->toArray();

        /*
        |--------------------------------------------------------------------------
        | BUSCAR CARRINHO ATIVO
        |--------------------------------------------------------------------------
        */

        $cart = Cart::with('items.variant.product')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | USUÁRIO SEM CARRINHO
        |--------------------------------------------------------------------------
        */

        if (!$cart) {
            return [
                'cart' => null,
                'items' => collect(),
                'subtotal' => 0,
                'total' => 0,
                'shipping' => 0,
                'addresses' => $addressesData,
                'address' => $address,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ITENS DO CARRINHO
        |--------------------------------------------------------------------------
        */

        $items = $cart->items ?? collect();

        /*
        |--------------------------------------------------------------------------
        | CALCULAR SUBTOTAL
        |--------------------------------------------------------------------------
        */

        $subtotal = $items->sum(function ($item) {
            return (float) $item->price * (int) $item->quantity;
        });

        /*
        |--------------------------------------------------------------------------
        | FRETE
        |--------------------------------------------------------------------------
        */

        $shipping = 0;

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $total = $subtotal + $shipping;

        /*
        |--------------------------------------------------------------------------
        | RETORNAR DADOS PARA A VIEW
        |--------------------------------------------------------------------------
        */

        return [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'addresses' => $addressesData,
            'address' => $address,
        ];
    }
}
