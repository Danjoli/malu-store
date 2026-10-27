<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportOctober2026CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_the_curated_collection_without_duplicates_or_small_sizes(): void
    {
        Storage::fake('local');
        config(['filesystems.default' => 'local']);

        $this->artisan('catalog:import-october-2026', ['--force' => true])->assertSuccessful();
        $this->artisan('catalog:import-october-2026', ['--force' => true])->assertSuccessful();

        $this->assertSame(23, Product::query()->whereIn('slug', $this->slugs())->count());
        $this->assertSame(67, ProductImage::query()->whereHas('product', fn ($query) => $query->whereIn('slug', $this->slugs()))->count());
        $this->assertSame(0, ProductVariant::query()->whereHas('product', fn ($query) => $query->whereIn('slug', $this->slugs()))->where('size', 'P')->count());
        $this->assertTrue(Category::query()->where('slug', 'macaquinhos')->exists());
        $sandy = Product::query()->where('slug', 'conjunto-sandy')->firstOrFail();
        $this->assertSame('189.99', $sandy->price);
        $this->assertSame(18, $sandy->variants()->count());
        $this->assertSame(6, $sandy->images()->count());

        $maite = Product::query()->where('slug', 'conjunto-maite')->firstOrFail();
        $this->assertSame('199.99', $maite->price);
        $this->assertSame(12, $maite->variants()->count());
        $this->assertSame(4, $maite->images()->count());

        $lorena = Product::query()->where('slug', 'conjunto-lorena')->firstOrFail();
        $this->assertSame('80.00', $lorena->price);
        $this->assertSame(24, $lorena->variants()->count());
        $this->assertSame(8, $lorena->images()->count());

        $stella = Product::query()->where('slug', 'conjunto-stella')->firstOrFail();
        $this->assertSame('80.00', $stella->price);
        $this->assertSame(14, $stella->variants()->count());
        $this->assertSame(7, $stella->images()->count());

        $agata = Product::query()->where('slug', 'vestido-agata')->firstOrFail();
        $this->assertSame('80.00', $agata->price);
        $this->assertSame(12, $agata->variants()->count());
        $this->assertSame(4, $agata->images()->count());

        $iris = Product::query()->where('slug', 'conjunto-iris')->firstOrFail();
        $this->assertSame('99.90', $iris->price);
        $this->assertSame(10, $iris->variants()->count());
        $this->assertSame(5, $iris->images()->count());

        $zara = Product::query()->where('slug', 'conjunto-zara')->firstOrFail();
        $this->assertSame('109.90', $zara->price);
        $this->assertSame(30, $zara->variants()->count());
        $this->assertSame(10, $zara->images()->count());

        foreach (ProductImage::query()->get() as $image) {
            Storage::disk('local')->assertExists('products/'.$image->image);
        }
    }

    /** @return array<int, string> */
    private function slugs(): array
    {
        return [
            'conjunto-pantalona-essenza', 'conjunto-listrado-riviera', 'camisa-aurora',
            'conjunto-saia-lumiere', 'saia-longa-poa-solar', 'conjunto-tropical-brisa',
            'conjunto-cetim-champagne', 'saia-longa-folhagem', 'conjunto-pantalona-belle',
            'camisa-ampla-nuvem', 'conjunto-pantalona-manteiga', 'saia-longa-horizonte',
            'saia-envelope-rose', 'saia-longa-jardim-azul', 'macaquinho-listrado-serena',
            'conjunto-tres-pecas-alva',
            'conjunto-sandy',
            'conjunto-maite',
            'conjunto-lorena',
            'conjunto-stella',
            'vestido-agata',
            'conjunto-iris',
            'conjunto-zara',
        ];
    }
}
