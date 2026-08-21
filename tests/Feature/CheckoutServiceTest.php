<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Public\Checkout\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_address_order_items_and_shipment(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for(Category::factory())->create(['price' => 99.90]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 5]);
        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'color_snapshot' => $variant->color, 'size_snapshot' => $variant->size, 'price' => 99.90, 'quantity' => 2]);

        $order = $this->actingAs($user)->app->make(CheckoutService::class)->process([
            'recipient_name' => 'Cliente Teste', 'phone' => '11999999999', 'cpf' => '123.456.789-09',
            'street' => 'Rua Teste', 'number' => '100', 'complement' => '', 'neighborhood' => 'Centro',
            'city' => 'São Paulo', 'state' => 'sp', 'cep' => '01001-000', 'shipping_cost' => 15.50,
            'carrier' => 'Correios', 'service' => 'PAC',
        ]);

        $this->assertDatabaseHas('addresses', ['user_id' => $user->id, 'cep' => '01001000']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total' => 215.30, 'status' => 'pending']);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'quantity' => 2]);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'carrier' => 'Correios']);
    }

    public function test_checkout_updates_matching_address_instead_of_using_stale_data(): void
    {
        $user = User::factory()->create();
        $address = Address::create(['user_id' => $user->id, 'recipient_name' => 'Cliente Teste', 'phone' => '11111111111', 'street' => 'Rua Teste', 'number' => '100', 'neighborhood' => 'Centro', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000']);
        $product = Product::factory()->for(Category::factory())->create(['price' => 50]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 5]);
        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'color_snapshot' => $variant->color, 'size_snapshot' => $variant->size, 'price' => 50, 'quantity' => 1]);

        $order = $this->actingAs($user)->app->make(CheckoutService::class)->process([
            'recipient_name' => 'Cliente Teste', 'phone' => '11999999999', 'cpf' => '123.456.789-09',
            'street' => 'Rua Teste', 'number' => '100', 'complement' => 'Apto 2', 'neighborhood' => 'Novo Centro',
            'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001-000', 'shipping_cost' => 15,
            'carrier' => 'Correios', 'service' => 'PAC',
        ]);

        $this->assertDatabaseCount('addresses', 1);
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'phone' => '11999999999', 'complement' => 'Apto 2', 'neighborhood' => 'Novo Centro']);
        $this->assertSame('11999999999', $order->phone);
        $this->assertSame('Novo Centro', $order->neighborhood);
    }

    public function test_checkout_request_rejects_missing_shipping_option(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('checkout'))->post(route('checkout.process'), [
            'recipient_name' => 'Cliente', 'phone' => '11999999999', 'cpf' => '123.456.789-09',
            'street' => 'Rua A', 'number' => '10', 'neighborhood' => 'Centro', 'city' => 'São Paulo',
            'state' => 'SP', 'cep' => '01001-000', 'shipping_cost' => 0, 'payment_method' => 'pix',
        ])->assertRedirect(route('checkout'))->assertSessionHasErrors(['shipping_cost', 'carrier', 'service']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_page_renders_the_single_page_steps(): void
    {
        $user = User::factory()->create();
        Address::create(['user_id' => $user->id, 'label' => 'Casa', 'recipient_name' => 'Cliente', 'phone' => '11999999999', 'street' => 'Rua A', 'number' => '10', 'neighborhood' => 'Centro', 'city' => 'São Paulo', 'state' => 'SP', 'cep' => '01001000', 'is_default' => true]);
        $product = Product::factory()->for(Category::factory())->create(['name' => 'Produto para revisão', 'price' => 42.50]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 5]);
        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'name_snapshot' => $product->name, 'image_snapshot' => '', 'color_snapshot' => 'Rosa', 'size_snapshot' => 'M', 'price' => 42.50, 'quantity' => 2]);

        $this->actingAs($user)->get(route('checkout'))
            ->assertOk()
            ->assertSeeTextInOrder(['Endereço de entrega', 'Dados do pedido', 'Forma de entrega', 'Forma de pagamento', 'Revisar itens'])
            ->assertSee('Editar endereço selecionado')
            ->assertSee('Cartão de crédito')
            ->assertSee('Produto para revisão')
            ->assertSee('R$ 85,00')
            ->assertSee('Voltar ao carrinho e editar');
    }

    public function test_new_checkout_address_is_saved_and_preserved_after_checkout_validation_failure(): void
    {
        $user = User::factory()->create();
        $addressData = [
            'label' => 'Casa nova', 'recipient_name' => 'Cliente Novo', 'phone' => '11988887777',
            'cep' => '04567-000', 'street' => 'Rua Nova', 'number' => '25', 'complement' => 'Apto 4',
            'neighborhood' => 'Jardins', 'city' => 'São Paulo', 'state' => 'SP',
        ];

        $saveResponse = $this->actingAs($user)->postJson(route('addresses.store'), $addressData)
            ->assertCreated()
            ->assertJsonPath('address.user_id', $user->id)
            ->assertJsonPath('address.label', 'Casa nova');

        $addressId = $saveResponse->json('address.id');
        $this->assertDatabaseHas('addresses', ['id' => $addressId, 'user_id' => $user->id, 'street' => 'Rua Nova']);

        $this->actingAs($user)->from(route('checkout'))->post(route('checkout.process'), $addressData + [
            'address_id' => $addressId,
            'cpf' => '123.456.789-09',
            'shipping_cost' => 0,
            'payment_method' => 'pix',
        ])->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['shipping_cost', 'carrier', 'service'])
            ->assertSessionHas('_old_input.address_id', (string) $addressId);

        $this->assertDatabaseHas('addresses', ['id' => $addressId, 'user_id' => $user->id]);
    }
}
