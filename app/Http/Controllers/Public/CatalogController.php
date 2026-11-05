<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\Public\Shop\CatalogCacheService;
use App\Services\Public\Shop\FavoriteStateService;
use App\Services\Public\Shop\ProductFilterService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(
        Request $request,
        ProductFilterService $productFilter,
        CatalogCacheService $catalogCache,
        FavoriteStateService $favoriteState,
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
                'categories' => Category::query()
                    ->whereHas('products', fn ($productQuery) => $productQuery
                        ->where('active', true)
                        ->whereHas('variants', fn ($variantQuery) => $variantQuery->where('stock', '>', 0)))
                    ->orderBy('name')
                    ->get(),
                'filterColors' => ProductVariant::query()
                    ->join('products', 'products.id', '=', 'product_variants.product_id')
                    ->where('products.active', true)
                    ->where('product_variants.stock', '>', 0)
                    ->whereNotNull('product_variants.color')
                    ->where('product_variants.color', '!=', '')
                    ->distinct()
                    ->orderBy('product_variants.color')
                    ->pluck('product_variants.color'),
                'filterSizes' => ProductVariant::query()
                    ->join('products', 'products.id', '=', 'product_variants.product_id')
                    ->where('products.active', true)
                    ->where('product_variants.stock', '>', 0)
                    ->whereNotNull('product_variants.size')
                    ->where('product_variants.size', '!=', '')
                    ->distinct()
                    ->pluck('product_variants.size')
                    ->sortBy(fn (string $size) => match ($size) {
                        'PP' => 0,
                        'P' => 1,
                        'M' => 2,
                        'G' => 3,
                        'GG' => 4,
                        default => 99,
                    })
                    ->values(),
            ];
        });

        $favoriteState->mark($data['products']->items(), $request->user());

        return view('public.catalog.index', $data);
    }
}
