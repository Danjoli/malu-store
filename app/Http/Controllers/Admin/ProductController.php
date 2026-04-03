<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // LISTAR
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // MOSTRAR PRODUTO COMPLETO
    public function show(Product $product)
    {
        $product->load([
            'category',
            'images',
            'variants'
        ]);

        $totalStock = $product->variants->sum('stock');

        return view('admin.products.show', compact('product', 'totalStock'));
    }

    // FORM CRIAR
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // SALVAR PRODUTO
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants.*.color' => 'required',
            'variants.*.size' => 'required',
            'variants.*.stock' => 'required|integer'
        ]);

        // PRODUTO
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'active' => $request->active
        ]);

        // IMAGENS
        if ($request->hasFile('images')) {
            $publicPath = $_SERVER['DOCUMENT_ROOT'].'/products';

            foreach ($request->file('images') as $image) {
                $nomeArquivo = time().'_'.$image->getClientOriginalName();
                $image->move($publicPath, $nomeArquivo);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $nomeArquivo
                ]);
            }
        }

        // VARIAÇÕES
        if ($request->variants) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $variant['color'],
                    'size' => $variant['size'],
                    'stock' => $variant['stock']
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    // FORM EDITAR
    public function edit(Product $product)
    {
        $categories = Category::all();

        $product->load([
            'images',
            'variants'
        ]);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    // ATUALIZAR
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants.*.color' => 'required',
            'variants.*.size' => 'required',
            'variants.*.stock' => 'required|integer'
        ]);

        // ATUALIZA PRODUTO
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'active' => $request->has('active')
        ]);

        // ATUALIZAR IMAGENS
        if ($request->hasFile('images')) {
            $product->load('images');
            $publicPath = $_SERVER['DOCUMENT_ROOT'].'/products';

            // apagar antigas
            foreach ($product->images as $image) {
                $path = $publicPath.'/'.$image->image;
                if(file_exists($path)) unlink($path);
                $image->delete();
            }

            // salvar novas
            foreach ($request->file('images') as $image) {
                $nomeArquivo = time().'_'.$image->getClientOriginalName();
                $image->move($publicPath, $nomeArquivo);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $nomeArquivo
                ]);
            }
        }

        // ATUALIZAR VARIAÇÕES
        if ($request->variants) {
            // apagar antigas
            $product->variants()->delete();

            // criar novamente
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $variant['color'],
                    'size' => $variant['size'],
                    'stock' => $variant['stock']
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto atualizado!');
    }

    // DELETAR
    public function destroy(Product $product)
    {
        $product->load('images');
        $publicPath = $_SERVER['DOCUMENT_ROOT'].'/products';

        // apagar imagens do public
        foreach ($product->images as $image) {
            $path = $publicPath.'/'.$image->image;
            if(file_exists($path)) unlink($path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto removido!');
    }
}