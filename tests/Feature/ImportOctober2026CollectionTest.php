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

        $this->assertSame(16, Product::query()->whereIn('slug', $this->slugs())->count());
        $this->assertSame(23, ProductImage::query()->whereHas('product', fn ($query) => $query->whereIn('slug', $this->slugs()))->count());
        $this->assertSame(0, ProductVariant::query()->whereHas('product', fn ($query) => $query->whereIn('slug', $this->slugs()))->where('size', 'P')->count());
        $this->assertTrue(Category::query()->where('slug', 'macaquinhos')->exists());

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
        ];
    }
}
