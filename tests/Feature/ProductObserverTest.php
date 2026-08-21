<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_unique_slug_for_each_product(): void
    {
        $category = Category::factory()->create();
        $first = Product::factory()->for($category)->create(['name' => 'Vestido Aurora']);
        $second = Product::factory()->for($category)->create(['name' => 'Vestido Aurora']);

        $this->assertSame('vestido-aurora', $first->slug);
        $this->assertSame('vestido-aurora-2', $second->slug);
    }

    public function test_the_product_page_uses_the_slug_and_legacy_link_redirects(): void
    {
        $product = Product::factory()->create(['name' => 'Saia Floral']);

        $this->get(route('product.show', $product))->assertOk();
        $this->get(route('product.legacy-show', $product->id))
            ->assertRedirect(route('product.show', $product));
    }
}
