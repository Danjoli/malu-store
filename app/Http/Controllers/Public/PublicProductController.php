<?php

namespace App\Http\Controllers\Public;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\ProductSizeGuide;
use Illuminate\Support\Collection;

class PublicProductController extends Controller
{
    public function show(Product $product, ProductSizeGuide $sizeGuide)
    {
        $product = Product::with(['images', 'variants', 'category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('active', 1)
            ->whereKey($product->id)
            ->firstOrFail();

        $relatedProducts = Product::with(['images', 'variants'])
            ->where('active', true)
            ->whereKeyNot($product->id)
            ->inStock()
            ->where('category_id', $product->category_id)
            ->take(4)
            ->get();

        if ($relatedProducts->count() < 4) {
            $relatedProducts = $relatedProducts->concat(
                Product::with(['images', 'variants'])
                    ->where('active', true)
                    ->whereKeyNot($product->id)
                    ->whereNotIn('id', $relatedProducts->pluck('id'))
                    ->inStock()
                    ->take(4 - $relatedProducts->count())
                    ->get()
            );
        }

        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->take(12)
            ->get();

        $verifiedReviewSummary = $product->reviews()
            ->where('is_demo', false)
            ->selectRaw('COUNT(*) as count, AVG(rating) as average')
            ->first();

        $sizeGuideRows = $sizeGuide->for($product);
        $modelInformation = $sizeGuide->modelInformation($product);

        /** @var Collection<int, OrderItem> $eligibleReviewItems */
        $eligibleReviewItems = collect();

        if (auth()->check()) {
            $eligibleReviewItems = OrderItem::query()
                ->with('review')
                ->where('product_id', $product->id)
                ->whereHas('order', fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->where('status', OrderStatus::Delivered->value))
                ->latest()
                ->get();
        }

        return view('public.products.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'eligibleReviewItems',
            'verifiedReviewSummary',
            'sizeGuideRows',
            'modelInformation',
        ));
    }

    public function legacyShow(int $id)
    {
        $product = Product::where('active', true)->findOrFail($id);

        return redirect()->route('product.show', $product);
    }
}
