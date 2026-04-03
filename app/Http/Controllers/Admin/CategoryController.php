<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // LISTAR
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    // FORM CRIAR
    public function create()
    {
        return view('admin.categories.create');
    }

    // SALVAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Categoria criada com sucesso!');
    }

    // FORM EDITAR
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // ATUALIZAR
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Categoria atualizada!');
    }

    // DELETAR
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Categoria removida!');
    }
}
