<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_cannot_create_more_than_ten_addresses(): void
    {
        $user = User::factory()->create();
        Address::factory()->count(10)->for($user)->create();

        $this->actingAs($user)
            ->postJson(route('addresses.store'), [
                'recipient_name' => 'Cliente Teste', 'phone' => '11999999999', 'street' => 'Rua A',
                'number' => '10', 'neighborhood' => 'Centro', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('address');

        $this->assertDatabaseCount('addresses', 10);
    }
}
