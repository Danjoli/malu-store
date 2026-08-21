<?php

namespace App\Http\Requests\Public\Profile;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', ...StrongPassword::rules()],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'senha atual',
            'password' => 'nova senha',
            'password_confirmation' => 'confirmação da nova senha',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto válido.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'confirmed' => 'A confirmação da senha não corresponde.',

            'current_password.required' => 'Informe sua senha atual.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.mixed' => 'A nova senha deve ter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A nova senha deve ter pelo menos um número.',
            'password.symbols' => 'A nova senha deve ter pelo menos um símbolo.',
            'password.confirmed' => 'As senhas não conferem.',
        ];
    }
}
