<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Notifications\AdminTwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_enabled_two_factor_requires_and_accepts_the_emailed_code(): void
    {
        Notification::fake();
        config(['admin_security.two_factor_enabled' => true]);
        $admin = Admin::query()->create([
            'name' => 'Gestora',
            'email' => 'gestora@malustore.test',
            'password' => Hash::make('Senha!123'),
            'is_active' => true,
            'role' => AdminRole::SuperAdmin,
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'Senha!123',
        ])->assertRedirect(route('admin.two-factor.challenge'));

        $this->assertGuest('admin');
        $this->get(route('admin.two-factor.challenge'))->assertOk();

        $code = null;
        Notification::assertSentTo(
            $admin,
            AdminTwoFactorCodeNotification::class,
            function (AdminTwoFactorCodeNotification $notification) use ($admin, &$code): bool {
                $code = (string) $notification->toMail($admin)->introLines[1];

                return preg_match('/^\d{6}$/', $code) === 1;
            },
        );

        $this->post(route('admin.two-factor.verify'), ['code' => $code])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }
}
