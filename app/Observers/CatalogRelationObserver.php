<?php

namespace App\Observers;

use App\Services\Public\Shop\CatalogCacheService;
use Illuminate\Database\Eloquent\Model;

class CatalogRelationObserver
{
    public function saved(Model $model): void
    {
        CatalogCacheService::invalidate();
    }

    public function deleted(Model $model): void
    {
        CatalogCacheService::invalidate();
    }
}
