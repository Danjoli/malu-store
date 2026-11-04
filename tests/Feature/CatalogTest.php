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

    public function test_catalog_accepts_multiple_colors_and_sizes_from_available_stock(): void
    {
        $category = Category::factory()->create();
        $blue = Product::factory()->for($category)->create(['name' => 'Conjunto Azul']);
        ProductVariant::factory()->for($blue)->create(['color' => 'Azul', 'size' => 'M', 'stock' => 2]);
        $rose = Product::factory()->for($category)->create(['name' => 'Conjunto Rosé']);
        ProductVariant::factory()->for($rose)->create(['color' => 'Rosé', 'size' => 'G', 'stock' => 2]);
        $black = Product::factory()->for($category)->create(['name' => 'Conjunto Preto']);
        ProductVariant::factory()->for($black)->create(['color' => 'Preto', 'size' => 'GG', 'stock' => 2]);

        $this->get(route('catalog.index', [
            'color' => ['Azul', 'Rosé'],
            'size' => ['M', 'G'],
        ]))
            ->assertOk()
            ->assertSee($blue->name)
            ->assertSee($rose->name)
            ->assertDontSee($black->name)
            ->assertSee('data-catalog-form', false)
            ->assertSee('data-catalog-live-input', false);
    }

    public function test_catalog_searches_product_description_and_exposes_only_real_stock_filters(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create([
            'name' => 'Peça Serena',
            'description' => 'Tecido fluido para eventos especiais.',
        ]);
        ProductVariant::factory()->for($product)->create(['color' => 'Verde Menta', 'size' => 'G', 'stock' => 2]);
        $unavailable = Product::factory()->for($category)->create(['name' => 'Peça sem estoque']);
        ProductVariant::factory()->for($unavailable)->create(['color' => 'Laranja', 'size' => 'PP', 'stock' => 0]);

        $this->get(route('catalog.index', ['search' => 'eventos especiais']))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Verde Menta')
            ->assertSee('value="G"', false)
            ->assertDontSee('Laranja')
            ->assertDontSee('value="PP"', false);
    }

    public function test_catalog_sidebar_only_lists_categories_with_available_products(): void
    {
        $available = Category::factory()->create(['name' => 'Categoria Disponível']);
        $inactive = Category::factory()->create(['name' => 'Categoria Inativa']);
        $outOfStock = Category::factory()->create(['name' => 'Categoria Esgotada']);
        $empty = Category::factory()->create(['name' => 'Categoria Vazia']);

        $this->product('Produto disponível', $available);
        $this->product('Produto inativo', $inactive, active: false);
        $this->product('Produto esgotado', $outOfStock, stock: 0);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee($available->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($outOfStock->name)
            ->assertDontSee($empty->name);
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

    public function test_home_only_loads_a_bounded_product_showcase(): void
    {
        $category = Category::factory()->create();

        foreach (range(1, 13) as $number) {
            $this->product('Vitrine '.$number, $category);
        }

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertViewHas('products', fn ($products) => $products->count() === 12)
            ->assertSee(route('catalog.index'), false);
    }

    public function test_home_lists_every_available_category_even_outside_product_showcase_limit(): void
    {
        $categories = Category::factory()->count(13)->create();

        foreach ($categories as $category) {
            $product = Product::factory()->for($category)->create(['active' => true]);
            ProductVariant::factory()->for($product)->create(['stock' => 2]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('flex flex-wrap justify-center', false)
            ->assertSee($categories->first()->name)
            ->assertSee($categories->last()->name);
    }

    public function test_home_exposes_dynamic_filters_and_applies_search_category_and_sort(): void
    {
        $dresses = Category::factory()->create(['name' => 'Vestidos', 'slug' => 'vestidos']);
        $blouses = Category::factory()->create(['name' => 'Blusas', 'slug' => 'blusas']);
        $this->product('Vestido Aurora', $dresses)->update(['price' => 129.90]);
        $this->product('Blusa Serena', $blouses)->update(['price' => 79.90]);

        $this->get(route('home', [
            'search' => 'Aurora',
            'category' => 'vestidos',
            'sort' => 'price_desc',
        ]))
            ->assertOk()
            ->assertSee('data-home-catalog-shell', false)
            ->assertSee('data-home-live-search', false)
            ->assertSee('Vestido Aurora')
            ->assertDontSee('Blusa Serena');
    }
}
