<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;

class PublicProductController extends Controller
{
    public function show(Product $product)
    {
        $product = Product::with(['images', 'variants', 'category'])
            ->where('active', 1)
            ->whereKey($product->id)
            ->firstOrFail();

        $relatedProducts = Product::with(['images', 'variants'])
            ->where('active', true)
            ->whereKeyNot($product->id)
            ->whereHas('variants', fn ($variants) => $variants->where('stock', '>', 0))
            ->where('category_id', $product->category_id)
            ->take(4)
            ->get();

        if ($relatedProducts->count() < 4) {
            $relatedProducts = $relatedProducts->concat(
                Product::with(['images', 'variants'])
                    ->where('active', true)
                    ->whereKeyNot($product->id)
                    ->whereNotIn('id', $relatedProducts->pluck('id'))
                    ->whereHas('variants', fn ($variants) => $variants->where('stock', '>', 0))
                    ->take(4 - $relatedProducts->count())
                    ->get()
            );
        }

        return view('public.products.show', compact('product', 'relatedProducts'));
    }

    public function legacyShow(int $id)
    {
        $product = Product::where('active', true)->findOrFail($id);

        return redirect()->route('product.show', $product);
    }
}
