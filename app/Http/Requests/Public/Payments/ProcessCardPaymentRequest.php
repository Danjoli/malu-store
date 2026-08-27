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
            'card_number' => ['required', 'string', 'min:13', 'max:23', 'regex:/^[0-9 ]+$/'],
            'holder_name' => ['required', 'string', 'min:3', 'max:100'],
            'expiration_month' => ['required', 'integer', 'between:1,12'],
            'expiration_year' => ['required', 'integer', 'between:'.now()->year.','.now()->addYears(15)->year],
            'ccv' => ['required', 'digits_between:3,4'],
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

            'expiration_month.required' => 'Informe o mês de validade do cartão.',
            'expiration_month.between' => 'Informe um mês de validade válido.',

            'expiration_year.required' => 'Informe o ano de validade do cartão.',
            'expiration_year.between' => 'Informe um ano de validade válido.',

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
            'expiration_month' => 'mês de validade',
            'expiration_year' => 'ano de validade',
            'ccv' => 'código de segurança',
        ];
    }
}
