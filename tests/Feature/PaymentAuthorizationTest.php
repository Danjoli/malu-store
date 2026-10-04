<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_payment_routes(): void
    {
        $order = $this->orderFor(User::factory()->create());

        $this->get(route('payment.method', $order))->assertRedirect(route('login'));
        $this->get(route('payment.status', $order))->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_another_customers_payment_pages_or_status(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $order = $this->orderFor($owner);

        $this->actingAs($otherCustomer)->get(route('payment.method', $order))->assertNotFound();
        $this->actingAs($otherCustomer)->get(route('payment.status', $order))->assertNotFound();
        $this->actingAs($otherCustomer)->get(route('payment.success', $order))->assertNotFound();
        $this->actingAs($otherCustomer)->get(route('payment.error', $order))->assertNotFound();
    }

    public function test_payment_creation_endpoints_do_not_accept_get_requests(): void
    {
        $customer = User::factory()->create();
        $order = $this->orderFor($customer);

        $this->actingAs($customer)->get(route('payment.pix', $order))->assertMethodNotAllowed();
        $this->actingAs($customer)->get(route('payment.boleto', $order))->assertMethodNotAllowed();
    }

    private function orderFor(User $user): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Cliente proprietário',
            'street' => 'Rua A',
            'number' => '10',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'status' => 'pending',
        ]);
    }
}
