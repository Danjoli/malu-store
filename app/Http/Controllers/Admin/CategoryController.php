<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\StoreCategoryRequest;
use App\Http\Requests\Admin\Categories\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\Categories\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $service
    ) {}

    public function index(Request $request)
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = '%'.trim($request->string('q')->toString()).'%';
                $query->where(fn ($query) => $query->where('name', 'like', $search)->orWhere('slug', 'like', $search));
            })
            ->when($request->string('products')->toString() === 'with', fn ($query) => $query->has('products'))
            ->when($request->string('products')->toString() === 'without', fn ($query) => $query->doesntHave('products'))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->service->update($category, $request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category)
    {
        $this->service->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria removida!');
    }
}
