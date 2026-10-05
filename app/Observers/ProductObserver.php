<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Public\Shop\CatalogCacheService;
use Illuminate\Support\Str;

class ProductObserver
{
    public function saving(Product $product): void
    {
        if (blank($product->slug)) {
            $product->slug = $this->uniqueSlug($product->name);
        }
    }

    public function saved(Product $product): void
    {
        CatalogCacheService::invalidate();
    }

    public function deleted(Product $product): void
    {
        CatalogCacheService::invalidate();
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'produto';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
