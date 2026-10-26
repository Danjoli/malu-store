<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = trim($request->string('q')->toString());
                $query->where(function ($query) use ($term): void {
                    if (ctype_digit($term)) {
                        $query->orWhereKey((int) $term);
                    }
                    $search = '%'.$term.'%';
                    $query->orWhere('recipient_name', 'like', $search)
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', $search)->orWhere('email', 'like', $search));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('payment_method'), fn ($query) => $query->where('payment_method', $request->string('payment_method')->toString()))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items',
            'items.variant.product',
            'items.variant.product.images',
        ]);

        return view('admin.orders.show.index', compact('order'));
    }
}
