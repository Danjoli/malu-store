<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Senha@2026'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Cria um cliente pronto para cenários de catálogo, perfil e pedidos.
     */
    public function withStoreData(): static
    {
        return $this->afterCreating(function ($user) {
            $address = Address::factory()->for($user)->create(['is_default' => true]);
            Address::factory()->for($user)->create(['is_default' => false]);

            $product = Product::factory()->create();
            $variant = ProductVariant::factory()->for($product)->create();

            Favorite::firstOrCreate(['user_id' => $user->id, 'product_id' => $product->id]);

            $order = Order::create([
                'user_id' => $user->id,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'cpf' => '12345678909',
                'street' => $address->street,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city' => $address->city,
                'state' => $address->state,
                'cep' => $address->cep,
                'subtotal' => $product->price,
                'shipping' => 19.90,
                'total' => $product->price + 19.90,
                'status' => 'paid',
                'payment_method' => 'pix',
                'gateway_payment_id' => 'factory-order-'.$user->id,
                'paid_at' => now(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'name_snapshot' => $product->name,
                'image_snapshot' => '',
                'color_snapshot' => $variant->color,
                'size_snapshot' => $variant->size,
                'price' => $product->price,
                'quantity' => 1,
            ]);

            Shipment::create([
                'order_id' => $order->id,
                'shipment_id' => 'FACTORY-SHIP-'.$user->id,
                'carrier' => 'Correios',
                'tracking_code' => 'BR'.str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
                'shipping_cost' => 19.90,
                'service_id' => 'PAC',
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);
        });
    }
}
