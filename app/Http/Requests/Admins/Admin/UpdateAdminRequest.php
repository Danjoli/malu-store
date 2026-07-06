<?php

namespace App\Http\Requests\Admins\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Admin;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $admin = $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin->id),
            ],

            'password' => ['nullable', 'string', 'min:6'],

            'role' => ['required', 'string', Rule::in(Admin::role())],

            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'password' => 'senha',
            'role' => 'nível de acesso',
            'is_active' => 'status',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'email' => 'Informe um e-mail válido.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',

            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.min' => 'A senha deve possuir no mínimo 6 caracteres.',

            'role.in' => 'O nível de acesso selecionado é inválido.',
        ];
    }
}
