<?php

namespace App\Http\Requests\Public\Frete;

use Illuminate\Foundation\Http\FormRequest;

class FreteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cep' => 'required|string',
        ];
    }
}
