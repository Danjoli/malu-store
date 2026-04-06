<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Página de Checkout
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $cart = Cart::with('items.variant')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $shipping = 0;
        $total = $subtotal;

        $address = Address::where('user_id', $user->id)
            ->where('is_default', true)
            ->first() ?? Address::where('user_id', $user->id)->first();

        $hasAddress = !is_null($address);

        return view('public.checkout.index', compact(
            'cart', 'subtotal', 'shipping', 'total', 'address', 'hasAddress'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Processar Pedido
    |--------------------------------------------------------------------------
    */
    public function processOrder(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // COLOCA AQUI
        if (!$request->shipping_cost || !$request->carrier) {
            return back()->with('error', 'Selecione um frete antes de continuar.');
        }

        $rules = [
            'label' => 'nullable|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'cep' => 'required|string|max:20',
            'cpf' => 'required|string|max:14',
            'is_default' => 'nullable|boolean',

            // NOVO (frete)
            'shipping_cost' => 'required|numeric|min:0',
            'carrier' => 'required|string|max:100',
            'service' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // Endereço
            $address = Address::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'id' => $request->address_id ?? null
                ],
                [
                    'label' => $validatedData['label'] ?? null,
                    'recipient_name' => $validatedData['recipient_name'],
                    'phone' => $validatedData['phone'],
                    'street' => $validatedData['street'],
                    'number' => $validatedData['number'],
                    'complement' => $validatedData['complement'] ?? null,
                    'neighborhood' => $validatedData['neighborhood'],
                    'city' => $validatedData['city'],
                    'state' => strtoupper($validatedData['state']),
                    'cep' => $validatedData['cep'],
                    'cpf' => $validatedData['cpf'],
                    'is_default' => $request->has('is_default'),
                ]
            );

            if ($request->has('is_default')) {
                Address::where('user_id', $user->id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            // 🛒 Carrinho
            $cart = Cart::with('items.variant')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
            }

            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);

            // 🔥 FRETE DINÂMICO
            $shipping = (float) $request->shipping_cost;
            $total = $subtotal + $shipping;

            /*
            |--------------------------------------------------------------
            | Verificar pedido pendente
            |--------------------------------------------------------------
            */
            $existingOrder = Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($existingOrder) {

                if ($existingOrder->payment_method !== 'boleto') {

                    if ($existingOrder->total == $total) {
                        DB::commit();
                        return redirect()->route('payment', $existingOrder->id);
                    }

                    $existingOrder->update(['status' => 'cancelled']);
                }
            }

            /*
            |--------------------------------------------------------------
            | Criar Pedido
            |--------------------------------------------------------------
            */
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'cpf' => preg_replace('/\D/', '', $validatedData['cpf']),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'status' => 'pending',
            ]);

            /*
            |--------------------------------------------------------------
            | Criar Itens do Pedido
            |--------------------------------------------------------------
            */
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'name_snapshot' => $item->name_snapshot ?? 'Produto',
                    'image_snapshot' => $item->image_snapshot ?? 'sem-imagem.jpg',
                    'color_snapshot' => $item->color_snapshot,
                    'size_snapshot' => $item->size_snapshot,
                    'price' => $item->price,
                    'quantity' => $item->quantity
                ]);
            }

            /*
            |--------------------------------------------------------------
            | 🚚 Criar ENVIO
            |--------------------------------------------------------------
            */
            Shipment::create([
                'order_id' => $order->id,
                'carrier' => $request->carrier,
                'shipping_cost' => $shipping,
                'shipment_id' => $request->service,
                'status' => 'pending'
            ]);

            DB::commit();

            return redirect()->route('payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Página de Pagamento
    |--------------------------------------------------------------------------
    */
    public function payment($orderId)
    {
        $user = Auth::user();

        $order = Order::with('items')
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->firstOrFail();

        return view('public.payment.index', compact('order'));
    }

    /*
    |--------------------------------------------------------------------------
    | Confirmar Pagamento
    |--------------------------------------------------------------------------
    */
    public function confirmPayment(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required'
        ]);

        $user = Auth::user();

        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->firstOrFail();

        $order->update([
            'payment_method' => $request->payment_method
        ]);

        return match($request->payment_method) {
            'pix' => redirect()->route('payment.pix', $order->id),
            'card' => redirect()->route('payment.card', $order->id),
            'boleto' => redirect()->route('payment.boleto', $order->id),
            default => redirect()->back()->with('error', 'Método de pagamento inválido.')
        };
    }
}
