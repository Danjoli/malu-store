<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\Public\Shop\CatalogCacheService;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        if (blank($category->slug)) {
            $category->slug = $this->uniqueSlug($category->name);
        }
    }

    public function saved(Category $category): void
    {
        CatalogCacheService::invalidate();
    }

    public function deleted(Category $category): void
    {
        CatalogCacheService::invalidate();
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'categoria';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Category::query()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
