<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_stock_filters_require_an_available_matching_variant(): void
    {
        $category = Category::factory()->create();
        $matching = Product::factory()->for($category)->create(['name' => 'Conjunto Disponível']);
        ProductVariant::factory()->for($matching)->create([
            'color' => 'Rosé',
            'size' => 'M',
            'stock' => 3,
        ]);

        $unavailable = Product::factory()->for($category)->create(['name' => 'Conjunto Esgotado']);
        ProductVariant::factory()->for($unavailable)->create([
            'color' => 'Rosé',
            'size' => 'M',
            'stock' => 0,
        ]);

        $this->get(route('catalog.index', ['color' => 'Rosé', 'size' => 'M']))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($unavailable->name);
    }

    public function test_catalog_reuses_cached_data_and_invalidates_it_after_a_change(): void
    {
        $category = Category::factory()->create();
        $product = $this->product('Produto em Cache', $category);

        $this->get(route('catalog.index'))->assertOk()->assertSee('Produto em Cache');

        DB::enableQueryLog();
        $this->get(route('catalog.index'))->assertOk()->assertSee('Produto em Cache');
        $this->assertSame([], DB::getQueryLog());

        $product->update(['name' => 'Produto Atualizado']);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Produto Atualizado')
            ->assertDontSee('Produto em Cache');
    }

    public function test_large_catalog_uses_navigation_without_rendering_every_page_number(): void
    {
        $category = Category::factory()->create();

        foreach (range(1, 11) as $number) {
            $this->product('Produto '.$number, $category);
        }

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Página 1')
            ->assertSee(route('catalog.index', ['page' => 2]), false);

        $this->get(route('catalog.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Página 2')
            ->assertSee(route('catalog.index'), false);
    }
}
