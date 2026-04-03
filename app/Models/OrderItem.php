<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'name_snapshot',
        'image_snapshot',
        'color_snapshot',
        'size_snapshot',
        'price',
        'quantity'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    // Item pertence a um pedido
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Item pertence a uma variante de produto
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Acessor
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
