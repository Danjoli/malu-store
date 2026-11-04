<?php

namespace App\Support;

use App\Models\Product;

class ProductSizeGuide
{
    /** @return array<int, array{size: string, bust: string, waist: string, hip: string}> */
    public function for(Product $product): array
    {
        $measurements = [
            'P' => ['bust' => '82–88', 'waist' => '64–70', 'hip' => '90–96'],
            'M' => ['bust' => '88–94', 'waist' => '70–76', 'hip' => '96–102'],
            'G' => ['bust' => '94–102', 'waist' => '76–84', 'hip' => '102–110'],
            'GG' => ['bust' => '102–110', 'waist' => '84–92', 'hip' => '110–118'],
        ];

        return $product->variants
            ->pluck('size')
            ->filter(fn (string $size) => isset($measurements[$size]))
            ->unique()
            ->sortBy(fn (string $size) => array_search($size, ['P', 'M', 'G', 'GG'], true))
            ->map(fn (string $size) => ['size' => $size, ...$measurements[$size]])
            ->values()
            ->all();
    }

    public function modelInformation(Product $product): string
    {
        $category = mb_strtolower($product->category?->name ?? '');

        return str_contains($category, 'vestido')
            ? 'Modelo de referência: 1,68 m de altura e veste tamanho M.'
            : 'Modelo de referência: 1,68 m de altura, busto 88 cm, cintura 70 cm, quadril 98 cm e veste tamanho M.';
    }
}
