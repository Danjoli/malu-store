<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $name, Category $category, bool $active = true, int $stock = 5): Product
    {
        $product = Product::factory()->for($category)->create(['name' => $name, 'active' => $active]);
        ProductVariant::factory()->for($product)->create(['stock' => $stock]);

        return $product;
    }

    public function test_catalog_filters_by_search_and_category(): void
    {
        $vestidos = Category::factory()->create(['name' => 'Vestidos', 'slug' => 'vestidos']);
        $blusas = Category::factory()->create(['name' => 'Blusas', 'slug' => 'blusas']);
        $dress = $this->product('Vestido Aurora', $vestidos);
        $blouse = $this->product('Blusa Serena', $blusas);

        $this->get(route('catalog.index', ['search' => 'Aurora']))
            ->assertOk()->assertSee($dress->name)->assertDontSee($blouse->name);

        $this->get(route('catalog.index', ['category' => 'blusas']))
            ->assertOk()->assertSee($blouse->name)->assertDontSee($dress->name);
    }

    public function test_store_hides_inactive_and_out_of_stock_products(): void
    {
        $category = Category::factory()->create();
        $visible = $this->product('Produto Visível', $category);
        $inactive = $this->product('Produto Inativo', $category, active: false);
        $outOfStock = $this->product('Produto Sem Estoque', $category, stock: 0);

        $response = $this->get(route('catalog.index'));

        $response->assertOk()->assertSee($visible->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($outOfStock->name);
    }
}
