<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_is_authenticated(): void
    {
        $this->post(route('register'), [
            'name' => 'Cliente Teste',
            'email' => 'cliente@malustore.test',
            'phone' => '11999999999',
            'password' => 'Senha!123',
        ])
            ->assertRedirect('/');

        $user = User::where('email', 'cliente@malustore.test')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('Senha!123', $user->password));
    }

    public function test_customer_cannot_log_in_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente@malustore.test',
            'password' => Hash::make('Senha!123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Senha-incorreta!1',
        ])
            ->assertSessionHas('error');

        $this->assertGuest();
    }
}
