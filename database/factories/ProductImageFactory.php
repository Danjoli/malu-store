<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductImage> */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            // Arquivos de demonstração presentes em storage/app/public/products.
            'image' => fake()->randomElement([
                'catalogo-vestido-floral.png',
                'catalogo-conjunto-linho.png',
                'catalogo-blusa-decote-v.png',
                'catalogo-calca-wide-leg.png',
                'catalogo-macacao-preto.png',
                'catalogo-conjunto-areia.png',
                'catalogo-blusa-oliva.png',
            ]),
        ];
    }
}
