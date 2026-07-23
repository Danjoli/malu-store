<?php

namespace App\Services\Public\Checkout;

use App\Models\{Cart, Address, Order, OrderItem, Shipment};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    public function process(array $data)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($user, $data) {

            $cart = Cart::with('items.variant')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Carrinho vazio');
            }

            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);

            $shipping = (float) $data['shipping_cost'];
            $total = $subtotal + $shipping;

            /*
            |--------------------------------------------------------------------------
            | ENDEREÇO
            |--------------------------------------------------------------------------
            */

            $addressId = $data['address_id'] ?? null;

            if ($addressId) {

                // Usa o endereço existente selecionado pelo usuário
                // e garante que ele pertence ao usuário logado.
                $address = Address::where('user_id', $user->id)
                    ->findOrFail($addressId);

            } else {

                // Novo endereço
                $cpf = preg_replace('/\D/', '', $data['cpf']);

                $address = Address::where('user_id', $user->id)
                    ->where('recipient_name', $data['recipient_name'])
                    ->where('phone', $data['phone'])
                    ->where('cpf', $cpf)
                    ->where('street', $data['street'])
                    ->where('number', $data['number'])
                    ->where('neighborhood', $data['neighborhood'])
                    ->where('city', $data['city'])
                    ->where('state', strtoupper($data['state']))
                    ->where('cep', $data['cep'])
                    ->where(function ($query) use ($data) {
                        $complement = $data['complement'] ?? null;

                        if ($complement === null || $complement === '') {
                            $query->whereNull('complement')
                                ->orWhere('complement', '');
                        } else {
                            $query->where('complement', $complement);
                        }
                    })
                    ->first();

                // Se não encontrou endereço igual, cria um novo
                if (!$address) {

                    $address = Address::create([
                        'user_id' => $user->id,
                        'label' => $data['label'] ?? null,
                        'recipient_name' => $data['recipient_name'],
                        'phone' => $data['phone'],
                        'street' => $data['street'],
                        'number' => $data['number'],
                        'complement' => $data['complement'] ?? null,
                        'neighborhood' => $data['neighborhood'],
                        'city' => $data['city'],
                        'state' => strtoupper($data['state']),
                        'cep' => $data['cep'],
                        'cpf' => $cpf,
                        'is_default' => !empty($data['is_default']),
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CANCELAR PEDIDOS PENDENTES ANTERIORES
            |--------------------------------------------------------------------------
            */

            Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            /*
            |--------------------------------------------------------------------------
            | CRIAR PEDIDO
            |--------------------------------------------------------------------------
            */

            $order = Order::create([
                'user_id' => $user->id,

                // Snapshot do endereço
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'cpf' => $address->cpf,
                'street' => $address->street,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city' => $address->city,
                'state' => $address->state,
                'cep' => $address->cep,

                // Valores
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,

                // Status
                'status' => 'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | CRIAR ITENS DO PEDIDO
            |--------------------------------------------------------------------------
            */

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'name_snapshot' => $item->name_snapshot,
                    'image_snapshot' => $item->image_snapshot,
                    'color_snapshot' => $item->color_snapshot,
                    'size_snapshot' => $item->size_snapshot,
                    'price' => $item->price,
                    'quantity' => $item->quantity
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CRIAR ENVIO
            |--------------------------------------------------------------------------
            */

            Shipment::create([
                'order_id' => $order->id,
                'carrier' => $data['carrier'],
                'shipping_cost' => $shipping,
                'service_id' => $data['service'],
                'status' => 'pending'
            ]);

            return $order;
        });
    }
}
