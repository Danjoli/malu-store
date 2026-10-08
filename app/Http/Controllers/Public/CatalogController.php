<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Public\Shop\CatalogCacheService;
use App\Services\Public\Shop\ProductFilterService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(
        Request $request,
        ProductFilterService $productFilter,
        CatalogCacheService $catalogCache,
    ) {
        $data = $catalogCache->remember('index', $request->query(), function () use ($request, $productFilter): array {
            $query = $productFilter->query($request);

            $sort = $request->get('sort', 'recent');
            match ($sort) {
                'price_asc' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                default => $query->latest(),
            };

            return [
                // O catalogo nao precisa contar todos os produtos para exibir a
                // navegacao. simplePaginate evita uma contagem cara em bases grandes.
                'products' => $query->simplePaginate(9)->withQueryString(),
                'categories' => Category::orderBy('name')->get(),
            ];
        });

        return view('public.catalog.index', $data);
    }
}
