<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductVariant> */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color' => fake()->randomElement(['Preto', 'Off-white', 'Rosé', 'Areia', 'Verde oliva']),
            'size' => fake()->randomElement(['P', 'M', 'G', 'GG']),
            'stock' => fake()->numberBetween(3, 25),
        ];
    }
}
