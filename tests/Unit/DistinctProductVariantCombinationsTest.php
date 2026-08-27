<?php

namespace Tests\Unit;

use App\Rules\DistinctProductVariantCombinations;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DistinctProductVariantCombinationsTest extends TestCase
{
    public function test_it_accepts_different_color_and_size_combinations(): void
    {
        $validator = Validator::make([
            'variants' => [
                ['color' => 'Off-white', 'size' => 'P'],
                ['color' => 'Off-white', 'size' => 'M'],
                ['color' => 'Rosé', 'size' => 'P'],
            ],
        ], [
            'variants' => [new DistinctProductVariantCombinations],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_it_rejects_duplicate_color_and_size_combinations(): void
    {
        $validator = Validator::make([
            'variants' => [
                ['color' => 'Off-white', 'size' => 'M'],
                ['color' => ' off-WHITE ', 'size' => 'm'],
            ],
        ], [
            'variants' => [new DistinctProductVariantCombinations],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'Não repita a mesma combinação de cor e tamanho.',
            $validator->errors()->first('variants')
        );
    }
}
