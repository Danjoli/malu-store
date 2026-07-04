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
            'status' => ['required', 'string'],
        ];
    }
}
