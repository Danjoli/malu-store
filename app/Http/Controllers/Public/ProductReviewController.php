<?php

namespace App\Http\Controllers\Public;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): RedirectResponse
    {
        $orderItem = OrderItem::query()
            ->whereKey($request->integer('order_item_id'))
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query
                ->where('user_id', $request->user()->id)
                ->where('status', OrderStatus::Delivered->value))
            ->firstOrFail();

        ProductReview::query()->updateOrCreate(
            ['order_item_id' => $orderItem->id],
            [
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'rating' => $request->integer('rating'),
                'title' => $request->string('title')->trim()->value() ?: null,
                'comment' => $request->string('comment')->trim()->value(),
            ],
        );

        return redirect()
            ->to(route('product.show', $product).'#avaliacoes')
            ->with('success', 'Sua avaliação foi salva. Obrigada por compartilhar sua experiência!');
    }
}
