<?php

namespace App\Services\Admins\AdminProduct;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function create(array $data): Product
    {
        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'active' => $data['active'],
        ]);

        $this->handleImages($product, $data['images'] ?? []);
        $this->handleVariants($product, $data['variants']);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
        ]);

        if (!empty($data['images'])) {
            $this->replaceImages($product, $data['images']);
        }

        if (!empty($data['variants'])) {
            $this->replaceVariants($product, $data['variants']);
        }

        return $product;
    }

    public function delete(Product $product): void
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete('products/' . $img->image);
            $img->delete();
        }

        $product->delete();
    }

    private function handleImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $name = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('products', $name, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $name,
            ]);
        }
    }

    private function replaceImages(Product $product, array $images): void
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete('products/' . $img->image);
            $img->delete();
        }

        $this->handleImages($product, $images);
    }

    private function handleVariants(Product $product, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'color' => $variant['color'],
                'size' => $variant['size'],
                'stock' => $variant['stock'],
            ]);
        }
    }

    private function replaceVariants(Product $product, array $variants): void
    {
        $product->variants()->delete();
        $this->handleVariants($product, $variants);
    }
}
