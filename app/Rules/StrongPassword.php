<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

class StrongPassword
{
    /** @return array<int, string|Password> */
    public static function rules(): array
    {
        return [
            'string',
            Password::min(8)->mixedCase()->numbers()->symbols(),
        ];
    }
}
