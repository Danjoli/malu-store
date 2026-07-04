<?php

namespace App\Http\Requests\Admins\AdminProduct;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],

            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'variants' => ['required', 'array'],
            'variants.*.color' => ['required', 'string'],
            'variants.*.size' => ['required', 'string'],
            'variants.*.stock' => ['required', 'integer'],
        ];
    }
}
