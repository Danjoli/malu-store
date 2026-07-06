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
            'cep' => 'required|string',
        ];
    }

    /**
     * Mensagens personalizadas.
     */
    public function messages(): array
    {
        return [
            'cep.required' => 'Informe o CEP.',
            'cep.string'   => 'O CEP informado é inválido.',
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
