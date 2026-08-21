<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Casa', 'Trabalho']),
            'recipient_name' => fake()->name(),
            'phone' => '11999999999',
            'street' => fake()->streetName(),
            'number' => (string) fake()->numberBetween(1, 999),
            'complement' => null,
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'is_default' => false,
        ];
    }
}
