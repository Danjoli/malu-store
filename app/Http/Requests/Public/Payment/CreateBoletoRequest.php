<?php

namespace App\Http\Requests\Public\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreateBoletoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'pedido',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número válido.',
            'exists' => 'O :attribute informado não foi encontrado.',

            'order_id.required' => 'Informe o pedido para gerar o boleto.',
            'order_id.exists' => 'O pedido informado não existe ou já foi removido.',
        ];
    }
}
