<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['images', 'variants'])
            ->whereHas('favorites', fn ($favorites) => $favorites->where('user_id', $request->user()->id))
            ->get();

        return view('public.favorites.index', compact('products'));
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $favorite = Favorite::where('user_id', $request->user()->id)->where('product_id', $product->id)->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'Produto removido dos favoritos.');
        }

        Favorite::create(['user_id' => $request->user()->id, 'product_id' => $product->id]);

        return back()->with('success', 'Produto adicionado aos favoritos.');
    }
}
