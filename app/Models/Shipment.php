<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipment_id',
        'carrier',
        'tracking_code',
        'shipping_cost',
        'service_id',
        'status',
        'label_url',
        'last_update',
        'shipped_at',
        'delivered_at'
    ];

    /*
    |----------------------------------------------------------------------
    | LIBERAR MASS ASSIGNMENT
    |----------------------------------------------------------------------
    */
    protected $guarded = [];

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
