<?php

namespace App\Services\Admin\Catalog;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Support\ProductImageStorage;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(private readonly ProductImageStorage $imageStorage) {}

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
        DB::transaction(function () use ($product, $data): void {
            $product->update([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'active' => $data['active'],
            ]);

            if (! empty($data['images'])) {
                $this->replaceImages($product, $data['images']);
            }

            $this->syncVariants($product, $data['variants']);
        });

        return $product;
    }

    public function delete(Product $product): void
    {
        foreach ($product->images as $img) {
            $this->imageStorage->delete($img->image);
            $img->delete();
        }

        $product->delete();
    }

    private function handleImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $name = $this->imageStorage->store($image);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $name,
            ]);
        }
    }

    /**
     * Substitui a galeria completa e remove os arquivos antigos do storage.
     */
    public function replaceImages(Product $product, array $images): void
    {
        foreach ($product->images as $img) {
            $this->imageStorage->delete($img->image);
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

    private function syncVariants(Product $product, array $variants): void
    {
        $existing = $product->variants()->lockForUpdate()->get()->keyBy('id');
        $retainedIds = collect($variants)
            ->pluck('id')
            ->filter()
            ->map(fn ($id): int => (int) $id);
        $removedIds = $existing->keys()->diff($retainedIds)->values();

        if ($removedIds->isNotEmpty()) {
            CartItem::query()->whereIn('product_variant_id', $removedIds)->delete();
            $product->variants()->whereKey($removedIds)->delete();
        }

        foreach ($retainedIds as $id) {
            $existing[$id]->update(['color' => "__updating_{$id}"]);
        }

        foreach ($variants as $variant) {
            $attributes = [
                'color' => $variant['color'],
                'size' => $variant['size'],
                'stock' => $variant['stock'],
            ];

            if (! empty($variant['id'])) {
                $existing[(int) $variant['id']]->update($attributes);

                continue;
            }

            $product->variants()->create($attributes);
        }
    }
}
