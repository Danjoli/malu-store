<?php

namespace App\Http\Requests\Admins\AdminProduct;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],

            'active' => ['required', 'boolean'],

            'images.*' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            'variants' => ['required', 'array', 'min:1'],

            'variants.*.color' => ['required', 'string', 'max:50'],
            'variants.*.size' => ['required', 'string', 'max:20'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoria',
            'name' => 'nome do produto',
            'description' => 'descrição',
            'price' => 'preço',
            'active' => 'status',
            'images' => 'imagens',
            'variants' => 'variações',
            'variants.*.color' => 'cor',
            'variants.*.size' => 'tamanho',
            'variants.*.stock' => 'estoque',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'numeric' => 'O campo :attribute deve ser um número válido.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            'array' => 'O campo :attribute deve ser uma lista válida.',
            'min' => 'O campo :attribute deve ter pelo menos :min.',

            'category_id.required' => 'Selecione uma categoria.',
            'category_id.exists' => 'A categoria selecionada não existe.',

            'name.required' => 'Informe o nome do produto.',
            'price.required' => 'Informe o preço do produto.',
            'price.min' => 'O preço não pode ser negativo.',

            'variants.required' => 'Adicione pelo menos uma variação do produto.',

            'variants.*.color.required' => 'Informe a cor da variação.',
            'variants.*.size.required' => 'Informe o tamanho da variação.',
            'variants.*.stock.required' => 'Informe o estoque da variação.',
            'variants.*.stock.min' => 'O estoque não pode ser negativo.',
        ];
    }
}
