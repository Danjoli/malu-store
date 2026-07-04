<?php

namespace App\Http\Requests\Public\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest  extends FormRequest
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
            'state' => 'required|string|max:2',
            'cep' => 'required|string|max:20',
            'cpf' => 'required|string|max:14',
            'is_default' => 'nullable',
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
            'cpf' => 'CPF',
        ];
    }
}
