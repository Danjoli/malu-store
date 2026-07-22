<?php

namespace App\Http\Requests\Public\Payments;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCardPaymentRequest extends FormRequest
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
            'card_number' => 'required|string',
            'holder_name' => 'required|string',
            'cpf' => 'required|string',
            'expiration_month' => 'required|string',
            'expiration_year' => 'required|string',
            'ccv' => 'required|string',
        ];
    }

    /**
     * Mensagens personalizadas.
     */
    public function messages(): array
    {
        return [
            'card_number.required' => 'Informe o número do cartão.',
            'card_number.string' => 'O número do cartão informado é inválido.',

            'holder_name.required' => 'Informe o nome do titular do cartão.',
            'holder_name.string' => 'O nome do titular informado é inválido.',

            'cpf.required' => 'Informe o CPF do titular do cartão.',
            'cpf.string' => 'O CPF informado é inválido.',

            'expiration_month.required' => 'Informe o mês de validade do cartão.',
            'expiration_month.string' => 'O mês de validade informado é inválido.',

            'expiration_year.required' => 'Informe o ano de validade do cartão.',
            'expiration_year.string' => 'O ano de validade informado é inválido.',

            'ccv.required' => 'Informe o código de segurança do cartão.',
            'ccv.string' => 'O código de segurança informado é inválido.',
        ];
    }

    /**
     * Tradução dos nomes dos campos.
     */
    public function attributes(): array
    {
        return [
            'card_number' => 'número do cartão',
            'holder_name' => 'nome do titular',
            'cpf' => 'CPF',
            'expiration_month' => 'mês de validade',
            'expiration_year' => 'ano de validade',
            'ccv' => 'código de segurança',
        ];
    }
}
