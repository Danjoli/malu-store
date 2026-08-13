<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('active', true)
            ->whereHas('variants', fn ($variants) => $variants->where('stock', '>', 0));

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($category) => $category->where('slug', $request->category));
        }
        if ($request->filled('color')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('color', $request->color)->where('stock', '>', 0));
        }
        if ($request->filled('size')) {
            $query->whereHas('variants', fn ($variant) => $variant->where('size', $request->size)->where('stock', '>', 0));
        }
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        return view('public.catalog.index', [
            'products' => $query->paginate(9)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
