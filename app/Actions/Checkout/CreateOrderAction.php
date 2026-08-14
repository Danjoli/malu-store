<?php

namespace App\Actions\Checkout;

use App\Data\CheckoutData;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class CreateOrderAction
{
    public function execute(User $user, Cart $cart, Address $address, CheckoutData $data): Order
    {
        $subtotal = $cart->items->sum(fn ($item) => $item->price * $item->quantity);
        Order::where('user_id', $user->id)->where('status', 'pending')->update(['status' => 'cancelled']);

        return Order::create(['user_id' => $user->id, 'recipient_name' => $address->recipient_name, 'phone' => $address->phone, 'cpf' => $data->cpf, 'street' => $address->street, 'number' => $address->number, 'complement' => $address->complement, 'neighborhood' => $address->neighborhood, 'city' => $address->city, 'state' => $address->state, 'cep' => $address->cep, 'subtotal' => $subtotal, 'shipping' => $data->shippingCost, 'total' => $subtotal + $data->shippingCost, 'status' => 'pending']);
    }
}
