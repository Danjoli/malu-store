<?php

namespace App\Http\Requests\Public\Frete;

use Illuminate\Foundation\Http\FormRequest;

class FreteRequest extends FormRequest
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
            'cep' => ['required', 'string', 'regex:/^\d{8}$/'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ];
    }

    /**
     * Mensagens personalizadas.
     */
    public function messages(): array
    {
        return [
            'cep.required' => 'Informe o CEP.',
            'cep.string' => 'O CEP informado é inválido.',
            'cep.regex' => 'Informe um CEP com 8 dígitos.',
        ];
    }

    /**
     * Tradução dos nomes dos campos.
     */
    public function attributes(): array
    {
        return [
            'cep' => 'CEP',
        ];
    }
}
