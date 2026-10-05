<?php

namespace App\Services\Public\Shop;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class CatalogCacheService
{
    public function remember(string $scope, array $parameters, Closure $load): mixed
    {
        ksort($parameters);

        $version = (int) Cache::get('catalog:version', 1);
        $fingerprint = hash('sha256', json_encode($parameters, JSON_THROW_ON_ERROR));
        $key = "catalog:{$version}:{$scope}:{$fingerprint}";
        $seconds = (int) config('cache.catalog_ttl_seconds', 60);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        try {
            return Cache::lock("{$key}:lock", 15)->block(10, fn () => Cache::remember(
                $key,
                now()->addSeconds($seconds),
                $load,
            ));
        } catch (LockTimeoutException) {
            return $load();
        }
    }

    public static function invalidate(): void
    {
        Cache::increment('catalog:version');
    }
}
