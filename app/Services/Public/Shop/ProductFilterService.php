<?php

namespace App\Services\Public\Shop;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilterService
{
    /**
     * Centraliza os filtros usados pela home e pelo catálogo público.
     *
     * @return Builder<Product>
     */
    public function query(Request $request): Builder
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('active', 1)
            ->inStock($request->string('color')->toString(), $request->string('size')->toString());

        // Busca por nome
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Preço mínimo
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        // Preço máximo
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        return $query;
    }

    public function filter(Request $request)
    {
        return $this->query($request)->get();
    }
}
