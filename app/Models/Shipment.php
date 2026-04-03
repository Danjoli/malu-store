<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipment_id',     // ✅ NOVO (essencial pra API)
        'carrier',
        'tracking_code',
        'shipping_cost',
        'status',
        'last_update',     // ✅ NOVO (status detalhado)
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}