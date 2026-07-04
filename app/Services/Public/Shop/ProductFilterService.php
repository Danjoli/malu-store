<?php

namespace App\Services\Public\Shop;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductFilterService
{
    public function filter(Request $request)
    {
        $query = Product::with(['images', 'variants'])
            ->where('active', 1)
            ->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });

        // Busca por nome
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Preço mínimo
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        // Preço máximo
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Cor
        if ($request->color) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color', $request->color)
                  ->where('stock', '>', 0);
            });
        }

        // Tamanho
        if ($request->size) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size', $request->size)
                  ->where('stock', '>', 0);
            });
        }

        return $query->get();
    }
}
