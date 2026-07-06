<?php

namespace App\Http\Requests\Admins\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug'),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome da categoria',
            'slug' => 'slug',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'unique' => 'Este :attribute já está em uso.',

            'name.required' => 'Informe o nome da categoria.',
            'slug.required' => 'Informe o slug da categoria.',
            'slug.unique' => 'Este slug já está em uso.',
        ];
    }
}
