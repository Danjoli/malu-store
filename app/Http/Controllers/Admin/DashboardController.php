<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Totais simples
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalClients = User::count();

        // Total de envios realizados
        $totalShipped = Order::whereHas('shipment', function ($query) {
            $query->where('status', ShipmentStatus::Shipped->value);
        })->count();

        // Total geral de vendas
        $totalSalesOverall = Order::sum('total');

        $salesThisMonth = Order::where('status', OrderStatus::Paid->value)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $pendingOrders = Order::whereIn('status', [OrderStatus::Pending->value, OrderStatus::PendingPayment->value])->count();
        $paidOrders = Order::where('status', OrderStatus::Paid->value)->count();
        $lowStockProducts = Product::whereHas('variants', fn ($query) => $query->where('stock', '<=', 5))->count();

        // Vendas mensais (últimos 12 meses)
        $salesData = Order::query()
            ->whereYear('created_at', now()->year)
            ->get(['created_at', 'total'])
            ->groupBy(fn (Order $order) => $order->created_at->month)
            ->map(fn ($orders) => $orders->sum('total'));

        $months = [];
        $sales = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $sales[] = $salesData[$i] ?? 0;
        }

        // Pedidos recentes (últimos 5 pedidos)
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalProducts',
            'totalOrders',
            'totalClients',
            'totalShipped',
            'totalSalesOverall',
            'salesThisMonth',
            'pendingOrders',
            'paidOrders',
            'lowStockProducts',
            'sales',
            'months',
            'recentOrders'
        ));
    }
}
