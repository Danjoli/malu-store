<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;

class PublicProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['images', 'variants', 'category'])
            ->where('active', 1)
            ->findOrFail($id);

        return view('public.products.show', compact('product'));
    }
}
