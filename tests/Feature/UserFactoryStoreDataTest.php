<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryStoreDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_complete_customer_scenario_when_requested(): void
    {
        $user = User::factory()->withStoreData()->create();

        $this->assertCount(2, $user->addresses);
        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('shipments', 1);
    }
}
