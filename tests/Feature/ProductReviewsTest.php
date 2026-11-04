<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_uses_real_content_and_review_totals(): void
    {
        $product = Product::factory()->create([
            'details' => 'Modelagem reta com fechamento frontal.',
            'composition' => '70% viscose e 30% linho.',
        ]);
        $user = User::factory()->create(['name' => 'Maria Silva']);
        $item = $this->deliveredOrderItem($user, $product);
        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_item_id' => $item->id,
            'rating' => 4,
            'title' => 'Ótimo caimento',
            'comment' => 'A peça veste muito bem e o tecido é confortável.',
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Detalhes')
            ->assertSee('Modelagem reta com fechamento frontal.')
            ->assertSee('70% viscose e 30% linho.')
            ->assertSee('Avaliações (1)')
            ->assertSee('Ótimo caimento')
            ->assertSee('Compra verificada')
            ->assertSee('aggregateRating')
            ->assertSee('"reviewCount":1', false);
    }

    public function test_demo_review_is_clearly_identified_and_not_presented_as_verified_purchase(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(['name' => 'Ana']);
        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_item_id' => null,
            'rating' => 5,
            'title' => 'Avaliação de teste',
            'comment' => 'Comentário demonstrativo para validar a experiência da página.',
            'is_demo' => true,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Avaliação demonstrativa')
            ->assertDontSee('Compra verificada')
            ->assertDontSee('aggregateRating');
    }

    public function test_product_page_has_size_guide_stock_feedback_trust_and_product_schema(): void
    {
        $product = Product::factory()->create([
            'description' => 'Conjunto confortável para diferentes ocasiões.',
        ]);
        ProductVariant::factory()->for($product)->create([
            'size' => 'M',
            'stock' => 3,
        ]);
        ProductVariant::factory()->for($product)->create([
            'size' => 'GG',
            'stock' => 12,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Guia de medidas')
            ->assertSee('88–94')
            ->assertSee('102–110')
            ->assertSee('Postagem em até 2 dias úteis')
            ->assertSee('Troca fácil em até 7 dias')
            ->assertSee('data-variant-stock', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('<link rel="canonical"', false);
    }

    public function test_only_customer_with_delivered_purchase_can_review_product(): void
    {
        $product = Product::factory()->create();
        $customer = User::factory()->create();
        $item = $this->deliveredOrderItem($customer, $product);

        $this->actingAs($customer)->post(route('product.reviews.store', $product), [
            'order_item_id' => $item->id,
            'rating' => 5,
            'title' => 'Adorei',
            'comment' => 'O tamanho ficou perfeito e gostei muito do tecido.',
        ])->assertRedirect(route('product.show', $product).'#avaliacoes');

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'rating' => 5,
        ]);

        $otherCustomer = User::factory()->create();
        $this->actingAs($otherCustomer)->post(route('product.reviews.store', $product), [
            'order_item_id' => $item->id,
            'rating' => 1,
            'comment' => 'Tentativa de avaliar uma compra que não pertence a mim.',
        ])->assertNotFound();

        $this->assertDatabaseCount('product_reviews', 1);
    }

    public function test_customer_can_update_review_without_creating_a_duplicate(): void
    {
        $product = Product::factory()->create();
        $customer = User::factory()->create();
        $item = $this->deliveredOrderItem($customer, $product);

        foreach ([3, 5] as $rating) {
            $this->actingAs($customer)->post(route('product.reviews.store', $product), [
                'order_item_id' => $item->id,
                'rating' => $rating,
                'comment' => 'Avaliação atualizada depois de usar a peça novamente.',
            ])->assertRedirect();
        }

        $this->assertDatabaseCount('product_reviews', 1);
        $this->assertDatabaseHas('product_reviews', ['order_item_id' => $item->id, 'rating' => 5]);
    }

    private function deliveredOrderItem(User $user, Product $product): OrderItem
    {
        $variant = ProductVariant::factory()->for($product)->create();
        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => $user->name,
            'street' => 'Rua das Flores',
            'number' => '10',
            'city' => 'São Paulo',
            'state' => 'SP',
            'cep' => '01001000',
            'subtotal' => $product->price,
            'shipping' => 0,
            'total' => $product->price,
            'status' => 'delivered',
            'payment_method' => 'pix',
        ]);

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'name_snapshot' => $product->name,
            'image_snapshot' => '',
            'color_snapshot' => $variant->color,
            'size_snapshot' => $variant->size,
            'price' => $product->price,
            'quantity' => 1,
        ]);
    }
}
