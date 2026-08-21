<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Enums\ClothingSize;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\Products\StoreProductRequest;
use App\Http\Requests\Admin\Catalog\Products\UpdateProductImagesRequest;
use App\Http\Requests\Admin\Catalog\Products\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Admin\Catalog\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants']);

        $totalStock = $product->variants->sum('stock');

        return view('admin.products.show', compact('product', 'totalStock'));
    }

    public function create()
    {
        $categories = Category::all();
        $variantIndex = 1;
        $sizes = ClothingSize::cases();

        return view('admin.products.create', compact(
            'categories',
            'variantIndex',
            'sizes'
        ));
    }

    public function store(StoreProductRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        $product->load(['images', 'variants']);

        $variantIndex = $product->variants->count();
        $sizes = ClothingSize::cases();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'variantIndex',
            'sizes'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->service->update($product, $request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto atualizado!');
    }

    public function updateImages(UpdateProductImagesRequest $request, Product $product)
    {
        $this->service->replaceImages($product, $request->validated('images'));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Galeria de imagens atualizada!');
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto removido!');
    }
}
