<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_admin_can_search_and_combine_catalog_filters(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();
        $match = Product::factory()->for($category)->create(['name' => 'Camisa Aurora', 'active' => true]);
        ProductVariant::factory()->for($match)->create(['stock' => 5]);
        Product::factory()->for($category)->create(['name' => 'Outro produto', 'active' => false]);

        $this->actingAs($admin, 'admin')->get(route('admin.products.index', [
            'q' => 'Aurora', 'category' => $category->id, 'status' => 'active', 'stock' => 'available',
        ]))->assertOk()->assertSee('Camisa Aurora')->assertDontSee('Outro produto');
    }

    public function test_customer_and_order_search_preserve_relevant_results(): void
    {
        $admin = $this->admin();
        $customer = User::factory()->create(['name' => 'Cliente Buscável', 'email' => 'busca@example.com']);
        Order::query()->create([
            'user_id' => $customer->id, 'recipient_name' => $customer->name, 'street' => 'Rua Teste',
            'number' => '1', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000',
            'subtotal' => 100, 'shipping' => 10, 'total' => 110, 'status' => 'paid',
        ]);
        User::factory()->create(['name' => 'Pessoa Diferente']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.clients.index', ['q' => 'busca@example.com', 'orders' => 'with']))
            ->assertOk()->assertSee('Cliente Buscável')->assertDontSee('Pessoa Diferente');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index', ['q' => 'Cliente Buscável', 'status' => 'paid']))
            ->assertOk()->assertSee('Cliente Buscável');
    }

    private function admin(): Admin
    {
        return Admin::query()->create([
            'name' => 'Super Admin', 'email' => 'admin@malustore.test',
            'password' => Hash::make('Senha!123'), 'is_active' => true, 'role' => AdminRole::SuperAdmin,
        ]);
    }
}
