<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement([
                'Vestido',
                'Conjunto',
                'Blusa',
                'Calça',
                'Saia',
            ]).' '.fake()->unique()->word(),
            'description' => fake()->paragraph(2),
            'price' => fake()->randomFloat(2, 69.90, 299.90),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'active' => false,
        ]);
    }

    /**
     * Cria uma galeria de demonstração para cenários de teste que precisem de fotos.
     */
    public function withGallery(int $count = 3): static
    {
        return $this->afterCreating(function (Product $product) use ($count): void {
            ProductImage::factory()
                ->count($count)
                ->create(['product_id' => $product->id]);
        });
    }
}
