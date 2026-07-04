<?php

namespace App\Http\Requests\Public\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePixRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
        ];
    }
}
