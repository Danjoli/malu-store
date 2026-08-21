<?php

namespace App\Http\Requests\Admin\Admins;

use App\Enums\AdminRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\StrongPassword;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:admins,email',
            ],

            'password' => ['required', ...StrongPassword::rules()],

            'role' => [
                'required',
                'string',
                Rule::enum(AdminRole::class),
            ],

            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'password' => 'senha',
            'role' => 'cargo',
            'is_active' => 'ativo',
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
            'unique' => 'Este :attribute já está cadastrado.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',

            'name.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'Informe a senha.',
            'password.min' => 'A senha deve possuir no mínimo 8 caracteres.',
            'password.mixed' => 'A senha deve ter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve ter pelo menos um número.',
            'password.symbols' => 'A senha deve ter pelo menos um símbolo.',
            'role.required' => 'Selecione um cargo.',
            'role.in' => 'Cargo inválido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
