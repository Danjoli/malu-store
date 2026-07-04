<?php

namespace App\Http\Requests\Public\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label'=>'required',
            'recipient_name'=>'required',
            'phone'=>'required',
            'street'=>'required',
            'number'=>'required',
            'neighborhood'=>'required',
            'city'=>'required',
            'state'=>'required',
            'cep'=>'required'
        ];
    }
}
