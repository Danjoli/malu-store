<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Public\Cart\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();

        return view('public.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $this->cartService->addItem(
            $request->variant_id,
            $request->quantity
        );

        return back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $this->cartService->updateItem($id, $request->quantity);

        return redirect()->route('public.cart.index')
            ->with('success', 'Quantidade atualizada com sucesso.');
    }

    public function remove($id)
    {
        $this->cartService->removeItem($id);

        return redirect()->route('public.cart.index')
            ->with('success', 'Item removido do carrinho.');
    }
}
