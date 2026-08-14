<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * A customer may only access their own order.
     */
    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}
