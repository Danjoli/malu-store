<?php

namespace App\Models;

use App\Support\ProductImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'name_snapshot',
        'image_snapshot',
        'color_snapshot',
        'size_snapshot',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public function getImageUrlAttribute(): string
    {
        return app(ProductImageStorage::class)->url($this->image_snapshot);
    }
}
