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
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->withSum('variants', 'stock')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = '%'.trim($request->string('q')->toString()).'%';
                $query->where(fn ($query) => $query->where('name', 'like', $search)->orWhere('slug', 'like', $search));
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->string('status')->toString() === 'active', fn ($query) => $query->where('active', true))
            ->when($request->string('status')->toString() === 'inactive', fn ($query) => $query->where('active', false))
            ->when($request->string('stock')->toString() === 'available', fn ($query) => $query->inStock())
            ->when($request->string('stock')->toString() === 'empty', fn ($query) => $query->whereDoesntHave('variants', fn ($query) => $query->where('stock', '>', 0)))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.products.index', ['products' => $products, 'categories' => Category::query()->orderBy('name')->get()]);
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
