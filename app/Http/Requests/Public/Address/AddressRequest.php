<?php

namespace App\Http\Requests\Public\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => 'nullable|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'cep' => 'required|string|max:20',
            'is_default' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'label' => 'Etiqueta',
            'recipient_name' => 'Nome do destinatário',
            'phone' => 'Telefone',
            'street' => 'Rua',
            'number' => 'Número',
            'complement' => 'Complemento',
            'neighborhood' => 'Bairro',
            'city' => 'Cidade',
            'state' => 'Estado',
            'cep' => 'CEP',
            'is_default' => 'Endereço padrão',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'size' => 'O campo :attribute deve ter exatamente :size caracteres.',

            'recipient_name.required' => 'Informe o nome do destinatário.',
            'phone.required' => 'Informe um telefone para contato.',
            'street.required' => 'Informe o nome da rua.',
            'number.required' => 'Informe o número do endereço.',
            'neighborhood.required' => 'Informe o bairro.',
            'city.required' => 'Informe a cidade.',
            'state.required' => 'Informe o estado.',
            'cep.required' => 'Informe o CEP.',
        ];
    }
}
