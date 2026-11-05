<?php

namespace App\Services\Public\Shop;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class FavoriteStateService
{
    /**
     * Marca os produtos favoritos com uma unica consulta por pagina.
     *
     * @param  iterable<int, Product>  $products
     */
    public function mark(iterable $products, ?User $user): void
    {
        $products = collect($products);

        if ($products->isEmpty()) {
            return;
        }

        /** @var Collection<int, int> $favoriteIds */
        $favoriteIds = $user
            ? Favorite::query()
                ->where('user_id', $user->id)
                ->whereIn('product_id', $products->pluck('id'))
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
            : collect();

        $lookup = $favoriteIds->flip();

        foreach ($products as $product) {
            $product->setAttribute('is_favorited', $lookup->has((int) $product->id));
        }
    }
}
