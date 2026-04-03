<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'subtotal',
        'shipping',
        'total',
        'status',
        'payment_method',
        'paid_at',
        
        'gateway_payment_id',
        'expires_at',
        'gateway_status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    // Pedido pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pedido pertence a um endereço
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Pedido possui vários itens
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Pedido pertence a um envio
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
