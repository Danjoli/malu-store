<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_store_settings(): void
    {
        $admin = $this->admin(AdminRole::SuperAdmin);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.settings.update'), [
                'store_name' => 'Malu Store',
                'support_email' => 'atendimento@malu-store.com',
                'whatsapp' => '5511999999999',
                'free_shipping_threshold' => 349.90,
                'max_installments' => 8,
                'low_stock_threshold' => 7,
                'storefront_enabled' => '1',
                'announcement' => 'Frete grátis em compras selecionadas',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $settings = StoreSetting::current();
        $this->assertSame('349.90', $settings->free_shipping_threshold);
        $this->assertSame(8, $settings->max_installments);
        $this->assertSame(7, $settings->low_stock_threshold);
    }

    public function test_regular_admin_can_view_operations_but_cannot_change_settings(): void
    {
        $admin = $this->admin(AdminRole::Admin);

        $this->actingAs($admin, 'admin')->get(route('admin.operations.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.settings.edit'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_disabled_storefront_shows_maintenance_but_keeps_admin_accessible(): void
    {
        $settings = StoreSetting::current();
        $settings->update(['storefront_enabled' => false]);

        $this->get('/')->assertStatus(503)->assertSee('Estamos preparando novidades');
        $this->get(route('admin.login'))->assertOk();

        $settings->update(['storefront_enabled' => true]);
    }

    private function admin(AdminRole $role): Admin
    {
        return Admin::query()->create([
            'name' => $role->label(),
            'email' => $role->value.'-settings@malustore.test',
            'password' => Hash::make('Senha!123'),
            'is_active' => true,
            'role' => $role,
        ]);
    }
}
