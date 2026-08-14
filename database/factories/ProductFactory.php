<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement(['Vestido', 'Conjunto', 'Blusa', 'Calça', 'Saia']).' '.fake()->unique()->word(),
            'description' => fake()->paragraph(2),
            'price' => fake()->randomFloat(2, 69.90, 299.90),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
