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
use Inertia\Inertia;
use Inertia\Response;

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

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', $this->formProps());
    }

    public function store(StoreProductRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants']);

        return Inertia::render('Admin/Products/Form', $this->formProps($product));
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

    /**
     * @return array<string, mixed>
     */
    private function formProps(?Product $product = null): array
    {
        return [
            'mode' => $product ? 'edit' : 'create',
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'sizes' => array_map(fn (ClothingSize $size): string => $size->value, ClothingSize::cases()),
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => (string) $product->price,
                'description' => $product->description,
                'details' => $product->details,
                'composition' => $product->composition,
                'active' => $product->active,
                'images' => $product->images->map(fn ($image): array => [
                    'id' => $image->id,
                    'url' => $image->url,
                ])->values(),
                'variants' => $product->variants->map(fn ($variant): array => [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'stock' => $variant->stock,
                ])->values(),
            ] : null,
            'urls' => [
                'submit' => $product
                    ? route('admin.products.update', $product)
                    : route('admin.products.store'),
                'index' => route('admin.products.index'),
            ],
        ];
    }
}
