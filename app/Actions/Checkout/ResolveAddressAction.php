<?php

namespace App\Actions\Checkout;

use App\Data\CheckoutData;
use App\Models\Address;
use App\Models\User;

class ResolveAddressAction
{
    public function execute(User $user, CheckoutData $data): Address
    {
        $attributes = ['label' => $data->label, 'recipient_name' => $data->recipientName, 'phone' => $data->phone, 'street' => $data->street, 'number' => $data->number, 'complement' => $data->complement, 'neighborhood' => $data->neighborhood, 'city' => $data->city, 'state' => $data->state, 'cep' => $data->cep];
        if ($data->addressId) {
            $address = $user->addresses()->findOrFail($data->addressId);
            $address->update($attributes);

            return $address->fresh();
        }

        return Address::updateOrCreate(
            ['user_id' => $user->id, 'recipient_name' => $data->recipientName, 'street' => $data->street, 'number' => $data->number, 'cep' => $data->cep],
            $attributes + ['user_id' => $user->id, 'is_default' => $data->isDefault]
        );
    }
}
