<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * A customer may only change addresses that belong to them.
     */
    public function update(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * A customer may only remove addresses that belong to them.
     */
    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }
}
