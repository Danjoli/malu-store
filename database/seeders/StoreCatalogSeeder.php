<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            ['category' => 'Vestidos', 'name' => 'Vestido Midi Floral', 'price' => 109.90, 'image' => 'catalogo-vestido-floral.png', 'description' => 'Vestido midi leve, com caimento delicado e estampa floral para ocasiões especiais.'],
            ['category' => 'Vestidos', 'name' => 'Vestido Aurora', 'price' => 119.90, 'image' => 'catalogo-vestido-floral.png', 'description' => 'Vestido feminino elegante, pensado para acompanhar você do dia à noite.'],
            ['category' => 'Conjuntos', 'name' => 'Conjunto Linho', 'price' => 129.90, 'image' => 'catalogo-conjunto-linho.png', 'description' => 'Conjunto em tecido leve, com modelagem confortável e acabamento sofisticado.'],
            ['category' => 'Conjuntos', 'name' => 'Conjunto Alfaiataria', 'price' => 159.90, 'image' => 'catalogo-conjunto-linho.png', 'description' => 'Alfaiataria moderna para um visual marcante e versátil.'],
            ['category' => 'Blusas', 'name' => 'Blusa Decote V', 'price' => 69.90, 'image' => 'catalogo-blusa-decote-v.png', 'description' => 'Blusa de caimento fluido e decote V, essencial para combinações elegantes.'],
            ['category' => 'Blusas', 'name' => 'Blusa Manga Bufante', 'price' => 79.90, 'image' => 'catalogo-blusa-decote-v.png', 'description' => 'Blusa com mangas volumosas e acabamento romântico.'],
            ['category' => 'Calças', 'name' => 'Calça Wide Leg Jeans', 'price' => 119.90, 'image' => 'catalogo-calca-wide-leg.png', 'description' => 'Calça jeans wide leg de cintura alta e modelagem confortável.'],
            ['category' => 'Calças', 'name' => 'Calça Pantalona', 'price' => 129.90, 'image' => 'catalogo-calca-wide-leg.png', 'description' => 'Pantalona de corte amplo e elegante, ideal para composições atemporais.'],
            ['category' => 'Saias', 'name' => 'Saia Midi Floral', 'price' => 89.90, 'image' => 'catalogo-vestido-floral.png', 'description' => 'Saia midi floral de caimento leve, perfeita para produções românticas e elegantes.'],
            ['category' => 'Acessórios', 'name' => 'Cinto Elegance', 'price' => 59.90, 'image' => 'catalogo-conjunto-linho.png', 'description' => 'Cinto versátil para finalizar produções com um toque de sofisticação.'],
        ];

        foreach ($catalog as $item) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($item['category'])],
                ['name' => $item['category']]
            );

            $product = Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $category->id,
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'active' => true,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image' => $item['image']]
            );

            foreach (['P', 'M', 'G'] as $size) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'color' => 'Off-white', 'size' => $size],
                    ['stock' => $size === 'M' ? 12 : 8]
                );
            }
        }
    }
}
