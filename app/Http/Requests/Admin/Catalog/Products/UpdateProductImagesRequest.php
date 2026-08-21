<?php

namespace App\Http\Requests\Admin\Catalog\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'images' => 'imagens',
            'images.*' => 'imagem',
        ];
    }
}
