<?php

namespace App\Http\Requests\Admins\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_code' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tracking_code' => 'código de rastreio',
            'status' => 'status do envio',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',

            'status.required' => 'Informe o status do envio.',
        ];
    }
}
