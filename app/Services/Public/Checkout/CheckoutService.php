<?php

namespace App\Services\Public\Checkout;

use App\Actions\Checkout\CreateOrderAction;
use App\Actions\Checkout\CreateOrderItemsAction;
use App\Actions\Checkout\CreateShipmentAction;
use App\Actions\Checkout\ResolveAddressAction;
use App\Data\CheckoutData;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CheckoutService
{
    public function __construct(private ResolveAddressAction $resolveAddress, private CreateOrderAction $createOrder, private CreateOrderItemsAction $createOrderItems, private CreateShipmentAction $createShipment) {}

    public function process(array $input): Order
    {
        $user = Auth::user();
        if (! $user) {
            throw new RuntimeException('Usuário não autenticado.');
        }
        $data = CheckoutData::fromArray($input);

        return DB::transaction(function () use ($user, $data) {
            $cart = Cart::with('items.variant')->where('user_id', $user->id)->where('status', 'active')->firstOrFail();
            if ($cart->items->isEmpty()) {
                throw new RuntimeException('Carrinho vazio.');
            }
            $address = $this->resolveAddress->execute($user, $data);
            $order = $this->createOrder->execute($user, $cart, $address, $data);
            $this->createOrderItems->execute($order, $cart);
            $this->createShipment->execute($order, $data);

            Log::info('Checkout concluído.', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total' => $order->total,
                'payment_status' => $order->status,
            ]);

            return $order;
        });
    }
}
