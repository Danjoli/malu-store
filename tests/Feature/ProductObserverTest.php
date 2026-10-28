<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
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

    public function test_product_images_are_centered_without_cropping_in_gallery_and_cards(): void
    {
        $product = Product::factory()->create(['name' => 'Conjunto com foto horizontal']);
        ProductImage::factory()->for($product)->create();
        ProductVariant::factory()->for($product)->create(['stock' => 5]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('aspect-[3/4] w-full object-contain', false)
            ->assertSee('h-full w-full object-contain', false);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('h-full w-full object-contain p-2', false);
    }
}
