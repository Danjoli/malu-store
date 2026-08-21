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
            [
                'category' => 'Vestidos',
                'name' => 'Vestido Midi Floral',
                'price' => 109.90,
                'images' => ['catalogo-vestido-floral.png', 'catalogo-conjunto-areia.png', 'catalogo-blusa-oliva.png'],
                'description' => 'Vestido midi leve, com caimento delicado e estampa floral para ocasiões especiais.',
            ],
            [
                'category' => 'Vestidos',
                'name' => 'Vestido Aurora',
                'price' => 119.90,
                'images' => ['catalogo-vestido-floral.png', 'catalogo-blusa-oliva.png', 'catalogo-conjunto-areia.png'],
                'description' => 'Vestido feminino elegante, pensado para acompanhar você do dia à noite.',
            ],
            [
                'category' => 'Conjuntos',
                'name' => 'Conjunto Linho',
                'price' => 129.90,
                'images' => ['catalogo-conjunto-linho.png', 'catalogo-conjunto-areia.png', 'catalogo-blusa-oliva.png'],
                'description' => 'Conjunto em tecido leve, com modelagem confortável e acabamento sofisticado.',
            ],
            [
                'category' => 'Conjuntos',
                'name' => 'Conjunto Alfaiataria',
                'price' => 159.90,
                'images' => ['catalogo-conjunto-areia.png', 'catalogo-conjunto-linho.png', 'catalogo-macacao-preto.png'],
                'description' => 'Alfaiataria moderna para um visual marcante e versátil.',
            ],
            [
                'category' => 'Blusas',
                'name' => 'Blusa Decote V',
                'price' => 69.90,
                'images' => ['catalogo-blusa-decote-v.png', 'catalogo-blusa-oliva.png', 'catalogo-conjunto-areia.png'],
                'description' => 'Blusa de caimento fluido e decote V, essencial para combinações elegantes.',
            ],
            [
                'category' => 'Blusas',
                'name' => 'Blusa Manga Bufante',
                'price' => 79.90,
                'images' => ['catalogo-blusa-oliva.png', 'catalogo-blusa-decote-v.png', 'catalogo-conjunto-linho.png'],
                'description' => 'Blusa com mangas volumosas e acabamento romântico.',
            ],
            [
                'category' => 'Calças',
                'name' => 'Calça Wide Leg Jeans',
                'price' => 119.90,
                'images' => ['catalogo-calca-wide-leg.png', 'catalogo-conjunto-areia.png', 'catalogo-blusa-oliva.png'],
                'description' => 'Calça jeans wide leg de cintura alta e modelagem confortável.',
            ],
            [
                'category' => 'Calças',
                'name' => 'Calça Pantalona',
                'price' => 129.90,
                'images' => ['catalogo-calca-wide-leg.png', 'catalogo-macacao-preto.png', 'catalogo-conjunto-areia.png'],
                'description' => 'Pantalona de corte amplo e elegante, ideal para composições atemporais.',
            ],
            [
                'category' => 'Saias',
                'name' => 'Saia Midi Floral',
                'price' => 89.90,
                'images' => ['catalogo-vestido-floral.png', 'catalogo-blusa-oliva.png', 'catalogo-conjunto-areia.png'],
                'description' => 'Saia midi floral de caimento leve, perfeita para produções românticas e elegantes.',
            ],
            [
                'category' => 'Acessórios',
                'name' => 'Cinto Elegance',
                'price' => 59.90,
                'images' => ['catalogo-macacao-preto.png', 'catalogo-conjunto-linho.png', 'catalogo-conjunto-areia.png'],
                'description' => 'Cinto versátil para finalizar produções com um toque de sofisticação.',
            ],
        ];

        foreach ($catalog as $item) {
            $category = Category::updateOrCreate(
                [
                    'name' => $item['category'],
                ],
                [
                    'slug' => Str::slug($item['category']),
                ]
            );

            $product = Product::updateOrCreate(
                [
                    'name' => $item['name'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['description'],
                    'slug' => Str::slug($item['name']),
                    'price' => $item['price'],
                    'active' => true,
                ]
            );

            // A galeria de demonstração tem três fotos por produto.
            foreach ($item['images'] as $image) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'image' => $image,
                ]);
            }

            foreach (['P', 'M', 'G'] as $size) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'color' => 'Off-white',
                        'size' => $size,
                    ],
                    [
                        'stock' => $size === 'M' ? 12 : 8,
                    ]
                );
            }
        }
    }
}
