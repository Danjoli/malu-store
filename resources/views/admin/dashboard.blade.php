@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<h1 class="text-3xl font-bold mb-8">
    Dashboard
</h1>

{{-- CARDS PRINCIPAIS --}}
<div class="grid grid-cols-3 gap-6 mb-8">

    {{-- Total Geral de Vendas --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Vendas Totais</p>
        <p class="text-3xl font-bold text-green-700 mt-2">
            R$ {{ number_format($totalSalesOverall, 2, ',', '.') }}
        </p>
    </div>

    {{-- Total de Vendas Mensais --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Vendas Mensais</p>
        <p class="text-3xl font-bold text-green-600 mt-2">
            R$ {{ number_format(array_sum($sales), 2, ',', '.') }}
        </p>
    </div>

    {{-- Pedidos --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Pedidos</p>
        <p class="text-3xl font-bold text-blue-600 mt-2">
            {{ $totalOrders }}
        </p>
    </div>

    {{-- Clientes --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Clientes</p>
        <p class="text-3xl font-bold text-purple-600 mt-2">
            {{ $totalClients }}
        </p>
    </div>

    {{-- Produtos --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Produtos</p>
        <p class="text-3xl font-bold text-orange-500 mt-2">
            {{ $totalProducts }}
        </p>
    </div>

    {{-- Envios Realizados --}}
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-gray-500 text-sm">Envios Realizados</p>
        <p class="text-3xl font-bold text-indigo-600 mt-2">
            {{ $totalShipped }}
        </p>
    </div>

</div>

{{-- GRAFICO DE VENDAS --}}
<div class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-xl font-bold mb-4">Vendas Mensais</h2>
    <canvas id="salesChart"></canvas>
</div>

{{-- PEDIDOS RECENTES --}}
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Pedidos Recentes</h2>
    <table class="w-full">
        <thead class="border-b">
            <tr>
                <th class="text-left py-2">Pedido</th>
                <th class="text-left py-2">Cliente</th>
                <th class="text-left py-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr class="border-b">
                    <td class="py-2">#{{ $order->id }}</td>
                    <td class="py-2">{{ $order->user->name }}</td>
                    <td class="py-2">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Vendas',
                data: {!! json_encode($sales) !!},
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.2)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
