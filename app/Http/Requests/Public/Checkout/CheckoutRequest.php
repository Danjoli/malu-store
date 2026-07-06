<?php

namespace App\Http\Requests\Public\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'street'         => 'required|string|max:255',
            'number'         => 'required|string|max:20',
            'neighborhood'   => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|size:2',
            'cep'            => 'required|string|max:20',
            'cpf'            => 'required|string|max:14',

            'shipping_cost'  => 'required|numeric|min:0',
            'carrier'        => 'required|string|max:100',
            'service'        => 'required|string',
        ];
    }

    /**
     * Mensagens personalizadas.
     */
    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Informe o nome do destinatário.',
            'phone.required'          => 'Informe o telefone.',
            'street.required'         => 'Informe a rua.',
            'number.required'         => 'Informe o número.',
            'neighborhood.required'   => 'Informe o bairro.',
            'city.required'           => 'Informe a cidade.',
            'state.required'          => 'Informe o estado.',
            'state.size'              => 'O estado deve conter exatamente 2 caracteres.',
            'cep.required'            => 'Informe o CEP.',
            'cpf.required'            => 'Informe o CPF.',

            'shipping_cost.required' => 'Selecione um frete antes de finalizar o pedido.',
            'shipping_cost.numeric'  => 'O valor do frete é inválido.',
            'shipping_cost.min'      => 'O valor do frete deve ser maior ou igual a zero.',

            'carrier.required'       => 'Selecione uma transportadora.',
            'service.required'       => 'Selecione um serviço de entrega.',
        ];
    }

    /**
     * Tradução dos nomes dos campos.
     */
    public function attributes(): array
    {
        return [
            'recipient_name' => 'nome do destinatário',
            'phone'          => 'telefone',
            'street'         => 'rua',
            'number'         => 'número',
            'neighborhood'   => 'bairro',
            'city'           => 'cidade',
            'state'          => 'estado',
            'cep'            => 'CEP',
            'cpf'            => 'CPF',

            'shipping_cost'  => 'valor do frete',
            'carrier'        => 'transportadora',
            'service'        => 'serviço de entrega',
        ];
    }
}
