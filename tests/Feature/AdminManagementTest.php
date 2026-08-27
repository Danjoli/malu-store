<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_category(): void
    {
        $response = $this->actingAs($this->admin(AdminRole::SuperAdmin), 'admin')
            ->post(route('admin.categories.store'), [
                'name' => 'Coleção cápsula',
                'slug' => 'colecao-capsula',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Coleção cápsula',
            'slug' => 'colecao-capsula',
        ]);
    }

    public function test_admin_cannot_create_a_category(): void
    {
        $this->actingAs($this->admin(AdminRole::Admin), 'admin')
            ->post(route('admin.categories.store'), [
                'name' => 'Coleção não autorizada',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('categories', [
            'name' => 'Coleção não autorizada',
        ]);
    }

    public function test_admin_dashboard_loads_with_operational_metrics(): void
    {
        $this->actingAs($this->admin(AdminRole::Admin), 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHasAll([
                'totalProducts',
                'totalOrders',
                'totalClients',
                'salesThisMonth',
                'lowStockProducts',
            ]);
    }

    public function test_super_admin_can_create_a_product_with_variants_and_image(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin(AdminRole::SuperAdmin), 'admin')
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Vestido de teste',
                'description' => 'Produto criado pelo teste administrativo.',
                'price' => 189.90,
                'active' => true,
                'images' => [UploadedFile::fake()->image('vestido.jpg')],
                'variants' => [
                    ['color' => 'Rosé', 'size' => 'M', 'stock' => 8],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Vestido de teste',
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'color' => 'Rosé',
            'size' => 'M',
            'stock' => 8,
        ]);
        $this->assertCount(1, Storage::disk('public')->files('products'));
    }

    public function test_admin_can_update_a_shipment_with_an_allowed_status(): void
    {
        $shipment = $this->shipment();

        $response = $this->actingAs($this->admin(AdminRole::Admin), 'admin')
            ->put(route('admin.shipments.update', $shipment), [
                'tracking_code' => 'BR000000123',
                'status' => 'in_transit',
            ]);

        $response->assertRedirect(route('admin.shipments.index'));
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'tracking_code' => 'BR000000123',
            'status' => 'in_transit',
        ]);
    }

    public function test_shipment_update_rejects_an_unknown_status(): void
    {
        $shipment = $this->shipment();

        $this->actingAs($this->admin(AdminRole::Admin), 'admin')
            ->from(route('admin.shipments.edit', $shipment))
            ->put(route('admin.shipments.update', $shipment), [
                'status' => 'status-invalido',
            ])
            ->assertRedirect(route('admin.shipments.edit', $shipment))
            ->assertSessionHasErrors('status');
    }

    private function admin(AdminRole $role): Admin
    {
        return Admin::create([
            'name' => $role->label(),
            'email' => $role->value.'-'.fake()->unique()->safeEmail(),
            'password' => Hash::make('Senha@2026'),
            'is_active' => true,
            'role' => $role,
        ]);
    }

    private function shipment(): Shipment
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Cliente de teste',
            'phone' => '11999999999',
            'cpf' => '12345678909',
            'street' => 'Rua de teste',
            'number' => '10',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => 100,
            'shipping' => 15,
            'total' => 115,
            'status' => 'paid',
        ]);

        return Shipment::create([
            'order_id' => $order->id,
            'carrier' => 'Correios',
            'service_id' => '1',
            'status' => 'pending',
        ]);
    }
}
