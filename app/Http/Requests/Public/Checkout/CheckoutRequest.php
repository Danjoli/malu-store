<?php

namespace App\Http\Requests\Public\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'cep' => 'required|string|max:20',
            'cpf' => 'required|string|max:14',

            'shipping_cost' => 'required|numeric|min:0',
            'carrier' => 'required|string|max:100',
            'service' => 'required|string',
        ];
    }
}
