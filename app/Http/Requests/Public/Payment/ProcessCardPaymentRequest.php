<?php

namespace App\Http\Requests\Public\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCardPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'payment_method_id' => ['required', 'string'],
            'issuer_id' => ['required', 'integer'],
            'installments' => ['required', 'integer', 'min:1'],
            'cpf' => ['required', 'string', 'max:14'],
        ];
    }

    public function attributes(): array
    {
        return [
            'token' => 'token do cartão',
            'payment_method_id' => 'método de pagamento',
            'issuer_id' => 'bandeira do cartão',
            'installments' => 'parcelas',
            'cpf' => 'CPF',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'min' => 'O campo :attribute deve ser no mínimo :min.',

            'token.required' => 'Informe o token do cartão.',
            'payment_method_id.required' => 'Informe o método de pagamento.',
            'issuer_id.required' => 'Informe a bandeira do cartão.',
            'installments.required' => 'Informe o número de parcelas.',
            'installments.min' => 'O número de parcelas deve ser pelo menos 1.',
            'cpf.required' => 'Informe o CPF do titular do cartão.',
        ];
    }
}
