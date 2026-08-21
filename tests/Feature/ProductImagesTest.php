<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Admin\Catalog\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_gallery_can_be_replaced_with_new_images(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'image' => 'imagem-antiga.png',
        ]);
        Storage::disk('public')->put('products/imagem-antiga.png', 'old-image');

        app(ProductService::class)->replaceImages($product, [
            UploadedFile::fake()->create('nova-frente.png', 100, 'image/png'),
            UploadedFile::fake()->create('nova-costas.png', 100, 'image/png'),
        ]);

        $this->assertDatabaseCount('product_images', 2);
        Storage::disk('public')->assertMissing('products/imagem-antiga.png');
        $this->assertCount(2, Storage::disk('public')->files('products'));
    }
}
