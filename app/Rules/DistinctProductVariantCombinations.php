<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DistinctProductVariantCombinations implements ValidationRule
{
    /**
     * Impede variações iguais de cor e tamanho no mesmo produto.
     *
     * @param  array<int, array{color?: string, size?: string}>  $value
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        $combinations = [];

        foreach ($value as $variant) {
            $color = mb_strtolower(trim((string) ($variant['color'] ?? '')));
            $size = mb_strtolower(trim((string) ($variant['size'] ?? '')));

            if ($color === '' || $size === '') {
                continue;
            }

            $combination = $color.'|'.$size;

            if (isset($combinations[$combination])) {
                $fail('Não repita a mesma combinação de cor e tamanho.');

                return;
            }

            $combinations[$combination] = true;
        }
    }
}
