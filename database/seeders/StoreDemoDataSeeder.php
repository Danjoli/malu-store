<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'test@gmail.com'
            ],
            [
                'name' => 'test',
                'phone' => '(11) 99999-9999',
                'password' => Hash::make('Senha@2026')
            ]
        );

        Admin::updateOrCreate(
            [
                'email' => 'admin@malustore.test'
            ],
            [
                'name' => 'Administrador Malu Store',
                'password' => Hash::make('Senha@2026'),
                'role' => 'superadmin',
                'is_active' => true
            ]
        );

        $address = Address::updateOrCreate(
            [
                'user_id' => $user->id,
                'label' => 'Casa'
            ],
            [
                'recipient_name' => 'test',
                'phone' => '(11) 99999-9999',
                'street' => 'Rua das Flores',
                'number' => '123',
                'complement' => 'Apto 12',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'cep' => '01001-000',
                'is_default' => true
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $user->id, 'label' => 'Trabalho'],
            ['recipient_name' => 'test', 'phone' => '(11) 99999-9999', 'street' => 'Avenida Paulista', 'number' => '1000', 'neighborhood' => 'Bela Vista', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01310-100', 'is_default' => false]
        );

        $products = Product::with(['images', 'variants'])
            ->where('active', true)
            ->take(4)
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        foreach ($products->take(3) as $product) {
            Favorite::firstOrCreate(['user_id' => $user->id, 'product_id' => $product->id]);
        }

        $statuses = [
            'pending_payment',
            'paid',
            'shipped',
            'delivered'
        ];

        foreach ($statuses as $index => $status) {
            $product = $products[$index % $products->count()];
            $variant = $product->variants->first();
            $quantity = $index % 2 + 1;
            $subtotal = $product->price * $quantity;
            $shipping = $index === 0 ? 0 : 19.90;

            $order = Order::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'gateway_payment_id' => 'demo-order-'.($index + 1)
                ],
                [
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'cpf' => '123.456.789-09',
                    'street' => $address->street,
                    'number' => $address->number,
                    'complement' => $address->complement,
                    'neighborhood' => $address->neighborhood,
                    'city' => $address->city,
                    'state' => $address->state,
                    'cep' => $address->cep,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $subtotal + $shipping,
                    'status' => $status,
                    'payment_method' => $index % 2 ? 'pix' : 'credit_card',
                    'gateway_status' => $status,
                    'paid_at' => $status === 'pending_payment'
                        ? null
                        : now()->subDays($index + 2),
                ]
            );

            OrderItem::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id],
                [
                    'name_snapshot' => $product->name,
                    'image_snapshot' => $product->images->first()?->image ?? '',
                    'color_snapshot' => $variant->color,
                    'size_snapshot' => $variant->size,
                    'price' => $product->price,
                    'quantity' => $quantity]
            );

            if ($status !== 'pending_payment') {
                Shipment::updateOrCreate(
                    [
                        'order_id' => $order->id
                    ],
                    [
                        'shipment_id' => 'DEMO-SHIP-'.($index + 1),
                        'service_id' => 'PAC',
                        'carrier' => 'Correios',
                        'tracking_code' => 'BR'.str_pad(
                            (string) ($index + 1),
                            10,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'shipping_cost' => $shipping,
                        'status' => $status === 'paid'
                            ? 'waiting_post'
                            : ($status === 'shipped' ? 'in_transit' : 'delivered'),
                        'shipped_at' => $status !== 'paid'
                            ? now()->subDays($index)
                            : null,
                        'delivered_at' => $status === 'delivered'
                            ? now()->subDay()
                            : null
                    ]
                );
            }
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'active'
        ]);

        $cartProduct = $products->last();
        $cartVariant = $cartProduct->variants->first();

        CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id, 'product_variant_id' => $cartVariant->id
            ],
            [
                'name_snapshot' => $cartProduct->name,
                'image_snapshot' => $cartProduct->images->first()?->image ?? '',
                'color_snapshot' => $cartVariant->color,
                'size_snapshot' => $cartVariant->size,
                'price' => $cartProduct->price,
                'quantity' => 1
            ]
        );
    }
}
