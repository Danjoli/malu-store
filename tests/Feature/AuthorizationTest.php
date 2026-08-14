<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_customer_routes(): void
    {
        $this->get(route('public.cart.index'))->assertRedirect(route('login'));
        $this->get(route('favorites.index'))->assertRedirect(route('login'));
        $this->get(route('profile.orders'))->assertRedirect(route('login'));
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'recipient_name' => 'Cliente',
            'street' => 'Rua A',
            'number' => '1',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'status' => 'pending',
        ]);

        $this->actingAs($otherUser)
            ->get(route('profile.orders.show', $order))
            ->assertNotFound();
    }

    public function test_user_cannot_change_another_users_favorite(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create();
        $this->actingAs($owner)->post(route('favorites.toggle', $product));

        $this->actingAs($otherUser)->post(route('favorites.toggle', $product));

        $this->assertDatabaseCount('favorites', 2);
        $this->assertDatabaseHas('favorites', ['user_id' => $owner->id, 'product_id' => $product->id]);
        $this->assertDatabaseHas('favorites', ['user_id' => $otherUser->id, 'product_id' => $product->id]);
    }
}
