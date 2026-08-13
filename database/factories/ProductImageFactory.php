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
            // O arquivo deve ser copiado para storage/app/public/products pelo seeder ou upload administrativo.
            'image' => 'produto-exemplo.jpg',
        ];
    }
}
