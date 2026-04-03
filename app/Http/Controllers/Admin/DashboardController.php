<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
            $query->where('status', 'shipped');
        })->count();

        // Total geral de vendas
        $totalSalesOverall = Order::sum('total');

        // Vendas mensais (últimos 12 meses)
        $salesData = Order::select(
            DB::raw("MONTH(created_at) as month"),
            DB::raw("SUM(total) as total")
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

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

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalClients',
            'totalShipped',
            'totalSalesOverall',
            'sales',
            'months',
            'recentOrders'
        ));
    }
}
