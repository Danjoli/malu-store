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
        $colors = array_values(array_filter((array) $request->input('color'), 'is_string'));
        $sizes = array_values(array_filter((array) $request->input('size'), 'is_string'));

        $query = Product::with(['category', 'images', 'variants'])
            ->where('active', 1)
            ->inStock($colors, $sizes);

        // Busca por nome
        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(fn (Builder $builder) => $builder
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%'));
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
