<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Mostrar Carrinho
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $cart = $this->getCart();

        return view('public.cart.index', compact('cart'));
    }

    /*
    |--------------------------------------------------------------------------
    | Adicionar Item ao Carrinho
    |--------------------------------------------------------------------------
    */

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->getCart();

        $variant = ProductVariant::with('product.images')
            ->findOrFail($request->variant_id);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $image = optional($variant->product->images->first())->image ?? '';

            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'name_snapshot' => $variant->product->name,
                'image_snapshot' => $image,
                'color_snapshot' => $variant->color,
                'size_snapshot' => $variant->size,
                'price' => $variant->product->price,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produto adicionado ao carrinho!');
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar Quantidade
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->getCart();

        $item = $cart->items()->where('id', $id)->firstOrFail();

        $item->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->route('cart.index')
            ->with('success', 'Quantidade atualizada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Remover Item
    |--------------------------------------------------------------------------
    */

    public function remove($id)
    {
        $cart = $this->getCart();

        $item = $cart->items()->where('id', $id)->firstOrFail();

        $item->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Item removido do carrinho.');
    }

    /*
    |--------------------------------------------------------------------------
    | Buscar ou Criar Carrinho
    |--------------------------------------------------------------------------
    */

    private function getCart()
    {
        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->with('items')
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'status'  => 'active',
            ]);
        }

        return $cart->load('items');
    }
}
