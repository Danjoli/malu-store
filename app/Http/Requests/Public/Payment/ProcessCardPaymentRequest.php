<?php

namespace App\Http\Requests\Public\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCardPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'payment_method_id' => 'required|string',
            'issuer_id' => 'required|integer',
            'installments' => 'required|integer|min:1',
            'cpf' => 'required|string',
        ];
    }
}
