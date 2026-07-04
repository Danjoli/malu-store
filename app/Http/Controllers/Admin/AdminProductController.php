<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\Admins\AdminProduct\ProductService;
use App\Http\Requests\Admins\AdminProduct\StoreProductRequest;
use App\Http\Requests\Admins\AdminProduct\UpdateProductRequest;

class AdminProductController extends Controller
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

        return view('admin.products.create', compact(
            'categories',
            'variantIndex'
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

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'variantIndex'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->service->update($product, $request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto atualizado!');
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto removido!');
    }
}
