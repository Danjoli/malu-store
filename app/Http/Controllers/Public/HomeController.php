<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Public\Shop\CatalogCacheService;
use App\Services\Public\Shop\ProductFilterService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private const PRODUCT_LIMIT = 12;

    protected $productFilterService;

    public function __construct(ProductFilterService $productFilterService)
    {
        $this->productFilterService = $productFilterService;
    }

    public function index(Request $request, CatalogCacheService $catalogCache)
    {
        $products = $catalogCache->remember(
            'home',
            $request->query(),
            fn () => $this->productFilterService
                ->query($request)
                ->latest('created_at')
                ->latest('id')
                ->limit(self::PRODUCT_LIMIT)
                ->get(),
        );

        $categories = Category::query()
            ->whereHas('products', fn ($query) => $query->where('active', true)->inStock())
            ->with(['products' => fn ($query) => $query
                ->where('active', true)
                ->inStock()
                ->with('images')
                ->latest('created_at')
                ->latest('id')
                ->limit(1)])
            ->orderBy('name')
            ->get();

        return view('public.home.index', compact('products', 'categories'));
    }
}
