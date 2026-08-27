<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_support_can_view_orders(): void
    {
        $support = $this->admin(AdminRole::Support);

        $this->actingAs($support, 'admin')->get(route('admin.orders.index'))
            ->assertOk();
    }

    public function test_support_cannot_manage_catalog(): void
    {
        $support = $this->admin(AdminRole::Support);

        $this->actingAs($support, 'admin')->get(route('admin.categories.create'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_super_admin_can_access_administrator_management(): void
    {
        $superAdmin = $this->admin(AdminRole::SuperAdmin);

        $this->actingAs($superAdmin, 'admin')->get(route('admin.admins.create'))
            ->assertOk();

        $this->actingAs($superAdmin, 'admin')->get(route('admin.products.create'))
            ->assertOk();
    }

    private function admin(AdminRole $role): Admin
    {
        return Admin::query()->create([
            'name' => $role->label(),
            'email' => $role->value.'@malustore.test',
            'password' => Hash::make('Senha!123'),
            'is_active' => true,
            'role' => $role,
        ]);
    }
}
