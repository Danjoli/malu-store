<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class StoreSetting extends Model
{
    private const CACHE_KEY = 'store-settings.current';

    protected $fillable = [
        'store_name',
        'support_email',
        'whatsapp',
        'free_shipping_threshold',
        'max_installments',
        'low_stock_threshold',
        'storefront_enabled',
        'announcement',
    ];

    protected function casts(): array
    {
        return [
            'free_shipping_threshold' => 'decimal:2',
            'max_installments' => 'integer',
            'low_stock_threshold' => 'integer',
            'storefront_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): self => self::loadCurrent());
    }

    public static function clearCurrentCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget(self::CACHE_KEY));
        static::deleted(fn (): bool => Cache::forget(self::CACHE_KEY));
    }

    private static function loadCurrent(): self
    {
        $defaults = [
            'store_name' => config('app.name', 'Malu Store'),
            'support_email' => config('mail.from.address'),
            'free_shipping_threshold' => 299,
            'max_installments' => 6,
            'low_stock_threshold' => 5,
            'storefront_enabled' => true,
        ];

        if (! Schema::hasTable('store_settings')) {
            return new self($defaults);
        }

        return self::query()->firstOrCreate([], $defaults);
    }
}
